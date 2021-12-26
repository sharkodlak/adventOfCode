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
	$fnX = $matches[1] < $matches[3] ? fn($x) => ++$x : ($matches[1] > $matches[3] ? fn($x) => --$x : fn($x) => $x);
	$fnY = $matches[2] < $matches[4] ? fn($y) => ++$y : ($matches[2] > $matches[4] ? fn($y) => --$y : fn($y) => $y);

	for ($x = $matches[1], $y = $matches[2]; true; $x = $fnX($x), $y = $fnY($y)) {
		echo "$x,$y\n";
		$map[$x][$y] = 1 + ($map[$x][$y] ?? 0);
		if ($x == $matches[3] && $y == $matches[4]) {
			break;
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

