.PHONY: in podshell build test test-all tests run

PODMAN_SCRIPT := ./scripts/podman-dev.sh
TEST_DIR := 2025/tests
SRC_DIR := 2025/src/aoc2025

TEST_FILES := $(sort $(wildcard $(TEST_DIR)/test_day*.py))
DAY_FILES := $(sort $(wildcard $(SRC_DIR)/day*.py))
LATEST_TEST := $(lastword $(TEST_FILES))
LATEST_DAY_FILE := $(lastword $(DAY_FILES))
LATEST_DAY :=
ifneq ($(strip $(LATEST_DAY_FILE)),)
LATEST_DAY := $(patsubst $(SRC_DIR)/day%.py,%,$(LATEST_DAY_FILE))
endif

TEST_ARG :=
ifeq ($(firstword $(MAKECMDGOALS)),test)
TEST_ARG := $(word 2,$(MAKECMDGOALS))
ifneq ($(strip $(TEST_ARG)),)
.PHONY: $(TEST_ARG)
$(TEST_ARG):
	@:
endif
endif

RUN_ARG :=
ifeq ($(firstword $(MAKECMDGOALS)),run)
RUN_ARG := $(word 2,$(MAKECMDGOALS))
ifneq ($(strip $(RUN_ARG)),)
.PHONY: $(RUN_ARG)
$(RUN_ARG):
	@:
endif
endif

# Enter interactive Podman shell
in: podshell

podshell:
	$(PODMAN_SCRIPT)

build:
	podman build -t $${AOC2025_IMAGE:-aoc2025} -f Containerfile .

test:
	@if [ -z "$(LATEST_TEST)" ]; then \
		echo "No test files found in $(TEST_DIR)." >&2; \
		exit 1; \
	fi
ifeq ($(strip $(TEST_ARG)),)
	$(PODMAN_SCRIPT) pytest $(LATEST_TEST)
else ifeq ($(strip $(TEST_ARG)),all)
	$(PODMAN_SCRIPT) pytest $(TEST_DIR)
else
	$(PODMAN_SCRIPT) pytest $(TEST_DIR)/test_day$(TEST_ARG).py
endif

test-all tests:
	$(PODMAN_SCRIPT) pytest $(TEST_DIR)

run:
	@if [ -z "$(LATEST_DAY)" ]; then \
		echo "No day modules found in $(SRC_DIR)." >&2; \
		exit 1; \
	fi
	DAY=$(strip $(RUN_ARG)); \
	if [ -z "$$DAY" ]; then \
		DAY=$(LATEST_DAY); \
	fi; \
	$(PODMAN_SCRIPT) python -m aoc2025.runner --day $$DAY --part both
