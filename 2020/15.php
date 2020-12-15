#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$startingNumbers = [18,11,9,0,5,1];
$spokenNumbers = [];
$store = [];
$fp = fopen('php://stdin', 'r');

for ($i = 1; $i <= 2020; ++$i) {

	if (isset($startingNumbers[$i - 1])) {
		$spokenNumber = $startingNumbers[$i - 1];
	} else {
		//printf("spoken: %d, %d - [t: %d] => ", $spokenNumber, $i - 1, $spokenNumbers[$spokenNumber]);
		if (isset($spokenNumbers[$spokenNumber])) {
			$spokenNumber = $i - 1 - $spokenNumbers[$spokenNumber];
		} else {
			$spokenNumber = 0;
		}

	}
	$spokenNumbers = $store + $spokenNumbers;
	$store = [$spokenNumber => $i];
	/*
	echo "$spokenNumber\n";
	if (fgets($fp) !== PHP_EOL) {
		var_dump($spokenNumbers, $store);
	}
	*/
}


printf("The 2020th spoken number is: %d .\n", $spokenNumber);
