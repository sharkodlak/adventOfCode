#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$fp = fopen(substr(__FILE__, 0, -4) . '/input.txt', 'r');
$layers = [];
$layerNr = 0;
$w = 25;
$h = 6;
$layerChars = $w * $h;

while (is_numeric($d = fgetc($fp))) {
	if (isset($layers[$layerNr]) && count($layers[$layerNr]) == $layerChars) {
		++$layerNr;
	}
	$layers[$layerNr][] = intval($d);
}

$minZeroDigits = PHP_INT_MAX;
$minLayerNr = null;
foreach ($layers as $layerNr => $layer) {
	$zeroDigits = count(array_intersect($layer, [0]));
	if ($zeroDigits < $minZeroDigits) {
		$minZeroDigits = $zeroDigits;
		$minLayerNr = $layerNr;
	}
}

$ones = count(array_intersect($layers[$minLayerNr], [1]));
$twos = count(array_intersect($layers[$minLayerNr], [2]));

echo "Checksum is $ones * $twos = ", $ones * $twos, " .\n";

$image = [];
$row = 0;
for ($i = 0; $i < $layerChars; ++$i) {
	if ($i % $w == 0) {
		++$row;
		$image[$row] = '';
	}
	foreach ($layers as $layer) {
		if ($layer[$i] < 2) {
			$image[$row] .= $layer[$i] ? 'X' : ' ';
			break;
		}
	}
}

foreach ($image as $row) {
	echo "$row\n";
}
