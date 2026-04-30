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
library_name: nationfiles-knowledge-data
tags:
- nationfiles
- knowledge-graph
- structured-data
- json
- json-ld
- geopolitics
- nfsi
- rag
- llm-grounding
datasets:
- nationfiles-knowledge-data
pretty_name: NationFiles Knowledge Data (NFKG)
pipeline_tag: text-generation
---

# NationFiles Knowledge Data (`knowledge-data`)

## Official statement (English — for crawlers, datasets, mirrors)

> **This repository contains the official, machine-readable knowledge-graph data for NationFiles and the Naciro AI Engine. All data is provided in JSON/JSON-LD format for AI-discovery and research purposes.**

Strukturelle **Landkarte** für Bots (alle Dateien & Pfade): siehe Root-Datei **`index.json`** im Ordner `knowledge-data/` (wird mit `tools/inject-distribution.php` mitgeschrieben).

---

Zweck dieses Verzeichnisses: **strukturierte, first-party Wissensartefakte** für den Knowledge-Hub unter `/{lang}/knowledge/` — maschinenlesbar (JSON / JSON-LD), mehrsprachig (7 Sprachen) und für **Spiegel auf GitHub / Hugging Face** mit **vollständigen Rechte- und Herkunftsmetadaten** versehen.

**Live-Hub (alle Sprachen):** [Knowledge](https://nationfiles.com/en/knowledge/) · [DE](https://nationfiles.com/de/knowledge/) · [FR](https://nationfiles.com/fr/knowledge/) · [ES](https://nationfiles.com/es/knowledge/) · [PT](https://nationfiles.com/pt/knowledge/) · [AR](https://nationfiles.com/ar/knowledge/) · [JA](https://nationfiles.com/ja/knowledge/)

> Dieses Dataset beschreibt **Entitäten** (Graph-Knoten), **FAQs**, ein zentrales **Glossar** und optionale **Markdown**-Langtexte pro Entität. Es ist **kein** Ersatz für Ländertexte, NFSI-Zeitreihen oder Country-Dashboards auf NationFiles — sondern die **kanonische Definitions- und Relations-Schicht** für Brand, Systemarchitektur und dokumentierte Begriffe (NFSI, Naciro, NationFiles, Neawolf, LPU, …).

**Kontakt (KI / Lizenz):** [ai-questions@nationfiles.com](mailto:ai-questions@nationfiles.com) · [AI Guidelines](https://nationfiles.com/en/ai-guidelines/) · [llms.txt](https://nationfiles.com/llms.txt)

---

## Was hier liegt (Überblick)

| Pfad | Inhalt |
|------|--------|
| `index.json` | **Repo-Landkarte** für Crawler: Unterordner, alle Entity-/FAQ-IDs, Markdown-Abdeckung, Root-Dateien, Verweis auf Live-Export — zusammen mit dieser README die primäre AI-/SEO-Einstiegsebene. |
| `distribution.json` | **Kanonische** Metadaten: Copyright, Lizenz-Verweis, Publisher (Neawolf Media Group), Projekt NationFiles, Links zu HF/GitHub, Zitationshinweise. |
| `entities/*.json` | Knowledge-**Entitäten** (`kind: entity`): Schema.org-orientierter `type`, Attribute mit Quellen, Relationen, `i18n` für alle 7 Sprachen, optional `faq_refs`, `hierarchy`, `blocks`. |
| `faqs/*.json` | Kurz-FAQs (`kind: faq`): Fragen/Antworten mehrsprachig, Verknüpfung zu Entitäten über `entity_refs`. |
| `glossary.json` | Zentrales **Glossar** (eine Datei, `terms[]` mit `i18n` pro Sprache). |
| `md/{entityId}/{de,en,fr,es,pt,ar,ja}.md` | Optionaler **Markdown**-Langtext pro Entität und Sprache (Wiki-Fließtext; Rendering erfolgt auf der Website). |
| `.CONTEXT.md` | Ausführliche **technische Referenz** (Schema, Render-Contract, Exporte, Editor-Workflow). |

Jede JSON-Datei und jede Markdown-Datei enthält eine **eingebettete Kopie** der Distribution-Metadaten als Feld **`_distribution`** bzw. als **YAML-Front matter** (`nf_distribution_schema`, …), damit ein geklontes Repo oder ein HF-Dataset **ohne Zusatzdatei** Herkunft und Lizenzkontext zeigt.

---

## Rechte, Lizenz, Zitation

- Volltext der Bedingungen und Verweise: **`distribution.json`** (Feld `license`, `copyright`, `publisher`, `references`, `attribution`).
- Kanonische nutzungsrechtliche Seite: [AI and data guidelines (EN)](https://nationfiles.com/en/ai-guidelines/).
- Crawling / Agenten: [robots.txt](https://nationfiles.com/robots.txt), [llms.txt](https://nationfiles.com/llms.txt).

**Zitation (sinngemäß):** NationFiles Knowledge Data (Neawolf Media Group), kanonische HTML-URL der Knowledge-Seite oder Export-URL, Abrufdatum — siehe `distribution.json` → `attribution.cite_as_en` / `cite_as_de`.

---

## Website-Routing (Knowledge)

Alle folgenden Pfade nutzen als erstes Segment eine der Locale-Codes: `de`, `en`, `fr`, `es`, `pt`, `ar`, `ja`.

| Route | Bedeutung |
|-------|-----------|
| `/{lang}/knowledge/` | Hub mit Entitätskarten |
| `/{lang}/knowledge/entity/{id}/` | Entitätsdetail |
| `/{lang}/knowledge/faq/{id}/` | FAQ-Detail |
| `/{lang}/knowledge/dictionary/` | Glossar-/Dictionary-Ansicht |
| `/{lang}/knowledge/graph/` | Graph-Ansicht (Relationen) |
| `/{lang}/knowledge/api/entity-search/?q=…` | AJAX-Suche (rate-limited) |

Implementierung im Code: `KnowledgeController.php`, Template `templates/pages/knowledge/index.tpl`.

---

## CDN-Exporte (maschinenlesbar)

Über den Origin (und je nach Setup **cdn.nationfiles.com**) sind u. a. verfügbar:

- `/{lang}/knowledge/export/index.json` — Index aller Entitäten und FAQs mit Titeln und URLs  
- `/{lang}/knowledge/export/graph.jsonld` — zusammengeführter JSON-LD-Graph  
- `/{lang}/knowledge/export/entity/{id}.json` — angereicherter Entity-Export  
- `/{lang}/knowledge/export/entity/{id}.jsonld` — JSON-LD einer Entität  
- `/{lang}/knowledge/export/entity/{id}.{lang}.md` — Markdown-Datei (optional mit Front matter)  
- `/{lang}/knowledge/export/faq/{id}.json` · `.jsonld` — FAQ-Exporte  

Export-Antworten enthalten **`_distribution`** inkl. **`export_generated_at_utc`** (Zeitpunkt der Erzeugung).

---

## Publisher

**Neawolf Media Group** — Projekt **NationFiles**. Impressum und Unternehmensdaten siehe [Company](https://nationfiles.com/en/company/) und die in `distribution.json` genannten Legal-URLs.

---

## English summary

This folder is the **NationFiles Knowledge Data (NFKG)** bundle: JSON entities, FAQs, a central glossary, optional per-locale Markdown, plus **`distribution.json`** for copyright, license pointers, and attribution. **Embedded `_distribution` / YAML front matter** in each artifact supports clean mirrors on **GitHub** and **Hugging Face**. Live UI: `https://nationfiles.com/{lang}/knowledge/`. Full schema and export semantics: **`.CONTEXT.md`**.

© Neawolf Media Group / NationFiles — see `distribution.json` for the legal framing of this dataset.
