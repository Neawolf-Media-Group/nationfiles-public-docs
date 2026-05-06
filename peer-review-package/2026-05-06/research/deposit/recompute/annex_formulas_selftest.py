#!/usr/bin/env python3
from __future__ import annotations

import json
import math
import re
import sys
from pathlib import Path


def log10(x: float) -> float:
    return math.log10(x)


def max_(a, b):
    return a if a > b else b


def min_(a, b):
    return a if a < b else b


def clip(x: float, lo: float, hi: float) -> float:
    return max(lo, min(hi, x))


def eval_expr(expr: str, env: dict) -> float | bool:
    # Minimal safe evaluator for our expression subset.
    # Supports: min(), max(), log10(), clip(), numbers, + - * /, comparisons, &&, ||, ?:.
    # We implement ternary by translating `cond ? a : b` to python `a if cond else b` (single level).
    s = expr.strip()
    # normalize boolean ops
    s = s.replace("&&", " and ").replace("||", " or ")
    # translate ternary (single)
    m = re.match(r"^(.*)\?(.*):(.*)$", s)
    if m:
        cond = m.group(1).strip()
        a = m.group(2).strip()
        b = m.group(3).strip()
        s = f"({a}) if ({cond}) else ({b})"

    safe = {
        "min": min_,
        "max": max_,
        "log10": log10,
        "clip": clip,
        **env,
    }
    return eval(s, {"__builtins__": {}}, safe)  # noqa: S307


def main() -> int:
    p = Path("/var/www/clients/client1/web3/web/research/deposit/annex_formulas.json")
    data = json.loads(p.read_text(encoding="utf-8"))

    formulas = {f["id"]: f for f in data["formulas"]}
    tvs = data["test_vectors"]

    failures = 0
    for tv in tvs:
        inputs = tv["inputs"]
        expected = tv["expected"]
        for outk, expv in expected.items():
            # select formula that outputs this key
            fmatch = None
            for f in formulas.values():
                if f.get("output") == outk:
                    fmatch = f
                    break
            if not fmatch:
                print(f"FAIL: no formula outputs '{outk}' for test {tv['id']}")
                failures += 1
                continue

            got = eval_expr(fmatch["expression"], dict(inputs))
            if isinstance(expv, (int, float)):
                if abs(float(got) - float(expv)) > 1e-3:
                    print(f"FAIL: {tv['id']} {outk}: got={got} expected={expv} expr={fmatch['expression']}")
                    failures += 1
            else:
                if got != expv:
                    print(f"FAIL: {tv['id']} {outk}: got={got} expected={expv}")
                    failures += 1

    if failures:
        print(f"FAIL: {failures} failures")
        return 1
    print("OK: annex_formulas.json test_vectors passed")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

