# TODO ACL26S1 Matrix Read-Only Ijin Akses Super Admin 2026-02-28

Tanggal: 2026-02-28  
Status: `planned`  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`  
Parent Concern: `docs/process/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`

## Konteks
- Tahap awal perlu halaman read-only agar stakeholder dapat memvalidasi desain matrix akses sebelum write override diaktifkan.
- Basis data akses diambil dari resolver runtime existing (`RoleMenuVisibilityService`) agar representasi akurat dengan kondisi produksi saat ini.

## Target Hasil
- [ ] Menu super-admin baru: `Management Ijin Akses`.
- [ ] Tabel read-only menampilkan kombinasi:
  - `scope` (`desa|kecamatan`),
  - `role group`,
  - `modul`,
  - `mode efektif` (`read-only|read-write|hidden`).
- [ ] Filter minimum: scope, role group, mode akses.
- [ ] Tidak ada endpoint write/update pada tahap ini.

## Langkah Eksekusi
- [ ] Tambah route + controller read-only concern super-admin.
- [ ] Tambah use case read model matrix akses dari resolver runtime.
- [ ] Tambah halaman Inertia `SuperAdmin/AccessControl/Index` (read-only).
- [ ] Tambah menu pada layout super-admin.
- [ ] Tambah feature test akses menu (allowed: super-admin, denied: non super-admin).

## Validasi
- [ ] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php`
- [ ] `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`
- [ ] `php artisan test`

## Risiko
- Potensi salah tafsir jika label tabel terlalu teknis.
- Potensi mismatch jika resolver runtime berubah tanpa sinkronisasi read model.

## Keputusan
- [ ] Tahap ini murni observability UI, tanpa mutasi data ijin akses.

## Output Final
- [ ] Screenshot/rekap matrix akses dari UI super-admin.
- [ ] Catatan keputusan desain untuk lanjut tahap write pilot.
