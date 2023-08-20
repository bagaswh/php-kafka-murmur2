<?php

require_once __DIR__ . '/hash.php';
require_once __DIR__ . '/hash_32.php';

function read_lines($handle, int $linesNum = 10)
{
    $result = [];
    while (1) {
        $line = fgets($handle);
        if (!$line) {
            break;
        }
        $result[] = $line;
        if (count($result) >= $linesNum) {
            break;
        }
    }
    return $result;
}

$file_handle_test_data = fopen(__DIR__ . '/testdata/random_strings', 'r');

$hash64_durations = [];
$hash32_durations = [];

while (1) {
    $lines = read_lines($file_handle_test_data, 1000);
    if (empty($lines)) {
        break;
    }
    for ($i = 0; $i < count($lines); $i++) {
        $line = trim($lines[$i]);

        // Benchmark hash3_64_int function execution time
        $hash64_start_time = microtime(true);
        $hash_64_val = hash3_64_int($line);
        $hash64_end_time = microtime(true);
        $hash64_durations[] = $hash64_end_time - $hash64_start_time;

        // Benchmark hash3_32_int function execution time
        $hash32_start_time = microtime(true);
        $hash_32_val = hash3_32_int($line);
        $hash32_end_time = microtime(true);
        $hash32_durations[] = $hash32_end_time - $hash32_start_time;

        echo "$line,$hash_32_val,$hash_64_val" . PHP_EOL;
    }
    unset($lines);
}

$hash64_duration_avg = array_sum($hash64_durations) / count($hash64_durations);
$hash64_duration_min = min($hash64_durations);
$hash64_duration_max = max($hash64_durations);
sort($hash64_durations);
$hash64_duration_percentiles = [
    99.9 => $hash64_durations[intval(count($hash64_durations) * 0.999)],
    99 => $hash64_durations[intval(count($hash64_durations) * 0.99)],
    95 => $hash64_durations[intval(count($hash64_durations) * 0.95)],
    90 => $hash64_durations[intval(count($hash64_durations) * 0.9)],
    50 => $hash64_durations[intval(count($hash64_durations) * 0.5)],
];

$hash32_duration_avg = array_sum($hash32_durations) / count($hash32_durations);
$hash32_duration_min = min($hash32_durations);
$hash32_duration_max = max($hash32_durations);
sort($hash32_durations);
$hash32_duration_percentiles = [
    99.9 => $hash32_durations[intval(count($hash32_durations) * 0.999)],
    99 => $hash32_durations[intval(count($hash32_durations) * 0.99)],
    95 => $hash32_durations[intval(count($hash32_durations) * 0.95)],
    90 => $hash32_durations[intval(count($hash32_durations) * 0.9)],
    50 => $hash32_durations[intval(count($hash32_durations) * 0.5)],
];

print_r("Hash64 stats:\n");
print_r("Average: $hash64_duration_avg\n");
print_r("Min: $hash64_duration_min\n");
print_r("Max: $hash64_duration_max\n");
print_r("Percentiles:\n");
print_r($hash64_duration_percentiles);
print_r("\n");

print_r("Hash32 stats:\n");
print_r("Average: $hash32_duration_avg\n");
print_r("Min: $hash32_duration_min\n");
print_r("Max: $hash32_duration_max\n");
print_r("Percentiles:\n");
print_r($hash32_duration_percentiles);
print_r("\n");
