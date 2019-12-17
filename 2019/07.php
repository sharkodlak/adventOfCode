#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$file = file_get_contents(substr(__FILE__, 0, -4) . '/input.txt');
$source = explode(',', $file);
$source = array_map('intval', $source);
//$source = [3,15,3,16,1002,16,10,16,1,16,15,15,4,15,99,0,0];
//$source = [3,23,3,24,1002,24,10,24,1002,23,-1,23,101,5,23,23,1,24,23,23,4,23,99,0,0];
//$source = [3,31,3,32,1002,32,10,32,1001,31,-2,31,1007,31,0,33,1002,33,7,33,1,33,31,31,1,32,31,31,4,31,99,0,0,0];


$phases = new \drupol\phpermutations\Generators\Permutations(range(0, 4));
$maxOutput = 0;
foreach ($phases->generator() as $phase) {
	$input = 0;
	foreach ($phase as $phaseSettings) {
		$interpreter = new \adventOfCode\Computer\Interpreter($source);
		$interpreter->input($phaseSettings);
		$interpreter->run();
		$interpreter->input($input);
		[$output] = $interpreter->run();
		$input = $output;
		//echo "Output: $output.\n";
		//echo $interpreter->done() ? "Done.\n" : "halted...\n";
	}
	$maxOutput = max($maxOutput, $output);
}

echo "Max output is: $maxOutput .\n";

//$source = [3,26,1001,26,-4,26,3,27,1002,27,2,27,1,27,26,27,4,27,1001,28,-1,28,1005,28,6,99,0,0,5];
//$source = [3,52,1001,52,-5,52,3,53,1,52,56,54,1007,54,5,55,1005,55,26,1001,54,-5,54,1105,1,12,1,53,54,53,1008,54,0,55,1001,55,1,55,2,53,55,53,4,53,1001,56,-1,56,1005,56,6,99,0,0,0,0,10];
$phases = new \drupol\phpermutations\Generators\Permutations(range(5, 9));
$maxOutput = 0;
foreach ($phases->generator() as $phase) {
	$amplifiers = [];
	foreach ($phase as $phaseSettings) {
		$amplifier = new \adventOfCode\Computer\Interpreter($source);
		$amplifier->input($phaseSettings);
		$amplifiers[$phaseSettings] = $amplifier;
	}
	$input = 0;
	do {
		foreach ($amplifiers as $amplifier) {
			$amplifier->run();
			$amplifier->input($input);
			[$output] = $amplifier->run();
			$input = $output;
			//echo "Output: $output.\n";
			//echo $amplifier->done() ? "Done.\n" : "halted...\n";
		}
	} while (!$amplifier->done());
	$maxOutput = max($maxOutput, $output);
}

echo "Max output is: $maxOutput .\n";
