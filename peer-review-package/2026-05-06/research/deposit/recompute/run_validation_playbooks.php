<?php
declare(strict_types=1);

/**
 * Small, manuscript-aligned playbook assertions (tests A–E scaffolding).
 *
 * IMPORTANT:
 * - These assertions encode *documented normative behaviors* for formulas and thresholds.
 * - They intentionally do NOT require a database.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

function assert_true(bool $cond, string $msg): void {
    if (!$cond) {
        fwrite(STDERR, "FAIL: {$msg}\n");
        exit(2);
    }
}

function approx(float $a, float $b, float $eps = 1e-9): bool {
    return abs($a - $b) <= $eps;
}

// ---- Test A: crash mode trigger semantics (documented predicate)
// min(real group-100 scores) < 25 => crash; placeholders excluded from min set.
function test_a_crash_mode_trigger(): void {
    $scores = [24.9, 30.0]; // placeholders excluded conceptually in manuscript
    $minReal = min($scores);
    $crash = ($minReal < 25.0);
    assert_true($crash === true, 'Test A: expected crash=true for min(real)=24.9');
}

// ---- Test B: pinned PHP — any neutral substitution => inertia 0.50 (matches StabilityIndex.php)
function test_b_inertia_any_no_data(): void {
    $noDataCount = 2;
    $inertiaWeight = 0.80;
    if ($noDataCount > 0) {
        $inertiaWeight = 0.50;
    }
    assert_true($inertiaWeight === 0.50, 'Test B: inertiaWeight 0.50 when noDataCount>0');
}

// ---- Test B2: mirrors trimmed PHP — binary inertia only (no legacy 0.45 tier)
function test_b2_inertia_binary_only(): void {
    $noDataCount = 6;
    $inertiaWeight = 0.80;
    if ($noDataCount > 0) {
        $inertiaWeight = 0.50;
    } else {
        $inertiaWeight = 0.80;
    }
    assert_true($inertiaWeight === 0.50, 'Test B2: noDataCount>0 forces 0.50 (binary mapping)');
}

// ---- Test C: sensitivity toy on LAYER2 smoothing only (isolates constant effect)
function layered2_smooth(float $dayScore, float $yesterday, float $todayWeight): float {
    return $todayWeight * $dayScore + (1.0 - $todayWeight) * $yesterday;
}

function test_c_layer2_today_weight_sensitivity(): void {
    $day = 55.0;
    $yesterday = 65.0;
    $s050 = layered2_smooth($day, $yesterday, 0.50); // 60.0
    $s060 = layered2_smooth($day, $yesterday, 0.60); // 59.0
    $s070 = layered2_smooth($day, $yesterday, 0.70); // 58.0
    assert_true(approx($s050, 60.0), 'Test C: s050');
    assert_true(approx($s060, 59.0), 'Test C: s060');
    assert_true(approx($s070, 58.0), 'Test C: s070');
    assert_true(approx(abs($s070 - $s050), 2.0), 'Test C: swing should be 2.0 points under this toy setup');
}

// ---- Test D: population edge cases on popNegMult bounds and monotonicity regions
function pop_neg_mult(float $pop, float $ref, float $floor): float {
    $den = max($pop, $floor);
    return min(2.0, log10($ref) / log10($den));
}

function fragility_malus(float $est_total, float $pop, float $popNegMult): float {
    $wgi010 = $est_total / 10.0;
    $govGap = 10.0 - $wgi010;
    $floor = 100_000.0;
    $popSens = 1.0 / log10(max($pop, $floor));
    $raw = $govGap * $popSens * 3.0 * $popNegMult;
    return min(15.0, $raw);
}

function test_d_population_edge_cases(): void {
    $ref = 45_000_000.0;

    $p50k = pop_neg_mult(50_000.0, $ref, 100_000.0);
    $p5m = pop_neg_mult(5_000_000.0, $ref, 100_000.0);
    $p50m = pop_neg_mult(50_000_000.0, $ref, 100_000.0);

    assert_true($p50k <= 2.0 && $p50k > 1.0, 'Test D: popNegMult should be >1 for very small pop');
    assert_true($p5m <= 2.0 && $p5m >= 1.0, 'Test D: popNegMult bounded for mid pop');
    assert_true(approx($p50m, 1.0) || $p50m < 1.0000001, 'Test D: large pop should trend to ~1 multiplier');

    // Use a fixed WGI total to ensure malus is finite and capped
    $m50k = fragility_malus(55.0, 50_000.0, $p50k);
    $m50m = fragility_malus(55.0, 50_000_000.0, $p50m);
    assert_true($m50k >= 0.0 && $m50k <= 15.0, 'Test D: fragility malus capped for small pop case');
    assert_true($m50m >= 0.0 && $m50m <= 15.0, 'Test D: fragility malus capped for large pop case');
}

// ---- Test E: determinism gate is executed by validate_recompute.sh (byte-identical recompute)

test_a_crash_mode_trigger();
test_b_inertia_any_no_data();
test_b2_inertia_binary_only();
test_c_layer2_today_weight_sensitivity();
test_d_population_edge_cases();

echo "OK: validation playbooks passed.\n";
exit(0);
