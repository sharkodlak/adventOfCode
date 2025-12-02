from pathlib import Path

from aoc2025 import runner


def test_execute_runs_both_parts(tmp_path: Path):
    input_file = tmp_path / "input.txt"
    input_file.write_text("1\n2\n3\n4\n", encoding="utf-8")

    results = runner.execute(day=1, part="both", input_path=input_file)

    assert results == [(1, 10), (2, 3)]
