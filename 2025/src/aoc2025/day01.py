from __future__ import annotations

from typing import Iterable, List


def parse_input(raw_text: str) -> List[int]:
    """Parse newline-separated integers, skipping blank lines."""
    lines = (line.strip() for line in raw_text.splitlines())
    return [int(value) for value in lines if value]


def part_one(data: Iterable[int]) -> int:
    """Return the sum as a simple sanity-check puzzle."""
    return sum(data)


def part_two(data: Iterable[int]) -> int:
    values = list(data)
    if not values:
        return 0
    return max(values) - min(values)


if __name__ == "__main__":  # pragma: no cover
    from pathlib import Path

    input_path = Path(__file__).resolve().parents[2] / "inputs" / "01.sample.txt"
    raw = input_path.read_text(encoding="utf-8")
    parsed = parse_input(raw)
    print("Part 1:", part_one(parsed))
    print("Part 2:", part_two(parsed))
