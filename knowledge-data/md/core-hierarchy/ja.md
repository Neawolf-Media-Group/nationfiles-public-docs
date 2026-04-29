---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/core-hierarchy/ja.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/core-hierarchy/"
nf_markdown_lang_file: "ja"
---
# コア階層

## 目的

**コア階層**は NationFiles の **組織構造とシステムスタック** を正規化した見取り図です。HTML UI・エクスポート（JSON / JSON‑LD）・任意の Markdown で用語を揃えます。マーケティング用の組織図ではなく、**引用可能な鎖**（法的発行主体から定量メトリクスまで）です。

## レベル一覧

| レベル | エンティティ（Knowledge ID） | 役割（短縮） |
|--------|------------------------------|--------------|
| 1 | `neawolf-media-group` | 運営組織 / 発行者 |
| 2 | `nationfiles` | システム / サイト |
| 3 | `naciro` | 推論・ロジック・エンジン |
| 4a | `lpu-architecture` | ハードウェア／実行層 |
| 4b | `nfsi` | 統計スコア（0–100）、データセット |

**エンジンロジック**と**出力メトリクス**を分離する場合、同一レベルに複数ノードが並びます。

## ノードを分ける理由

- **責任分界**：法人・プラットフォーム・ソフトウェア・指標。
- **引用**：ソースが異なる（Legal Notice、`llms.txt`、方法論、VVR、DOI）。
- **技術的正確性**：NFSI はパイプライン生成物であり、階層は **役割** を表します。

## グラフの辺

`relations`（`relatedTo` 等）で接続。JSON‑LD では Schema.org に応じて `hasPart` / `isPartOf` / `makesOffer` に写像します。

## 機械可読性

安定 ID、`generated_at_utc`、正規 URL、構造化属性。`hierarchy.levels` は「階層」ブロックと JSON‑LD の `ItemList` に利用されます。

## 参照

- [llms.txt](https://nationfiles.com/llms.txt)
- [Legal Notice](https://nationfiles.com/en/legal/legal-notice/)
- [NFSI 方法論](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
