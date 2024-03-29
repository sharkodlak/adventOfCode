#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$assignmentsFullyContained = 0;
$assignmentsOverlaps = 0;

foreach ($inputLoader as $line) {
    [$elfA, $elfB] = explode(',', $line);
    [$a, $A] = explode('-', $elfA);
    [$b, $B] = explode('-', $elfB);
    $fullyContains = $a <= $b && $A >= $B || $b <= $a && $B >= $A;
    if ($fullyContains) {
        ++$assignmentsFullyContained;
    }
    $overlaps = $b <= $a && $a <= $B || $b <= $A && $A <= $B || $a <= $b && $b <= $A || $a <= $B && $B <= $A;
    if ($overlaps) {
        ++$assignmentsOverlaps;
    }
    //\adventOfCode\lib\Dumper::dump(['a' => $a, 'A' => $A, 'b' => $b, 'B' => $B, 'b <= a <= B' => $b <= $a && $a <= $B, $b <= $A && $A <= $B, $a <= $b && $b <= $A, $a <= $B && $B <= $A]);
}

echo "Assignments fully contained: $assignmentsFullyContained .\n";
echo "Assignments overlaps: $assignmentsOverlaps .\n";
