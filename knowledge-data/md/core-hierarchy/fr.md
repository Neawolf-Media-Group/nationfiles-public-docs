---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/core-hierarchy/fr.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/core-hierarchy/"
nf_markdown_lang_file: "fr"
---
# Hiérarchie centrale

## Objectif

La **hiérarchie centrale** est la vue canonique de la **structure organisationnelle et système** de NationFiles. Elle aligne la terminologie entre l’interface HTML, les exports (JSON, JSON‑LD) et le Markdown optionnel. Il ne s’agit pas d’un organigramme marketing mais d’une **chaîne citable** : de l’éditeur juridique jusqu’à la métrique quantitative.

## Niveaux

| Niveau | Entité (ID Knowledge) | Rôle (bref) |
|--------|------------------------|-------------|
| 1 | `neawolf-media-group` | Organisation éditrice |
| 2 | `nationfiles` | Système / site |
| 3 | `naciro` | Moteur d’inférence et logique |
| 4a | `lpu-architecture` | Couche matérielle / exécution |
| 4b | `nfsi` | Indicateur statistique (0–100), jeu de données |

Plusieurs entités peuvent partager un même niveau lorsque la **logique moteur** et la **métrique de sortie** sont distinguées.

## Séparation des nœuds

- **Responsabilité** : entité légale, plateforme, logiciel, métrique.
- **Citation** : sources distinctes (mentions légales, `llms.txt`, méthodologie, VVR, DOI).
- **Exactitude technique** : la NFSI naît d’un pipeline ; la hiérarchie reflète des **rôles**.

## Liens du graphe

Les composants sont reliés par `relations` (`relatedTo`, relations métier). Les exports JSON‑LD projettent certaines arêtes vers `hasPart` / `isPartOf` ou `makesOffer` selon Schema.org.

## Lisibilité machine

Identifiants stables, horodatage `generated_at_utc`, URL canoniques, attributs structurés ; `hierarchy.levels` alimente le bloc visuel et `ItemList` en JSON‑LD.

## Références

- [llms.txt](https://nationfiles.com/llms.txt)
- [Legal Notice](https://nationfiles.com/en/legal/legal-notice/)
- [Méthodologie NFSI](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
