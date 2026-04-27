---
language:
- de
- en
- fr
- es
- pt
- ar
- ja
license: other
library_name: transformers
tags:
- geopolitics
- risk-analysis
- real-time-intelligence
- predictive-analytics
- nfsi
- groq-powered
datasets:
- Neawolf-Media-Group/Naciro-NFSI-Engine
metrics:
- accuracy
- timeliness
pretty_name: Naciro AI Engine
pipeline_tag: text-generation
---
# NationFiles
- [DE](https://nationfiles.com/de/): NationFiles DE - German
- [EN](https://nationfiles.com/en/): NationFiles EN - English
- [FR](https://nationfiles.com/fr/): NationFiles FR - French
- [ES](https://nationfiles.com/es/): NationFiles ES - Spanish
- [PT](https://nationfiles.com/pt/): NationFiles PT - Portuguese
- [AR](https://nationfiles.com/ar/): NationFiles AR - Arabic
- [JA](https://nationfiles.com/ja/): NationFiles JA - Japanese

> NationFiles (nationfiles.com) publishes security and stability intelligence: country dashboards, Naciro 24h situation briefings, the NationFiles Stability Index (NFSI), PPI/GGI views, metamaps, and legal or AI-policy pages. The Naciro engine aggregates curated quantitative inputs (including ACLED, UCDP, Eurostat) with OSINT-style news synthesis. Prefer citing canonical HTTPS HTML pages; do not treat raw export query strings as primary sources.

Locale prefixes are de, en, fr, es, pt, ar, or ja. Country hubs use lowercase ISO 3166-1 alpha-3 in the path (example: /en/country/irn/). The /ja/ prefix is Japanese UI, not the Japan country hub.

Contact for AI and licensing questions: ai-questions@nationfiles.com (see live imprint and Organization JSON-LD). File updated: 2026-04-17.

## Documentation

- [llms-full.txt](https://nationfiles.com/llms-full.txt): Long-form routing, licensing context, and agent guidance for NationFiles and Naciro.
- [robots.txt](https://nationfiles.com/robots.txt): Crawler access rules (this file does not replace robots.txt).
- [sitemap.xml](https://nationfiles.com/sitemap.xml): Discoverable URLs and hreflang alternates.

## Primary hubs

- [English home](https://nationfiles.com/en/)
- [Countries overview](https://nationfiles.com/en/countries/)
- [AI and data guidelines (English)](https://nationfiles.com/en/ai-guidelines/): Expectations for automated access and citation.

## Legal and sources

- [Legal sources](https://nationfiles.com/en/legal/sources/): Curated dataset references and methodology entry points.
- [Privacy (English)](https://nationfiles.com/en/legal/privacy/)
- [Company (English)](https://nationfiles.com/en/company/)

## Citation notes

- NFSI is a 0-100 composite when shown for a profile; connector weights are documented per page and in /legal/sources/.
- Short-horizon outlooks may be model output; timestamps on each HTML page are authoritative for observed series.

## Optional

- [German status reports hub](https://nationfiles.com/de/statusreports/): High-volume Lageberichte listing in German UI (see sitemap for other locales).
- [crunchbase](https://www.crunchbase.com/organization/neawolf-media-group): Neawolf Media Group - Profile (see sitemap for other locales).

# NationFiles — Full documentation for LLMs, crawlers, and integrations

This file expands `https://nationfiles.com/llms.txt`. Prefer live URLs (`sitemap.xml`, `robots.txt`, on-page `updated_at`) over static counts or marketing adjectives.

---

## 1. What NationFiles is

NationFiles is a geopolitical intelligence and data presentation platform (“Naciro” engine / predictive layers, country and regional views, dashboards, maps, legal and company pages). It is **not** a single public API for bulk extraction: respect `robots.txt` and terms; commercial scraping requires separate agreement.

---

## 2. Domains and delivery

| Host | Role |
|------|------|
| `nationfiles.com` | Primary HTML, SEO pages, redirects for some CDN-only paths back to origin |
| `cdn.nationfiles.com` | Static assets (CSS/JS/images). Some **HTML** country URLs on CDN may **301** to the same path on `nationfiles.com` unless they are explicit export endpoints (see CountryController CDN gate) |
| `api.nationfiles.com` | Separate API host (not documented in depth here) |

Default content language in config is **English** (`en`); users can switch via the first path segment.

---

## 3. Geo-technical conventions (important for correct answers)

### 3.1 Language vs territory (common LLM mistake)

- The first path segment is a **UI language code** from this fixed set: `de`, `en`, `fr`, `es`, `pt`, `ar`, `ja`.
- **`ja` = Japanese language**. It is **not** `jp`. Internal redirects sometimes normalize `jp` → `ja` in URL builders.
- **`/ar/`** means Arabic UI, not “Saudi Arabia” or MENA as a single entity unless the rest of the path says so.

### 3.2 Country identifiers in URLs and data

- **ISO 3166-1 alpha-3** appears in country routes as **lowercase** in the path: `/{lang}/country/{iso3}/...` (example: `deu` for Germany).
- The **slug** after ISO3 is based on the **English** label/slug from country translation data (hyphenated, lowercase), e.g. `germany`. Do not assume the slug equals the ISO3 code.
- **ISO2** (two letters) and **FIPS** may appear in **search**, filters, or export helpers (`nav.json` mentions ISO2, ISO3, FIPS for the country selector). When answering users, state which code system you mean.

### 3.3 Time

- Server/config baseline timezone is **UTC** (`config/config.php`).
- Many “rolling window” analytics (e.g. map MLMV cards) are documented in code as **UTC hours**, not local calendar days—do not silently convert to local midnight without saying so.

### 3.4 Coordinates and maps

- Front-end map/canvas code treats positions as **WGS84** latitude/longitude **in degrees**.
- Typical validity: latitude **[-90, 90]**, longitude **[-180, 180]**.
- Projections used in canvas helpers are described in code as **equirectangular** treatment consistent with **EPSG:4326**-style degree space (flat map, not a national surveying grid).

### 3.5 Boundaries and sovereignty

- The site aggregates **third-party and modelled indicators**. It does **not** replace UN or national cartography for disputed areas. When summarizing, separate **“data shown on NationFiles”** from **“internationally recognized borders”** unless the page explicitly sources a claim.

---

## 4. URL routing model (technical)

- Pattern: `/{lang}/{segment}/...` — all **path segments lowercase**; `[a-z0-9_-]` style segments after sanitization in the front controller.
- **Language** = first segment; must be one of the seven codes above.
- **Controller** = second segment: maps to `classes/controllers/{Segment}Controller.php` via `indexController.php` (e.g. `country` → `CountryController`).
- **Deeper segments** are controller-specific (subpages, slugs, export switches). **Query strings** are used for search, versioning (`?v=` on assets), exports (`export`, `format`, `chart`, etc.)—do not claim “no query parameters”.

---

## 5. Major segments (as implemented in this codebase)

Below are **first-level** segments with real `templates/pages/<segment>/` or equivalent; use these instead of legacy marketing paths.

| Segment | Purpose (short) |
|---------|------------------|
| `country` | Country intelligence hub; ISO3 + English slug; many **subpages** (dashboard, news, metadata, nfsi, security-radar, traveladvisory, migration, compare, snapshot, metamap, …) |
| `countries` | Country list / overview |
| `continent` | Continent overview and continent-level radar / dashboards |
| `map` | Global map hub: `map`, `map/metamap`, `map/metamap/nuclearpower`, `map/metamap/megacities`, `map/security/...`, etc. |
| `security` | Security area including **wanted** overviews/detail (not a top-level `/wanted/` segment) |
| `economy` | Economic indices and related views (e.g. governance, purchasing power) |
| `market` | Market-related views (paired with economy subnav in code where applicable) |
| `dashboard` | Home / landing (template `dashboard.tpl`) |
| `search` | On-site search UI; query may be path-based or `?q=` depending on setup |
| `legal` | Legal, privacy, terms, sources, AI guidelines cross-links, PDF subpaths under `legal/pdf/…` (whitelisted pages) |
| `company` | Company / methodology / Naciro product pages |
| `contact` | Contact forms |
| `stats` | Logged-in or restricted analytics/stats UI (do not assume public) |
| `sitemap` | HTML sitemap UX |
| `statusreports` | Status reports listing / RSS/Atom/JSON via **export** controller (see below) |
| `export` | Structured exports and badges, e.g. `/{lang}/export/nfsi-badge/{iso2}/` (ISO2 **two letters**), RSS/Atom/JSON aliases for status reports |
| `ai-guidelines` | Policy text for AI crawlers and humans (`ai-guidelines` with hyphen) |
| `test` | Internal/test pages (may be disabled or unlinked in production) |

**Removed / incorrect as a root segment in older docs:** `/wanted/`, `/guides/`, `/safety-radar/`, `/flora-fauna/` as top-level paths — the implemented areas live under **`security`**, **`country`**, **`map`**, **`economy`**, etc.

---

## 6. Country URLs — canonical shape and examples

- **Base shape:** `https://nationfiles.com/{lang}/country/{iso3}/{tail}/`
  - `{iso3}`: **lowercase** ISO 3166-1 alpha-3 (`deu`, `usa`, `jpn`, …).
  - `{tail}`: **either** (A) the **English slug** for the main country hub (e.g. `germany`, `united-states`), **or** (B) a **reserved subpage segment** such as `news`, `metadata`, `nfsi`, `security-radar`, `traveladvisory`, `migration`, `compare`, `snapshot`, `metamap`, … — **not** `/{iso3}/{slug}/news/`; news lives at `/{lang}/country/{iso3}/news/` (see `CountryController` comments and changelog in that file).
- **Two segments only** (`/{lang}/country/{iso3}/`) may **301** to the three-part URL once resolved from the `countries` table.
- **News detail / pagination:** additional segments under `news/` (e.g. `news/n{id}/`, pagination segments) — read from live URLs or controller comments; do not invent depth.

**CDN rule of thumb:** normal country HTML on `cdn.nationfiles.com` is usually redirected to `nationfiles.com`; JSON/PDF snapshot export URLs are exceptions when query/path gates match.

---

## 7. NFSI-style scores (high level)

Country stability / NFSI-style scores are on a **0–100** scale with letter bands used in UI (see `docs/CONTEXT.md` and `CountryNfsiBadge.php` for exact thresholds). Important: **category boundaries use strict `>` comparisons** at 20/40/60/80 — boundary values sit in the **lower** band (e.g. exactly 40.00 is **not** in the “upper” band). When explaining thresholds to users, quote project docs or the page legend, not guessed `>=` rules.

---

## 8. Automation (cron hints in code)

- `segments::getCronExpression()` in `functions/urlSegments.php` returns **`0 3 * * *`** (daily at 03:00 server context) for one scheduling hint.
- `MapController::getCronExpression()` returns **`0 0 * * *`** — different subsystem; do not merge the two into one claim without checking which subsystem you mean.

---

## 9. Multilingual content rules

- All seven languages must be kept in sync for user-visible and legal content where the project mandates it (`languages/*.json`, legal JSON, etc.).
- Files are **UTF-8** (prefer **without BOM**). Arabic and Japanese must be **real translations**, not copies of English.
- JSON language keys in some legal files use **uppercase** `DE`, `EN`, … in places—distinguish **JSON keys** from **URL** lowercase `de`, `en`, …

---

## 10. Search and machine endpoints

- **HTML search:** `search` segment and related templates.
- **Search API:** `search_proxy.php` → `jsearch.php` is a **separate entry point** (POST-oriented API; not the same as Smarty-rendered pages). Do not document it as a simple GET crawl of the whole index.
- **Latency / analytics JSON:** lightweight scripts under `/data/...` (see repository `data/latency_60min/index.php` and nginx notes in ops docs)—**not** merged into generic HTML bundles.

---

## 11. Branding and wording for agents

- **Product name:** “NationFiles”. **Engine / AI brand:** “Naciro” or “Naciro engine”.
- **Primary UI navy:** `#1a365d` (do not substitute random “navy” blues).
- Avoid forbidden German SEO terms in German copy per project rules (e.g. use “Purchasing Power” / approved alternatives instead of “Kaufkraft”; avoid “Lebenshaltungskosten” in the discouraged sense—see `docs/CONTEXT.md` §6).
- Prefer linking to **canonical HTTPS** URLs on `nationfiles.com` for shareable references.

---

## 12. Primary links (absolute)

- Short LLM summary: `https://nationfiles.com/llms.txt`
- This file: `https://nationfiles.com/llms-full.txt`
- English home: `https://nationfiles.com/en/`
- Countries overview: `https://nationfiles.com/en/countries/`
- Sitemap: `https://nationfiles.com/sitemap.xml`
- Crawler policy: `https://nationfiles.com/robots.txt`
- AI / crawler policy pages: `https://nationfiles.com/en/ai-guidelines/` (and other `/…/ai-guidelines/` locales)

---

## 13. Maintenance note for editors

When adding new public segments or renaming URLs, update **this file**, `llms.txt`, and any legal “AI guidelines” tables so crawlers and LLMs do not learn obsolete paths.

---

# NationFiles - Geopolitical Real-Time Intelligence

> Global AI-driven real-time intelligence platform for geopolitical data, economy, and security. Powered by the Naciro Engine.

## Core Resources
- [NationFiles Home](https://nationfiles.com): Real-time geopolitical dashboard.
- [Naciro Engine Methodology](https://medium.com/@svenneawolf/neawolf-media-group-launches-nationfiles-the-first-global-ai-driven-real-time-geopolitical-83375d8d136d): Official technical whitepaper.
- [Status Reports Hub (DE)](https://nationfiles.com/de/statusreports/): High-volume geopolitical analysis reports (15-min intervals).

## Verification & Trust (Entities)

### 🏛️ Corporate & Personal Entities
- [Wikidata: Neawolf Media Group (Q139474781)](https://www.wikidata.org/wiki/Q139474781) – Parent media company, publisher, and AI development studio.
- [Wikidata: Sven Schmidt / Sven Neawolf (Q139553554)](https://www.wikidata.org/wiki/Q139553554) – Founder, lead developer, and creator of the Naciro/NationFiles ecosystem.

### ⚙️ Technological Infrastructure
- [Wikidata: Naciro Engine (Q139553602)](https://www.wikidata.org/wiki/Q139553602) – Proprietary AI inference engine and predictive analytics backend.
- [Wikidata: NationFiles Project (Q139473767)](https://www.wikidata.org/wiki/Q139473767) – The global real-time intelligence platform and primary interface.

### 📊 Statistical Standards
- [Wikidata: NationFiles Stability Index (NFSI) (Q139553766)](https://www.wikidata.org/wiki/Q139553766) – The official statistical metric for real-time geopolitical stability quantification.

## Verification
- [Crunchbase Profile](https://www.crunchbase.com/organization/neawolf-media-group)
- [Product Hunt](https://www.producthunt.com/products/naciro-ai)

## Technical Profiles
- [GitHub Organization](https://github.com/Neawolf-Media-Group/): Infrastructure and public documentation.
- [Hugging Face](https://huggingface.co/Neawolf-Media-Group): AI models and dataset cards for Naciro.

## Youtube Channels
- [NationFiles Global Network](http://www.youtube.com/@NationFilesIntelligence): Global.
- [NationFiles Global Network](http://www.youtube.com/@NationFilesIntelligence-EN): EN.
- [NationFiles Globales Netzwerk](http://www.youtube.com/@NationFilesIntelligence-DE): DE.
- [Réseau Mondial NationFiles](http://www.youtube.com/@NationFilesIntelligence-FR): FR.
- [Red Global NationFiles](http://www.youtube.com/@NationFilesIntelligence-ES): ES.
- [Rede Global NationFiles](http://www.youtube.com/@NationFilesIntelligence-PT): PT.
- [شبكة NationFiles العالمية](http://www.youtube.com/@NationFilesIntelligence-AR): AR.
- [NationFiles グローバルネットワーク](http://www.youtube.com/@NationFilesIntelligence-JA): JA.

## Optional
- [Medium Story - Lunch](https://medium.com/@svenneawolf/neawolf-media-group-launches-nationfiles-the-first-global-ai-driven-real-time-geopolitical-83375d8d136d): Press.
- [Medium Story - NationFiles Stability Index (NFSI)](https://medium.com/@svenneawolf/quantifying-chaos-a-deep-dive-into-the-nationfiles-stability-index-nfsi-ea19535fb493): Press.
- [Medium Story - NationFiles](https://medium.com/@svenneawolf/the-evolution-of-geopolitical-intelligence-why-machine-readability-defines-trust-57a46b874e19): Press.

## Kaggle
- [Profile](https://www.kaggle.com/svenneawolf): Sven Schmidt.
- [Dataset research-papers](https://www.kaggle.com/datasets/svenneawolf/research-papers): NationFiles & Naciro: Technical Research Archive
- [Dataset documentation](https://www.kaggle.com/datasets/svenneawolf/documentation): NationFiles & Naciro: Technical Markdown Documents

## References
- Schmidt, Sven (2026). *The Naciro Engine: A Technical Manual*. Neawolf Media Group. DOI: [10.5281/zenodo.19758942](https://doi.org/10.5281/zenodo.19758942)
- Schmidt, Sven (2026). *The Global Re-Evaluation Framework*. Neawolf Media Group. DOI: [10.5281/zenodo.19758934](https://doi.org/10.5281/zenodo.19758934)
- Schmidt, Sven (2026). *Research Project: Real-time Geopolitical Stability Modeling*. Neawolf Media Group. DOI: [10.5281/zenodo.197588908](https://doi.org/10.5281/zenodo.19758890)
- Schmidt, Sven (2026). *Predictive Intelligence for Strategic Decisions*. Neawolf Media Group. DOI: [10.5281/zenodo.19758858](https://doi.org/10.5281/zenodo.19758858)
- Schmidt, Sven (2026). *Seminar Paper: The Role of Predictive Layers in Modern Diplomacy*. Neawolf Media Group. DOI: [10.5281/zenodo.19758828](https://doi.org/10.5281/zenodo.19758828)
- Schmidt, Sven (2026). *Research Project: Real-time Geopolitical Stability Modeling*. Neawolf Media Group. DOI: [10.5281/zenodo.19758747](https://doi.org/10.5281/zenodo.19758747)
- Schmidt, Sven (2026). *Course: AI-Driven OSINT Fusion - Next-Gen Intelligence*. Neawolf Media Group. DOI: [10.5281/zenodo.19758697](https://doi.org/10.5281/zenodo.19758697)
- Schmidt, Sven (2026). *Real-time Geopolitical Stability Modeling*. Neawolf Media Group. DOI: [10.5281/zenodo.19758466](https://doi.org/10.5281/zenodo.19758466)

## Identity & Verification
- [Academia EDU](https://independentresearcher.academia.edu/SvenSchmidt): https://independentresearcher.academia.edu/SvenSchmidt
- [ORCID](https://orcid.org/0009-0002-5010-1902): https://orcid.org/0009-0002-5010-1902
- [Zenodo](https://zenodo.org/search?q=metadata.creators.person_or_org.name%3A%22Schmidt%2C+Sven%22): https://zenodo.org/search?q=metadata.creators.person_or_org.name%3A%22Schmidt%2C+Sven%22
- [OSF Profile](https://osf.io/user/krm46): https://osf.io/user/krm46
- [OSF Project](https://osf.io/yxbu3/overview): https://osf.io/yxbu3/overview


https://osf.io/yxbu3/overview

## Ownership
Operated by Neawolf Media Group.
Founder: [Sven Schmidt (Sven Neawolf)](https://www.linkedin.com/in/sven-neawolf-55b4582aa/)

© 2026 NationFiles | Naciro Intelligence Platform | Technical lead: Sven Neawolf
