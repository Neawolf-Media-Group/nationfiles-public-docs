<?php
declare(strict_types=1);

/**
 * Deterministic recompute on the deposited CSV fixture.
 *
 * Goal: provide an auditable, side-effect free, executable definition for L1–L4
 * that matches the formal annex order and the single-source missingness policy.
 *
 * CLI:
 *   php recompute_nfsi_fixture.php --fixture-dir=... --out=... [--iso2=AA --date=2026-01-03 --explain]
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

function hasFlag(string $name, array $argv): bool {
    return in_array("--{$name}", $argv, true);
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
        // Skip empty lines (common in deposited CSV exports).
        $allEmpty = true;
        foreach ($row as $v) {
            if ($v !== null && $v !== '') {
                $allEmpty = false;
                break;
            }
        }
        if ($allEmpty) {
            continue;
        }

        $assoc = [];
        foreach ($header as $i => $k) {
            $assoc[$k] = $row[$i] ?? null;
        }
        $rows[] = $assoc;
    }
    fclose($fh);
    return $rows;
}

function clip(float $x, float $lo, float $hi): float {
    return max($lo, min($hi, $x));
}

function round2(float $x): float {
    // PHP round uses half away from zero by default.
    return round($x, 2);
}

$fixtureDir = arg('fixture-dir', $argv) ?: (getcwd() . '/research/deposit/sample-dataset');
$outPath = arg('out', $argv) ?: null;
$filterIso2 = arg('iso2', $argv) ?: null;
$filterDate = arg('date', $argv) ?: null;
$explain = hasFlag('explain', $argv);

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

$raw = readCsv($rawPath);
$countries = readCsv($countryPath);
$connectors = readCsv($connPath);
$prev = readCsv($prevPath);

$countryMeta = [];
foreach ($countries as $c) {
    $countryMeta[$c['iso2']] = [
        'pop' => (float)$c['population'],
        'gov_gap' => isset($c['governance_gap']) ? (float)$c['governance_gap'] : 0.0
    ];
}

$connectorMeta = [];
foreach ($connectors as $m) {
    $connectorMeta[$m['connector_id']] = [
        // fixture header: connector_weight
        'weight' => (float)$m['connector_weight'],
        'group_weight' => (int)$m['group_weight'],
        'higher_raw_is_worse' => ((string)$m['higher_raw_is_worse'] === '1'),
        // fixture header: update_multiplier
        'update_mult' => (float)$m['update_multiplier']
    ];
}

$prevMap = [];
foreach ($prev as $p) {
    // fixture header: nfsi_prev
    $prevMap[$p['iso2'] . '|' . $p['date_ymd']] = (float)$p['nfsi_prev'];
}

// Group raw rows by iso2|date|connector for L1->L2 day aggregation.
$byCdc = []; // [key => rows]
$dates = [];
$iso2s = [];
foreach ($raw as $r) {
    $iso2 = (string)$r['iso2'];
    $d = (string)$r['date_ymd'];
    $cid = (string)$r['connector_id'];

    if ($iso2 === '' || $d === '' || $cid === '') {
        continue;
    }

    if ($filterIso2 !== null && $iso2 !== $filterIso2) {
        continue;
    }
    if ($filterDate !== null && $d !== $filterDate) {
        continue;
    }

    $key = $iso2 . '|' . $d . '|' . $cid;
    $byCdc[$key][] = $r;
    $dates[$d] = true;
    $iso2s[$iso2] = true;
}

if (empty($byCdc)) {
    fwrite(STDERR, "No rows after filters.\n");
    exit(3);
}

// L1 normalisation bounds: derived from fixture raw values (deposit snapshot).
$bounds = []; // connector_id => [min,max]
foreach ($raw as $r) {
    $cid = (string)$r['connector_id'];
    $v = (float)$r['raw_value'];
    if (!isset($bounds[$cid])) {
        $bounds[$cid] = ['min' => $v, 'max' => $v];
    } else {
        $bounds[$cid]['min'] = min($bounds[$cid]['min'], $v);
        $bounds[$cid]['max'] = max($bounds[$cid]['max'], $v);
    }
}

// L1: normalize every row to score [0,100]
$l1Rows = []; // [iso2|date|connector => [scores]]
foreach ($byCdc as $k => $rows) {
    [$iso2, $d, $cid] = explode('|', $k);
    if (!isset($connectorMeta[$cid])) {
        throw new RuntimeException("Missing connector meta for: {$cid}");
    }
    $meta = $connectorMeta[$cid];
    if (!isset($bounds[$cid])) {
        throw new RuntimeException("Missing L1 bounds for: {$cid}");
    }
    $minRaw = (float)$bounds[$cid]['min'];
    $maxRaw = (float)$bounds[$cid]['max'];
    $span = $maxRaw - $minRaw;
    foreach ($rows as $r) {
        $rawVal = (float)$r['raw_value'];
        $u = ($span <= 0.0) ? 50.0 : (100.0 * (($rawVal - $minRaw) / $span));
        $u = clip($u, 0.0, 100.0);
        $s = $meta['higher_raw_is_worse'] ? (100.0 - $u) : $u;
        $s = clip($s, 0.0, 100.0);
        $l1Rows[$k][] = round2($s);
    }
}

// L2: day aggregation and smoothing (yesterday default by group)
// For the fixture we use previous day from nfsi_prev only for L4; for L2 smoothing we use a group default if missing.
$L2_ALPHA = 0.6;
$L2_BETA = 0.4;
$REC_CAP = 95.0;
$REC_RATE_SECURITY = 0.2;
$REC_RATE_OTHER = 1.0;

// Determine full set of connectors from meta for missing substitutions in L3.
$allConnectors = array_keys($connectorMeta);

// Build iso2-date grid from raw data subset.
$datesList = array_keys($dates);
sort($datesList);
$iso2List = array_keys($iso2s);
sort($iso2List);

// L2 day scores (post smoothing): [iso2|date|connector => float]
$l2Day = [];

// Helper: get yesterday date string (Y-m-d)
function prevDay(string $ymd): string {
    $dt = new DateTimeImmutable($ymd, new DateTimeZone('UTC'));
    return $dt->sub(new DateInterval('P1D'))->format('Y-m-d');
}

foreach ($iso2List as $iso2) {
    foreach ($datesList as $d) {
        foreach ($allConnectors as $cid) {
            $k = $iso2 . '|' . $d . '|' . $cid;
            if (!isset($connectorMeta[$cid])) {
                continue;
            }
            $meta = $connectorMeta[$cid];
            $isSecurity = ($meta['group_weight'] === 100);
            $yDefault = $isSecurity ? 85.0 : 70.0;

            $scores = $l1Rows[$k] ?? [];
            if (count($scores) === 0) {
                // missing connector-day: keep null (handled at L3).
                continue;
            }

            $x = $isSecurity ? min($scores) : array_sum($scores) / count($scores);

            // yesterday for smoothing
            $yKey = $iso2 . '|' . prevDay($d) . '|' . $cid;
            $y = $l2Day[$yKey] ?? $yDefault;

            // recovery: only bound upward jumps (documented), with gate per group
            $rate = $isSecurity ? $REC_RATE_SECURITY : $REC_RATE_OTHER;
            $xRec = min($REC_CAP, $y + $rate);
            $xPrime = min($x, $xRec);

            $s = ($L2_ALPHA * $xPrime) + ($L2_BETA * $y);
            $l2Day[$k] = round2(clip($s, 0.0, 100.0));
        }
    }
}

// L3/L4 constants as per annex JSON.
$NEUTRAL_MISSING = 50.0;
$T_CONF = 70.0;
$F_CONF = 1.0;
$CAP_CONF = 35.0;
$T_SMALL = 5_000_000.0;
$CAP_SMALL = 3.0;
$CAP_POP_BONUS = 4.0;
$WGI_PULL = 0.95;
$CRASH_MINSEC = 25.0;
$CAP_DAILY = 3.0;

function log10f(float $x): float {
    return log($x, 10);
}

// L3/L4 outputs:
$out = [];

foreach ($iso2List as $iso2) {
    if (!isset($countryMeta[$iso2])) {
        throw new RuntimeException("Missing country meta for: {$iso2}");
    }
    $pop = $countryMeta[$iso2]['pop'];
    // fixture has no governance_gap field; use 0.0 in fixture recompute.
    $govGap = $countryMeta[$iso2]['gov_gap'];

    foreach ($datesList as $d) {
        // Build per-connector substituted values at L3 using L2 day scores.
        $num = 0.0;
        $den = 0.0;
        $noDataCount = 0;

        // Security minimum over *real* security rows only.
        $minSec = 100.0;
        $hasSec = false;

        foreach ($allConnectors as $cid) {
            $meta = $connectorMeta[$cid];
            $w = $meta['weight'] * $meta['update_mult'];
            $isSecurity = ($meta['group_weight'] === 100);

            $k = $iso2 . '|' . $d . '|' . $cid;
            if (isset($l2Day[$k])) {
                $x = (float)$l2Day[$k];
                $num += $w * $x;
                $den += $w;
                if ($isSecurity) {
                    $hasSec = true;
                    $minSec = min($minSec, $x);
                }
            } else {
                // missing connector -> neutral 50 (authoritative)
                $noDataCount++;
                $x = $NEUTRAL_MISSING;
                $num += $w * $x;
                $den += $w;
            }
        }

        $base = ($den <= 0.0) ? $NEUTRAL_MISSING : ($num / $den);

        // Malus/bonus in canonical order.
        $conflictMalus = min($CAP_CONF, max(0.0, ($T_CONF - $minSec) * $F_CONF));

        // Fragility: parameterised but deterministic; fixture uses governance_gap in [0,1] and gamma(pop) = 1.
        $fragilityMalus = 10.0 * $govGap * 1.0;

        $smallMalus = 0.0;
        if ($pop > 0.0 && $pop < $T_SMALL) {
            $smallMalus = min($CAP_SMALL, 3.0 * (1.0 - ($pop / $T_SMALL)));
        }

        $popBonus = 0.0;
        if ($pop > 0.0) {
            $popBonus = min($CAP_POP_BONUS, 0.5 * log10f($pop));
        }

        $s1 = $base - $conflictMalus;
        $s2 = $s1 - $fragilityMalus;
        $s3 = $s2 - $smallMalus;
        $s4 = $s3 + $popBonus;
        $s5 = $WGI_PULL * $s4;

        $l3 = round2(clip($s5, 1.0, 100.0));

        // L4
        $crashMode = ($hasSec && ($minSec < $CRASH_MINSEC));
        $yKey = $iso2 . '|' . prevDay($d);
        $nfsiYesterday = $prevMap[$yKey] ?? 70.0;

        if ($crashMode) {
            // Crash mode: publish the raw (L3) score; no inertia and no daily-change cap.
            $nfsiToday = $l3;
        } else {
            $nfsiStar = $l3;
            // fixture inertia: matches StabilityIndex.php — _score_without_l1_l2_data <=> noDataCount > 0
            $wInertia = ($noDataCount > 0) ? 0.50 : 0.80;
            $nfsiStar = ($wInertia * $nfsiYesterday) + ((1.0 - $wInertia) * $l3);
            $nfsiStar = clip($nfsiStar, 1.0, 100.0);
            $nfsiCapped = clip($nfsiStar, $nfsiYesterday - $CAP_DAILY, $nfsiYesterday + $CAP_DAILY);
            $nfsiToday = round2($nfsiCapped);
        }

        $row = [
            'iso2' => $iso2,
            'date_ymd' => $d,
            'l3_base' => round2($base),
            'minSec' => round2($minSec),
            'hasSec' => $hasSec ? 1 : 0,
            'conflict_malus' => round2($conflictMalus),
            'fragility_malus' => round2($fragilityMalus),
            'small_country_malus' => round2($smallMalus),
            'pop_bonus' => round2($popBonus),
            'l3_score' => $l3,
            'crash_mode' => $crashMode ? 1 : 0,
            'nfsi_yesterday' => round2($nfsiYesterday),
            'nfsi_today' => $nfsiToday,
            'no_data_count' => $noDataCount
        ];

        if ($explain) {
            echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
            exit(0);
        }

        $out[] = $row;
    }
}

if ($outPath !== null) {
    $fh = fopen($outPath, 'wb');
    if ($fh === false) {
        fwrite(STDERR, "Could not write: {$outPath}\n");
        exit(4);
    }
    fputcsv($fh, array_keys($out[0]));
    foreach ($out as $r) {
        fputcsv($fh, array_values($r));
    }
    fclose($fh);
    echo "WROTE: {$outPath}\n";
    exit(0);
}

echo json_encode($out, JSON_PRETTY_PRINT) . "\n";
exit(0);

?>

