---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/nfsi/fr.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/nfsi/"
nf_markdown_lang_file: "fr"
---
# NationFiles Stability Index (NFSI)

## Définition

Le **NFSI** est un **indicateur quantitatif sur une échelle 0–100** agrégeant la stabilité géopolitique / le profil de risque documenté. Le type **`Dataset`** souligne qu’il s’agit d’une **série indicielle** avec pipeline publiée.

## Bandes

Selon `score_bands` : A 81–100, B 61–80, C 41–60, D 21–40, E 0–20.

## Pipeline

Couches indicatrices → agrégation → score pondéré → lissage d’inertie ; détails dans le **VVR**.

## Couverture / cadence

195 pays ; cycle 15 minutes (`update_cycle`). Slot `global_current` peut afficher l’agrégat monde si résolu côté serveur.

## Licence / DOI

CC BY‑ND 4.0 ; DOI **10.5281/zenodo.19758890**.

## Références

- [Méthodologie NFSI](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
- [DOI](https://doi.org/10.5281/zenodo.19758890)
