#!/usr/bin/env python3
from __future__ import annotations

import hashlib
import json
import subprocess
from datetime import datetime, timezone
from pathlib import Path


ROOT = Path("/var/www/clients/client1/web3/web")
DEPOSIT = ROOT / "research" / "deposit"


def sha256_file(path: Path) -> str:
    h = hashlib.sha256()
    with path.open("rb") as f:
        for chunk in iter(lambda: f.read(1024 * 1024), b""):
            h.update(chunk)
    return h.hexdigest()


def git_commit() -> str:
    try:
        out = subprocess.check_output(["git", "rev-parse", "HEAD"], cwd=str(ROOT))
        return out.decode("utf-8").strip()
    except Exception:
        return "UNKNOWN"


def main() -> int:
    inputs = [
        Path("research/deposit/backtest/backtest_events.csv.txt"),
        Path("research/deposit/backtest-named/named_events_backtest.csv.txt"),
    ]
    outputs = [
        Path("research/deposit/backtest/sensitivity_fixture_delta_matrix.csv.txt"),
    ]

    manifest = {
        "generated_utc": datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ"),
        "git_commit": git_commit(),
        "inputs": [],
        "rng": {"seed": 1337, "permutations": 10000, "test": "sign_flip_paired_summary"},
        "outputs": [],
    }

    for p in inputs:
        abs_p = ROOT / p
        if not abs_p.exists():
            manifest["inputs"].append({"path": str(p), "sha256": "MISSING"})
        else:
            manifest["inputs"].append({"path": str(p), "sha256": sha256_file(abs_p)})

    for p in outputs:
        abs_p = ROOT / p
        if not abs_p.exists():
            manifest["outputs"].append({"path": str(p), "sha256": "MISSING"})
        else:
            manifest["outputs"].append({"path": str(p), "sha256": sha256_file(abs_p)})

    out_path = DEPOSIT / "backtest" / "run_manifest.json"
    out_path.write_text(json.dumps(manifest, indent=2) + "\n", encoding="utf-8")
    print(f"WROTE: {out_path}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

