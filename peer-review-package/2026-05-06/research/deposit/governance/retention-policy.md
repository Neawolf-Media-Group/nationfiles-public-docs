## Retention policy (template)

This is a **template** retention policy for an NFSI deployment that aims to be auditable under OSF/IEEE/NASA/NIST expectations.

### Scope

- **In scope**: raw connector fetch artefacts (where licensing permits), intermediate Layer 1-4 values, recompute logs, commit hashes, and evidence manifests.
- **Out of scope**: personal data (should not be present), provider secrets, API keys, and proprietary data disallowed from archiving.

### Retention durations

- **Audit log (intermediates, hashes, commit)**: 24 months minimum.
- **Evidence manifests (sha256, commit pointers)**: 60 months minimum.
- **Fixture / deposited snapshots used for publications**: indefinite (as required by the publication/deposit).

### Integrity controls

- Append-only storage (WORM if available).
- Daily hash chain or signed digest of the previous day’s audit log partition.
- Documented access controls and change management.

### Deletion policy

- Deletions are permitted only when required by licensing or security policy and must be recorded as an auditable event (who/when/why/what).

