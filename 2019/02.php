#!/usr/bin/env php
<?php declare(strict_types=1);

$file = file_get_contents(substr(__FILE__, 0, -4) . '/input.txt');
$source = explode(',', $file);
$source = array_map('intval', $source);

for ($noun = 0; $noun <= 99; ++$noun) {
	$sourceNoun = $source;
	$sourceNoun[1] = $noun;
	for ($verb = 0; $verb <= 99; ++$verb) {
		$sourceVerb = $sourceNoun;
		$sourceVerb[2] = $verb;
		for ($i = 0; $i < count($source); $i += 4) {
			$opcode = $sourceVerb[$i];
			$addrA = $sourceVerb[$i + 1];
			$addrB = $sourceVerb[$i + 2];
			$addrS = $sourceVerb[$i + 3];
			switch ($opcode) {
				case 1:
					$sourceVerb[$addrS] = $sourceVerb[$addrA] + $sourceVerb[$addrB];
				break;
				case 2:
					$sourceVerb[$addrS] = $sourceVerb[$addrA] * $sourceVerb[$addrB];
				break;
				case 99:
					if ($noun == 12 && $verb == 2) {
						echo "First task: Noun is: $noun, verb is: $verb. Result is {$sourceVerb[0]} .\n";
					}
					if ($sourceVerb[0] == 19690720) {
						echo "Second task: Noun is: $noun, verb is: $verb. Result is {$sourceVerb[0]}. So 100 * $noun + $verb = ", 100 * $noun + $verb, "\n";
					}
				break(2);
				default:
					exit('Error, unknown instruction: '. $opcode);
			}
		}
	}
}
