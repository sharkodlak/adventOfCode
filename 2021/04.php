#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$inputFile = sprintf('%s/inputs/%02d%s.txt',
	__DIR__,
	basename(__FILE__, '.php'),
	$argc == 1 ? '.sample' : ''
);
$lines = file($inputFile);
$drawnNumbers = explode(',', array_shift($lines));
$drawnNumbers = array_map('intval', $drawnNumbers);

class Board {
	private array $numbers = [];
	private array $cols = [0, 0, 0, 0, 0];
	private array $rows = [0, 0, 0, 0, 0];
	private bool $won = false;

	public function __construct(private int $boardNumber) {}

	public function addNumber(int $number): self {
		$this->numbers[] = $number;
		return $this;
	}

	public function drawn(int $number): self {
		$position = \array_search($number, $this->numbers, true);
		if ($position !== false) {
			unset($this->numbers[$position]);
			$row = \floor($position / 5);
			$col = $position % 5;
			//echo "b{$this->boardNumber}: $position = [$col, $row]\n";

			if (++$this->cols[$col] === 5 || ++$this->rows[$row] === 5) {
				$this->won = true;
			}
		}

		return $this;
	}
	
	public function won(): bool {
		return $this->won;
	}
	
	public function getScore(): int {
		return array_sum($this->numbers);
	}
}

$boards = [];
$lastBoard = null;

foreach ($lines as $line) {
	if (trim($line) === '') {
		$lastBoard = new Board(count($boards));
		$boards[] = $lastBoard;
	} else {
		for ($i = 0; $i < 5; ++$i) {
			$number = intval(substr($line, $i * 3, 2));
			$lastBoard->addNumber($number);
		}
	}
}

foreach ($drawnNumbers as $drawn) {
	//echo "number drawn: $drawn\n";
	foreach ($boards as $board) {
		$board->drawn($drawn);

		if ($board->won()) {
			$score = $board->getScore();
			break(2);
		}
	}
}

printf("Bingo final score is %d . Score is %d multiplied by %d last number.\n", $score * $drawn, $score, $drawn);

