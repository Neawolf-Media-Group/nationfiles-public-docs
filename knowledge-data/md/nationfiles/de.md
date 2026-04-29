---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/nationfiles/de.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/nationfiles/"
nf_markdown_lang_file: "de"
---
# NationFiles (System)

## Definition

**NationFiles** ist die **öffentliche Web-Präsenz und das gebündelte Informationssystem**, unter dem Ländertexte, NFSI‑Auswertungen, Knowledge‑Exports und sprachpräfixierte Routen bereitgestellt werden. Schema.org‑Typ `WebSite` spiegelt wider, dass es sich um eine **Adresse mit deterministischen Pfaden** handelt (`/{lang}/…`), nicht um eine lose Sammlung von Markdown‑Dateien.

## Sprachen und Routing

| Sprach‑Prefix | Status |
|---------------|--------|
| `de`, `en`, `fr`, `es`, `pt`, `ar`, `ja` | Kanonisch im Knowledge‑Hub dokumentiert |

Alle Knowledge‑Entitäten sind unter denselben Sprachpräfixen erreichbar; hreflang wird layoutweit gesetzt.

## Operative Kopplungen

- **Operator**: `neawolf-media-group` (Graph‑Relation `operator`).
- **System‑Engine**: `naciro` — beschreibt die Schicht, unter der globale Neuberechnungen und Forecast‑Horizonte geführt werden (Attribute `system_engine`).
- **Kernmetrik**: `nfsi` — die öffentlich ausgewiesene Kennzahl (Relation `core_metric` über Attribute).

## Outputs (faktisch)

Laut dokumentierten Attributen u. a.: **Country‑Dashboards**, **NFSI‑Index**, **24h / 7‑Tage‑Signals**, **maschinenlesbare Exporte**. Konkrete UI‑Screens können sich weiterentwickeln; die Knowledge‑Entität beschreibt die **Rolle** des Systems, nicht jedes Einzelwidget.

## Lizenz / Maschinenhinweise

Die Root‑Datei **`llms.txt`** beschreibt Crawling‑ und Nutzungsregeln; Knowledge‑Exporte verlinken kanonische URLs unter `nationfiles.com` und optional CDN‑Spiegel für JSON.

## Referenzen

- [llms.txt](https://nationfiles.com/llms.txt)
- [Countries Hub](https://nationfiles.com/en/countries/)
- [Knowledge Hub](https://nationfiles.com/en/knowledge/)
