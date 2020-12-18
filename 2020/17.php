#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

class Rules {
	private static $rules = [
		false => [3],
		true => [2, 3],
	];
	private static $delta = [-1, 0, 1];
	private $relatives;

	public function __construct(int $numberOfCoordinates) {
		$relatives = [];
		for (; $numberOfCoordinates; --$numberOfCoordinates) {
			$previousDimensionRelatives = $relatives;
			$relatives = [];
			foreach (self::$delta as $i) {
				if (empty($previousDimensionRelatives)) {
					$relatives[] = $i;
				} else {
					foreach ($previousDimensionRelatives as $pdr) {
						$pdr = (array) $pdr;
						$relatives[] = [$i, ...$pdr];
					}
				}
			}
		}
		$middle = floor(count($relatives) / 2);
		unset($relatives[$middle]);
		$this->relatives = $relatives;
	}

	public function willBeActive(bool $isActive, int $activeNeighbours): bool {
		return in_array($activeNeighbours, self::$rules[$isActive]);
	}

	public function getNeighbourRelativeCoordinates(): array {
		return $this->relatives;
	}
}

class Cell {
	private $rules;

	public function __construct(Rules $rules) {
		$this->rules = $rules;
	}

	public function getNeighbourCoordinates($x, $y, $z) {
		$neighbourCoordinates = [];
		foreach ($this->rules->getNeighbourRelativeCoordinates() as $n => [$dz, $dy, $dx]) {
			$neighbourCoordinates[$n] = [$z + $dz, $y + $dy, $x + $dx];
		}
		return $neighbourCoordinates;
	}
}

class Space {
	private $cell;
	private $rules;
	private $space = [];

	public function __construct(Rules $rules, Cell $cell) {
		$this->rules = $rules;
		$this->cell = $cell;
	}

	public function activate(int $x, int $y, int $z) {
		$this->space[$z][$y][$x] = 1;
	}

	public function step() {
		$neighboursCount = $this->getNeighboursCount();
		$this->modifySpace($neighboursCount);
	}

	public function getNeighboursCount(): array {
		$neighboursCount = [];
		foreach ($this->space as $z => $plane) {
			foreach ($plane as $y => $row) {
				foreach ($row as $x => $cell) {
					foreach ($this->cell->getNeighbourCoordinates($x, $y, $z) as [$nz, $ny, $nx]) {
						if (isset($neighboursCount[$nz][$ny][$nx])) {
							++$neighboursCount[$nz][$ny][$nx];
						} else {
							$neighboursCount[$nz][$ny][$nx] = 1;
						}
					}
				}
			}
		}
		return $neighboursCount;
	}

	public function modifySpace(array $neighboursCount) {
		$spaceTurn = $this->space;
		$this->space = [];
		foreach ($neighboursCount as $z => $plane) {
			foreach ($plane as $y => $row) {
				foreach ($row as $x => $activeNeighboursCount) {
					$isActive = isset($spaceTurn[$z][$y][$x]);
					$willBeActive = $this->rules->willBeActive($isActive, $activeNeighboursCount);
					if ($willBeActive) {
						//echo "activate: $z, $y, $x\n";
						$this->activate($x, $y, $z);
					}
				}
			}
		}
	}

	public function countActiveCells(): int {
		$activeCells = 0;
		foreach ($this->space as $z => $plane) {
			foreach ($plane as $y => $row) {
				$activeCells += array_sum($row);
			}
		}
		return $activeCells;
	}
}

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');
$minus = -floor(count($lines) / 2);
$x = $y = $z = 0;
$rules3D = new Rules(3);
//$rules4D = new Rules(4);
$cell = new Cell($rules3D);
$space = new Space($rules3D, $cell);

foreach ($lines as $y => $line) {
	for ($x = 0; $x < strlen($line); ++$x) {
		if ($line[$x] == '#') {
			$space->activate($x, $y, $z);
		}
	}
}

for ($i = 0; $i < 6; ++$i) {
	$space->step();
}

printf("Number of active cubes: %d .\n", $space->countActiveCells());
