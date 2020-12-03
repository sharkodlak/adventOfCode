#!/usr/bin/env php
<?php declare(strict_types=1);

$lines = file(__DIR__ . '/01/input.txt');
sort($lines, SORT_NUMERIC);
$lines = array_map('intval', $lines);

function find($sum, $items, $haystack) {
	if ($items == 1) {
		return in_array($sum, $haystack) ? [$sum] : null;
	} else if ($items-- > 1) {
		foreach ($haystack as $a) {
			$b = $sum - $a;
			if ($b > 0) {
				$find = find($b, $items, $haystack);
				if (!empty($find)) {
					array_unshift($find, $a);
					return $find;
				}
			} else {
				return null;
			}
		}
	}
	return null;
}

function array_multiply($stack) {
	$multiply = fn($carry, $i) => $carry * $i;
	return array_reduce($stack, $multiply, 1);
}


$find = find(2020, 2, $lines);
$multiplication = array_multiply($find);
echo "{$find[0]} * {$find[1]} is: $multiplication .\n";

$find = find(2020, 3, $lines);
$multiplication = array_multiply($find);
echo "{$find[0]} * {$find[1]} * {$find[2]} is: $multiplication .\n";
