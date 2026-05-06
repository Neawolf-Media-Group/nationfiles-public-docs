## Event-style backtest (data-driven, DB)

- Since: `2026-02-14`
- Selection rule: top-5 largest absolute daily deltas from `nfsi_country`.
- Window: 7 days pre and 7 days post.

| iso2 | date | score_prev | score | delta | pre_mean | pre_sd | post_min | post_max | z_vs_pre |
| :-- | :-- | --: | --: | --: | --: | --: | --: | --: | --: |
| AQ | 2026-02-27 | 7.84 | 26.12 | 18.2800 | 12.5014 | 6.6340 | 8.7700 | 26.1200 | 2.0528 |
| RE | 2026-02-18 | 71.25 | 60.60 | -10.6500 | 63.7050 | 5.0536 | 60.6000 | 62.8400 | -0.6144 |
| MK | 2026-04-14 | 54.79 | 65.30 | 10.5100 | 57.7171 | 3.8608 | 56.5900 | 66.7000 | 1.9641 |
| NU | 2026-03-31 | 47.56 | 57.44 | 9.8800 | 47.1843 | 1.2596 | 46.9300 | 57.4400 | 8.1421 |
| ME | 2026-03-17 | 56.58 | 66.44 | 9.8600 | 61.2400 | 3.3182 | 59.5200 | 66.4400 | 1.5671 |

Files:
- `backtest_events.csv.txt`
- `backtest_events.md`

