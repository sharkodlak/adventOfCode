from pathlib import Path

import pytest

from aoc2025 import day01

SAMPLE = (Path(__file__).resolve().parents[1] / "inputs" / "01.sample.txt").read_text(
    encoding="utf-8"
)


def test_real_sample_expected_distance():
    data = day01.parse_input(SAMPLE)
    assert day01.part_one(data) == 3
