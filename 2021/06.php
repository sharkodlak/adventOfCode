#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$sample = $argc == 1 ? '.sample' : '';
if ($sample) {
	echo "\n --->   SAMPLE   <---\n\n";
}
$inputFile = sprintf('%s/inputs/%02d%s.txt', __DIR__, basename(__FILE__, '.php'), $sample);
$lines = file($inputFile);
$fishesInput = explode(',', $lines[0]);
$fishGroups = array_fill(0, 9, 0);

foreach ($fishesInput as $fish) {
	++$fishGroups[$fish];
}

for ($i = 0; $i < 80; ++$i) {
	$currentGroup = array_shift($fishGroups);
	echo "$currentGroup\n";
	$fishGroups[6] += $currentGroup;
	$fishGroups[8] = $currentGroup;
}

printf("Total number of fish is %d .\n", array_sum($fishGroups));

