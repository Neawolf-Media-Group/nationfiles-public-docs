---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/core-hierarchy/es.md"
nf_canonical_html_url: "https://nationfiles.com/es/knowledge/entity/core-hierarchy/"
nf_markdown_lang_file: "es"
---
# Core Hierarchy — arquitectura de la plataforma de inteligencia geopolítica

## Para qué sirve este esquema

La **Core Hierarchy** describe **cómo encaja el sistema NationFiles** en roles y responsabilidad — no un gráfico de marketing, sino una **cadena**: quién publica, dónde está la superficie pública, dónde corre la IA de análisis, dónde nace el indicador, qué capa de cálculo está debajo.

## Los niveles — qué hace cada pieza

| Nivel | Entidad | Rol en el producto |
|-------|---------|-------------------|
| 1 | **Neawolf Media Group** | **Editor / operador** — aviso legal y responsabilidad; financia y dirige NationFiles. |
| 2 | **NationFiles** | **Superficie de inteligencia geopolítica** — web, análisis por país, mapas, paneles, vistas API según producto. |
| 3 | **Naciro Engine** | **IA de análisis** — condensa señales en NFSI, textos, componentes de pronóstico según método. |
| 4a | **LPU Architecture** | **Base de cómputo de inferencia** — ejecución determinista y rápida para rutas Naciro. |
| 4b | **NFSI** | **Puntuación estabilidad / riesgo geopolítico** — salida cuantitativa de la pipeline (0–100). |

**Nota:** motor e índice están **al mismo nivel** porque **lógica de cálculo** y **resultado visible** son cosas distintas.

## Lo que esta jerarquía **no** es

No es topología de red ni organigrama de personal. Ordena **roles de producto** para lectores y socios que necesitan saber **de dónde salen números y análisis**.

## Fuentes

- [llms.txt](https://nationfiles.com/llms.txt)
- [Aviso legal](https://nationfiles.com/es/legal/legal-notice/)
- [Metodología NFSI](https://nationfiles.com/en/company/nfsi/)
