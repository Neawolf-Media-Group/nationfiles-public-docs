## Forecast evaluation (what is computable now)

### What we can compute immediately from public exports

Using `?export=json&chart=country_nfsi_30d` we can compute **persistence-baseline** errors for horizons $h=1..7$:

- $\hat{y}_{t+h} = y_t$ (persistence)
- report RMSE/MAE per horizon

Script:

- `research/deposit/recompute/forecast_skill_persistence.php`

### What we cannot compute without archived forecasts

The public export endpoint provides **future predictions**, but does not provide historical forecast archives ("what was predicted on day t for t+h") in a way that allows immediate retrospective scoring.

To compute forecast skill for the VAR component (RMSE/MAE vs baseline), the deposit must include:

- a daily archive of model forecasts with issue date (forecast_date) and horizons, plus the realised NFSI later.

Recommended practice:

- run `fetch_public_exports.php` daily and store the export JSON + SHA-256 in an append-only location,
- after $h$ days, compare archived prediction to realised timeline to compute RMSE/MAE.

