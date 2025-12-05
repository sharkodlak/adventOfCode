from __future__ import annotations
from typing import Iterable, List


def parse_input(raw_text: str) -> List[List[bool]]:
	"""Parse lines into grid of paper rolls."""
	grid: List[List[bool]] = []
	for y, line in enumerate(raw_text.splitlines()):
		for x, char in enumerate(line):
			if y >= len(grid):
				grid.append([])
			if x >= len(grid[y]):
				grid[y].append(char == "@")
	return grid


def part_one(grid: List[List[bool]], remove: bool = False) -> int:
	"""Find positions with less than 4 adjacent rolls."""
	positions = 0
	for y, row in enumerate(grid):
		for x, cell in enumerate(row):
			if cell:
				adjacent = 0
				for dy in (-1, 0, 1):
					for dx in (-1, 0, 1):
						if dy == 0 and dx == 0:
							continue
						ny = y + dy
						nx = x + dx
						if 0 <= ny < len(grid) and 0 <= nx < len(row) and grid[ny][nx]:
							adjacent += 1
				if adjacent < 4:
					if remove:
						grid[y][x] = False
					positions += 1
	return positions

def part_two(grid: List[List[bool]]) -> int:
	"""Repeat: find positions with less than 4 adjacent rolls."""
	positions = 0
	while (removed := part_one(grid, remove=True)) > 0:
		positions += removed
	return positions


if __name__ == "__main__":  # pragma: no cover
	from pathlib import Path

	input_path = Path(__file__).resolve().parents[2] / "inputs" / "04.sample.txt"
	raw = input_path.read_text(encoding="utf-8")
	parsed = parse_input(raw)
	print("Part 1:", part_one(parsed))
	parsed = parse_input(raw)
	print("Part 2:", part_two(parsed))
