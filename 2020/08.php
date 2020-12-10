#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$instructions = file(substr(__FILE__, 0, -4) . '/input.txt');
foreach ($instructions as &$instruction) {
	[$opcode, $arg] = explode(' ', $instruction);
	$arg = (int) $arg;
	$instruction = [$opcode, $arg];
}
unset($instruction);
$instructionSet = [
	'acc' => fn($pointer, $acc, $arg) => [++$pointer, $acc + $arg],
	'jmp' => fn($pointer, $acc, $arg) => [$pointer + $arg, $acc],
	'nop' => fn($pointer, $acc) => [++$pointer, $acc],
];

class Computer {
	private $instructionSet = [];
	private $acc;
	private $pointer;
	private $visited;

	public function __construct(array $instructionSet) {
		$this->instructionSet = $instructionSet;
	}

	public function execute(array $instructions) {
		$this->pointer = 0;
		$this->acc = 0;
		$this->visited = [];
		while ($this->pointer < count($instructions) && !isset($this->visited[$this->pointer])) {
			$this->visited[$this->pointer] = 1;
			[$opcode, $arg] = $instructions[$this->pointer];
			[$this->pointer, $this->acc] = $this->instructionSet[$opcode]($this->pointer, $this->acc, $arg);
		}
		return !isset($this->visited[$this->pointer]);
	}

	public function getAcc() {
		return $this->acc;
	}
}

$computer = new Computer($instructionSet);
$computer->execute($instructions);
$acc = $computer->getAcc();
echo "Accumulator value $acc .\n";

$modify = ['jmp' => 'nop', 'nop' => 'jmp'];
foreach ($instructions as $i => $instruction) {
	if (in_array($instruction[0], array_keys($modify))) {
		$instructionsCopy = $instructions;
		$opcode = $instructionsCopy[$i][0];
		$instructionsCopy[$i][0] = $modify[$opcode];
		$success = $computer->execute($instructionsCopy);
		if ($success) {
			$acc = $computer->getAcc();
			echo "Accumulator value $acc .\n";
		}
	}
}
