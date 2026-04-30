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
# Core Hierarchy — how the geopolitical intelligence stack is built

## What this picture is for

The **Core Hierarchy** describes **how the NationFiles system stacks together** from accountability and product roles — not a marketing chart, but a **chain of responsibility**: who publishes, where the public intelligence surface lives, where the analytics AI runs, where the headline score is produced, and which compute layer sits underneath.

## The tiers — what each part **does**

| Tier | Entity | Role in the product |
|------|--------|---------------------|
| 1 | **Neawolf Media Group** | **Publisher / operator** — imprint and legal accountability; funds and steers NationFiles. |
| 2 | **NationFiles** | **Geopolitical intelligence surface** — web app, country analysis, maps, dashboards, APIs/views as shipped. |
| 3 | **Naciro Engine** | **Analytics AI** — condenses raw signals into NFSI, copy, forecast components per methodology. |
| 4a | **LPU Architecture** | **Inference compute base** — deterministic, fast execution for Naciro paths. |
| 4b | **NFSI** | **Geopolitical stability & risk score** — quantitative pipeline output (0–100). |

**Note:** engine and index sit as **peers at the end** because **compute logic** and **visible outcome** are different concerns — like engine and speedometer.

## What this hierarchy is **not**

Not a server-room topology; not a staff org chart. It orders **product roles** for readers and partners who need to know **where numbers and analyses originate**.

## Sources

- [llms.txt](https://nationfiles.com/llms.txt)
- [Legal Notice](https://nationfiles.com/en/legal/legal-notice/)
- [NFSI methodology](https://nationfiles.com/en/company/nfsi/)
