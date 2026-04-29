---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/naciro/ja.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/naciro/"
nf_markdown_lang_file: "ja"
---
# Naciro Engine

## 機能

**Naciro Engine** は **SoftwareApplication** としてモデル化される推論層で、検証済みシグナルを **正規化された中間状態** と **スカラー出力（主に NFSI）** に変換します。一般対話ボットではなく、**宣言されたパイプライン**（属性 `pipeline`、**VVR**）として説明されます。

## 論理的データフロー

取り込み → 集約・正規化 → バイアス補正／予測調整 → NFSI 出力。更新周期は `update_cycle` に従います。

## 範囲

本ページはランタイムテレメトリを保持しません。公開方法論と VVR を参照してください。

## 参照

- [Naciro AI](https://nationfiles.com/en/company/naciro-ai/)
- [NFSI 方法論](https://nationfiles.com/en/company/nfsi/)
- [VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
