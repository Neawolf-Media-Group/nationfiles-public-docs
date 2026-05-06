<?php
declare(strict_types=1);

/**
 * DB-backed event-style backtest (since NFSI_DATA_VALID_FROM).
 *
 * Goal (audit): Produce 5 "events" as the largest absolute daily NFSI deltas, with
 * effect-size style summaries over a fixed window.
 *
 * This script uses the SAME DB config file as the connector cron runner:
 *   config/.db.php (via DATA_SOURCE_CONNECTOR_DB_CONFIG)
 *
 * Usage:
 *   php research/deposit/recompute/backtest_events_from_db.php --out-dir=research/deposit/backtest --since=2026-02-14 --k=5
 *
 * Output:
 * - backtest_events.csv.txt
 * - backtest_events.md
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

$outDir = null;
$since = '2026-02-14';
$k = 5;
$window = 7;
foreach ($argv as $a) {
    if (str_starts_with($a, '--out-dir=')) $outDir = substr($a, 10);
    if (str_starts_with($a, '--since=')) $since = substr($a, 8);
    if (str_starts_with($a, '--k=')) $k = max(1, (int)substr($a, 4));
    if (str_starts_with($a, '--window=')) $window = max(1, (int)substr($a, 9));
}
$outDir = $outDir ?: (getcwd() . '/research/deposit/backtest');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $since)) {
    fwrite(STDERR, "Invalid --since date.\n");
    exit(2);
}

// Locate config/.db.php like run_stability_index.php does, without printing secrets.
$base = getcwd();
$cfg = $base . '/config/.db.php';
if (!is_file($cfg)) {
    // try relative to connector root if launched elsewhere
    $connectorRoot = $base . '/bin/DataSourceConnector';
    $alt = $connectorRoot . '/../config/.db.php';
    if (is_file($alt)) $cfg = $alt;
}
if (!is_file($cfg)) {
    fwrite(STDERR, "Missing DB config: config/.db.php\n");
    exit(3);
}

$db_vars = [];
require $cfg; // provides $db_vars
// normalize (may be list of single-key arrays)
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
    fwrite(STDERR, "DB name missing in config.\n");
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

// Ensure table exists.
try {
    $pdo->query("SELECT 1 FROM nfsi_country LIMIT 1");
} catch (Throwable $e) {
    fwrite(STDERR, "Missing table: nfsi_country\n");
    exit(7);
}

// Pull top absolute deltas since date (skip neutral 50 and null prev),
// then select the first K unique ISO2 to avoid multiple entries for one country.
$sqlTop = "
SELECT
  c.iso2,
  c.date AS date_ymd,
  c.score AS score,
  p.score AS score_prev,
  (c.score - p.score) AS delta
FROM nfsi_country c
JOIN nfsi_country p
  ON p.iso2 = c.iso2
 AND p.date = DATE_SUB(c.date, INTERVAL 1 DAY)
JOIN countries co
  ON BINARY co.iso2 = BINARY c.iso2
WHERE c.date >= :since
  AND c.score IS NOT NULL
  AND p.score IS NOT NULL
  AND c.score NOT IN (50)
  AND p.score NOT IN (50)
ORDER BY ABS(c.score - p.score) DESC
LIMIT 300
";

$top = $pdo->prepare($sqlTop);
$top->execute(['since' => $since]);
$cand = $top->fetchAll() ?: [];
$events = [];
$seen = [];
foreach ($cand as $row) {
    $iso = (string)($row['iso2'] ?? '');
    if ($iso === '' || isset($seen[$iso])) {
        continue;
    }
    $seen[$iso] = true;
    $events[] = $row;
    if (count($events) >= $k) {
        break;
    }
}
if (!$events) {
    fwrite(STDERR, "No events found.\n");
    exit(8);
}

// Window stats around each event.
// Pre window: [t-window .. t-1], Post window: [t .. t+window]
$sqlWindow = "
SELECT date, score
FROM nfsi_country
WHERE iso2 = :iso2
  AND date BETWEEN :d0 AND :d1
  AND score IS NOT NULL
ORDER BY date ASC
";
$stmtW = $pdo->prepare($sqlWindow);

function mean(array $xs): ?float {
    if (!$xs) return null;
    return array_sum($xs) / count($xs);
}
function stddev(array $xs): ?float {
    $n = count($xs);
    if ($n < 2) return null;
    $m = array_sum($xs) / $n;
    $ss = 0.0;
    foreach ($xs as $x) $ss += ($x - $m) * ($x - $m);
    return sqrt($ss / ($n - 1));
}

$rowsOut = [];
foreach ($events as $ev) {
    $iso2 = (string)$ev['iso2'];
    $t = (string)$ev['date_ymd'];
    $score = (float)$ev['score'];
    $scorePrev = (float)$ev['score_prev'];
    $delta = (float)$ev['delta'];

    $d0pre = (new DateTimeImmutable($t))->sub(new DateInterval('P' . $window . 'D'))->format('Y-m-d');
    $d1pre = (new DateTimeImmutable($t))->sub(new DateInterval('P1D'))->format('Y-m-d');
    $d0post = $t;
    $d1post = (new DateTimeImmutable($t))->add(new DateInterval('P' . $window . 'D'))->format('Y-m-d');

    $pre = [];
    $stmtW->execute(['iso2' => $iso2, 'd0' => $d0pre, 'd1' => $d1pre]);
    foreach ($stmtW->fetchAll() as $r) $pre[] = (float)$r['score'];

    $post = [];
    $stmtW->execute(['iso2' => $iso2, 'd0' => $d0post, 'd1' => $d1post]);
    foreach ($stmtW->fetchAll() as $r) $post[] = (float)$r['score'];

    $preMean = mean($pre);
    $preSd = stddev($pre);
    $postMin = $post ? min($post) : null;
    $postMax = $post ? max($post) : null;
    $effectZ = ($preSd !== null && $preSd > 0.0 && $preMean !== null) ? (($score - $preMean) / $preSd) : null;

    $rowsOut[] = [
        'iso2' => $iso2,
        'date_ymd' => $t,
        'score_prev' => $scorePrev,
        'score' => $score,
        'delta' => $delta,
        'pre_mean' => $preMean,
        'pre_sd' => $preSd,
        'post_min' => $postMin,
        'post_max' => $postMax,
        'z_vs_pre' => $effectZ,
        'window_days' => $window
    ];
}

// Write CSV-like.
$csvPath = rtrim($outDir, '/') . '/backtest_events.csv.txt';
$fh = fopen($csvPath, 'wb');
fputcsv($fh, array_keys($rowsOut[0]));
foreach ($rowsOut as $r) {
    $line = $r;
    foreach (['delta','pre_mean','pre_sd','post_min','post_max','z_vs_pre'] as $k2) {
        if ($line[$k2] === null) continue;
        $line[$k2] = is_float($line[$k2]) ? number_format((float)$line[$k2], 4, '.', '') : $line[$k2];
    }
    fputcsv($fh, $line);
}
fclose($fh);

// Write Markdown summary.
$mdPath = rtrim($outDir, '/') . '/backtest_events.md';
$md = [];
$md[] = "## Event-style backtest (data-driven, DB)";
$md[] = "";
$md[] = "- Since: `{$since}`";
$md[] = "- Selection rule: top-" . count($rowsOut) . " largest absolute daily deltas from `nfsi_country`.";
$md[] = "- Window: {$window} days pre and {$window} days post.";
$md[] = "";
$md[] = "| iso2 | date | score_prev | score | delta | pre_mean | pre_sd | post_min | post_max | z_vs_pre |";
$md[] = "| :-- | :-- | --: | --: | --: | --: | --: | --: | --: | --: |";
foreach ($rowsOut as $r) {
    $fmt = fn($v) => $v === null ? "" : (is_float($v) ? number_format($v, 4, '.', '') : (string)$v);
    $md[] =
        "| {$r['iso2']} | {$r['date_ymd']} | " .
        number_format((float)$r['score_prev'], 2, '.', '') . " | " .
        number_format((float)$r['score'], 2, '.', '') . " | " .
        $fmt($r['delta']) . " | " .
        $fmt($r['pre_mean']) . " | " .
        $fmt($r['pre_sd']) . " | " .
        $fmt($r['post_min']) . " | " .
        $fmt($r['post_max']) . " | " .
        $fmt($r['z_vs_pre']) . " |";
}
$md[] = "";
$md[] = "Files:";
$md[] = "- `backtest_events.csv.txt`";
$md[] = "- `backtest_events.md`";
$md[] = "";
file_put_contents($mdPath, implode("\n", $md) . "\n");

fwrite(STDERR, "Wrote {$csvPath}\n");
fwrite(STDERR, "Wrote {$mdPath}\n");
exit(0);

?>

