#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
// 2, 3
// 3, 2
// 2, 1
// 1, 0 -> 1
function gcd(int $a, int $b) {
	return $b ? gcd($b, $a % $b) : $a;
}

$lines = file(substr(__FILE__, 0, -4) . '/input.txt', FILE_IGNORE_NEW_LINES);
//$lines = ['.#..#','.....','#####','....#','...##'];
/*$lines = [
	'......#.#.',
	'#..#.#....',
	'..#######.',
	'.#.#.###..',
	'.#..#.....',
	'..#....#.#',
	'#..#....#.',
	'.##.#..###',
	'##...#..#.',
	'.#....####',
];
$lines = [
	'.#....#####...#..',
	'##...##.#####..##',
	'##...#...#.#####.',
	'..#.....X...###..',
	'..#.#.....#....##',
];*/

$asteroids = [];
foreach ($lines as $y => $line) {
	for ($x = 0; $x < strlen($line); ++$x) {
		switch ($line[$x]) {
			case '.':
				// Continue
			break;
			case '#':
			case 'X':
				$asteroids["$x;$y"] = ['x' => $x, 'y' => $y];
			break;
			default:
				throw new Exception("Unknown character '$line[$x]'!");
		}
	}
}

function filterVisible(array $current, array $asteroids): array {
	$visibles = [];
	foreach ($asteroids as $targetKey => $target) {
		if ($current['x'] != $target['x'] || $current['y'] != $target['y']) {
			$addToVisibles = true;
			if (!isset($target['x'])) var_dump($target);
			$relX = $target['x'] - $current['x'];
			$relY = $target['y'] - $current['y'];
			$divisor = abs(gcd($relX, $relY));
			$angleX = $divisor ? $relX / $divisor : $relX;
			$angleY = $divisor ? $relY / $divisor : $relY;
			//    -y
			// -x     +x
			//    +y
			$angle = atan2($angleX, -$angleY) / M_PI;
			if ($angle < 0) {
				$angle += 2;
			}
			//echo "  Target [{$target['x']}; {$target['y']}] divisor $divisor angle($angleX; $angleY) $angle rel($relX; $relY).\n";
			foreach ($visibles as $visibleKey => $visible) {
				// If target and visible have same angle -> who's closer?
				// 1. visible is closer -> target['invisibles'][] = current, addToVisibles = false
				//    [2,4] < [3,6]
				// 2. target is closer  -> visible['invisibles'][] = current, unset visibles[visible]
				//echo "    Previously visible angle({$visible['angleX']}; {$visible['angleY']}).\n";
				if ($angleX == $visible['angleX'] && $angleY == $visible['angleY']) {
					//echo "    Same angle!\n";
					if (abs($visible['relX']) < abs($relX)) {
						//echo "    Previously visible is closer.\n";
						//$asteroids[$targetKey]['invisibles'][$currentKey] = $current;
						$addToVisibles = false;
					} else {
						//echo "    Previously visible is further.\n";
						//$asteroids[$visibleKey]['invisibles'][$currentKey] = $current;
						unset($visibles[$visibleKey]);
					}
					break;
				}
			}
			if ($addToVisibles) {
				$visibles["{$target['x']};{$target['y']}"] = ['relX' => $relX, 'relY' => $relY, 'angleX' => $angleX, 'angleY' => $angleY, 'angle' => $angle] + $target;
			}
		}
	}
	return $visibles;
}

$maxVisible = null;
$maxVisibleCount = 0;
foreach ($asteroids as $currentKey => $current) {
	//echo "Current is [{$current['x']}; {$current['y']}]:\n";
	$visibles = filterVisible($current, $asteroids);
	$asteroids[$currentKey]['visibles'] = $visibles;
	$visibleCount = count($visibles);
	if ($visibleCount > $maxVisibleCount) {
		$maxVisible = $currentKey;
		$maxVisibleCount = $visibleCount;
	}
}

echo "Max visible is: [{$asteroids[$maxVisible]['x']}; {$asteroids[$maxVisible]['y']}] with $maxVisibleCount asteroids in sight.\n";

$cmp = function ($a, $b) {
	return ceil($a['angle'] - $b['angle']);
};
$current = $asteroids[$maxVisible];
unset($asteroids[$maxVisible]);
$i = 1;
do {
	$visibles = filterVisible($current, $asteroids);
	uasort($visibles, $cmp);
	foreach ($visibles as $visible) {
		if (200 == $i++) {
			echo "200th vaporized asteroid is [{$visible['x']}; {$visible['y']}], so result is ", $visible['x'] * 100 + $visible['y'] ,"\n";
		}
		//echo $i++, " angle {$visible['angle']} ({$visible['angleX']}; {$visible['angleY']}) [{$visible['x']}; {$visible['y']}]\n";
	}
	$asteroids = array_diff_key($asteroids, $visibles);
} while($asteroids);
