#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$instructions = file(substr(__FILE__, 0, -4) . '/example.txt');
$instructionSet = [
	'acc' => fn($pointer, $acc, $arg) => [++$pointer, $acc + $arg],
	'jmp' => fn($pointer, $acc, $arg) => [$pointer + $arg, $acc],
	'nop' => fn($pointer, $acc) => [++$pointer, $acc],
];

class Computer {
	private $instructionSet = [];

	public function __construct(array $instructionSet) {
		$this->instructionSet = $instructionSet;
	}

	private $acc;
	private $pointer;
	private $visited;

	public function execute(array $instructions) {
		$this->pointer = 0;
		$this->acc = 0;
		$this->visited = [];
		while ($this->pointer < count($instructions) && !isset($this->visited[$this->pointer])) {
			$this->visited[$this->pointer] = 1;
			[$opcode, $arg] = explode(' ', $instructions[$this->pointer]);
			$arg = (int) $arg;
			[$this->pointer, $this->acc] = $this->instructionSet[$opcode]($this->pointer, $this->acc, $arg);
		}
		return $this->acc;
	}
}

$computer = new Computer($instructionSet);
$acc = $computer->execute($instructions);
echo "Accumulator value $acc .\n";
