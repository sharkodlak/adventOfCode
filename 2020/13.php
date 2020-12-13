#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');

$timestamp = (int) $lines[0];
preg_match_all('~(?<=^|,)\d+~', $lines[1], $matches);
$busLines = $matches[0];
$departs = [];

foreach ($busLines as $bus) {
	$sinceLastDepart = $timestamp % $bus;
	$tillNextDepart = $bus - $sinceLastDepart;
	$departs[$bus] = $tillNextDepart;
}

$tillNextDepart = min($departs);
$bus = array_search($tillNextDepart, $departs);

printf("Bus %d departs in %d. Product is %d .\n", $bus, $tillNextDepart, $bus * $tillNextDepart);
