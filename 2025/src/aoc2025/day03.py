from __future__ import annotations

from typing import Iterable, List


def parse_input(raw_text: str) -> List[List[int]]:
	"""Parse lines into banks of jolts."""
	banks: List[List[int]] = []
	for bank in raw_text.splitlines():
		jolts = list(bank)
		banks.append([int(jolt) for jolt in jolts])
	return banks


def part_one(banks: Iterable[List[int]]) -> int:
	"""Find highest joltage batteries."""
	joltage = 0
	for bank in banks:
		tens = 0
		ones = 0
		for i, jolt in enumerate(bank):
			if jolt > tens and i < len(bank) - 1:
				tens = jolt
				ones = 0
			elif tens > 0 and jolt > ones:
				ones = jolt
		joltage += tens * 10 + ones
	return joltage

def part_two(banks: Iterable[List[int]]) -> int:
	"""Crawl through all ranges and sum invalid IDs."""
	
	return 0


if __name__ == "__main__":  # pragma: no cover
	from pathlib import Path

	input_path = Path(__file__).resolve().parents[2] / "inputs" / "03.sample.txt"
	raw = input_path.read_text(encoding="utf-8")
	parsed = parse_input(raw)
	print("Part 1:", part_one(parsed))
	print("Part 2:", part_two(parsed))
