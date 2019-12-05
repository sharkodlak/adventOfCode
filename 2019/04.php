#!/usr/bin/env php
<?php declare(strict_types=1);

$good = [];
$better = [];
for ($i = 273025; $i < 767253; ++$i) {
	$s = (string) $i;
	$nr = 0;
	$match = false;
	$groups = [];
	for ($c = 0; $c < 6; ++$c) {
		$d = intval($s[$c]);
		if ($nr > $d) {
			$match = false;
			break;
		} else if ($nr == $d) {
			$match = true;
		}
		$nr = $s[$c];
		$groups[$d] = 1 + ($groups[$d] ?? 0);
	}
	if ($match) {
		$good[] = $i;
		if (in_array(2, $groups, true)) {
			$better[] = $i;
		}
	}
}

echo "Number of good passwords is: ", count($good), " .\n";
echo "Number of better passwords is: ", count($better), " .\n";
