# ADR 0012: Centralized Permission Matrix Authorization

Status: `accepted`
Date: 2026-05-19

## Context
The project needs a scalable and auditable way to manage permissions across a 10-level organizational hierarchy (Rakernas X PKK). Previous implementations relied on fragmented ownership checks and hardcoded role strings in policies.

## Decision
We adopt a centralized **Permission Matrix** within `app/Support/RoleScopeMatrix.php`. 

1.  **Unified Anchor**: All Laravel Policies must delegate permission checks to `RoleScopeMatrix::hasPermission($user->role, 'domain.action')`.
2.  **Naming Convention**: Roles use underscores (e.g., `super_admin`, `admin_kecamatan`) for consistency with the matrix constants.
3.  **Boundary**: Implementation is currently active up to the **Kecamatan** level. Roles and permissions for **Kabupaten, Provinsi, and Pusat** are defined for planning purposes but not yet active in seeders or scope-level enums.

## Consequences
- **Positive**: Single source of truth for all domain permissions, easier to audit against Rakernas X documents.
- **Negative**: Requires careful synchronization of role names in the database during migration.
- **Maintenance**: Adding new modules requires updating the matrix in `RoleScopeMatrix`.
