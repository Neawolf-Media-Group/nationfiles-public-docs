<?php
declare(strict_types=1);

/**
 * Missingness policy comparison (50 vs 100) on a lightweight approximation:
 * - Reads a timeline of connector scores (already in 0..100) from CSV
 * - Computes a weighted mean with a configurable substitution value for missing connectors
 *
 * This is a deposit-side audit tool: it quantifies the delta induced by different
 * missing substitutions, without needing production DB access.
 *
 * CSV format:
 * iso2,date_ymd,connector_id,score_0_100
 *
 * Usage:
 *   php research/deposit/recompute/missingness_policy_compare.php path/to/scores.csv.txt --missing=50 --missing-alt=100
 */

if (PHP_SAPI !== 'cli') { fwrite(STDERR, "CLI only.\n"); exit(1); }

$path = $argv[1] ?? '';
if ($path === '' || !is_readable($path)) {
    fwrite(STDERR, "Usage: php .../missingness_policy_compare.php path/to/scores.csv.txt --missing=50 --missing-alt=100\n");
    exit(2);
}

$missing = 50.0;
$missingAlt = 100.0;
foreach ($argv as $a) {
    if (str_starts_with($a, '--missing=')) $missing = (float)substr($a, 10);
    if (str_starts_with($a, '--missing-alt=')) $missingAlt = (float)substr($a, 14);
}

$fh = fopen($path, 'rb');
if ($fh === false) { fwrite(STDERR, "Could not open.\n"); exit(3); }
$header = fgetcsv($fh);
if (!is_array($header)) { fwrite(STDERR, "Bad header.\n"); exit(4); }
$idx = array_flip($header);
foreach (['iso2','date_ymd','connector_id','score_0_100'] as $col) {
    if (!isset($idx[$col])) { fwrite(STDERR, "Missing column: {$col}\n"); exit(5); }
}

$by = []; // iso2|date => connector => score
$connectors = [];
while (($row = fgetcsv($fh)) !== false) {
    $iso2 = strtoupper(trim((string)($row[$idx['iso2']] ?? '')));
    $d = trim((string)($row[$idx['date_ymd']] ?? ''));
    $cid = trim((string)($row[$idx['connector_id']] ?? ''));
    $s = (float)($row[$idx['score_0_100']] ?? 0);
    if ($iso2 === '' || $d === '' || $cid === '') continue;
    $key = $iso2 . '|' . $d;
    if (!isset($by[$key])) $by[$key] = [];
    $by[$key][$cid] = $s;
    $connectors[$cid] = true;
}
fclose($fh);

$connectorList = array_keys($connectors);
sort($connectorList);
if (!$connectorList) { fwrite(STDERR, "No connectors.\n"); exit(6); }

function mean(array $xs): float { return array_sum($xs) / max(1, count($xs)); }

echo "iso2,date_ymd,score_missing_" . (int)$missing . ",score_missing_" . (int)$missingAlt . ",delta\n";
ksort($by);
foreach ($by as $key => $m) {
    [$iso2, $d] = explode('|', $key, 2);
    $scoresA = [];
    $scoresB = [];
    foreach ($connectorList as $cid) {
        $v = array_key_exists($cid, $m) ? (float)$m[$cid] : $missing;
        $v2 = array_key_exists($cid, $m) ? (float)$m[$cid] : $missingAlt;
        $scoresA[] = $v;
        $scoresB[] = $v2;
    }
    $a = mean($scoresA);
    $b = mean($scoresB);
    $delta = $a - $b;
    echo "{$iso2},{$d}," . number_format($a, 4, '.', '') . "," . number_format($b, 4, '.', '') . "," . number_format($delta, 4, '.', '') . "\n";
}

exit(0);

?>

