#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(__DIR__ . '/inputs/01.txt');
$last = $lines[0];
$lastWindow = $lines[0] + $lines[1] + $lines[2];
$increases = 0;
$windowIncreases = 0;

foreach ($lines as $i => $depth) {
	printf("%d%s\n", $depth, $depth > $last ? '+' : '');
	if ($depth > $last) {
		++$increases;
	}
	$last = $depth;
	if ($i >= 3) {
		$currentWindow = $lines[$i - 2] + $lines[$i - 1] + $lines[$i];
		if ($currentWindow > $lastWindow) {
			++$windowIncreases;
		}
		$lastWindow = $currentWindow;
	}
}

echo "Number of increases is: $increases .\n";
echo "Number of window increases is: $windowIncreases .\n";

