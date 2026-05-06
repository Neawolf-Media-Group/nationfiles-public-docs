<?php
/**
 * Forecast backtest scaffold (RMSE) for the NFSI 7‑day forecast.
 *
 * This script is intentionally a scaffold: it does not ship with production data.
 * It computes RMSE over a user-provided CSV export.
 *
 * Expected CSV columns (header row required):
 * - iso2
 * - forecast_date (YYYY-MM-DD)          // day the forecast was issued
 * - horizon_days (integer, e.g. 1..7)
 * - y_pred (float)                     // predicted NFSI for target_date
 * - y_true (float)                     // realised NFSI on target_date
 *
 * Output:
 * - RMSE overall
 * - RMSE per horizon_days
 *
 * Usage:
 *   php research/evidence/forecast_backtest_rmse.php path/to/backtest.csv
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

$path = $argv[1] ?? '';
if ($path === '' || !is_readable($path)) {
    fwrite(STDERR, "Usage: php research/evidence/forecast_backtest_rmse.php path/to/backtest.csv\n");
    exit(2);
}

$fh = fopen($path, 'rb');
if ($fh === false) {
    fwrite(STDERR, "Could not open: {$path}\n");
    exit(3);
}

$header = fgetcsv($fh);
if (!is_array($header) || count($header) < 5) {
    fwrite(STDERR, "Invalid CSV header.\n");
    exit(4);
}

$idx = array_flip($header);
foreach (['iso2', 'forecast_date', 'horizon_days', 'y_pred', 'y_true'] as $col) {
    if (!array_key_exists($col, $idx)) {
        fwrite(STDERR, "Missing column: {$col}\n");
        exit(5);
    }
}

$sumSq = 0.0;
$n = 0;
$byH = []; // horizon => [sumSq, n]

while (($row = fgetcsv($fh)) !== false) {
    $h = (int) ($row[$idx['horizon_days']] ?? 0);
    $pred = (float) ($row[$idx['y_pred']] ?? 0);
    $true = (float) ($row[$idx['y_true']] ?? 0);
    if ($h <= 0) {
        continue;
    }
    $e = $pred - $true;
    $sumSq += ($e * $e);
    $n++;

    if (!isset($byH[$h])) {
        $byH[$h] = ['sumSq' => 0.0, 'n' => 0];
    }
    $byH[$h]['sumSq'] += ($e * $e);
    $byH[$h]['n']++;
}
fclose($fh);

if ($n === 0) {
    fwrite(STDERR, "No usable rows.\n");
    exit(6);
}

$rmse = sqrt($sumSq / $n);
echo "RMSE overall: " . number_format($rmse, 4) . " (n={$n})\n";

ksort($byH);
foreach ($byH as $h => $s) {
    if ($s['n'] <= 0) {
        continue;
    }
    $rmseH = sqrt($s['sumSq'] / $s['n']);
    echo "RMSE horizon {$h}d: " . number_format($rmseH, 4) . " (n={$s['n']})\n";
}

