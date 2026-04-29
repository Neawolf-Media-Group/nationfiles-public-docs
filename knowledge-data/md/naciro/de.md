---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/naciro/de.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/naciro/"
nf_markdown_lang_file: "de"
---
# Naciro Engine

## Funktion

Die **Naciro Engine** ist im Knowledge Graph als **SoftwareApplication** modelliert: logische und inferenzbezogene Schicht, die **Signale** aus dem dokumentierten Quellenraum in **normalisierte Zwischenzustände** und **skalare Ausgaben** (v. a. den NFSI) überführt. Sie ist bewusst **nicht** als generischer Chatbot beschrieben, sondern als **Engine mit definierter Pipeline** (siehe Attribut `pipeline` und VVR).

## Datenfluss (logisch)

1. **Aufnahme** validierter und Echtzeit‑Quellen (Anzahl `115+` als dokumentiertes Aggregat, nicht als Live‑Zähler auf dieser Seite).
2. **Aggregation & Normalisierung** gemäß Methodik‑Layer (Details im **Validation & Verification Report**).
3. **Bias‑Korrektur / Predictive Adjustment** wie im VVR referenziert.
4. **Output**: Welt‑ und Länderscores NFSI; Aktualisierungsrhythmus laut Attribut `update_cycle` (15‑Minuten‑Takt als dokumentierte Vorgabe).

## Grenzen der Darstellung

Diese Knowledge‑Seite enthält **keine Live‑Telemetrie** der Engine (CPU, GPU‑Auslastung, Queue‑Längen). Sie verweist auf **öffentliche Methodik** und **rechtliche Dokumente**.

## Graph‑Relationen

- `computes` → `nfsi`
- `published_via` → `nationfiles`
- `operator` über Attribute auf `neawolf-media-group`

## Referenzen

- [Naciro AI (Company)](https://nationfiles.com/en/company/naciro-ai/)
- [NFSI Methodik](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
