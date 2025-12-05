from __future__ import annotations
from typing import Iterable, List


def parse_input(raw_text: str) -> tuple[List[range], List[int]]:
	"""Parse lines into ranges of fresh ids and a list of ids to check."""
	fresh_ranges: List[range] = []
	ids: List[int] = []
	first_part = True
	for line in raw_text.splitlines():
		if not line.strip():
			first_part = False
			continue
		if first_part:
			parts = line.split('-')
			if len(parts) != 2:
				raise ValueError(f"Invalid range instruction: {line}")
			start = int(parts[0])
			end = int(parts[1])
			fresh_ranges.append(range(start, end + 1))
		else:
			ids.append(int(line.strip()))
	return fresh_ranges, ids


def part_one(fresh_ranges: List[range], ids: List[int]) -> int:
	"""Find how many ingredients are fresh_ranges ids."""
	fresh = 0
	for id in ids:
		if any(id in r for r in fresh_ranges):
			fresh += 1
	return fresh

def part_two(fresh_ranges: List[range], ids: List[int]) -> int:
	"""Find how many ingredients are fresh_ranges ids."""
	fresh = 0
	return fresh


if __name__ == "__main__":  # pragma: no cover
	from pathlib import Path

	input_path = Path(__file__).resolve().parents[2] / "inputs" / "05.sample.txt"
	raw = input_path.read_text(encoding="utf-8")
	parsed = parse_input(raw)
	print("Part 1:", part_one(*parsed))
	parsed = parse_input(raw)
	print("Part 2:", part_two(*parsed))
