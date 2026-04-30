---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/data-integration-overview/ja.md"
nf_canonical_html_url: "https://nationfiles.com/ja/knowledge/entity/data-integration-overview/"
nf_markdown_lang_file: "ja"
---
# NationFiles におけるデータ統合

## この記事の位置づけ

NationFiles は多数のデータを、国情報ページ・地図・主要指標としてまとめます。**データ統合**とはここでは、その**全体像**のことです。一般向けの文章に**載せない**もの（パスワード、社内のサーバー名、バッチやジョブの細かい列挙など）も意図の一部です。公開サービスではよくある区切り方で、手順の詳細は **NFSI** やソース登録、関連レポートを読んでください。

## Naciro

**Naciro** は処理の中心です。データの整形・突合・スコアリングは、公開されている **NFSI** の扱いと**検証・ベリフィケーション・レポート**に沿います。**画面に出ている結果**から筋の良さを判断でき、内部アカウントの有無は必須ではありません。

## LPU

**LPU Architecture** のページでは、負荷が高いときでも地図や KPI の表示が大幅に乱れないように、**低遅延で推論を回す専用の層**がある理由を説明しています。データセンターの配線図の代わりにはなりません。

## リーガルノーティス

**リーガルノーティス**には、**ライブの為替・暗号・安全保障**まわりを **LPU** 経由で扱う旨が**一文**で書かれています。本稿は、そこからベンダー一覧や社内の数表を**推測しません**。

## API やエクスポートでつなぐ方へ

**公開されているスキーマ**に従い、出力されている場合は **`built_at_utc`** を使い、**正規の HTTPS ページ**へリンクしてください。**ソース登録**と **AI ガイドライン**に、引用と再利用のルールがまとまっています。

## 参考リンク

- [Knowledge：データ統合](https://nationfiles.com/ja/knowledge/entity/data-integration-overview/)
- [Naciro AI](https://nationfiles.com/ja/company/naciro-ai/)
- [NFSI](https://nationfiles.com/ja/company/nfsi/)
- [LPU Architecture](https://nationfiles.com/ja/knowledge/entity/lpu-architecture/)
- [リーガルノーティス](https://nationfiles.com/ja/legal/legal-notice/)
- [ソース登録](https://nationfiles.com/ja/legal/sources/)
- [AI ガイドライン](https://nationfiles.com/ja/ai-guidelines/)
- [Knowledge](https://nationfiles.com/ja/knowledge/)
