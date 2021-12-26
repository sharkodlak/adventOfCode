#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = [
	"4" => [4],
	"( 3 )" => [[3]],
	"1 + 2 * 3 + 4 * 5 + 6\n" => [[[[[1, '+', 2], '*', 3], '+', 4], '*', 5], '+', 6],
		/*
		//[[[1 + 2] * [3 + 4]] * [5 + 6]],
	"1 + (2 * 3) + (4 * (5 + 6))\n" =>
		[[1 + [2 * 3]] + [4 * [5 + 6]]],
	'2 * 3 + (4 * 5)' =>
		[[2 * 3] + [4 * 5]],
		//[2 * [3 + [4 * 5]]],
	'5 + (8 * 3 + 9 + 3 * 4 * 3)' =>
		[5 + [[[[[8 * 3] + 9] + 3] * 4] * 3]],
		//[5 + [[[8 * [[3 + 9] + 3]] * 4] * 3]],
	'5 * 9 * (7 * 3 * 3 + 9 * 3 + (8 + 6 * 4))' =>
		[[5 * 9] * [[[[[7 * 3] * 3] + 9] * 3] + [[8 + 6] * 4]]],
		//[[5 * 9] * [[[7 * 3] * [3 + 9]] * [3 + [[8 + 6] * 4]]]],
	'((2 + 4 * 9) * (6 + 9 * 8 + 6) + 6) + 2 + 4 * 2' =>
		[[[[[[[2 + 4] * 9] * [[[6 + 9] * 8] + 6]] + 6] + 2] + 4] * 2],
		//[[[[[[2 + 4] * 9] * [[[6 + 9] * [8 + 6]] + 6]] + 2] + 4] * 2],
	*/
];
//$lines = file(substr(__FILE__, 0, -4) . '/input.txt');
$sum = 0;

class Calc {
	private static $ops;
	private $operatorPrecedence;

	public function __construct(array $operatorPrecedence) {
		self::$ops = [
			'+' => fn(int $a, int $b) => $a + $b,
			'*' => fn(int $a, int $b) => $a * $b,
		];
		$this->operatorPrecedence = $operatorPrecedence;
	}

	public function calculate($a, $op, $b): int {
		//echo "$a $op $b\n";
		return self::$ops[$op]($a, $b);
	}

	public function eval(array $tokens): int {
		$a = $tokens[0];
		$a = is_array($a) ? $this->eval($a) : (int) $a;
		for ($i = 1; $i < count($tokens); ++$i) {
			$op = $tokens[$i];
			$b = $tokens[++$i];
			$b = is_array($b) ? $this->eval($b) : (int) $b;
			$a = $this->calculate($a, $op, $b);
		}
		return $a;
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
			if (in_array($c, ['(', ')'])) {
				$current = $c;
				$current = &$tokens[];
			} else if ($c == ' ') {
				if (isset($current)) {
					$current = &$tokens[];
				}
			} else if ($c != PHP_EOL) {
				$current .= $c;
			}
		}
		return array_filter($tokens);
	}
}

$operatorPrecedence = ['(' => 2, ')' => 2, '+' => 0, '*' => 0];
$calc = new Calc($operatorPrecedence);

foreach ($lines as $line => $formula) {
	$tokens = $calc->tokenize($line);
	var_dump($tokens);
	//$sum += $calc->eval($tokens);
}

printf("Sum of resulting values: %d .\n", $sum);
