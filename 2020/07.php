#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');
$tokenLines = [];

foreach ($lines as $key => $line) {
	preg_match('~^(\w+ \w+) bags contain( .+\.)$~', $line, $matches);
	$color = $matches[1];
	preg_match_all('~ (\d+) (\w+ \w+) bags?[.,]~', $matches[2], $matches, PREG_SET_ORDER);
	if (empty($matches)) {
		$tokenLines[$color] = [];
	} else {
		foreach ($matches as $match) {
			$tokenLines[$color][$match[2]] = (int) $match[1];
		}
	}
}
unset($lines);

class Bag {
	private $color;
	private $bags = [];
	private $bagsCount = 1;

	public function __construct(string $color) {
		$this->color = $color;
	}

	public function add(Bag $bag, int $amount) {
		$this->bags[$bag->color] = [$bag, $amount];
		$this->bagsCount += $amount * $bag->getBagsCount();
	}

	public function contains(string $color, int $amount) {
		foreach ($this->bags as $innerColor => [$bag, $innerAmount]) {
			if ($color == $innerColor && $amount <= $innerAmount
				|| $bag->contains($color, $amount)
			) {
				return true;
			}
		}
		return false;
	}

	public function getBagsCount() {
		return $this->bagsCount;
	}
}

$bags = [];
while (count($tokenLines) != count($bags)) {
	foreach ($tokenLines as $color => $innerBags) {
		$foundBags = array_intersect_key($innerBags, $bags);
		if (count($foundBags) == count($innerBags)) {
			$bag = new Bag($color);
			foreach ($innerBags as $innerColor => $amount) {
				$bag->add($bags[$innerColor], $amount);
			}
			$bags[$color] = $bag;
		}
	}
}

$bagsCount = 0;
foreach ($bags as $bag) {
	if ($bag->contains('shiny gold', 1)) {
		++$bagsCount;
	}
}

echo "One shiny gold bag can be contained in $bagsCount bags.\n";
printf("Shiny gold bag containes %d bags.\n", $bags['shiny gold']->getBagsCount() - 1);
