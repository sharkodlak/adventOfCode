#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$elfNumber = 0;
$elves = [
	$elfNumber => 0,
];

foreach ($inputLoader as $line) {
	if ($line === '') {
		$elves[++$elfNumber] = 0;
	}
	$elves[$elfNumber] += (int) $line;
}

$max = max($elves);

echo "Max. total calories carried by en elf: $max .\n";

rsort($elves);
var_dump($elves);
$total = $elves[0] + $elves[1] + $elves[2];

echo "Max. total calories carried by 3 elves: $total .\n";