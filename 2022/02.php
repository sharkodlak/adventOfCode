#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$totalScore = 0;
$opponentSigns = \array_flip([1 => 'A', 'B', 'C']);
$mySigns = \array_flip([1 => 'X', 'Y', 'Z']);

foreach ($inputLoader as $line) {
	$opponent = $opponentSigns[$line[0]];
    $me = $mySigns[$line[2]];
    $delta = $me - $opponent;
    if ($delta === 2) {
        $delta = -1;
    } else if ($delta === -2) {
        $delta = 1;
    }
    $roundOutcome = ($delta + 1) * 3;
    $score = $me + $roundOutcome;
    $totalScore += $score;

    //\adventOfCode\lib\Dumper::dump([$line[0] => $opponent, $line[2] => $me, 'delta' => $delta, 'outcome' => $roundOutcome, 'score' => $score]);
}

echo "Total score: $totalScore .\n";
