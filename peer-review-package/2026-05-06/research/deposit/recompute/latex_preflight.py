#!/usr/bin/env python3
"""
LaTeX/Pandoc preflight for manuscript Markdown.

Goal: catch the exact classes of issues that trigger "parser escaping" / broken math
or hard LaTeX failures before a reviewer runs Pandoc/TeX.

This script is intentionally conservative and fast:
- It does not try to fully parse Markdown.
- It flags suspicious tokens outside code fences.
- It can optionally run the repo's pandoc+latex compile check and PDF text sanity check.

Usage:
  python3 research/deposit/recompute/latex_preflight.py \
    research/algorithmic-geopolitics-nfsi-comprehensive-peer-review.md
"""

from __future__ import annotations

import argparse
import re
import subprocess
import sys
from dataclasses import dataclass
from pathlib import Path


@dataclass
class Finding:
    path: str
    line: int
    kind: str
    message: str


FENCE_RE = re.compile(r"^\s*```")

# Tokens that should never appear in a clean PDF text layer, but can show up after
# converter "escaping" or OCR-ish corruption.
SUSPICIOUS_PDF_TOKENS = [
    r"\$(",
    r"\$\pm",
    r"$\$",
    "backslash hat",
    "lambda hat",
    r"\theta",
    r"\emptyset",
    "h=1c",
]

# Unicode characters that frequently break pdflatex or indicate "smart punctuation".
UNICODE_LATEX_RISK = {
    "\u2212": "Unicode minus (U+2212). Prefer ASCII '-' if you want pdflatex compatibility.",
    "\u2013": "En dash (U+2013). Usually OK in Unicode engines; may break pdflatex without inputenc.",
    "\u2014": "Em dash (U+2014). Usually OK in Unicode engines; may break pdflatex without inputenc.",
    "\u2018": "Left single quote (U+2018).",
    "\u2019": "Right single quote (U+2019).",
    "\u201c": "Left double quote (U+201C).",
    "\u201d": "Right double quote (U+201D).",
}


def iter_lines(path: Path) -> list[str]:
    return path.read_text(encoding="utf-8").splitlines()


def scan_markdown(path: Path) -> list[Finding]:
    findings: list[Finding] = []
    lines = iter_lines(path)

    in_fence = False
    fence_lang = ""

    for i, line in enumerate(lines, 1):
        if FENCE_RE.match(line):
            if not in_fence:
                in_fence = True
                fence_lang = line.strip().lstrip("`").strip()
            else:
                in_fence = False
                fence_lang = ""
            continue

        # Only scan "rendered text" (outside code fences)
        if in_fence:
            continue

        # 1) Escaped dollar delimiters (these are the classic failure mode)
        if r"\$(" in line or r"\$" in line and "$" in line:
            # avoid over-triggering: we only flag the exact "\$(" token which is always wrong for math-dollars
            if r"\$(" in line:
                findings.append(
                    Finding(str(path), i, "escaped_math_dollar", r"Found '\$(' which will render as a literal '$(' (broken inline math).")
                )

        # 2) Double-dollar corruption patterns
        if "$\\$" in line or "\\$" in line and "$" in line and "```" not in line:
            if "$\\$" in line:
                findings.append(
                    Finding(str(path), i, "double_escaped_dollars", r"Found '$\$', indicative of converter double-escaping.")
                )

        # 3) Legacy LaTeX delimiters discouraged by project policy
        if "\\(" in line or "\\)" in line or "\\[" in line or "\\]" in line:
            findings.append(
                Finding(str(path), i, "legacy_math_delimiter", r"Found '\(\)' or '\[\]'. Prefer $...$ / $$...$$ in Markdown.")
            )

        # 4) Raw HTML that commonly breaks Pandoc-to-LaTeX workflows
        if "<figure" in line.lower() or "</figure" in line.lower():
            findings.append(
                Finding(str(path), i, "raw_html_figure", "Found <figure> HTML. Prefer pure Markdown images + captions.")
            )

        # 5) Unicode characters that are risky for pdflatex and often differ across converters
        # We record these as warnings; they are not fatal if you compile with lualatex/xelatex.
        for ch, msg in UNICODE_LATEX_RISK.items():
            if ch in line:
                findings.append(Finding(str(path), i, "unicode_warn", msg))
                break

    return findings


def run_cmd(cmd: list[str]) -> tuple[int, str]:
    try:
        out = subprocess.check_output(cmd, text=True, stderr=subprocess.STDOUT)
        return 0, out
    except subprocess.CalledProcessError as e:
        return int(e.returncode), e.output or ""
    except FileNotFoundError:
        return 127, f"missing command: {cmd[0]}"


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("markdown_path", type=str)
    ap.add_argument("--run-pandoc-latex-check", action="store_true", help="Also run research/deposit/recompute/pandoc_latex_check.sh")
    ap.add_argument("--run-pdf-text-sanity", action="store_true", help="Also run pdf_text_sanity_check.py against the official PDF path")
    ap.add_argument("--fail-on-unicode", action="store_true", help="Treat unicode_warn as fatal (pdflatex-only environments).")
    args = ap.parse_args()

    md = Path(args.markdown_path)
    if not md.exists():
        print(f"ERROR: missing markdown: {md}", file=sys.stderr)
        return 2

    findings = scan_markdown(md)
    fatals = [f for f in findings if f.kind not in ("unicode_warn",)]
    warns = [f for f in findings if f.kind in ("unicode_warn",)]

    if fatals or (warns and args.fail_on_unicode):
        print("FAIL: preflight findings:")
        for f in (fatals + (warns if args.fail_on_unicode else []))[:200]:
            print(f"- {f.path}:{f.line} [{f.kind}] {f.message}")
        if len(findings) > 200:
            print(f"... plus {len(findings) - 200} more findings")
        return 1

    print("OK: markdown preflight passed (no fatal tokens found).")
    if warns:
        print("WARN: unicode characters present (safe with lualatex/xelatex; risky with pdflatex):")
        for w in warns[:80]:
            print(f"- {w.path}:{w.line} [{w.kind}] {w.message}")
        if len(warns) > 80:
            print(f"... plus {len(warns) - 80} more unicode warnings")

    if args.run_pandoc_latex_check:
        code, out = run_cmd(["bash", "research/deposit/recompute/pandoc_latex_check.sh"])
        if code != 0:
            print("FAIL: pandoc+latex check failed:")
            print(out.strip())
            return 1
        print(out.strip())

    if args.run_pdf_text_sanity:
        pdf = md.parent / "algorithmic-geopolitics-nfsi-comprehensive-peer-review.pdf"
        if not pdf.exists():
            print(f"ERROR: expected PDF missing: {pdf}", file=sys.stderr)
            return 2
        code, out = run_cmd(["python3", "research/deposit/recompute/pdf_text_sanity_check.py", str(pdf)])
        if code != 0:
            print("FAIL: PDF text sanity check failed:")
            print(out.strip())
            print("\nSuspicious tokens checked:")
            for t in SUSPICIOUS_PDF_TOKENS:
                print(" -", t)
            return 1
        print(out.strip())

    return 0


if __name__ == "__main__":
    raise SystemExit(main())

