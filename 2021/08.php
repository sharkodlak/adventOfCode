#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$input = $argc == 1 ? '' : ".{$argv[1]}";
if ($argc != 1) {
	echo "\n --->   SAMPLE   <---\n\n";
}
$inputFile = sprintf('%s/inputs/%02d%s.txt', __DIR__, basename(__FILE__, '.php'), $input);
$lines = file($inputFile);

class Segments {
	public function __construct(private array $segments, private array $output) {}

	public function countEasyDigits(): int {
		$easyDigits = 0;

		foreach ($this->output as $digit) {
			if (in_array(strlen($digit), [2, 4, 3, 7])) {
				++$easyDigits;
			}
		}

		return $easyDigits;
	}
}

$displays = [];

foreach ($lines as $line)  {
	[$digits, $toDisplay] = explode(' | ', trim($line));
	$digits = explode(' ', $digits);
	sort($digits);
	$toDisplay = explode(' ', $toDisplay);
	$displays[] = new Segments($digits, $toDisplay);
}

$easyDigits = 0;

foreach ($displays as $display) {
	$easyDigits += $display->countEasyDigits();
}

printf("Easy digits count is %d .\n", $easyDigits);
