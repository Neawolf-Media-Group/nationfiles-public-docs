---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/knowledge-hub-nfkg/en.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/knowledge-hub-nfkg/"
nf_markdown_lang_file: "en"
---
# Knowledge Hub (NFKG): order instead of unverified million-node claims

Documentation that people can **audit** needs a layer that explains how **IDs**, **relations**, and **seven-locale copy** stay aligned. The **Knowledge Hub** plus **NationFiles Knowledge Graph (NFKG)** is that layer: a **curated** bundle of entities, FAQs, glossary entries, and optional Markdown, rendered under `/{lang}/knowledge/` and published for machines through **HTTPS** export endpoints.

The **semantic network** is a **contract**, not mysticism: each node keeps a stable identifier; edges come from explicit **`relations`** records; topics and aliases widen fuzzy search—they **must not** silently duplicate legal identities.

**CDN-facing exports** pair legal distribution metadata with operational JSON: cite the **HTTPS** URLs documented for `/knowledge/export/…`, not scraped HTML fragments. The **AI guidelines** spell out the behavioural frame for automated clients.

**On headline node counts:** roadmaps sometimes cite **very large** graphs. NFKG prose may repeat such numbers **only** when the **identical** figure, with **defined scope**, appears in the **published export index** metadata attached to the live site. Otherwise the honest move is to **omit** the number and point readers to the exports that **do** carry cardinality.

- [Knowledge hub](https://nationfiles.com/en/knowledge/)
- [Graph view](https://nationfiles.com/en/knowledge/graph/)
- [Live export index](https://nationfiles.com/en/knowledge/export/index.json)
- [This entity](https://nationfiles.com/en/knowledge/entity/knowledge-hub-nfkg/)
