#!/usr/bin/env php
<?php declare(strict_types=1);

$wires = file(substr(__FILE__, 0, -4) . '/input.txt', FILE_IGNORE_NEW_LINES);
$wires = array_map(function($wire) {
	return explode(',', $wire);
}, $wires);

$area = [];
$closestIntersection = PHP_INT_MAX;
$minStepsIntersection = PHP_INT_MAX;
$value = function(int $v): int {
	return $v;
};
$negate = function(int $v): int {
	return -$v;
};
foreach ($wires as $wireNr => $wire) {
	$x = 0;
	$y = 0;
	$step = 0;
	foreach ($wire as $run) {
		$direction = $run[0];
		$length = intval(substr($run, 1));
		$sign = $value;
		for ($i = 0; $i < $length; ++$i) {
			++$step;
			switch ($direction) {
				case 'L':
					$sign = $negate;
				case 'R':
					$x += $sign(1);
				break;
				case 'D':
					$sign = $negate;
				case 'U':
					$y += $sign(1);
				break;
				default:
					exit("Unknown direction $direction.\n");
			}
			$area[$x][$y][$wireNr] = $area[$x][$y][$wireNr] ?? $step;
			if (!empty($area[$x][$y]) && array_diff_key($area[$x][$y], [$wireNr => 1])) {
				$closestIntersection = min($closestIntersection, abs($x) + abs($y));
				$minStepsIntersection = min($minStepsIntersection, array_sum($area[$x][$y]));
			}
		}
	}
}

echo "Closest intersection is: $closestIntersection .\n";
echo "Min steps intersection is: $minStepsIntersection .\n";
