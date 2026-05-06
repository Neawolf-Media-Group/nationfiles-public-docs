<?php
declare(strict_types=1);

/**
 * Runs recompute over the fixture and enforces invariants.
 *
 * CLI:
 *   php check_invariants_fixture.php --fixture-dir=... --tmp-out=/tmp/nfsi_fixture.csv
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

function arg(string $name, array $argv): ?string {
    foreach ($argv as $a) {
        if (str_starts_with($a, "--{$name}=")) {
            return substr($a, strlen("--{$name}="));
        }
    }
    return null;
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

function fail(string $msg): void {
    fwrite(STDERR, "FAIL: {$msg}\n");
    exit(2);
}

$fixtureDir = arg('fixture-dir', $argv) ?: (getcwd() . '/research/deposit/sample-dataset');
$tmpOut = arg('tmp-out', $argv) ?: sys_get_temp_dir() . '/nfsi_fixture_out.csv';

$cmd = escapeshellcmd(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/recompute_nfsi_fixture.php')
    . ' --fixture-dir=' . escapeshellarg($fixtureDir)
    . ' --out=' . escapeshellarg($tmpOut);

exec($cmd, $o, $rc);
if ($rc !== 0) {
    fail("recompute failed with exit code {$rc}");
}

if (!is_readable($tmpOut)) {
    fail("missing output csv: {$tmpOut}");
}

$rows = readCsv($tmpOut);
if (count($rows) < 1) {
    fail("no recompute rows produced");
}

foreach ($rows as $r) {
    $iso2 = (string)$r['iso2'];
    $d = (string)$r['date_ymd'];

    $l3 = (float)$r['l3_score'];
    $nfsi = (float)$r['nfsi_today'];
    $y = (float)$r['nfsi_yesterday'];
    $crash = ((string)$r['crash_mode'] === '1');
    $minSec = (float)$r['minSec'];
    $hasSec = ((string)$r['hasSec'] === '1');

    if ($l3 < 1.0 || $l3 > 100.0) {
        fail("I3 violated for {$iso2} {$d}: l3_score={$l3}");
    }
    if ($nfsi < 1.0 || $nfsi > 100.0) {
        fail("I4 violated for {$iso2} {$d}: nfsi_today={$nfsi}");
    }

    // I7 crash predicate equivalence
    $crashExpected = ($hasSec && ($minSec < 25.0));
    if ($crash !== $crashExpected) {
        fail("I7 violated for {$iso2} {$d}: crash_mode={$crash} expected=" . ($crashExpected ? '1' : '0'));
    }

    // I8 crash override
    if ($crash && abs($nfsi - $l3) > 1e-9) {
        fail("I8 violated for {$iso2} {$d}: crash mode but nfsi_today != l3_score ({$nfsi} vs {$l3})");
    }

    // I9 daily change cap (non-crash)
    if (!$crash && abs($nfsi - $y) > 3.00001) {
        fail("I9 violated for {$iso2} {$d}: |delta| > 3 (nfsi_today={$nfsi}, yesterday={$y})");
    }
}

echo "OK: invariants passed for fixture recompute (" . count($rows) . " rows)\n";
exit(0);

?>

