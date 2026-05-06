Synthetic fixture dataset (10 countries x 30 days)

Files:

- `connector_meta.csv`
- `country_meta.csv`
- `connectors_raw.csv`
- `nfsi_prev.csv`

The dataset is **synthetic** and intended for:

- deterministic recompute smoke-tests of Layers 1-4
- unit-test fixtures (input -> intermediate -> expected NFSI)
- validation scaffolds (RMSE, sensitivity, ROC/AUC) with explicitly synthetic labels

