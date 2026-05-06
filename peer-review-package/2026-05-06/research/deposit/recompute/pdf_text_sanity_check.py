#!/usr/bin/env python3
"""
PDF text sanity check (deposit-side).

Purpose:
- Detect common LaTeX escaping / OCR-like artefacts that should NOT appear in the
  generated manuscript PDF.

This checks the *text layer* of the PDF via `pdftotext` (poppler).
It does not OCR images.
"""

from __future__ import annotations

import re
import subprocess
import sys
from pathlib import Path


def main() -> int:
    if len(sys.argv) != 2:
        print("Usage: pdf_text_sanity_check.py /abs/path/to/file.pdf", file=sys.stderr)
        return 2

    pdf = Path(sys.argv[1])
    if not pdf.exists():
        print(f"ERROR: missing PDF: {pdf}", file=sys.stderr)
        return 2

    try:
        text = subprocess.check_output(["pdftotext", str(pdf), "-"], text=True, errors="replace")
    except FileNotFoundError:
        print("ERROR: pdftotext not found (install poppler-utils).", file=sys.stderr)
        return 2
    except subprocess.CalledProcessError as e:
        print(f"ERROR: pdftotext failed: exit={e.returncode}", file=sys.stderr)
        return 2

    # NOTE: We intentionally use plain substring checks (not regex),
    # because backslashes in these tokens can easily produce invalid regex escapes.
    patterns: list[tuple[str, str]] = [
        (r"\$(" , r"escaped dollar before ("),
        (r"\$\pm", r"escaped dollar before \pm"),
        (r"\$[1, 100]\$", r"escaped dollar around [1,100]"),
        (r"$\$", r"double-escaped dollars ($\$)"),
        ("lambda hat", "corrupted hat macro (lambda hat)"),
        ("backslash hat", "corrupted hat macro (backslash hat)"),
        (r"\theta", r"OCR artefact (theta)"),
        (r"\emptyset", r"OCR artefact (emptyset)"),
        (r"$MAE=", r"math-delimited MAE in text block"),
        ("h=1c", "OCR artefact (c instead of d)"),
    ]

    failures: list[str] = []
    for pat, label in patterns:
        if pat in text:
            failures.append(f"{label}: '{pat}'")

    if failures:
        print("FAIL: suspicious patterns found in PDF text layer:")
        for f in failures:
            print(" -", f)
        return 1

    print("OK: no suspicious LaTeX/OCR patterns found in PDF text layer.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

