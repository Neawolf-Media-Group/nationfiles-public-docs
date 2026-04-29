---
nf_distribution_schema: "nf-distribution-1.0"
nf_dataset: "NationFiles Knowledge Data (NFKG)"
nf_copyright_holder: "Neawolf Media Group"
nf_copyright_years: "2025–2026"
nf_license_url: "https://nationfiles.com/en/ai-guidelines/"
nf_license_spdx: "LicenseRef-NationFiles-AI-Guidelines"
nf_llms_txt: "https://nationfiles.com/llms.txt"
nf_ai_licensing_email: "ai-questions@nationfiles.com"
nf_repository_relative_path: "knowledge-data/md/core-hierarchy/ar.md"
nf_canonical_html_url: "https://nationfiles.com/en/knowledge/entity/core-hierarchy/"
nf_markdown_lang_file: "ar"
---
# الهيكلية الأساسية

## الغرض

**الهيكلية الأساسية** هي التمثيل المرجعي لِ**هيكل المؤسسة والنظام** في NationFiles. توحّد المفردات بين واجهة HTML والتصديرات (JSON و JSON-LD) وملفات Markdown الاختيارية. الهدف ليس تسلسلًا ترويجيًا بل **سلسلة قابلة للاقتباس**: من الجهة الناشرة قانونيًا حتى المقياس الكمي.

## المستويات

| المستوى | الكيان (معرّف Knowledge) | الدور (مختصر) |
|---------|---------------------------|----------------|
| 1 | `neawolf-media-group` | مؤسسة / ناشر |
| 2 | `nationfiles` | النظام / الموقع |
| 3 | `naciro` | محرك الاستدلال والمنطق |
| 4أ | `lpu-architecture` | طبقة عتاد / تنفيذ |
| 4ب | `nfsi` | مؤشر إحصائي (0–100)، مجموعة بيانات |

قد تتقاسم عدة كيانات المستوى نفسه عند فصل **محرك المنطق** و**مخرجات المقياس**.

## فصل العقد

- **المسؤولية**: كيان قانوني، منصة، برنامج، مقياس.
- **الاقتباس**: مصادر متميزة (الإشعار القانوني، `llms.txt`، المنهجية، VVR، DOI).
- **الدقة التقنية**: مخرجات NFSI تمر عبر مسار معالجة؛ الهيكل يعكس **الأدوار** لا شجرة بنية فردية بالضرورة.

## الروابط

تربط `relations` (مثل `relatedTo` وروابط نطاقية) الكيانات؛ يعاد إسقاطها في JSON-LD إلى `hasPart` / `isPartOf` أو `makesOffer` عند التوافق مع Schema.org.

## القراءة الآلية

معرّفات ثابتة، `generated_at_utc`، عناوين URL موحّدة، سمات مهيكلة؛ الحقل `hierarchy.levels` يغذي عرض «المستويات» و`ItemList` في JSON-LD.

## مراجع

- [llms.txt](https://nationfiles.com/llms.txt)
- [Legal Notice](https://nationfiles.com/en/legal/legal-notice/)
- [منهجية NFSI](https://nationfiles.com/en/company/nfsi/)
- [تقرير VVR](https://nationfiles.com/en/legal/validation-and-verification-report/)
