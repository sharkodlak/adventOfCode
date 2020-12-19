#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');
$lines = [
	'1 + 2 * 3 + 4 * 5 + 6',
	'1 + (2 * 3) + (4 * (5 + 6))',
];
$sum = 0;

class Calc {
	private static $ops;

	public function __construct() {
		self::$ops = [
			'+' => fn($a, $b) => $a + $b,
			'*' => fn($a, $b) => $a * $b,
		];
	}

	public function eval($a, $op, $b) {
		return self::$ops[$op]($a, $b);
	}

	public function tokenize(string $line) {
		$i = 0;
		return $this->subtokenize($line, $i);
	}

	private function subtokenize(string $line, int &$i) {
		$tokens = [];
		$current = &$tokens[];
		for (; $i < strlen($line); ++$i) {
			$c = $line[$i];
			if ($c == ' ') {
				$current = &$tokens[];
			} else if ($c == '(') {
				++$i;
				$current = self::subtokenize($line, $i);
			} else if ($c == ')') {
				break;
			} else {
				$current .= $line[$i];
			}
		}
		return $tokens;
	}
}

$calc = new Calc;

foreach ($lines as $line) {
	var_dump($calc->tokenize($line));
}

printf("Sum of resulting values: %d .\n", $sum);
