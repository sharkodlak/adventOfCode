#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$fp = fopen(substr(__FILE__, 0, -4) . '/input.txt', 'r');
$answers = [];
$answersSum = 0;
$newline = false;

while (false !== ($c = fgetc($fp))) {
	if (PHP_EOL === $c) {
		if ($newline) {
			$answersSum += array_sum($answers);
			$answers = [];
		}
		$newline = true;
	} else {
		$answers[$c] = 1;
		$newline = false;
	}
}

$answersSum += array_sum($answers);

echo "Answers sum: $answersSum .\n";
