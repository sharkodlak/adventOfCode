#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = [
	'<x=-1, y=0, z=2>',
	'<x=2, y=-10, z=-7>',
	'<x=4, y=-8, z=8>',
	'<x=3, y=5, z=-1>',
];
$lines = [
	'<x=-8, y=-10, z=0>',
	'<x=5, y=5, z=10>',
	'<x=2, y=-7, z=3>',
	'<x=9, y=-8, z=-3>',
];
$lines = file(substr(__FILE__, 0, -4) . '/input.txt', FILE_IGNORE_NEW_LINES);
$moonNames = ['Io', 'Europa', 'Ganymede', 'Callisto'];
$gravityPairs = [
	'Io' => ['Europa', 'Ganymede', 'Callisto'],
	'Europa' => ['Ganymede', 'Callisto'],
	'Ganymede' => ['Callisto'],
];
$zeroVelocity = [
	'x' => 0,
	'y' => 0,
	'z' => 0,
];
$coordinates = array_keys($zeroVelocity);
$repeats = $zeroVelocity;

foreach ($lines as $i => $line) {
	preg_match('~<x=(-?\d+), y=(-?\d+), z=(-?\d+)>~', $line, $matches);
	$moons[$moonNames[$i]] = [
		'position' => [
			'x' => intval($matches[1]),
			'y' => intval($matches[2]),
			'z' => intval($matches[3]),
		],
		'velocity' => $zeroVelocity,
	];
}

function totalEnergy(array $moons): int {
	foreach ($moons as $name => $moon) {
		$potential = array_sum(array_map('abs', $moon['position']));
		$kinetic = array_sum(array_map('abs', $moon['velocity']));
		$energies[$name] = $potential * $kinetic;
	}
	return array_sum($energies);
}

$init = $moons;
$i = 1;
$startTime = microtime(true);
$lastTime = 0;
do {
	foreach ($gravityPairs as $first => $others) {
		foreach ($others as $second) {
			foreach ($coordinates as $coordinate) {
				$delta = $moons[$first]['position'][$coordinate] - $moons[$second]['position'][$coordinate];
				if ($delta < 0) {
					++$moons[$first]['velocity'][$coordinate];
					--$moons[$second]['velocity'][$coordinate];
				} else if ($delta > 0) {
					++$moons[$second]['velocity'][$coordinate];
					--$moons[$first]['velocity'][$coordinate];
				}
			}
		}
	}

	foreach ($coordinates as $coordinate) {
		$isSame = true;
		foreach ($moons as $name => $moon) {
			$moons[$name]['position'][$coordinate] += $moon['velocity'][$coordinate];
			if ($moons[$name]['position'][$coordinate] != $init[$name]['position'][$coordinate]) {
				$isSame = false;
			}
		}
		if ($isSame) {
			$repeats[$coordinate] = $repeats[$coordinate] ?: $i + 1;
		}
	}

	if ($i % 1000 == 0) {
		if ($i == 1000) {
			echo "Total energy after ", $i, " steps is ", totalEnergy($moons), " .\n";
		}
		$time = floor(microtime(true) - $startTime);
		if ($time > $lastTime) {
			printf("[@%ds]: %d steps\n", floor($time), $i);
			$lastTime = $time;
		}
	}
	++$i;
} while ($moons != $init && in_array(0, $repeats));

echo "Stopped after $i steps, repeats are [", implode(', ', $repeats), "].\n";

$lcm = \Calc\Arithmetic\ArithmeticFunctions::lcmm(array_values($repeats));
echo "Steps until universe repeats itself is: $lcm .\n";
