#!/usr/bin/env php
<?php declare(strict_types=1);

use Directory as GlobalDirectory;

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$monkeys = [];

class Monkey {
	private int $inspectedItems = 0;
	private array $items;
	private Closure $operation;
	private int $divisor;
	private self $passIfTrue;
	private self $passIfFalse;

	public function __construct(
		private int $order
	) {
	}

	public function setOperation(Closure $formula) {
		$this->operation = $formula;
	}

	public function setTestDivision(int $divisor): self {
		$this->divisor = $divisor;
		return $this;
	}

	public function setPassIfTrue(self $monkey): self {
		$this->passIfTrue = $monkey;
		return $this;
	}

	public function setPassIfFalse(self $monkey): self {
		$this->passIfFalse = $monkey;
		return $this;
	}

	public function passItem(): bool {
		$item = array_shift($this->items);
		if ($item === null) {
			return false;
		}
		$worryLevel = call_user_func($this->operation, $item);
		$item = intval($worryLevel / 3);
		$this->inspectedItems++;
		$receivingMonkey = $item % $this->divisor === 0 ? $this->passIfTrue : $this->passIfFalse;
		$receivingMonkey->add($item);
		//var_dump(['monkey' => $this->order, 'worry' => $worryLevel, 'item' => $item, 'throw to' => $receivingMonkey->order]);
		return true;
	}

	public function add(int $item): self {
		$this->items[] = $item;
		return $this;
	}

	public function getInspectedItems(): int {
		return $this->inspectedItems;
	}
}

foreach ($inputLoader as $line) {
	if (substr($line, 0, 7) === 'Monkey ') {
		$i = (int) substr($line, 7);
		$monkeys[$i] = $monkey = $monkeys[$i] ?? new Monkey($i);
	} else if (substr($line, 0, 18) === '  Starting items: ') {
		$items = explode(', ', substr($line, 18));
		foreach ($items as $item) {
			$monkey->add((int) $item);
		}
	} else if (substr($line, 0, 19) === '  Operation: new = ') {
		$rightArgument = substr($line, 25);
		if ($rightArgument === 'old') {
			if ($line[23] === '*') {
				$formula = fn(int $old) => $old * $old;
			} else {
				$formula = fn(int $old) => $old + $old;
			}
		} else {
			$rightArgument = (int) $rightArgument;
			if ($line[23] === '*') {
				$formula = fn(int $old) => $old * $rightArgument;
			} else {
				$formula = fn(int $old) => $old + $rightArgument;
			}
		}
		$monkey->setOperation($formula);
		//\adventOfCode\lib\Dumper::dump($formula);
	} else if (substr($line, 0, 21) === '  Test: divisible by ') {
		$divisor = (int) substr($line, 21);
		$monkey->setTestDivision($divisor);
		//\adventOfCode\lib\Dumper::dump($formula);
	} else if (substr($line, 0, 29) === '    If true: throw to monkey ') {
		$i = (int) substr($line, 29);
		$monkeys[$i] = $receiver = $monkeys[$i] ?? new Monkey($i);
		$monkey->setPassIfTrue($receiver);
		//\adventOfCode\lib\Dumper::dump($formula);
	} else if (substr($line, 0, 30) === '    If false: throw to monkey ') {
		$i = (int) substr($line, 30);
		$monkeys[$i] = $receiver = $monkeys[$i] ?? new Monkey($i);
		$monkey->setPassIfFalse($receiver);
		//\adventOfCode\lib\Dumper::dump($formula);
	}
	//\adventOfCode\lib\Dumper::dump([$instruction, $value, $registerX]);
}

ksort($monkeys);

for ($i = 0; $i < 20; ++$i) {
	foreach ($monkeys as $monkey) {
		while ($monkey->passItem()) {
			// nothing
		}
	}	
}

$inspectedItems = [];

foreach ($monkeys as $key => $monkey) {
	$inspectedItems[$key] = $monkey->getInspectedItems();
}

rsort($inspectedItems);

$monkeyMostInspectedItems = array_slice($inspectedItems, 0, 2);
$product = array_reduce($monkeyMostInspectedItems, fn($carry, $inspectedItems) => $carry * $inspectedItems, 1);

printf("Monkey bussiness: %d .\n", $product);
