#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');

$mem = [];
foreach ($lines as $line) {
	if (preg_match('~^mask = ([X01]+)$~', $line, $matches)) {
		$mask = $matches[1];
		$and = 0;
		$or = 0;
		for ($i = 0; $i < strlen($mask); ++$i) {
			$chr = $mask[$i];
			$and <<= 1;
			$or <<= 1;
			if ($chr === '0') {
				$and |= 0;
			} else {
				$and |= 1;
			}
			if ($chr === '1') {
				$or |= 1;
			} else {
				$or |= 0;
			}
		}
	} else if (preg_match('~^mem\[(\d+)\] = (\d+)$~', $line, $matches)) {
		$address = (int) $matches[1];
		$value = (int) $matches[2];
		$mem[$address] = $value & $and | $or;
	}
}

printf("Memory sum %d .\n", array_sum($mem));
