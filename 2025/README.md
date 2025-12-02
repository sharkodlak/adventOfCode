# Advent of Code 2025 (Python)

This folder holds the Python-based solutions for Advent of Code 2025. The code is meant to be executed inside a Podman container so the PHP tooling from previous years can stay untouched.

## Layout

- `src/aoc2025/` — Python package with the solution code (one module per day, e.g. `day01.py`).
- `tests/` — Pytest suites for regression coverage.
- `inputs/` — Puzzle inputs; keep real input files out of version control when required by AoC rules.
- `requirements.txt` — Runtime/test dependencies installed into the container.
- `pyproject.toml` — Minimal packaging metadata so the project can be installed in editable mode.

## Using Podman

1. Build the image from the repo root:
   ```bash
   podman build -t aoc2025 -f Containerfile .
   ```
2. Start a dev shell with the repository mounted:
   ```bash
   ./scripts/podman-dev.sh
   ```
   Override the command by appending it, e.g. `./scripts/podman-dev.sh pytest 2025/tests`.
3. Inside the container the `aoc2025` package is already installed in editable mode, so Python can resolve it regardless of the cwd.

## Running a day

```bash
python -m aoc2025.runner --day 1 --part both --input 2025/inputs/01.sample.txt
```

If `--input` is omitted the runner looks for `2025/inputs/<day>.txt`.

## Testing

```bash
pytest 2025/tests
```

Feel free to extend `requirements.txt` with any helper libraries you like. Rebuild the container whenever dependencies change.
