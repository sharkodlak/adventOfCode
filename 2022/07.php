#!/usr/bin/env php
<?php declare(strict_types=1);

use Directory as GlobalDirectory;

require_once __DIR__ . '/../vendor/autoload.php';

$inputLoader = new \adventOfCode\lib\InputLoader(__FILE__, in_array('--sample', $argv));

interface FileSystem {
	public function getName(): string;
	public function getSize(): int;
}

class FsFile implements FileSystem {
	public function __construct(
		private string $fileName,
		private int $fileSize
	) {}

	public function getName(): string {
		return $this->fileName;
	}

	public function getSize(): int {
		return $this->fileSize;
	}
}

class FsDirectory implements FileSystem {
	private array $children = [];

	public function __construct(
		private string $dirName,
		private ?self $parent
	) {}

	public function addChild(FileSystem $child): self {
		$this->children[$child->getName()] = $child;
		return $this;
	}

	public function getChildDirectory(string $name): self {
		return $this->children[$name];
	}

	public function getChildren(): array {
		return $this->children;
	}

	public function getName(): string {
		return $this->dirName;
	}

	public function getParent(): self {
		return $this->parent;
	}

	public function getSize(): int {
		return array_reduce($this->children, fn(int $carry, FileSystem $child) => $carry + $child->getSize(), 0);
	}
}

class FsDepthFirstIterator implements Iterator {
	private array $toTraverse;

	public function __construct(
		private FileSystem $root
	) {}

	public function current(): mixed {
		return reset($this->toTraverse);
	}

	public function next(): void {
		$node = array_shift($this->toTraverse);
		if ($node instanceof FsDirectory) {
			array_splice($this->toTraverse, 0, 0, $node->getChildren());
		}
	}

	public function key(): mixed {
		return key($this->toTraverse);
	}

	public function valid(): bool {
		return !empty($this->toTraverse);
	}

	public function rewind(): void {
		$this->toTraverse = [$this->root->getName() => $this->root];
	}
}

$directoryTree = new FsDirectory('/', null);
$cwd = $directoryTree;

foreach ($inputLoader as $line) {
	if ($line[0] === '$') {
		$command = substr($line, 2, 2);
		if ($command === 'cd') {
			$arg = substr($line, 5);
			if ($arg === '/') {
				$cwd = $directoryTree;
			} else if ($arg === '..') {
				$cwd = $cwd->getParent();
			} else {
				$cwd = $cwd->getChildDirectory($arg);
			}
		}
	} else {
		[$dirOrSize, $name] = explode(' ', $line);
		if ($dirOrSize === 'dir') {
			$newFileSystem = new FsDirectory($name, $cwd);
		} else {
			$newFileSystem = new FsFile($name, (int) $dirOrSize);
		}
		$cwd->addChild($newFileSystem);
	}
}

$fsIterator = new FsDepthFirstIterator($directoryTree);
$sumOfRightDirectories = 0;
const DIRECTORY_SIZE = 100_000;
const FILESYSTEM_SIZE = 70_000_000;
const REQUIRED_FREE_SPACE = 30_000_000;
$spaceToDelete = REQUIRED_FREE_SPACE - FILESYSTEM_SIZE + $directoryTree->getSize();
$direcotrySizeToDelete = REQUIRED_FREE_SPACE;

foreach ($fsIterator as $node) {
	if ($node instanceof FsDirectory && $node->getSize() < DIRECTORY_SIZE) {
		$sumOfRightDirectories += $node->getSize();
	}
	if ($node instanceof FsDirectory && $node->getSize() >= $spaceToDelete && $node->getSize() < $direcotrySizeToDelete) {
		$direcotrySizeToDelete = $node->getSize();
	}
	//\adventOfCode\lib\Dumper::dump([$node->getName() => $node->getSize()]);
}

printf("Total small directories size: %d .\n", $sumOfRightDirectories);
printf("Directory size to delete: %d .\n", $direcotrySizeToDelete);
