from pathlib import Path

import pytest

from aoc2025 import day03

SAMPLE = (Path(__file__).resolve().parents[1] / "inputs" / "03.sample.txt").read_text(
	encoding="utf-8"
)


def test_sample():
	data = day03.parse_input(SAMPLE)
	assert day03.part_one(data) == 357

@pytest.mark.skip(reason="Not implemented yet")
def test_sample_part_two():
	data = day03.parse_input(SAMPLE)
	assert day03.part_two(data) == 0
