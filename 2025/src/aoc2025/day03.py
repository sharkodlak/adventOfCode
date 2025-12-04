from __future__ import annotations
from functools import reduce
from typing import Iterable, List, Sequence


def parse_input(raw_text: str) -> List[List[int]]:
	"""Parse lines into banks of jolts."""
	banks: List[List[int]] = []
	for bank in raw_text.splitlines():
		jolts = list(bank)
		banks.append([int(jolt) for jolt in jolts])
	return banks


def part_one(banks: Iterable[List[int]]) -> int:
	"""Find highest joltage batteries."""
	positions = range(2)
	return common(banks, positions)

def part_two(banks: Iterable[List[int]]) -> int:
	"""Find highest joltage batteries."""
	positions = range(12)
	return common(banks, positions)

def common(banks: Iterable[List[int]], positions: Sequence[int]) -> int:
	"""Find highest joltage batteries."""
	total_joltage = 0
	for bank in banks:
		joltages = [0] * len(positions)
		for i, jolt in enumerate(bank):
			found_higher = False
			for position in positions:
				if found_higher:
					joltages[position] = 0
					continue
				digit = joltages[position]
				bank_position = i + len(positions) - position - 1
				if bank_position < len(bank) and digit < jolt:
					joltages[position] = jolt
					found_higher = True
		joltage = reduce(lambda acc, j: acc * 10 + j, joltages, 0)
		total_joltage += joltage
	return total_joltage


if __name__ == "__main__":  # pragma: no cover
	from pathlib import Path

	input_path = Path(__file__).resolve().parents[2] / "inputs" / "03.sample.txt"
	raw = input_path.read_text(encoding="utf-8")
	parsed = parse_input(raw)
	print("Part 1:", part_one(parsed))
	print("Part 2:", part_two(parsed))
