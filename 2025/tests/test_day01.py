from aoc2025 import day01

SAMPLE = "1\n2\n3\n4\n"


def test_part_one_sample():
    data = day01.parse_input(SAMPLE)
    assert day01.part_one(data) == 10


def test_part_two_sample():
    data = day01.parse_input(SAMPLE)
    assert day01.part_two(data) == 3
