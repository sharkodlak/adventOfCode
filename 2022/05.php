#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$stacks = [];

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
		//\adventOfCode\lib\Dumper::dump($stacks);
	}

	if (preg_match('~^move (\d+) from (\d) to (\d)~', $line, $matches)) {
		//\adventOfCode\lib\Dumper::dump($line);
		$from = (int) $matches[2];
		$to = (int) $matches[3];
		for ($i = (int) $matches[1]; $i > 0; --$i) {
			$crate = array_pop($stacks[$from]);
			array_push($stacks[$to], $crate);
			//\adventOfCode\lib\Dumper::dump($crate);
		}
	}
}

$topCrates = array_map(fn($stack) => end($stack), $stacks);
printf("Top crates: %s .\n", implode('', $topCrates));