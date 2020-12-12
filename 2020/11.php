#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$fp = fopen(substr(__FILE__, 0, -4) . '/input.txt', 'r');
$board = [];
$x = 0;
$y = 0;

while (false !== ($c = fgetc($fp))) {
	if (PHP_EOL == $c) {
		++$y;
		$x = 0;
	} else {
		if ($c == 'L') {
			$board[$y][$x] = 0;
		}
		++$x;
	}
}

class Seats {
	private $d;
	private $seats = [];
	private $rules;
	private $maxY, $maxX;
	private $neighbours;

	public function __construct(array $seats, Rules $rules, Display $d) {
		$this->seats = $seats;
		$this->rules = $rules;
		$this->maxY = max(array_keys($seats));
		$this->maxX = max(array_map(fn($row) => max(array_keys($row)), $seats));
		$this->rules->setBoardSize($this->maxY, $this->maxX);
		$this->setNeighbours();
		$this->d = $d->setBoardSize($this->maxY, $this->maxX);
	}

	private function setNeighbours(): void {
		//$fp = fopen("php://stdin", "r");
		foreach ($this->seats as $y => $row) {
			foreach ($row as $x => $occupied) {
				$neighbourCoordinates = $this->getNeighbourCoordinates($y, $x);
				//$this->inspect($y, $x, $neighbourCoordinates);
				//$c = fgetc($fp);
				//if ($c != "\n") {
				//	exit;
				//}
				$this->neighbours[$y][$x] = $neighbourCoordinates;
			}
		}
	}

	private function inspect(int $currentY, int $currentX, array $neighbourCoordinates) {
		$string = '';
		for ($y = 0; $y <= 10; ++$y) {
			for ($x = 0; $x <= $this->maxX; ++$x) {
				if ($currentY == $y && $currentX == $x) {
					$string .= "\033[0;32mo\033[0m";
				} else if (in_array([$y, $x], $neighbourCoordinates)) {
					$string .= "\033[1;33mX\033[0m";
				} else {
					$string .= isset($this->seats[$y][$x]) ? ($this->seats[$y][$x] ? '#' : 'L') : '.';
				}
			}
			$string .= "\n";
		}
		echo "[$currentY, $currentX]\n$string\n";
	}

	public function step(): bool {
		$seatsToModify = [];
		foreach ($this->neighbours as $y => $row) {
			foreach ($row as $x => $neighbourCoordinates) {
				$occupiedNeighbourSeats = 0;
				foreach ($neighbourCoordinates as $nY => $nRow) {
					foreach (array_keys($nRow) as $nX) {
						$occupiedNeighbourSeats += $this->seats[$nY][$nX];
					}
				}
				$emptySeat = empty($this->seats[$y][$x]);
				if ($this->rules->modify($emptySeat, $occupiedNeighbourSeats)) {
					$seatsToModify[$y][$x] = true;
				}
			}
		}
		$modified = !empty($seatsToModify);
		$this->d->setSeats($this->seats)->setSeatsToModify($seatsToModify)->show();
		foreach ($seatsToModify as $y => $row) {
			foreach (array_keys($row) as $x) {
				$this->seats[$y][$x] = (int) !$this->seats[$y][$x];
			}
		}
		return $modified;
	}

	private function getNeighbourCoordinates(int $y, int $x): array {
		return $this->rules->getNeighbourCoordinates($this->seats, $y, $x);
	}

	public function loop(): int {
		$i = 0;
		while ($this->step()) {
			++$i;
		}
		return $i;
	}

	public function occupied(): int {
		return array_reduce($this->seats, fn($sum, $a) => $sum + array_sum($a), 0);
	}

	public function __toString(): string {
		return (string) $this->d->setSeats($this->seats);
	}
}

interface Display {
	public function setBoardSize(int $y, int $x): self;
	public function setSeats(array $seats): self;
	public function setSeatsToModify(array $seatsToModify): self;
	public function show(): self;
}

class NoDisplay implements Display {
	protected $maxY, $maxX;
	protected $seats, $seatsToModify;

	public function setBoardSize(int $y, int $x): self {
		$this->maxY = $y;
		$this->maxX = $x;
		return $this;
	}

	public function setSeats(array $seats): self {
		$this->seats = $seats;
		return $this;
	}

	public function setSeatsToModify(array $seatsToModify): self {
		$this->seatsToModify = $seatsToModify;
		return $this;
	}

	public function show(): self {
		return $this;
	}
}

class ColorDisplay extends NoDisplay {
	const RED = "\033[0;31m";
	const GREEN = "\033[0;32m";
	const YELLOW = "\033[1;33m";
	const NO_COLOR = "\033[0m";

	private function char(int $y, int $x) {
		if (isset($this->seats[$y][$x])) {
			if (isset($this->seatsToModify[$y][$x])) {
				return $this->seats[$y][$x]
					? self::RED . '#' . self::NO_COLOR
					: self::GREEN . 'L' . self::NO_COLOR;
			} else {
				return $this->seats[$y][$x] ? '#' : 'L';
			}
		} else {
			return '.';
		}
	}

	public function show(): self {
		echo $this;
		return $this;
	}

	public function __toString(): string {
		$string = '';
		for ($y = 0; $y <= $this->maxY; ++$y) {
			for ($x = 0; $x <= $this->maxX; ++$x) {
				$string .= $this->char($y, $x);
			}
			$string .= "\n";
		}
		return "$string\n";
	}
}

abstract class Rules {
	protected static $neighboursRelative = [
		[-1, -1], [-1, 0], [-1, 1],
		[ 0, -1],          [ 0, 1],
		[ 1, -1], [ 1, 0], [ 1, 1],
	];
	protected $maxY, $maxX;

	public function setBoardSize(int $y, int $x): void {
		$this->maxY = $y;
		$this->maxX = $x;
	}

	abstract public function getNeighbourCoordinates(array $seats, int $row, int $col): array;

	public function modify(bool $empty, int $occupiedNeighbourSeats): bool {
		return $empty ?
			$occupiedNeighbourSeats == 0 :
			$occupiedNeighbourSeats >= $this->getMinOccupiedSeatsToLeave()
		;
	}

	abstract protected function getMinOccupiedSeatsToLeave(): int;
}

class FirstStar extends Rules {
	const MIN_OCCUPIED_SEATS_TO_LEAVE = 4;

	public function getNeighbourCoordinates(array $seats, int $row, int $col): array {
		$neighbours = [];
		foreach (self::$neighboursRelative as [$dY, $dX]) {
			$y = $row + $dY;
			$x = $col + $dX;
			if (isset($seats[$y][$x])) {
				$neighbours[$y][$x] = true;
			}
		}
		return $neighbours;
	}

	protected function getMinOccupiedSeatsToLeave(): int {
		return self::MIN_OCCUPIED_SEATS_TO_LEAVE;
	}
}

class SecondStar extends Rules {
	const MIN_OCCUPIED_SEATS_TO_LEAVE = 5;

	public function getNeighbourCoordinates(array $seats, int $row, int $col): array {
		$neighbours = [];
		foreach (self::$neighboursRelative as [$dY, $dX]) {
			$y = $row;
			$x = $col;
			do {
				$y += $dY;
				$x += $dX;
				if (isset($seats[$y][$x])) {
					$neighbours[$y][$x] = true;
					break;
				}
			} while (0 <= $y && $y <= $this->maxY && 0 <= $x && $x <= $this->maxX);
		}
		return $neighbours;
	}

	protected function getMinOccupiedSeatsToLeave(): int {
		return self::MIN_OCCUPIED_SEATS_TO_LEAVE;
	}
}

$display = new NoDisplay;
$rules = new FirstStar();
$seats = new Seats($board, $rules, $display);
$seats->loop();
printf("Number of seats occupied: %d .\n", $seats->occupied());

$display = new ColorDisplay;
$rules = new SecondStar();
$seats = new Seats($board, $rules, $display);
$seats->loop();
printf("Number of seats occupied: %d .\n", $seats->occupied());
