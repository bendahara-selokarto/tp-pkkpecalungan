#!/usr/bin/env python3
from __future__ import annotations

import re
from dataclasses import dataclass
from datetime import date
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
ROUTES_PATH = ROOT / "routes" / "web.php"
POLICY_DIR = ROOT / "app" / "Policies"
OUTPUT_PATH = ROOT / "docs" / "security" / "AUTH_SCOPE_ROUTE_POLICY_MAP_2026_03_15.md"

ROLE_SCOPE_MATRIX = "app/Support/RoleScopeMatrix.php"
SCOPE_LEVEL_ENUM = "app/Domains/Wilayah/Enums/ScopeLevel.php"


@dataclass(frozen=True)
class RouteEntry:
    scope: str
    slug: str
    controller: str
    kind: str


@dataclass(frozen=True)
class PolicyEntry:
    policy: str
    scope_service: str
    slug: str


def read_text(path: Path) -> str:
    return path.read_text(encoding="utf-8")


def camel_to_kebab(value: str) -> str:
    step1 = re.sub(r"(.)([A-Z][a-z]+)", r"\1-\2", value)
    step2 = re.sub(r"([a-z0-9])([A-Z])", r"\1-\2", step1)
    return step2.replace("_", "-").lower()


def extract_block(text: str, start_token: str, end_token: str) -> str:
    start = text.find(start_token)
    if start == -1:
        return ""
    end = text.find(end_token, start + len(start_token))
    if end == -1:
        return text[start:]
    return text[start:end]


def extract_routes(block: str, scope: str) -> list[RouteEntry]:
    entries: list[RouteEntry] = []

    for match in re.finditer(r"Route::resource\('([^']+)',\s*([A-Za-z0-9_\\\\]+)::class", block):
        slug, controller = match.groups()
        entries.append(RouteEntry(scope=scope, slug=slug, controller=controller.split("\\")[-1], kind="resource"))

    for match in re.finditer(r"Route::get\('([^']+)'\s*,\s*\[([^\]]+)\]\)", block):
        path, handler = match.groups()
        if any(token in path for token in ["/report/pdf", "/print", "/attachments", "/attachment"]):
            continue
        controller = handler.split(",")[0].replace("::class", "").strip().split("\\")[-1]
        slug = path.strip("/").split("/")[0]
        entries.append(RouteEntry(scope=scope, slug=slug, controller=controller, kind="get"))

    return entries


def load_policies() -> dict[str, PolicyEntry]:
    policies: dict[str, PolicyEntry] = {}
    for policy_file in POLICY_DIR.glob("*Policy.php"):
        text = read_text(policy_file)
        class_match = re.search(r"class\s+([A-Za-z0-9_]+)Policy", text)
        if not class_match:
            continue
        class_name = class_match.group(1)
        policy_name = f"{class_name}Policy"
        slug = camel_to_kebab(class_name)

        scope_service = "-"
        use_match = re.search(
            r"use\s+App\\Domains\\Wilayah\\[A-Za-z0-9_\\\\]+\\([A-Za-z0-9_]+ScopeService);",
            text,
        )
        if use_match:
            scope_service = use_match.group(1)
        else:
            prop_match = re.search(r"readonly\s+(\\w+ScopeService)\s+\\$", text)
            if prop_match:
                scope_service = prop_match.group(1)

        policies[slug] = PolicyEntry(policy=policy_name, scope_service=scope_service, slug=slug)

    return policies


def build_map() -> str:
    text = read_text(ROUTES_PATH)
    desa_block = extract_block(text, "Route::prefix('desa')", "Route::prefix('kecamatan')")
    kecamatan_block = extract_block(text, "Route::prefix('kecamatan')", "require __DIR__")

    routes = extract_routes(desa_block, "desa") + extract_routes(kecamatan_block, "kecamatan")
    routes = [r for r in routes if r.slug]
    routes = list({(r.scope, r.slug, r.controller, r.kind): r for r in routes}.values())

    policies = load_policies()
    mapped = []
    unmapped_routes = []

    explicit_overrides = {
        "desa-activities": "activity",
        "desa-arsip": "arsip-document",
    }

    def singularize(value: str) -> str:
        if value.endswith("ies") and len(value) > 3:
            return value[:-3] + "y"
        if value.endswith("s") and len(value) > 1:
            return value[:-1]
        return value

    def policy_candidates(route_slug: str) -> list[str]:
        base = route_slug.split("/")[0]
        candidates = [base]
        if base in explicit_overrides:
            candidates.append(explicit_overrides[base])
        if base.startswith("desa-"):
            stripped = base.replace("desa-", "", 1)
            candidates.append(stripped)
            candidates.append(singularize(stripped))
        candidates.append(singularize(base))
        return list(dict.fromkeys(candidates))

    for route in routes:
        policy = None
        for candidate in policy_candidates(route.slug):
            policy = policies.get(candidate)
            if policy:
                break
        if not policy:
            unmapped_routes.append(route)
            continue
        mapped.append((route, policy))

    mapped.sort(key=lambda x: (x[0].slug, x[0].scope))
    unmapped_routes.sort(key=lambda r: (r.slug, r.scope))

    mapped_policy_slugs = {policy.slug for _, policy in mapped}
    unmapped_policies = [p for slug, p in sorted(policies.items()) if slug not in mapped_policy_slugs]

    lines: list[str] = []
    lines.append(f"# Auth Scope Route-Policy Map ({date.today().isoformat()})")
    lines.append("")
    lines.append("Tujuan: peta cepat `route -> policy -> scope service -> matrix` untuk audit auth/scope.")
    lines.append("")
    lines.append("Sumber matrix:")
    lines.append("")
    lines.append(f"- `{ROLE_SCOPE_MATRIX}`")
    lines.append(f"- `{SCOPE_LEVEL_ENUM}`")
    lines.append("")
    lines.append("Ringkasan cakupan:")
    lines.append("")
    lines.append(f"- Total route scoped (desa+kecamatan, non-print): {len(routes)}")
    lines.append(f"- Route dengan policy terpetakan: {len(mapped)}")
    lines.append(f"- Route tanpa policy match: {len(unmapped_routes)}")
    lines.append(f"- Policy tanpa route match: {len(unmapped_policies)}")
    lines.append("")
    lines.append("Catatan:")
    lines.append("")
    lines.append("- Map ini berbasis konvensi nama (`slug` route <-> `Policy` class).")
    lines.append("- Pastikan controller tetap memanggil policy (manual authorize atau `authorizeResource`).")
    lines.append("- Route print/report disaring; audit print tetap ada di jalur PDF map.")
    lines.append("- Dokumen ini dihasilkan oleh `scripts/generate_auth_scope_route_map.py`.")
    lines.append("")
    lines.append("Override mapping:")
    lines.append("")
    lines.append("- `desa-activities` -> `ActivityPolicy`")
    lines.append("- `desa-arsip` -> `ArsipDocumentPolicy`")
    lines.append("")
    lines.append("## Route -> Policy -> Scope Service -> Matrix")
    lines.append("")
    lines.append("| Scope | Route slug | Controller | Policy | Scope service | Matrix |")
    lines.append("| --- | --- | --- | --- | --- | --- |")

    for route, policy in mapped:
        lines.append(
            "| `{scope}` | `{slug}` | `{controller}` | `{policy}` | `{service}` | `{matrix}` |".format(
                scope=route.scope,
                slug=route.slug,
                controller=route.controller,
                policy=policy.policy,
                service=policy.scope_service,
                matrix="RoleScopeMatrix",
            )
        )

    lines.append("")
    lines.append("## Route Tanpa Policy Match")
    lines.append("")
    if unmapped_routes:
        lines.append("| Scope | Route slug | Controller |")
        lines.append("| --- | --- | --- |")
        for route in unmapped_routes:
            lines.append(
                "| `{scope}` | `{slug}` | `{controller}` |".format(
                    scope=route.scope, slug=route.slug, controller=route.controller
                )
            )
    else:
        lines.append("- Tidak ada.")

    lines.append("")
    lines.append("## Policy Tanpa Route Match")
    lines.append("")
    if unmapped_policies:
        lines.append("| Policy | Scope service |")
        lines.append("| --- | --- |")
        for policy in unmapped_policies:
            lines.append("| `{policy}` | `{service}` |".format(policy=policy.policy, service=policy.scope_service))
    else:
        lines.append("- Tidak ada.")

    return "\n".join(lines) + "\n"


def main() -> None:
    content = build_map()
    OUTPUT_PATH.write_text(content, encoding="utf-8")
    print(f"Wrote {OUTPUT_PATH}")


if __name__ == "__main__":
    main()
