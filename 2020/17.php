#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

class Rules {
	private static $rules = [
		false => [3],
		true => [2, 3],
	];
	private static $relatives = [
		[-1, -1, -1], [-1, -1, 0], [-1, -1, 1],
		[-1,  0, -1], [-1,  0, 0], [-1,  0, 1],
		[-1,  1, -1], [-1,  1, 0], [-1,  1, 1],

		[ 0, -1, -1], [ 0, -1, 0], [ 0, -1, 1],
		[ 0,  0, -1],        14 => [ 0,  0, 1],
		[ 0,  1, -1], [ 0,  1, 0], [ 0,  1, 1],

		[ 1, -1, -1], [ 1, -1, 0], [ 1, -1, 1],
		[ 1,  0, -1], [ 1,  0, 0], [ 1,  0, 1],
		[ 1,  1, -1], [ 1,  1, 0], [ 1,  1, 1],
	];

	public function willBeActive(bool $isActive, int $activeNeighbours): bool {
		return in_array($activeNeighbours, self::$rules[$isActive]);
	}

	public function getNeighbourRelativeCoordinates() {
		return self::$relatives;
	}
}

class Cube {
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
	private $cube;
	private $rules;
	private $space = [];

	public function __construct(Rules $rules, Cube $cube) {
		$this->rules = $rules;
		$this->cube = $cube;
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
				foreach ($row as $x => $cube) {
					foreach ($this->cube->getNeighbourCoordinates($x, $y, $z) as [$nz, $ny, $nx]) {
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

	public function countActiveCubes(): int {
		$activeCubes = 0;
		foreach ($this->space as $z => $plane) {
			foreach ($plane as $y => $row) {
				$activeCubes += array_sum($row);
			}
		}
		return $activeCubes;
	}
}

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');
$minus = -floor(count($lines) / 2);
$x = $y = $z = 0;
$rules = new Rules;
$cube = new Cube($rules);
$space = new Space($rules, $cube);

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

printf("Number of active cubes: %d .\n", $space->countActiveCubes());
