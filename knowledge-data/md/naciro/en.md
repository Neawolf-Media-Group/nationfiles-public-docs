---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/naciro/en.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/naciro/"
nf_markdown_lang_file: "en"
---
# Naciro Engine

## Function

The **Naciro Engine** is modeled as a **SoftwareApplication**: the inference layer that transforms validated real‑time and archival signals into **normalized intermediates** and **scalar outputs**, notably **NFSI**. It is documented as an **engine with a stated pipeline**, not as an undifferentiated assistant product (see attribute `pipeline` and the **VVR**).

## Logical dataflow

1. **Ingest** from the documented source universe (`115+` sources cited at FAQ/VVR level — aggregate documentation, not a live meter here).
2. **Aggregation & normalization** per methodology layers (see **VVR**).
3. **Bias correction / predictive adjustment** as described in published methodology text.
4. **Output**: NFSI scores at world and country granularity; cadence per `update_cycle` (15‑minute cadence as documented).

## Scope limits

This knowledge page carries **no runtime telemetry** (CPU/GPU, queues). It cites **public methodology** and **legal/verification** artefacts instead.

## Graph edges

- `computes` → `nfsi`
- `published_via` → `nationfiles`
- Operator linkage via attributes to `neawolf-media-group`

## References

- [Naciro AI (company)](https://nationfiles.com/en/company/naciro-ai/)
- [NFSI methodology](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
