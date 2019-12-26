#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$file = file_get_contents(substr(__FILE__, 0, -4) . '/input.txt');
$source = explode(',', $file);
$source = array_map('intval', $source);

$source[0] = 2;

$interpreter = new \adventOfCode\Computer\Interpreter($source);
$types = [
	' ',
	"\033[30;47m#\033[0m",
	"\033[43mb\033[0m",
	"\033[30;47m-\033[0m",
	"\033[34mo\033[0m",
];
$blocks = 0;
$paddleX = 0;
$ballX = 0;
$score = 0;

while (!$interpreter->done()) {
	$output = $interpreter->run(3);
	if ($interpreter->isInput()) {
		$joystickMove = $ballX - $paddleX;
		if ($joystickMove > 1) {
			$joystickMove = 1;
		} else if ($joystickMove < -1) {
			$joystickMove = -1;
		}
		$interpreter->input($joystickMove);
	} else if ($output) {
		[$col, $row, $type] = $output;
		if ($col == -1) {
			$score = $type;
			printf("\033[%d;%df%s\n", 26, 20, $score);
		} else {
			if ($type == 2) {
				++$blocks;
			} else if ($type == 3) {
				$paddleX = $col;
			} else if ($type == 4) {
				$ballX = $col;
			}
			printf("\033[%d;%df%s", ++$row, ++$col, $types[$type]);
			if ($type > 2) {
				usleep(20*1000);
			}
		}
	}
}

echo "Number of blocks is: $blocks .\n";
