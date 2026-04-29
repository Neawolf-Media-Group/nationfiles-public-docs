---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/core-hierarchy/en.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/core-hierarchy/"
nf_markdown_lang_file: "en"
---
# Core Hierarchy

## Purpose

The **Core Hierarchy** is the canonical map of NationFiles’ **organizational and system stack**. It aligns terminology across the HTML UI, exports (JSON, JSON‑LD), and optional Markdown. It is **not** a marketing ladder but a **citable chain**: from the legally filed publisher through the public system to inference, hardware semantics, and the quantitative metric.

## Layers at a glance

| Level | Entity (Knowledge ID) | Role (short) |
|------|------------------------|---------------|
| 1 | `neawolf-media-group` | Owning organization / publisher |
| 2 | `nationfiles` | System / website |
| 3 | `naciro` | Inference & logic engine |
| 4a | `lpu-architecture` | Hardware / execution layer (dedicated inference basis) |
| 4b | `nfsi` | Statistical score (0–100), dataset |

Multiple entities may share the same conceptual level when **engine logic** and **output metric** are modeled separately.

## Why separate nodes instead of one blob

- **Accountability**: legal entity vs. platform vs. software vs. metric.
- **Citation**: each layer has distinct sources (Legal Notice, `llms.txt`, methodology, VVR, DOIs).
- **Technical fidelity**: NFSI values are produced by a pipeline; the hierarchy reflects **roles**, not necessarily a single-parent infrastructure tree.

## Graph edges

Components link via `relations` (`relatedTo`, plus domain relations such as `computes`, `isPartOf`). JSON‑LD exports map selected edges to `hasPart` / `isPartOf` or `makesOffer` where Schema.org permits.

## Machine readability

- Stable **IDs** (kebab-case).
- Exports include **`generated_at_utc`**, **`canonical_url`**, structured attributes.
- **`hierarchy.levels`** powers the “Hierarchy levels” block and surfaces as `ItemList` in JSON‑LD.

## Entry references

- [llms.txt](https://nationfiles.com/llms.txt)
- [Legal Notice](https://nationfiles.com/en/legal/legal-notice/)
- [NFSI methodology](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
