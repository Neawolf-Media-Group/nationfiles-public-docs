# Appendices

<div class="nf-stepper"><div class="nf-title">Figure 401: Provenance Ladder</div><div class="nf-step"><strong>1</strong>: Source</div><div class="nf-step"><strong>2</strong>: Raw row</div><div class="nf-step"><strong>3</strong>: Normalization</div><div class="nf-step"><strong>4</strong>: Aggregation</div><div class="nf-step"><strong>5</strong>: Composition</div><div class="nf-step"><strong>6</strong>: Publication</div><div class="nf-step"><strong>7</strong>: Audit loop</div></div>


This book is written to be readable. But it is also written to be **citable**.

The purpose of these appendices is therefore not to "add more prose," but to provide a compact, audit-oriented reference layer that an expert reader can use to verify terminology, provenance posture, governance logic, and the boundary between public surfaces and internal operations.

Where relevant, the appendices align with the system's documented properties in this repository (NFSI VVR, governance artefacts, and canonical Knowledge entities).







<div style="page-break-after: always;"></div>

## Appendix A: Glossary of the New Intelligence

### A.1 NationFiles

**Definition (functional)**: **NationFiles** is the public-facing web and data platform that publishes machine-readable country intelligence artefacts and stability surfaces (dashboards, maps, index views, briefings, legal and methodology pages). It is the presentation and distribution layer.

**What NationFiles is not**:
- Not a single bulk-export API for unrestricted scraping.
- Not a replacement for official sovereign accounts, UN border conventions, or national statistical offices.

**Core properties**:
- Multilingual publication across fixed UI locales (DE/EN/FR/ES/PT/AR/JA).
- Canonical HTML pages intended for citation; exports exist but do not replace primary citations.
- "Truth on schedule": stable publication cadence with timestamps.

### A.2 Naciro (the engine)

**Definition (functional)**: **Naciro** is the analytics engine within NationFiles. It ingests signals and standardized country profiles, applies documented transformations, computes stability outputs (including NFSI), and emits bounded predictive outlooks where product surfaces disclose them.

**Core properties**:
- Deterministic posture: same inputs -> same outputs (auditability requirement).
- Layered processing: ingestion/normalization/aggregation/weighting/stabilization.
- Governance-bound operation: change control, invariants, audit trail, retention.

### A.3 NFSI (NationFiles Stability Index)

**Definition**: **NFSI** is the headline **0-100** operational stability/risk score per country (and world aggregates where present), computed by Naciro and surfaced via NationFiles. Higher values represent higher stability in the index's documented semantics.

**Bands (human-readable)**:
- A (81-100), B (61-80), C (41-60), D (21-40), E (0-20)

**Critical nuance**: NFSI is a **documented composite**. It should not be misread as "news sentiment," "conflict count," or "FX volatility." It aggregates multiple families and applies smoothing/inertia and crisis gating to remain readable at high cadence.

### A.4 Predictive layer (24h / 7d)

**Definition**: The predictive layer is a bounded decision-support output (typically **24 hours to 7 days**) produced from recent history and coupled time series under explicit constraints. It is designed as constrained extrapolation, not long-horizon prophecy.

**Non-claims**:
- Not a guarantee of future events.
- Not an "oracle" for long-run geopolitical destiny.

**Audit posture**:
- Named method (e.g., VAR-based 7d), fixed parameters in implementation, deterministic output.
- Outputs must be comparable against outcomes to close an audit loop.

### A.5 Global Re‑Evaluation (GRF)

**Definition**: The Global Re‑Evaluation framework is the operational cycle by which the system re-interprets the world on a recurring schedule (e.g., daily baseline refresh). It synchronizes heterogeneous signals into a consistent temporal window and drives systematic recalculation rather than selective updates.

**Purpose**:
- Reduce recency bias and selective focus.
- Detect second-order cascades via global recompute discipline.
- Produce versionable "world states" suitable for audit and reproducibility.







<div style="page-break-after: always;"></div>

## Appendix B: Signal Provenance

### B.1 Provenance principles

A platform that publishes stability scores must provide a clear provenance posture. The minimum principles are:

- **Declared upstream families**: readers should know the classes of inputs that shape the index.
- **Licensing awareness**: sources may be public, licensed, or restricted; publication must respect upstream terms.
- **Update cadence disclosure**: a source that updates weekly cannot be interpreted like a source that updates multiple times daily.
- **Traceability density**: the pipeline should be reconstructable from raw rows -> row scores -> connector-day -> country headline.

### B.2 Source taxonomy (high-level)

NationFiles-style intelligence benefits from a taxonomy that separates "what the data is" from "what the data means." A workable taxonomy is:

1. **Economic anchors** (macro, prices, labor, reserves, FX-linked strands)
   - Purpose: capture slow-moving fragility and market stress channels.
2. **Security & kinetic events** (conflict logs, violence indicators, disasters, travel advisories)
   - Purpose: capture acute physical risk and crisis gating signals.
3. **Governance & institutions** (rule of law, corruption control, accountability composites)
   - Purpose: capture resilience, fragility, and recovery capacity.
4. **Narrative / media signals** (news tone, event risk, sentiment-bearing strands)
   - Purpose: capture perception dynamics and early escalation signatures, while treating them as one channel among many.
5. **Digital / network signals** (traffic anomalies, abuse signals, botnet indicators)
   - Purpose: capture cyber-stress proxies and infrastructure turbulence.
6. **Reference scaffolding** (geography, boundaries, ports, airports, identifiers)
   - Purpose: enable mapping and normalization; typically not direct stability impact.

### B.3 OSINT vs curated data: why both are required

OSINT provides breadth and timeliness. Curated datasets provide stability and comparability. A credible stability system blends both:

- OSINT can detect early shifts but is vulnerable to selection bias and manipulation.
- Curated datasets are slower but provide anchors that prevent narrative storms from dominating the headline.

Therefore, "provenance" is not only a list of sources; it is a design philosophy: diversified input families reduce single-channel capture.

### B.4 Missingness and the ethics of provenance

Provenance must include the handling of missing data:

- Missingness is not innocence; absence of observation is not absence of risk.
- A neutral substitution policy (e.g., a defined neutral score) and recovery logic must be declared.
- If licensing prevents archiving raw artefacts, the system should retain hashed evidence manifests and intermediate computation logs within permissible boundaries.







<div style="page-break-after: always;"></div>

## Appendix C: Governance & Audit Trails

### C.1 Why governance is part of architecture

Without governance, a stability engine will drift:

- weights shift silently,
- crash gates change behavior,
- sources degrade or change schema,
- and the platform becomes inconsistent across pages and languages.

Governance is therefore not bureaucracy. It is the engineering layer that preserves neutrality and credibility.

### C.2 Change control (model logic and constants)

A minimum change control protocol for stability logic includes:

- defined roles (author, method reviewer, ops reviewer, approver),
- required artefacts (before/after constants table, affected formulas, evidence),
- mandatory gates (tests, invariant checks, sensitivity deltas),
- rollout discipline (shadow mode -> active, monitoring, rollback thresholds),
- and a structured changelog format with commit references and approvals.

This transforms "model changes" into auditable events rather than discretionary edits.

### C.3 Machine-checkable invariants (examples)

Invariants are the immune system of auditability. Typical invariants include:

- **Value bounds**: L1, L2, L3, L4 outputs remain within declared ranges.
- **Determinism**: no hidden randomness; stable ordering where sequence matters.
- **Crash-mode correctness**: if crash predicate triggers, published == raw (no smoothing).
- **Daily cap correctness**: if not crash mode, the published daily change respects the cap.
- **Missingness policy**: missing connectors are substituted consistently (neutral score) unless explicitly enumerated.

### C.4 Audit trail and retention posture

Auditability requires memory:

- retention of intermediate values and logs for a minimum duration,
- retention of evidence manifests and commit pointers for longer durations,
- indefinite retention of deposited fixtures used for publications (where permitted),
- and deletion events recorded as auditable events when licensing or security requires removal.

### C.5 Independence and neutrality by design

Neutrality is protected by:

- deterministic computation (no manual overrides),
- transparent methodology posture ("no invented values"),
- governed change control,
- and explicit public/private boundaries (see Appendix D).







<div style="page-break-after: always;"></div>

## Appendix D: Public Interfaces

### D.1 Why the boundary matters

An intelligence system must be both:

- open enough to be trusted,
- and constrained enough to be safe, lawful, and operationally stable.

Therefore, exports are not "everything." Exports are **the audited subset** suitable for public consumption, grounding, or integration under declared policies.

### D.2 Public surfaces (typical)

Publicly consumable elements generally include:

- canonical HTML pages (country hubs, index pages, legal/methodology pages),
- stabilized headline metrics (e.g., NFSI values, bands, time series charts),
- and structured exports where explicitly provided (JSON snapshots, badges, RSS/Atom/JSON feeds for some surfaces).

Public surfaces should include timestamps and enough metadata to support citation and correct interpretation.

### D.3 Internal surfaces (typical)

Elements that often remain internal for safety, licensing, or abuse prevention:

- provider secrets, keys, and raw credentials,
- licensing-restricted raw fetch artefacts (where redistribution is disallowed),
- anti-abuse heuristics (rate limiting thresholds, detection logic),
- and operational infrastructure details (exact caching keys, internal endpoints).

### D.4 Minimal export "contract"

For integration and reproducibility, a minimal export contract should specify:

- identifier conventions (ISO codes, locale codes),
- timestamps (UTC policy),
- version markers (model version, bundle snapshot),
- and consistent schema for headline metrics and time series.

### D.5 Exports as a trust feature

Exports are not only a developer convenience. They are part of the trust architecture:

- they allow independent verification of published values,
- they support external grounding and citation,
- and they create a stable interface between the engine and the world.
