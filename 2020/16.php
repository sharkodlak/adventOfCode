#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$fp = fopen(substr(__FILE__, 0, -4) . '/input.txt', 'r');
$fields = [];

while (PHP_EOL !== ($line = fgets($fp))) {
	preg_match('~^([^:]+): (\d+)-(\d+) or (\d+)-(\d+)$~', $line, $matches);
	$firstRange = range($matches[2], $matches[3]);
	$secondRange = range($matches[4], $matches[5]);
	$fields[$matches[1]] = array_merge($firstRange, $secondRange);
}

$ticketPattern = '~(?<=^|,)(\d+)~';

while (PHP_EOL !== ($line = fgets($fp))) {
	if ($line !== "your ticket:\n") {
		preg_match_all($ticketPattern, $line, $matches);
		$myTicket = array_map('intval', $matches[1]);
	}
}

$otherTickets = [];

while (false !== ($line = fgets($fp))) {
	if ($line !== "nearby tickets:\n") {
		preg_match_all($ticketPattern, $line, $matches);
		$otherTickets[] = array_map('intval', $matches[1]);
	}
}

$allValidNumbers = array_merge(...array_values($fields));
$invalidNumbers = [];
foreach ($otherTickets as $ticketKey => $ticket) {
	$valid = true;
	foreach ($ticket as $fieldValue) {
		if (!in_array($fieldValue, $allValidNumbers)) {
			$invalidNumbers[] = $fieldValue;
			$valid = false;
		}
	}
	if (!$valid) {
		unset($otherTickets[$ticketKey]);
	}
}

printf("Scanning error rate: %d .\n", array_sum($invalidNumbers));

$allTickets = $otherTickets;
array_unshift($allTickets, $myTicket);
$possibleFieldNames = [];

foreach (array_keys($fields) as $fieldName) {
	$possibleFieldNames[] = array_flip(array_keys($fields));
}

$fp = fopen('php://stdin', 'r');
foreach ($allTickets as $ticket) {
	foreach ($ticket as $fieldNumber => $value) {
		foreach (array_keys($possibleFieldNames[$fieldNumber]) as $fieldName) {
			if (!in_array($value, $fields[$fieldName])) {
				//var_dump(in_array($value, $range), $value, $fieldNumber, $fieldName);
				//fgets($fp);
				unset($possibleFieldNames[$fieldNumber][$fieldName]);
			}
		}
	}
}

do {
	$reduced = false;
	foreach ($possibleFieldNames as $fieldNumber => $fields) {
		//echo count($possibleFieldNames[$fieldNumber]), "\n";
		if (count($possibleFieldNames[$fieldNumber]) === 1) {
			$fieldName = key($possibleFieldNames[$fieldNumber]);
			//echo "Single $fieldName\n";
			foreach (array_keys($possibleFieldNames) as $fn) {
				if ($fieldNumber !== $fn && isset($possibleFieldNames[$fn][$fieldName])) {
					unset($possibleFieldNames[$fn][$fieldName]);
					$reduced = true;
					//echo "Drop $fn:$fieldName\n";
				}
			}
			if ($reduced) {
				continue(2);
			}
		}
	}
} while ($reduced);

//var_dump($possibleFieldNames);
$sum = 1;

foreach ($possibleFieldNames as $fieldNumber => $fields) {
	$fieldName = key($fields);
	$possibleFieldNames[$fieldNumber] = $fieldName;
	if (substr($fieldName, 0, 9) === 'departure') {
		$sum *= $myTicket[$fieldNumber];
	}
}

printf("Departure multiplication: %d .\n", $sum);
