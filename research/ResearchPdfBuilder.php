<?php
/**
 * ResearchPdfBuilder – Klon/Ableitung von LegalPdfBuilder
 * Erzeugt PDF-Dokumente aus Markdown-Dateien im NationFiles-Layout.
 *
 * Deckblatt  → Hero-Header + Logo + Adresse + Dokumenttitel
 * Content    → Markdown → HTML (Parsedown) → nf-doc-main
 * Schlussseite → Rechtsblock OHNE QR-Code und OHNE Link zur MD-Datei
 *
 * Rendering-Pipeline (identisch mit LegalPdfBuilder):
 *   WeasyPrint › mPDF › Dompdf
 *
 * Autor: basierend auf LegalPdfBuilder (Sven Neawolf) | Stand: 2026-04
 */

declare(strict_types=1);

/* ──────────────────────────────────────────────────────────────
   Abhängigkeiten aus dem Projekt-Root
   ────────────────────────────────────────────────────────────── */
$_rpb_base = dirname(__DIR__);

require_once $_rpb_base . '/vendor/autoload.php';
require_once $_rpb_base . '/classes/helpers/LegalPdfBuilder.php';

/* ──────────────────────────────────────────────────────────────
   1. Markdown → HTML (Parsedown)
   ────────────────────────────────────────────────────────────── */

/**
 * Wandelt Markdown-Text in HTML um.
 * Nutzt Parsedown; Links bleiben als <a>-Tags erhalten.
 */
function researchMarkdownToHtml(string $markdown): string
{
    if (!class_exists(\Parsedown::class, true)) {
        // Notfall-Fallback: Leerzeilen → <p>
        $paras = preg_split('/\n{2,}/', trim($markdown));
        $out = '';
        foreach ((array) $paras as $p) {
            $p = trim((string) $p);
            if ($p !== '') {
                $out .= '<p class="nf-doc-p">' . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . '</p>' . "\n";
            }
        }
        return $out;
    }

    $parsedown = new \Parsedown();
    $parsedown->setSafeMode(false); // Links/Formatierungen erhalten
    $html = $parsedown->text($markdown);

    // Markdown-HTML-Klassen mit nf-doc-Klassen anreichern für konsistentes Styling
    $html = preg_replace('/<h1(\s[^>]*)?>/', '<h1$1 class="nf-doc-h1">', (string) $html) ?? $html;
    $html = preg_replace('/<h1>/', '<h1 class="nf-doc-h1">', $html) ?? $html;
    $html = preg_replace('/<h2(\s[^>]*)?>/', '<h2$1 class="nf-doc-h2">', (string) $html) ?? $html;
    $html = preg_replace('/<h2>/', '<h2 class="nf-doc-h2">', $html) ?? $html;
    $html = preg_replace('/<h3(\s[^>]*)?>/', '<h3$1 class="nf-doc-h3">', (string) $html) ?? $html;
    $html = preg_replace('/<h3>/', '<h3 class="nf-doc-h3">', $html) ?? $html;
    $html = preg_replace('/<p(\s[^>]*)?>/', '<p$1 class="nf-doc-p">', (string) $html) ?? $html;
    $html = preg_replace('/<p>/', '<p class="nf-doc-p">', $html) ?? $html;
    $html = preg_replace('/<ul(\s[^>]*)?>/', '<ul$1 class="nf-doc-ul">', (string) $html) ?? $html;
    $html = preg_replace('/<ul>/', '<ul class="nf-doc-ul">', $html) ?? $html;
    $html = preg_replace('/<li(\s[^>]*)?>/', '<li$1 class="nf-doc-li">', (string) $html) ?? $html;
    $html = preg_replace('/<li>/', '<li class="nf-doc-li">', $html) ?? $html;
    $html = preg_replace('/<table(\s[^>]*)?>/', '<table$1 class="nf-doc-table">', (string) $html) ?? $html;
    $html = preg_replace('/<table>/', '<table class="nf-doc-table">', $html) ?? $html;
    $html = preg_replace('/<pre(\s[^>]*)?>/', '<pre$1 class="nf-doc-pre">', (string) $html) ?? $html;
    $html = preg_replace('/<pre>/', '<pre class="nf-doc-pre">', $html) ?? $html;
    $html = preg_replace('/<blockquote(\s[^>]*)?>/', '<blockquote$1 class="nf-doc-blockquote">', (string) $html) ?? $html;
    $html = preg_replace('/<blockquote>/', '<blockquote class="nf-doc-blockquote">', $html) ?? $html;
    $html = preg_replace('/<a(\s)/', '<a class="nf-doc-link"$1', (string) $html) ?? $html;
    $html = preg_replace('/<hr(\s*\/?)>/', '<hr class="nf-doc-hr"$1>', (string) $html) ?? $html;

    return (string) $html;
}

/* ──────────────────────────────────────────────────────────────
   2. Titel aus Markdown extrahieren (erste H1, sonst Dateiname)
   ────────────────────────────────────────────────────────────── */

function researchExtractTitle(string $markdown, string $fallback = ''): string
{
    if (preg_match('/^#\s+(.+)$/m', $markdown, $m)) {
        return trim($m[1]);
    }
    return $fallback;
}

/* ──────────────────────────────────────────────────────────────
   3. Body-Wrapper
   ────────────────────────────────────────────────────────────── */

function buildResearchPdfBody(string $markdownHtml): string
{
    return "\n" . '  <section class="nf-doc-section nf-doc-research-body">' . "\n"
        . $markdownHtml . "\n"
        . '  </section>' . "\n";
}

/* ──────────────────────────────────────────────────────────────
   4. Schlussseite – OHNE QR-Code, OHNE URL
   ────────────────────────────────────────────────────────────── */

/**
 * Letzte Seite: Rechtstext + Footerzeile.
 * Absichtlich kein QR-Code, kein Link zur MD-Datei.
 */
function buildResearchPdfLastPage(string $lang, string $standDate, string $footerText): string
{
    $langKey = ($lang === 'ja' || $lang === 'JA') ? 'JA' : strtoupper($lang);
    // getClosingPageContentHtml aus LegalPdfBuilder
    $contentHtml = buildClosingPageContentHtml($langKey, $standDate);

    return '<div class="nf-doc-last-page">'
        . '<div class="nf-doc-last-page-inner">' . $contentHtml . '</div>'
        . '<div class="nf-doc-last-page-foot">'
        . '<div class="nf-doc-last-page-footer-line">' . htmlspecialchars($footerText, ENT_QUOTES, 'UTF-8') . '</div>'
        . '</div></div>';
}

/* ──────────────────────────────────────────────────────────────
   5. Zusätzliche CSS-Regeln für Research-PDFs
   ────────────────────────────────────────────────────────────── */

function getResearchPdfExtraCss(): string
{
    $n = '#1a365d';
    $border = '#e5e7eb';
    $g = '#374151';
    $gLight = '#6b7280';

    return '.nf-doc-h1{font-size:14pt;color:' . $n . ';margin:0 0 14pt;padding:0 0 8pt;border-bottom:2px solid ' . $n . ';font-weight:700;letter-spacing:0.03em}'
        . '.nf-doc-research-body h1.nf-doc-h1:first-child{display:none}' // erste H1 im Body verstecken (ist schon Deckblatt-Titel)
        . '.nf-doc-blockquote{margin:0 0 14pt;padding:10pt 14pt;border-left:4px solid ' . $n . ';background:#f8fafc;font-style:italic;color:' . $g . '}'
        . '.nf-doc-blockquote p.nf-doc-p{margin:0;font-style:italic}'
        . '.nf-doc-pre{font-family:monospace;font-size:8pt;line-height:1.4;margin:10pt 0;padding:12pt;background:#f8fafc;border:1px solid ' . $border . ';white-space:pre-wrap;word-break:break-word;page-break-inside:auto}'
        . 'code{font-family:monospace;font-size:8.5pt;background:#f3f4f6;padding:1pt 3pt}'
        . '.nf-doc-link{color:' . $n . ';text-decoration:underline}'
        . '.nf-doc-hr{border:0 none;border-top:2px solid ' . $n . ';margin:16pt 0}'
        . '.nf-doc-box{background:#f8fafc;border:1px solid ' . $border . ';padding:14pt;margin:0 0 14pt;page-break-inside:auto}'
        . '.nf-doc-strong{font-weight:700}.nf-doc-italic{font-style:italic}.nf-doc-mono{font-family:monospace;font-size:8.5pt}'
        . '.nf-doc-small{font-size:8pt;color:' . $g . '}'
        . '.nf-doc-figure{margin:20pt 0;page-break-inside:avoid;text-align:center}'
        . '.nf-doc-figure img{max-width:100%;height:auto;display:block;margin:0 auto}'
        . '.nf-doc-figcaption{font-size:8.5pt;color:' . $gLight . ';margin:6pt 0 0;text-align:center}'
        . 'table.nf-doc-table{width:100%;border-collapse:separate;border-spacing:0;font-size:8.5pt;border:1px solid ' . $border . ';margin:10pt 0}'
        . 'table.nf-doc-table th{background:' . $n . ';color:#fff;font-weight:600;padding:8pt 10pt;text-align:left}'
        . 'table.nf-doc-table td{border:1px solid ' . $border . ';padding:7pt 10pt;vertical-align:top}'
        . 'table.nf-doc-table tbody tr:nth-child(even){background:#f9fafb}'
        . '.nf-doc-ol{margin:0 0 10pt;padding-left:1.4em}'
        . 'ol.nf-doc-ol li,.nf-doc-ul li.nf-doc-li{margin:0 0 4pt}';
}

/* ──────────────────────────────────────────────────────────────
   6. Logo-Data-URI laden
   ────────────────────────────────────────────────────────────── */

function researchLoadLogoDataUri(string $base): array
{
    $result = ['uri' => null, 'width' => null, 'height' => null];

    $candidates = [
        $base . '/images/pdf-logo.jpg',
        $base . '/images/logo.png',
        $base . '/images/logo-c.png',
    ];

    foreach ($candidates as $path) {
        if (!is_file($path)) {
            continue;
        }
        $bin = @file_get_contents($path);
        if ($bin === false || $bin === '') {
            continue;
        }
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
        $result['uri'] = 'data:' . $mime . ';base64,' . base64_encode($bin);
        $size = @getimagesize($path);
        if (!empty($size[0])) {
            $result['width']  = (int) $size[0];
            $result['height'] = (int) $size[1];
        }
        break;
    }

    return $result;
}

/* ──────────────────────────────────────────────────────────────
   7. Vollständiges HTML-Dokument zusammensetzen
   ────────────────────────────────────────────────────────────── */

/**
 * Baut das vollständige HTML für ein Research-PDF.
 *
 * @param string $documentTitle  Dokumenttitel (aus MD-H1 oder Dateiname)
 * @param string $subtitle       Optionaler Untertitel (z. B. "Research Paper")
 * @param string $standDate      Datum-String (z. B. "2026-04-25")
 * @param string $lang           Sprache (de|en|fr|es|pt|ar|ja)
 * @param string $markdownHtml   Bereits in HTML konvertierter MD-Inhalt
 * @param array  $data           Optionale Overrides: _logo_data_uri, _generated_at, _pdf_meta…
 */
function buildResearchPdfHtml(
    string $documentTitle,
    string $subtitle,
    string $standDate,
    string $lang,
    string $markdownHtml,
    array $data = []
): string {
    $base = dirname(__DIR__);
    $dir  = ($lang === 'ar') ? 'rtl' : 'ltr';

    // Logo laden (falls nicht in $data übergeben)
    if (empty($data['_logo_data_uri'])) {
        $logo = researchLoadLogoDataUri($base);
        if ($logo['uri'] !== null) {
            $data['_logo_data_uri'] = $logo['uri'];
            $data['_logo_width']    = $logo['width'];
            $data['_logo_height']   = $logo['height'];
        } else {
            $data['_logo_svg_for_placeholder'] = true;
        }
    }

    $generatedAt = $data['_generated_at'] ?? date('Y-m-d H:i:s') . ' UTC';
    $data['_generated_at'] = $generatedAt;

    // Aus LegalPdfBuilder: Deckblatt-Header + Deckblatt-Seite
    $langUpper  = strtoupper($lang);
    $headerHtml = buildLegalPdfHeader('NationFiles', 'Neawolf Media Group', $documentTitle, $subtitle, $standDate, $langUpper);
    $coverHtml  = buildLegalPdfCoverPage($documentTitle, $standDate, $data, $lang, $headerHtml);

    // Body: MD-Inhalt
    $bodyHtml = buildResearchPdfBody($markdownHtml);

    // Schlussseite OHNE QR/URL
    $footerText = 'nationfiles.com | ' . $documentTitle . ' | ' . $generatedAt;
    $lastPageHtml = buildResearchPdfLastPage($lang, $standDate, $footerText);

    // CSS: Basis von getLegalPdfCss + Research-Erweiterungen
    $css = getLegalPdfCss($lang, 'validation-and-verification-report') . getResearchPdfExtraCss();

    $metaTags = buildLegalPdfMetaTags($data);

    // WeasyPrint-String-Set-Spans für Footer
    $stringSetSpans = '<span class="nf-doc-string-title">' . htmlspecialchars($documentTitle, ENT_QUOTES, 'UTF-8') . '</span>'
        . '<span class="nf-doc-string-date">' . htmlspecialchars($generatedAt, ENT_QUOTES, 'UTF-8') . '</span>';

    return '<!DOCTYPE html><html lang="' . $lang . '" dir="' . $dir . '" class="nf-doc">'
        . '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><meta charset="UTF-8"/>'
        . $metaTags
        . '<style>' . $css . '</style>'
        . '<title>' . htmlspecialchars($documentTitle, ENT_QUOTES, 'UTF-8') . ' | NationFiles Research</title>'
        . '</head>'
        . '<body class="nf-doc-body research-pdf" dir="' . $dir . '">'
        . $stringSetSpans
        . $coverHtml
        . '<div class="nf-doc-content-pages"><main class="nf-doc-main">' . $bodyHtml . '</main></div>'
        . $lastPageHtml
        . '</body></html>';
}

/* ──────────────────────────────────────────────────────────────
   8. PDF-Rendering-Pipeline (WeasyPrint > mPDF > Dompdf)
   ────────────────────────────────────────────────────────────── */

/**
 * Wandelt fertiges HTML in ein PDF-Binary um.
 * Gleiche Pipeline wie nationfiles_pdf_binary_from_html(), aber direkt inline.
 *
 * @return string PDF-Binary oder leerer String bei Fehler
 */
function researchPdfBinaryFromHtml(string $html, string $lang, array $pdfMeta = []): string
{
    $base    = dirname(__DIR__);
    $tmpBase = $base . '/tmp';
    if (!is_dir($tmpBase)) {
        @mkdir($tmpBase, 0775, true);
    }

    $pdfOutput = '';

    // ── WeasyPrint ──────────────────────────────────────────────
    $weasyPrint = trim((string) (shell_exec('which weasyprint 2>/dev/null') ?: ''));
    if ($weasyPrint === '') {
        foreach (['/usr/bin/weasyprint', '/usr/local/bin/weasyprint'] as $wp) {
            if (is_executable($wp)) {
                $weasyPrint = $wp;
                break;
            }
        }
    }
    if ($weasyPrint !== '') {
        $htmlFile = $tmpBase . '/research_pdf_' . substr(md5($html . $lang), 0, 14) . '.html';
        if (@file_put_contents($htmlFile, $html) !== false) {
            $baseUrl = 'file://' . str_replace([' ', '\\'], ['%20', '/'], $base) . '/';
            $cmd     = $weasyPrint . ' --custom-metadata -u ' . escapeshellarg($baseUrl)
                . ' ' . escapeshellarg($htmlFile) . ' - 2>/dev/null';
            if (function_exists('proc_open')) {
                $desc = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
                $proc = @proc_open($cmd, $desc, $pipes, $tmpBase, ['PATH' => '/usr/local/bin:/usr/bin']);
                if (is_resource($proc)) {
                    fclose($pipes[0]);
                    $pdfOutput = (string) stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    proc_close($proc);
                }
            }
            if (($pdfOutput === '' || substr($pdfOutput, 0, 4) !== '%PDF') && function_exists('shell_exec')) {
                $pdfOutput = (string) shell_exec($cmd);
            }
            @unlink($htmlFile);
            if ($pdfOutput === '' || substr($pdfOutput, 0, 4) !== '%PDF') {
                $pdfOutput = '';
            }
        }
    }

    // ── mPDF ────────────────────────────────────────────────────
    if ($pdfOutput === '' && class_exists(\Mpdf\Mpdf::class, true)) {
        $mpdfDir = $tmpBase . '/mpdf';
        if (!is_dir($mpdfDir)) {
            @mkdir($mpdfDir, 0775, true);
        }
        $mpdfConfig = [
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 24.1,
            'margin_right'  => 20,
            'margin_top'    => 20,
            'margin_bottom' => 20,
            'tempDir'       => $mpdfDir,
        ];
        if ($lang === 'ja' && is_file($base . '/assets/fonts/noto/ipag.ttf')) {
            $mpdfConfig['fontDir']      = [$base . '/assets/fonts/noto'];
            $mpdfConfig['fontdata']     = ['ipagothic' => ['R' => 'ipag.ttf', 'B' => 'ipag.ttf']];
            $mpdfConfig['default_font'] = 'ipagothic';
        }
        try {
            $mpdf = new \Mpdf\Mpdf($mpdfConfig);
            foreach (['title', 'author', 'subject', 'keywords', 'creator'] as $k) {
                if (!empty($pdfMeta[$k])) {
                    $method = 'Set' . ucfirst($k);
                    if (method_exists($mpdf, $method)) {
                        $mpdf->{$method}($pdfMeta[$k]);
                    }
                }
            }
            if ($lang === 'ar') {
                $mpdf->SetDirectionality('rtl');
            }
            $pageOf     = getPageOfLabels($lang);
            $footerHtml = '<div style="color:#1a365d;text-align:right;font-size:9pt;">'
                . 'nationfiles.com | Research | {PAGENO} ' . $pageOf['of'] . ' {nbpg}</div>';
            $mpdf->SetHTMLFooter($footerHtml);
            $mpdf->WriteHTML($html);
            $pdfOutput = (string) $mpdf->Output('', 'S');
        } catch (\Throwable $e) {
            $pdfOutput = '';
        }
    }

    // ── Dompdf ──────────────────────────────────────────────────
    if ($pdfOutput === '' && class_exists(\Dompdf\Dompdf::class, true)) {
        $dompdfDir = $tmpBase . '/dompdf';
        if (!is_dir($dompdfDir)) {
            @mkdir($dompdfDir, 0775, true);
        }
        $dompdfDir = realpath($dompdfDir) ?: $dompdfDir;
        $dompdf    = new \Dompdf\Dompdf([
            'isRemoteEnabled' => false,
            'fontCache'       => $dompdfDir,
            'tempDir'         => $dompdfDir,
        ]);
        $opts = $dompdf->getOptions();
        $opts->setFontDir($dompdfDir);
        $opts->setFontCache($dompdfDir);
        $opts->setTempDir($dompdfDir);
        $opts->setChroot([$base]);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->setBasePath($base);
        $dompdf->render();
        $pdfOutput = (string) $dompdf->output();
    }

    return (is_string($pdfOutput) && substr($pdfOutput, 0, 4) === '%PDF') ? $pdfOutput : '';
}

/* ──────────────────────────────────────────────────────────────
   9. Hauptfunktion: MD-Datei → PDF-Binary
   ────────────────────────────────────────────────────────────── */

/**
 * Liest eine Markdown-Datei und gibt das PDF-Binary zurück.
 *
 * @param string $mdFilePath Absoluter Pfad zur .md-Datei
 * @param string $lang       Sprache (Standard: 'en')
 * @param array  $options    Optionale Overrides (subtitle, standDate, _pdf_meta…)
 * @return string PDF-Binary oder leerer String bei Fehler
 */
function buildResearchPdfFromMdFile(string $mdFilePath, string $lang = 'en', array $options = []): string
{
    $markdown = @file_get_contents($mdFilePath);
    if ($markdown === false) {
        return '';
    }

    $fileBasename = pathinfo($mdFilePath, PATHINFO_FILENAME);
    $humanName    = str_replace(['_', '-'], ' ', $fileBasename);

    $documentTitle = researchExtractTitle($markdown, $humanName);
    $subtitle      = $options['subtitle'] ?? 'NationFiles Research';
    $standDate     = $options['standDate'] ?? date('Y-m-d');

    $markdownHtml = researchMarkdownToHtml($markdown);
    $bodyHtml     = buildResearchPdfBody($markdownHtml);

    // PDF-Meta
    $pdfMeta = array_merge([
        'title'    => $documentTitle,
        'author'   => 'NationFiles / Neawolf Media Group',
        'subject'  => 'NationFiles Research Paper',
        'keywords' => 'NationFiles, research, geopolitics, naciro',
        'creator'  => 'NationFiles ResearchPdfBuilder',
        'producer' => 'NationFiles ResearchPdfBuilder',
    ], $options['_pdf_meta'] ?? []);

    $data = array_merge($options, ['_pdf_meta' => $pdfMeta]);

    $html = buildResearchPdfHtml($documentTitle, $subtitle, $standDate, $lang, $markdownHtml, $data);

    return researchPdfBinaryFromHtml($html, $lang, $pdfMeta);
}
