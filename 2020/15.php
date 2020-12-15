#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$startingNumbers = [18,11,9,0,5,1];
//$startingNumbers = [0,3,6];
$spokenNumbers = new SplFixedArray(30_000_000);
$storeNumber = 0;
$storeTime = 0;

foreach ($startingNumbers as $i => $startingNumber) {
	$spokenNumber = $startingNumber;
	$spokenNumbers[$storeNumber] = $storeTime;
	$storeNumber = $spokenNumber;
	$storeTime = $i + 1;
}

for ($i = count($startingNumbers); $i < 30_000_000; ++$i) {
	if (isset($spokenNumbers[$spokenNumber])) {
		$spokenNumber = $i - $spokenNumbers[$spokenNumber];
	} else {
		$spokenNumber = 0;
	}

	$spokenNumbers[$storeNumber] = $storeTime;
	$storeNumber = $spokenNumber;
	$storeTime = $i + 1;
	if ($storeTime == 2020) {
		printf("The %dth spoken number is: %d .\n", $storeTime, $spokenNumber);
	}
}

printf("The %dth spoken number is: %d .\n", $i, $spokenNumber);
