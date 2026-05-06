<?php
declare(strict_types=1);

/**
 * Compute persistence-baseline forecast skill on an NFSI timeline.
 *
 * Input: JSON from nationfiles public export (?export=json&chart=country_nfsi_30d).
 * It must contain country_nfsi_30d.timeline as a date=>value map.
 *
 * Usage:
 *   php research/deposit/recompute/forecast_skill_persistence.php path/to/usa__country_nfsi_30d.json --max-h=7
 *
 * Output:
 *   RMSE/MAE per horizon h=1..max-h, using y_pred(t+h)=y_true(t) (persistence).
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

$path = $argv[1] ?? '';
if ($path === '' || !is_readable($path)) {
    fwrite(STDERR, "Usage: php .../forecast_skill_persistence.php path/to/export.json --max-h=7\n");
    exit(2);
}

$maxH = 7;
foreach ($argv as $a) {
    if (str_starts_with($a, '--max-h=')) $maxH = max(1, (int)substr($a, 8));
}

$raw = file_get_contents($path);
if ($raw === false) {
    fwrite(STDERR, "Could not read: {$path}\n");
    exit(3);
}
$j = json_decode($raw, true);
if (!is_array($j)) {
    fwrite(STDERR, "Invalid JSON: {$path}\n");
    exit(4);
}

// Accept either "sample_data.country_nfsi_30d.timeline" (repo sample wrapper) or direct payload.
$timeline = $j['country_nfsi_30d']['timeline'] ?? ($j['sample_data']['country_nfsi_30d']['timeline'] ?? null);
if (!is_array($timeline) || count($timeline) < 10) {
    fwrite(STDERR, "Missing or too short timeline in JSON.\n");
    exit(5);
}

// Remove truncation sentinel if present.
unset($timeline['__truncated__']);

// Sort by date key.
ksort($timeline);
$dates = array_keys($timeline);
$vals = array_values($timeline);
$n = count($vals);

echo "Persistence baseline skill\n";
echo "File: {$path}\n";
echo "Points: {$n} (from {$dates[0]} to {$dates[$n-1]})\n\n";

for ($h = 1; $h <= $maxH; $h++) {
    $sumSq = 0.0;
    $sumAbs = 0.0;
    $k = 0;
    for ($i = 0; $i + $h < $n; $i++) {
        $pred = (float)$vals[$i];
        $true = (float)$vals[$i + $h];
        $e = $pred - $true;
        $sumSq += $e * $e;
        $sumAbs += abs($e);
        $k++;
    }
    if ($k === 0) continue;
    $rmse = sqrt($sumSq / $k);
    $mae = $sumAbs / $k;
    echo "h={$h}d  RMSE=" . number_format($rmse, 4) . "  MAE=" . number_format($mae, 4) . "  n={$k}\n";
}

exit(0);

?>

