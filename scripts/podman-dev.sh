#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
REPO_ROOT=$(cd "${SCRIPT_DIR}/.." && pwd)
IMAGE_NAME=${AOC2025_IMAGE:-aoc2025}
CONTAINERFILE=${CONTAINERFILE:-Containerfile}

if ! podman image exists "${IMAGE_NAME}" >/dev/null 2>&1; then
    echo "Building image ${IMAGE_NAME}"
    podman build -t "${IMAGE_NAME}" -f "${REPO_ROOT}/${CONTAINERFILE}" "${REPO_ROOT}"
fi

podman run --rm -it \
    -v "${REPO_ROOT}:/workspace" \
    -w /workspace \
    "${IMAGE_NAME}" \
    "${@:-bash}"
