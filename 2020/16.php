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
	preg_match_all($ticketPattern, $line, $matches);
	$myTicket = array_map('intval', $matches[1]);
}

$otherTickets = [];

while (false !== ($line = fgets($fp))) {
	preg_match_all($ticketPattern, $line, $matches);
	$otherTickets[] = array_map('intval', $matches[1]);
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
