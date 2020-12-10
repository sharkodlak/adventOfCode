#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');
$adapters = array_map(fn($a) => (int) $a, $lines);
sort($adapters);
$diffs = [1 => 0, 0, 0];
$device = 3 + max($adapters);
$adapters[] = $device;
$last = 0;

foreach ($adapters as $adapter) {
	$diff = $adapter - $last;
	$diffs[$diff]++;
	$last = $adapter;
}

printf("Joltage differences %d * %d = %d .\n", $diffs[1], $diffs[3], $diffs[1] * $diffs[3]);
