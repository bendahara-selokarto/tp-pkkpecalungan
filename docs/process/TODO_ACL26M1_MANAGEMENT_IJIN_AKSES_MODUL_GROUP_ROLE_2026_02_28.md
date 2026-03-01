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
- [x] Menu baru sidebar super-admin: `Management Ijin Akses`.
- [x] Halaman matrix akses read-only untuk kontrol keputusan desain.
- [x] Fitur update override akses untuk pilot modul `catatan-keluarga`.
- [x] Jejak audit perubahan ijin akses (`changed_by`, waktu, before/after).

## Langkah Eksekusi
- [x] Audit kontrak akses existing (`RoleMenuVisibilityService`, middleware, matrix role).
- [x] Finalisasi keputusan bertahap via markdown (master TODO + ADR + child TODO).
- [x] Implementasi Tahap 1 read-only matrix super-admin.
- [ ] Validasi desain matrix bersama stakeholder domain.
- [x] Implementasi Tahap 2 write override modul `catatan-keluarga`.
- [ ] Rollout modul berikutnya per concern terpisah.
- [ ] Sinkronisasi dokumen domain/process/ADR pada setiap batch concern.

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
- [ ] Ringkasan perubahan + alasan + dampak.
- [ ] Daftar file terdampak per layer (route/request/use case/repository/model/ui/test/docs).
- [ ] Hasil validasi + residual risk + opsi lanjutan.

## Progress Tahap 1 (2026-02-28)
- Tahap 1 read-only selesai diimplementasikan dengan route/controller/use case/UI/table filter.
- Kontrak mode `read-only|read-write|hidden` sudah dapat diobservasi dari UI super-admin tanpa endpoint mutasi.
- Concern aktif berpindah ke validasi stakeholder dan persiapan Tahap 2 (pilot write modul `catatan-keluarga`).

## Progress Tahap 2 (2026-02-28)
- Tahap 2 pilot write modul `catatan-keluarga` sudah aktif untuk update + rollback dari UI super-admin.
- Audit trail perubahan mode sudah tercatat (`before_mode`, `after_mode`, `changed_by`, `changed_at`).
- Resolver runtime sudah memprioritaskan override pilot saat aktif dan fallback ke hardcoded saat rollback.

## Hardening Koherensi Sidebar vs Matrix (2026-03-01)
- Kontrak dikunci ulang: `super-admin` diposisikan sebagai role administratif jalur `/super-admin/*` dan tidak lagi dimodelkan sebagai role scoped operasional `desa|kecamatan`.
- Dampak implementasi:
  - baseline visibilitas `RoleMenuVisibilityService` untuk `super-admin` pada group domain/monitoring dihapus,
  - `RoleScopeMatrix::scopedRoles()` tidak lagi memasukkan `super-admin` pada scope `kecamatan`.
- Dampak produk:
  - matrix management ijin tidak lagi menampilkan impresi bahwa `super-admin` bisa mengakses monitoring kecamatan,
  - perilaku sidebar dan enforcement route menjadi koheren.
