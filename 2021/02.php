#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(__DIR__ . '/inputs/02.txt');
$x = 0;
$depth = 0;
$depth2 = 0;
$aim = 0;

foreach ($lines as $line) {
	if (!preg_match('~^(\w+) (\d+)$~', $line, $matches)) {
		throw Exception("Wrong command line:\n$line.");
	}
	switch ($matches[1]) {
		case 'forward':
			$x += $matches[2];
			$depth2 += $matches[2] * $aim;
			break;
		case 'down':
			$depth += $matches[2];
			$aim += $matches[2];
			break;
		case 'up':
			$depth -= $matches[2];
			$aim -= $matches[2];
			break;
	}
	echo "$x, $depth2, $aim\n";
}

printf("Final coordinates are: $x, $depth. It's product is %d .\n", $x * $depth);
printf("Final coordinates are: $x, $depth2. It's product is %d .\n", $x * $depth2);

