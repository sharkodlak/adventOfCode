#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$input = $argc == 1 ? '' : ".{$argv[1]}";
if ($argc != 1) {
	echo "\n --->   SAMPLE   <---\n\n";
}
$inputFile = sprintf('%s/inputs/%02d%s.txt', __DIR__, basename(__FILE__, '.php'), $input);
$lines = file($inputFile);
$map = [];
$lowPointsSum = 0;
$lowPoints = [];

foreach ($lines as $line) {
	$map[] = str_split(trim($line));
}

class Neighbours {
	private const CELL_VALUE = 1;
	private array $areas = [];
	public function __construct(private array $map) {}

	public function check(int $x, int $y): bool {
		$height = $this->map[$y][$x];
		$neighbours = self::getNeighbours($x, $y);
	
		foreach ($neighbours as $ny => $neighboursRow) {
			foreach ($neighboursRow as $nx => $neighbourHeight) {
				if ($neighbourHeight <= $height) {
					return false;
				}
			}
		}
	
		return true;
	}

	public function getNeighbours(int $x, int $y): array {
		$neighbours = [];
		if (isset($this->map[$y][$x - 1])) {
			$neighbours[$y][$x - 1] = $this->map[$y][$x - 1];
		}
		if (isset($this->map[$y][$x + 1])) {
			$neighbours[$y][$x + 1] = $this->map[$y][$x + 1];
		}
		if (isset($this->map[$y - 1][$x])) {
			$neighbours[$y - 1][$x] = $this->map[$y - 1][$x];
		}
		if (isset($this->map[$y + 1][$x])) {
			$neighbours[$y + 1][$x] = $this->map[$y + 1][$x];
		}
		return $neighbours;
	}

	public function addToArea(int $x, int $y): void {
		$areaKeys = $this->findArea($x, $y);

		if (empty($areaKeys)) {
			$this->areas[][$y][$x] = self::CELL_VALUE;
			printf("New area %d [%d, %d].\n", array_key_last($this->areas), $y, $x);
		} else {
			$this->areas[$areaKeys[0]][$y][$x] = self::CELL_VALUE;
			printf("  add to %d [%d, %d].\n", $areaKeys[0], $y, $x);

			if (count($areaKeys) === 2) {
				foreach ($this->areas[$areaKeys[1]] as $y => $row) {
					foreach ($row as $x => $cell) {
						$this->areas[$areaKeys[0]][$y][$x] = $cell;
					}
				}
				unset($this->areas[$areaKeys[1]]);
				printf("Merge area %d to %d.\n", $areaKeys[1], $areaKeys[0]);
			}
		}
	}
	
	private function findArea(int $x, int $y): array {
		$areaKeys = [];
		$checkCoordinates = [];

		if (isset($this->map[$y - 1][$x])) {
			$checkCoordinates[] = [$y - 1, $x];
		}
		if (isset($this->map[$y][$x - 1])) {
			$checkCoordinates[] = [$y, $x - 1];
		}

		foreach ($checkCoordinates as [$y, $x]) {
			foreach ($this->areas as $areaKey => $area) {
				if (isset($area[$y][$x]) && !in_array($areaKey, $areaKeys)) {
					$areaKeys[] = $areaKey;
				}
			}
		}

		sort($areaKeys);
		return $areaKeys;
	}

	public function getLargestAreasProduct(): int {
		$calculateRow = function (int $carry, array $row) {
			return $carry + array_sum($row);
		};
		$calculateArea = function ($area) use ($calculateRow) {
			return array_reduce($area, $calculateRow, 0);
		};
		$areaSizes = array_map($calculateArea, $this->areas);
		rsort($areaSizes);
		return $areaSizes[0] * $areaSizes[1] * $areaSizes[2];
	}
}

$neighbours = new Neighbours($map);

foreach ($map as $y => $row) {
	foreach ($row as $x => $height) {
		if ($neighbours->check($x, $y)) {
			$lowPointsSum += 1 + $height;
		}

		if ($height != 9) {
			$neighbours->addToArea($x, $y);
		}
	}
}

printf("Low points sum is %d .\n", $lowPointsSum);
printf("Largest areas product is %d .\n", $neighbours->getLargestAreasProduct());
