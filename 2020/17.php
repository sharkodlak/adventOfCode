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

class Rules {
	private static $rules = [
		false => [3],
		true => [2, 3],
	];
	private static $delta = [-1, 0, 1];
	private $dimension;
	private $numberOfDimensions;
	private $relatives;

	public function __construct(int $numberOfDimensions, Dimension $dimension) {
		$this->numberOfDimensions = $numberOfDimensions;
		$this->dimension = $dimension;
		$this->setRelatives($numberOfDimensions);
	}

	private function setRelatives(int $numberOfDimensions): void {
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

	public function getCoordinates(array $space): array {
		return $this->dimension->getCoordinates($space, $this->numberOfDimensions);
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

	public function getCell(array $space, array $coordinates) {
		foreach ($coordinates as $d) {
			if (!isset($space[$d])) {
				return null;
			}
			$space = &$space[$d];
		}
		return $space;
	}

	public function &getCellReference(array &$space, array $coordinates) {
		foreach ($coordinates as $d) {
			$space = &$space[$d];
		}
		return $space;
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
		$activeCoordinates = $this->rules->getCoordinates($this->space);
		foreach ($activeCoordinates as $coordinates) {
			$coordinates = array_reverse($coordinates);
			foreach ($this->cell->getNeighbourCoordinates(...$coordinates) as $neighbourCoordinates) {
				$neighboursCountPointer = &$this->cell->getCellReference($neighboursCount, $neighbourCoordinates);
				/*
				if ($neighbourCoordinates == [0, 0, 0]) var_dump($neighboursCountPointer);
				$neighboursCountPointer = &$neighboursCount;
				foreach ($neighbourCoordinates as $d) {
					$neighboursCountPointer = &$neighboursCountPointer[$d];
				}
				if ($neighbourCoordinates == [0, 0, 0]) var_dump($neighboursCountPointer);
				*/
				if (!isset($neighboursCountPointer)) {
					$neighboursCountPointer = 0;
				}
				++$neighboursCountPointer;
			}
		}
		return $neighboursCount;
	}

	public function modifySpace(array $allNeighboursCount) {
		$spaceTurn = $this->space;
		$this->space = [];
		$neighbourCoordinates = $this->rules->getCoordinates($allNeighboursCount);
		foreach ($neighbourCoordinates as $coordinates) {
			$reverseCoordinates = array_reverse($coordinates);
			$activeNeighboursCount = $this->cell->getCell($allNeighboursCount, $coordinates);
			$isActive = !empty($this->cell->getCell($spaceTurn, $coordinates));
			$willBeActive = $this->rules->willBeActive($isActive, $activeNeighboursCount);
			if ($willBeActive) {
				$this->activate(...$reverseCoordinates);
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
$dimension = new Dimension;
$rules3D = new Rules(3, $dimension);
//$rules4D = new Rules(4, $dimension);
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
