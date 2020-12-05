#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');
$boardingPasses = [];

foreach ($lines as $bp => $line) {
	$boardingPasses[$bp] = 0;
	for ($i = 0; $i < 10; ++$i) {
		if (in_array($line[$i], ['B', 'R'])) {
			$boardingPasses[$bp] |= 1;
		}
		if ($i != 9) {
			$boardingPasses[$bp] = $boardingPasses[$bp] << 1;
		}
	}
}

$max = max($boardingPasses);

echo "Max seat ID: $max .\n";

$min = $max - count($boardingPasses) - 1;
for ($i = $max; $i > $min; --$i) {
	if (!in_array($i, $boardingPasses)) {
		$missing = $i;
		break;
	}
}

echo "Missing seat ID: $missing .\n";
