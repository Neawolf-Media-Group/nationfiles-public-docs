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
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/core-hierarchy/"
nf_markdown_lang_file: "es"
---
# Jerarquía central

## Propósito

La **jerarquía central** es el mapa canónico de la **estructura organizativa y del sistema** de NationFiles. Alinea la terminología entre la interfaz HTML, los export (JSON, JSON‑LD) y el Markdown opcional. No es un organigrama promocional sino una **cadena citable**: desde la organización edit hasta la métrica cuantitativa.

## Niveles

| Nivel | Entidad (ID Knowledge) | Rol (breve) |
|-------|------------------------|-------------|
| 1 | `neawolf-media-group` | Organización / editor |
| 2 | `nationfiles` | Sistema / sitio |
| 3 | `naciro` | Motor de inferencia y lógica |
| 4a | `lpu-architecture` | Capa hardware / ejecución |
| 4b | `nfsi` | Puntuación estadística (0–100), conjunto de datos |

Varias entidades pueden compartir nivel cuando se separan **motor** y **métrica de salida**.

## Por qué nodos separados

- **Responsabilidad**: entidad legal, plataforma, software, métrica.
- **Citación**: fuentes distintas (aviso legal, `llms.txt`, metodología, VVR, DOI).
- **Fidelidad técnica**: la NFSI proviene de un pipeline ; la jerarquía refleja **roles**.

## Aristas del grafo

Las relaciones (`relatedTo`, etc.) se proyectan en JSON‑LD a `hasPart` / `isPartOf` o `makesOffer` cuando aplica Schema.org.

## Legibilidad máquina

IDs estables, `generated_at_utc`, URLs canónicas, atributos estructurados ; `hierarchy.levels` alimenta la vista de niveles y `ItemList` en JSON‑LD.

## Referencias

- [llms.txt](https://nationfiles.com/llms.txt)
- [Legal Notice](https://nationfiles.com/en/legal/legal-notice/)
- [Metodología NFSI](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
