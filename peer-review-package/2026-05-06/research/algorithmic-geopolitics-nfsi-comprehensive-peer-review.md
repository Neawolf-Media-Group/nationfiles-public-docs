---
title: "Algorithmic Geopolitics: Comprehensive Methodology, Architecture, and Validation of the NationFiles Stability Index (NFSI)"
version: "1.6.5"
date: "2026-05-05"
figures_note: "Raster figures (PNG) for PDF/LaTeX; vector sources in peer-review/figures/*.svg."
language: "en"
document_type: "external_peer_review_manuscript"
publisher: "Neawolf Media Group"
affiliation_address: "Reinhardstr. 1b, 52078 Aachen, Germany"
license: "CC BY 4.0 where stated for cited artefacts"
---

# Algorithmic Geopolitics: Comprehensive Methodology, Architecture, and Validation of the NationFiles Stability Index (NFSI)

**Document control:** version 1.6.5 · **As of:** 2026-05-05 · **Publisher:** Neawolf Media Group (NationFiles).

**Suggested citation:** Neawolf Media Group (2026). *Algorithmic Geopolitics: Comprehensive Methodology, Architecture, and Validation of the NationFiles Stability Index (NFSI)*. Peer-review manuscript v1.6.5, 2026-05-05. Aachen, Germany.

**Repository paths:** Deposit/evidence locations are cited as **SM-xx** in the main text; full `research/deposit/...` and `research/evidence/...` paths appear **only** in Supplementary Material **Table 17** (merged in IEEE PDF builds after main matter).

---

## Abstract

This manuscript specifies a **deterministic, auditable** computation for the **NationFiles Stability Index (NFSI)**: a bounded composite score on $[1,100]$ produced by layers **L1-L4** (row normalisation; connector‑day aggregation and smoothing; country‑level composition with malus/bonus terms; inertia and a crash‑mode gate). **Determinism** means: given **pinned** upstream connector inputs, **fixed** release manifests (connector metadata, optional MI seeds where explicitly declared), and a **pinned implementation identifier** (see **Discussion → Implementation constancy**), recomputation yields the **same** NFSI outputs; the pipeline does **not** inject stochastic draws outside documented branches. It states **definitions, constants, pseudocode, validation hooks, reproducibility artefacts**, and **deposit-backed empirical excerpts** (persistence-baseline residuals on the toy fixture, named-event classification metrics, sensitivity magnitudes; aggregated under **Validation and Results**) for the NationFiles reference implementation.

**Keywords:** NationFiles; NFSI; Naciro; OSINT; validation; stability index; inertia; crash mode; reproducibility.

---

## List of figures

**Table 1 — Figure placement index.**

| ID | Placement | Content |
|:---|:----------|:--------|
| Fig. 1 | Introduction | Paradigm: archival snapshot vs operational situational loop |
| Fig. 2 | Related Work | Headline refresh axis (benchmark contrast) |
| Fig. 3 | Methodology | Data plane: L1-L4 to delivery; NationFiles (frontend) vs Naciro (backend engine) |
| Fig. 4 | Methodology | Layers L1-L4 and crash-mode gate |
| Fig. 5 | Validation and Results | Integrity fault tree (excerpt) |
| Fig. 6 | Validation and Results | Sensitivity fixture: mean absolute NFSI delta by perturbation family |

---

## List of tables

| Table | Placement | Short title |
|:-----|:-----------|:------------|
| 1 | List of figures | Figure placement index |
| 2 | Introduction | Normative class definitions |
| 3 | Methodology | NFSI connector groups and semantics |
| 4 | Methodology | Connector inventory excerpt (audit-facing) |
| 5 | Validation and Results | Sensitivity fixture summary statistics |
| 6 | Validation and Results | Forecast skill: persistence baseline (toy fixture) |
| 7 | Validation and Results | Named-event stress classification metrics |
| 8 | Validation and Results | Crash-mode rule conformance checks |
| 9 | Validation and Results | Comparator indices: cadence, coverage, correlation stance |
| 10 | Discussion | Abbreviated glossary of terms |
| 11 | Discussion | Errata and specification deltas |
| 12 | Appendix A | NFSI constants: Layer 1 |
| 13 | Appendix A | NFSI constants: Layer 2 |
| 14 | Appendix A | NFSI constants: Layer 3 |
| 15 | Appendix A | NFSI constants: Layer 4 |
| 16 | Appendix C | Manuscript vs shipped PHP (implementation parity) |
| 17 | Supplementary Material | Repository artefact index (SM-ID paths) |

---

## Introduction

NationFiles materialises country-level indicators on a fixed cadence through scheduled **connectors**. The **published NFSI** $S_c(t)$ for country $c$ at date $t$ is **operationally defined** by the L1-L4 pipeline--not estimated as a latent factor. On the scale feeding Layer 4, **higher NFSI corresponds to lower acute geopolitical stress**.

### Index mode (published NFSI vs Predictive Layer)

This manuscript normatively specifies the **daily NFSI composite** (Layers 1–4): a **descriptive, auditably deterministic** country-day score on $[1,100]$ derived from connector inputs and declared substitution rules. That score functions as the **baseline situational index** for NationFiles products and for any downstream analytics that **consume** NFSI as a **state variable** (level, short-run momentum, cross-country rank).

Where the product surface includes a **Predictive Layer**—**short-horizon outlooks** such as **24-hour** or **7-day** trajectories—those outputs are **not** produced by L1–L4 alone. They require a **separate forecasting stack** (models, ensembles, recalibration, backtests, and publication semantics) that may **condition on** recent NFSI, connector histories, and other covariates to emit forward-looking estimates with model-specific uncertainty. In that architecture, **current NFSI is an input / anchor signal** to the Predictive Layer, not the forecast itself.

**Scope boundary (explicit):** This manuscript defines **L1–L4 NFSI only**. The **Predictive Layer** (short-horizon outlooks such as 24h/7d paths built on top of NFSI) has its **own** mathematics, training regime, governance, and acceptance criteria—**outside** this document. Persistence baselines and RMSE scaffolds under **Validation** are **non-normative illustrations** unless superseded by a frozen evaluation annex; operational forecasts must cite a **separate** versioned specification when claimed.

### Scope and normative hierarchy

**Table 2 — Normative class definitions.**

| Class | Meaning |
|:------|:--------|
| **Normative** | SHALL/MUST requirements for specification compliance (Methodology; Validation; governance minima in Discussion). |
| **Informative** | Figures and non-binding interpretation. |
| **Implementation** | Equivalence to the pinned PHP reference (**Discussion**, Reproducibility subsection). |

**Independent verification:** formulas map to deposit recompute scripts, validation playbooks, and evidence manifests. **Normative manuscript behaviour** is **one** specification: security-group Layer‑2 MIN aggregation uses **missing-as-worst structural pads (value 0)** and conservative gap-synthesis endpoints aligned with that stance (**Methodology**). Parity checks against the **deployed PHP module** may follow different literals (`100.0` MIN pads, etc.); that mapping is **non-normative** for manuscript claims and is isolated in **Appendix C (Implementation note)**.

**Safety hooks:** Layer‑4 **crash mode** bypasses inertia when measured security minima fall below `LAYER4_SECURITY_CRISIS_THRESHOLD`. Operational gates **T1-T3** appear under Validation and Discussion.

**Other limits:** causal identification and uncertainty bands are **not** asserted unless backed by deposited evaluation manifests (see **Discussion and Limitations → Epistemic scope**).

![Fig. 1 -- Paradigm: static stock vs operational situational picture](research/peer-review/figures-png/fig01-paradigm-shift.png){#fig:01-paradigm}

*Fig. 1 -- Caption:* Continuous situational picture vs archival snapshot; headline outputs share one pipeline.

---

## Related Work

Peers include long-horizon academic/NGO indices, mandate-bound advisory, and market-linked risk feeds. “Benchmarking” here means **functional** comparison of refresh cadence and traceability--not a quality ranking.

![Fig. 2 -- Headline refresh axis (benchmark contrast)](research/peer-review/figures-png/fig06-benchmark-freshness.png){#fig:02-benchmark}

*Fig. 2 -- Caption:* Long-cycle indices versus high-frequency NFSI (schematic).

---

## Methodology

### Service boundaries (NationFiles vs. Naciro)

**NationFiles** is the **delivery and publication plane**: public web presence, operator-facing surfaces where applicable, stable HTTP/API endpoints, and structured **data exports** (scores, provenance flags, manifests) consumed by reviewers and downstream systems. It does **not** host the full heavy ingestion and scoring stack as a monolith on the same boundary as static or semi-static site delivery.

**Naciro** is a **separate backend programme**—the **processing and intelligence engine**—that runs connector pipelines, scheduled recomputation, orchestration of raw-to-score workflows, and any **model-assisted or NLP preprocessing** required before Layer‑1 normalisation. Deterministic NFSI layers (L1–L4) described in this manuscript are implemented and executed in that **backend processing** context; outputs are then exposed through NationFiles as **published artefacts**.

Readers should assume a **two-tier layout** (typically **two server roles or clusters**): **(i) frontend / delivery**—TLS termination, web UI, public API, caching, and export bundles; **(ii) backend / engine**—Naciro workers, databases holding connector and NFSI state, batch jobs, and internal orchestration. Trust boundaries, scaling, observability, and incident response **separate web delivery from compute**: NationFiles answers *what we publish*; Naciro answers *how scores are produced* from pinned inputs. Cross-references to “orchestration” in architecture figures denote **Naciro-side** coordination of ingestion and scoring, not NationFiles web rendering.

### System overview

Connectors write to relational stores; aggregates flow **L1 → L2 → L3 → L4** before API/web delivery. Fig. 3 shows **logical** data flow.

![Fig. 3 -- High-level data-flow](research/peer-review/figures-png/fig02-system-architecture.png){#fig:03-architecture}

*Fig. 3 -- Caption:* Ingestion through layered scoring to publication. **Service boundaries:** NationFiles delivers outputs to clients; **Naciro** (backend engine) performs ingestion, orchestration, and L1–L4 computation on processing infrastructure—see **Service boundaries (NationFiles vs. Naciro)** above.

### Normative specification

This manuscript adopts **one** binding NFSI rule-set:

* **Security group ($g=100$):** multiset **MIN** over connector-day scores; absent structural slots in the MIN construction are padded with **0** (missing-as-worst), **to strictly prevent masking security crises when connectors go missing under benign outages or adversarial withholding.**  
* **Non-security groups:** anchored average recipe with midpoint pads **50** to uniform length $M$ (see Layer 2 below).  
* **Exports:** operational systems may record padding/imputation provenance with boolean flags (`was_padded`, `was_imputed`, …). **No** parallel “profile × variant” grid is normative here--alternate numeric mappings exist only as **implementation parity** notes (**Appendix C**).

### Population consensus (inputs to Layer 3)

Population `country_meta.pop` is a weighted consensus across public sources (Eurostat, UN WPP, IMF, World Bank, RDF, CIA, REST Countries, API-Ninjas) with weights **1.5 / 1.5 / 1.2 / 1.2 / 0.9 / 0.8 / 0.8 / 0.5**. Extrapolation fills gaps; `allowedGrowth = MAX(0.015, masterRate + 0.005)` caps implausible positive annual drift from 2024 onward. Details: **Appendix A**; deposit mirror **SM-15** (Supplementary Material index).

### Algebraic definition

**Layer 1 -- Row normalisation.** Let $\mathrm{MIN}_{\mathrm{raw}}$, $\mathrm{MAX}_{\mathrm{raw}}$ be the extrema of raw values for connector $k$ on the audit slice; $\mathrm{SPAN}=\mathrm{MAX}_{\mathrm{raw}}-\mathrm{MIN}_{\mathrm{raw}}$. For each row,

$$\mathrm{normalized}=
\begin{cases}
50, & \mathrm{SPAN}\le 0 \text{ or missing bounds} \\
100\cdot\frac{r-\mathrm{MIN}_{\mathrm{raw}}}{\mathrm{SPAN}}, & \text{otherwise}
\end{cases}$$

Let $\mathrm{norm}_{01}=\mathrm{clamp}(\mathrm{normalized},0,100)$. With direction flag “higher raw is worse”, $\mathrm{score}_{\mathrm{row}}=\mathrm{round}\!\left(\mathrm{clamp}(100-\mathrm{norm}_{01},0,100),2\right)$; otherwise swap $100-\cdot$.  

**Layer 2 -- Connector-day.** Let $M$ be the maximum row-count across ISO2 for fixed $(\mathrm{connector\_id},\mathrm{date})$. For security ($g=100$), let observed scores form multiset $\mathcal{S}$; pad to length $M$ with literal **0** as needed; $\mathrm{dayScore}=\min(\mathcal{S}\cup\{\text{padded zeros}\})$. For non-security groups, build $\mathbf{v}=(0,s_1,\ldots,s_k,\underbrace{50,\ldots,50}_{M-k},100)$ and $\mathrm{dayScore}=\mathrm{mean}(\mathbf{v})$. Smooth:

$$\mathrm{score}^{L2}_{t}=0.6\cdot \mathrm{dayScore}+0.4\cdot \mathrm{yesterday}$$

(with documented defaults **70** / **85** when no yesterday).  

#### Gap synthesis

Let $A_D$ denote the deposited availability statistic for calendar day $D$ on the audited slice (row-count or coverage tally per frozen recompute manifest). Define the day-on-day growth ratio

$$\rho_D =
\begin{cases}
A_D / A_{D-1}, & \text{if } A_{D-1} \text{ exists and } A_{D-1} > 0 \\
1, & \text{otherwise}
\end{cases}$$

**Layer 3 -- Composite.** With $\mathrm{effW}_n=g_n\cdot(w_n/100)\cdot u_n$, dummy anchors at $0$ and $100$, weighted mean $\mathrm{baseScore}$; apply conflict malus using $\mathrm{minSec}$ from Layer 3 inputs; fragility / small-pop / bonus; governance pull via $\mathrm{est\_total}$ → $\mathrm{nfsi\_raw}$ (terms exactly as in **Appendix A** constants).

**Layer 4 -- Security minimum, crash mode, inertia.** Let $\mathcal{N}$ denote the connector universe for the release. For each country-day $(c,t)$, define the set of **real, non-placeholder** group-100 connector-day scores as

$$\mathcal{S}_{c,t} = \{ s_{n,c,t} \in [0,100] \mid n \in \mathcal{N},\, g_n = 100,\, \mathrm{isReal}(n,c,t)=1,\, \mathrm{isPlaceholder}(n,c,t)=0 \}.$$

Define

$$\mathrm{minSec}(c,t)=
\begin{cases}
\displaystyle\min_{s\in\mathcal{S}_{c,t}} s, & |\mathcal{S}_{c,t}|>0\\
\texttt{LAYER3\_NO\_SECURITY\_DEFAULT\_MINSEC}, & |\mathcal{S}_{c,t}|=0
\end{cases}$$

**Crash mode** (normative predicate):

$$\mathrm{crash\_mode}(c,t)=\mathbf{1}\left\{\mathrm{minSec}(c,t)<\texttt{LAYER4\_SECURITY\_CRISIS\_THRESHOLD}\right\}.$$

**Inertia weight** (normative, binary):

$$w_{\mathrm{inertia}} =
\begin{cases}
0.50, & N_{\mathrm{no\_data}} > 0 \\
0.80, & N_{\mathrm{no\_data}} = 0
\end{cases}$$

If $\mathrm{crash\_mode}=1$, published NFSI equals Layer‑3 output (after floor/bounds). Otherwise let $w=w_{\mathrm{inertia}}$ and apply inertia and daily cap $\pm\texttt{LAYER4\_DAILY\_CHANGE\_CAP}$, round to two decimals.

### Normative SQL -- $\mathrm{minSec}$ and crash predicate

The following is **normative** for audits that materialise scores in `nfsi_connector` joined to `datasource_meta`. Bind `:default_minsec` to **`LAYER3_NO_SECURITY_DEFAULT_MINSEC`** (**Appendix A**).

```text
WITH sec AS (
  SELECT nc.score AS score_final
  FROM nfsi_connector nc
  INNER JOIN datasource_meta dm ON dm.connector_id = nc.connector_id
  WHERE nc.iso2 = :iso2
    AND nc.date = :d
    AND dm.nfsi_group = 100
    AND nc.was_placeholder = 0
)
SELECT CASE WHEN COUNT(*) = 0 THEN :default_minsec ELSE MIN(score_final) END AS minSec
FROM sec;
```

### Pseudocode listings

All listings use a single fence style (` ```text `) for KaTeX HTML PDF and Pandoc/LaTeX pipelines; treat them as **lstlisting-style** monospace blocks in journal templates (**no** `$...$` / TeX escapes inside fences; relational operators are literal text under PDF `listings`).

**Listing -- Layer 1**

```text
SPAN = MAX_RAW - MIN_RAW
FOR each row:
  IF SPAN <= 0 OR bounds missing:
      score_row = 50
  ELSE:
      normalized = 100 * (raw - MIN_RAW) / SPAN
      normalized = CLAMP(normalized, 0, 100)
      IF higher_raw_is_worse: score_row = 100 - normalized ELSE score_row = normalized
  score_row = ROUND(CLAMP(score_row, 0, 100), 2)
```

**Listing -- Layer 2 (normative security MIN + pad 0)**

```text
M = MAX row-count k over iso2 for this (connector_id, date)
IF group == 100:
    scores_ext = observed_scores ++ REPEAT(0, M - k_observed)
    dayScore = MIN(scores_ext)
ELSE:
    dayScore = AVG( [0] ++ observed_scores ++ PAD(50, M-k) ++ [100] )
score_final = ROUND(CLAMP(0.6*dayScore + 0.4*yesterday, 0, 100), 2)
```

**Listing -- Layer 4 inertia / crash**

```text
minSec = MIN(real group-100 scores) OR DEFAULT_MINSEC
IF minSec < LAYER4_SECURITY_CRISIS_THRESHOLD:
    NFSI_Today = nfsi_raw
ELSE IF prevScore exists:
    inertiaWeight = IF noDataCount > 0 THEN 0.50 ELSE 0.80
    smoothed = prevScore * inertiaWeight + nfsi_raw * (1 - inertiaWeight)
    APPLY daily cap +/- LAYER4_DAILY_CHANGE_CAP vs prevScore
    NFSI_Today = ROUND(smoothed, 2)
```

Full line-by-line artefact: **Appendix B** and **SM-28** (deposit may mention legacy pad literals--**Appendix C** reconciles shipped PHP).

![Fig. 4 -- NFSI layered pipeline + crash-mode gate](research/peer-review/figures-png/fig03-nfsi-layers.png){#fig:04-nfsi-layers}

*Fig. 4 -- Caption:* L1-L4 with crash branch on measured security minima.

### Constants in formulas

Numeric **pins** for every configuration key appear **once**, in **Appendix A** (Tables **12–15**). This subsection relates **symbol names** to their **roles in the equations above**, without repeating scalar values.

**Layer 2.** Connector-day smoothing uses $\mathrm{score}^{L2}_{t}=\alpha\cdot \mathrm{dayScore}+\beta\cdot \mathrm{yesterday}$ with $\alpha+\beta=1$, implemented as $\alpha=\texttt{LAYER2\_TODAY\_WEIGHT}$, $\beta=\texttt{LAYER2\_YESTERDAY\_WEIGHT}$. Cold-start defaults for $\mathrm{yesterday}$, recovery increments and horizons, anchored-average multiset endpoints (dummy lows/highs and midpoint pads), and operational coverage warnings are keyed to the remaining Layer‑2 constants listed under **Appendix A.2**.

**Layer 3.** Neutral substitution for missing connectors uses $\texttt{LAYER3\_NO\_DATA\_NEUTRAL\_SCORE}$; dummy anchors at $0$ and $100$ carry weights $\texttt{LAYER3\_DUMMY\_LOW\_WEIGHT}$, $\texttt{LAYER3\_DUMMY\_HIGH\_WEIGHT}$. Conflict malus compares $\mathrm{minSec}$ to $\texttt{LAYER3\_CONFLICT\_THRESHOLD}$ with slope $\texttt{LAYER3\_CONFLICT\_MALUS\_FACTOR}$ capped by $\texttt{LAYER3\_CONFLICT\_MALUS\_CAP}$. Fragility, small-population, population-bonus, and governance-pull terms use the population and WGI-related pins in **Appendix A.3**. When $|\mathcal{S}_{c,t}|=0$, $\mathrm{minSec}$ falls back to $\texttt{LAYER3\_NO\_SECURITY\_DEFAULT\_MINSEC}$ as in the piecewise definition above—numerical values **only** in the appendix tables.

**Layer 4.** The **Crash mode** and **Inertia weight** displays above compare $\mathrm{minSec}(c,t)$ and $N_{\mathrm{no\_data}}$ to the Layer‑4 keys $\texttt{LAYER4\_SECURITY\_CRISIS\_THRESHOLD}$, $\texttt{LAYER4\_INERTIA\_SCORE\_WITHOUT\_L1L2}$, $\texttt{LAYER4\_INERTIA\_STANDARD}$, and bound daily moves with $\texttt{LAYER4\_DAILY\_CHANGE\_CAP}$; scalar pins are listed **once** in **Appendix A.4** (Table **15**).

### Connector roles and provenance

Groups **100** (security MIN), **85**, **60**, **50**, **40**, **&lt;0** excluded define thematic roles. Full per-connector table: **SM-23** (Supplementary Material index; not duplicated here).

---

## Validation and Results

### Integrity testing and event-style evidence

Scenario identifiers (internal QA anchors) serve as **test questions** (e.g., missing-media semantics, territorial fallbacks, crash-mode behaviour). They are not presented as historical ground-truth adjudication.

![Fig. 5 -- Fault tree sketch](research/peer-review/figures-png/fig05-stress-fault-tree.png){#fig:05-fault-tree}

*Fig. 5 -- Caption:* Each branch requires explicit policy: substitution semantics, structured status exports, or documented mitigations -- not silent “all clear.”

**Empirical validation note (minimum evidence added):** A data-driven event-style backtest is provided from an internal country-day score table (available from a documented cutover date in the product implementation). The backtest selects the top-$k$ largest absolute daily deltas (unique countries) and reports windowed effect summaries (pre-mean, pre-sd, post-min/post-max, and a z-style deviation vs. the pre window). This does **not** adjudicate historical “crises” as ground truth; it provides a reproducible quantitative sanity check and a template for crisis-labelled studies.

Deposit artefacts for this block: **SM-31**, **SM-09**, **SM-08**, **SM-35**, **SM-07**, **SM-06** (paths indexed as **SM-xx** in Supplementary Material).

**Backtest (top-$k$ absolute daily deltas, unique ISO2; window=7d).** Row-level metrics are deposited as **SM-08** with narrative **SM-09**; full numeric rows are **not** inlined here (IEEE/PDF-friendly layout).

**Illustrative summary (backtest excerpt; full rows in SM-08).**

| Statistic | Value | Note |
| :-- | :-- | :-- |
| Largest $|z|$ (studentised vs pre-window) | **8.14** | NU, 2026-03-31 (**SM-08**) |
| Illustrative two-sided normal tail at $|z|=8.14$ | $\approx 4.4\times 10^{-16}$ | **Non-inferential**: residuals are **not** assumed i.i.d.; use block bootstrap / permutation tests (**Validation**). |

**Named events (external event-source annotations; post-window=7d).** Raw rows and scoring outputs are **SM-06**, **SM-44**, **SM-45**, **SM-43**; aggregates appear below as **Table 7**.

### Ethics and operational thresholds

This index can be misused or misinterpreted. Potential harms include: (i) misclassification leading to inappropriate travel or operational decisions, (ii) reputational impacts on countries or populations, (iii) market or policy misuse, and (iv) feedback loops when users treat an index as ground truth.

Minimum mitigations for deployment and publication:
* **Separation of concerns:** distinguish descriptive stability scoring from forecasts and from any policy advice.
* **Human review thresholds (minimum, audit-ready):**
  - **T1 (large movement):** if $|\Delta \text{NFSI}_{c,t}| > 6$ within 24h, require human review before external dissemination.
  - **T2 (crash mode):** if crash mode triggers ($\min(S)<25$ for group-100 scores excluding placeholders), require 2-person review within 4h.
  - **T3 (data integrity):** if no-data ratio $\ge 0.5$, any group-100 connector ingestion anomaly occurs, or `security_missingness_review=required` (padding policy / Methodology), require review within 8h.
* **Transparency:** document score scope, provenance links, and a dispute channel in published materials.
* **Red-team / misuse testing:** test for adversarial data gaps, systematic bias from missingness, and interpretation hazards.

**Escalation workflow / SLA (minimum):** A deposit-ready SOP is provided as **SM-20** (roles A1/A2/E1/M1, evidence requirements, and a public dispute/correction pathway).

---

### Reproducibility and audit

This paper supports independent verification (traceability to code, constants, and disclosed provenance). Third-party certification or endorsement, if any, must be cited from separate publications.

#### Machine-executable recompute recipe

The following is a **minimal recompute recipe** sufficient to reproduce Layers 1-4 on a deposited dataset snapshot.

**Pinned implementation:** a stable implementation identifier (commit/tag/build provenance) recorded in **SM-49/SM-50** and referenced under **Discussion → Implementation constancy** (single canonical pin for this manuscript; redacted in the public package to avoid internal targeting data).

**Environment (minimum):**
* PHP (>= 8.1)
* a SQL database containing the deposited snapshot tables

**Input tables (snapshot):**
* `connectors_raw` -- raw rows for all connectors (columns at minimum):
  - `connector_id` (string)
  - `iso2` (string)
  - `date` (YYYY-MM-DD)
  - `raw_value` (float) or connector-specific numeric raw
  - optional: `severity` (float) if the connector defines a severity transform before normalisation
* `connector_meta` -- connector metadata:
  - `connector_id`
  - `group_weight` (e.g. 100/85/60/50/40; negative = excluded)
  - `weight` (0-100)
  - `higher_raw_is_worse` (bool)
  - `update_frequency` (categorical or numeric multiplier)
* `country_meta` -- country-level metadata used by malus/bonus:
  - `iso2`
  - `population`
  - $\mathrm{est\_total}$ (0-100 governance)
* `nfsi_prev` -- previous-day NFSI values (for Layer 4), or empty for cold start.

**Layer 1 (row score)**
* For each `connector_id`, compute `min_raw`, `max_raw` over the snapshot.
* For each row:
  - if $\mathrm{SPAN}\le 0$ or missing min/max $\Rightarrow$ $\mathrm{score}_{\mathrm{row}} := 50$
  - else $\mathrm{normalized}:=100\cdot(\mathrm{raw}-\mathrm{MIN}_{\mathrm{raw}})/\mathrm{SPAN}$
  - if $\mathrm{higher\_raw\_is\_worse}=1$ $\Rightarrow$ $\mathrm{score}_{\mathrm{row}} := 100-\mathrm{normalized}$, else $\mathrm{score}_{\mathrm{row}} := \mathrm{normalized}$
  - clamp to [0,100], round to 2 decimals.

**Layer 2 (connector-day score)**
* Collect all $\mathrm{score}_{\mathrm{row}}$ for a given $(\mathrm{connector\_id},\mathrm{iso2},\mathrm{date})$.
* If $g=100$: $\mathrm{dayScore}=\min(\mathcal{S})$ after padding absent multiset slots with **0** (manuscript-normative missing-as-worst).
* Else: compute $\mathrm{dayScore}$ by the anchored-average construction with midpoint pads **50** (see **Layer 2** above).
* Smooth with previous day: $\mathrm{score}_{\mathrm{final}}=0.6\,\mathrm{dayScore}+0.4\,\mathrm{yesterday}$, where $\mathrm{yesterday}$ defaults to 70 (security) or 85 (others) when absent.

**Layer 3 (country score)**
* For each $(\mathrm{iso2},\mathrm{date})$ aggregate all connector-day $\mathrm{score}_{\mathrm{final}}$ into a weighted mean using:
  - $\mathrm{effW}_n=g_n\cdot(w_n/100)\cdot u_n$
  - include dummies 0 and 100 with weight 1 each
* Apply malus/bonus sequence exactly as in Layer 3 / Appendix B.

**Layer 4 (inertia and crash mode)**
* Compute crash mode as specified for Layer 4: min of real, non-placeholder group-100 scores < 25 → bypass smoothing.
* Else apply inertia and daily-change cap ±3.

#### Deposit packaging and evidence pointers

The full line-by-line pseudo-code is in **Appendix B** and **SM-28**. For audits, the evidence deposit should additionally include a small fixture and a log bundle of intermediate values per country/day (see **Discussion → Reproducibility** and **SM-48**).

**Deposit packaging (checklist):**
* Deposit manuscript, markdown sources, and figures (SVG + PNG).
* Pin versions: manuscript `version` and the implementation pin documented under **Discussion → Implementation constancy** (commit + module path + internal version label).
* Include licence statements per artefact.

**Evidence pack (generated in this repository):**
* **SM-49** and **SM-50**
* Generator: **SM-52**

**Forecast evaluation (optional):**
* RMSE/backtest scaffold: **SM-51**
* No RMSE values are asserted here without a separately published evaluation report.


### Data provenance and connectors

Per-connector **numeric Layer‑1 basis fields**, weights, and implementation-class pointers: see **connector inventory excerpt** below and **SM-23** for the full machine-readable table.

### Connector groups and semantics

The **Top‑10-by-influence** excerpt (computed licence/snapshot fingerprints) is available in the supplementary deposit paths **SM-27** and **SM-26** (**SHA‑256** in the Markdown header). These rows are mechanically derived from **SM-23**; `deposit_provenance_row_sha256` fingerprints the deposited provenance tuple for **audit diffing** when upstream raw payloads are absent from git. Authoritative provider wording remains upstream; redistribution of derivatives must comply with provider terms.

**Provenance/licence appendix (deposit):** machine-readable provenance is provided as **SM-23** (includes `connector_id, source_url, data_version, last_snapshot_date, weight, group, higher_raw_is_worse, update_mult`) and an additional licence-focused appendix as **SM-24**.

Connector groups define **semantic roles** in the composite and constrain aggregation behaviour (e.g. security MIN rule). Unless otherwise stated, $g_n$ denotes the group weight of connector $n$.

**Table 3 — NFSI connector groups and semantics.**

| Group (g) | Name (one line) | Semantics / required handling |
| :-- | :-- | :-- |
| 100 | Security / acute safety | Crisis-relevant telemetry. Layer‑2 uses **MIN** (one critical signal dominates). Included in $\mathrm{minSec}$ and crash/conflict predicates, but **only** for real, non-placeholder observations. |
| 85 | Governance & rule-of-law | Structural institutional indicators; aggregated with non-security AVG recipe; contributes to Layer‑3 weighted mean and governance-related adjustments (WGI pull operates separately on $\mathrm{est\_total}$). |
| 60 | Economic / macro-financial | Non-security group; averaged with anchors/pads; contributes via weights and update multipliers. |
| 50 | Infrastructure / societal | Non-security group; averaged with anchors/pads; contributes via weights and update multipliers. |
| 40 | Demographic / slow structural | Non-security group; averaged with anchors/pads; generally lower cadence; contributes via weights and update multipliers. |
| < 0 | Excluded | Connector is excluded from NFSI composition (implementation filter). Must remain present in provenance but is not a contributor. |

This table defines **group semantics**; per-connector group assignments are enumerated in deposited provenance CSVs.

#### Linguistic Processing and Locale Standardization

Some connectors materialise multilingual fields or parallel translation columns (e.g. **`translate_de`**, **`translate_en`**, **`translate_fr`**, **`translate_es`**, **`translate_pt`**, **`translate_ar`**, **`translate_ja`** on feeds ingested via **NaciroTelegram**). **Japanese** text uses **ISO 639-1 `ja`** (`translate_ja`); do **not** label language fields as **`JP`** (that is **ISO 3166-1** country code). Layer‑4 NFSI arithmetic does **not** branch on language; locale metadata supports **provenance, QA, and connector-specific audits** only.

#### Connector inventory excerpt

**Audit-facing inventory (normative for manuscript self-containment):** this table maps each `connector_id` to its effective NFSI group, the primary Layer‑1 basis field used for audits, the connector weight, and the implementation class. Full legal language, upstream URLs, and snapshot dating remain in **SM-23**. Maintainer-only regeneration of this embedded fragment is documented under **SM-02** (Peer-review manuscript PDF / connector inventory).

**Table 4 — Connector inventory excerpt (audit-facing).**

```{=latex}
\begingroup
\setlength{\tabcolsep}{3pt}
\renewcommand{\arraystretch}{1.05}
\footnotesize
\begin{longtable}{@{}p{0.22\linewidth}c p{0.26\linewidth}c p{0.18\linewidth}p{0.22\linewidth}@{}}
\textbf{Connector ID} & \textbf{Grp} & \textbf{Layer-1 basis} & \textbf{Wgt} & \textbf{PHP class} & \textbf{Licence digest} \\
\hline
\endhead
AcledMonthAll & 10 & ACLED-style event fields (\texttt{fatalities} + event-type weights $\rightarrow$ per-row NF score) & 90 & \ttfamily\seqsplit{AcledMonthAllConnector} & ACLED / filtered, aggregated for Naciro Intelligence; mult=1.0; live \\
AcledMonthAllOverview & 10 & ACLED-style event fields (\texttt{fatalities} + event-type weights $\rightarrow$ per-row NF score) & 95 & \ttfamily\seqsplit{AcledMonthAllOverviewConnector} & ACLED / filtered, aggregated for Naciro Intelligence; mult=1.0; live \\
CountriesConflictUcdpGed & 100 & high & 85 & \ttfamily\seqsplit{CountriesConflictUcdpGedConnector} & Free for academic, commercial, governmental use (see UCDP); mult=3.65; live \\
CountriesCurrencyFromFa & 60 & EUR & 65 & \ttfamily\seqsplit{CurrencyDailyFaConnector} & Internal; mult=3.65; live \\
CountriesGdeltGlobRadar & 100 & GoldsteinScale & 85 & \ttfamily\seqsplit{CountriesGdeltGlobRadarConnector} & GDELT Terms of Use (see provider); mult=7.30; live \\
CountriesNetAbuseIpDb & 85 & count & 70 & \ttfamily\seqsplit{CountriesNetAbuseIpDbConnector} & AbuseIPDB Terms of Use; mult=3.65; live \\
CountriesNetGrpTrfcAnom & 50 & value & 55 & \ttfamily\seqsplit{CountriesNetGrpTrfcAnomConnector} & Cloudflare Terms of Use; mult=7.30; live \\
CountriesNetSpmBot & 85 & total\_today & 70 & \ttfamily\seqsplit{CountriesNetSpmBotConnector} & Spamhaus Terms \& Conditions; mult=3.65; live \\
CountriesNetTrfcAnom & 50 & value & 70 & \ttfamily\seqsplit{CountriesNetTrfcAnomConnector} & Cloudflare Terms of Use; mult=7.30; live \\
CountriesPopPrisn100K & 85 & Prison\_population\_rate & 50 & \ttfamily\seqsplit{CountriesPopPrisn100KConnector} & CC BY / Our World in Data; mult=1.0; live \\
CountriesVatRatesApiVer & 60 & rate\_standard & 60 & \ttfamily\seqsplit{CountriesVatRatesApiVerConnector} & APIVerve Terms of Service; mult=1.12; live \\
CountriesVatRatesVatLup & 60 & rate\_standard & 60 & \ttfamily\seqsplit{CountriesVatRatesVatLupConnector} & Refer to source (EC data); mult=1.0; live \\
CountriesWb65Up & 40 & \texttt{value} field for WB indicator \texttt{SP.POP.65UP.TO.ZS} & 10 & \ttfamily\seqsplit{CountriesWb65UpConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbAccsZs & 40 & \texttt{value} field for WB indicator \texttt{EG.ELC.ACCS.ZS} & 60 & \ttfamily\seqsplit{CountriesWbAccsZsConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbCcEst & 100 & \texttt{value} field for WB indicator \texttt{CC.EST} & 85 & \ttfamily\seqsplit{CountriesWbCcEstConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbCdrtIn & 40 & \texttt{value} field for WB indicator \texttt{SP.DYN.CDRT.IN} & 30 & \ttfamily\seqsplit{CountriesWbCdrtInConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbCpiTotlZg & 60 & connector-specific (see connector class) (\texttt{WB:FP.CPI.TOTL.ZG}) & 75 & \ttfamily\seqsplit{CountriesWbCpiTotlZgConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbFpCpiTotl & 60 & connector-specific (see connector class) (\texttt{WB:FP.CPI.TOTL.ZG}) & 20 & \ttfamily\seqsplit{CountriesWbFpCpiTotlConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbLe00 & 40 & \texttt{value} field for WB indicator \texttt{SP.DYN.LE00.IN} & 45 & \ttfamily\seqsplit{CountriesWbLe00Connector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbMilTotl & 40 & \texttt{value} field for WB indicator \texttt{MS.MIL.TOTL.P1} & 40 & \ttfamily\seqsplit{CountriesWbMilTotlConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbNetUser & 40 & \texttt{value} field for WB indicator \texttt{IT.NET.USER.ZS} & 40 & \ttfamily\seqsplit{CountriesWbNetUserConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbPopDpnd & 40 & \texttt{value} field for WB indicator \texttt{SP.POP.DPND} & 20 & \ttfamily\seqsplit{CountriesWbPopDpndConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbPopGrow & 40 & \texttt{value} field for WB indicator \texttt{SP.POP.GROW} & 25 & \ttfamily\seqsplit{CountriesWbPopGrowConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbPovGini & 60 & \texttt{value} field for WB indicator \texttt{SI.POV.GINI} & 60 & \ttfamily\seqsplit{CountriesWbPovGiniConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbPsrcP5 & 100 & \texttt{value} field for WB indicator \texttt{VC.IHR.PSRC.P5} & 100 & \ttfamily\seqsplit{CountriesWbPsrcP5Connector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbResTotlCd & 40 & \texttt{value} field for WB indicator \texttt{FI.RES.TOTL.CD} & 25 & \ttfamily\seqsplit{CountriesWbResTotlCdConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbRlEst & 100 & \texttt{value} field for WB indicator \texttt{RL.EST} & 80 & \ttfamily\seqsplit{CountriesWbRlEstConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbUem1524 & 60 & \texttt{value} field for WB indicator \texttt{SL.UEM.1524.ZS} & 70 & \ttfamily\seqsplit{CountriesWbUem1524Connector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbUemTotl & 60 & \texttt{value} field for WB indicator \texttt{SL.UEM.TOTL.ZS} & 55 & \ttfamily\seqsplit{CountriesWbUemTotlConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbVaEst & 100 & \texttt{value} field for WB indicator \texttt{VA.EST} & 50 & \ttfamily\seqsplit{CountriesWbVaEstConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountriesWbWgiPvCcRl & 100 & est\_total (\texttt{WB:PV.EST}) & 85 & \ttfamily\seqsplit{CountriesWbWgiPvCcRlConnector} & CC BY 4.0 / filtered, aggregated; mult=1.0; live \\
CountryNewsRiskLevel & 100 & risk\_level & 100 & \ttfamily\seqsplit{CountryNewsRiskLevelConnector} & CC BY-ND 4.0; mult=3.65; live \\
DisasterGdacsHumNat & 100 & value & 100 & \ttfamily\seqsplit{DisasterGdacsHumNatConnector} & GDACS Terms of Use; mult=7.30; live \\
EconFredHerCountScOvSc & 60 & overall\_score & 60 & \ttfamily\seqsplit{EconFredHerCountScOvScConnector} & Refer to Source Link; mult=1.0; live \\
EconFredHerCountScRanks & 60 & total & 60 & \ttfamily\seqsplit{EconFredHerCountScRanksConnector} & Refer to Source Link; mult=1.0; live \\
EconFredHerCountScRights & 100 & property\_rights & 100 & \ttfamily\seqsplit{EconFredHerCountScRightsConnector} & Refer to Source Link; mult=1.0; live \\
EconFredHerCountScTax & 60 & total & 60 & \ttfamily\seqsplit{EconFredHerCountScTaxConnector} & Refer to Source Link; mult=1.0; live \\
EurostatStatArblsQ & 60 & value & 60 & \ttfamily\seqsplit{EurostatStatArblsQConnector} & Refer to Source Link; mult=1.12; live \\
EurostatStatTps & 40 & value & 40 & \ttfamily\seqsplit{EurostatStatTpsConnector} & Refer to Source Link; mult=1.0; live \\
GovFbiMw & 100 & OpenSanctions NDJSON entities (aggregate COUNT per iso2/date) & 80 & \ttfamily\seqsplit{GovFbiMwConnector} & OpenSanctions / FBI data - Public Domain (DOJ); mult=1.52; live \\
GovIntPol & 100 & INTERPOL Red Notices: count per capita per iso2/day (aggregated iso2/date; direction negative) & 85 & \ttfamily\seqsplit{GovIntPolConnector} & INTERPOL Terms of Use; mult=1.52; live \\
NewsMediaStck & 70 & sentiment & 50 & \ttfamily\seqsplit{NewsMediaStckConnector} & Refer to Service Agreement; mult=3.65; live \\
NfMilitaryMassViolence & 100 & mlmv\_actor\_day\_stress & 100 & \ttfamily\seqsplit{NfMilitaryMassViolenceNfsiConnector} & Internal processing for NationFiles Intelligence; mult=7.30; live \\
NfNewsCountry24 & 70 & risk\_level & 70 & \ttfamily\seqsplit{NfNewsCountry24Connector} & CC BY-ND 4.0; mult=3.65; live \\
NfRawNewsSignalsEventRisk & 100 & event\_risk\_level & 100 & \ttfamily\seqsplit{NfRawNewsSignalsEventRiskConnector} & CC BY-ND 4.0; mult=3.65; live \\
OecdSdmxGdpvAnnpct & 60 & value & 60 & \ttfamily\seqsplit{OecdSdmxGdpvAnnpctConnector} & OECD Terms \& Conditions; mult=1.0; live \\
OWIDDemocracyIndex & 70 & value & 70 & \ttfamily\seqsplit{OWIDDemocracyIndexConnector} & CC BY / Our World in Data; mult=1.0; live \\
SdgGlobalIndexSdsn & 60 & overall\_score & 60 & \ttfamily\seqsplit{SdgGlobalIndexSdsnConnector} & Refer to Source Link; mult=1.0; live \\
TravelAdvisoryUnified & 100 & warning-source count in unified row (\texttt{pub\_*} columns non-null): 1 source $\rightarrow$ raw 75; two or more sources $\rightarrow$ raw 100 (0-100; negative direction) & 75 & \ttfamily\seqsplit{TravelAdvisoryUnifiedConnector} & Refer to Source Link; mult=3.65; live \\
TravelStateGov & 100 & DoS Level-4 advisories only: row score via \texttt{getStabilityRowScore}; \texttt{travel\_alert\_level} documents threat level in DB & 80 & \ttfamily\seqsplit{TravelStateGovConnector} & U.S. Government Work; mult=3.65; live \\
TrvlWarnCaVoy & 100 & advisory\_state & 75 & \ttfamily\seqsplit{TrvlWarnCaVoyConnector} & Open Government Licence - Canada; mult=1.52; live \\
TrvlWarnDe & 100 & warning,partial\_warning,situation\_warning & 75 & \ttfamily\seqsplit{TrvlWarnDeConnector} & Refer to Source Link; mult=1.52; live \\
TrvlWarnDeWarn & 100 & connector-specific (see connector class) & 75 & \ttfamily\seqsplit{TrvlWarnDeWarnConnector} & Refer to Source Link; mult=1.52; live \\
TrvlWarnUk & 100 & has\_travel\_advice & 75 & \ttfamily\seqsplit{TrvlWarnUkConnector} & Open Government Licence v2.0; mult=3.65; live \\
WorldEarthQuakeUsgs & 100 & value & 100 & \ttfamily\seqsplit{WorldEarthQuakeUsgsConnector} & USGS Public Domain; mult=7.30; live \\
WorldUnivDataSetIpsa & 40 & universities-per-capita score from COUNT(domains) vs population per iso2/\texttt{date\_published} & 25 & \ttfamily\seqsplit{WorldUnivDataSetIpsaConnector} & Refer to repository; mult=1.0; live \\
\end{longtable}
\endgroup
```

### Validation protocol

This part defines a **deposit-backed** validation protocol (hypotheses, metrics, sensitivity gates) and records **concrete numerical excerpts** regenerated from pinned deposit artefacts (**Empirical validation results** below). Protocol-only claims remain distinguished from **reported metrics**: anything tabulated there cites an on-disk CSV/JSON path or a reproducible command.

**Validation protocol scope:** full-panel forecasting benchmarks and **annual-index correlation studies** (FSI / GPI / WGI) require additional **released exports** merged to a frozen ISO/year panel; **Comparator indices** reports structural comparators now and defers cross-index Spearman coefficients until such a manifest exists. Bootstrap intervals and per‑source bias audits remain supported via deposit annexes.

### Hypotheses and baselines

- **H1 (event reaction):** event-window shift differs from baseline windows.  
- **H2 (classification quality):** crash predicate and event labels achieve better-than-random discrimination.  
- **H3 (forecast baseline):** persistence baseline is reported and compared to alternative baselines.

Baselines to include in deposited evaluations:

- persistence: $(\hat{y}_{t+h} = y_t)$ (naive copy-value forecast),
- **GDELT-derived alarm score baseline (reproducible):** For each country-day $(c,t)$, compute the **day-mean Goldstein scale** $G_{c,t}$ over all GDELT event rows retained by the **same iso2/day filter** recorded in the deposit’s GDELT→Layer‑1 recipe (`CountriesGdeltGlobRadarConnector` row window; empty days treated as missing unless a declared forward-fill rule is versioned in the evaluation manifest). Map to an alarm level on $[0,100]$ via $a_{c,t} = 100 \cdot \frac{10 - G_{c,t}}{20}$, so that $G=-10 \Rightarrow a=100$ (most conflictual) and $G=+10 \Rightarrow a=0$. The series $\{a_{c,t}\}$ is the **GDELT alarm baseline** for windowed contrasts against NFSI (and may be $z$-scored in the evaluation script, provided the transform is deposited).

- externally published index baseline where license permits comparison.

### Metrics and significance tests

- Event detection: precision, recall, F1, ROC/AUC.
- Forecast: RMSE, MAE per horizon.
- Robustness: sensitivity deltas under connector/constant perturbations.
- Significance: permutation sign-flip test (default), bootstrap CI where window-level samples are available.

Reference artefacts:

- **SM-10**
- **SM-11**
- **SM-12**
- **Validation annex (deposit):** **SM-47**
- **Sensitivity / robustness report template (deposit):** **SM-46**
- **One-shot reproduce bundle:** **SM-41**

Named-event illustrative metrics (deposit outputs, regenerated by the bundle):

- **SM-44**
- **SM-45**
- **SM-43**

### Crash-mode operational definition

The **normative** mathematical definition of $\mathrm{minSec}$, $\mathrm{crash\_mode}$, and the **authoritative SQL listing** are stated once under **Methodology** (“Normative SQL -- $\mathrm{minSec}$ and crash predicate”).  

Crash evaluation uses **real, non-placeholder** group-100 inputs only: connector-days synthesised at Layer‑3 as neutral `LAYER3_NO_DATA_NEUTRAL_SCORE` carry a **`no_data` flag** and **must be excluded** from $\mathrm{minSec}$ / crash predicates.

Third-party audits MUST align warehouse views to those predicates (repository-aligned naming; `nfsi_connector` / `nfsi_daily` / `nf_stab_*` deployments may alias columns--document equivalence).

Windowing for evaluation:

- primary: 24h trigger window,
- secondary robustness: 72h aggregation.

Evaluation outputs must report false-positive and false-negative rates against deposited event labels.

### Data quality metrics

Required operational metrics per release:

- coverage per country/date,
- missingness by connector/day,
- connector uptime,
- ingestion latency distribution (p50/p90/p99).

SQL queries: **SM-32**.

### Sensitivity and robustness

This subsection is **normative** for publication-style review: NFSI releases MUST include a sensitivity/robustness artefact set that demonstrates bounded output changes under the highest-leverage knobs and under missingness stress. The intent is not to optimise NFSI but to quantify **fragility** and to prevent silent behavioural drift.

**Required perturbation families (minimum):**

- **Layer‑4 daily cap**: vary `LAYER4_DAILY_CHANGE_CAP` over a declared grid.
- **Security conflict/crash thresholds**: vary `LAYER3_CONFLICT_THRESHOLD` and `LAYER4_SECURITY_CRISIS_THRESHOLD`.
- **Update multipliers**: multiply all `updateMult` values by a scalar $m \in [m_{\min}, m_{\max}]$ and report delta distributions.
- **Missingness worst-case**: run at least one declared missingness stress mode (e.g. increase $\mathrm{noDataRatio}$ by synthetic row removal and quantify crash-trigger deltas).
- **Dummy anchors**: vary dummy-anchor weights (`W_L`, `W_H`) and report sensitivity of the Layer‑3 base score.

**Deposit linkage (minimum artefacts):**

- Sweep plan: **SM-13**
- Execution template: **SM-38**
- Fixture delta matrix (example panel): **SM-12**
- Report template: **SM-46**

**Minimum panel size (full sweep, normative acceptance):** Let $|\mathcal{C}|$ be the audited active ISO2 universe for the release and let $|T|$ be the count of evaluation calendar dates in that release window. The production sensitivity sweep MUST include at least $N_{\min}=|\mathcal{C}|\cdot|T|$ country-days (complete grid unless a reviewer-approved sampling plan documents coverage and stratification).

**Fixture excerpt (panel illustration, not a full sweep):** the deposited **SM-12** artefact uses a toy grid with $N=30$. Summary statistics (mean, median, p95, $\max(|\Delta|)$):

**Table 5 — Sensitivity fixture summary statistics (toy grid, $N=30$ per perturbation family).**

| perturb | $N$ | mean $\Delta$ | median $\Delta$ | p95 $\Delta$ | $\max(|\Delta|)$ |
| :-- | --: | --: | --: | --: | --: |
| `SEC_CONFLICT_plus10` | 30 | +0.0373 | +0.0500 | +0.0700 | 0.0700 |
| `NET_OUTAGE_plus10` | 30 | -0.0120 | -0.0100 | +0.0100 | 0.0300 |
| `SOC_SENT_plus10` | 30 | -0.0187 | -0.0200 | +0.0000 | 0.0500 |
| `ECO_INFL_plus10` | 30 | -0.0047 | +0.0000 | +0.0000 | 0.0200 |
| `GOV_WGI_plus10` | 30 | -0.0047 | +0.0000 | +0.0000 | 0.0100 |

**Acceptance gate:** a release MUST provide the full sweep and a signed manifest (hashes of CSV + plots) when the manuscript/deposit is updated for a new implementation version.

**Hard pass/fail criterion (normative default):** Let $\Delta_{c,t}$ denote the NFSI point delta for country $c$ and date $t$ versus the unperturbed baseline for the same implementation snapshot. For any perturbation row in the signed sweep that is classified as a **standard** knob in the sweep plan (excluding rows explicitly tagged `exploratory` / `stress_only` in **SM-13**), the release **fails** validation if

$$\max_{c \in \mathcal{C},\, t \in T} |\Delta_{c,t}| > 2.0$$

A failed gate **blocks** external dissemination until: (i) rollback to the last passing build, (ii) manual recalibration with documented reviewer sign-off and updated constants/manifest, or (iii) a waiver recorded in the release manifest with a quantitative justification. Fixture illustrations with $N \ll |\mathcal{C}|\cdot|T|$ do not substitute for this full-panel test.

### Empirical validation results

The tables below restate outputs already present under **SM-01** (or values computed **deterministically** from those files). They are **illustrative** where $N$ is small or the panel is a toy fixture; they nonetheless satisfy the minimum expectation that empirical quantities are **named, bounded, and reproducible**.

#### Forecast skill -- persistence baseline (toy fixture)

Using the published sample fixture (**SM-39**) and **SM-36**, the **naive persistence** forecaster $\hat{y}_{t}=y_{t-1}$ (previous published NFSI vs next-day NFSI on the same ISO2) yields, over **20** successive country-day transitions on that fixture:

**Table 6 — Forecast skill: persistence baseline (toy fixture).**

| Metric | Horizon $h=1$ | Notes |
| :-- | --: | :-- |
| RMSE | **12.76** | toy panel; not production headline skill |
| MAE | **7.06** | same |

**Reproduce:** Run **SM-36** against the toy fixture tree **SM-39** (exact CLI flags in **SM-02**), then pair successive rows per ISO2. Production-grade RMSE requires exporting `(forecast_date, horizon, y_pred, y_true)` and running **SM-51** (**Reproducibility subsection**).

#### Named-event stress classification (real DB excerpt, small $n$)

Deposit outputs score **same-day deterioration** after external events listed in **SM-06**. Let **positive** denote “large same-day downward move” per the deposited rule `positive_if_delta_1d_lte = -3.0` (bootstrap CI manifest **SM-43**). This is **not** the Layer‑4 **crash_mode** Boolean; it is a **discriminative stress proxy** for manuscript **H2**.

Summary (**SM-45**, $n=5$ event rows, positive rate 0.6):

**Table 7 — Named-event stress classification metrics (deposit excerpt, small $n$).** *ROC AUC is shown as **n/a** (`-1.0` deposited) because this isolated $n=5$ slice yields **no false positives or true negatives**, so trapezoidal ROC area is mathematically undefined—not an empirical failure of rank scoring.*

| Metric | Value | Comment |
| :-- | :-- | :-- |
| Precision (at scanned threshold) | **1.00** | tp=3, fp=0 |
| Recall | **1.00** | fn=0 |
| F1 | **1.00** | degenerate perfect separation on $n=5$ |
| ROC AUC (trapezoid) | **n/a** | deposited value `-1.0` indicates **undefined** metric under tiny-$n$ scoring tie rules |

**Bootstrap mean $\Delta$ NFSI (day of event vs baseline):** mean **-1.45** index points; 95% bootstrap CI **\[-5.28, +2.51\]** ($B=5000$, same JSON manifest).

#### Crash-mode rule conformance (deterministic checks)

**Discriminative F1 for crash_mode vs an external crisis catalogue** is **not** deposited: it requires a labelled evaluation calendar. The following **implementation conformance** checks are nonetheless quantitative pass/fail artefacts:

**Table 8 — Crash-mode rule conformance (deterministic checks).**

| Check | Artefact | Disposition |
| :-- | :-- | :-- |
| Predicate $\mathrm{minSec}<\texttt{LAYER4\_SECURITY\_CRISIS\_THRESHOLD}$ triggers bypass | **SM-37** Test A | **Pass** (fixture forces crash where expected) |
| SQL outline alignment | **Methodology** normative SQL vs contributor filtering | **Closed** by construction |

#### Comparator indices -- refresh, coverage, correlation stance

Structural comparison (cadence and publisher-reported scale scope). **Spearman $\rho$** between **annualised NFSI** and each index requires a **dated merge manifest** (ISO3 harmonisation, lag alignment, overlapping years); cells show **n.a.** until that file ships alongside **SM-49**.

**Table 9 — Comparator indices: refresh cadence, coverage, and correlation stance.**

| Family | Median refresh interval | Economies covered (order of magnitude) | Spearman $\rho$ vs annualised NFSI | Primary public methodology |
| :-- | :-- | :-- | :-- | :-- |
| **NFSI (this work)** | **1 calendar day** | broad ISO2 surface (connector-dependent; comparable to $\approx 190+$ when connectors active) | -- | this manuscript + pinned implementation |
| Fragile States Index (FSI) | $\approx$ **365 d** | $\approx$ **178** | **n.a.** (manifest pending) | Fund for Peace FSI reports |
| Global Peace Index (GPI) | $\approx$ **365 d** | $\approx$ **163** | **n.a.** | Institute for Economics & Peace |
| Worldwide Governance Indicators (WGI) | $\approx$ **730 d** (biennial wave spacing typical) | **200+** | **n.a.** | World Bank WGI |
| NFSI vs WGI sub-indicators | -- | -- | **Mechanically coupled** for governance pull: **WGI-derived $\mathrm{est\_total}$** feeds Layer‑3; standalone $\rho$ must be interpreted as **construct overlap**, not independent validation |

![Fig. 6 -- Sensitivity fixture: mean absolute NFSI delta](research/peer-review/figures-png/fig08-sensitivity-mean-abs.png){#fig:06-sensitivity-mean-abs}

*Fig. 6 -- Caption:* Mean absolute published NFSI delta under five deposited perturbation families on the toy sensitivity grid ($N=30$ cells each); values coincide with the **Sensitivity and robustness** table columns “mean Δ” (absolute magnitude). Source: **SM-12**.

---

## Discussion and Limitations

This opening subsection positions the NFSI for **scientific readers** without altering the **normative** computation in **Methodology through Validation**.

### Epistemic scope

* **Not causal identification.** The pipeline specifies a deterministic mapping from declared inputs to $S_c(t)$; it does **not**, by itself, identify causal effects of policies, incidents, or interventions on countries’ stability outcomes.
* **Composite subjectivity.** Connector weights, group assignments, malus/bonus terms, and inertia/crash thresholds encode **engineering and policy choices**. They are documented and sweepable (**Validation and Results → Sensitivity and robustness**) but not uniquely determined by a single welfare objective.
* **Uncertainty.** Published NFSI values are **point outputs**. Probabilistic bands are **not** implied except where **Variant D** multiple imputation documents seeds and manifests; other layers are deterministic given pinned inputs.

### Endogeneity and feedback

High-frequency inputs (e.g. news-derived risk feeds) may co-move with geopolitical stress. **Misinterpretation risk:** treating NFSI as an independent “ground truth” can confuse descriptive composite movement with causal attribution. Operators SHOULD separate **inputs**, **outputs**, and **downstream decisions** (see **Governance ethics subsection**).

### External validation and comparability

Deposit-backed **numerical excerpts** (persistence-baseline residuals on the toy fixture, named-event metrics, sensitivity magnitudes, comparator cadence/coverage) appear in **Validation -- empirical excerpts**. **Cross-index Spearman correlations** against FSI / GPI / WGI await a **frozen annual merge manifest** (harmonised ISO list, year alignment); until then $\rho$ cells in **Validation -- Comparator indices** remain **n.a.** Readers SHOULD consult each publisher’s methodology before interpreting rank or level comparisons.

### Comparison landscape

Quantitative refresh/coverage comparators for headline indices are consolidated in **Validation -- Comparator indices**. Qualitative contrasts with additional peer families (e.g. V‑Dem) remain as in prior editions: annual methodology publications and open datasets where licenced.

---



### Governance, operations, and threats

#### Roles, controls, and audit logs

- Publication/change authority is defined in **SM-20** (A1/A2/E1/M1).
- Audit-log field schema: **SM-18**.
- Retention policy: **SM-22**.
- Technical attestation template: **SM-17**.

#### Change control and rollback

Constants and logic changes are governed by:

- **SM-19**.

Minimum gate set before rollout:

- fixture tests pass,
- invariant checks pass,
- sensitivity artefacts updated,
- documented rollback reference.

#### License and publishability matrix

Connector-level publishability assessment is deposited at:

- **SM-25**.

Fields include: raw-data redistributability, aggregated-score publishability, attribution requirement, and publishability status (`yes/no/conditional`).

#### API transparency contract

A minimal OpenAPI contract for published NFSI outputs is provided at:

- **SM-05**.

#### Threat model

This subsection is **normative** for publication‑grade security review of an operational, connector‑backed stability index and documents minimum threat assumptions and mitigations.

**Assets and trust boundaries**
* **Assets:** raw connector payloads; Layer‑1/2 intermediates; published NFSI values; provenance/flags (padded/imputed/placeholder); release manifests; audit logs.
* **Boundaries:** connector ingestion boundary (external → internal); computation boundary (L1-L4); publication boundary (API/web exports).

**Threats / attack vectors**
* **Data poisoning:** adversary injects or manipulates upstream signals (e.g. automated news spam, event-source manipulation) to move security-group minima or amplify deltas.
* **Adversarial missingness:** targeted outages/delays of high-influence connectors to suppress crash mode or to bias the composite toward neutral substitutions.
* **Weight/constant tampering:** unauthorized changes to **SM-15** / connector metadata (weights, groups, update multipliers) to alter scores.
* **Replay / stale-data attacks:** old connector-day scores replayed to avoid detection of acute changes.
* **Export integrity attacks:** tampering with published JSON/CSV so consumers see altered NFSI values or missing provenance flags.

**Mitigations (minimum required)**
* **Provenance + flagging:** exports MUST include `was_placeholder`, `was_padded`, `was_imputed`, coverage metrics, and last-update timestamps per connector-day (optional legacy keys such as `pad_variant` may remain for backward-compatible audits--see **Appendix C**).
* **Integrity gates:** deterministic recompute fixtures, invariant checks, and evidence hashes (see **Reproducibility** subsection and **SM-48**).
* **Change control:** constants/weights changes require logged approvals and tagged releases (see **SM-19**).
* **Anti-replay:** ingestion stores provider snapshot timestamps and rejects/flags out-of-window payloads (operator policy; export the resulting flags).
* **Monitoring:** operational alerting on coverage drops, latency spikes, and abrupt cross-connector distribution shifts. For **country-day** $\mathrm{noDataRatio}$ (**Methodology / Appendix B**): emit a **severity‑2 (warning)** when $\mathrm{noDataRatio} \geq 0.40$ on monitored aggregates; escalate to **severity‑1** when $\mathrm{noDataRatio} \geq 0.50$. These thresholds track **missingness operational risk**; Layer‑4 inertia remains **binary** ($0.50$ if $N_{\mathrm{no\_data}}>0$, else $0.80$; **Methodology / Appendix A**).
* **Red-team tests:** adversarial missingness simulations (random/block/country-targeted) must be run per release candidate; publish FPR/FNR for crash gate where labels exist.

**Discrimination limits: random outage vs systematic suppression (Layers 1-2).** Layer‑1/Layer‑2 score algebra **cannot**, by itself, invert the missingness mechanism: a benign CDN outage, a routing partition, and a coordinated state-level signal blackout can yield **indistinguishable** sparse score matrices at the connector-day granularity. The NFSI pipeline therefore does **not** claim to classify “server fault” vs “strategic withholding” from scores alone. Instead, audit-grade operation must fuse **orthogonal evidence**: (i) cross-connector correlation of dropout (single-AS failures vs broad, geography-aligned gaps); (ii) external reachability / BGP / public incident telemetry where available; (iii) temporal clustering relative to known conflict escalations; (iv) mandatory `security_missingness_review` when structural pads contribute to security MIN under the **manuscript normative** missing-as-worst rule (**Methodology**). Neutral midpoint pads (**50**) apply **only** to non-security anchored averages--not to security MIN semantics.

#### Ethics and societal impact

Beyond operational threat controls (**Governance subsection above**) and protocol ethics (**Validation (T1-T3)**), publication-grade use SHOULD address:

* **Misuse / dual-use:** Composite indices can inform travel, finance, insurance, or migration narratives. Publishers SHOULD avoid implying precision beyond documented uncertainty (see **Limitations**) and SHOULD maintain dispute and correction pathways (**SM-20**).
* **Equity and dignity:** Country-level scores aggregate heterogeneous populations; they MUST NOT substitute for situational intelligence or ground verification in humanitarian or security contexts.
* **Feedback loops:** Repeated consumption of the same headline score in automated systems may amplify volatility perception; governance SHOULD favour provenance-rich exports and human gates (**T1-T3**).
* **Environmental footprint:** High-frequency ingestion has compute and energy costs; operators SHOULD document retention and batching policies (**SM-22**).

---



### Terminology

**Table 10 — Abbreviated glossary of terms.**

| Term | Definition |
| :--- | :--- |
| **NFSI** | NationFiles Stability Index. A 0-100 normalised stability indicator per country and day. Higher value = more stable assessment; computed from multiple data sources across four processing layers (Layers 1-4). |
| **Layer 1** | First processing layer: raw values from each source are normalised to a row-level score 0-100 (min-max, with fixed direction ‘higher = better/worse’). |
| **Layer 2** | Second layer: row scores are aggregated per source, country, and date into a daily score and smoothed with the previous day (e.g. 60% today, 40% previous day). |
| **Layer 3** | Third layer: the country NFSI (raw score) is computed from all source scores: weighted average with effective weights, conflict malus, WGI pull, and other adjustments. |
| **Layer 4** | Fourth layer: daily smoothing (inertia). The shipped implementation applies $w_{\mathrm{inertia}}$ to **yesterday’s published NFSI** and $(1-w_{\mathrm{inertia}})$ to **today’s Layer‑3 bounded score** $\max(\texttt{LAYER3\_SCORE\_FLOOR},\mathrm{nfsi\_raw}^{\mathrm{rounded}})$ (post‑floor Layer‑3 output), **not** the raw pre‑floor value. **Effective** weights are **$(w_{\mathrm{prev}},w_{\mathrm{today}})=(0.50,0.50)$** when any neutral Layer‑3 substitution occurred ($N_{\mathrm{no\_data}}>0$), else **$(0.80,0.20)$**; see **Methodology / Appendix A**. Crash mode bypasses smoothing; $\pm\texttt{LAYER4\_DAILY\_CHANGE\_CAP}$ applies when inertia runs. |
| **Raw value (raw)** | Original numeric value from a data source before conversion to a 0-100 score (e.g. conflict count, percentage, scale value). |
| **Normalisation** | Conversion of raw values into a common range (here 0-100). With min-max: (value - minimum) / (maximum - minimum) * 100, optionally inverted when ‘higher = worse’. |
| **Score (row / daily / country)** | A value bounded to 0-100: row score per record (Layer 1), daily score per source/country/date (Layer 2), country score = NFSI (Layers 3-4). |
| **Goldstein scale** | Scale used by GDELT from -10 (highly conflictual) to +10 (cooperative). Translated into stability scores in NFSI logic (higher Goldstein = better stability). |
| **Dummy value** | Fixed additive value (0 and 100) in aggregation so that averages and weighting remain stable even when few sources are available. |
| **Inertia** | Smoothing over time: published NFSI blends yesterday’s value with today’s Layer‑3 output. **Shipped** policy: faster reaction ($0.50$ previous weight) whenever **any** neutral Layer‑3 substitution exists; otherwise standard **$0.80$** (**Methodology / Appendix A**). |
| **Recovery** | When no new data exists for a country/date, the score is gradually raised (up to cap 95) so that gaps are not penalised indefinitely. |
| **Recovery cap** | Upper bound on recovery-driven score increases without new measurements (e.g. `LAYER2_RECOVERY_CAP=95`). Bounded recovery mechanisms prevent asymptotic convergence to an unrealistically “perfect” stability score when observations remain absent. |
| **Conflict malus** | Deduction from the NFSI when the minimum of security indicators (e.g. conflicts, violence) falls below a threshold (70). Emphasises the role of security data. |
| **WGI (Worldwide Governance Indicators)** | World Bank indicators of government quality (rule of law, effectiveness, etc.). In NFSI: ‘WGI pull’ can raise the raw score when governance is strong. |
| **Security group (group 100)** | Thematic group of security-critical sources (e.g. conflicts, travel warnings). For daily aggregation the minimum is taken (one critical event suffices). |
| **Coverage** | For a fixed `(connector_id, date)`, the fraction of ISO2 countries with at least one **real Layer‑1** row (`k_meas>0`). Used to flag low-coverage connector-days. |
| **Trend synthesis** | Deterministic imputation step used when a connector-day is active for some countries but missing for a specific country; the missing cell is synthesised from last known value and cross-country day trend and must be flagged as imputed/placeholder as applicable. |
| **Connector / data source** | An integrated data source (e.g. World Bank, GDELT, ACLED) that contributes to the NFSI in Layers 1-3. Each connector has a thematic group and a weight. |
| **`contributors_count`** | In exports/logs: typically $N_{\mathrm{slots}}$--Layer‑3 connector slots in the composite for `(iso2, date)` (includes neutral fills). Distinct from “rows with measured Layer‑1 evidence.” |
| **No-data (Layer‑3 substitution)** | A connector lacks a real `nfsi_connector` observation for `(iso2, date)` during Layer‑3 aggregation. Implemented as **`LAYER3_NO_DATA_NEUTRAL_SCORE` (50)** plus an explicit internal `_no_data` flag so neutral substitution is **never** mistaken for measured security deterioration. |
| **Placeholder score** | Synthetic **non-real** substitution emitted for operational completeness (distinct from statistical missingness); must be excluded from crash/min-security predicates ($\mathrm{minSec}$). Flag names vary by deployment; exporters should materialise `was_placeholder`/equivalent explicitly. |
| **`refLog` / `popLog`** | Code identifiers for $R_{\log}$, $P_{\log}$ (Layer 3): $R_{\log}=\log_{10}(\max(N_{\mathrm{ref}},\mathrm{Pop}_{\min}))$, $P_{\log}=\log_{10}(\max(\mathrm{pop},\mathrm{Pop}_{\min}))$ with $N_{\mathrm{ref}}=$ `LAYER3_POPULATION_REF`, $\mathrm{Pop}_{\min}=$ `LAYER3_POP_LOG_MIN`. |
| **`popNegMult`** | Code identifier for $M_{\mathrm{neg}}$: $\min(2,\ R_{\log}/P_{\log})$ with cap `LAYER3_POP_NEG_MULTIPLIER_CAP` (=2). |
| **`sumWeighted`, `sumWeights`, `effW_n`** | Layer‑3 numerators/denominator for weighted composition: $effW_n = g_n \cdot (w_n/100)\cdot u_n$, then aggregate with dummy anchors (`NFSI_DUMMY_LOW`, `NFSI_DUMMY_HIGH`) as in the **reference implementation** (pinned PHP module). |
| **`contributors_count` / `no_data_ratio`** | $N_{\mathrm{slots}}$ vs $N_{\mathrm{no\_data}}$ (internal `_no_data_connectors`); $\mathrm{noDataRatio}=N_{\mathrm{no\_data}}/N_{\mathrm{slots}}$. Shipped inertia uses `_score_without_l1_l2_data` $\Leftrightarrow (N_{\mathrm{no\_data}}>0)$ (**Methodology / Appendix B**). |
| **VAR (vector autoregression)** | Multivariate time-series model relating several variables over time; used in some optional product stacks, **not** specified by L1–L4 here. |
| **Data provenance** | Origin and traceability of data: which source, which licence, how often updated. Documented in this report’s data inventory and legal sources. |
| **Audit trail** | Traceable log of changes (e.g. data snapshot, model version) with timestamp and hash, for auditors and reproducibility. |
| **Reproducibility** | Property that the same result can be obtained from the same inputs and methodology. Pursued via documented formulas, code, and data provenance. |

---



### Related documents

Operator-published summaries (methodology, governance, security posture) may accompany this manuscript but are **not** normative substitutes for the deposited artefacts in **SM-01** and **SM-48**. Reviewers should privilege: the deposited evidence manifests (**SM-49/SM-50**), **SM-23**–**SM-24**, **SM-15**, governance SOPs under **SM-16**, and the OpenAPI stub **SM-05**.

---



### Reproducibility, evidence pack, and errata

This annex provides a **verifiable evidence mechanism** for reviewers and auditors:

* **Implementation constancy**: pin to a stable implementation identifier; hash key artefacts.
* **Reproducibility**: define what inputs are required; provide a fixture pattern and testable invariants.
* **Forecast validation (optional):** deposit backtest/RMSE tooling (**SM-51**, **SM-42**); performance claims belong in dated artefacts, not static prose.

In addition, a **deposit-ready local bundle** is provided at **SM-01**, including:

* a synthetic/redacted **sample dataset fixture** (>= 10 countries x 30 days),
* a minimal schema (**SM-40**),
* executable unit tests and validation scaffolds (**SM-30**),
* governance artefacts (**SM-16**),
* a formal, machine-checkable formulas annex (**SM-04** and **SM-03**),
* machine-readable connector provenance (**SM-23**),
* validation annex templates (**SM-47**, **SM-46**),
* one-shot reviewer runner (**SM-41**).

#### Implementation constancy (code/commit + hashes)

**Reference module:** redacted in the public package (**security hardening**). For peer reviewers who require source-level verification, the exact module path can be provided under controlled access together with the pinned implementation identifier.

**Pinned implementation identifier:** `redacted identifier (available to reviewers under controlled access)` (redacted in the public package; recorded in evidence manifests and available to reviewers under controlled access if required).

**Evidence manifest (auto-generated):** **SM-49** (generated at `2026-05-05 08:05:32 UTC`).

**SHA-256 sums (auto-generated):** **SM-50**.

Notes:
* The repository can be operationally “dirty” due to connector runtime artefacts (queues, timestamps). The evidence manifest hashes **only** the key documentation/build artefacts and a pinned implementation artifact (redacted in the public package), not runtime queues.
* For a third-party audit, pin to a clean tag and attach the manifest + sums as deposited artefacts (see the deposit packaging checklist in the **Validation** section above).

#### Reproducibility on real inputs (or a clean fixture)

To reproduce a country/day NFSI value deterministically, a reviewer needs:

* **Layer 1 inputs**: raw rows per connector per (iso2, date) for the defined raw column(s), plus direction metadata (“higher = worse/better”).
* **Layer 2 inputs**: per-connector/day aggregation policy, including group handling (security MIN), dummies/pads, recovery rules, and start values.
* **Layer 3 inputs**: connector-day scores, connector group/weight matrix, governance inputs (e.g. $\mathrm{est\_total}$ where used), and population values where penalties/bonuses apply.
* **Layer 4 inputs**: previous-day score, $\mathrm{minSec}$, and the inertia/crash-mode rules.

**Fixture recommendation (publishable):**
* Provide a minimal synthetic dataset with a handful of countries (e.g. 3) and 10-20 consecutive days.
* Include cases for:
  - Missing connectors (no-data ratio $\geq 0.5$)
  - Security group present vs absent
  - Crash mode triggered ($\mathrm{minSec} < 25$)
  - Daily change cap (±3) binding

**Testable invariants (audit checks):**
* All emitted scores must satisfy $(1 \leq \text{NFSI} \leq 100)$ (floor and cap).
* If crash mode triggers, Layer 4 must not apply inertia (reason logged).
* If inertia applies, the per-day delta must be capped at $\pm 3$.
* Missing connectors must not be treated as security negatives for $\mathrm{minSec}$ (only real group-100 data contributes to crash/conflict minima).

Deposit-side executable checks:
* **Bundle:** **SM-41** (markdown audit → deterministic fixture recompute ×2 → invariants → unit tests → playbook assertions → pandoc/lualatex smoke → PDF text sanity)
* **À-la-carte:** **SM-36**, **SM-53**, and **SM-37** against fixture **SM-39** (CLI examples in **SM-02**).
* Invariant definitions: **SM-21**

#### Forecast validation (optional): backtest + RMSE

For traceability, deposited tooling supports evaluation without freezing fragile numbers in prose:

* **Performance annex (if claimed):** dataset window, exclusions, target, baselines, rolling-origin protocol, horizons/metrics.
* **RMSE scaffold:** **SM-51** — input CSV columns `iso2, forecast_date, horizon_days, y_pred, y_true`; outputs RMSE overall and per horizon.

Archive generated RMSE/MAE tables under **SM-42** with timestamps and input manifests; cite a dated backtest report when asserting performance.

Computation scripts and raw export snapshots are provided in the deposit bundle:

- **SM-34**
- **SM-33**
- **SM-29**

Normative pipeline pseudocode (deposit artefact):

- **SM-28**

Backtest protocol runner (no Jupyter required):

- **SM-11**

#### Errata and intentional specification deltas

Shipped-code parity differences versus this manuscript’s **single normative variant** are consolidated in **Appendix C**. Summary:

**Table 11 — Errata and intentional specification deltas.**

| Topic | Manuscript (normative) | Deployed PHP (non-normative for prose claims) |
|:------|:------------------------|:-----------------------------------------------|
| L2 security MIN structural pads | Pad absent multiset slots with **0** | Literal **`100.0`** in MIN multiset for parity audits |
| Gap synthesis when Layer‑2 history absent | Conservative **0** endpoint aligned with missing-as-worst | Default **`100.0`** in reference module |
| L4 inertia | Binary **0.50** / **0.80** vs $N_{\mathrm{no\_data}}$ | Same binary mapping in shipped reference module (**Appendix C**) |

* **Pseudocode naming:** CamelCase symbols in **Methodology / Appendix B** map to `snake_case` PHP fields where noted.

**PDF / build note:** The **canonical** reviewer PDF is generated by the NationFiles **HTML + KaTeX** pipeline (`ResearchPdfBuilder`). The **IEEE-style** PDF for journal submission builds via **`bash research/build-peer-review-pdf-ieee.sh`** (Pandoc + **IEEEtran** + **pdflatex** ×3, optional **bibtex** if `\bibdata` is present). Overfull box warnings can still appear on wide tables; treat them as **typographic** unless paired with `LaTeX Error`. Combined transcript: **SM-14** (SM supplement appended after main matter).

---

## Conclusion

The NationFiles Stability Index is specified as a **deterministic** mapping from declared connector inputs and manifests to a bounded daily country score $S_c(t)\in[1,100]$. Four layers--normalisation, connector-day aggregation with explicit missingness semantics, a weighted national composite with malus/bonus terms, and inertia with a crash-mode gate driven by measured security minima--yield reproducible outputs under a pinned implementation. **Validation and Results** summarises deposit-backed metrics (fixture persistence errors, named-event discrimination, sensitivity panels, structural comparison to annual indices). Full rank correlations against FSI/GPI/WGI require frozen merge manifests. Operators SHOULD combine headline scores with provenance exports, mandatory human gates where stated, and the reproducibility bundle described under **Discussion**.


---

## References

Composite methodology, reproducibility, and comparator indices (selection for positioning NFSI in the literature; URLs current as of manuscript date):

1. OECD & Joint Research Centre (2008). *Handbook on Constructing Composite Indicators: Methodology and User Guide*. OECD Publishing. [https://doi.org/10.1787/9789264043466-en](https://doi.org/10.1787/9789264043466-en)
2. Saltelli, A., Ratto, M., Andres, T., et al. (2008). *Global Sensitivity Analysis: The Primer*. Wiley.
3. Peng, R. D. (2011). Reproducible research in computational science. *Science*, 334(6060), 1226-1227. [https://doi.org/10.1126/science.1213847](https://doi.org/10.1126/science.1213847)
4. Stodden, V., McNutt, M., Bailey, D. H., et al. (2016). Enhancing reproducibility for computational methods. *Science*, 354(6317), 1240-1241. [https://doi.org/10.1126/science.aah6168](https://doi.org/10.1126/science.aah6168)
5. Fund for Peace (ongoing). *Fragile States Index -- Methodology*. [https://fragilestatesindex.org/methodology/](https://fragilestatesindex.org/methodology/)
6. Institute for Economics & Peace (ongoing). *Global Peace Index* -- methodology in annual report annexes. [https://www.economicsandpeace.org/](https://www.economicsandpeace.org/)
7. World Bank (ongoing). *Worldwide Governance Indicators -- Overview / methodology*. [https://www.worldbank.org/en/publication/worldwide-governance-indicators](https://www.worldbank.org/en/publication/worldwide-governance-indicators)
8. Coppedge, M., et al. (ongoing). *V-Dem Methodology*. V-Dem Institute. [https://www.v-dem.net/documents/methodology/](https://www.v-dem.net/documents/methodology/)
9. Raleigh, C., et al. ACLED methodology & codebook (ongoing). Armed Conflict Location & Event Data Project. [https://acleddata.com/methodology/](https://acleddata.com/methodology/)
10. Leetaru, K., Schrodt, P. A., et al. GDELT project documentation (ongoing). [https://www.gdeltproject.org/](https://www.gdeltproject.org/)
11. Morgan, M. G., & Henrion, M. (1990). *Uncertainty: A Guide to Dealing with Uncertainty in Quantitative Risk and Policy Analysis*. Cambridge University Press.
12. Saisana, M., Saltelli, A., & Tarantola, S. (2005). Uncertainty and sensitivity analysis techniques as tools for the quality assessment of composite indicators. *Journal of the Royal Statistical Society: Series A*, 168(2), 307-323. [https://doi.org/10.1111/j.1467-985X.2005.00350.x](https://doi.org/10.1111/j.1467-985X.2005.00350.x)

---

## Appendix A -- Full constants table

For self‑contained peer review, this appendix reproduces the complete constant set that is otherwise deposited as **SM-15**. **Sensitivity** follows the taxonomy of **Validation and Results → Sensitivity and robustness** (required perturbation families and operational audit distinction). Tables are split by layer for LaTeX/IEEE column width (same content as a single matrix).

### Appendix A.1 -- Layer 1

**Table 12 — NFSI constants: Layer 1.**

| Name | Value | Rationale | Sensitivity |
| :--- | :--- | :--- | :--- |
| `LAYER1_DEFAULT_SCORE` | `50` | Neutral score when raw span is degenerate or bounds missing | Indirect (neutral fill propagates); not a named sweep axis |

### Appendix A.2 -- Layer 2

**Table 13 — NFSI constants: Layer 2.**

```{=latex}
\begingroup
\setlength{\tabcolsep}{3pt}
\renewcommand{\arraystretch}{1.08}
\footnotesize
\begin{longtable}{@{}>{\RaggedRight\ttfamily\arraybackslash}p{0.28\linewidth} >{\RaggedRight\ttfamily\arraybackslash}p{0.09\linewidth} >{\RaggedRight\arraybackslash}p{0.31\linewidth} >{\RaggedRight\arraybackslash}p{0.30\linewidth}@{}}
\textbf{Name} & \textbf{Value} & \textbf{Rationale} & \textbf{Sensitivity} \\
\hline
\endhead
\seqsplit{LAYER2\_TODAY\_WEIGHT} & 0.6 & Today weight $\alpha$ in connector-day smoothing & Indirect (smoothing geometry); couples to headline volatility \\
\seqsplit{LAYER2\_YESTERDAY\_WEIGHT} & 0.4 & Yesterday weight $\beta$ in connector-day smoothing & Indirect (paired with \seqsplit{LAYER2\_TODAY\_WEIGHT}) \\
\seqsplit{LAYER2\_NO\_DATA\_START\_SECURITY} & 70 & Default “yesterday” when no prior day exists (security group) & Missingness / continuity path \\
\seqsplit{LAYER2\_NO\_DATA\_START\_OTHER} & 85 & Default “yesterday” when no prior day exists (non-security) & Missingness / continuity path \\
\seqsplit{LAYER2\_RECOVERY\_PER\_DAY\_SECURITY} & 0.2 & Recovery increment per empty day (security), capped elsewhere & Missingness stress (recovery ladder) \\
\seqsplit{LAYER2\_RECOVERY\_PER\_DAY\_OTHER} & 1.0 & Recovery increment per empty day (non-security) & Missingness stress (recovery ladder) \\
\seqsplit{LAYER2\_RECOVERY\_CAP} & 95 & Upper cap for recovery-filled scores & Missingness stress (bounds) \\
\seqsplit{LAYER2\_RECOVERY\_MAX\_DAYS} & 90 & Maximum consecutive recovery days & Missingness stress (horizon) \\
\seqsplit{NFSI\_DUMMY\_LOW} & 0 & Low anchor in non-security AVG multiset & Direct sweep knob -- dummy anchors (Validation sensitivity) \\
\seqsplit{NFSI\_DUMMY\_HIGH} & 100 & High anchor in non-security AVG multiset & Direct sweep knob -- dummy anchors \\
\seqsplit{NFSI\_ARRAY\_PAD\_VALUE} & 50 & MID pad value for non-security length alignment & Missingness / substitution geometry \\
\seqsplit{LAYER2\_SECURITY\_STRUCTURAL\_PAD\_VALUE} & 100 & MIN multiset pad in \textbf{deployed PHP} (length $M$); manuscript normative MIN uses \textbf{0} (Appendix C) & Parity-only literal; not used for manuscript methodological claims \\
\seqsplit{LAYER2\_COVERAGE\_WARN\_THRESHOLD} & 0.2 & Fractional coverage below which operators SHOULD flag sparse connector-days & Operational audit only (not a score sweep knob) \\
\end{longtable}
\endgroup
```

### Appendix A.3 -- Layer 3

**Table 14 — NFSI constants: Layer 3.**

```{=latex}
\begingroup
\setlength{\tabcolsep}{3pt}
\renewcommand{\arraystretch}{1.08}
\footnotesize
\begin{longtable}{@{}>{\RaggedRight\ttfamily\arraybackslash}p{0.28\linewidth} >{\RaggedRight\ttfamily\arraybackslash}p{0.09\linewidth} >{\RaggedRight\arraybackslash}p{0.31\linewidth} >{\RaggedRight\arraybackslash}p{0.30\linewidth}@{}}
\textbf{Name} & \textbf{Value} & \textbf{Rationale} & \textbf{Sensitivity} \\
\hline
\endhead
\seqsplit{LAYER3\_NO\_DATA\_NEUTRAL\_SCORE} & 50 & Substitute score for flagged missing connectors at composition & Missingness stress / substitution \\
\seqsplit{LAYER3\_SCORE\_FLOOR} & 1 & Lower bound on published / post-floor NFSI & Indirect (bounds published scale) \\
\seqsplit{LAYER3\_NO\_SECURITY\_DEFAULT\_MINSEC} & 80 & Default $\mathrm{minSec}$ when no real security connector exists & Indirect (gates conflict/crash predicates) \\
\seqsplit{LAYER3\_DUMMY\_LOW\_WEIGHT} & 1 & Weight on dummy low anchor in Layer-3 mean & Direct sweep knob -- dummy anchors \\
\seqsplit{LAYER3\_DUMMY\_HIGH\_WEIGHT} & 1 & Weight on dummy high anchor in Layer-3 mean & Direct sweep knob -- dummy anchors \\
\seqsplit{LAYER3\_CONFLICT\_THRESHOLD} & 70 & $\mathrm{minSec}$ below which conflict malus activates & Direct sweep knob -- conflict threshold family \\
\seqsplit{LAYER3\_CONFLICT\_MALUS\_FACTOR} & 1.0 & Linear multiplier on depth below threshold & Direct sweep knob -- conflict threshold family \\
\seqsplit{LAYER3\_CONFLICT\_MALUS\_CAP} & 35 & Cap on conflict malus points & Direct sweep knob -- conflict threshold family \\
\seqsplit{LAYER3\_POPULATION\_REF} & 45000000 & Reference population for pop malus/bonus & Indirect composite lever \\
\seqsplit{LAYER3\_POP\_LOG\_MIN} & 100000 & Floor on population used in log terms & Indirect composite lever \\
\seqsplit{LAYER3\_POP\_NEG\_MULTIPLIER\_CAP} & 2.0 & Caps population-negative multiplier & Indirect composite lever \\
\seqsplit{LAYER3\_WGI\_SENTIMENT\_SCALE} & 10 & Maps $\mathrm{est\_total}$ to a $[0,10]$-style WGI axis in formulas & Indirect (ties to governance pull) \\
\seqsplit{LAYER3\_FRAGILITY\_POP\_FACTOR} & 3.0 & Scales fragility population term & Indirect composite lever \\
\seqsplit{LAYER3\_FRAGILITY\_MALUS\_CAP} & 15 & Cap on fragility malus & Indirect composite lever \\
\seqsplit{LAYER3\_SMALL\_POP\_THRESHOLD} & 5000000 & Threshold for small-population malus & Indirect composite lever \\
\seqsplit{LAYER3\_SMALL\_POP\_MALUS\_PER\_LOG10} & 4.0 & Malus per $\log_{10}$ band below threshold & Indirect composite lever \\
\seqsplit{LAYER3\_SMALL\_POP\_MALUS\_CAP} & 25 & Cap on small-pop malus & Indirect composite lever \\
\seqsplit{LAYER3\_POPULATION\_BONUS\_FACTOR} & 0.5 & Scales population bonus term & Indirect composite lever \\
\seqsplit{LAYER3\_POPULATION\_BONUS\_CAP} & 4.0 & Cap on population bonus & Indirect composite lever \\
\seqsplit{LAYER3\_WGI\_PULL} & 0.95 & Strength of governance pull toward $\mathrm{est\_total}$ & Indirect composite lever (construct overlap with WGI) \\
\end{longtable}
\endgroup
```

### Appendix A.4 -- Layer 4

**Table 15 — NFSI constants: Layer 4.**

```{=latex}
\begingroup
\setlength{\tabcolsep}{3pt}
\renewcommand{\arraystretch}{1.08}
\footnotesize
\begin{longtable}{@{}>{\RaggedRight\ttfamily\arraybackslash}p{0.28\linewidth} >{\RaggedRight\ttfamily\arraybackslash}p{0.09\linewidth} >{\RaggedRight\arraybackslash}p{0.31\linewidth} >{\RaggedRight\arraybackslash}p{0.30\linewidth}@{}}
\textbf{Name} & \textbf{Value} & \textbf{Rationale} & \textbf{Sensitivity} \\
\hline
\endhead
\seqsplit{LAYER4\_INERTIA\_STANDARD} & 0.80 & Inertia weight when $N_{\mathrm{no\_data}}=0$ & Indirect (smoothing path; not a listed sweep family) \\
\seqsplit{LAYER4\_INERTIA\_SCORE\_WITHOUT\_L1L2} & 0.50 & Inertia weight when $N_{\mathrm{no\_data}}>0$ (matches \texttt{\_score\_without\_l1l2\_data}) & Indirect (paired with standard weight) \\
\seqsplit{LAYER4\_DAILY\_CHANGE\_CAP} & 3 & Max absolute day-on-day move under inertia & Direct sweep knob -- daily cap family \\
\seqsplit{LAYER4\_SECURITY\_CRISIS\_THRESHOLD} & 25 & $\mathrm{minSec}$ threshold for crash mode & Direct sweep knob -- crash threshold family \\
\end{longtable}
\endgroup
```

**Rounding / storage (applies to all layers):** round all stored scores to **two decimals** (round-half-away-from-zero); suggested SQL type **`DECIMAL(5,2)`** for published intermediates in deposit outputs.

---

## Appendix B -- Layer pseudocode

The authoritative full pseudocode is **SM-28**. The deposit text may still describe **100**-based security MIN padding for recompute bundle compatibility; the **manuscript-normative** security MIN uses structural pad **0** (**Methodology**). Excerpt (opening):

```text
NFSI L1-L4 deterministic pseudocode

========================
L1 - Row-level normalisation
========================
INPUT per connector k:
  - rows r_i with raw_value r_i and row timestamp/event id (for deterministic sorting)
  - direction higher_raw_is_worse_k in {0,1}
  - bounds min_raw_k, max_raw_k over the deposited snapshot for connector k

FOR each row i:
  span = max_raw_k - min_raw_k
  IF span <= 0 OR min_raw_k/max_raw_k missing:
      normalized = 50
  ELSE:
      normalized = 100 * (r_i - min_raw_k) / span
  normalized = clamp(normalized, 0, 100)

  IF higher_raw_is_worse_k == 1:
      score_row = 100 - normalized
  ELSE:
      score_row = normalized
  score_row = round2(clamp(score_row, 0, 100))

OUTPUT:
  - score_row per row (0..100)

========================
L2 - Connector-day aggregation + smoothing + recovery
========================
INPUT per (connector k, country c, day t):
  - all L1 score_row values for that triple, sorted deterministically by event_timestamp ASC
  - group_weight g_k (100 = security group)
  - pad policy:
      - security: MIN(scores), missing padded with 100 for MIN semantics
      - non-security: arr = [0] + scores + [100]; dayScore = AVG(arr)
  - smoothing weights alpha=0.6 (today), beta=0.4 (yesterday)
  - default yesterday:
      - security: 70
      - other: 85
  - recovery:
      - per day without data: +0.2 (security) or +1.0 (other), capped at 95
      - maximum recovery days: 90

DAY AGGREGATION:
  IF g_k == 100:
      dayScore = MIN(scores)  (if score list empty: dayScore is "missing")
  ELSE:
      arr = [0] + scores + [100]
      dayScore = AVG(arr)

SMOOTHING (if dayScore present):
  y = yesterday L2 score for (k,c,t-1) if exists else default_yesterday(g_k)
  score_final = alpha * dayScore + beta * y
  score_final = round2(clamp(score_final, 0, 100))

RECOVERY (if dayScore missing for a day):
  Start from last available score_final on previous day; apply per-day recovery step until:
    - next real dayScore exists, OR
    - recovery reaches cap 95, OR
    - 90 days filled.

OUTPUT:
  - score_final per connector/country/day (0..100)

========================
L3 - Country composition (nfsi_raw)
========================
INPUT per (country c, day t):
  - per connector k: L2 score_final (0..100) if present else missing
  - connector meta:
      - group g_k (int; group < 0 excluded)
      - scoreValue_k = connector_weight_k / 100 (float 0..1)
      - updateMult_k (float)
  - constants:
      - LAYER3_NO_DATA_NEUTRAL_SCORE = 50
      - conflict threshold = 70, factor=1.0, cap=35
      - population ref = 45,000,000; pop log min=100,000; popNeg cap=2.0
      - fragility factor=3.0 cap=15; WGI_0_10 = est_total/10; governanceGap=10-WGI_0_10
      - small country threshold=5,000,000; malus per log10=4.0 cap=25
      - population bonus = min(4, log10(pop)*0.5)
      - WGI pull = 0.95
      - floor=1 cap=100
```

---

## Appendix C -- Implementation note

**Status: non-normative for manuscript claims.** This appendix records how the **pinned PHP reference** behaves when auditors require **numeric parity** with the deployed connector pipeline. It does **not** replace the **single** rule-set in **Methodology** (security MIN with structural pad **0**).

**Table 16 — Manuscript normative behaviour vs shipped PHP reference module (implementation parity).**

| Aspect | Manuscript (normative) | Shipped reference module (parity audits) |
|:-------|:------------------------|:----------------------------------------|
| Security-group MIN padding to length $M$ | pad with **0** (missing-as-worst) | literal **`100.0`** in MIN multiset (see `LAYER2_SECURITY_STRUCTURAL_PAD_VALUE` in **Appendix A**) |
| Gap-synthesis default when no in-country L2 history | **0** (aligned with missing-as-worst) | default **`100.0`** in PHP for cold-start cells |
| Export / manifest keys | optional provenance flags (`was_padded`, `was_imputed`, …) | legacy keys (e.g. `pad_profile`, `pad_variant`) may appear in operator exports for audit trails only |
| L4 inertia (parity audits) | Binary weights **0.50** / **0.80** vs neutral substitution ($N_{\mathrm{no\_data}}$) | Same mapping in current reference sources |

**Maintenance (changelog-level, non-normative):** Older PHP snapshots included an extra inertia literal (`0.45`, keyed on `noDataRatio`) that was unreachable alongside `_score_without_l1_l2_data`; it has been dropped from current reference sources. Normative L4 behaviour remains the binary rule in **Methodology**. Commit-level audits SHOULD diff `redacted internal source artifact` against the pinned hash under **Discussion → Reproducibility**.

**Labelling rule:** report implementation comparisons as **“PROD parity”** or **“reference-module parity”**; report scientific methodology claims **only** against **Methodology** + **Appendix A/B** as stated here.

