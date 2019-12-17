#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$file = file_get_contents(substr(__FILE__, 0, -4) . '/input.txt');
$source = explode(',', $file);
$source = array_map('intval', $source);

for ($noun = 0; $noun <= 99; ++$noun) {
	$sourceNoun = $source;
	$sourceNoun[1] = $noun;
	for ($verb = 0; $verb <= 99; ++$verb) {
		$sourceVerb = $sourceNoun;
		$sourceVerb[2] = $verb;

		$interpreter = new \adventOfCode\Computer\Interpreter($sourceVerb);
		$interpreter->run();
		$result = $interpreter->addrLoad(0);
		if ($noun == 12 && $verb == 2) {
			echo "First task: Noun is: $noun, verb is: $verb. Result is {$result} .\n";
		}
		if ($result == 19690720) {
			echo "Second task: Noun is: $noun, verb is: $verb. Result is {$result}. So 100 * $noun + $verb = ", 100 * $noun + $verb, "\n";
		}
	}
}
