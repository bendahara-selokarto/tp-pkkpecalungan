#!/usr/bin/env python3
from __future__ import annotations

import re
from datetime import date
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
ROUTES_PATH = ROOT / "routes" / "web.php"
OUTPUT_PATH = ROOT / "docs" / "pdf" / "PDF_ROUTE_CONTROLLER_VIEW_MAP_2026_03_15.md"


def read_text(path: Path) -> str:
    return path.read_text(encoding="utf-8")


def find_get_routes(text: str) -> list[str]:
    return re.findall(r"Route::get\([\s\S]*?;", text)


def extract_route(block: str) -> dict[str, str]:
    path_match = re.search(r"Route::get\('([^']+)'", block)
    if not path_match:
        return {}
    path = path_match.group(1)

    handler_match = re.search(r"\[\s*([^\]]+)\]", block)
    controller = ""
    method = ""
    if handler_match:
        parts = [p.strip() for p in handler_match.group(1).split(",")]
        if len(parts) >= 2:
            controller = parts[0].replace("::class", "").strip()
            method = parts[1].strip().strip("'")

    name_match = re.search(r"->name\('([^']+)'\)", block)
    name = name_match.group(1) if name_match else ""

    return {
        "path": path,
        "name": name,
        "controller": controller,
        "method": method,
    }


def list_print_controllers(root: Path) -> dict[str, list[str]]:
    controller_views: dict[str, list[str]] = {}
    for path in root.rglob("*PrintController.php"):
        txt = read_text(path)
        views = re.findall(r"loadView\(\s*['\"]([^'\"]+)['\"]", txt)
        views += re.findall(r"view\(\s*['\"](pdf\.[^'\"]+)['\"]", txt)
        controller_views[path.stem] = sorted(set(views))
    return controller_views


def build_map() -> str:
    routes_text = read_text(ROUTES_PATH)
    blocks = find_get_routes(routes_text)

    all_paths = []
    routes = []
    for block in blocks:
        route = extract_route(block)
        if not route:
            continue
        all_paths.append(route["path"])

        if "report/pdf" not in block and "PrintController" not in block and "/print" not in block:
            continue
        routes.append(route)

    report_pdf = [p for p in all_paths if p.endswith("report/pdf")]
    print_pdf = [p for p in all_paths if p.endswith("/print")]
    print_docx = [p for p in all_paths if p.endswith("/print/docx")]

    controller_views = list_print_controllers(ROOT / "app")
    controller_routes: dict[str, list[dict[str, str]]] = {}
    for route in routes:
        controller = route["controller"].split("\\")[-1] if route["controller"] else ""
        controller_routes.setdefault(controller, []).append(route)

    lines: list[str] = []
    lines.append(f"# PDF Route-Controller-View Map ({date.today().isoformat()})")
    lines.append("")
    lines.append(
        "Tujuan: mempermudah audit jalur PDF dengan peta cepat `route -> controller -> view`."
    )
    lines.append("")
    lines.append("Ringkasan cakupan:")
    lines.append("")
    lines.append(f"- Total route `GET` di `routes/web.php`: {len(all_paths)}")
    lines.append(f"- Total route `report/pdf`: {len(report_pdf)}")
    lines.append(f"- Total route `*/print`: {len(print_pdf)}")
    lines.append(f"- Total route `*/print/docx`: {len(print_docx)}")
    lines.append("")
    lines.append("Catatan:")
    lines.append("")
    lines.append("- Map ini diambil dari `routes/web.php` + seluruh `*PrintController.php`.")
    lines.append("- `DashboardController` punya jalur PDF chart tetapi bukan `*PrintController`.")
    lines.append("- `LaporanTahunanPkkPrintController` menghasilkan `docx`, bukan PDF view.")
    lines.append("- Dokumen ini dihasilkan oleh `scripts/generate_pdf_route_map.py`.")
    lines.append("")
    lines.append("## Route -> Controller -> View")
    lines.append("")
    lines.append("| Controller | Routes | Views |")
    lines.append("| --- | --- | --- |")

    for controller in sorted(controller_routes.keys()):
        rlist = sorted(controller_routes[controller], key=lambda x: x["path"])
        route_entries = []
        for route in rlist:
            name = f" ({route['name']})" if route["name"] else ""
            route_entries.append(
                f"GET {route['path']}{name} => {route['method']}"
            )
        routes_cell = "<br>".join(route_entries) if route_entries else "-"
        views = controller_views.get(controller, [])
        views_cell = "<br>".join(views) if views else "-"
        lines.append(f"| `{controller}` | {routes_cell} | {views_cell} |")

    return "\n".join(lines) + "\n"


def main() -> None:
    content = build_map()
    OUTPUT_PATH.write_text(content, encoding="utf-8")
    print(f"Wrote {OUTPUT_PATH}")


if __name__ == "__main__":
    main()
