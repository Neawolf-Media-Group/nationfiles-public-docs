---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/core-hierarchy/de.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/core-hierarchy/"
nf_markdown_lang_file: "de"
---
# Core Hierarchy

## Zweck und Anwendungsfall

Die **Core Hierarchy** ist die kanonische Abbildung der **System- und Organisationsstruktur** von NationFiles. Sie ordnet die Akteure und Schichten so, dass dieselbe Terminologie in der Web-Oberfläche, in Exporten (JSON, JSON-LD) und in optionalen Markdown-Dateien verwendet wird. Ziel ist **keine Marketing-Hierarchie**, sondern eine **referenzierbare Kette**: von der juristisch geführten Organisation über das öffentliche System bis zu Inferenz, Hardware-Metapher und quantitativer Metrik.

## Ebenen im Überblick

| Ebene | Entität (Knowledge-ID) | Rolle (Kurz) |
|------|---------------------------|--------------|
| 1 | `neawolf-media-group` | Trägerorganisation / Publisher |
| 2 | `nationfiles` | System- und Web-Präsenz |
| 3 | `naciro` | Inferenz- und Logik-Engine |
| 4a | `lpu-architecture` | Hardware- bzw. Ausführungsschicht (dedizierte Inferenz-Basis) |
| 4b | `nfsi` | Statistische Kennzahl (0–100), Dataset |

Auf derselben fachlichen Stufe können **mehrere** Entitäten stehen (hier: LPU und NFSI), weil **Logik/Engine** und **Output-Metrik** getrennt modelliert werden.

## Warum getrennte Knoten statt „alles in einer Kiste“

- **Verantwortung**: Organisation (Recht, Impressum) vs. Plattform vs. Software vs. Metrik.
- **Zitierfähigkeit**: Jede Schicht hat eigene Quellen (Legal Notice, `llms.txt`, Methodik, VVR, DOI).
- **Technische Wahrheit**: Die NFSI-Werte entstehen in einer Pipeline; die Hierarchie spiegelt **Rollen**, nicht unbedingt einen linearen Single-Parent-Baum in der Infrastruktur.

## Beziehungen im Graph

In dieser Knowledge-Base sind die Komponenten über `relations` mit `relatedTo` (und teils Fach-Relationen wie `computes`, `isPartOf`) verknüpft. Im **JSON-LD-Export** werden ausgewählte Relationen als `hasPart` / `isPartOf` oder als `makesOffer` abgebildet, sofern Schema.org und der Entity-Typ das hergeben.

## Maschinenlesbarkeit

- Jede Entität besitzt eine **stabile ID** (kebab-case).
- **Exporte** enthalten `generated_at_utc`, `canonical_url` und strukturierte Attribute.
- **Hierarchie-Metadaten** (`hierarchy.levels`) dienen der Visualisierung des Blocks „Hierarchieebenen“ und fließen im JSON-LD u. a. als `ItemList` ein.

## Referenzen (einstieg)

- [llms.txt](https://nationfiles.com/llms.txt) – maschinenlesbare Einstiegsregeln
- [Legal Notice](https://nationfiles.com/en/legal/legal-notice/) – Impressum / Rechtsrahmen
- [NFSI Methodik](https://nationfiles.com/en/company/nfsi/) – Score und Datenzyklus
- [VVR (Validation & Verification Report)](https://nationfiles.com/en/legal/validation-and-verification-report/) – methodische Vertiefung
