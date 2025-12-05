from __future__ import annotations

import argparse
import importlib
import inspect
from pathlib import Path
from typing import Any, Iterable, List, Sequence, Tuple

PartResult = Tuple[int, Any]


def _module_name(day: int) -> str:
    return f"aoc2025.day{day:02d}"


def load_day_module(day: int):
    try:
        return importlib.import_module(_module_name(day))
    except ModuleNotFoundError as exc:  # pragma: no cover - turns into SystemExit
        raise SystemExit(f"Day {day:02d} module not found. Expected {_module_name(day)}") from exc


def prepare_data(module: Any, raw_text: str) -> Any:
    if hasattr(module, "prepare_data"):
        return module.prepare_data(raw_text)
    if hasattr(module, "parse_input"):
        return module.parse_input(raw_text)
    return raw_text


def run_part(module: Any, part: int, data: Any) -> Any:
    func_name = "part_one" if part == 1 else "part_two"
    if not hasattr(module, func_name):
        raise SystemExit(f"{module.__name__} is missing {func_name}()")
    part_func = getattr(module, func_name)

    # Allow day modules to declare multiple positional params (e.g., part_one(a, b))
    sig = inspect.signature(part_func)
    positional_params = [p for p in sig.parameters.values() if p.kind in {p.POSITIONAL_ONLY, p.POSITIONAL_OR_KEYWORD}]
    if len(positional_params) > 1 and isinstance(data, (tuple, list)):
        return part_func(*data)
    return part_func(data)


def execute(day: int, part: str, input_path: Path) -> List[PartResult]:
    if part not in {"1", "2", "both"}:
        raise ValueError("part must be '1', '2', or 'both'")

    if not input_path.exists():
        raise SystemExit(f"Input file not found: {input_path}")

    module = load_day_module(day)
    raw_text = input_path.read_text(encoding="utf-8")
    data = prepare_data(module, raw_text)

    requested_parts: Sequence[int] = (1, 2) if part == "both" else (int(part),)
    results: List[PartResult] = []
    for current in requested_parts:
        results.append((current, run_part(module, current, data)))
    return results


def build_argument_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(description="Run Advent of Code 2025 solutions.")
    parser.add_argument("--day", type=int, required=True, help="Day number (1-25).")
    parser.add_argument(
        "--part",
        choices=["1", "2", "both"],
        default="both",
        help="Which part to run (default: both).",
    )
    parser.add_argument(
        "--input",
        type=Path,
        help="Optional input file. Defaults to 2025/inputs/<day>.txt",
    )
    return parser


def main(argv: Sequence[str] | None = None) -> None:
    parser = build_argument_parser()
    args = parser.parse_args(argv)

    default_input = Path("2025") / "inputs" / f"{args.day:02d}.txt"
    input_path = args.input or default_input

    for part_number, value in execute(args.day, args.part, input_path):
        print(f"Day {args.day:02d} part {part_number}: {value}")


if __name__ == "__main__":  # pragma: no cover
    main()
