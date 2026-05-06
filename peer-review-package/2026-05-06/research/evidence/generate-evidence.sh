#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
OUT_DIR="${ROOT_DIR}/research/evidence"

cd "${ROOT_DIR}"

if ! command -v sha256sum >/dev/null 2>&1; then
  echo "sha256sum not found" >&2
  exit 1
fi

if ! git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  echo "Not a git repository: ${ROOT_DIR}" >&2
  exit 1
fi

mkdir -p "${OUT_DIR}"

COMMIT="$(git rev-parse HEAD)"
BRANCH="$(git rev-parse --abbrev-ref HEAD 2>/dev/null || true)"
DIRTY_COUNT="$(git status --porcelain=v1 | wc -l | tr -d ' ')"
GENERATED_AT_UTC="$(date -u +"%Y-%m-%d %H:%M:%S UTC")"

# Key artefacts: methodology, builder, figures, and the stability implementation reference.
FILES=(
  "research/algorithmic-geopolitics-nfsi-comprehensive-peer-review.md"
  "research/peer_review_main.md"
  "research/ResearchPdfBuilder.php"
  "classes/helpers/LegalPdfBuilder.php"
  "research/nationFiles-Stability-Index--NFSI--Validation-and-Verification-Report.md"
  "download/research/nationFiles-Stability-Index--NFSI--Validation-and-Verification-Report.md"
  "bin/DataSourceConnector/StabilityIndex.php"
  "research/peer-review/figures/fig01-paradigm-shift.svg"
  "research/peer-review/figures/fig02-system-architecture.svg"
  "research/peer-review/figures/fig03-nfsi-layers.svg"
  "research/peer-review/figures/fig04-lpu-sram.svg"
  "research/peer-review/figures/fig05-stress-fault-tree.svg"
  "research/peer-review/figures/fig06-benchmark-freshness.svg"
  "research/peer-review/figures-png/fig01-paradigm-shift.png"
  "research/peer-review/figures-png/fig02-system-architecture.png"
  "research/peer-review/figures-png/fig03-nfsi-layers.png"
  "research/peer-review/figures-png/fig04-lpu-sram.png"
  "research/peer-review/figures-png/fig05-stress-fault-tree.png"
  "research/peer-review/figures-png/fig06-benchmark-freshness.png"
)

MANIFEST_JSON="${OUT_DIR}/evidence.json"
SHA_FILE="${OUT_DIR}/evidence.sha256"

{
  echo "{"
  echo "  \"generated_at_utc\": \"${GENERATED_AT_UTC}\","
  echo "  \"git\": {"
  echo "    \"commit\": \"${COMMIT}\","
  echo "    \"branch\": \"${BRANCH}\","
  echo "    \"dirty_files_count\": ${DIRTY_COUNT}"
  echo "  },"
  echo "  \"files\": ["
  first=1
  for f in "${FILES[@]}"; do
    if [[ ! -f "${ROOT_DIR}/${f}" ]]; then
      continue
    fi
    sha="$(sha256sum "${ROOT_DIR}/${f}" | awk '{print $1}')"
    if [[ $first -eq 0 ]]; then echo "    ,"; fi
    first=0
    echo -n "    {\"path\": \"${f}\", \"sha256\": \"${sha}\"}"
  done
  echo ""
  echo "  ]"
  echo "}"
} > "${MANIFEST_JSON}"

# Deterministic hash list (including manifest itself).
{
  sha256sum "${MANIFEST_JSON}"
  for f in "${FILES[@]}"; do
    if [[ -f "${ROOT_DIR}/${f}" ]]; then
      sha256sum "${ROOT_DIR}/${f}"
    fi
  done
} > "${SHA_FILE}"

echo "Wrote ${MANIFEST_JSON}"
echo "Wrote ${SHA_FILE}"

