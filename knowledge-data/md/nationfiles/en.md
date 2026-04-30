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
# NationFiles — geopolitical intelligence platform

## What NationFiles **is**

**NationFiles** is a **geopolitical analysis and intelligence platform**. It processes **country-level and global data streams** — economic and market signals, security and situational feeds, news and sentiment — and turns them into a **single operational surface** for reading the world: country dossiers, maps and radar, dashboards, indicators, and — where enabled — briefings and forecast windows. The design intent is a hybrid of **deep reference-style country intelligence** and **continuously refreshed indicators** that are not limited to annual reports.

The **unit of record** is the standardized, machine-readable **{{nationfile-json}}** profile; **NFSI** and other published analytics are built on that structure. **Roles:** NationFiles as the overall system, **Naciro** as the engine, **NFSI** as the index, **{{nationfile-json}}** as the data format.

The public site (`/{lang}/…`) is the **product** users interact with. Behind it, documented data pipelines (including via the DataSourceConnector) and the **Naciro Engine** supply scores, charts, and narrative context.

## What NationFiles **delivers in practice**

- **Country analysis:** Structured, comparable profiles for sovereign states and regions — multilingual.
- **Spatial context:** Maps and radar views so risks and indicators can be read **geographically**, not only as tables.
- **Measurable stability and risk:** The **NationFiles Stability Index (NFSI)** compresses documented inputs into a 0–100 scale; methodology explains layers and limits.
- **Fresh situational data:** Regular recomputation on a documented cadence — aimed at analysts, media, and institutions that need **current** situational pictures.
- **Predictive layer (as shipped):** Short-horizon assessments and forecast windows (e.g. 24h / 7-day) where the product exposes them — not a guarantee for every breaking headline.

## Role of **Naciro** and **NFSI**

- **Naciro** is the **AI-driven evaluation engine** for NationFiles: it aggregates signals from connected sources, normalises and weights them per published methodology, and drives the NFSI plus dashboards and reports.
- The **NFSI** is the platform’s **headline geopolitical stability / risk score** at country level (and as a world aggregate) — a rule-based index, not an editorial opinion score.

## What NationFiles is **not**

Not personal investment or travel advice; not a government agency; not a substitute for on-the-ground primary reporting — it is a **data and analysis product** with a documented external pipeline.

## Transparency & citation

Methodology (**Validation & Verification Report**), source registry, and legal pages explain provenance and permitted use. For automated systems: **`llms.txt`**.

## Further reading

- [Countries hub](https://nationfiles.com/en/countries/)
- [NFSI methodology](https://nationfiles.com/en/company/nfsi/)
- [Naciro AI](https://nationfiles.com/en/company/naciro-ai/)
- [Sources / legal](https://nationfiles.com/en/legal/sources/)
