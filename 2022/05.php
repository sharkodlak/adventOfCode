#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$stacks = [];
$stacks9001 = null;

foreach ($inputLoader as $line) {
	if (preg_match('~\[[A-Z]\]~', $line)) {
		$crates = str_split($line, 4);
		foreach ($crates as $i => $crate) {
			if ($crate[0] === '[') {
				$stacks[$i + 1][] = $crate[1];
			}
		}
	}

	if ($line === '') {
		ksort($stacks);
		foreach ($stacks as &$stack) {
			$stack = array_reverse($stack);
		}
		unset($stack);
		$stacks9001 = $stacks;
		//\adventOfCode\lib\Dumper::dump($stacks);
	}

	if (preg_match('~^move (\d+) from (\d) to (\d)~', $line, $matches)) {
		//\adventOfCode\lib\Dumper::dump($line);
		$numberToMove = (int) $matches[1];
		$from = (int) $matches[2];
		$to = (int) $matches[3];
		for ($i = $numberToMove; $i > 0; --$i) {
			$crate = array_pop($stacks[$from]);
			array_push($stacks[$to], $crate);
			//\adventOfCode\lib\Dumper::dump($crate);
		}
		$crates9001 = array_splice($stacks9001[$from], -$numberToMove);
		array_push($stacks9001[$to], ...$crates9001);
		//\adventOfCode\lib\Dumper::dump(...$crates9001);
	}
}

$readTopCrates = fn($stack) => end($stack);
$topCrates = array_map($readTopCrates, $stacks);
printf("Top crates: %s .\n", implode('', $topCrates));

$topCrates = array_map($readTopCrates, $stacks9001);
printf("Top crates 9001: %s .\n", implode('', $topCrates));