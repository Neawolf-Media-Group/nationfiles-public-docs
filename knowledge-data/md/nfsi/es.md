---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/nfsi/es.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/nfsi/"
nf_markdown_lang_file: "es"
---
# NationFiles Stability Index (NFSI)

## Definición

El **NFSI** es un **índice cuantitativo 0–100** que agrega estabilidad geopolítica / perfil de riesgo documentado. Como **`Dataset`** subraya que es una **serie métrica** con pipeline declarada.

## Bandas

`score_bands`: A 81–100 … E 0–20.

## Pipeline

Capas de indicadores → agregación → puntuación ponderada → suavizado de inercia; detalle en **VVR**.

## Cobertura / cadencia

195 países ; ciclo 15 minutos (`update_cycle`). El slot `global_current` puede mostrar el agregado mundial si el servidor lo resuelve.

## Licencia / DOI

CC BY‑ND 4.0 ; DOI **10.5281/zenodo.19758890**.

## Referencias

- [Metodología NFSI](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
- [DOI](https://doi.org/10.5281/zenodo.19758890)
