---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/lpu-architecture/ja.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/lpu-architecture/"
nf_markdown_lang_file: "ja"
---
# LPU Architecture

## 役割

**LPU Architecture** は Naciro Engine の下位にある **専用推論ハードウェア層**を指します。**決定性と低遅延**が NFSI 系ワークロードへのシグナル経路に課されます。型 **`CreativeWork`** と DOI は、ダイナミックな SKU 表ではなく **アーキテクチャ記述**として位置づけます。

## 遅延指標

`inference_latency`: **< 50 ms**（文書化された目標値。本ページはライブ計測ではありません）。

## DOI

**10.5281/zenodo.19774594** — 技術的詳細の一次情報は Zenodo 記録を参照してください。

## 文脈

エンジン `naciro`、指標 `nfsi`、公開経路 `nationfiles`。

## 参照

- [Naciro AI](https://nationfiles.com/en/company/naciro-ai/)
- [NFSI 方法論](https://nationfiles.com/en/company/nfsi/)
- [DOI](https://doi.org/10.5281/zenodo.19774594)
