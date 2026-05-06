#!/usr/bin/env python3
from __future__ import annotations

import re
import sys
from dataclasses import dataclass
from pathlib import Path


@dataclass
class Issue:
    path: str
    line: int
    kind: str
    message: str


FENCE_RE = re.compile(r"^\s*```")
HTML_RE = re.compile(r"<!--|-->|</?(div|span|table|br|p)\b", re.IGNORECASE)


def split_table_cols(line: str) -> int:
    # Count pipe-separated columns in a Markdown table row.
    # We treat escaped pipes as literal (very rare here).
    s = line.strip()
    if not s.startswith("|"):
        return 0
    if "|" not in s[1:]:
        return 0
    # Remove leading/trailing pipe for counting.
    if s.endswith("|"):
        s2 = s[1:-1]
    else:
        s2 = s[1:]
    # Split on unescaped pipes
    parts = re.split(r"(?<!\\)\|", s2)
    return len(parts)


def is_table_sep(line: str) -> bool:
    s = line.strip()
    if not (s.startswith("|") and s.endswith("|")):
        return False
    # Header separator like: | --- | :---: | ---: |
    parts = [p.strip() for p in s.strip("|").split("|")]
    if not parts:
        return False
    for p in parts:
        if not re.fullmatch(r":?-{3,}:?", p):
            return False
    return True


def check_file(path: Path) -> list[Issue]:
    issues: list[Issue] = []
    try:
        lines = path.read_text(encoding="utf-8").splitlines()
    except Exception as e:
        return [Issue(str(path), 0, "read_error", str(e))]

    # 1) Fenced code blocks balance
    fence_lines = [i + 1 for i, l in enumerate(lines) if FENCE_RE.match(l)]
    if len(fence_lines) % 2 == 1:
        issues.append(Issue(str(path), fence_lines[-1], "fence_unclosed", f"Odd number of ``` fences ({len(fence_lines)})."))

    # 2) Raw HTML / HTML comments (policy)
    for i, l in enumerate(lines, 1):
        if HTML_RE.search(l):
            issues.append(Issue(str(path), i, "raw_html", "Raw HTML or HTML comment token found."))
            break

    # 3) Math delimiters policy: reject \\( \\) (legacy) and \\[ \\]
    for i, l in enumerate(lines, 1):
        if "\\(" in l or "\\)" in l or "\\[" in l or "\\]" in l:
            issues.append(Issue(str(path), i, "math_delimiter", "Found legacy LaTeX delimiters \\(\\) or \\[\\]. Use $...$ / $$...$$ in Markdown."))
            break

    # 4) Table column consistency checks (simple)
    in_table = False
    expected_cols = None
    table_start_line = 0
    saw_header_sep = False

    for i, l in enumerate(lines, 1):
        cols = split_table_cols(l)
        if cols > 0:
            if not in_table:
                in_table = True
                expected_cols = cols
                table_start_line = i
                saw_header_sep = False
            else:
                # allow the header-separator row itself
                if is_table_sep(l):
                    saw_header_sep = True
                else:
                    if expected_cols is not None and cols != expected_cols:
                        issues.append(
                            Issue(
                                str(path),
                                i,
                                "table_cols_mismatch",
                                f"Table started at line {table_start_line}: expected {expected_cols} cols, saw {cols}.",
                            )
                        )
                        # stop after first mismatch per file for signal
                        break
        else:
            if in_table:
                # end of table block
                if expected_cols is not None and not saw_header_sep:
                    issues.append(
                        Issue(
                            str(path),
                            table_start_line,
                            "table_missing_header_sep",
                            "Table block missing header separator row (| --- | ... |).",
                        )
                    )
                in_table = False
                expected_cols = None
                table_start_line = 0
                saw_header_sep = False

    return issues


def main(argv: list[str]) -> int:
    if len(argv) < 2:
        print("Usage: markdown_audit_check.py <file1.md> [file2.md ...]", file=sys.stderr)
        return 2

    all_issues: list[Issue] = []
    for a in argv[1:]:
        p = Path(a)
        if not p.exists():
            all_issues.append(Issue(a, 0, "missing", "File does not exist."))
            continue
        all_issues.extend(check_file(p))

    if not all_issues:
        print("OK: no issues detected.")
        return 0

    print("FAIL: issues detected:")
    for it in all_issues:
        loc = f"{it.path}:{it.line}" if it.line else it.path
        print(f"- {loc} [{it.kind}] {it.message}")
    return 1


if __name__ == "__main__":
    raise SystemExit(main(sys.argv))

