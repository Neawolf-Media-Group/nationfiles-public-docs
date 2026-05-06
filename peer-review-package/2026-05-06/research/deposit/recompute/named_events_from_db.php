<?php
declare(strict_types=1);

/**
 * Named event backtest (DB-backed) using real event-source tables.
 *
 * Produces 5 events (since 2026-02-14) selected from:
 * - ACLED (fatalities, event_type)
 * - UCDP GED (best fatalities)
 * - GDACS disasters (alertscore/value)
 * - USGS earthquakes (mag)
 * - News risk level (risk_level) [fallback]
 *
 * For each event, compute NFSI impact summary:
 * - nfsi_prev (t-1), nfsi_t (t), nfsi_post_min/max within [t..t+7]
 * - delta_1d = nfsi_t - nfsi_prev
 * - delta_to_post_min/max
 *
 * Usage:
 *   php research/deposit/recompute/named_events_from_db.php --out-dir=research/deposit/backtest-named --since=2026-02-14 --k=5
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

$outDir = null;
$since = '2026-02-14';
$k = 5;
$postWindow = 7;
foreach ($argv as $a) {
    if (str_starts_with($a, '--out-dir=')) $outDir = substr($a, 10);
    if (str_starts_with($a, '--since=')) $since = substr($a, 8);
    if (str_starts_with($a, '--k=')) $k = max(1, (int)substr($a, 4));
    if (str_starts_with($a, '--post-window=')) $postWindow = max(1, (int)substr($a, 14));
}
$outDir = $outDir ?: (getcwd() . '/research/deposit/backtest-named');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $since)) {
    fwrite(STDERR, "Invalid --since date.\n");
    exit(2);
}

// Connect using config/.db.php (same approach as other scripts); do not print secrets.
$cfg = getcwd() . '/config/.db.php';
if (!is_file($cfg)) {
    fwrite(STDERR, "Missing DB config: config/.db.php\n");
    exit(3);
}
$db_vars = [];
require $cfg;
if (!isset($db_vars['db_name']) && is_array($db_vars) && isset($db_vars[0]) && is_array($db_vars[0])) {
    $flat = [];
    foreach ($db_vars as $sub) $flat = array_merge($flat, (array)$sub);
    $db_vars = $flat;
}
$host   = $db_vars['db_host'] ?? $db_vars['host'] ?? 'localhost';
$user   = $db_vars['db_user'] ?? $db_vars['user'] ?? '';
$pass   = $db_vars['db_pass'] ?? $db_vars['pass'] ?? '';
$name   = $db_vars['db_name'] ?? $db_vars['name'] ?? '';
$socket = $db_vars['db_socket'] ?? $db_vars['socket'] ?? '';
if ($name === '') {
    fwrite(STDERR, "DB name missing.\n");
    exit(4);
}
$dsn = 'mysql:' . ($socket !== '' ? ('unix_socket=' . $socket . ';') : ('host=' . $host . ';')) . 'dbname=' . $name . ';charset=utf8mb4';
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
} catch (Throwable $e) {
    fwrite(STDERR, "DB connect failed.\n");
    exit(5);
}

if (!is_dir($outDir) && !mkdir($outDir, 0775, true)) {
    fwrite(STDERR, "Could not create out dir: {$outDir}\n");
    exit(6);
}

function dt(string $d): DateTimeImmutable { return new DateTimeImmutable($d); }

/**
 * Fetch top candidates per source (lightweight).
 * Each candidate: [source, iso2, date_ymd, title, url, severity_value, severity_unit]
 */
$cands = [];

// ACLED: fatalities
try {
    $st = $pdo->prepare("
        SELECT iso2, date, event_type, sub_event_type, fatalities, source, notes
        FROM datasource_acledmonthall
        WHERE date >= :since AND iso2 IS NOT NULL AND CHAR_LENGTH(TRIM(iso2))=2
        ORDER BY fatalities DESC
        LIMIT 50
    ");
    $st->execute(['since' => $since]);
    foreach ($st->fetchAll() as $r) {
        $iso2 = strtoupper(trim((string)$r['iso2']));
        $date = (string)$r['date'];
        $fat = (int)($r['fatalities'] ?? 0);
        if ($fat <= 0) continue;
        $title = 'ACLED: ' . trim((string)($r['event_type'] ?? 'event')) . ' / ' . trim((string)($r['sub_event_type'] ?? ''));
        $cands[] = [
            'source' => 'ACLED',
            'iso2' => $iso2,
            'date_ymd' => $date,
            'title' => trim($title),
            'url' => '',
            'severity_value' => $fat,
            'severity_unit' => 'fatalities'
        ];
    }
} catch (Throwable $e) { /* optional */ }

// UCDP GED: best deaths
try {
    $st = $pdo->prepare("
        SELECT iso2, date, type_of_violence, best
        FROM datasource_countriesconflictucdpged
        WHERE date >= :since AND iso2 IS NOT NULL AND CHAR_LENGTH(TRIM(iso2))=2
        ORDER BY best DESC
        LIMIT 50
    ");
    $st->execute(['since' => $since]);
    foreach ($st->fetchAll() as $r) {
        $iso2 = strtoupper(trim((string)$r['iso2']));
        $date = (string)$r['date'];
        $best = (int)($r['best'] ?? 0);
        if ($best <= 0) continue;
        $title = 'UCDP GED: type=' . (string)($r['type_of_violence'] ?? '');
        $cands[] = [
            'source' => 'UCDP_GED',
            'iso2' => $iso2,
            'date_ymd' => $date,
            'title' => trim($title),
            'url' => '',
            'severity_value' => $best,
            'severity_unit' => 'best_deaths'
        ];
    }
} catch (Throwable $e) { /* optional */ }

// GDACS disasters: alertscore/value
try {
    $st = $pdo->prepare("
        SELECT iso2, date, title, link, alertscore, value, alertlevel, eventtype
        FROM datasource_disastergdacshumnat
        WHERE date >= :since AND iso2 IS NOT NULL AND CHAR_LENGTH(TRIM(iso2))=2
        ORDER BY alertscore DESC, value DESC
        LIMIT 50
    ");
    $st->execute(['since' => $since]);
    foreach ($st->fetchAll() as $r) {
        $iso2 = strtoupper(trim((string)$r['iso2']));
        $date = (string)$r['date'];
        $a = (int)($r['alertscore'] ?? 0);
        if ($a <= 0) continue;
        $title = 'GDACS: ' . trim((string)($r['eventtype'] ?? '')) . ' ' . trim((string)($r['alertlevel'] ?? '')) . ' — ' . trim((string)($r['title'] ?? ''));
        $cands[] = [
            'source' => 'GDACS',
            'iso2' => $iso2,
            'date_ymd' => $date,
            'title' => trim($title),
            'url' => (string)($r['link'] ?? ''),
            'severity_value' => $a,
            'severity_unit' => 'alertscore'
        ];
    }
} catch (Throwable $e) { /* optional */ }

// USGS earthquakes: mag
try {
    $st = $pdo->prepare("
        SELECT iso2, date, title, place, mag
        FROM datasource_worldearthquakeusgs
        WHERE date >= :since AND iso2 IS NOT NULL AND CHAR_LENGTH(TRIM(iso2))=2
        ORDER BY mag DESC
        LIMIT 50
    ");
    $st->execute(['since' => $since]);
    foreach ($st->fetchAll() as $r) {
        $iso2 = strtoupper(trim((string)$r['iso2']));
        $date = (string)$r['date'];
        $mag = (float)($r['mag'] ?? 0);
        if ($mag <= 0) continue;
        $title = 'USGS EQ: M' . number_format($mag, 1, '.', '') . ' — ' . trim((string)($r['place'] ?? $r['title'] ?? ''));
        $cands[] = [
            'source' => 'USGS_EQ',
            'iso2' => $iso2,
            'date_ymd' => $date,
            'title' => trim($title),
            'url' => '',
            'severity_value' => $mag,
            'severity_unit' => 'magnitude'
        ];
    }
} catch (Throwable $e) { /* optional */ }

// Country news risk level: risk_level (fallback)
try {
    $st = $pdo->prepare("
        SELECT iso2, nf_date AS date, risk_level
        FROM datasource_countrynewsrisklevel
        WHERE nf_date >= :since AND iso2 IS NOT NULL AND CHAR_LENGTH(TRIM(iso2))=2
        ORDER BY risk_level DESC
        LIMIT 50
    ");
    $st->execute(['since' => $since]);
    foreach ($st->fetchAll() as $r) {
        $iso2 = strtoupper(trim((string)$r['iso2']));
        $date = (string)$r['date'];
        $risk = (float)($r['risk_level'] ?? 0);
        if ($risk <= 0) continue;
        $title = 'Country news risk level';
        $cands[] = [
            'source' => 'NEWS_RISK',
            'iso2' => $iso2,
            'date_ymd' => $date,
            'title' => $title,
            'url' => '',
            'severity_value' => $risk,
            'severity_unit' => 'risk_level'
        ];
    }
} catch (Throwable $e) { /* optional */ }

if (!$cands) {
    fwrite(STDERR, "No candidates found.\n");
    exit(7);
}

// Compute NFSI impact per candidate and select top-k by combined score:
// score = abs(delta_1d) + 0.1*severity_value (scaled to keep delta dominant)
$stmtPrev = $pdo->prepare("SELECT score FROM nfsi_country WHERE iso2 = :iso2 AND date = :d LIMIT 1");
$stmtWin = $pdo->prepare("
    SELECT MIN(score) AS min_s, MAX(score) AS max_s
    FROM nfsi_country
    WHERE iso2 = :iso2 AND date BETWEEN :d0 AND :d1 AND score IS NOT NULL
");

$enriched = [];
foreach ($cands as $c) {
    $iso2 = $c['iso2'];
    $t = $c['date_ymd'];
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $t)) continue;
    $tPrev = dt($t)->sub(new DateInterval('P1D'))->format('Y-m-d');
    $tPostEnd = dt($t)->add(new DateInterval('P' . $postWindow . 'D'))->format('Y-m-d');

    $stmtPrev->execute(['iso2' => $iso2, 'd' => $tPrev]);
    $prev = $stmtPrev->fetchColumn();
    $stmtPrev->execute(['iso2' => $iso2, 'd' => $t]);
    $cur = $stmtPrev->fetchColumn();
    if ($prev === false || $cur === false) continue;
    $prevF = (float)$prev;
    $curF = (float)$cur;
    if ($prevF <= 0 || $curF <= 0) continue;

    $stmtWin->execute(['iso2' => $iso2, 'd0' => $t, 'd1' => $tPostEnd]);
    $mm = $stmtWin->fetch() ?: null;
    $postMin = $mm ? (float)($mm['min_s'] ?? $curF) : $curF;
    $postMax = $mm ? (float)($mm['max_s'] ?? $curF) : $curF;

    $delta1d = $curF - $prevF;
    $impactScore = abs($delta1d) + 0.1 * (float)$c['severity_value'];

    $enriched[] = $c + [
        'nfsi_prev' => $prevF,
        'nfsi_t' => $curF,
        'delta_1d' => $delta1d,
        'post_min' => $postMin,
        'post_max' => $postMax,
        'post_window_days' => $postWindow,
        'rank_score' => $impactScore,
    ];
}

if (!$enriched) {
    fwrite(STDERR, "No enriched events with NFSI match.\n");
    exit(8);
}

usort($enriched, fn($a, $b) => ($b['rank_score'] <=> $a['rank_score']));

// Unique ISO2 + source to avoid duplicates.
$picked = [];
$seenIso = [];
foreach ($enriched as $e) {
    $key = $e['iso2'] . '|' . $e['source'];
    if (isset($seenIso[$key])) continue;
    $seenIso[$key] = true;
    $picked[] = $e;
    if (count($picked) >= $k) break;
}

// Write CSV-like
$csvPath = rtrim($outDir, '/') . '/named_events_backtest.csv.txt';
$fh = fopen($csvPath, 'wb');
fputcsv($fh, [
    'source','iso2','date_ymd','title','severity_value','severity_unit',
    'nfsi_prev','nfsi_t','delta_1d','post_min','post_max','post_window_days','rank_score','url'
]);
foreach ($picked as $e) {
    fputcsv($fh, [
        $e['source'], $e['iso2'], $e['date_ymd'], $e['title'],
        $e['severity_value'], $e['severity_unit'],
        number_format((float)$e['nfsi_prev'], 2, '.', ''),
        number_format((float)$e['nfsi_t'], 2, '.', ''),
        number_format((float)$e['delta_1d'], 4, '.', ''),
        number_format((float)$e['post_min'], 2, '.', ''),
        number_format((float)$e['post_max'], 2, '.', ''),
        (int)$e['post_window_days'],
        number_format((float)$e['rank_score'], 4, '.', ''),
        $e['url'] ?? ''
    ]);
}
fclose($fh);

// Write Markdown
$mdPath = rtrim($outDir, '/') . '/named_events_backtest.md';
$md = [];
$md[] = "## Named events backtest (DB, real sources)";
$md[] = "";
$md[] = "- Since: `{$since}`";
$md[] = "- Post window: {$postWindow} days";
$md[] = "- Selection: combined score = |delta_1d| + 0.1*severity_value; unique (iso2, source).";
$md[] = "";
$md[] = "| source | iso2 | date | title | severity | nfsi_prev | nfsi_t | delta_1d | post_min | post_max |";
$md[] = "| :-- | :-- | :-- | :-- | --: | --: | --: | --: | --: | --: |";
foreach ($picked as $e) {
    $sev = (string)$e['severity_value'] . ' ' . (string)$e['severity_unit'];
    $md[] = "| {$e['source']} | {$e['iso2']} | {$e['date_ymd']} | " . str_replace('|', '/', (string)$e['title']) . " | {$sev} | " .
        number_format((float)$e['nfsi_prev'], 2, '.', '') . " | " .
        number_format((float)$e['nfsi_t'], 2, '.', '') . " | " .
        number_format((float)$e['delta_1d'], 4, '.', '') . " | " .
        number_format((float)$e['post_min'], 2, '.', '') . " | " .
        number_format((float)$e['post_max'], 2, '.', '') . " |";
}
$md[] = "";
$md[] = "Files:";
$md[] = "- `named_events_backtest.csv.txt`";
$md[] = "- `named_events_backtest.md`";
$md[] = "";
file_put_contents($mdPath, implode("\n", $md) . "\n");

fwrite(STDERR, "Wrote {$csvPath}\n");
fwrite(STDERR, "Wrote {$mdPath}\n");
exit(0);

?>

