#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd -- "$SCRIPT_DIR/.." && pwd)"

cd "$PROJECT_DIR"

if [ "$#" -eq 0 ]; then
    mapfile -t files < <(find tests -type f -name '*Test.php' | sort)
else
    files=("$@")
fi

for file in "${files[@]}"; do
    printf '\n[%s] %s\n' "$(date '+%H:%M:%S')" "$file"

    if ! bash "$SCRIPT_DIR/artest-tty.sh" "$file" --stop-on-failure; then
        printf '\nFAILED FILE: %s\n' "$file" >&2
        exit 1
    fi
done
