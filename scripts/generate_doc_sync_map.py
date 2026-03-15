#!/usr/bin/env python3
from __future__ import annotations

import re
from dataclasses import dataclass
from datetime import date
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
TODO_DIR = ROOT / "docs" / "process"
ADR_DIR = ROOT / "docs" / "adr"
OUTPUT_PATH = ROOT / "docs" / "process" / "DOC_TODO_ADR_SYNC_MAP_2026_03_15.md"

TERMINOLOGY_ANCHORS = [
    "PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md",
    "docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md",
    "docs/domain/DOMAIN_CONTRACT_MATRIX.md",
]


@dataclass(frozen=True)
class TodoEntry:
    code: str
    title: str
    status: str
    related_adrs: list[str]
    path: str


@dataclass(frozen=True)
class AdrEntry:
    number: str
    title: str
    status: str
    related_todos: list[str]
    path: str


def read_text(path: Path) -> str:
    return path.read_text(encoding="utf-8")


def parse_todo(path: Path) -> TodoEntry | None:
    text = read_text(path)
    title_match = re.search(r"^#\s+TODO\s+([A-Z0-9]+)\s+(.*)$", text, re.MULTILINE)
    if not title_match:
        return None
    code, title = title_match.groups()

    status_match = re.search(r"^Status:\s+`([^`]+)`", text, re.MULTILINE)
    status = status_match.group(1) if status_match else "-"

    related_adrs = []
    related_line = re.search(r"^Related ADR:\s+`([^`]+)`", text, re.MULTILINE)
    if related_line:
        related_adrs = [token.strip() for token in related_line.group(1).split(",") if token.strip() and token.strip() != "-"]

    # Find ADR references in body
    for adr_ref in re.findall(r"ADR_\\d{4}_[A-Z0-9_]+\\.md", text):
        related_adrs.append(f"docs/adr/{adr_ref}")

    related_adrs = sorted(set(related_adrs))

    return TodoEntry(
        code=code,
        title=title.strip(),
        status=status.strip(),
        related_adrs=related_adrs,
        path=str(path.relative_to(ROOT)),
    )


def parse_adr(path: Path) -> AdrEntry | None:
    text = read_text(path)
    title_match = re.search(r"^#\s+ADR\s+(\d{4})\s+(.*)$", text, re.MULTILINE)
    if not title_match:
        return None
    number, title = title_match.groups()

    status_match = re.search(r"^Status:\s+`([^`]+)`", text, re.MULTILINE)
    status = status_match.group(1) if status_match else "-"

    related_todos = []
    related_line = re.search(r"^Related TODO:\s+`([^`]+)`", text, re.MULTILINE)
    if related_line:
        related_todos = [token.strip() for token in related_line.group(1).split(",") if token.strip() and token.strip() != "-"]

    for todo_ref in re.findall(r"TODO_[A-Z0-9]+_[A-Z0-9_]+_\\d{4}_\\d{2}_\\d{2}\\.md", text):
        related_todos.append(f"docs/process/{todo_ref}")

    related_todos = sorted(set(related_todos))

    return AdrEntry(
        number=number,
        title=title.strip(),
        status=status.strip(),
        related_todos=related_todos,
        path=str(path.relative_to(ROOT)),
    )


def build_map() -> str:
    todos = []
    for path in TODO_DIR.glob("TODO_*.md"):
        if "archive" in path.parts:
            continue
        entry = parse_todo(path)
        if entry:
            todos.append(entry)
    todos.sort(key=lambda t: t.code)

    adrs = []
    for path in ADR_DIR.glob("ADR_*.md"):
        if path.name == "ADR_TEMPLATE.md":
            continue
        entry = parse_adr(path)
        if entry:
            adrs.append(entry)
    adrs.sort(key=lambda a: a.number)

    status_counts = {}
    for todo in todos:
        status_counts[todo.status] = status_counts.get(todo.status, 0) + 1

    adr_status_counts = {}
    for adr in adrs:
        adr_status_counts[adr.status] = adr_status_counts.get(adr.status, 0) + 1

    lines: list[str] = []
    lines.append(f"# Documentation Sync Map (TODO/ADR) ({date.today().isoformat()})")
    lines.append("")
    lines.append("Tujuan: membantu sinkronisasi status TODO/ADR dan menjaga konsistensi istilah.")
    lines.append("")
    lines.append("- Dokumen ini dihasilkan oleh `scripts/generate_doc_sync_map.py`.")
    lines.append("")
    lines.append("## Ringkasan")
    lines.append("")
    lines.append(f"- Total TODO aktif: {len(todos)}")
    for status, count in sorted(status_counts.items()):
        lines.append(f"- TODO status `{status}`: {count}")
    lines.append(f"- Total ADR aktif: {len(adrs)}")
    for status, count in sorted(adr_status_counts.items()):
        lines.append(f"- ADR status `{status}`: {count}")
    lines.append("")
    lines.append("## TODO -> ADR")
    lines.append("")
    lines.append("| Code | Judul | Status | Related ADR | File |")
    lines.append("| --- | --- | --- | --- | --- |")
    for todo in todos:
        adrs_cell = "<br>".join(todo.related_adrs) if todo.related_adrs else "-"
        lines.append(
            "| `{code}` | {title} | `{status}` | {adrs} | `{path}` |".format(
                code=todo.code,
                title=todo.title,
                status=todo.status,
                adrs=adrs_cell,
                path=todo.path,
            )
        )
    lines.append("")
    lines.append("## ADR -> TODO")
    lines.append("")
    lines.append("| ADR | Judul | Status | Related TODO | File |")
    lines.append("| --- | --- | --- | --- | --- |")
    for adr in adrs:
        todos_cell = "<br>".join(adr.related_todos) if adr.related_todos else "-"
        lines.append(
            "| `{number}` | {title} | `{status}` | {todos} | `{path}` |".format(
                number=adr.number,
                title=adr.title,
                status=adr.status,
                todos=todos_cell,
                path=adr.path,
            )
        )
    lines.append("")
    lines.append("## Terminologi Anchor")
    lines.append("")
    for anchor in TERMINOLOGY_ANCHORS:
        lines.append(f"- `{anchor}`")

    return "\n".join(lines) + "\n"


def main() -> None:
    content = build_map()
    OUTPUT_PATH.write_text(content, encoding="utf-8")
    print(f"Wrote {OUTPUT_PATH}")


if __name__ == "__main__":
    main()
