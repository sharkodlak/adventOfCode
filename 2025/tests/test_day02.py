from pathlib import Path

import pytest

from aoc2025 import day02

SAMPLE = (Path(__file__).resolve().parents[1] / "inputs" / "02.sample.txt").read_text(
	encoding="utf-8"
)


def test_sample_expected_distance():
	data = day02.parse_input(SAMPLE)
	assert day02.part_one(data) == 1227775554

#@pytest.mark.skip(reason="Not implemented yet")
def test_sample_clicks_on_zero():
	data = day02.parse_input(SAMPLE)
	assert day02.part_two(data) == 4174379265
