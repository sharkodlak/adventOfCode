#!/usr/bin/env php
<?php declare(strict_types=1);

use Directory as GlobalDirectory;

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$clock = 0;
$registerX = 1;
$instructions = [
	'noop' => ['cycles' => 1],
	'addx' => ['cycles' => 2],
];
$watchedClocks = [20, 60, 100, 140, 180, 220];
$sumOfSignalStrengths = 0;

foreach ($inputLoader as $y => $line) {
	$instruction = substr($line, 0, 4);
	for ($c = 1; $c <= $instructions[$instruction]['cycles']; ++$c) {
		echo abs($registerX - $clock % 40) <= 1 ? '#' : ' ';
		$clock++;
		if (in_array($clock, $watchedClocks)) {
			//\adventOfCode\lib\Dumper::dump([$clock => $registerX]);
			$sumOfSignalStrengths += $clock * $registerX;
		}
		if ($clock % 40 === 0) {
			echo "\n";
		}
		if ($c === $instructions[$instruction]['cycles']) {
			switch ($instruction) {
				case 'addx':
					$value = (int) substr($line, 5);
					$registerX += $value;
				break;
			}
		}
	}
	//\adventOfCode\lib\Dumper::dump([$instruction, $value, $registerX]);
}

printf("Sum of signal strengths: %d .\n", $sumOfSignalStrengths);
