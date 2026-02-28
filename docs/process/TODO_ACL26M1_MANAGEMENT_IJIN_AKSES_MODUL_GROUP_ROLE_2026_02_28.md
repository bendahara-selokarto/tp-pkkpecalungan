# TODO ACL26M1 Management Ijin Akses Berbasis Modul dan Group Role 2026-02-28

Tanggal: 2026-02-28  
Status: `in-progress`  
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
  - `docs/process/TODO_ACL26S1_SUPER_ADMIN_MATRIX_READ_ONLY_2026_02_28.md`
- Tahap 2 pilot modul `catatan-keluarga`:
  - `docs/process/TODO_ACL26C1_PILOT_OVERRIDE_CATATAN_KELUARGA_2026_02_28.md`

## Target Hasil
- [x] Observasi kontrak akses existing selesai dan tervalidasi scoped.
- [x] Finalisasi markdown baseline concern (master TODO + ADR + registry SOT).
- [ ] Menu baru sidebar super-admin: `Management Ijin Akses`.
- [ ] Halaman matrix akses read-only untuk kontrol keputusan desain.
- [ ] Fitur update override akses untuk pilot modul `catatan-keluarga`.
- [ ] Jejak audit perubahan ijin akses (`changed_by`, waktu, before/after).

## Langkah Eksekusi
- [x] Audit kontrak akses existing (`RoleMenuVisibilityService`, middleware, matrix role).
- [x] Finalisasi keputusan bertahap via markdown (master TODO + ADR + child TODO).
- [ ] Implementasi Tahap 1 read-only matrix super-admin.
- [ ] Validasi desain matrix bersama stakeholder domain.
- [ ] Implementasi Tahap 2 write override modul `catatan-keluarga`.
- [ ] Rollout modul berikutnya per concern terpisah.
- [ ] Sinkronisasi dokumen domain/process/ADR pada setiap batch concern.

## Test Matrix Minimum (Concern Ini)
- [ ] Feature test super-admin dapat melihat matrix akses read-only.
- [ ] Feature test role non super-admin ditolak akses menu management ijin.
- [ ] Feature test pilot override `catatan-keluarga` sukses dan fallback aman.
- [ ] Unit test resolver akses: prioritas override vs hardcoded fallback.
- [ ] Regression test `MenuVisibilityPayload` + middleware visibilitas.
- [ ] `php artisan test` penuh sebelum close concern.

## Validasi
- [x] Validasi observasi scoped file akses + route `catatan-keluarga`.
- [ ] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php`
- [ ] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`
- [ ] `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
- [ ] `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`
- [ ] `php artisan test`

## Risiko
- Risiko eskalasi privilege jika kombinasi role/modul tidak divalidasi ketat.
- Risiko drift lintas scope jika override tidak mengikat `scope-role-area`.
- Risiko regresi global karena concern ini menyentuh central gate akses modul.

## Keputusan
- [x] K1: Implementasi dilakukan bertahap per concern modul (bukan big-bang).
- [x] K2: Tahap awal wajib read-only untuk mengunci keputusan desain sebelum write.
- [ ] K3: Override storage aktif terbatas pada modul pilot sampai test matrix stabil.
- [x] K4: Authority tetap backend (`EnsureScopeRole` + `EnsureModuleVisibility` + policy), frontend hanya konsumsi payload.

## Fallback Plan
- [ ] Flag rollback untuk menonaktifkan pembacaan override storage dan kembali ke hardcoded penuh.
- [ ] Snapshot matrix sebelum perubahan write massal.

## Output Final
- [ ] Ringkasan perubahan + alasan + dampak.
- [ ] Daftar file terdampak per layer (route/request/use case/repository/model/ui/test/docs).
- [ ] Hasil validasi + residual risk + opsi lanjutan.
