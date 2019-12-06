#!/usr/bin/env php
<?php declare(strict_types=1);

$file = file_get_contents(substr(__FILE__, 0, -4) . '/input.txt');
$source = explode(',', $file);
//$source = [3,0,4,0,99];
//$source = [1002,4,3,4,33];
//$source = [1101,100,-1,4,0];
//$source = [3,9,8,9,10,9,4,9,99,-1,8];
//$source = [3,9,7,9,10,9,4,9,99,-1,8];
//$source = [3,3,1108,-1,8,3,4,3,99];
//$source = [3,3,1107,-1,8,3,4,3,99];
//$source = [3,12,6,12,15,1,13,14,13,4,13,99,-1,0,1,9];
//$source = [3,3,1105,-1,9,1101,0,0,12,4,12,99,1];
$source = [3,21,1008,21,8,20,1005,20,22,107,8,21,20,1006,20,31,1106,0,36,98,0,0,1002,21,125,20,4,20,1105,1,46,104,999,1105,1,46,1101,1000,1,20,4,20,1105,1,46,98,99];
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
		return '*' . $this->original();
	}
}

class Interpreter {
	private $instructionSet = [];
	private $end = false;
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
		$this->instructionSet[3] = [
			'args' => 1,
			'fn' => function(AddrValue $addrStore): int {
				echo "Insert input to function:\n";
				fscanf(STDIN, "%d\n", $input);
				return $this->addrStore($addrStore->original(), $input);
			},
		];
		$this->instructionSet[4] = [
			'args' => 1,
			'fn' => function(AddrValue $addrLoad): int {
				$value = $addrLoad->get();
				echo "Output: $value.\n";
				return $value;
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
				$this->end = true;
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

	public function current() {
		$intCode = current($this->source);
		$opcode = intval(substr((string) $intCode, -2));
		if (empty($this->instructionSet[$opcode])) {
			exit("Unknown opcode: $opcode.\n");
		}
		echo "Instruction $opcode with arguments (";
		['args' => $argsCount, 'fn' => $instruction] = $this->instructionSet[$opcode];
		$args = [];
		for ($i = 0; $i < $argsCount; ++$i) {
			$value = next($this->source);
			$d = substr("00000$intCode", -3 - $i, 1);
			switch (intval($d)) {
				case 1:
					$arg = new Value($value);
				break;
				case 0:
				default:
					$arg = new Addr($value, $this);
				break;
			}
			$args[$i] = $arg;
			echo (string) $arg, ', ';
		}
		echo ").\n";
		return call_user_func_array($instruction, $args);
	}

	public function key(): int {
		return key($this->source);
	}

	public function next() {
		next($this->source);
	}

	public function rewind() {
		reset($this->source);
	}

	public function valid(): bool {
		return !$this->end && $this->key() !== null;
	}

	public function seek(int $position) {
		echo "Seek to $position.";
		$this->rewind();
		while ($this->key() < $position) {
			$this->next();
		}
		echo "Position is: ", $this->key(), ".\n";
		return $this->current();
	}
}

$interpreter = new Interpreter($source);
while ($interpreter->valid()) {
	$result = $interpreter->current();
	$interpreter->next();
}

echo "Result is: $result .\n";
