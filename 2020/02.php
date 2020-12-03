#!/usr/bin/env php
<?php declare(strict_types=1);

$lines = [
	'1-3 a: abcde',
	'1-3 b: cdefg',
	'2-9 c: ccccccccc',
];
$lines = file(__DIR__ . '/02/input.txt');
$validFirst = $validSecond = 0;

foreach ($lines as $line) {
	preg_match('~(\d+)-(\d+) ([a-z]): (.+)~', $line, $matches);
	$count = preg_match_all("~{$matches[3]}~", $matches[4]);
	if ($matches[1] <= $count && $count <= $matches[2]) {
		++$validFirst;
	}
	if ($matches[4][$matches[1] - 1] === $matches[3] xor $matches[4][$matches[2] - 1] === $matches[3]) {
		++$validSecond;
	}
}

echo "Valid passwords: $validFirst .\n";
echo "Valid passwords according to second rules: $validSecond .\n";
