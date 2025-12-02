from __future__ import annotations

from typing import Iterable, List


def parse_input(raw_text: str) -> List[int]:
	"""Parse newline-separated turning instructions into signed steps."""
	steps: List[int] = []
	for value in raw_text.splitlines():
		instruction = value.strip()
		if not instruction:
			continue
		distance = int(instruction[1:])
		turn = instruction[0].upper()
		if turn not in {"L", "R"}:
			raise ValueError(f"Invalid turn instruction: {turn}")
		if turn == "L":
			distance = -distance
		steps.append(distance)
	return steps


def part_one(data: Iterable[int]) -> int:
	"""Count how many times dial ends on 0."""
	position = 50
	count = 0
	for step in data:
		position = (position + step) % 100
		if position == 0:
			count += 1
	return count

def part_two(data: Iterable[int]) -> int:
	"""Count how many times dial clicks on 0."""
	position = 50
	count = 0
	for step in data:
		last_position = position
		position += step
		count += abs(position) // 100
		if last_position != 0 and position <= 0:
			count += 1
		position %= 100
	return count


if __name__ == "__main__":  # pragma: no cover
	from pathlib import Path

	input_path = Path(__file__).resolve().parents[2] / "inputs" / "01.sample.txt"
	raw = input_path.read_text(encoding="utf-8")
	parsed = parse_input(raw)
	print("Part 1:", part_one(parsed))
	print("Part 2:", part_two(parsed))
