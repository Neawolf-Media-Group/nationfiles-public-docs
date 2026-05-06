# Deposit package (local, DOI pending)

This directory provides a **self-contained deposit bundle** for peer review and audit reproduction.
It is designed to be copied to OSF/Zenodo as-is.

## DOI / persistent identifier

- **DOI (Zenodo):** `10.5281/zenodo.20010874`

## Contents

- `manuscript/`: manuscript sources (Markdown) and generated PDF (if included).
- `figures/`: SVG + PNG figures referenced by the manuscript.
- `code/`: pinned code snapshot for `StabilityIndex v2.5` (repository snapshot at a specific commit).
- `sample-dataset/`: synthetic/redacted fixture dataset (>= 10 countries x 30 days).
- `schema.txt`: minimal SQL schema matching the fixture tables.
- `recompute/`: machine-executable recompute and validation scripts.
- `governance/`: audit-log schema, retention policy, and attestation template.
- `provenance/`: machine-readable connector provenance (CSV) and license appendix.
- `public-exports/`: raw JSON pulled from public export endpoints + manifest (optional but recommended for real-data validation).
- `annex_formulas.tex`: formal formulas appendix (LaTeX).
- `annex_formulas.json`: same formulas + test vectors (machine-readable).
- `backtest/`: backtest protocol and executable notebook (protocol paper evidence block).
- `validation/`: validation annex + sensitivity/robustness reporting templates (deposit-facing).

## Peer-review manuscript PDF (maintainers)

The comprehensive NFSI methods manuscript is **`research/algorithmic-geopolitics-nfsi-comprehensive-peer-review.md`**. Build artefacts are **not** part of the published paper body:

- **Canonical PDF** (NationFiles layout + KaTeX): `php research/build-peer-review-pdf.php`
- **Optional journal-style PDF** (Pandoc + LaTeX): `php research/build-peer-review-pdf.php --latex`

**Markdown math for editors:** use `$...$` / `$$...$$` for TeX; use fenced ` ```text ` for pseudocode. Do not put literal `$...$` placeholder examples in prose inside the manuscript — the HTML pipeline treats them as real math.

**Connector inventory fragment (§7.2 table in manuscript):** regenerate after provenance changes:

```bash
python3 research/tools/generate_connector_inventory_manuscript_fragment.py --write
```

## Quickstart (fixture recompute)

1) Create the tables (SQLite, Postgres, or MySQL; this is minimal portable SQL):

```bash
cat research/deposit/schema.txt
```

2) Load CSVs from `sample-dataset/` into the tables (method depends on DB).

3) Run:

```bash
php research/deposit/recompute/recompute_nfsi_fixture.php --fixture-dir research/deposit/sample-dataset --out /tmp/nfsi_fixture.csv
php research/deposit/recompute/run_unit_tests.php --fixture-dir research/deposit/sample-dataset
php research/deposit/recompute/check_invariants_fixture.php --fixture-dir research/deposit/sample-dataset
php research/deposit/recompute/validation_metrics.php --fixture-dir research/deposit/sample-dataset
```

3b) One-shot reviewer bundle (markdown audit → fixture recompute → playbooks → pandoc/lualatex smoke → PDF text sanity):

```bash
bash research/deposit/validate_recompute.sh
```

4) Optional (real-data, public endpoint snapshots):

```bash
php research/deposit/recompute/fetch_public_exports.php --out-dir=research/deposit/public-exports --iso2=usa,deu,ukr
php research/deposit/recompute/forecast_skill_persistence.php research/deposit/public-exports/usa__country_nfsi_30d.json --max-h=7
```

5) Optional (Pandoc -> LaTeX compile check; requires `pandoc` and `pdflatex` installed):

```bash
research/deposit/recompute/pandoc_latex_check.sh
```

5b) Optional (preflight: catch escaping / HTML / Unicode risks early):

```bash
python3 research/deposit/recompute/latex_preflight.py research/algorithmic-geopolitics-nfsi-comprehensive-peer-review.md --run-pandoc-latex-check --run-pdf-text-sanity
```

6) Backtest protocol artefacts:

- Protocol: `research/deposit/backtest/backtest_protocol.md`
- Notebook: `research/deposit/backtest/backtest_protocol.ipynb`

## Sensitivity sweep template (protocol scaffold)

To generate a sweep plan (parameter grid + required outputs):

```bash
python3 research/deposit/recompute/sensitivity_sweep_template.py --out research/deposit/backtest/sensitivity_plan.csv.txt
```

