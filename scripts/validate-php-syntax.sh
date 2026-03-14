#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd -- "$SCRIPT_DIR/.." && pwd)"

cd "$PROJECT_DIR"

mapfile -t files < <(find . \
  -path './vendor' -prune -o \
  -path './node_modules' -prune -o \
  -path './storage' -prune -o \
  -path './bootstrap/cache' -prune -o \
  -type f -name '*.php' ! -name '*.blade.php' -print)

if [ ${#files[@]} -eq 0 ]; then
  echo "PHP syntax check: no files found."
  exit 0
fi

failed=0
for file in "${files[@]}"; do
  if ! php -l "$file" >/dev/null 2>&1; then
    echo "PHP syntax error: $file"
    php -l "$file"
    failed=1
  fi
done

if [ $failed -ne 0 ]; then
  exit 1
fi

echo "PHP syntax check: OK (${#files[@]} files)"
