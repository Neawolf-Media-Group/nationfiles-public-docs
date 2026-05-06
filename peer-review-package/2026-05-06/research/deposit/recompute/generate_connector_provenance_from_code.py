#!/usr/bin/env python3
from __future__ import annotations

import csv
import re
from pathlib import Path


ROOT = Path("/var/www/clients/client1/web3/web")
SRC_INFO = ROOT / "bin" / "DataSourceConnector" / "sourcenodes.info.json"
SRC_NODE_DIR = ROOT / "bin" / "DataSourceConnector" / "SourceNode"
OUT = ROOT / "research" / "deposit" / "provenance" / "connector_provenance.csv.txt"


def iter_connector_files() -> list[Path]:
    return sorted(SRC_NODE_DIR.glob("**/*Connector.php"))


RE_CLASS = re.compile(r"class\s+([A-Za-z0-9_]+)Connector\s+extends\s+[A-Za-z0-9_\\\\]+", re.IGNORECASE)
RE_GROUP_PROP = re.compile(r"\$nfsiGroup\s*=\s*([0-9]+)\s*;", re.IGNORECASE)
RE_MULT_PROP = re.compile(r"\$tupleUpdateMultiplier\s*=\s*([0-9]+(?:\.[0-9]+)?)\s*;", re.IGNORECASE)
RE_DIR_PROP = re.compile(r"\$stabilityIndexDirection\s*=\s*'?(positive|negative)'?\s*;", re.IGNORECASE)
RE_RAWCOL_METHOD = re.compile(r"getStabilityRawValueColumn\s*\(\s*\)\s*:\s*\?string\s*\{[\s\S]{0,200}?return\s*'([^']+)'\s*;", re.IGNORECASE)
RE_RAWCOLS_PROP = re.compile(r"\$stabilityIndexDbColumns\s*=\s*\[([^\]]+)\]\s*;", re.IGNORECASE)
RE_STR_LIT = re.compile(r"'([A-Za-z0-9_]+)'")


def parse_meta_from_php(path: Path) -> dict:
    s = path.read_text(encoding="utf-8", errors="replace")
    out: dict[str, str] = {}

    m = RE_CLASS.search(s)
    if m:
        out["connector_id"] = m.group(1)

    m = RE_GROUP_PROP.search(s)
    if m:
        out["group"] = m.group(1)

    m = RE_MULT_PROP.search(s)
    if m:
        out["update_mult"] = m.group(1)

    m = RE_DIR_PROP.search(s)
    if m:
        # In NFSI: negative direction means higher raw is worse.
        out["higher_raw_is_worse"] = "1" if m.group(1).lower() == "negative" else "0"

    # Raw fields: prefer explicit method; else stabilityIndexDbColumns list.
    m = RE_RAWCOL_METHOD.search(s)
    if m:
        out["raw_fields"] = m.group(1)
    else:
        m = RE_RAWCOLS_PROP.search(s)
        if m:
            lits = [mm.group(1) for mm in RE_STR_LIT.finditer(m.group(1))]
            if lits:
                out["raw_fields"] = ";".join(lits[:5])

    return out


def main() -> int:
    # Build lookup from connector_id -> meta
    meta: dict[str, dict[str, str]] = {}
    for f in iter_connector_files():
        m = parse_meta_from_php(f)
        cid = m.get("connector_id")
        if not cid:
            continue
        meta[cid] = m

    # Read existing provenance CSV (so we preserve weights/licence snippets already curated)
    if not OUT.exists():
        raise SystemExit(f"Missing: {OUT}")

    rows = []
    with OUT.open(newline="") as fp:
        r = csv.DictReader(fp)
        fieldnames = r.fieldnames or []
        for row in r:
            rows.append(row)

    # Ensure required columns exist
    required = [
        "connector_id",
        "source_url",
        "data_version",
        "last_snapshot_date",
        "license_text_snippet",
        "derivative_reuse",
        "contact",
        "weight",
        "group",
        "higher_raw_is_worse",
        "update_mult",
        "raw_fields",
    ]
    for k in required:
        if k not in fieldnames:
            fieldnames.append(k)

    # Fill from code where missing
    updated = 0
    for row in rows:
        cid = (row.get("connector_id") or "").strip()
        if not cid or cid not in meta:
            continue
        m = meta[cid]
        for k in ("group", "higher_raw_is_worse", "update_mult", "raw_fields"):
            if (row.get(k) or "").strip() == "" and k in m:
                row[k] = m[k]
                updated += 1

    # Write back
    with OUT.open("w", newline="") as fp:
        w = csv.DictWriter(fp, fieldnames=fieldnames)
        w.writeheader()
        for row in rows:
            w.writerow(row)

    print(f"WROTE: {OUT} (fields filled from code: {updated})")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

