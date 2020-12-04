#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$fp = fopen(substr(__FILE__, 0, -4) . '/input.txt', 'r');
$fields = [
	'byr' => fn($x) => is_numeric($x) && 1920 <= $x && $x <= 2002,
	'iyr' => fn($x) => is_numeric($x) && 2010 <= $x && $x <= 2020,
	'eyr' => fn($x) => is_numeric($x) && 2020 <= $x && $x <= 2030,
	'hgt' => function($x) {
		$size = substr($x, 0, -2);
		$measure = substr($x, -2);
		if ($measure == 'cm') {
			return 150 <= $size && $size <= 193;
		} else if ($measure == 'in') {
			return 59 <= $size && $size <= 76;
		}
		return false;
	},
	'hcl' => function($x) {
		return (bool) preg_match('~^#[0-9a-f]{6}$~', $x);
	},
	'ecl' => fn($x) => in_array($x, ['amb', 'blu', 'brn', 'gry', 'grn', 'hzl', 'oth'], true),
	'pid' => function($x) {
		return (bool) preg_match('~^\d{9}$~', $x);
	},
	'cid' => fn($x) => true,
];
unset($fields['cid']);
$token = '';
$documents = [];
$i = 0;

while (false !== ($c = fgetc($fp))) {
	switch ($c) {
		case ':':
			$field = $token;
			$token = '';
		break;
		case ' ':
		case "\n":
			if ($token == '') {
				++$i;
			} else {
				$documents[$i][$field] = $token;
				$token = '';
			}
		break;
		default:
			$token .= $c;
	}
}

$allFieldsDocuments = 0;
$validDocuments = 0;
foreach ($documents as $document) {
	$missing = array_diff_key($fields, $document);
	if (empty($missing)) {
		++$allFieldsDocuments;
		foreach ($document as $field => $value) {
			$fn = $fields[$field] ?? null;
			if (isset($fn)) {
				var_dump($field, $value, $fn($value));
				if (!$fn($value)) {
					continue 2;
				}
			}
		}
		++$validDocuments;
	}
}

echo "Documents with all fields: $allFieldsDocuments .\n";
echo "Valid documents: $validDocuments .\n";
