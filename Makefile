.PHONY: in podshell build test test-all

PODMAN_SCRIPT := ./scripts/podman-dev.sh


TEST_DIR := 2025/tests
TEST_FILES := $(sort $(wildcard $(TEST_DIR)/test_day*.py))
LATEST_TEST := $(lastword $(TEST_FILES))

# Enter interactive Podman shell
in: podshell

podshell:
	$(PODMAN_SCRIPT)

build:
	podman build -t $${AOC2025_IMAGE:-aoc2025} -f Containerfile .

test:
	@if [ -z "$(LATEST_TEST)" ]; then \
		echo "No test files found." >&2; \
		exit 1; \
	fi
	$(PODMAN_SCRIPT) pytest $(LATEST_TEST)

test-all:
	$(PODMAN_SCRIPT) pytest $(TEST_DIR)