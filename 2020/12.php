#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');

interface Direction {
	public function turnLeft(): self;
	public function turnRight(): self;
	public function deltaX(int $amount): int;
	public function deltaY(int $amount): int;
}

class BaseDirection implements Direction {
	private $deltaX, $deltaY;
	private $left, $right;

	public function __construct(int $deltaX, int $deltaY) {
		$this->deltaX = $deltaX;
		$this->deltaY = $deltaY;
	}

	public function setLeft(Direction $left): self {
		$this->left = $left;
		return $this;
	}

	public function setRight(Direction $right): self {
		$this->right = $right;
		return $this;
	}

	public function turnLeft(): self {
		return $this->left;
	}

	public function turnRight(): self {
		return $this->right;
	}

	public function deltaX(int $amount): int {
		return $this->deltaX * $amount;
	}

	public function deltaY(int $amount): int {
		return $this->deltaY * $amount;
	}
}

class Delta {
	public $x;
	public $y;
	public function setX(int $x): self {
		$this->x = $x;
		return $this;
	}
	public function setY(int $y): self {
		$this->y = $y;
		return $this;
	}
}

class Heading {
	private static $directions = [];
	private $heading;
	public $delta;

	public function __construct(Delta $delta, string $heading = 'E') {
		$this->delta = $delta;
		self::$directions['E'] = new BaseDirection(1, 0);
		self::$directions['S'] = new BaseDirection(0, -1);
		self::$directions['W'] = new BaseDirection(-1, 0);
		self::$directions['N'] = new BaseDirection(0, 1);
		self::$directions['E']->setLeft(self::$directions['N'])->setRight(self::$directions['S']);
		self::$directions['S']->setLeft(self::$directions['E'])->setRight(self::$directions['W']);
		self::$directions['W']->setLeft(self::$directions['S'])->setRight(self::$directions['N']);
		self::$directions['N']->setLeft(self::$directions['W'])->setRight(self::$directions['E']);
		$this->heading = self::$directions[$heading];
	}

	public function __call(string $action, array $args): Delta {
		if (isset(self::$directions[$action])) {
			return self::forward(self::$directions[$action], $this->delta, ...$args);
		}
		switch ($action) {
			case 'B':
				return self::backward($this->heading, $this->delta, ...$args);
			case 'F':
				return self::forward($this->heading, $this->delta, ...$args);
			case 'L':
				$turns = $args[0] / 90;
				for (; $turns; --$turns) {
					$this->heading = $this->heading->turnLeft();
				}
				return $this->delta->setX(0)->setY(0);
			case 'R':
				$turns = $args[0] / 90;
				for (; $turns; --$turns) {
					$this->heading = $this->heading->turnRight();
				}
				return $this->delta->setX(0)->setY(0);
		}
	}

	static private function backward(Direction $direction, Delta $delta, int $amount): Delta {
		$delta = self::forward($delta, $amout);
		$delta->x *= -1;
		$delta->y *= -1;
		return $delta;
	}

	static private function forward(Direction $direction, Delta $delta, int $amount): Delta {
		$delta->x = $direction->deltaX($amount);
		$delta->y = $direction->deltaY($amount);
		return $delta;
	}

	public function getHeading(): string {
		return array_search($this->heading, self::$directions);
	}
}

class Navigator {
	private $heading;
	private $x = 0, $y = 0;

	public function __construct(Heading $heading) {
		$this->heading = $heading;
	}

	public function step(string $action, int $amount): self {
		$delta = $this->heading->$action($amount);
		$this->x += $delta->x;
		$this->y += $delta->y;
		//printf("[%d, %d] -> %s\n", $this->x, $this->y, $this->heading->getHeading());
		return $this;
	}

	public function getDistance(): int {
		return abs($this->x) + abs($this->y);
	}
}

$delta = new Delta;
$east = new Heading($delta, 'E');
$navigator = new Navigator($east);

foreach ($lines as $line) {
	$action = $line[0];
	$value = (int) substr($line, 1);
	$navigator->step($action, $value);
}

printf("Manhattan distance: %d .\n", $navigator->getDistance());
