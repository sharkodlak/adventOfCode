from __future__ import annotations

import re
from typing import Iterable, List, Dict


def parse_input(raw_text: str) -> List[Dict[str, int]]:
	"""Parse comma-separated range instructions into list of ranges."""
	ranges: List[Dict[str, int]] = []
	for range_text in re.split(r"[\n,]+", raw_text.strip()):
		range_text = range_text.strip()
		if not range_text:
			continue
		range_list = range_text.split('-')
		if len(range_list) != 2:
			raise ValueError(f"Invalid range instruction: {range_text}")
		ids_range = {"start": int(range_list[0]), "end": int(range_list[1])}
		ranges.append(ids_range)
	return ranges


def part_one(data: Iterable[Dict[str, int]]) -> int:
	"""Crawl through all ranges and sum invalid IDs."""
	sum = 0
	for ids_range in data:
		for id in range(ids_range["start"], ids_range["end"] + 1):
			id_str = str(id)
			if len(id_str) % 2 != 0:
				continue
			pos = len(id_str) // 2
			first_half = id_str[:pos]
			second_half = id_str[pos:]
			if first_half == second_half:
				sum += id
	return sum

def part_two(data: Iterable[Dict[str, int]]) -> int:
	"""Crawl through all ranges and sum invalid IDs."""
	sum = 0
	for ids_range in data:
		for id in range(ids_range["start"], ids_range["end"] + 1):
			id_str = str(id)
			id_len = len(id_str)
			for i in range(1, id_len // 2 + 1):
				if id_len % i != 0:
					continue
				chunk = id_str[:i]
				if all(id_str[j:j+i] == chunk for j in range(0, id_len, i)):
					sum += id
					break
	return sum


if __name__ == "__main__":  # pragma: no cover
	from pathlib import Path

	input_path = Path(__file__).resolve().parents[2] / "inputs" / "02.sample.txt"
	raw = input_path.read_text(encoding="utf-8")
	parsed = parse_input(raw)
	print("Part 1:", part_one(parsed))
	print("Part 2:", part_two(parsed))
