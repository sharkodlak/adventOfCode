#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$sample = $argc == 1 ? '.sample' : '';
if ($sample) {
	echo "\n --->   SAMPLE   <---\n\n";
}
$inputFile = sprintf('%s/inputs/%02d%s.txt', __DIR__, basename(__FILE__, '.php'), $sample);
$lines = file($inputFile);
$crabs = explode(',', $lines[0]);
$min = min($crabs);
$max = max($crabs);
$average = (int) (array_sum($crabs) / count($crabs));

function countDistance(int $pivot, array $positions): int {
	$distance = 0;
	foreach ($positions as $position) {
		$distance += abs($position - $pivot);
	}
	return $distance;
}

$distanceFloor = countDistance($average, $crabs);
$distanceCeil = countDistance($average + 1, $crabs);
$isFlooDistanceLower = $distanceCeil - $distanceFloor > 0;
$minDistance = $isFlooDistanceLower ? $distanceFloor : $distanceCeil;
$modify = $isFlooDistanceLower ? fn($i) => --$i : fn($i) => ++$i;
$position = $isFlooDistanceLower ? $average : $average + 1;

for ($i = $modify($position); $i >= $min && $i <= $max; $i = $modify($i)) {
	$distance = countDistance($i, $crabs);
	if ($distance > $minDistance) {
		break;
	}

	$position = $i;
	$minDistance = $distance;
}

printf("Least fuel expensive position is %d with fuel cost %d .\n", $position, $minDistance);

class Crabs {
	// 0, 1, 2, 3,  4,  5,  6
	// 0, 1, 3, 6, 10, 15, 21
	private array $distanceFuel = [0];

	public function __construct(private array $crabs) {}

	function countFuelIncremental(int $pivot): int {
		$fuel = 0;
		foreach ($this->crabs as $position) {
			$distance = abs($position - $pivot);
			$df = $this->getDistanceFuel($distance);
			$fuel += $df;
		}
		return $fuel;
	}

	private function getDistanceFuel(int $distance): int {
		if (!isset($this->distanceFuel[$distance])) {
			for ($i = 1 + array_key_last($this->distanceFuel); $i <= $distance; ++$i) {
				$this->distanceFuel[$i] = $this->distanceFuel[$i - 1] + $i;
			}
		}
		return $this->distanceFuel[$distance];
	}
}

$pivotFloor = intval(count($crabs) / 2);
$pivotCeil = $pivotFloor + 1;
$crabs = new Crabs($crabs);
$distanceFloor = $crabs->countFuelIncremental($pivotFloor);
$distanceCeil = $crabs->countFuelIncremental($pivotCeil);
$isFlooDistanceLower = $distanceCeil - $distanceFloor > 0;
$minDistance = $isFlooDistanceLower ? $distanceFloor : $distanceCeil;
$modify = $isFlooDistanceLower ? fn($i) => --$i : fn($i) => ++$i;
$position = $isFlooDistanceLower ? $pivotFloor : $pivotCeil;

for ($i = $modify($position); $i >= $min && $i <= $max; $i = $modify($i)) {
	$distance = $crabs->countFuelIncremental($i);
	if ($distance > $minDistance) {
		break;
	}

	$position = $i;
	$minDistance = $distance;
}

printf("Least fuel expensive position is %d with fuel cost %d .\n", $position, $minDistance);
