from pathlib import Path

import pytest

from aoc2025 import day01

SAMPLE = (Path(__file__).resolve().parents[1] / "inputs" / "01.sample.txt").read_text(
    encoding="utf-8"
)


def test_sample_expected_distance():
    data = day01.parse_input(SAMPLE)
    assert day01.part_one(data) == 3

def test_sample_clicks_on_zero_once():
	data = day01.parse_input("L68\n")
	assert day01.part_two(data) == 1

def test_sample_clicks_on_zero_twice():
	data = day01.parse_input("L68\nL30\nR48\n")
	assert day01.part_two(data) == 2

def test_sample_clicks_on_zero_thrice():
	data = day01.parse_input("L68\nL30\nR48\nL5\nR60\n")
	assert day01.part_two(data) == 3

def test_sample_clicks_on_zero_four_times():
	data = day01.parse_input("L68\nL30\nR48\nL5\nR60\nL55\n")
	assert day01.part_two(data) == 4

def test_sample_clicks_on_zero_five_times():
	data = day01.parse_input("L68\nL30\nR48\nL5\nR60\nL55\nL1\nL99\n")
	assert day01.part_two(data) == 5

def test_sample_clicks_on_zero():
	data = day01.parse_input(SAMPLE)
	assert day01.part_two(data) == 6
