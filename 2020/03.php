#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(__DIR__ . '/03/input.txt');
$lineLength = strlen($lines[0]) - 1;
$slopes = [
	[1, 1],
	[3, 1],
	[5, 1],
	[7, 1],
	[1, 2],
];
$trees = [0, 0, 0, 0, 0];

foreach ($slopes as $i => [$right, $down]) {
	//echo "Slope $i, right: $right, down: $down.\n";
	$x = 0;
	$y = 0;
	foreach ($lines as $row => $line) {
		//echo "Row: $row == Y: $y.\n";
		if ($row == $y) {
			if ($line[$x % $lineLength] === '#') {
				++$trees[$i];
			}
			$x += $right;
			$y += $down;
		}
	}
}


echo "Trees hit: {$trees[1]} .\n";
$sum = adventOfCode\Arrays::multiply($trees);
echo "Trees hit multiplied: $sum .\n";
