#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$assignmentsFullyContained = 0;


foreach ($inputLoader as $line) {
    [$elfA, $elfB] = explode(',', $line);
    [$a, $A] = explode('-', $elfA);
    [$b, $B] = explode('-', $elfB);
    $fullyContains = $a <= $b && $A >= $B || $b <= $a && $B >= $A;
    if ($fullyContains) {
        ++$assignmentsFullyContained;
    }
    //\adventOfCode\lib\Dumper::dump([$a, $A, $b, $B, $fullyContains]);
}

echo "Assignments fully contained: $assignmentsFullyContained .\n";