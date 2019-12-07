#!/usr/bin/env php
<?php declare(strict_types=1);

$lines = file(substr(__FILE__, 0, -4) . '/input.txt', FILE_IGNORE_NEW_LINES);
$nodes = [];

class TreeNode {
	public $parent;
	public $children = [];
	public function __construct($parent) {
		$this->parent = $parent;
	}
	public function depth() {
		return empty($this->parent) ? 0 : 1 + $this->parent->depth();
	}
	public function commonAncestor($other) {
		$thisDepth = $this->depth();
		$otherDepth = $other->depth();
		$delta = $thisDepth - $otherDepth;
		$shallower = $delta > 0 ? $other : $this;
		$deeper = $delta > 0 ? $this : $other;
		for ($i = 0; $i < abs($delta); ++$i) {
			$deeper = $deeper->parent;
		}
		while ($shallower->parent !== $deeper->parent) {
			$shallower = $shallower->parent;
			$deeper = $deeper->parent;
			$i += 2;
		}
		return $i;
	}
}

foreach ($lines as $line) {
	[$parent, $child] = explode(')', $line);
	$nodes[$parent] = $nodes[$parent] ?? new TreeNode(null);
	$nodes[$child] = $nodes[$child] ?? new TreeNode($nodes[$parent]);
	$nodes[$parent]->children[] = $nodes[$child];
	$nodes[$child]->parent = $nodes[$parent];
}

$depths = array_map(function($node) {
	return $node->depth();
}, $nodes);

$sum = array_sum($depths);

echo "Total number of orbits is: $sum .\n";

$transitions = $nodes['YOU']->commonAncestor($nodes['SAN']);

echo "Number of transitions is: $transitions .\n";
