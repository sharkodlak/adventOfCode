#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$fp = fopen(substr(__FILE__, 0, -4) . '/input.txt', 'r');
$group = [];
$person = [];
$anyoneSum = 0;
$everyoneSum = 0;

do {
	$c = fgetc($fp);
	if (PHP_EOL === $c || false === $c) {
		if (empty($person)) {
			$anyone = count($group) > 1 ? array_merge(...$group) : $group[0];
			$everyone = count($group) > 1 ? array_intersect_key(...$group) : $group[0];
			$anyoneSum += array_sum($anyone);
			$everyoneSum += array_sum($everyone);
			$group = [];
		} else {
			$group[] = $person;
			$person = [];
		}
	} else {
		$person[$c] = 1;
	}
} while (false !== $c);

echo "Anyone answers sum: $anyoneSum .\n";
echo "Everyone answers sum: $everyoneSum .\n";
