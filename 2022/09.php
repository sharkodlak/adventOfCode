#!/usr/bin/env php
<?php declare(strict_types=1);

use Directory as GlobalDirectory;

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$start = ['x' => 0, 'y' => 0];
$ropes = [
	'short' => array_fill(0, 2, $start),
	'long' => array_fill(0, 10, $start),
];
$ropesVisited = [];

foreach ($inputLoader as $y => $line) {
	[$direction, $steps] = explode(' ', $line);

	foreach ($ropes as $ropeName => &$rope) {
		for ($i = 0; $i < $steps; ++$i) {
			switch ($direction) {
				case 'R':
					$rope[0]['x']++;
				break;
				case 'L':
					$rope[0]['x']--;
				break;
				case 'U':
					$rope[0]['y']++;
				break;
				case 'D':
					$rope[0]['y']--;
				break;
			}

			for ($knot = 1; $knot < count($rope); ++$knot) {
				$diffX = $rope[$knot - 1]['x'] - $rope[$knot]['x'];
				$diffY = $rope[$knot - 1]['y'] - $rope[$knot]['y'];
		
				if (abs($diffX) > 1) {
					$rope[$knot]['x'] += $diffX > 0 ? 1 : -1;
					
					if ($diffY !== 0) {
						$rope[$knot]['y'] += $diffY > 0 ? 1 : -1;
					}
				} else if (abs($diffY) > 1) {
					$rope[$knot]['y'] += $diffY > 0 ? 1 : -1;
					
					if ($diffX !== 0) {
						$rope[$knot]['x'] += $diffX > 0 ? 1 : -1;
					}
				}
			}

			$tail = end($rope);
			$ropesVisited[$ropeName][$tail['x']][$tail['y']] = true;
			//\adventOfCode\lib\Dumper::dump($head);
		}
	}
}

//var_dump($ropes);

$visitedCoordinates = array_reduce($ropesVisited['short'], fn($carry, array $row) => $carry + count($row), 0);
printf("Number of visited coordinates: %d .\n", $visitedCoordinates);
$visitedCoordinates = array_reduce($ropesVisited['long'], fn($carry, array $row) => $carry + count($row), 0);
printf("Number of visited coordinates for long rope: %d .\n", $visitedCoordinates);
