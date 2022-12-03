#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$priorities = \array_merge([null], \range('a', 'z'), \range('A', 'Z'));
unset($priorities[0]);
$priorities = \array_flip($priorities);
$sumOfPriorities = 0;

foreach ($inputLoader as $line) {
    $halfLength = \strlen($line) / 2;
    $compartments = [
        '1st' => str_split(substr($line, 0, $halfLength)),
        '2nd' => str_split(substr($line, $halfLength)),
    ];
    $intersect = array_intersect($compartments['1st'], $compartments['2nd']);
    $commonType = current($intersect);
    $priority = $priorities[$commonType];
    $sumOfPriorities += $priority;
    //\adventOfCode\lib\Dumper::dump([$compartments, $intersect, $commonType, $priority]);
}

echo "Sum of priorities: $sumOfPriorities .\n";
