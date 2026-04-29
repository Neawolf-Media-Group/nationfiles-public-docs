---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/core-hierarchy/pt.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/core-hierarchy/"
nf_markdown_lang_file: "pt"
---
# Hierarquia central

## Objetivo

A **hierarquia central** é o mapa canónico da **estrutura organizacional e de sistema** da NationFiles. Alinha a terminologia entre a interface HTML, exportações (JSON, JSON‑LD) e Markdown opcional. Não é um organograma de marketing, e sim uma **cadeia citável**: da entidade legal à métrica quantitativa.

## Níveis

| Nível | Entidade (ID Knowledge) | Papel (resumo) |
|-------|-------------------------|-----------------|
| 1 | `neawolf-media-group` | Organização / editora |
| 2 | `nationfiles` | Sistema / site |
| 3 | `naciro` | Motor de inferência e lógica |
| 4a | `lpu-architecture` | Camada hardware / execução |
| 4b | `nfsi` | Pontuação estatística (0–100), conjunto de dados |

Várias entidades podem compartilhar o nível quando **motor** e **métrica de saída** são modelados em separado.

## Nós distintos

- **Responsabilidade**: pessoa jurídica, plataforma, software, métrica.
- **Citação**: fontes distintas (legal notice, `llms.txt`, metodologia, VVR, DOI).
- **Fidelidade técnica**: a NFSI vem de um pipeline ; a hierarquia reflete **papéis**.

## Arestas

`relations` (`relatedTo`, etc.) mapeiam em JSON‑LD para `hasPart` / `isPartOf` ou `makesOffer` quando cabível.

## Leitura automática

IDs estáveis, `generated_at_utc`, URLs canônicas, atributos estruturados ; `hierarchy.levels` alimenta o bloco visual e `ItemList` no JSON‑LD.

## Referências

- [llms.txt](https://nationfiles.com/llms.txt)
- [Legal Notice](https://nationfiles.com/en/legal/legal-notice/)
- [Metodologia NFSI](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
