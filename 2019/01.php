#!/usr/bin/env php
<?php declare(strict_types=1);

$lines = file(__DIR__ . '/01/input.txt');

$sum = array_reduce($lines, function($sum, $moduleMass) {
	$fuel = floor(intval($moduleMass) / 3) - 2;
	return $sum + $fuel;
}, 0);

echo "Sum of fuel is: $sum .\n";

$sum = array_reduce($lines, function($sum, $mass) {
	$mass = intval($mass);
	while ($mass >= 9) {
		$fuel = floor($mass / 3) - 2;
		$sum += $fuel;
		$mass = $fuel;
	}
	return $sum;
}, 0);

echo "Sum of fuel is: $sum .\n";
