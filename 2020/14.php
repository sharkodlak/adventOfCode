#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$lines = file(substr(__FILE__, 0, -4) . '/input.txt');
$memFirst = [];
$memSecond = [];

foreach ($lines as $line) {
	if (preg_match('~^mask = ([X01]+)$~', $line, $matches)) {
		$mask = $matches[1];
		$and = 0;
		$or = 0;
		$float = 0;
		for ($i = 0; $i < strlen($mask); ++$i) {
			$chr = $mask[$i];
			$and <<= 1;
			$or <<= 1;
			$float <<= 1;
			$and |= intval($chr !== '0');
			$or |= intval($chr === '1');
			$float |= intval($chr === 'X');
		}
		$floatAddresses = [0];
		for ($i = 0; $i < 36; ++$i) {
			if ($float >> $i & 1) {
				foreach ($floatAddresses as $floatAddress) {
					$floatAddresses[] = $floatAddress | 1 << $i;
				}
			}
		}
	} else if (preg_match('~^mem\[(\d+)\] = (\d+)$~', $line, $matches)) {
		$address = (int) $matches[1];
		$value = (int) $matches[2];
		$memFirst[$address] = $value & $and | $or;
		$address |= $or;
		$address &= ~$float;
		foreach ($floatAddresses as $floatAddress) {
			$memSecond[$address | $floatAddress] = $value;
		}
	}
}

printf("Docking v.1 memory sum: %d .\n", array_sum($memFirst));
printf("Docking v.2 memory sum: %d .\n", array_sum($memSecond));
