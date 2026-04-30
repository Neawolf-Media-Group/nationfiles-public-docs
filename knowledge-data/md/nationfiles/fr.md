---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/nationfiles/fr.md"
nf_canonical_html_url: "https://nationfiles.com/fr/knowledge/entity/nationfiles/"
nf_markdown_lang_file: "fr"
---
# NationFiles — plateforme d’intelligence géopolitique

## Ce qu’est NationFiles

**NationFiles** est une **plateforme d’analyse et d’intelligence géopolitique**. Elle traite **les pays et les flux de données mondiaux** — signaux économiques et de marché, informations de sécurité et de situation, actualités et sentiment — et les transforme en **surface opérationnelle unique** pour lire le monde : dossiers pays, cartes et radar, tableaux de bord, indicateurs, et — lorsque c’est activé — synthèses et fenêtres prévisionnelles. L’objectif est un hybride entre **profondeur encyclopédique** et **indicateurs régulièrement actualisés**.

Le site public (`/{lang}/…`) est le **produit**. En coulisse, des pipelines documentés (dont via DataSourceConnector) et le moteur **Naciro** alimentent scores, graphiques et contexte.

**Unité de données :** les profils standardisés **{{nationfile-json}}** (machine-readables) servent de base commune aux indicateurs, graphiques et exports. **Rôles :** NationFiles = système global, **Naciro** = moteur, **NFSI** = indice principal, **{{nationfile-json}}** = format canonique des profils.

## Ce que la plateforme livre concrètement

- **Analyse pays :** profils structurés et comparables pour États et régions — multilingue.
- **Contexte spatial :** cartes et vues radar pour lire risques et indicateurs **géographiquement**.
- **Stabilité et risque mesurables :** l’**indice NationFiles Stability Index (NFSI)** résume des entrées documentées sur une échelle 0–100 ; la méthodologie précise les couches et les limites.
- **Données de situation à jour :** recalcul régulier selon un rythme documenté — pour analystes, médias et institutions.
- **Couche prédictive (selon l’offre) :** horizons courts (p. ex. 24h / 7 jours) là où le produit les expose — pas une garantie sur chaque événement.

## Rôle de **Naciro** et du **NFSI**

- **Naciro** est le **moteur d’évaluation assistée par IA** : agrégation des signaux des sources connectées, normalisation et pondération selon la méthode publiée ; alimentation du NFSI et des reportings.
- Le **NFSI** est l’**indicateur central de stabilité / risque géopolitique** au niveau pays (et agrégat mondial) — un indice régi par des règles documentées, pas une note éditoriale.

## Ce que NationFiles n’est pas

Pas de conseil personnel en investissement ou voyage ; pas un organe étatique ; pas un substitut au reportage de terrain — un **produit données et analyse** avec chaîne documentée vers l’extérieur.

## Transparence

Méthodologie (**Validation & Verification Report**), registre des sources, pages légales. Pour systèmes automatisés : **`llms.txt`**.

## Pour aller plus loin

- [Hub pays](https://nationfiles.com/en/countries/)
- [Méthodologie NFSI](https://nationfiles.com/en/company/nfsi/)
- [Naciro AI](https://nationfiles.com/en/company/naciro-ai/)
- [Sources](https://nationfiles.com/en/legal/sources/)
