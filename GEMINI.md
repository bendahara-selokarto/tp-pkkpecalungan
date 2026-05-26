# GEMINI CLI Foundational Mandate - TP PKK Pecalungan

This document serves as the foundational mandate for Gemini CLI within this repository. It defines the core engineering standards, architectural patterns, and operational workflows that MUST be strictly adhered to.

## 0. Hierarchy of Mandates

If there is a conflict between documents, follow this priority:
1. `AGENTS.md`: Primary technical, architectural, and quality gate "Source of Truth".
2. `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`: Mandatory operational routing and execution contract.
3. `PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md`: Canonical domain terminology.
4. `GEMINI.md`: (This document) Foundational instructions for Gemini CLI.
5. `README.md`: General project guide.

## 1. Engineering Standards & Architecture

### Architectural Layering
All domain logic MUST follow this strict layering:
`Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`
- Controllers MUST be thin (orchestration only).
- Business flows MUST reside in `UseCase` or `Action` classes.
- All domain queries MUST go through a `Repository`.

### Authorization & Scoping
- **Source of Truth**: `Policy -> Scope Service` using `RoleScopeMatrix`.
- **Frontend vs Backend**: The frontend is NEVER the authority for access; enforcement MUST be at the backend level (Policy/Middleware).
- **Data Scoping**: Every domain table MUST include columns: `level` (desa/kecamatan), `area_id` (FK to `areas`), and `created_by`.
- **Level Consistency**: Data `level` MUST be consistent with `areas.level` of the referenced `area_id`.

### Source of Truth for Regions
- The `areas` table is the single source of truth for all regional data.
- Do NOT add new dependencies to legacy tables (`kecamatans`, `desas`, `user_assignments`).

## 2. Operational Workflows for AI

### Documentation Governance
- **Multi-file Tasks**: MUST create a dedicated `docs/process/TODO_<KODE_UNIK>_...` file.
- **Architectural Decisions**: MUST create or update an `ADR` in `docs/adr/`.
- **Terminology**: Use `PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md` for all domain-specific labels and terms.

### Execution Path
Follow the **AI Single Path Architecture**:
1. **Classify**: Map task to a canonical concern.
2. **Self-Reflective Checkpoint**: Evaluate assumptions before patching.
3. **Contract Lock**: Define target files and acceptance criteria.
4. **Minimal Patch**: Apply surgical changes within defined layers.
5. **Validation**: Run the mandatory test matrix.

## 3. Language & Style Conventions

- **Business Domain**: Bahasa Indonesia (e.g., `anggota`, `kegiatan`).
- **Technical Terms**: English (e.g., `Repository`, `Interface`, `Request`).
- **Test Methods**: Bahasa Indonesia (e.g., `test_menampilkan_halaman_index_desa`).
- **Commit Messages**: `type(scope): intent` (e.g., `feat(activities): add repository for desa scope`).

## 4. Definition of Done (DoD)

A task is considered complete ONLY if:
1. It follows the architectural layering and authorization patterns defined here.
2. It does not introduce bypasses to the Repository boundary.
3. It passes relevant tests (`php artisan test`). Use `--compact` for efficient output.
4. Documentation (TODO, ADR, or GEMINI.md) is updated to reflect changes in contracts or architecture.
5. Code follows existing project style and naming conventions.
