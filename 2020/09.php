#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');
$preambleLength = 25;
$numbers = [];

class XmasCypher {
	public static function canSum(array $numbers, int $sum): bool {
		$otherNumbers = $numbers;
		foreach ($numbers as $a) {
			$b = $sum - $a;
			array_shift($otherNumbers);
			if (in_array($b, $otherNumbers)) {
				return true;
			}
		}
		return false;
	}
}

foreach ($lines as $i => $line) {
	$sum = (int) $line;
	if ($i >= $preambleLength) {
		if (XmasCypher::canSum($numbers, $sum)) { // lze vypocitat ze 2 cisel v $numbers?
			unset($numbers[$i - $preambleLength]);
		} else {
			$incorrectNumber = $sum;
			break;
		}
	}
	$numbers[] = $sum;
}

echo "First incorrect value $incorrectNumber .\n";

for ($first = 0; $first < count($lines); ++$first) {
	$contiguous = [];
	$i = $first;
	while (array_sum($contiguous) < $incorrectNumber) {
		$contiguous[$i] = (int) $lines[$i++];
	}
	if (count($contiguous) > 1 && array_sum($contiguous) == $incorrectNumber) {
		break;
	}
}

$min = min($contiguous);
$max = max($contiguous);
printf("Encryption weakness %d + %d = %s .\n", $min, $max, $min + $max);
