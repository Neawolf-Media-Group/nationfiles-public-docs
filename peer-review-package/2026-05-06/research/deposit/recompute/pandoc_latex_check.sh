#!/usr/bin/env bash
set -euo pipefail

# Minimal Pandoc -> LaTeX compile check for audit readiness.
# This script is intentionally "local-only" and produces a small log bundle.

# Script lives at research/deposit/recompute → repo root is three levels up.
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)"
MANUSCRIPT="${ROOT_DIR}/research/algorithmic-geopolitics-nfsi-comprehensive-peer-review.md"
OUT_DIR="${ROOT_DIR}/research/deposit/build-logs"

mkdir -p "${OUT_DIR}"

TEX_OUT="${OUT_DIR}/draft.tex"
PANDOC_LOG="${OUT_DIR}/pandoc.log"
LATEX_LOG="${OUT_DIR}/pdflatex.log"

echo "MANUSCRIPT=${MANUSCRIPT}" > "${OUT_DIR}/inputs.txt"
date -u +"UTC_BUILD_TIME=%Y-%m-%dT%H:%M:%SZ" >> "${OUT_DIR}/inputs.txt"

if ! command -v pandoc >/dev/null 2>&1; then
  echo "ERROR: pandoc not found on PATH." | tee "${PANDOC_LOG}"
  echo "Install pandoc, then re-run this check." | tee -a "${PANDOC_LOG}"
  exit 2
fi

# Prefer LuaLaTeX: manuscript uses Unicode minus (U+2212), smart punctuation, etc.
LATEX_ENGINE="lualatex"
if ! command -v "${LATEX_ENGINE}" >/dev/null 2>&1; then
  LATEX_ENGINE="pdflatex"
fi
if ! command -v "${LATEX_ENGINE}" >/dev/null 2>&1; then
  echo "ERROR: neither lualatex nor pdflatex found on PATH." | tee "${LATEX_LOG}"
  echo "Install texlive-latex-recommended (and lualatex), then re-run." | tee -a "${LATEX_LOG}"
  exit 2
fi

echo "Running pandoc -> LaTeX..." | tee "${PANDOC_LOG}"
pandoc \
  --standalone \
  --from markdown+tex_math_dollars \
  --to latex \
  -o "${TEX_OUT}" \
  "${MANUSCRIPT}" >> "${PANDOC_LOG}" 2>&1

echo "Compiling LaTeX (${LATEX_ENGINE}, 2 passes)..." | tee -a "${LATEX_LOG}"
"${LATEX_ENGINE}" -interaction=nonstopmode -halt-on-error -output-directory "${OUT_DIR}" "${TEX_OUT}" >> "${LATEX_LOG}" 2>&1
"${LATEX_ENGINE}" -interaction=nonstopmode -halt-on-error -output-directory "${OUT_DIR}" "${TEX_OUT}" >> "${LATEX_LOG}" 2>&1

PDF_ENGINE="${OUT_DIR}/draft.pdf"
if [[ ! -f "${PDF_ENGINE}" ]]; then
  echo "ERROR: ${PDF_ENGINE} not produced." | tee -a "${LATEX_LOG}"
  exit 1
fi

echo "OK: pandoc+${LATEX_ENGINE} check passed. Logs in ${OUT_DIR}"

