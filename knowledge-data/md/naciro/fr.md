---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/naciro/fr.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/naciro/"
nf_markdown_lang_file: "fr"
---
# Naciro Engine

## Fonction

Le **moteur Naciro** est modélisé comme **SoftwareApplication** : couche d’inférence qui transforme des signaux sourcés en **états intermédiaires normalisés** et **sorties scalaires** (notamment NFSI). La documentation insiste sur une **pipeline définie** (attribut `pipeline`, **VVR**), pas sur un assistant générique.

## Flux logique

Ingestion → agrégation / normalisation → corrections / ajustement prédictif → sortie NFSI ; cadence selon `update_cycle`.

## Périmètre

Pas de télémétrie runtime sur cette page ; renvois vers méthodologie publique et VVR.

## Références

- [Naciro AI](https://nationfiles.com/en/company/naciro-ai/)
- [Méthodologie NFSI](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
