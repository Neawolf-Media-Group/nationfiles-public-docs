#!/usr/bin/env bash
# Peer-review manuscript → PDF via Pandoc (IEEEtran.cls) + pdflatex (+ optional bibtex).
# Vendored class: research/latex/vendor/IEEEtran/IEEEtran.cls (CTAN).
#
# LaTeX hygiene (see research/latex/ieee-peer-review-header.tex):
#   - IEEEtran options: peerreview,journal,onecolumn (journal-style single column).
#   - Figures: Markdown images use {#fig:...} → \caption{...}\label{fig:...}; \includegraphics
#     scaled to 0.9\linewidth.
#   - Listings (fenced ```text): Unicode en/em dashes normalized to ASCII in the MD source
#     (listingsutf8 + pdflatex).
#   - Wide tables: longtable uses \footnotesize + tighter \tabcolsep; residual Overfull boxes may
#     need editorial splits or \begin{landscape}...\end{landscape} (pdflscape, already loaded).
#
# Outputs:
#   research/algorithmic-geopolitics-nfsi-comprehensive-peer-review.pdf (default)
#   research/deposit/build/manuscript-peer-review-ieee.tex (+ logs)
#
# Usage (from project root):
#   bash research/build-peer-review-pdf-ieee.sh

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT_DIR="${ROOT_DIR}/research/deposit/build"
LOG_DIR="${OUT_DIR}/logs"
IEEE_VENDOR="${ROOT_DIR}/research/latex/vendor/IEEEtran"
HEADER_TEX="${ROOT_DIR}/research/latex/ieee-peer-review-header.tex"

FINAL_PDF="${PEER_REVIEW_LATEX_DEST:-${ROOT_DIR}/research/algorithmic-geopolitics-nfsi-comprehensive-peer-review.pdf}"
MANUSCRIPT="${ROOT_DIR}/research/algorithmic-geopolitics-nfsi-comprehensive-peer-review.md"
SUPPLEMENT="${ROOT_DIR}/research/supplementary-peer-review-artefacts.md"
TEX_OUT="${OUT_DIR}/manuscript-peer-review-ieee.tex"

mkdir -p "${LOG_DIR}"

if ! command -v pandoc >/dev/null 2>&1; then
  echo "pandoc not found" >&2
  exit 2
fi

if [[ ! -f "${IEEE_VENDOR}/IEEEtran.cls" ]]; then
  echo "Missing ${IEEE_VENDOR}/IEEEtran.cls — run from repo root or restore vendor IEEEtran." >&2
  exit 3
fi

export TEXINPUTS="${IEEE_VENDOR}//:${TEXINPUTS:-}"

# Merge main manuscript + supplementary artefact index (SM-xx paths).
pandoc "${MANUSCRIPT}" "${SUPPLEMENT}" \
  -o "${TEX_OUT}" \
  --standalone \
  --from "markdown+tex_math_dollars+yaml_metadata_block+raw_tex" \
  --listings \
  -V documentclass=IEEEtran \
  -V classoption=peerreview,journal,onecolumn \
  -V fontsize=11pt \
  -H "${HEADER_TEX}" \
  2> "${LOG_DIR}/pandoc_peer_review_ieee_stderr.txt" || {
    echo "pandoc failed (see ${LOG_DIR}/pandoc_peer_review_ieee_stderr.txt)" >&2
    exit 4
  }

PDF_ENGINE="pdflatex"
if ! command -v "${PDF_ENGINE}" >/dev/null 2>&1; then
  echo "pdflatex not found" >&2
  exit 5
fi

run_pdflatex() {
  (
    cd "${ROOT_DIR}"
    "${PDF_ENGINE}" -interaction=nonstopmode -halt-on-error -output-directory "${OUT_DIR}" "${TEX_OUT}"
  )
}

run_pdflatex > "${LOG_DIR}/latex_ieee_pass1.log" 2>&1 || {
  echo "pdflatex pass 1 failed — see ${LOG_DIR}/latex_ieee_pass1.log" >&2
  exit 8
}

AUX_FILE="${OUT_DIR}/$(basename "${TEX_OUT}" .tex).aux"
if grep -q '\\bibdata{' "${AUX_FILE}" 2>/dev/null; then
  (
    cd "${OUT_DIR}"
    bibtex "$(basename "${TEX_OUT}" .tex)" >> "${LOG_DIR}/bibtex_ieee.log" 2>&1 || true
  )
fi

run_pdflatex >> "${LOG_DIR}/latex_ieee_pass2.log" 2>&1 || {
  echo "pdflatex pass 2 failed — see ${LOG_DIR}/latex_ieee_pass2.log" >&2
  exit 8
}
run_pdflatex >> "${LOG_DIR}/latex_ieee_pass3.log" 2>&1 || {
  echo "pdflatex pass 3 failed — see ${LOG_DIR}/latex_ieee_pass3.log" >&2
  exit 8
}

ENGINE_PDF="${OUT_DIR}/$(basename "${TEX_OUT}" .tex).pdf"
if [[ ! -f "${ENGINE_PDF}" ]]; then
  echo "LaTeX did not produce PDF: ${ENGINE_PDF}" >&2
  exit 6
fi

cp -f "${ENGINE_PDF}" "${FINAL_PDF}"

{
  cat "${LOG_DIR}/latex_ieee_pass1.log"
  [[ -f "${LOG_DIR}/latex_ieee_pass2.log" ]] && cat "${LOG_DIR}/latex_ieee_pass2.log"
  [[ -f "${LOG_DIR}/latex_ieee_pass3.log" ]] && cat "${LOG_DIR}/latex_ieee_pass3.log"
} > "${LOG_DIR}/latex_ieee_merged.log"

ERR_FOUND=0
for f in "${LOG_DIR}/latex_ieee_pass1.log" "${LOG_DIR}/latex_ieee_pass2.log" "${LOG_DIR}/latex_ieee_pass3.log"; do
  [[ -f "$f" ]] || continue
  if grep -q "^! LaTeX Error" "$f" || grep -q "Emergency stop" "$f"; then
    ERR_FOUND=1
    break
  fi
done
if [[ "$ERR_FOUND" -eq 1 ]]; then
  echo "LaTeX reported fatal errors — see ${LOG_DIR}/latex_ieee_merged.log" >&2
  exit 7
fi

echo "Wrote ${FINAL_PDF}"
echo "Intermediate: ${TEX_OUT}, ${ENGINE_PDF}, ${LOG_DIR}/latex_ieee_merged.log"
