# TODO AUTH26M1: Rollout Centralized Permission Matrix Finalization

Status: `completed`
Priority: `critical`
Owner: Gemini CLI
Start Date: 2026-05-20
End Date: 2026-05-20

## Context
Following the core implementation on May 19, 2026, the project has established the foundation for a centralized permission matrix in `RoleScopeMatrix.php`. This task tracked and completed the migration of all remaining policies and achieved 100% coverage and architectural consistency.

## 1. Matrix Completion (RoleScopeMatrix.php)
- [x] Map all remaining domain permissions to roles in `PERMISSIONS` constant.
    - [x] Domain: Tim Penggerak & Kader (`anggota_tim_penggerak`, `kader_umum`)
    - [x] Domain: Pokja I (`paar`, `simulasi_penyuluhan`, `literasi_warga`, `bkl`, `bkr`)
    - [x] Domain: Pokja II (`koperasi`, `up2k`, `taman_bacaan`, `kejar_paket`, `pelatihan_kader`)
    - [x] Domain: Pokja III (`industri_rumah_tangga`, `pemanfaatan_tanah_pekarangan`)
    - [x] Domain: Pokja IV (`posyandu`, `bkb`, `perencanaan_sehat`, `pilot_project`)
    - [x] Domain: General Administration (`buku_wajib`, `buku_bantu`, `laporan_tahunan`)
- [x] Add missing functional roles to `resolveJobGroup` mapping.
- [x] Ensure consistency between `ROLE_*` constants and actual database role names (hyphen format).

## 2. Policy Migration (app/Policies)
Migrate the following policies to use `RoleScopeMatrix::userHasPermission($user, 'domain.action')`:
- [x] `AnggotaTimPenggerakPolicy`
- [x] `BkbKegiatanPolicy`, `BklPolicy`, `BkrPolicy`
- [x] `BukuDaftarHadirPolicy`, `BukuKeuanganPolicy`, `BukuNotulenRapatPolicy`, `BukuTamuPolicy`
- [x] `CatatanKeluargaPolicy`
- [x] `DataIndustriRumahTanggaPolicy`, `DataKegiatanWargaPolicy`, `DataKeluargaPolicy`, `DataPelatihanKaderPolicy`, `DataPemanfaatanTanahPekaranganHatinyaPkkPolicy`, `DataWargaPolicy`
- [x] `KejarPaketPolicy`, `KoperasiPolicy`, `LaporanTahunanPkkPolicy`, `LiterasiWargaPolicy`, `PaarPolicy`, `PelatihanKaderPokjaIiPolicy`, `PosyanduPolicy`, `PraKoperasiUp2kPolicy`, `SimulasiPenyuluhanPolicy`, `TamanBacaanPolicy`, `TutorKhususPolicy`, `WarungPkkPolicy`
- [x] `UserPolicy` (Refactored to use matrix or unified anchor)

## 3. Hardcoded String Cleanup
- [x] Replace literal `'super-admin'` with `RoleScopeMatrix::ROLE_SUPER_ADMIN` or `userIsSuperAdmin` in:
    - [x] `ActivityRepository.php`
    - [x] `DeleteUserAction.php`
    - [x] `DashboardController.php`
    - [x] `CetakLampiranController.php`
    - [x] `EnsureModuleVisibility.php`
- [x] Audit and replace other literal role strings (e.g., `'desa-sekretaris'`) where applicable.

## 4. Documentation & ADR Sync
- [x] Update `docs/adr/ADR_0012_PERMISSION_MATRIX_AUTHORIZATION.md` to reflect the final decision on hyphen-based role names.
- [x] Update `AGENTS.md` if any architectural boundaries changed during final rollout.
- [x] Mark this TODO as `done` and archive related legacy ACL tasks.

## 5. Validation Gate
- [x] Run `php artisan test` (100% pass for all 109 policy tests).
- [x] Verify no "Permission Denied" regressions for existing functional roles.
- [x] Verify `super-admin` still has absolute bypass across all domains.

## Progress Log
- **2026-05-19**: Core infrastructure and initial 9 policies migrated.
- **2026-05-20**: Comprehensive rollout completed; all policies migrated; matrix expanded; hardcoded strings removed; tests passed.
