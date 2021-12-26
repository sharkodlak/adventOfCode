#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(__DIR__ . '/inputs/04.sample.txt');
$numbersDrawn = explode(',', array_shift($lines));

class Board {
	private array $rows = [];

	public function setRow(array $numbers): self {
		$this->rows[] = $numbers;
		return $this;
	}
}

$lastBoard = null;

foreach ($lines as $line) {
	if ($lines === '\n') {
		exit('asdfghj');
	}
}

printf("Bingo final score is %d . It's score is %d multiplied by %d lest number.\n", $score * $number, $score, $number);

