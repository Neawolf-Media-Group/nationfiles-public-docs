---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/nationfiles/en.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/nationfiles/"
nf_markdown_lang_file: "en"
---
# NationFiles (system)

## Definition

**NationFiles** is the **public web presence and bundled information system** that delivers country narratives, NFSI analytics, knowledge exports, and language‑prefixed routes. The Schema.org type `WebSite` reflects a stable entry point with **deterministic paths** (`/{lang}/…`), not an ad‑hoc folder of files.

## Languages

Canonical prefixes documented on this entity: **`de`, `en`, `fr`, `es`, `pt`, `ar`, `ja`**. Global hreflang wiring lives in the layout templates.

## Operational links

- **Operator**: `neawolf-media-group`.
- **Engine**: `naciro` — inference stack referenced by `system_engine`.
- **Metric**: `nfsi` — primary score surfaced publicly (`core_metric`).

## Outputs (as attributed)

Per entity attributes: country dashboards, NFSI index, short‑horizon outlook signals, machine‑readable exports. Individual UI modules may evolve; the entity states **system responsibilities**, not each widget version.

## Policy hints

**`llms.txt`** governs crawler etiquette at the site root. Knowledge exports cite canonical `https://nationfiles.com/...` URLs.

## References

- [llms.txt](https://nationfiles.com/llms.txt)
- [Countries hub](https://nationfiles.com/en/countries/)
- [Knowledge hub](https://nationfiles.com/en/knowledge/)
