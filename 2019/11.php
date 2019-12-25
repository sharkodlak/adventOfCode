#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$file = file_get_contents(substr(__FILE__, 0, -4) . '/input.txt');
$source = explode(',', $file);
$source = array_map('intval', $source);

$interpreter = new \adventOfCode\Computer\Interpreter($source);

function paint(\adventOfCode\Computer\Interpreter $interpreter, bool $startingColorWhite) {
	$image = [];
	$x = 0;
	$y = 0;
	$image[0][0] = $startingColorWhite ? 1 : 0;
	$direction = 0;
	do {
		$onColor = $image[$y][$x] ?? 0;
		$interpreter->input($onColor);
		[$color, $rotation] = $interpreter->run();
		//echo "[$x; $y] Color: $color, rotation: $rotation. \n";
		$image[$y][$x] = $color;
		$direction += $rotation ? 1 : 3;
		switch ($direction % 4) {
			case 0:
				++$y;
			break;
			case 1:
				++$x;
			break;
			case 2:
				--$y;
			break;
			case 3:
				--$x;
			break;
		}
	} while (!$interpreter->done());
	return $image;
}

$image = paint($interpreter, false);

$sumCounts = function($sum, $imageRow) {
	return $sum + count($imageRow);
};
$sum = array_reduce($image, $sumCounts, 0);

echo "Sum of painted tiles is: $sum .\n";

$interpreter = new \adventOfCode\Computer\Interpreter($source);
$image = paint($interpreter, true);

foreach ($image as $imageRow) {
	$maxX = max(array_keys($imageRow));
	for ($i = 0; $i < $maxX; ++$i) {
		$color = $imageRow[$i] ?? 0;
		echo $color ? '#' : ' ';
	}
	echo "\n";
}
