---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/nfsi/ja.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/nfsi/"
nf_markdown_lang_file: "ja"
---
# NationFiles Stability Index (NFSI)

## 定義

**NFSI** は **0–100 の定量指標**であり、地政学的安定性／文書化されたリスク姿勢を集約します。型 **`Dataset`** は、散文の国別記事ではなく **時系列型メトリクス** であることを示します。

## バンド

`score_bands` に従い A（81–100）～ E（0–20）。

## パイプライン

指標層 → 集約 → 加重最終スコア → 慣性スムージング。数値詳細は **VVR** を参照。

## カバレッジ／更新

195 か国、15 分周期（`update_cycle`）。`global_current` スロットはサーバ側で世界集計を解決できる場合に表示されます。

## ライセンス／DOI

CC BY‑ND 4.0、DOI **10.5281/zenodo.19758890**。

## 参照

- [NFSI 方法論](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
- [DOI](https://doi.org/10.5281/zenodo.19758890)
