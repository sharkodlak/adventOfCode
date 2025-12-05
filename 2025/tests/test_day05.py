from pathlib import Path

import pytest

from aoc2025 import day05 as day

SAMPLE = (Path(__file__).resolve().parents[1] / "inputs" / "05.sample.txt").read_text(
	encoding="utf-8"
)


def test_sample():
	data = day.parse_input(SAMPLE)
	assert day.part_one(*data) == 3

@pytest.mark.skip(reason="Not implemented yet")
def test_sample_part_two():
	data = day.parse_input(SAMPLE)
	assert day.part_two(data) == 0
