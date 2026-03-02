# TODO ACL26A2 Rollout Override Modul Activities 2026-03-02

Tanggal: 2026-03-02  
Status: `done`  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`  
Parent Concern: `docs/process/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`

## Konteks
- Tahap 2 pilot `catatan-keluarga` sudah stabil dan fallback aman.
- Tahap 3 membutuhkan rollout batch modul berikutnya secara terkontrol tanpa melemahkan enforcement backend.
- Modul `activities` dipilih sebagai batch pertama Tahap 3 karena coverage route lintas `desa|kecamatan` sudah stabil dan memiliki test matrix middleware yang matang.

## Kontrak Concern (Lock)
- Domain: rollout override akses `module x role-scope` untuk modul `activities`.
- Role/scope target:
  - pengelola: `super-admin`,
  - runtime terdampak: role operasional kompatibel scope `desa|kecamatan`.
- Boundary data:
  - enforcement runtime tetap di `RoleMenuVisibilityService` + `EnsureModuleVisibility`,
  - storage override tetap `module_access_overrides` + audit `module_access_override_audits`.
- Acceptance criteria:
  - super-admin bisa set/rollback mode `activities` via halaman management ijin akses,
  - kombinasi `module x role x scope` tidak valid ditolak di request/action,
  - override non-rollout tidak diterapkan oleh resolver runtime.
- Dampak keputusan arsitektur: `tidak` (tetap dalam ADR 0002, tanpa boundary baru).

## Target Hasil
- [x] Kontrol override matrix tidak lagi hardcoded satu modul.
- [x] Modul `activities` aktif sebagai rollout batch pertama setelah pilot.
- [x] Validasi role-scope-module diterapkan konsisten di request/action/service.

## Langkah Eksekusi
- [x] Analisis scoped dependency + side effect (`controller/request/action/service/usecase/ui/test/docs`).
- [x] Patch minimal:
  - generalisasi endpoint override menjadi `PUT/DELETE /super-admin/access-control/override` dengan payload `module`,
  - generalisasi resolver override ke daftar modul rollout terkelola,
  - aktivasi rollout `activities` via config.
- [x] Sinkronisasi dokumen concern terkait (`ACL26M1`, `ACL26C1`, registry SOT, ADR 0002, validation log).

## Validasi
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php`
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`
- [x] `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
- [x] `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`
- [x] `php artisan test tests/Feature/ModuleVisibilityMiddlewareTest.php`
- [x] `php artisan test`
- [x] `npm run build`

## Risiko
- Override pada modul `activities` berpotensi memblokir akses role kompatibel jika salah kombinasi role-scope-module.
- Rollout batch berikutnya berisiko drift jika modul tidak divalidasi terhadap group role scope.

## Keputusan
- [x] Batch Tahap 3 dimulai dengan satu modul (`activities`) agar blast radius tetap kecil.
- [x] Resolver hanya membaca override dari modul rollout terkelola; override modul lain diabaikan.

## Fallback Plan
- Nonaktifkan override rollout global via `ACCESS_CONTROL_ROLLOUT_OVERRIDE_ENABLED=false` (fallback ke hardcoded penuh).
- Rollback per kombinasi role-scope-module tetap tersedia via endpoint rollback override.

## Output Final
- [x] Ringkasan perubahan dan alasan teknis per layer.
- [x] Daftar file terdampak + bukti validasi test/build.
- [x] Residual risk + jalur lanjutan batch rollout berikutnya.
