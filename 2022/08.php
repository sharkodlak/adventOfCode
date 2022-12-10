#!/usr/bin/env php
<?php declare(strict_types=1);

use Directory as GlobalDirectory;

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));
$trees = [];
$treesVisibleCount = 0;

class Tree {
	private bool $isVisible = false;

	public function __construct(
		private int $height,
	) {}

	public function getHeight(): int {
		return $this->height;
	}

	public function setVisible(): self {
		$this->isVisible = true;
		return $this;
	}

	public function isVisible(): bool {
		return $this->isVisible;
	}
}

foreach ($inputLoader as $y => $line) {
	$row = str_split($line);

	foreach ($row as $x => $height) {
		$tree = new Tree((int) $height);
		$trees[$y][$x] = $tree;
		//\adventOfCode\lib\Dumper::dump($tree);
	}
}

abstract class PlanarIterator implements Iterator {
	protected int $colsCount;
	protected int $rowsCount;
	protected int $x;
	protected int $y;

	public function __construct(
		protected array $plane
	) {
		$this->colsCount = count($this->plane[0]);
		$this->rowsCount = count($this->plane);
	}

	public function current(): mixed {
		return $this->plane[$this->y][$this->x];
	}

	public function key(): mixed {
		return sprintf('y:%d,x:%d', $this->y, $this->x);
	}

	public function valid(): bool {
		return isset($this->plane[$this->y][$this->x]);
	}

	abstract public function isRetrace(): bool;
}

class PlanarLeftToRightUpToDownIterator extends PlanarIterator {
	public function next(): void {
		$this->x++;
		if ($this->x === $this->colsCount) {
			$this->y++;
			$this->x = 0;
		}
	}

	public function rewind(): void {
		$this->x = 0;
		$this->y = 0;
	}

	public function isRetrace(): bool {
		return $this->x === 0;
	}
}

class PlanarRightToLeftUpToDownIterator extends PlanarIterator {
	public function next(): void {
		$this->x--;
		if ($this->x === -1) {
			$this->y++;
			$this->x = $this->getMaxX();
		}
	}

	public function rewind(): void {
		$this->x = $this->getMaxX();
		$this->y = 0;
	}

	public function isRetrace(): bool {
		return $this->x === $this->getMaxX();
	}

	private function getMaxX(): int {
		return $this->colsCount - 1;
	}
}

class PlanarUpToDownLeftToRightIterator extends PlanarIterator {
	public function next(): void {
		$this->y++;
		if ($this->y === $this->rowsCount) {
			$this->x++;
			$this->y = 0;
		}
	}

	public function rewind(): void {
		$this->x = 0;
		$this->y = 0;
	}

	public function isRetrace(): bool {
		return $this->y === 0;
	}
}

class PlanarDownToUpLeftToRightIterator extends PlanarIterator {
	public function next(): void {
		$this->y--;
		if ($this->y === 0) {
			$this->x++;
			$this->y = $this->getMaxY();
		}
	}

	public function rewind(): void {
		$this->x = 0;
		$this->y = $this->getMaxY();
	}

	public function isRetrace(): bool {
		return $this->y === $this->getMaxY();
	}

	private function getMaxY(): int {
		return $this->rowsCount - 1;
	}
}

abstract class PlanarOneDirectionIterator extends PlanarIterator {
	private int $resetX;
	private int $resetY;

	public function key(): mixed {
		return abs($this->x - $this->resetX) + abs($this->y - $this->resetY);
	}

	public function rewind(): void {
		$this->x = $this->resetX;
		$this->y = $this->resetY;
	}

	public function isRetrace(): bool {
		return false;
	}

	public function reset(int $resetX, int $resetY): self {
		$this->resetX = $resetX;
		$this->resetY = $resetY;
		return $this;
	}
}

class PlanarRightIterator extends PlanarOneDirectionIterator {
	public function next(): void {
		$this->x++;
	}
}

class PlanarLeftIterator extends PlanarOneDirectionIterator {
	public function next(): void {
		$this->x--;
	}
}

class PlanarUpIterator extends PlanarOneDirectionIterator {
	public function next(): void {
		$this->y--;
	}
}

class PlanarDownIterator extends PlanarOneDirectionIterator {
	public function next(): void {
		$this->y++;
	}
}

$toRightIterator = new PlanarLeftToRightUpToDownIterator($trees);
$toLeftIterator = new PlanarRightToLeftUpToDownIterator($trees);
$toDownIterator = new PlanarUpToDownLeftToRightIterator($trees);
$toUpIterator = new PlanarDownToUpLeftToRightIterator($trees);
$iterators = [$toRightIterator, $toLeftIterator, $toDownIterator, $toUpIterator];

foreach ($iterators as $iterator) {
	foreach ($iterator as $pos => $tree) {
		if ($iterator->isRetrace()) {
			$maxHeight = PHP_INT_MIN;
		}
	
		if ($maxHeight < $tree->getHeight()) {
			$maxHeight = $tree->setVisible()->getHeight();
		}
	}	
}

$rightIterator = new PlanarRightIterator($trees);
$leftIterator = new PlanarLeftIterator($trees);
$upIterator = new PlanarUpIterator($trees);
$downIterator = new PlanarDownIterator($trees);
$oneDirectionIterators = [$rightIterator, $leftIterator, $upIterator, $downIterator];
$highestScenicScore = 0;

foreach ($trees as $y => $row) {
	foreach ($row as $x => $tree) {
		//printf('%d', $tree->isVisible());
		if ($tree->isVisible()) {
			++$treesVisibleCount;
		}

		$scenicScore = 1;
		foreach ($oneDirectionIterators as $iterator) {
			$iterator->reset($x, $y);
			foreach ($iterator as $i => $sceneTree) {
				if ($i > 0 && $tree->getHeight() <= $sceneTree->getHeight()) {
					break;
				}
			}
			//\adventOfCode\lib\Dumper::dump($y, $x, $iterator::class, $i);
			$scenicScore *= $i;
		}

		$highestScenicScore = max($highestScenicScore, $scenicScore);
		//printf('%2d ', $scenicScore);
	}
	//echo "\n";
}

printf("Number of trees visible: %d .\n", $treesVisibleCount);
printf("Highest scenicscore: %d .\n", $highestScenicScore);
