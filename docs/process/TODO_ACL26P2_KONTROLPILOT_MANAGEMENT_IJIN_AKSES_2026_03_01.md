# TODO ACL26P2 Kontrol Pilot Management Ijin Akses Super Admin 2026-03-01

Tanggal: 2026-03-01  
Status: `done`  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`  
Referensi Pola: `docs/process/TODO_ACL26C1_PILOT_OVERRIDE_CATATAN_KELUARGA_2026_02_28.md`

## Konteks
- Dibutuhkan perencanaan implementasi `kontrol pilot` pada menu `Management Ijin Akses` untuk modul berikutnya dengan pola yang sudah stabil di `catatan-keluarga`.
- Eksekusi harus menjaga guardrail backend: `EnsureScopeRole` + `EnsureModuleVisibility` + policy domain.
- Implementasi harus bertahap per concern, dengan jejak commit yang jelas sampai merge ke `main`.

## Kontrak Concern
- Pengelola perubahan: `super-admin`.
- Cakupan runtime: role scoped operasional `desa|kecamatan` (tanpa memodelkan `super-admin` sebagai role scoped).
- Boundary implementasi:
  - `Controller -> UseCase/Action -> Repository -> Model`
  - UI super-admin hanya konsumsi payload dan kirim aksi pilot.
- Mode akses yang didukung: `read-write`, `read-only`, `hidden`.
- Fallback wajib aktif: baseline hardcoded tetap menjadi source aman saat override pilot rollback/nonaktif.

## Target Hasil
- [x] Modul target pilot baru dapat dikontrol dari matrix `Management Ijin Akses`.
- [x] Super-admin dapat `update` dan `rollback` mode pilot modul target.
- [x] Audit trail tercatat (`before_mode`, `after_mode`, `changed_by`, `changed_at`).
- [x] Tidak ada pelemahan otorisasi backend dan tidak ada data leak lintas scope/area.
- [x] Alur branch dan commit concern terdokumentasi serta repeatable.

## Flow Eksekusi (Wajib)
1. Buat branch by concern.
2. Implement concern tahap 1.
3. Commit tahap 1.
4. Implement concern tahap 2.
5. Commit tahap 2.
6. Final validation.
7. Merge ke `main`.

## Rencana Branch dan Commit
1. Branch
- [x] Buat branch: `feature/acl26p2-pilot-pilot-project-keluarga-sehat`.

2. Implementasi Tahap 1 (Backend Pilot Resolver + Validasi)
- [x] Tambah konstanta/module marker pilot baru di `RoleMenuVisibilityService`.
- [x] Tambah action/use case untuk `upsert` dan `rollback` pilot modul target (meniru pola `catatan-keluarga`).
- [x] Pastikan validasi request membatasi kombinasi `scope x role` hanya role compatible.
- [x] Pastikan resolver prioritas: override pilot -> fallback hardcoded.
- [x] Commit 1:
  - `feat(access-control): add pilot override backend for pilot-project-keluarga-sehat`

3. Implementasi Tahap 2 (UI Super Admin + Test Coverage)
- [x] Tambah kontrol pilot pada baris modul target di halaman `SuperAdmin/AccessControl/Index`.
- [x] Hardening copywriting user-facing agar natural dan tidak teknis internal.
- [x] Tambah/ubah feature test:
  - super-admin berhasil update + rollback pilot modul target,
  - non super-admin ditolak,
  - invalid scope-role ditolak validasi.
- [x] Tambah/ubah unit test resolver untuk prioritas override dan fallback modul target.
- [x] Commit 2:
  - `feat(access-control): add pilot control UI and tests for pilot-project-keluarga-sehat`

4. Finalisasi dan Merge
- [x] Jalankan validasi penuh `php artisan test`.
- [x] Pastikan TODO/ADR terkait sinkron status implementasi.
- [ ] Merge branch concern ke `main` setelah seluruh gate hijau.

## File Scope Implementasi (Estimasi)
- Route/Controller:
  - `routes/web.php`
  - `app/Http/Controllers/SuperAdmin/AccessControlManagementController.php`
- Request:
  - `app/Http/Requests/SuperAdmin/*Pilot*OverrideRequest.php`
- UseCase/Action/Repository/Model:
  - `app/UseCases/SuperAdmin/ListAccessControlMatrixUseCase.php`
  - `app/Domains/Wilayah/AccessControl/Actions/*`
  - `app/Domains/Wilayah/AccessControl/Repositories/*`
  - `app/Domains/Wilayah/AccessControl/Models/*`
- Service Enforcement:
  - `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- UI:
  - `resources/js/Pages/SuperAdmin/AccessControl/Index.vue`
- Test:
  - `tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`
  - `tests/Unit/Services/RoleMenuVisibilityServiceTest.php`

## Validasi Minimum
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php`
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`
- [x] `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
- [x] `php artisan test`

## Risiko
- Salah validasi kombinasi `scope-role` dapat membuka eskalasi privilege.
- Override yang tidak sinkron dengan fallback hardcoded dapat memicu drift mode akses.
- Rollout multi-modul tanpa batching concern berisiko regresi global.

## Keputusan
- [x] K1: Pilot tetap per modul (bukan big-bang).
- [x] K2: Otorisasi backend tetap authority akhir.
- [x] K3: Setiap concern pilot wajib selesai dengan 2 commit fungsional + 1 validasi final.

## Fallback Plan
- [x] Rollback override modul target via endpoint rollback.
- [x] Nonaktifkan pembacaan override pilot global via konfigurasi jika anomali.
- [x] Kembali ke baseline hardcoded tanpa mengubah kontrak route/policy.

## Output Final Concern
- [x] Ringkasan perubahan per layer (route/request/use case/repository/model/ui/test/docs).
- [x] Bukti hasil test.
- [x] Catatan residual risk + rekomendasi rollout modul berikutnya.

## Implementasi Aktual
- Modul target pilot: `pilot-project-keluarga-sehat`.
- Backend:
  - resolver pilot kini membaca daftar modul pilot dari konfigurasi `access_control.pilot_override.modules`,
  - action generic pilot (`upsert`/`rollback`) aktif untuk multi-modul pilot,
  - request validasi endpoint generic memastikan `module_slug` valid + `scope-role` kompatibel.
- UI:
  - tombol kontrol pilot pada matrix kini menembak endpoint generic berbasis slug modul baris.
- Test:
  - feature test super-admin update/rollback untuk `pilot-project-keluarga-sehat` ditambahkan,
  - test validasi modul non-pilot ditolak ditambahkan,
  - unit test resolver override/fallback untuk modul target ditambahkan.
- Branch concern: `feature/acl26p2-pilot-pilot-project-keluarga-sehat`.
