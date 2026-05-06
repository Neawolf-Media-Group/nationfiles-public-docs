## Audit invariants (machine-checkable)

This file defines the invariants that must hold for any recompute run over a deposited snapshot (fixture or real snapshot). A recompute is **audit-pass** only if all invariants hold.

### Value bounds

- **I1 (L1 row score bounds)**: for all rows, `0 <= l1_row_score <= 100`
- **I2 (L2 connector-day score bounds)**: for all connector-days, `0 <= l2_day_score <= 100`
- **I3 (L3 raw score bounds)**: for all countries/days, `1 <= l3_score <= 100`
- **I4 (L4 published NFSI bounds)**: for all countries/days, `1 <= nfsi_today <= 100`

### Determinism and ordering

- **I5 (stable ordering)**: any aggregation over rows must sort by `event_timestamp` ascending before applying sequence-sensitive operations.
- **I6 (no hidden randomness)**: recompute must not call non-deterministic sources (system time, RNG) for values used in L1–L4.

### Crash-mode gate

- **I7 (crash-mode predicate)**: crash mode is triggered iff `has_security_rows && minSec < 25` where `minSec` is computed over **real** group-100 security rows only (placeholders excluded).
- **I8 (crash-mode override)**: if crash mode triggers then `nfsi_today == l3_score` (no inertia smoothing).

### Daily change cap

- **I9 (cap applies post-inertia)**: if not crash mode then the published daily change is capped:
  `abs(nfsi_today - nfsi_yesterday) <= 3`

### Missingness policy (authoritative)

- **I10 (L3 missing substitution)**: unless explicitly enumerated in a deposited exception list, any missing connector at L3 uses `neutral = 50`.

