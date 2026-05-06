#!/usr/bin/env python3
"""
Generate a markdown summary table for top connectors by a simple influence score.

InfluenceScore ~= group_weight * connector_weight * update_mult

Inputs:
  research/deposit/provenance/connector_provenance.csv.txt

Outputs:
  research/deposit/provenance/top_connectors_by_weight.md (human-facing)
  research/deposit/provenance/top_connectors_by_weight.csv.txt (machine-facing)
"""

from __future__ import annotations

import csv
import hashlib
from pathlib import Path


ROOT = Path(__file__).resolve().parents[3]  # .../web
SRC = ROOT / "research/deposit/provenance/connector_provenance.csv.txt"
OUT_MD = ROOT / "research/deposit/provenance/top_connectors_by_weight.md"
OUT_CSV = ROOT / "research/deposit/provenance/top_connectors_by_weight.csv.txt"


def sha256_bytes(b: bytes) -> str:
    return hashlib.sha256(b).hexdigest()


def provenance_row_fingerprint(d: dict[str, str]) -> str:
    """
    Stable fingerprint over the reviewer-relevant snapshot/provenance tuple.

    NOTE: Upstream snapshot bytes are frequently not checked into git; reviewers should treat this as a
    verifiable fingerprint over the deposited provenance CSV row contents (not necessarily raw payloads).
    """
    parts = [
        (d.get("connector_id") or "").strip(),
        (d.get("source_url") or "").strip(),
        (d.get("data_version") or "").strip(),
        (d.get("last_snapshot_date") or "").strip(),
        (d.get("license_text_snippet") or "").strip().replace("\r", ""),
        (d.get("derivative_reuse") or "").strip().replace("\r", ""),
        (d.get("raw_fields") or "").strip(),
    ]
    payload = "|".join(parts).encode("utf-8")
    return sha256_bytes(payload)


def live_vs_archived(last_snapshot_date: str, data_version: str) -> str:
    dt = last_snapshot_date.strip().lower()
    dv = data_version.strip().lower()
    if dt == "live" or dv == "live":
        return "live_feed"
    if dt and dt != "live":
        return "archived_or_pinned"
    return "unknown"


def load_rows(path: Path) -> list[tuple[dict[str, str], float]]:
    rows: list[tuple[dict[str, str], float]] = []
    with path.open("r", encoding="utf-8", newline="") as fh:
        r = csv.DictReader(fh)
        for d in r:
            cid = (d.get("connector_id") or "").strip()
            if not cid:
                continue
            w = float(d.get("weight") or 0.0)
            g = float(d.get("group") or 0.0)
            u = float(d.get("update_mult") or 0.0)
            infl = abs(w * g * u)
            rows.append((dict(d), infl))
    return rows


def main() -> int:
    if not SRC.exists():
        raise SystemExit(f"missing source csv: {SRC}")

    loaded = load_rows(SRC)
    loaded.sort(key=lambda x: x[1], reverse=True)
    top = loaded[:10]

    OUT_CSV.parent.mkdir(parents=True, exist_ok=True)
    with OUT_CSV.open("w", encoding="utf-8", newline="") as fh:
        w = csv.writer(fh)
        w.writerow(
            [
                "rank",
                "connector_id",
                "group",
                "connector_weight",
                "update_mult",
                "influence_score",
                "license_text_snippet",
                "derivative_reuse",
                "deposit_provenance_row_sha256",
                "live_vs_archived",
            ]
        )
        for i, (d, influence) in enumerate(top, 1):
            cid = (d.get("connector_id") or "").strip()
            fp = provenance_row_fingerprint(d)
            lva = live_vs_archived(d.get("last_snapshot_date") or "", d.get("data_version") or "")
            w.writerow(
                [
                    str(i),
                    cid,
                    f"{float(d.get('group') or 0.0):g}",
                    f"{float(d.get('weight') or 0.0):g}",
                    f"{float(d.get('update_mult') or 0.0):g}",
                    f"{influence:.6f}".rstrip("0").rstrip("."),
                    (d.get("license_text_snippet") or "").strip().replace("\n", " "),
                    (d.get("derivative_reuse") or "").strip().replace("\n", " "),
                    fp,
                    lva,
                ]
            )

    csv_bytes = OUT_CSV.read_bytes()
    csv_sha = sha256_bytes(csv_bytes)

    md: list[str] = []
    md.append("## Top connectors by influence score (deposit-generated)")
    md.append("")
    md.append(f"**Generated from:** `{SRC.relative_to(ROOT)}`")
    md.append(f"**Generated artifact:** `{OUT_CSV.relative_to(ROOT)}`")
    md.append(f"**SHA256({OUT_CSV.name}):** `{csv_sha}`")
    md.append("")
    md.append("**Definition:** `influence_score = abs(group_weight * connector_weight * update_mult)`.")
    md.append("")
    md.append(
        "| Rank | Connector ID | group | connector_weight | update_mult | influence | snapshot lineage (deposit) | live/archived | row SHA256 | license excerpt | derivative/reuse notes |"
    )
    md.append("|---:|---|---:|---:|---:|---:|---|---:|---|---|---|")
    for i, (d, influence) in enumerate(top, 1):
        cid = (d.get("connector_id") or "").strip()
        fp = provenance_row_fingerprint(d)
        lva = live_vs_archived(d.get("last_snapshot_date") or "", d.get("data_version") or "")
        lineage = f"data_version={d.get('data_version')}; last_snapshot_date={d.get('last_snapshot_date')}"
        lineage = lineage.replace("|", "\\|")
        lic = (d.get("license_text_snippet") or "").replace("|", "\\|").replace("\n", " ").strip()
        drv = (d.get("derivative_reuse") or "").replace("|", "\\|").replace("\n", " ").strip()
        md.append(
            f"| {i} | `{cid}` | {float(d.get('group') or 0.0):g} | {float(d.get('weight') or 0.0):g} | "
            f"{float(d.get('update_mult') or 0.0):g} | {influence:.3g} | {lineage} | {lva} | `{fp}` | {lic} | {drv} |"
        )
    md.append("")
    md.append(
        "**Note:** This summary is mechanically derived from the deposited provenance CSV. "
        "It is intended for reviewer transparency; authoritative legal wording remains with each provider "
        "(see `/legal/sources/` and the deposited license appendix)."
    )
    md.append("")
    OUT_MD.write_text("\n".join(md), encoding="utf-8")
    print(f"Wrote {OUT_MD}")
    print(f"Wrote {OUT_CSV} (sha256={csv_sha})")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
