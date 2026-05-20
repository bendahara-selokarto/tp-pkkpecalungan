# ADR 0012: Centralized Permission Matrix Authorization

Status: `accepted`
Date: 2026-05-19

## Context
The project needs a scalable and auditable way to manage permissions across a 10-level organizational hierarchy (Rakernas X PKK). Previous implementations relied on fragmented ownership checks and hardcoded role strings in policies.

## Decision
We adopt a centralized **Permission Matrix** within `app/Support/RoleScopeMatrix.php`. 

1.  **Unified Anchor**: All Laravel Policies must delegate permission checks to `RoleScopeMatrix::userHasPermission($user, 'domain.action')`.
2.  **Naming Convention**: Roles use hyphens (e.g., `super-admin`, `kecamatan-sekretaris`) for consistency with the database and canonical constants in `RoleScopeMatrix`.
3.  **Boundary**: Implementation is currently active up to the **Kecamatan** level. Roles and permissions for **Kabupaten, Provinsi, and Pusat** are defined for planning purposes but not yet active in seeders or scope-level enums.

## Functional Roles & Scoping
To support Rakernas X functional differentiation (e.g., Pokja I-IV), the matrix includes functional roles that map to specific job groups via `RoleScopeMatrix::resolveJobGroup`. 

1.  **Separation of Concerns**: Policies use the Matrix for permission checks (`can viewAny`, `can create`). Scoping Services use the resolved Job Group to filter database queries.
2.  **Mapping Standard**: Roles like `desa-pokja-i` map to the canonical group `pokja-i` used in the database.
