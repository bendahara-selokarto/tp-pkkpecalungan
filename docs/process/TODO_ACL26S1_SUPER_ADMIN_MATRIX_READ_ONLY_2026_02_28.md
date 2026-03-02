# TODO ACL26S1 Matrix Read-Only Ijin Akses Super Admin 2026-02-28

Tanggal: 2026-02-28  
Status: `done`  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`  
Parent Concern: `docs/process/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`

## Konteks
- Tahap awal perlu halaman read-only agar stakeholder dapat memvalidasi desain matrix akses sebelum write override diaktifkan.
- Basis data akses diambil dari resolver runtime existing (`RoleMenuVisibilityService`) agar representasi akurat dengan kondisi produksi saat ini.

## Target Hasil
- [x] Menu super-admin baru: `Management Ijin Akses`.
- [x] Tabel read-only menampilkan kombinasi:
  - `scope` (`desa|kecamatan`),
  - `role group`,
  - `modul`,
  - `mode efektif` (`read-only|read-write|hidden`).
- [x] Filter minimum: scope, role group, mode akses.
- [x] Tidak ada endpoint write/update pada tahap ini.

## Langkah Eksekusi
- [x] Tambah route + controller read-only concern super-admin.
- [x] Tambah use case read model matrix akses dari resolver runtime.
- [x] Tambah halaman Inertia `SuperAdmin/AccessControl/Index` (read-only).
- [x] Tambah menu pada layout super-admin.
- [x] Tambah feature test akses menu (allowed: super-admin, denied: non super-admin).

## Validasi
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php`
- [x] `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`
- [x] `php artisan test`

## Risiko
- Potensi salah tafsir jika label tabel terlalu teknis.
- Potensi mismatch jika resolver runtime berubah tanpa sinkronisasi read model.

## Keputusan
- [x] Tahap ini murni observability UI, tanpa mutasi data ijin akses.

## Output Final
- [x] Screenshot/rekap matrix akses dari UI super-admin.
- [x] Catatan keputusan desain untuk lanjut tahap write pilot.

## Hasil Implementasi (2026-02-28)
- Backend:
  - route `super-admin/access-control` (read-only),
  - controller `AccessControlManagementController`,
  - use case `ListAccessControlMatrixUseCase`,
  - read model berbasis `RoleMenuVisibilityService` + `RoleScopeMatrix`.
- Frontend:
  - halaman `SuperAdmin/AccessControl/Index.vue`,
  - menu sidebar super-admin `Management Ijin Akses`.
- Testing:
  - feature test khusus read-only matrix super-admin,
  - regression test payload visibilitas menu,
  - full test suite (`php artisan test`) hijau.

## Hardening Koherensi (2026-03-01)
- Matrix read-only diselaraskan dengan kontrak runtime aktual bahwa `super-admin` tidak memiliki akses menu domain scoped (`desa/kecamatan`) termasuk `monitoring`.
- Basis kontrak yang diperbarui:
  - `RoleMenuVisibilityService`: baseline group mode `super-admin` untuk domain/monitoring dihapus.
  - `RoleScopeMatrix`: `super-admin` tidak lagi tercatat sebagai scoped role `kecamatan`.
- Efek yang diharapkan:
  - tidak ada lagi mismatch antara tabel management ijin dan sidebar nyata untuk akun `super-admin`.
