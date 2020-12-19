#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

class Dimension {
	public static function getCoordinates($space, int $dimensions): array {
		$coordinates = [];
		--$dimensions;
		foreach ($space as $c => $inner) {
			if ($dimensions > 0) {
				foreach (self::getCoordinates($inner, $dimensions) as $innerCoordinates) {
					$coordinates[] = [$c, ...$innerCoordinates];
				}
			} else {
				$coordinates[] = [$c];
			}
		}
		return $coordinates;
	}
}

$s = [
	-1 => [
		-1 => [-1 => -11, 0 => -12, 1 => -13],
		 0 => [-1 => -14, 0 => -15, 1 => -16],
		 1 => [-1 => -17, 0 => -18, 1 => -19],
	],
	 0 => [
		-1 => [-1 => 1, 0 => 2, 1 => 3],
		 0 => [-1 => 4, 0 => 5, 1 => 6],
		 1 => [-1 => 7, 0 => 8, 1 => 9],
	],
	 1 => [
		-1 => [-1 => 11, 0 => 12, 1 => 13],
		 0 => [-1 => 14, 0 => 15, 1 => 16],
		 1 => [-1 => 17, 0 => 18, 1 => 19],
	],
];
$x = Dimension::getCoordinates($s, 3);
var_dump($x);
exit;

class Rules {
	private static $rules = [
		false => [3],
		true => [2, 3],
	];
	private static $delta = [-1, 0, 1];
	private $numberOfDimensions;
	private $relatives;

	public function __construct(int $numberOfDimensions) {
		$this->numberOfDimensions = $numberOfDimensions;
		$relatives = [];
		for (; $numberOfDimensions; --$numberOfDimensions) {
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

	public function getNumberOfDimensions(): int {
		return $this->numberOfDimensions;
	}
}

class Cell {
	private $rules;

	public function __construct(Rules $rules) {
		$this->rules = $rules;
	}

	public function getNeighbourCoordinates(int ...$dimensions) {
		$dimensions = array_reverse($dimensions);
		$neighbourCoordinates = [];
		foreach ($this->rules->getNeighbourRelativeCoordinates() as $n => $relativeDimensions) {
			$neighbourDimensions = array_map(fn($d, $r) => $d + $r, $dimensions, $relativeDimensions);
			$neighbourCoordinates[$n] = $neighbourDimensions;
		}
		return array_reverse($neighbourCoordinates);
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

	public function activate(int ...$dimensions) {
		$dimensions = array_reverse($dimensions);
		$narrowSpace = &$this->space;
		foreach ($dimensions as $d) {
			$narrowSpace = &$narrowSpace[$d];
		}
		$narrowSpace = 1;
	}

	public function step() {
		$neighboursCount = $this->getNeighboursCount();
		$this->modifySpace($neighboursCount);
	}

	public function getNeighboursCount(): array {
		$numberOfDimensions = $this->rules->getNumberOfDimensions();
		$neighboursCount = [];
		$narrowSpace = &$this->space;
		for (; $numberOfDimensions; --$numberOfDimensions) {

			foreach ($plane as $y => $row) {
				foreach ($row as $x => $cell) {
					foreach ($this->cell->getNeighbourCoordinates($x, $y, $z) as $neighbourDimensions) {
						if (isset($neighboursCount[$neighbourDimensions[0]][$neighbourDimensions[1]][$neighbourDimensions[2]])) {
							++$neighboursCount[$neighbourDimensions[0]][$neighbourDimensions[1]][$neighbourDimensions[2]];
						} else {
							$neighboursCount[$neighbourDimensions[0]][$neighbourDimensions[1]][$neighbourDimensions[2]] = 1;
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
