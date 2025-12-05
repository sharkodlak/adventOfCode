from __future__ import annotations
from typing import Iterable, List


def parse_input(raw_text: str) -> List[List[bool]]:
	"""Parse lines into coordinates of paper rolls."""
	coordinates: List[List[bool]] = []
	for y, line in enumerate(raw_text.splitlines()):
		for x, char in enumerate(line):
			if y >= len(coordinates):
				coordinates.append([])
			if x >= len(coordinates[y]):
				coordinates[y].append(char == "@")
	return coordinates


def part_one(coordinates: List[List[bool]]) -> int:
	"""Find positions with less than 4 adjacent rolls."""
	positions = 0
	for y, row in enumerate(coordinates):
		for x, cell in enumerate(row):
			if cell:
				adjacent = 0
				for dy in (-1, 0, 1):
					for dx in (-1, 0, 1):
						if dy == 0 and dx == 0:
							continue
						ny = y + dy
						nx = x + dx
						if 0 <= ny < len(coordinates) and 0 <= nx < len(row) and coordinates[ny][nx]:
							adjacent += 1
				if adjacent < 4:
					positions += 1
	return positions

def part_two(banks: List[List[int]]) -> int:
	"""Find positions with less than 4 adjacent rolls."""
	return 0


if __name__ == "__main__":  # pragma: no cover
	from pathlib import Path

	input_path = Path(__file__).resolve().parents[2] / "inputs" / "04.sample.txt"
	raw = input_path.read_text(encoding="utf-8")
	parsed = parse_input(raw)
	print("Part 1:", part_one(parsed))
	print("Part 2:", part_two(parsed))
