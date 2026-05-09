# Anhänge

<div class="nf-stepper"><div class="nf-title">Grafik 301: Provenance Ladder</div><div class="nf-step"><strong>1</strong>: Quelle</div><div class="nf-step"><strong>2</strong>: Rohzeile</div><div class="nf-step"><strong>3</strong>: Normalisierung</div><div class="nf-step"><strong>4</strong>: Aggregation</div><div class="nf-step"><strong>5</strong>: Komposition</div><div class="nf-step"><strong>6</strong>: Veröffentlichung</div><div class="nf-step"><strong>7</strong>: Audit Loop</div></div>


Dieses Buch ist so geschrieben, dass es lesbar ist. Es ist aber auch so geschrieben, dass es **zitierfähig** ist.

Ziel dieser Anhänge ist daher nicht "mehr Prosa", sondern eine kompakte, audit-orientierte Referenzschicht, mit der Expertinnen und Experten Terminologie, Provenienz-Posture, Governance-Logik und die Grenze zwischen öffentlicher Oberfläche und interner Operation prüfen können.

Wo sinnvoll, sind die Anhänge an die in diesem Repository dokumentierten Artefakte angelehnt (NFSI‑VVR, Governance‑Artefakte, kanonische Knowledge‑Entitäten).







<div style="page-break-after: always;"></div>

## Anhang A: Glossar der neuen Intelligenz

### A.1 NationFiles

**Definition (funktional)**: **NationFiles** ist die öffentlich sichtbare Web‑ und Datenplattform, die maschinenlesbare Länder‑Artefakte und Stabilitätsoberflächen publiziert (Dashboards, Maps, Index‑Views, Briefings, Legal‑ und Methodikseiten). NationFiles ist die Präsentations‑ und Distributionsschicht.

**Was NationFiles nicht ist**:
- keine einzelne Bulk‑API für unbeschränktes Scraping,
- kein Ersatz für souveräne amtliche Statistiken, UN‑Grenzkonventionen oder nationale Statistikämter.

**Kerneigenschaften**:
- Mehrsprachige Publikation über feste UI‑Locales (DE/EN/FR/ES/PT/AR/JA).
- Kanonische HTML‑Seiten für Zitation; Exporte existieren, ersetzen aber keine Primärzitate.
- "Truth on schedule": definierte Cadence mit Zeitstempeln.

### A.2 Naciro (die Engine)

**Definition (funktional)**: **Naciro** ist die Analytics‑Engine innerhalb von NationFiles. Sie ingestiert Signale und standardisierte Länderprofile, wendet dokumentierte Transformationen an, berechnet Stabilitätsoutputs (inkl. NFSI) und emittiert begrenzte Predictive‑Outlooks dort, wo Produktoberflächen dies ausweisen.

**Kerneigenschaften**:
- Deterministische Posture: gleiche Inputs -> gleiche Outputs (Auditierbarkeitsanforderung).
- Layered Processing: Ingestion/Normalisierung/Aggregation/Gewichtung/Stabilisierung.
- Governance‑gebundener Betrieb: Change Control, Invariants, Audit Trail, Retention.

### A.3 NFSI (NationFiles Stability Index)

**Definition**: **NFSI** ist der Headline‑Score **0-100** für operativen Stabilitäts‑/Risikokontext pro Land (und ggf. Weltaggregation), berechnet durch Naciro und publiziert über NationFiles. Höhere Werte bedeuten höhere Stabilität in der dokumentierten Semantik.

**Bänder (human‑lesbar)**:
- A (81-100), B (61-80), C (41-60), D (21-40), E (0-20)

**Kritische Einordnung**: Der NFSI ist ein **dokumentiertes Komposit**. Er ist nicht "News‑Sentiment", nicht "Konfliktcount" und nicht "FX‑Volatilität". Er aggregiert mehrere Familien und nutzt Glättung/Inertia sowie Crisis‑Gating, um bei hoher Cadence lesbar zu bleiben.

### A.4 Predictive Layer (24h / 7d)

**Definition**: Die Predictive Layer ist ein begrenzter Decision‑Support‑Output (typischerweise **24 Stunden bis 7 Tage**), abgeleitet aus jüngster Historie und gekoppelten Zeitreihen unter expliziten Randbedingungen. Sie ist constrained extrapolation, nicht Langfrist‑Prophezeiung.

**Non‑Claims**:
- keine Garantie für Ereignisse,
- kein Orakel für langfristiges geopolitisches Schicksal.

**Audit‑Posture**:
- benannte Methode (z. B. VAR‑basiert 7d), fixe Parameter in der Implementierung, deterministischer Output,
- Outputs sind mit Outcomes vergleichbar (Audit‑Loop).

### A.5 Global Re‑Evaluation (GRF)

**Definition**: Global Re‑Evaluation ist der operative Zyklus, der die Welt in einem wiederkehrenden Takt neu interpretiert (z. B. täglicher Baseline‑Refresh). Er synchronisiert heterogene Signale in konsistente Zeitfenster und erzwingt systematische Recompute‑Disziplin statt selektiver Updates.

**Zweck**:
- Recency‑Bias und selektiven Fokus reduzieren,
- Second‑Order‑Kaskaden über globale Recompute‑Disziplin sichtbar machen,
- versionierbare "Weltzustände" für Audit und Reproduzierbarkeit erzeugen.







<div style="page-break-after: always;"></div>

## Anhang B: Herkunft der Signale

### B.1 Provenienz‑Prinzipien

Eine Plattform, die Stabilitätswerte publiziert, braucht eine klare Provenienz‑Posture. Mindestprinzipien:

- **Deklarierte Input‑Familien**: Leser müssen die Klassen der Inputs kennen.
- **Lizenzbewusstsein**: Quellen können öffentlich, lizenziert oder beschränkt sein; Publikation muss Terms respektieren.
- **Update‑Cadence‑Disclosure**: wöchentlich aktualisierte Quellen sind anders zu lesen als mehrmals täglich aktualisierte.
- **Traceability‑Dichte**: Pipeline muss von Rohzeile -> Row‑Score -> Connector‑Day -> Headline rekonstruierbar sein.

### B.2 Source‑Taxonomie (High‑Level)

Eine brauchbare Taxonomie trennt "was die Daten sind" von "was sie bedeuten":

1. **Ökonomische Anker** (Makro, Preise, Arbeit, Reserven, FX‑Stränge)
   - Zweck: langsame Fragilität und Marktstresskanäle abbilden.
2. **Sicherheit & kinetische Events** (Konfliktlogs, Gewaltindikatoren, Katastrophen, Reisewarnungen)
   - Zweck: akutes physisches Risiko und Crisis‑Gating‑Signale.
3. **Governance & Institutionen** (Rechtsstaat, Korruptionskontrolle, Accountability‑Komposite)
   - Zweck: Resilienz, Fragilität und Erholungskapazität.
4. **Narrativ-/Medien‑Signale** (Tonalität, Event‑Risk, sentimenttragende Stränge)
   - Zweck: Wahrnehmungsdynamik und Early‑Escalation‑Signaturen, als ein Kanal unter mehreren.
5. **Digital-/Netz‑Signale** (Traffic‑Anomalien, Abuse‑Signale, Botnet‑Indikatoren)
   - Zweck: Cyber‑Stress‑Proxies und Infrastruktur‑Turbulenz.
6. **Referenz‑Scaffolding** (Geografie, Grenzen, Ports, Airports, Identifiers)
   - Zweck: Mapping/Normalisierung; typischerweise kein direkter Stabilitäts‑Impact.

### B.3 OSINT vs kuratierte Daten: warum beides nötig ist

OSINT liefert Breite und Aktualität. Kuratierte Datensätze liefern Stabilität und Vergleichbarkeit. Ein glaubwürdiges System braucht beides:

- OSINT erkennt frühe Shifts, ist aber anfällig für Bias und Manipulation.
- Kuratierte Daten sind langsamer, wirken aber als Anker gegen Narrativstürme.

Provenienz ist daher nicht nur eine Quellliste, sondern Designphilosophie: Diversität der Familien reduziert Single‑Channel‑Capture.

### B.4 Missingness und die Ethik der Provenienz

Provenienz muss auch Missing‑Handling enthalten:

- Missingness ist nicht Unschuld; Abwesenheit von Beobachtung ist nicht Abwesenheit von Risiko.
- Neutral‑Substitution (z. B. definierter neutraler Score) und Recovery‑Logik müssen deklariert sein.
- Wenn Lizenzen das Archivieren von Rohartefakten verbieten, müssen Hash‑Evidence‑Manifeste und Intermediate‑Logs innerhalb erlaubter Grenzen erhalten bleiben.







<div style="page-break-after: always;"></div>

## Anhang C: Governance & Prüfpfade

### C.1 Warum Governance Architektur ist

Ohne Governance driftet eine Stabilitäts‑Engine:

- Gewichte verschieben sich still,
- Crash‑Gates ändern Verhalten,
- Quellen degradieren oder ändern Schema,
- Plattform wird inkonsistent über Seiten und Sprachen hinweg.

Governance ist daher kein Overhead. Governance ist das Engineering‑Layer, das Neutralität und Credibility stabil hält.

### C.2 Change Control (Modelllogik und Konstanten)

Ein minimales Change‑Control‑Protokoll umfasst:

- definierte Rollen (Author, Method Reviewer, Ops Reviewer, Approver),
- required artefacts (before/after constants, betroffene Formeln, Evidence),
- mandatory gates (Tests, Invariant Checks, Sensitivity‑Deltas),
- Rollout‑Disziplin (Shadow -> Active, Monitoring, Rollback‑Thresholds),
- strukturierte Changelog‑Formate mit Commit‑Referenzen und Approvals.

So werden Modelländerungen auditable Events statt discretionary Edits.

### C.3 Maschinenprüfbare Invarianten (Beispiele)

Invarianten sind das Immunsystem der Auditierbarkeit:

- **Bounds**: L1/L2/L3/L4 bleiben in deklarierten Bereichen.
- **Determinismus**: keine versteckte Zufälligkeit; stabile Sortierung.
- **Crash‑Mode‑Korrektheit**: bei Crash‑Predicate gilt published == raw (keine Glättung).
- **Daily‑Cap‑Korrektheit**: außerhalb Crash‑Mode gilt Tagesänderungs‑Cap.
- **Missingness‑Policy**: fehlende Connectoren werden konsistent neutral ersetzt, außer explizit enumeriert.

### C.4 Audit Trail und Retention‑Posture

Auditierbarkeit braucht Gedächtnis:

- Retention von Intermediates/Logs für Mindestlaufzeiten,
- Retention von Evidence‑Manifests und Commit‑Pointern länger,
- indefinite Retention deposited Fixtures (wo erlaubt),
- Deletions nur mit auditable Event (wer/wann/warum/was).

### C.5 Independence und Neutralität by design

Neutralität wird geschützt durch:

- deterministische Berechnung (keine manuellen Overrides),
- transparente Methodik‑Posture ("no invented values"),
- governte Change Control,
- explizite Public/Private‑Grenzen (siehe Anhang D).







<div style="page-break-after: always;"></div>

## Anhang D: Öffentliche Schnittstellen

### D.1 Warum die Grenze zählt

Ein Intelligence‑System muss zugleich:

- offen genug sein, um Vertrauen zu verdienen,
- und begrenzt genug, um sicher, rechtlich und operativ stabil zu bleiben.

Exporte sind daher nicht "alles". Exporte sind **der auditierte Subset**, der für Public Consumption, Grounding oder Integration unter den deklarierten Policies geeignet ist.

### D.2 Öffentliche Oberflächen (typisch)

Öffentlich konsumierbar sind typischerweise:

- kanonische HTML‑Seiten (Country Hubs, Index Pages, Legal/Methodik),
- stabilisierte Headline‑Metriken (NFSI‑Werte, Bänder, Zeitreihen),
- strukturierte Exporte, wo explizit vorgesehen (JSON‑Snapshots, Badges, RSS/Atom/JSON für bestimmte Bereiche).

Öffentliche Oberflächen sollten Zeitstempel und Metadaten enthalten, damit Zitation und Interpretation korrekt bleiben.

### D.3 Interne Oberflächen (typisch)

Aus Safety-/Lizenz-/Abuse‑Gründen bleiben oft intern:

- Provider‑Secrets, Keys, Credentials,
- lizenzrestriktierte Raw‑Fetch‑Artefakte (wenn Redistribution verboten),
- Anti‑Abuse‑Heuristiken (Rate Limits, Detection Logic),
- Infrastruktur‑Details (Caching‑Keys, interne Endpunkte).

### D.4 Minimaler Export‑"Contract"

Für Integration und Reproduzierbarkeit sollte ein minimaler Export‑Contract definieren:

- Identifier‑Konventionen (ISO‑Codes, Locale‑Codes),
- Timestamps (UTC‑Policy),
- Version‑Marker (Model Version, Bundle Snapshot),
- konsistente Schemas für Headline und Zeitreihen.

### D.5 Exporte als Trust‑Feature

Exporte sind nicht nur Developer‑Convenience. Sie sind Trust‑Architektur:

- ermöglichen unabhängige Verifikation publizierter Werte,
- unterstützen Grounding und Zitation,
- schaffen eine stabile Schnittstelle zwischen Engine und Welt.
