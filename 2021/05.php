#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$sample = $argc == 1 ? '.sample' : '';
if ($sample) {
	echo "\n --->   SAMPLE   <---\n\n";
}
$inputFile = sprintf('%s/inputs/%02d%s.txt', __DIR__, basename(__FILE__, '.php'), $sample);
$lines = file($inputFile);
$map = [];
$sum = 0;

foreach ($lines as $line) {
	preg_match('~^(\d+),(\d+) -> (\d+),(\d+)$~', $line, $matches);

	if ($matches[1] === $matches[3] || $matches[2] === $matches[4]) {
		foreach (range($matches[1], $matches[3]) as $x) {
			foreach (range($matches[2], $matches[4]) as $y) {
				$map[$x][$y] = 1 + ($map[$x][$y] ?? 0);
			}
		}
	}
}

foreach ($map as $x => $col) {
	foreach ($col as $y => $value) {
		if ($value > 1) {
			++$sum;
		}
	}
}

printf("Overlaping fields sum %d .\n", $sum);

