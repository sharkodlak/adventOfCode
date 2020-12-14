#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');

$timestamp = (int) $lines[0];
preg_match_all('~(?<=^|,)\d+|x~', $lines[1], $matches);
$busLines = [];
foreach ($matches[0] as $i => $bus) {
	if ($bus != 'x') {
		$busLines[$i] = (int) $bus;
	}
}
$departs = [];

foreach ($busLines as $bus) {
	$sinceLastDepart = $timestamp % $bus;
	$tillNextDepart = $bus - $sinceLastDepart;
	$departs[$bus] = $tillNextDepart;
}

$tillNextDepart = min($departs);
$bus = array_search($tillNextDepart, $departs);

printf("Bus %d departs in %d. Product is %d .\n", $bus, $tillNextDepart, $bus * $tillNextDepart);

$busStep = 0;
$step = $busLines[$busStep];
$offset = array_search($busStep, $busLines);
$t = 0;

do {
	$t += $step;
	$found = true;
	//echo "Time $t\n";
	foreach ($busLines as $i => $bus) {
		$sinceLastDepart = ($t + $i) % $bus;
		//echo "$i, $bus: $sinceLastDepart\n";
		if ($sinceLastDepart == 0) {
			if ($i > $busStep) {
				$prev = $step;
				$step = \Calc\Arithmetic\ArithmeticFunctions::lcm($step, $bus);
				//echo "New step: lcm($prev, $bus) = $step.\n";
				$busStep = $i;
			}
		} else {
			$found = false;
			break;
		}
	}
} while(!$found);

printf("First time with continuous departs is %d .\n", $t);
