<?php
declare(strict_types=1);

// Minimal executable unit tests for the fixture (3-5 checks).
// The goal is determinism and auditability, not framework completeness.

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

$fixtureDir = null;
foreach ($argv as $a) {
    if (str_starts_with($a, '--fixture-dir=')) {
        $fixtureDir = substr($a, strlen('--fixture-dir='));
    }
}
$fixtureDir = $fixtureDir ?: (getcwd() . '/research/deposit/sample-dataset');
$rawPath = rtrim($fixtureDir, '/') . '/connectors_raw.csv.txt';
$countryPath = rtrim($fixtureDir, '/') . '/country_meta.csv.txt';
$connPath = rtrim($fixtureDir, '/') . '/connector_meta.csv.txt';
$prevPath = rtrim($fixtureDir, '/') . '/nfsi_prev.csv.txt';

foreach ([$rawPath, $countryPath, $connPath, $prevPath] as $p) {
    if (!is_readable($p)) {
        fwrite(STDERR, "Missing fixture file: {$p}\n");
        exit(2);
    }
}

function readCsv(string $path): array {
    $fh = fopen($path, 'rb');
    if ($fh === false) {
        throw new RuntimeException("Could not open: {$path}");
    }
    $header = fgetcsv($fh);
    if (!is_array($header)) {
        throw new RuntimeException("Invalid header: {$path}");
    }
    $rows = [];
    while (($row = fgetcsv($fh)) !== false) {
        $assoc = [];
        foreach ($header as $i => $k) {
            $assoc[$k] = $row[$i] ?? null;
        }
        $rows[] = $assoc;
    }
    fclose($fh);
    return $rows;
}

$raw = readCsv($rawPath);
$countries = readCsv($countryPath);
$connectors = readCsv($connPath);
$prev = readCsv($prevPath);

// Test 1: fixture integrity
if (count($countries) < 10) {
    fwrite(STDERR, "FAIL: expected >=10 countries, got " . count($countries) . "\n");
    exit(3);
}

// Test 2: specific known row exists
$found = false;
foreach ($raw as $r) {
    if ($r['iso2'] === 'AA' && $r['date_ymd'] === '2026-01-03' && $r['connector_id'] === 'SEC_CONFLICT') {
        $found = true;
        break;
    }
}
if (!$found) {
    fwrite(STDERR, "FAIL: expected AA/2026-01-03/SEC_CONFLICT row.\n");
    exit(4);
}

// Test 3: meta sanity
$meta = [];
foreach ($connectors as $c) {
    $meta[$c['connector_id']] = $c;
}
if (!isset($meta['SEC_CONFLICT']) || (int)$meta['SEC_CONFLICT']['group_weight'] !== 100) {
    fwrite(STDERR, "FAIL: expected SEC_CONFLICT group_weight=100.\n");
    exit(5);
}

// Test 4: prev present for AA
$prevAA = false;
foreach ($prev as $p) {
    if ($p['iso2'] === 'AA' && $p['date_ymd'] === '2026-01-01') {
        $prevAA = true;
        break;
    }
}
if (!$prevAA) {
    fwrite(STDERR, "FAIL: expected nfsi_prev for AA on 2026-01-01.\n");
    exit(6);
}

// Test 5: recompute executes for a known explain row (smoke check)
$cmd = escapeshellcmd(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/recompute_nfsi_fixture.php')
    . ' --fixture-dir=' . escapeshellarg($fixtureDir)
    . ' --iso2=AA --date=2026-01-03 --explain';
exec($cmd, $out, $rc);
if ($rc !== 0 || count($out) < 1) {
    fwrite(STDERR, "FAIL: recompute_nfsi_fixture.php did not execute for AA/2026-01-03.\n");
    exit(7);
}

echo "OK: fixture unit tests passed.\n";
exit(0);

?>

