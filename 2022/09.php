#!/usr/bin/env php
<?php declare(strict_types=1);

use Directory as GlobalDirectory;

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$head = ['x' => 0, 'y' => 0];
$tail = ['x' => 0, 'y' => 0];
$visited = [];

foreach ($inputLoader as $y => $line) {
	[$direction, $steps] = explode(' ', $line);
	//var_dump($head, $direction, $steps);

	for ($i = 0; $i < $steps; ++$i) {
		switch ($direction) {
			case 'R':
				$head['x']++;
			break;
			case 'L':
				$head['x']--;
			break;
			case 'U':
				$head['y']++;
			break;
			case 'D':
				$head['y']--;
			break;
		}

		$diffX = $head['x'] - $tail['x'];
		$diffY = $head['y'] - $tail['y'];

		if (abs($diffX) > 1) {
			$tail['x'] += $diffX > 0 ? 1 : -1;
			
			if ($diffY !== 0) {
				$tail['y'] += $diffY > 0 ? 1 : -1;
			}
		} else if (abs($diffY) > 1) {
			$tail['y'] += $diffY > 0 ? 1 : -1;
			
			if ($diffX !== 0) {
				$tail['x'] += $diffX > 0 ? 1 : -1;
			}
		}

		$visited[$tail['x']][$tail['y']] = true;
		//\adventOfCode\lib\Dumper::dump($head);
	}
}

var_dump($head, $tail);

$visitedCoordinates = array_reduce($visited, fn($carry, array $row) => $carry + count($row), 0);
printf("Number of visited coordinates: %d .\n", $visitedCoordinates);
