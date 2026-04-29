---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/lpu-architecture/fr.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/lpu-architecture/"
nf_markdown_lang_file: "fr"
---
# Architecture LPU

## Rôle

**LPU Architecture** désigne la **couche matérielle d’inférence dédiée** sous le moteur Naciro : exigences de **déterminisme et faible latence** pour les chemins de signaux alimentant les charges NFSI. Le type **`CreativeWork`** et le DOI encadrent la fiche comme **document d’architecture**, pas comme fiche produit dynamique.

## Latence

`inference_latency` : **< 50 ms** (objectif documenté, pas mesure live).

## DOI

**10.5281/zenodo.19774594** — source primaire pour le détail technique.

## Contexte

Moteur `naciro`, métrique `nfsi`, diffusion via `nationfiles`.

## Références

- [Naciro AI](https://nationfiles.com/en/company/naciro-ai/)
- [Méthodologie NFSI](https://nationfiles.com/en/company/nfsi/)
- [DOI](https://doi.org/10.5281/zenodo.19774594)
