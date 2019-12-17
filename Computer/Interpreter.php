<?php declare(strict_types=1);

namespace adventOfCode\Computer;

class Interpreter {
	const BREAK_ON_INPUT = 1;
	const BREAK_ON_OUTPUT = 2;
	const OP_INPUT = 3;
	const OP_OUTPUT = 4;
	private $instructionSet = [];
	private $done = false;
	private $source;
	private $relativeBase = 0;

	public function __construct(array $source) {
		$this->source = $source;
		$this->rewind();
		$this->instructionSet[1] = [
			'args' => 3,
			'fn' => function(AddrValue $a, AddrValue $b, AddrValue $addrStore): int {
				$result = $a->get() + $b->get();
				return $this->addrStore($addrStore->getStoreAddr(), $result);
			},
		];
		$this->instructionSet[2] = [
			'args' => 3,
			'fn' => function(AddrValue $a, AddrValue $b, AddrValue $addrStore): int {
				$result = $a->get() * $b->get();
				return $this->addrStore($addrStore->getStoreAddr(), $result);
			},
		];
		$this->instructionSet[self::OP_INPUT] = [
			'args' => 1,
			'fn' => function(AddrValue $addrStore, int $input): int {
				return $this->addrStore($addrStore->getStoreAddr(), $input);
			},
		];
		$this->instructionSet[self::OP_OUTPUT] = [
			'args' => 1,
			'fn' => function(AddrValue $output): int {
				return $output->get();
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
				return $this->addrStore($addrStore->getStoreAddr(), $value);
			},
		];
		$this->instructionSet[8] = [
			'args' => 3,
			'fn' => function(AddrValue $a, AddrValue $b, AddrValue $addrStore): int {
				$value = $a->get() == $b->get() ? 1 : 0;
				return $this->addrStore($addrStore->getStoreAddr(), $value);
			},
		];
		$this->instructionSet[9] = [
			'args' => 1,
			'fn' => function(AddrValue $relativeBaseOffset): int {
				$value = $relativeBaseOffset->get();
				return $this->offsetRelativeBase($value);
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
		if ($addr < 0) {
			$msg = "Illegal memory address '$addr'!";
			throw new \Sharkodlak\Exception\IllegalArgumentException($msg);
		}
		return $this->source[$addr] ?? 0;
	}

	public function offsetRelativeBase(int $offset): int {
		$this->relativeBase += $offset;
		return $this->relativeBase;
	}

	public function getRelativeBase(): int {
		return $this->relativeBase;
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

	public function output(): int {
		$output = $this->current(self::OP_OUTPUT)();
		$this->next();
		return $output;
	}

	public function input(int $input): void {
		$this->current(self::OP_INPUT)($input);
		$this->next();
	}

	public function current(int $expectedInstruction = 0) {
		$opcode = $this->getOpcode();
		if ($expectedInstruction != 0 && $opcode != $expectedInstruction) {
			$msg = sprintf("Wrong instruction, expected opcode %d given: %d!",
				$expectedInstruction,
				$opcode
			);
			throw new \Sharkodlak\Exception\IllegalStateException($msg);
		}
		$argsMap = $this->getArgsMap();
		['args' => $argsCount, 'fn' => $instruction] = $this->instructionSet[$opcode];
		$args = [];
		for ($i = 0; $i < $argsCount; ++$i) {
			$value = $this->next();
			switch ($argsMap[$i] ?? 0) {
				case 2:
					$arg = new RelAddr($value, $this);
				break;
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
		//echo "Instruction $opcode.\n";
		return $this->instruction($instruction, $args);
	}

	private function instruction($instruction, array $args) {
		//printf("  - arguments (%s).\n", implode(', ', $args));
		return function(...$input) use($instruction, $args) {
			return call_user_func_array($instruction, array_merge($args, $input));
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

	public function getSource(): array {
		return $this->source;
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
		//echo "Seek to $position.";
		$this->rewind();
		while ($this->key() < $position) {
			$this->next();
		}
		//echo "Position is: ", $this->key(), " instruction is: " . $this->getCurrentInstruction() . ".\n";
	}

	public function done() {
		return $this->done;
	}
}
