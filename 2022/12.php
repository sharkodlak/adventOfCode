#!/usr/bin/env php
<?php declare(strict_types=1);

use Directory as GlobalDirectory;

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));

foreach ($inputLoader as $line) {
	//\adventOfCode\lib\Dumper::dump([$instruction, $value, $registerX]);
}

printf("Monkey bussiness: %d .\n", $product);
