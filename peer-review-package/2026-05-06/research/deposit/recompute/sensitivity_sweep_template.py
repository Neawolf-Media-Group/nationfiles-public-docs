#!/usr/bin/env python3
"""
Sensitivity sweep template (protocol-grade, deterministic).

This script generates a machine-readable sweep plan (CSV) for parameter sensitivity.
It does NOT assume a particular execution backend (PHP/DB/Python); instead it produces:
- a grid of parameter perturbations
- a list of required outputs/metrics per run

You can then implement an executor that:
1) applies the parameter overrides (constants) for a run
2) recomputes NFSI for a fixed fixture (sample-dataset or a pinned snapshot)
3) writes metrics to the output CSV

Usage:
  python3 research/deposit/recompute/sensitivity_sweep_template.py \
    --out research/deposit/backtest/sensitivity_plan.csv.txt
"""

from __future__ import annotations

import argparse
import csv
from dataclasses import dataclass
from pathlib import Path


@dataclass(frozen=True)
class Param:
    key: str
    base: float
    kind: str  # "weight"|"cap"|"factor"
    note: str


DEFAULT_PARAMS: list[Param] = [
    Param("LAYER2_TODAY_WEIGHT", 0.60, "weight", "L2 smoothing: today weight (yesterday = 1-today)."),
    Param("LAYER3_CONFLICT_MALUS_FACTOR", 1.00, "factor", "Conflict malus linear factor."),
    Param("LAYER4_INERTIA_STANDARD", 0.80, "weight", "L4 inertia weight on prevScore in normal mode."),
    Param("LAYER4_DAILY_CHANGE_CAP", 3.00, "cap", "L4 absolute daily delta cap in points (±cap)."),
]


def perturbations() -> list[tuple[str, float]]:
    # Protocol requirement: ±10%, ±25%, and extreme cases.
    # Extreme cases here are conservative "sanity extremes", not necessarily valid production settings.
    return [
        ("minus10", -0.10),
        ("plus10", +0.10),
        ("minus25", -0.25),
        ("plus25", +0.25),
        ("ext_low", -0.50),
        ("ext_high", +0.50),
    ]


def clamp01(x: float) -> float:
    if x < 0.0:
        return 0.0
    if x > 1.0:
        return 1.0
    return x


def format_val(x: float) -> str:
    return f"{x:.6f}".rstrip("0").rstrip(".")


def build_rows(params: list[Param]) -> list[dict[str, str]]:
    rows: list[dict[str, str]] = []
    for p in params:
        for tag, frac in perturbations():
            v = p.base * (1.0 + frac)
            if p.kind == "weight":
                v = clamp01(v)
            # caps/factors: allow 0 as lower bound for the plan
            if v < 0.0:
                v = 0.0

            run_id = f"{p.key}__{tag}"
            rows.append(
                {
                    "run_id": run_id,
                    "param_key": p.key,
                    "param_base": format_val(p.base),
                    "param_value": format_val(v),
                    "perturbation": tag,
                    "note": p.note,
                    # Required outputs (the executor must fill these):
                    "metric_country_score_dist_p50": "",
                    "metric_country_score_dist_p05": "",
                    "metric_country_score_dist_p95": "",
                    "metric_crash_mode_rate": "",
                    "metric_topk_delta_overlap": "",
                    "metric_topk_delta_mean_abs": "",
                    "artifact_path_run_log": "",
                }
            )
    return rows


def write_csv(path: Path, rows: list[dict[str, str]]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    with path.open("w", encoding="utf-8", newline="") as fh:
        w = csv.DictWriter(fh, fieldnames=list(rows[0].keys()))
        w.writeheader()
        w.writerows(rows)


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--out", required=True, type=str, help="Output CSV path (recommended: *.csv.txt)")
    args = ap.parse_args()

    out = Path(args.out)
    rows = build_rows(DEFAULT_PARAMS)
    write_csv(out, rows)
    print(f"Wrote sensitivity plan: {out} ({len(rows)} runs)")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

