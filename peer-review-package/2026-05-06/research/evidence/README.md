## Evidence pack (reproducibility support)

This folder contains **helper scripts** to produce an audit-friendly “evidence pack” for the NFSI methodology manuscript.

### What this is

- A way to pin the **exact code state** (git commit) and produce **cryptographic hashes** (SHA-256) for key artefacts.
- A minimal, reviewer-friendly **backtest/RMSE scaffold** (optional) for the 7‑day forecast, if a validation claim is desired.

### What this is not

- It is **not** a formal certification, endorsement, or a “passed/accepted” claim by any third party.
- It does **not** contain production data dumps or any PII.

### Generate artefact hashes and a manifest

From project root:

```bash
bash research/evidence/generate-evidence.sh
```

Outputs:

- `research/evidence/evidence.json` (manifest: commit, dirty state, file list)
- `research/evidence/evidence.sha256` (SHA-256 sums for the manifest and key artefacts)

### Forecast backtest scaffold (optional)

If you want an explicit forecast validation claim, run a backtest on an exported dataset and publish results separately. See:

- `research/evidence/forecast_backtest_rmse.php`

