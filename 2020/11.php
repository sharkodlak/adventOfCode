#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$fp = fopen(substr(__FILE__, 0, -4) . '/input.txt', 'r');
$seats = [];
$x = 0;
$y = 0;

while (false !== ($c = fgetc($fp))) {
	if (PHP_EOL == $c) {
		++$y;
		$x = 0;
	} else {
		if ($c == 'L') {
			$seats[$y][$x] = 0;
		}
		++$x;
	}
}

class Seats {
	private $seats = [];
	private $rules;
	private static $neighboursRelative = [
		[-1, -1], [-1, 0], [-1, 1],
		[ 0, -1],          [ 0, 1],
		[ 1, -1], [ 1, 0], [ 1, 1],
	];

	public function __construct(array $seats, Rules $rules) {
		$this->seats = $seats;
		$this->rules = $rules;
	}

	public function getNeighbours(int $row, int $col): array {
		$neighbours = [];
		foreach (self::$neighboursRelative as [$dY, $dX]) {
			$y = $row + $dY;
			$x = $col + $dX;
			if (isset($this->seats[$y][$x])) {
				$neighbours[] = &$this->seats[$y][$x];
			}
		}
		return $neighbours;
	}

	public function step(): bool {
		$seatsToModify = [];
		foreach ($this->seats as $y => $row) {
			foreach ($row as $x => $occupied) {
				$neighbours = $this->getNeighbours($y, $x);
				$modify = $this->rules->modify(!$occupied, $neighbours);
				if ($modify) {
					$seatsToModify[] = [$y, $x];
				}
			}
		}
		$modified = !empty($seatsToModify);
		foreach ($seatsToModify as [$y, $x]) {
			$this->seats[$y][$x] = (int) !$this->seats[$y][$x];
		}
		return $modified;
	}

	public function loop(): void {
		while ($this->step()) {
			// empty
		}
	}

	public function occupied(): int {
		return array_reduce($this->seats, fn($sum, $a) => $sum + array_sum($a), 0);
	}
}

class Rules {
	public function modify(bool $empty, array $neighbours): bool {
		$occupiedNeighbourSeats = array_sum($neighbours);
		if ($empty) {
			return $occupiedNeighbourSeats == 0;
		} else {
			return $occupiedNeighbourSeats >= 4;
		}
	}
}

$rules = new Rules();
$seats = new Seats($seats, $rules);
$seats->loop();
printf("Number of seats occupied: %d .\n", $seats->occupied());
