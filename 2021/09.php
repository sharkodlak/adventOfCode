#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$input = $argc == 1 ? '' : ".{$argv[1]}";
if ($argc != 1) {
	echo "\n --->   SAMPLE   <---\n\n";
}
$inputFile = sprintf('%s/inputs/%02d%s.txt', __DIR__, basename(__FILE__, '.php'), $input);
$lines = file($inputFile);
$map = [];
$lowPoints = 0;

foreach ($lines as $line) {
	$map[] = str_split(trim($line));
}

foreach ($map as $y => $row) {
	foreach ($row as $x => $height) {
		if (
			(!isset($map[$y][$x - 1]) || $map[$y][$x - 1] > $height)
			&& (!isset($map[$y][$x + 1]) || $map[$y][$x + 1] > $height)
			&& (!isset($map[$y - 1][$x]) || $map[$y - 1][$x] > $height)
			&& (!isset($map[$y + 1][$x]) || $map[$y + 1][$x] > $height)
		) {
			$lowPoints += 1 + $height;
		}
	}
}

printf("Low points sum is %d .\n", $lowPoints);
