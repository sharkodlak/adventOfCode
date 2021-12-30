#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$input = $argc == 1 ? '' : ".{$argv[1]}";
if ($argc != 1) {
	echo "\n --->   SAMPLE   <---\n\n";
}
$inputFile = sprintf('%s/inputs/%02d%s.txt', __DIR__, basename(__FILE__, '.php'), $input);
$lines = file($inputFile);

class Segments {
	private static array $digitsSegments;
	private static array $uniqueDigits = [
		1 => 2,
		4 => 4,
		7 => 3,
		8 => 7,
	];

	private array $digits;
	private array $segmentsCount = [
		'a' => 0,
		'b' => 0,
		'c' => 0,
		'd' => 0,
		'e' => 0,
		'f' => 0,
		'g' => 0,
	];

	public function __construct(array $digits, private array $output) {
		$this->digits = array_map('str_split', $digits);
		foreach ($this->digits as $digit) {
			foreach ($digit as $segment) {
				++$this->segmentsCount[$segment];
			}
		}
	}

	public function countEasyDigits(): int {
		$easyDigits = 0;

		foreach ($this->output as $digit) {
			if (in_array(strlen($digit), self::$uniqueDigits)) {
				++$easyDigits;
			}
		}

		return $easyDigits;
	}
	
	public function getOutputNumber(): int {
		$output = 0;
		$segmentsWiring = $this->findSegmentsWiring();

		foreach ($this->output as $digit) {
			$digit = str_split($digit);
			$digitsSegments = self::getDigitsSegments();

			foreach ($digit as $segment) {
				$wiring = $segmentsWiring[$segment];

				foreach ($digitsSegments as $key => $digitSegments) {
					if (!in_array($wiring, $digitSegments)) {
						unset($digitsSegments[$key]);
					}
				}
			}
			$output = $output * 10 + key($digitsSegments);
		}

		return $output;
	}

	private static function getDigitsSegments(): array {
		if (!isset(self::$digitsSegments)) {
			$digitsSegments = [
				0 => ['A', 'B', 'C', 'E', 'F', 'G'],
				1 => ['C', 'F'],
				2 => ['A', 'C', 'D', 'E', 'G'],
				3 => ['A', 'C', 'D', 'F', 'G'],
				4 => ['B', 'C', 'D', 'F'],
				5 => ['A', 'B', 'D', 'F', 'G'],
				6 => ['A', 'B', 'D', 'E', 'F', 'G'],
				7 => ['A', 'C', 'F'],
				8 => ['A', 'B', 'C', 'D', 'E', 'F', 'G'],
				9 => ['A', 'B', 'C', 'D', 'F', 'G'],
			];
			uasort($digitsSegments, fn($a, $b) => count($a) - count($b));
			self::$digitsSegments = $digitsSegments;
		}

		return self::$digitsSegments;
	}

	public function findSegmentsWiring(): array {
		$digits = $this->getEasyDigits($this->digits);
		$segments['A'] = current(array_diff($digits[7], $digits[1]));
		$segments['B'] = array_search(6, $this->segmentsCount);
		$segments['E'] = array_search(4, $this->segmentsCount);
		$segments['F'] = array_search(9, $this->segmentsCount);
		$segments['C'] = current(array_diff($digits[1], [$segments['F']]));
		$segments['D'] = current(array_diff($digits[4], [$segments['B'], $segments['C'], $segments['F']]));
		$segments['G'] = current(array_diff(array_keys($this->segmentsCount), $segments));
		return array_flip($segments);
	}
	
	private function getEasyDigits(array $digits): array {
		$easyDigits = [];

		foreach ($digits as $digit) {
			$digitSegments = count($digit);
			$number = array_search($digitSegments, self::$uniqueDigits);
			if ($number !== false) {
				$easyDigits[$number] = $digit;
			}
		}

		return $easyDigits;
	}
}

$displays = [];

foreach ($lines as $line)  {
	[$digits, $toDisplay] = explode(' | ', trim($line));
	$digits = explode(' ', $digits);
	$toDisplay = explode(' ', $toDisplay);
	$displays[] = new Segments($digits, $toDisplay);
}

$easyDigits = 0;
$displaySum = 0;

foreach ($displays as $display) {
	$easyDigits += $display->countEasyDigits();
	$displaySum += $display->getOutputNumber();
}

printf("Easy digits count is %d .\n", $easyDigits);
printf("Display sum is %d .\n", $displaySum);
