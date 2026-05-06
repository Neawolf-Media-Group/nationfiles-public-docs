<?php
declare(strict_types=1);

// Validation scaffolds on the synthetic fixture:
// - Crash-mode evaluation: Precision/Recall/Accuracy + ROC/AUC (synthetic labels; proxy based on SEC_CONFLICT raw_value)
// - Sensitivity analysis scaffold: +/-10% connector_weight perturbation (printed as CSV-like output)
//
// This is intentionally minimal and does not claim external validity.

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

if (!is_readable($rawPath)) {
    fwrite(STDERR, "Missing fixture file: {$rawPath}\n");
    exit(2);
}

function readCsv(string $path): array {
    $fh = fopen($path, 'rb');
    if ($fh === false) throw new RuntimeException("Could not open: {$path}");
    $header = fgetcsv($fh);
    if (!is_array($header)) throw new RuntimeException("Invalid header: {$path}");
    $rows = [];
    while (($row = fgetcsv($fh)) !== false) {
        $assoc = [];
        foreach ($header as $i => $k) $assoc[$k] = $row[$i] ?? null;
        $rows[] = $assoc;
    }
    fclose($fh);
    return $rows;
}

$raw = readCsv($rawPath);

// --- Crash-mode evaluation (synthetic) ---

// Synthetic crash label: y_true = 1 if SEC_CONFLICT raw_value >= 75 (proxy for min security < 25 after normalization)
$yTrue = [];
$yPred = [];
$yScore = []; // continuous-ish score for ROC/AUC
foreach ($raw as $r) {
    if ($r['connector_id'] !== 'SEC_CONFLICT') continue;
    $key = $r['iso2'] . '|' . $r['date_ymd'];
    $v = (float)$r['raw_value'];
    $yTrue[$key] = ($v >= 75.0) ? 1 : 0;
    // synthetic predictor: threshold at 70
    $yPred[$key] = ($v >= 70.0) ? 1 : 0;
    $yScore[$key] = $v; // higher = more likely crash
}

$tp = $fp = $tn = $fn = 0;
foreach ($yTrue as $k => $t) {
    $p = $yPred[$k] ?? 0;
    if ($t === 1 && $p === 1) $tp++;
    elseif ($t === 0 && $p === 1) $fp++;
    elseif ($t === 0 && $p === 0) $tn++;
    elseif ($t === 1 && $p === 0) $fn++;
}
$precision = ($tp + $fp) > 0 ? $tp / ($tp + $fp) : 0.0;
$recall = ($tp + $fn) > 0 ? $tp / ($tp + $fn) : 0.0;
$accuracy = ($tp + $tn + $fp + $fn) > 0 ? ($tp + $tn) / ($tp + $tn + $fp + $fn) : 0.0;

echo "Synthetic crash-mode classification:\n";
echo "TP={$tp} FP={$fp} TN={$tn} FN={$fn}\n";
echo "Precision=" . number_format($precision, 4) . " Recall=" . number_format($recall, 4) . " Accuracy=" . number_format($accuracy, 4) . "\n";

// ROC/AUC (Mann–Whitney U)
$pos = [];
$neg = [];
foreach ($yTrue as $k => $t) {
    $s = $yScore[$k] ?? null;
    if ($s === null) continue;
    if ($t === 1) $pos[] = $s; else $neg[] = $s;
}
if (count($pos) > 0 && count($neg) > 0) {
    $wins = 0.0;
    foreach ($pos as $ps) {
        foreach ($neg as $ns) {
            if ($ps > $ns) $wins += 1.0;
            elseif ($ps === $ns) $wins += 0.5;
        }
    }
    $auc = $wins / (count($pos) * count($neg));
    echo "ROC/AUC=" . number_format($auc, 4) . " (synthetic proxy)\n";
} else {
    echo "ROC/AUC=NA (need both positive and negative samples)\n";
}

// --- Sensitivity scaffold (+/-10% connector_weight) ---
// This does not recompute full L1-L4 (requires DB or full recompute script).
// It prints the perturbed weights to ensure the protocol is explicit and machine-readable.
$connMetaPath = rtrim($fixtureDir, '/') . '/connector_meta.csv.txt';
if (is_readable($connMetaPath)) {
    $metaRows = readCsv($connMetaPath);
    echo "\nSensitivity scaffold (+/-10% connector_weight):\n";
    echo "connector_id,weight_base,weight_minus10,weight_plus10\n";
    foreach ($metaRows as $m) {
        if (!isset($m['connector_id']) || trim((string)$m['connector_id']) === '') {
            continue;
        }
        $w = (float)$m['connector_weight'];
        echo $m['connector_id'] . "," .
            number_format($w, 2, '.', '') . "," .
            number_format($w * 0.90, 2, '.', '') . "," .
            number_format($w * 1.10, 2, '.', '') . "\n";
    }
}
exit(0);

?>

