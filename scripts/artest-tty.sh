#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd -- "$SCRIPT_DIR/.." && pwd)"

cd "$PROJECT_DIR"

export TERM="${TERM:-xterm-256color}"
export COLUMNS="${COLUMNS:-180}"
export LINES="${LINES:-50}"
export FORCE_COLOR=1

php artisan config:clear --ansi >/dev/null

cmd=(php -d memory_limit=512M artisan test "$@")

if ! command -v script >/dev/null 2>&1; then
    exec "${cmd[@]}"
fi

printf -v quoted '%q ' "${cmd[@]}"

if script -qefc "$quoted" /dev/null; then
    exit 0
fi

exec "${cmd[@]}"
