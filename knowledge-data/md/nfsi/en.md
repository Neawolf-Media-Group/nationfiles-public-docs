---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/nfsi/en.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/nfsi/"
nf_markdown_lang_file: "en"
---
# NationFiles Stability Index (NFSI)

## Definition

The **NFSI** is a **quantitative index on a 0–100 scale** summarizing geopolitical stability / documented risk posture as an aggregate signal. Modeled as a **`Dataset`** to stress that it is a **time‑series style metric** with a stated computation pipeline—not a prose country brief.

## Bands

Per attribute `score_bands`:

| Band | Range |
|------|--------|
| A | 81–100 |
| B | 61–80 |
| C | 41–60 |
| D | 21–40 |
| E | 0–20 |

Band assignment follows the layered methodology in the **VVR** and methodology pages.

## Pipeline (high level)

Attribute `pipeline`: Layer 1 indicators → Layer 2 aggregation → Layer 3 weighted final score → Layer 4 inertia smoothing. Numerical detail lives in the **VVR**, not this summary card.

## Coverage and cadence

- **Countries**: 195 (`country_coverage`).
- **Cadence**: 15‑minute recalculation per `update_cycle` (documented policy).
- **Live slot**: When enabled, `global_current` may surface the latest **world aggregate** via server resolver `get_last_score_values()`; static JSON exports remain point‑in‑time snapshots.

## License & DOI

`license`: **CC BY‑ND 4.0** (see Legal Notice linkage).  
`citation_doi`: **10.5281/zenodo.19758890**.

## References

- [NFSI methodology](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
- [DOI record](https://doi.org/10.5281/zenodo.19758890)
