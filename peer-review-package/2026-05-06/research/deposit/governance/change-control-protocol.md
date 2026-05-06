## NFSI change control protocol (constants and model logic)

**Scope:** Any change to constants, weighting logic, crash predicates, or L1-L4 transformation order.

### 1) Roles and approvals

- **Author (A1):** proposes the change and provides rationale.
- **Method reviewer (A2):** validates mathematical correctness and reproducibility.
- **Ops reviewer (E1):** validates operational impact and rollback readiness.
- **Approver (M1):** final sign-off for production rollout.

No change is allowed without approvals from A2 and M1.

### 2) Required artefacts per change

- Pull request with:
  - clear motivation and expected impact,
  - before/after constant table,
  - affected formulas and pseudocode references.
- Recompute evidence:
  - fixture recompute output diff,
  - invariant checks (PASS),
  - sensitivity delta (at minimum ±10% OAT for affected connectors/constants).
- Backout plan:
  - exact revert commit or feature flag path,
  - maximum rollback execution time.

### 3) Mandatory gates

- Unit tests pass (`research/deposit/tests/run_tests.sh`).
- Invariants pass (`research/deposit/recompute/check_invariants_fixture.php`).
- Documentation updated:
  - manuscript section 4,
  - `research/deposit/const_table.md`,
  - `research/deposit/annex_formulas.tex/.json`.

### 4) Rollout and monitoring

- Deploy in two phases: shadow (no publication impact) then active.
- Monitor:
  - distribution shift in `nfsi_today`,
  - crash-mode trigger frequency,
  - no-data ratio and connector uptime.
- Trigger immediate rollback if:
  - invariants fail,
  - unexplained median daily shift exceeds agreed threshold.

### 5) Change log format (minimum)

```text
change_id: YYYYMMDD-<slug>
commit_before: <40-hex>
commit_after: <40-hex>
owner: <name>
constants_changed:
  - name: LAYER3_WGI_PULL
    before: 0.95
    after: 0.92
reason: <brief rationale>
evidence:
  tests: PASS
  invariants: PASS
  sensitivity: attached
approval:
  A2: <name/date>
  M1: <name/date>
rollback_ref: <commit or flag>
```

