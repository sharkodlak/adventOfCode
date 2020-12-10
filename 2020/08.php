#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$instructions = file(substr(__FILE__, 0, -4) . '/input.txt');
$pointer = 0;
$acc = 0;
$visited = [];

$instructionSet = [
	'acc' => fn($pointer, $acc, $arg) => [++$pointer, $acc + $arg],
	'jmp' => fn($pointer, $acc, $arg) => [$pointer + $arg, $acc],
	'nop' => fn($pointer, $acc) => [++$pointer, $acc],
];

while (!in_array($pointer, $visited)) {
	$visited[$pointer] = $pointer;
	[$opcode, $arg] = explode(' ', $instructions[$pointer]);
	$arg = (int) $arg;
	[$pointer, $acc] = $instructionSet[$opcode]($pointer, $acc, $arg);
}

echo "Accumulator value $acc .\n";
