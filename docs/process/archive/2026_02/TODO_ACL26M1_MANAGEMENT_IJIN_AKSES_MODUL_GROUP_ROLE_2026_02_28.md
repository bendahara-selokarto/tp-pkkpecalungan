# TODO ACL26M1 Management Ijin Akses Berbasis Modul dan Group Role 2026-02-28

Tanggal: 2026-02-28  
Status: `done` (`state:phased-rollout-closed`)  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`

## Konteks
- Kontrol akses modul berbasis role/group saat ini masih hardcoded pada `RoleMenuVisibilityService`.
- Perubahan akses (contoh: concern `catatan-keluarga`) masih membutuhkan patch kode + deploy.
- Diperlukan menu baru pada `super-admin` untuk mengelola ijin akses berbasis `modul x group role` secara bertahap dengan guardrail backend tetap ketat.

## Hasil Observasi (2026-02-28)
- Source of truth akses runtime saat ini:
  - `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
  - `app/Http/Middleware/EnsureModuleVisibility.php`
  - `app/Http/Middleware/EnsureScopeRole.php`
  - `app/Support/RoleScopeMatrix.php`
- Modul `catatan-keluarga` secara runtime berada pada grup `pokja-iv`, dengan override khusus `kecamatan-pokja-iv` = hidden (`null`) untuk modul tersebut.
- Endpoint `Cetak 4.20b` ada di jalur `catatan-keluarga` dan ikut enforcement `scope.role` + `module.visibility` + policy.
- UI sidebar `catatan-keluarga` saat ini diset `uiVisibility: disabled`; akses banyak terjadi via URL langsung.

## Kontrak Concern (Lock)
- Domain: management ijin akses modul-group role oleh `super-admin`.
- Role/scope target:
  - pengelola: `super-admin`.
  - dampak runtime: semua role operasional `desa|kecamatan`.
- Boundary data:
  - backend: `Controller -> UseCase/Action -> Repository -> Model`.
  - enforcement: `RoleMenuVisibilityService`, `EnsureModuleVisibility`, `Policy` modul existing.
  - UI: halaman baru `SuperAdmin/AccessControl/*`.
- Acceptance criteria:
  - super-admin dapat melihat matrix akses `group role x modul`.
  - super-admin dapat mengubah mode akses (`read-only`/`read-write`/`hidden`) pada kombinasi valid.
  - mapping akses modul `catatan-keluarga` bisa direpresentasikan dan dikelola via UI tanpa patch hardcoded.
  - guardrail `scope-role-area` tidak melemah.

## Strategi Bertahap (Concern by Modul)
- Tahap 1 (read-only): tabel informatif matrix akses untuk validasi desain keputusan.
- Tahap 2 (pilot write): aktivasi write override hanya untuk modul `catatan-keluarga`.
- Tahap 3 (rollout): aktivasi write override modul lain secara batch concern.
- Tahap 4 (stabilisasi): audit, hardening, dan keputusan apakah matrix hardcoded tetap permanen sebagai fallback.

## Breakdown Concern Turunan
- Tahap 1 read-only matrix:
  - `docs/process/archive/2026_02/TODO_ACL26S1_SUPER_ADMIN_MATRIX_READ_ONLY_2026_02_28.md`
- Tahap 2 pilot modul `catatan-keluarga`:
  - `docs/process/archive/2026_02/TODO_ACL26C1_PILOT_OVERRIDE_CATATAN_KELUARGA_2026_02_28.md`
- Tahap 3 rollout batch modul `activities`:
  - `docs/process/archive/2026_03/TODO_ACL26A2_ROLLOUT_OVERRIDE_MODUL_ACTIVITIES_2026_03_02.md`
- Tahap 4 closure end-to-end parent concern:
  - `docs/process/archive/2026_03/TODO_ACL26E2_PENUTUPAN_GAP_END_TO_END_MANAGEMENT_IJIN_AKSES_2026_03_02.md`

## Target Hasil
- [x] Observasi kontrak akses existing selesai dan tervalidasi scoped.
- [x] Finalisasi markdown baseline concern (master TODO + ADR + registry SOT).
- [x] Menu baru sidebar super-admin: `Management Ijin Akses`.
- [x] Halaman matrix akses read-only untuk kontrol keputusan desain.
- [x] Fitur update override akses untuk pilot modul `catatan-keluarga`.
- [x] Jejak audit perubahan ijin akses (`changed_by`, waktu, before/after).

## Langkah Eksekusi
- [x] Audit kontrak akses existing (`RoleMenuVisibilityService`, middleware, matrix role).
- [x] Finalisasi keputusan bertahap via markdown (master TODO + ADR + child TODO).
- [x] Implementasi Tahap 1 read-only matrix super-admin.
- [x] Validasi desain matrix bersama stakeholder domain.
- [x] Implementasi Tahap 2 write override modul `catatan-keluarga`.
- [x] Rollout modul berikutnya per concern terpisah.
- [x] Sinkronisasi dokumen domain/process/ADR pada setiap batch concern.

## Test Matrix Minimum (Concern Ini)
- [x] Feature test super-admin dapat melihat matrix akses read-only.
- [x] Feature test role non super-admin ditolak akses menu management ijin.
- [x] Feature test pilot override `catatan-keluarga` sukses dan fallback aman.
- [x] Unit test resolver akses: prioritas override vs hardcoded fallback.
- [x] Regression test `MenuVisibilityPayload` + middleware visibilitas.
- [x] `php artisan test` penuh sebelum close concern.

## Validasi
- [x] Validasi observasi scoped file akses + route `catatan-keluarga`.
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php`
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`
- [x] `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
- [x] `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`
- [x] `php artisan test`
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/MenuVisibilityPayloadTest.php` (run ulang 2026-03-02)
  - hasil: `PASS` (`26` tests, `280` assertions).

## Risiko
- Risiko eskalasi privilege jika kombinasi role/modul tidak divalidasi ketat.
- Risiko drift lintas scope jika override tidak mengikat `scope-role-area`.
- Risiko regresi global karena concern ini menyentuh central gate akses modul.

## Keputusan
- [x] K1: Implementasi dilakukan bertahap per concern modul (bukan big-bang).
- [x] K2: Tahap awal wajib read-only untuk mengunci keputusan desain sebelum write.
- [x] K3: Override storage aktif terbatas pada modul pilot sampai test matrix stabil.
- [x] K4: Authority tetap backend (`EnsureScopeRole` + `EnsureModuleVisibility` + policy), frontend hanya konsumsi payload.

## Fallback Plan
- [x] Flag rollback untuk menonaktifkan pembacaan override storage dan kembali ke hardcoded penuh.
- [x] Snapshot matrix sebelum perubahan write massal.

## Output Final
- [x] Ringkasan perubahan + alasan + dampak.
  - Tahap 1: matrix akses read-only super-admin untuk observability keputusan akses tanpa mutasi.
  - Tahap 2: write override pilot `catatan-keluarga` + audit trail untuk validasi jalur mutasi terkontrol.
  - Tahap 3 batch 1: rollout override `activities` dengan validasi kompatibilitas `module x role x scope`.
  - Dampak: perubahan akses runtime tidak lagi wajib patch hardcoded untuk modul rollout terkelola; authority backend tetap dijaga via middleware + resolver + policy.
- [x] Daftar file terdampak per layer (route/request/use case/repository/model/ui/test/docs).
  - `route`: `routes/web.php`.
  - `request`: `app/Http/Requests/SuperAdmin/UpdatePilotCatatanKeluargaOverrideRequest.php`, `app/Http/Requests/SuperAdmin/RollbackPilotCatatanKeluargaOverrideRequest.php`.
  - `controller/use case/action`: `app/Http/Controllers/SuperAdmin/AccessControlManagementController.php`, `app/UseCases/SuperAdmin/ListAccessControlMatrixUseCase.php`, `app/Domains/Wilayah/AccessControl/Actions/UpsertPilotCatatanKeluargaOverrideAction.php`, `app/Domains/Wilayah/AccessControl/Actions/RollbackPilotCatatanKeluargaOverrideAction.php`.
  - `repository/model/config`: `app/Domains/Wilayah/AccessControl/Repositories/ModuleAccessOverrideRepositoryInterface.php`, `app/Domains/Wilayah/AccessControl/Repositories/ModuleAccessOverrideRepository.php`, `app/Domains/Wilayah/AccessControl/Models/ModuleAccessOverride.php`, `app/Domains/Wilayah/AccessControl/Models/ModuleAccessOverrideAudit.php`, `database/migrations/2026_02_28_160000_create_module_access_overrides_tables.php`, `config/access_control.php`.
  - `service enforcement`: `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`, `app/Http/Middleware/EnsureModuleVisibility.php`.
  - `ui`: `resources/js/Pages/SuperAdmin/AccessControl/Index.vue`, `resources/js/Layouts/DashboardLayout.vue`.
  - `test`: `tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php`, `tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`, `tests/Unit/Services/RoleMenuVisibilityServiceTest.php`, `tests/Feature/MenuVisibilityPayloadTest.php`.
  - `docs/adr`: `docs/process/archive/2026_02/TODO_ACL26S1_SUPER_ADMIN_MATRIX_READ_ONLY_2026_02_28.md`, `docs/process/archive/2026_02/TODO_ACL26C1_PILOT_OVERRIDE_CATATAN_KELUARGA_2026_02_28.md`, `docs/process/archive/2026_03/TODO_ACL26A2_ROLLOUT_OVERRIDE_MODUL_ACTIVITIES_2026_03_02.md`, `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`.
- [x] Hasil validasi + residual risk + opsi lanjutan.
  - Validasi terbaru 2026-03-02: targeted suite concern akses modular `PASS` (`40` tests, `325` assertions) + full suite `PASS` (`1043` tests, `7009` assertions).
  - Residual risk: tidak ada gap teknis ACL terbuka pada concern parent; fallback rollback per kombinasi dan fallback global hardcoded tetap aktif.
  - Opsi lanjutan:
    1) pertahankan rollout batch kecil untuk modul baru berikutnya dengan pola validasi ACL26E2,
    2) jika muncul insiden akses, rollback per kombinasi `module x role x scope`,
    3) jika perlu freeze total, nonaktifkan rollout override global via config dan kembali ke hardcoded penuh.

## Progress Tahap 1 (2026-02-28)
- Tahap 1 read-only selesai diimplementasikan dengan route/controller/use case/UI/table filter.
- Kontrak mode `read-only|read-write|hidden` sudah dapat diobservasi dari UI super-admin tanpa endpoint mutasi.
- Concern aktif berpindah ke validasi stakeholder dan persiapan Tahap 2 (pilot write modul `catatan-keluarga`).

## Progress Tahap 2 (2026-02-28)
- Tahap 2 pilot write modul `catatan-keluarga` sudah aktif untuk update + rollback dari UI super-admin.
- Audit trail perubahan mode sudah tercatat (`before_mode`, `after_mode`, `changed_by`, `changed_at`).
- Resolver runtime sudah memprioritaskan override pilot saat aktif dan fallback ke hardcoded saat rollback.

## Progress Tahap 3 Batch 1 (2026-03-02)
- Batch rollout modul berikutnya (`activities`) selesai diimplementasikan sebagai concern terpisah (`ACL26A2`).
- Endpoint override digeneralisasi menjadi `PUT/DELETE /super-admin/access-control/override` dengan payload `module`.
- Validasi role-scope-module ditambahkan untuk mencegah kombinasi override yang tidak kompatibel.
- Resolver runtime kini membaca override dari daftar modul rollout terkelola (`catatan-keluarga`, `activities`, `agenda-surat`) dengan fallback hardcoded tetap aktif.

## Mitigasi Ringan Blocker Eksternal (2026-03-02)
- [x] Scope rollout dibekukan sementara pada modul terkelola saat itu (`catatan-keluarga`, `activities`) sampai validasi stakeholder selesai.
- [x] Evidence pack teknis dikunci di concern ini:
  - ringkasan perubahan per tahap (`read-only` -> `pilot` -> `rollout batch 1`),
  - daftar file terdampak lintas layer,
  - hasil validasi targeted suite (`PASS`, `26` tests, `280` assertions).
- [x] Fallback operasional dikunci:
  - rollback per kombinasi role-scope-module tetap tersedia,
  - fallback global ke hardcoded tetap tersedia via flag konfigurasi.
- [x] Agenda validasi stakeholder dijadwalkan pada siklus review mingguan berikutnya (`2026-03-09`).
- [x] Eksekusi sesi validasi stakeholder matrix dan catat keputusan final (go/hold/adjust) pada concern ini.

## Progress Update 2026-03-02 (Mitigasi 1: Stakeholder Readiness Pack)

- Revalidasi evidence teknis concern akses modular dijalankan ulang:
  - `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/MenuVisibilityPayloadTest.php`
  - hasil: `PASS` (`26` tests, `280` assertions).
- Paket keputusan stakeholder dikunci dengan opsi final:
  - `go`: lanjut batch rollout modul berikutnya dengan pola validasi ACL26A2,
  - `hold`: pertahankan rollout saat ini (`catatan-keluarga`, `activities`) tanpa modul baru,
  - `adjust`: ubah daftar modul rollout dan ulang targeted suite sebelum aktivasi.
- Keputusan stakeholder final: `go`, sehingga concern parent dilanjutkan ke eksekusi closure E2E (`ACL26E2`).

## Progress Update 2026-03-02 (Planner Sync: Gap E2E Closure)

- Child concern penutupan gap end-to-end ditetapkan:
  - `docs/process/archive/2026_03/TODO_ACL26E2_PENUTUPAN_GAP_END_TO_END_MANAGEMENT_IJIN_AKSES_2026_03_02.md`
- Tujuan child concern:
  - mengunci keputusan stakeholder `go/hold/adjust`,
  - mengeksekusi rollout batch modul lanjutan dengan regression gate,
  - menyiapkan kriteria close parent concern `ACL26M1` ke status `done`.

## Progress Update 2026-03-02 (Closure ACL26E2)

- Batch 2 override terkelola dieksekusi untuk modul `agenda-surat` dan tervalidasi update/rollback end-to-end.
- Validasi concern ACL pasca batch 2:
  - `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php`
  - hasil: `PASS` (`40` tests, `325` assertions).
  - `php artisan test`
  - hasil: `PASS` (`1043` tests, `7009` assertions).
- Parent concern `ACL26M1` ditutup ke `done`; child concern closure `ACL26E2` menjadi bukti penutupan gap end-to-end.
