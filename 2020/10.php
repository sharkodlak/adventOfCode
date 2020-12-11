#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');
$adapters = array_map(fn($a) => (int) $a, $lines);
sort($adapters);
$diffs = [1 => 0, 0, 0];
$device = 3 + max($adapters);
$adapters[] = $device;
$last = 0;
$contiguous = 0;
$longest = 0;

$combinations = [
	2 => 2,
	3 => 4,
	4 => 7,
];
$combinationsCount = 1;

foreach ($adapters as $adapter) {
	$diff = $adapter - $last;
	$diffs[$diff]++;
	if ($diff == 1) {
		$longest = max($longest, ++$contiguous);
	} else {
		if ($contiguous > 1) {
			var_dump($combinationsCount, $contiguous, $combinations[$contiguous]);
			$combinationsCount *= $combinations[$contiguous];
		}
		$contiguous = 0;
	}
	$last = $adapter;
}

printf("Joltage differences %d * %d = %d .\n", $diffs[1], $diffs[3], $diffs[1] * $diffs[3]);

/*
(0), 1, 4, 5, 6, 7, 10, 11, 12, 15, 16, 19, (22)
(0), 1, 4, ...   7, 10, ... 12, 15, 16, 19, (22)
           4m           2m

(0), 1, 2, 3, 4, 7, 8, 9, 10, 11, 14, 17, 18, 19, 20, 23, 24, 25, 28, 31, 32, 33, 34, 35, 38, 39, 42, 45, 46, 47, 48, 49, (52)
(0), ...      4, 7,           11, 14, 17,         20, 23,     25, 28, 31,             35, 38, 39, 42, 45,             49, (52)
		 x, 2, 3
		 1, x, 3
		 1, 2, x
		 x, x, 3
		 x, 2, x
		 1, x, x
       7m              7m                     4m         2m                  7m                               7m

0, 1, 2, 3, 4, 5
   x, 2, 3, 4 (1 ze 4)
	 1, x, 3, 4
	 1, 2, x, 4
	 1, 2, 3, x

	 x, x, 3, 4 (2 ze 4)
	 x, 2, x, 4
	 x, 2, 3, x
	 1, x, x, 4
	 1, x, 3, x
	 1, 2, x, x

	 x, x, 3, x (3 ze 4)
	 x, 2, x, x
*/
echo "Number of combinations: $combinationsCount .\n";
