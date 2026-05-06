#!/usr/bin/env python3
"""
Backtest protocol runner (deposit-executable).

This script implements the same computations as backtest_protocol.ipynb but
without requiring Jupyter in the environment.

Outputs:
 - research/deposit/backtest/sensitivity_fixture_delta_matrix.csv.txt
 - prints summary metrics and permutation p-value
"""

from __future__ import annotations

import csv
import os
import random
import subprocess
import sys
from pathlib import Path


ROOT = Path("/var/www/clients/client1/web3/web")
DEPOSIT = ROOT / "research" / "deposit"
BACKTEST = DEPOSIT / "backtest"
BACKTEST_NAMED = DEPOSIT / "backtest-named"

EVENTS_CSV = BACKTEST / "backtest_events.csv.txt"
NAMED_CSV = BACKTEST_NAMED / "named_events_backtest.csv.txt"

RECOMPUTE = DEPOSIT / "recompute" / "recompute_nfsi_fixture.php"
FIXTURE_DIR = DEPOSIT / "sample-dataset"
META = FIXTURE_DIR / "connector_meta.csv.txt"


def read_csv_dict(path: Path) -> list[dict[str, str]]:
    with path.open(newline="") as f:
        r = csv.DictReader(f)
        return list(r)


def f(x: str | None) -> float:
    if x is None or x == "":
        return float("nan")
    return float(x)


def median(xs: list[float]) -> float:
    ys = sorted(xs)
    n = len(ys)
    if n == 0:
        return float("nan")
    return ys[n // 2] if (n % 2 == 1) else 0.5 * (ys[n // 2 - 1] + ys[n // 2])


def run_recompute(fixture_dir: Path, out_path: Path) -> None:
    cmd = ["php", str(RECOMPUTE), f"--fixture-dir={fixture_dir}", f"--out={out_path}"]
    subprocess.check_call(cmd)


def read_nfsi_map(path: Path) -> dict[tuple[str, str], float]:
    m: dict[tuple[str, str], float] = {}
    with path.open(newline="") as f_:
        r = csv.DictReader(f_)
        for row in r:
            m[(row["iso2"], row["date_ymd"])] = float(row["nfsi_today"])
    return m


def load_meta_rows() -> tuple[list[str], list[dict[str, str]]]:
    with META.open(newline="") as f_:
        r = csv.DictReader(f_)
        rows = list(r)
        assert r.fieldnames is not None
        return list(r.fieldnames), rows


def write_meta(fields: list[str], rows: list[dict[str, str]], path: Path) -> None:
    with path.open("w", newline="") as f_:
        w = csv.DictWriter(f_, fieldnames=fields)
        w.writeheader()
        for rr in rows:
            w.writerow(rr)


def build_temp_fixture_dir(tmp_dir: Path, meta_path: Path) -> Path:
    fx = tmp_dir / meta_path.stem
    fx.mkdir(parents=True, exist_ok=True)
    for name in ["connectors_raw.csv.txt", "country_meta.csv.txt", "nfsi_prev.csv.txt"]:
        (fx / name).write_bytes((FIXTURE_DIR / name).read_bytes())
    (fx / "connector_meta.csv.txt").write_bytes(meta_path.read_bytes())
    return fx


def main() -> int:
    for p in [EVENTS_CSV, NAMED_CSV, RECOMPUTE, META]:
        if not p.exists():
            print(f"ERROR: missing required file: {p}", file=sys.stderr)
            return 2

    events = read_csv_dict(EVENTS_CSV)
    _named = read_csv_dict(NAMED_CSV)

    deltas = [f(r["delta"]) for r in events]
    zs = [f(r["z_vs_pre"]) for r in events]

    abs_delta_median = median([abs(x) for x in deltas])
    frac_z_ge_2 = sum(1 for z in zs if abs(z) >= 2.0) / max(1, len(zs))

    print("event-style rows:", len(events))
    print("median |delta|:", round(abs_delta_median, 4))
    print("fraction |z|>=2:", round(frac_z_ge_2, 4))

    diffs: list[float] = []
    for r in events:
        score = f(r["score"])
        pre_mean = f(r["pre_mean"])
        diffs.append(score - pre_mean)

    obs = sum(diffs) / max(1, len(diffs))
    random.seed(1337)
    N = 10_000
    more_extreme = 0
    for _ in range(N):
        s = 0.0
        for d in diffs:
            s += d if (random.random() < 0.5) else (-d)
        stat = s / max(1, len(diffs))
        if abs(stat) >= abs(obs):
            more_extreme += 1
    p = (more_extreme + 1) / (N + 1)
    print("paired mean(score - pre_mean):", round(obs, 6))
    print("permutation p-value (two-sided, sign-flip):", p)

    # Sensitivity (fixture): +10% per connector weight (OAT)
    baseline_csv = Path("/tmp/nfsi_fixture_baseline.csv")
    run_recompute(FIXTURE_DIR, baseline_csv)
    base_map = read_nfsi_map(baseline_csv)

    fields, meta_rows = load_meta_rows()
    connector_ids = [r["connector_id"] for r in meta_rows if r.get("connector_id")]
    tmp_dir = Path("/tmp/nfsi_sensitivity")
    tmp_dir.mkdir(parents=True, exist_ok=True)

    delta_matrix: list[dict[str, str]] = []
    for cid in connector_ids:
        perturbed: list[dict[str, str]] = []
        for r in meta_rows:
            rr = dict(r)
            if rr["connector_id"] == cid:
                base_w = float(rr["connector_weight"])
                rr["connector_weight"] = str(base_w * 1.10)
            perturbed.append(rr)

        meta_path = tmp_dir / f"meta_{cid}_plus10.csv"
        write_meta(fields, perturbed, meta_path)
        fx = build_temp_fixture_dir(tmp_dir, meta_path)

        out_csv = tmp_dir / f"out_{cid}_plus10.csv"
        run_recompute(fx, out_csv)
        m = read_nfsi_map(out_csv)

        for (iso2, d), v in m.items():
            base = base_map[(iso2, d)]
            delta_matrix.append(
                {
                    "iso2": iso2,
                    "date_ymd": d,
                    "perturb": f"{cid}_plus10",
                    "delta_nfsi": str(round(v - base, 4)),
                }
            )

    out_path = BACKTEST / "sensitivity_fixture_delta_matrix.csv.txt"
    out_path.parent.mkdir(parents=True, exist_ok=True)
    with out_path.open("w", newline="") as f_:
        w = csv.DictWriter(f_, fieldnames=["iso2", "date_ymd", "perturb", "delta_nfsi"])
        w.writeheader()
        for r in delta_matrix:
            w.writerow(r)

    print("WROTE:", out_path)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

