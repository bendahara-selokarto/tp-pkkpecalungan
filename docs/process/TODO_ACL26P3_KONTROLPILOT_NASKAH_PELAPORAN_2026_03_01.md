# TODO ACL26P3 Kontrol Pilot Naskah Pelaporan Management Ijin Akses 2026-03-01

Tanggal: 2026-03-01  
Status: `done`  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`  
Parent Concern: `docs/process/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`

## Konteks
- Concern ini melanjutkan pola implementasi pilot seperti `ACL26C1` (`catatan-keluarga`) dan `ACL26P2` (`pilot-project-keluarga-sehat`).
- Target modul pilot berikutnya: `pilot-project-naskah-pelaporan`.
- Guardrail backend tetap: `EnsureScopeRole` + `EnsureModuleVisibility` + policy domain.

## Target Hasil
- [x] Modul `pilot-project-naskah-pelaporan` aktif sebagai modul pilot yang dapat dikontrol super-admin.
- [x] Super-admin dapat `update` dan `rollback` override mode untuk role scoped valid.
- [x] Audit trail override tercatat konsisten (`before_mode`, `after_mode`, `changed_by`, `changed_at`).
- [x] Matrix read-only menampilkan baris modul target sebagai `pilot_manageable`.

## Flow Branch dan Commit
1. Branch concern:
- [x] `feature/acl26p3-pilot-pilot-project-naskah-pelaporan`

2. Commit 1 (backend + enforcement + regression test):
- [x] `feat(access-control): add pilot override backend for pilot-project-naskah-pelaporan`

3. Commit 2 (UI copy + read model assertion + doc sync):
- [x] `feat(access-control): add pilot control UI and tests for pilot-project-naskah-pelaporan`

## Implementasi
- Backend:
  - `config/access_control.php`: menambah `pilot-project-naskah-pelaporan` ke daftar modul pilot.
  - `RoleMenuVisibilityService`: menambah konstanta modul pilot ketiga dan fallback daftar default pilot.
- Testing:
  - `AccessControlManagementWritePilotTest`: skenario update/rollback modul naskah pelaporan.
  - `RoleMenuVisibilityServiceTest`: skenario override/fallback modul naskah pelaporan.
  - `AccessControlManagementReadOnlyTest`: verifikasi baris matrix naskah pelaporan terdeteksi `pilot_manageable`.
- UI:
  - copy halaman matrix diperbarui agar mencerminkan tiga modul pilot aktif.

## Validasi
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`
- [x] `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php`
- [x] `php artisan test`

## Risiko Residual
- Perubahan daftar modul pilot perlu tetap melalui concern bertahap per modul untuk menjaga regression footprint kecil.
- Override pilot tetap berpotensi memblokir akses jika konfigurasi mode tidak tervalidasi stakeholder domain.

## Fallback Plan
- Rollback override per modul menggunakan endpoint rollback generic pilot.
- Nonaktifkan pembacaan override pilot global via `ACCESS_CONTROL_PILOT_OVERRIDE_ENABLED=false` jika terjadi anomali.
