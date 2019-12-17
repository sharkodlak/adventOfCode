#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$file = file_get_contents(substr(__FILE__, 0, -4) . '/input.txt');
$source = explode(',', $file);
$source = array_map('intval', $source);
//$source = [3,0,4,0,99];
//$source = [1002,4,3,4,33];
//$source = [1101,100,-1,4,0];
//$source = [3,9,8,9,10,9,4,9,99,-1,8];
//$source = [3,9,7,9,10,9,4,9,99,-1,8];
//$source = [3,3,1108,-1,8,3,4,3,99];
//$source = [3,3,1107,-1,8,3,4,3,99];
//$source = [3,12,6,12,15,1,13,14,13,4,13,99,-1,0,1,9];
//$source = [3,3,1105,-1,9,1101,0,0,12,4,12,99,1];
//$source = [3,21,1008,21,8,20,1005,20,22,107,8,21,20,1006,20,31,1106,0,36,98,0,0,1002,21,125,20,4,20,1105,1,46,104,999,1105,1,46,1101,1000,1,20,4,20,1105,1,46,98,99];


$interpreter = new \adventOfCode\Computer\Interpreter($source);
$interpreter->run();
$interpreter->input(5);
$result = $interpreter->run();
echo $interpreter->done() ? 'done' : 'halted', "\n";
//var_dump($interpreter->getSource());

echo "Result is: ", implode(', ', $result), " .\n";
