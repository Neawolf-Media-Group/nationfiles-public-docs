---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/nfsi/de.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/nfsi/"
nf_markdown_lang_file: "de"
---
# NationFiles Stability Index (NFSI)

## Definition

Der **NFSI** ist ein **quantitativer Index auf der Skala 0–100**, der geopolitische Stabilität bzw. das dokumentierte Risikoprofil **aggregiert** darstellt. Im Knowledge Graph ist er als **`Dataset`** modelliert, um klarzustellen: Es handelt sich um eine **zeitlich aktualisierte Kennzahlenserie** mit beschriebener Pipeline — nicht um einen narrativen Länderartikel.

## Skala und Bänder

| Band | Punktebereich (laut Attribut `score_bands`) |
|------|-----------------------------------------------|
| A | 81–100 |
| B | 61–80 |
| C | 41–60 |
| D | 21–40 |
| E | 0–20 |

Die Zuordnung eines Landes oder der Welt zu einem Band erfolgt rechnerisch gemäß der im **VVR** und der Methodenseite beschriebenen Schichten.

## Pipeline (high level)

Attribut `pipeline` (Kurznotation): Layer 1 Indikatoren → Layer 2 Aggregation → Layer 3 gewichteter Endscore → Layer 4 Trägheit / Glättung („inertia smoothing“). Detailgrade stehen im **Validation & Verification Report**, nicht auf dieser Übersichtsseite.

## Aktualisierung und Abdeckung

- **Länder**: 195 (Attribut `country_coverage`).
- **Zyklus**: alle 15 Minuten Neuaufbereitung laut `update_cycle` (systemische Vorgabe, keine Echtzeit‑CPU‑Anzeige hier).
- **Live‑Slot**: Auf dieser Seite kann — falls konfiguriert — der **aktuelle Welt‑Aggregatwert** aus dem Slot `global_current` erscheinen; Quelle Server‑Funktion `get_last_score_values()`, nicht der statische JSON‑Export allein.

## Lizenz und DOI

Attribut `license`: **CC BY‑ND 4.0** (laut Verweis auf Legal Notice).  
`citation_doi`: **10.5281/zenodo.19758890** — für akademisches Zitieren und Datenreferenz.

## Referenzen

- [NFSI Methodik](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
- [Research DOI](https://doi.org/10.5281/zenodo.19758890)
