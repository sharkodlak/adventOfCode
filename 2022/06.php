#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$packetLength = 4;
$messageMarkerLength = 14;

foreach ($inputLoader as $line) {
	$packetPos = null;
	$lineLength = strlen($line);
	for ($i = 0; $i < $lineLength; ++$i) {
		if (!isset($packetPos)) {
			$chunk = substr($line, $i, $packetLength);
			//\adventOfCode\lib\Dumper::dump($chunk);
			for ($aPos = 0; $aPos < $packetLength - 1; ++$aPos) {
				for ($bPos = $aPos + 1; $bPos < $packetLength; ++$bPos) {
					if ($chunk[$aPos] === $chunk[$bPos]) {
						//\adventOfCode\lib\Dumper::dump([$aPos => $chunk[$aPos], $bPos => $chunk[$bPos]]);
						continue(3);
					}
				}
			}
			$packetPos = $i + $packetLength;
		}

		$chunk = substr($line, $i, $messageMarkerLength);
		//\adventOfCode\lib\Dumper::dump([$i => $chunk]);
		for ($aPos = 0; $aPos < $messageMarkerLength - 1; ++$aPos) {
			for ($bPos = $aPos + 1; $bPos < $messageMarkerLength; ++$bPos) {
				//\adventOfCode\lib\Dumper::dump([$aPos => $bPos]);
				if ($chunk[$aPos] === $chunk[$bPos]) {
					//\adventOfCode\lib\Dumper::dump([$aPos => $chunk[$aPos], $bPos => $chunk[$bPos]]);
					continue(3);
				}
			}
		}
		$messagePos = $i + $messageMarkerLength;
		break;
	}
	//\adventOfCode\lib\Dumper::dump(...$crates9001);
	printf("Packet starts at: %s .\n", $packetPos);
	printf("Message starts at: %s .\n", $messagePos);
}
