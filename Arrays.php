<?php declare(strict_types=1);

namespace adventOfCode;

class Arrays {
	public static function multiply(array $stack) {
		$multiply = fn($carry, $i) => $carry * $i;
		return array_reduce($stack, $multiply, 1);
	}
}
