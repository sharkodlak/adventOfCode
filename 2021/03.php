#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(__DIR__ . '/inputs/03.txt');
$bits = strlen(trim($lines[0]));
$gammaParts = array_fill(0, $bits, 0);
$mask = bindec(str_repeat('1', $bits));
$gamma = 0;
$epsilon = 0;
$oxygen = 0;
$co2 = 0;

foreach ($lines as $line) {
	for ($i = 0; $i < $bits; ++$i) {
		if ($line[$i] === '1') {
			++$gammaParts[$i];
		} else if ($line[$i] === '0') {
			--$gammaParts[$i];
		}
	}
}

foreach ($gammaParts as $i => $part) {
	$bit = $part > 0 ? 1 : 0;
	$gamma <<= 1;
	$gamma |= $bit;
}

$epsilon = ~$gamma & $mask;

function findGas($lines, $co2 = false) {
	$bits = strlen(trim($lines[0]));
	for ($i = 0; $i <= $bits; ++$i) {
		if (count($lines) == 1) {
			return bindec(current($lines));
		}
		$gammaBit = 0;
		foreach ($lines as $line) {
			if ($line[$i] === '1') {
				++$gammaBit;
			} else if ($line[$i] === '0') {
				--$gammaBit;
			}
		}
		$bit = $gammaBit >= 0 ? 1 : 0;
		if ($co2) {
			$bit = ~$bit & 1;
		}
		foreach ($lines as $key => $line) {
			if ($line[$i] !== "$bit") {
				unset($lines[$key]);
			}
		}
	}
}

$oxygen = findGas($lines);
$co2 = findGas($lines, true);

printf("Diagnostics are: gamma: %s, epsilon: %s. It's product is %d .\n", decbin($gamma), decbin($epsilon), $gamma * $epsilon);
printf("Diagnostics are: oxygen: %s, CO2: %s. It's product is %d .\n", decbin($oxygen), decbin($co2), $oxygen * $co2);

