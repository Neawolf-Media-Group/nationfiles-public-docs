<?php
declare(strict_types=1);

/**
 * Fetch public NFSI export endpoints and store raw JSON for deposit.
 *
 * Usage:
 *   php research/deposit/recompute/fetch_public_exports.php --out-dir=research/deposit/public-exports --iso2=usa,deu,ukr
 *
 * Notes:
 * - This uses only PUBLIC endpoints (no DB, no secrets).
 * - Stored files can be hashed and deposited for reproducibility.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

$outDir = null;
$iso2List = null;
foreach ($argv as $a) {
    if (str_starts_with($a, '--out-dir=')) $outDir = substr($a, 10);
    if (str_starts_with($a, '--iso2=')) $iso2List = substr($a, 7);
}
$outDir = $outDir ?: (getcwd() . '/research/deposit/public-exports');
$iso2List = $iso2List ?: 'usa';

$isos = array_values(array_filter(array_map('trim', explode(',', $iso2List))));
if (!$isos) {
    fwrite(STDERR, "No iso2 provided.\n");
    exit(2);
}

if (!is_dir($outDir) && !mkdir($outDir, 0775, true)) {
    fwrite(STDERR, "Could not create out dir: {$outDir}\n");
    exit(3);
}

function httpGet(string $url): string {
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 20,
            'user_agent' => 'NationFiles-PeerReview-DepositFetcher/1.0'
        ]
    ]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data === false) {
        $err = error_get_last();
        throw new RuntimeException("GET failed: {$url} " . ($err['message'] ?? ''));
    }
    return $data;
}

$base = 'https://nationfiles.com/en';
$endpoints = [
    'country_nfsi_30d' => fn(string $iso) => "{$base}/country/{$iso}/nfsi/?export=json&chart=country_nfsi_30d",
    'country_snapshot' => fn(string $iso) => "{$base}/country/{$iso}/snapshot/?export=json&chart=country_nfsi_30d",
];

$manifest = [
    'generated_at_utc' => gmdate('c'),
    'source_domain' => 'nationfiles.com',
    'items' => []
];

foreach ($isos as $iso) {
    $iso = strtolower($iso);
    foreach ($endpoints as $key => $mkUrl) {
        $url = $mkUrl($iso);
        $raw = httpGet($url);
        $fname = "{$outDir}/{$iso}__{$key}.json";
        file_put_contents($fname, $raw);
        $sha = hash('sha256', $raw);
        $manifest['items'][] = [
            'iso2' => $iso,
            'key' => $key,
            'url' => $url,
            'path' => $fname,
            'sha256' => $sha,
            'bytes' => strlen($raw)
        ];
        fwrite(STDERR, "Wrote {$fname} ({$sha})\n");
    }
}

$mPath = "{$outDir}/MANIFEST.json";
file_put_contents($mPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
fwrite(STDERR, "Wrote {$mPath}\n");
exit(0);

?>

