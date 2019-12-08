#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$file = file_get_contents(substr(__FILE__, 0, -4) . '/input.txt');
$source = explode(',', $file);
$source = array_map('intval', $source);

interface AddrValue {
	public function original();
	public function get();
}

class ValueBase implements AddrValue {
	private $value;
	public function __construct(int $value) {
		$this->value = $value;
	}
	public function original() {
		return $this->value;
	}
	public function get() {
		return $this->value;
	}
	public function __toString() {
		return (string) $this->get();
	}
}

class Value extends ValueBase {
}

class Addr extends ValueBase {
	private $interpreter;
	public function __construct(int $value, Interpreter $interpreter) {
		parent::__construct($value);
		$this->interpreter = $interpreter;
	}
	public function get() {
		return $this->interpreter->addrLoad(parent::get());
	}
	public function __toString() {
		return '*' . $this->original() . '=' . $this->get();
	}
}

class Input extends Value {
	private $input;
	public function __construct(int $value, int $input) {
		parent::__construct($value);
		$this->input = $input;
	}
	public function getInput(): int {
		return $this->input;
	}
	public function __toString() {
		return "=$this->input > *" . parent::__toString();
	}
}

class Output extends Value {
	public function __toString() {
		return '*' . parent::__toString() . " > OUT";
	}
}

class Interpreter {
	const BREAK_ON_INPUT = 1;
	const BREAK_ON_OUTPUT = 2;
	const OP_INPUT = 3;
	const OP_OUTPUT = 4;
	private $instructionSet = [];
	private $done = false;
	private $source;

	public function __construct(array $source) {
		$this->source = $source;
		$this->rewind();
		$this->instructionSet[1] = [
			'args' => 3,
			'fn' => function(AddrValue $a, AddrValue $b, AddrValue $addrStore): int {
				$result = $a->get() + $b->get();
				return $this->addrStore($addrStore->original(), $result);
			},
		];
		$this->instructionSet[2] = [
			'args' => 3,
			'fn' => function(AddrValue $a, AddrValue $b, AddrValue $addrStore): int {
				$result = $a->get() * $b->get();
				return $this->addrStore($addrStore->original(), $result);
			},
		];
		$this->instructionSet[self::OP_INPUT] = [
			'args' => 1,
			'fn' => function(Input $input): int {
				return $this->addrStore($input->original(), $input->getInput());
			},
		];
		$this->instructionSet[self::OP_OUTPUT] = [
			'args' => 1,
			'fn' => function(Output $output): int {
				return $this->addrLoad($output->get());
			},
		];
		$this->instructionSet[5] = [
			'args' => 2,
			'fn' => function(AddrValue $nonZero, AddrValue $jumpToAddr): ?int {
				if ($nonZero->get()) {
					return $this->seek($jumpToAddr->get());
				}
				return null;
			},
		];
		$this->instructionSet[6] = [
			'args' => 2,
			'fn' => function(AddrValue $zero, AddrValue $jumpToAddr): ?int {
				if (!$zero->get()) {
					return $this->seek($jumpToAddr->get());
				}
				return null;
			},
		];
		$this->instructionSet[7] = [
			'args' => 3,
			'fn' => function(AddrValue $lesser, AddrValue $higher, AddrValue $addrStore): int {
				$value = $lesser->get() < $higher->get() ? 1 : 0;
				return $this->addrStore($addrStore->original(), $value);
			},
		];
		$this->instructionSet[8] = [
			'args' => 3,
			'fn' => function(AddrValue $a, AddrValue $b, AddrValue $addrStore): int {
				$value = $a->get() == $b->get() ? 1 : 0;
				return $this->addrStore($addrStore->original(), $value);
			},
		];
		$this->instructionSet[99] = [
			'args' => 0,
			'fn' => function(): int {
				$this->done = true;
				return $this->addrLoad(0);
			},
		];
	}

	public function addrStore(int $addr, int $value): int {
		return $this->source[$addr] = $value;
	}

	private function get(int $value, bool $isAddr) {
		return $isAddr ? $this->addrLoad($value) : $value;
	}

	public function addrLoad(int $addr): int {
		return $this->source[$addr];
	}

	public function run(): array {
		$output = [];
		do {
			while ($this->valid(self::BREAK_ON_INPUT | self::BREAK_ON_OUTPUT)) {
				$instruction = $this->current();
				$this->next();
				$instruction();
			}
			if (!$this->done && $continue = $this->isOutput()) {
				$output[] = $this->output();
			}
		} while (!$this->done && $continue);
		return $output;
	}

	public function isOutput() {
		return $this->getOpcode() == self::OP_OUTPUT;
	}

	public function output() {
		$opcode = $this->getOpcode();
		if ($opcode != self::OP_OUTPUT) {
			$msg = sprintf("Wrong instruction, expected opcode %d given: %d!",
				self::OP_OUTPUT,
				$opcode
			);
			throw new \Sharkodlak\Exception\IllegalStateException($msg);
		}
		['fn' => $instruction] = $this->instructionSet[$opcode];
		$value = $this->next();
		$this->next(); // Prepare next instruction to pointer
		$args = [
			new Output($value),
		];
		echo "Instruction $opcode.\n";
		return $this->instruction($instruction, $args)();
	}

	public function input(int $input) {
		$opcode = $this->getOpcode();
		if ($opcode != self::OP_INPUT) {
			$msg = sprintf("Wrong instruction, expected opcode %d given: %d!",
				self::OP_INPUT,
				$opcode
			);
			throw new \Sharkodlak\Exception\IllegalStateException($msg);
		}
		['fn' => $instruction] = $this->instructionSet[$opcode];
		$value = $this->next();
		$this->next(); // Prepare next instruction to pointer
		$args = [
			new Input($value, $input),
		];
		echo "Instruction $opcode.\n";
		return $this->instruction($instruction, $args)();
	}

	public function current() {
		$opcode = $this->getOpcode();
		$argsMap = $this->getArgsMap();
		['args' => $argsCount, 'fn' => $instruction] = $this->instructionSet[$opcode];
		$args = [];
		for ($i = 0; $i < $argsCount; ++$i) {
			$value = $this->next();
			switch ($argsMap[$i] ?? 0) {
				case 1:
					$arg = new Value($value);
				break;
				case 0:
					$arg = new Addr($value, $this);
				break;
				default:
					$msg = sprintf("Unknown modifier: '%s'", $argsMap[$i]);
					throw new \Sharkodlak\Exception\IllegalStateException($msg);
			}
			$args[$i] = $arg;
		}
		echo "Instruction $opcode.\n";
		return $this->instruction($instruction, $args);
	}

	private function instruction($instruction, array $args) {
		printf("  - arguments (%s).\n", implode(', ', $args));
		return function() use($instruction, $args) {
			return call_user_func_array($instruction, $args);
		};
	}

	private function getOpcode() {
		$intCode = $this->getCurrentInstruction();
		$opcode = intval(substr((string) $intCode, -2));
		if (empty($this->instructionSet[$opcode])) {
			$msg = "Unknown opcode: $opcode!";
			throw new \Sharkodlak\Exception\IllegalStateException($msg);
		}
		return $opcode;
	}

	private function getArgsMap() {
		$intCode = $this->getCurrentInstruction();
		$map = substr((string) $intCode, 0, -2);
		$argsMap = [];
		for ($i = strlen((string) $map) - 1; $i >= 0; --$i) {
			$argsMap[] = intval($map[$i]);
		}
		return $argsMap;
	}

	public function getCurrentInstruction() {
		return current($this->source);
	}

	public function key(): int {
		return key($this->source);
	}

	public function next() {
		return next($this->source);
	}

	public function rewind() {
		reset($this->source);
	}

	public function valid($flags = 0): bool {
		if ($this->done) {
			return false;
		}
		if ($flags & self::BREAK_ON_INPUT && $this->getOpcode() == self::OP_INPUT
			|| $flags & self::BREAK_ON_OUTPUT && $this->isOutput()
		) {
			return false;
		}
		return $this->key() !== null;
	}

	public function seek(int $position) {
		echo "Seek to $position.";
		$this->rewind();
		while ($this->key() < $position) {
			$this->next();
		}
		echo "Position is: ", $this->key(), " instruction is: " . $this->getCurrentInstruction() . ".\n";
	}

	public function done() {
		return $this->done;
	}
}

//$source = [3,15,3,16,1002,16,10,16,1,16,15,15,4,15,99,0,0];
//$source = [3,23,3,24,1002,24,10,24,1002,23,-1,23,101,5,23,23,1,24,23,23,4,23,99,0,0];
//$source = [3,31,3,32,1002,32,10,32,1001,31,-2,31,1007,31,0,33,1002,33,7,33,1,33,31,31,1,32,31,31,4,31,99,0,0,0];
$phases = new \drupol\phpermutations\Generators\Permutations(range(0, 4));
$maxOutput = 0;
foreach ($phases->generator() as $phase) {
	$input = 0;
	foreach ($phase as $phaseSettings) {
		$interpreter = new Interpreter($source);
		$interpreter->input($phaseSettings);
		$interpreter->run();
		$interpreter->input($input);
		[$output] = $interpreter->run();
		$input = $output;
		echo "Output: $output.\n";
		echo $interpreter->done() ? "Done.\n" : "halted...\n";
	}
	$maxOutput = max($maxOutput, $output);
}

echo "Max output is: $maxOutput .\n";

//$source = [3,26,1001,26,-4,26,3,27,1002,27,2,27,1,27,26,27,4,27,1001,28,-1,28,1005,28,6,99,0,0,5];
//$source = [3,52,1001,52,-5,52,3,53,1,52,56,54,1007,54,5,55,1005,55,26,1001,54,-5,54,1105,1,12,1,53,54,53,1008,54,0,55,1001,55,1,55,2,53,55,53,4,53,1001,56,-1,56,1005,56,6,99,0,0,0,0,10];
$phases = new \drupol\phpermutations\Generators\Permutations(range(5, 9));
$maxOutput = 0;
foreach ($phases->generator() as $phase) {
	$amplifiers = [];
	foreach ($phase as $phaseSettings) {
		$amplifier = new Interpreter($source);
		$amplifier->input($phaseSettings);
		$amplifiers[$phaseSettings] = $amplifier;
	}
	$input = 0;
	do {
		foreach ($amplifiers as $amplifier) {
			$amplifier->run();
			$amplifier->input($input);
			[$output] = $amplifier->run();
			$input = $output;
			echo "Output: $output.\n";
			echo $amplifier->done() ? "Done.\n" : "halted...\n";
		}
	} while (!$amplifier->done());
	$maxOutput = max($maxOutput, $output);
}

echo "Max output is: $maxOutput .\n";
