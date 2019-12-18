#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$file = file_get_contents(substr(__FILE__, 0, -4) . '/input.txt');
$source = explode(',', $file);
$source = array_map('intval', $source);
//$source = [109,1,204,-1,1001,100,1,100,1008,100,16,101,1006,101,0,99];
//$source = [1102,34915192,34915192,7,4,7,99,0];
//$source = [104,1125899906842624,99];

$interpreter = new \adventOfCode\Computer\Interpreter($source);
$output = $interpreter->run();
$interpreter->input(2);
$output += $interpreter->run();
echo "Output: " . implode(', ', $output) . " .\n";
echo $interpreter->done() ? "Done.\n" : "halted...\n";
