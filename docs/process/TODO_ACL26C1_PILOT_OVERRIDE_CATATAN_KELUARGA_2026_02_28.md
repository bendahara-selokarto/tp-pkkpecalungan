# TODO ACL26C1 Pilot Override Catatan Keluarga 2026-02-28

Tanggal: 2026-02-28  
Status: `done`  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`  
Parent Concern: `docs/process/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`

## Konteks
- Modul `catatan-keluarga` dipilih sebagai pilot write override karena:
  - sudah punya kasus desain akses aktif (termasuk `kecamatan-pokja-iv` hidden),
  - punya jalur report penting (`Cetak 4.20b`) sehingga cocok untuk regression test.
- Pilot dibatasi hanya untuk modul ini agar risiko perubahan akses global tetap terkendali.

## Target Hasil
- [x] Super-admin dapat mengubah mode akses modul `catatan-keluarga` per role group yang valid.
- [x] Perubahan tercatat audit trail (`changed_by`, timestamp, before/after).
- [x] Resolver runtime membaca override pilot jika ada, fallback ke hardcoded jika tidak ada.
- [x] Tidak ada data leak lintas scope/area setelah override diterapkan.

## Langkah Eksekusi
- [x] Definisikan schema override akses (scoped role-group + module + mode).
- [x] Implement repository + action update override khusus pilot.
- [x] Integrasi resolver runtime dengan prioritas:
  - override aktif,
  - fallback hardcoded matrix.
- [x] Tambah UI write control khusus baris `catatan-keluarga`.
- [x] Tambah endpoint rollback cepat untuk menghapus override pilot.

## Test Matrix Pilot
- [x] Feature test super-admin update override `catatan-keluarga` sukses.
- [x] Feature test non super-admin ditolak update override.
- [x] Feature test `kecamatan-pokja-iv` behavior berubah sesuai override dan kembali normal saat rollback.
- [x] Regression test endpoint report `4.20b` tetap mengikuti enforcement baru.
- [x] `php artisan test`

## Validasi
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`
- [x] `php artisan test tests/Feature/DesaCatatanKeluargaTest.php tests/Feature/KecamatanCatatanKeluargaTest.php`
- [x] `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`

## Risiko
- Kesalahan override dapat memblokir akses role valid pada modul kritis report.
- Jika prioritas resolver salah, fallback hardcoded bisa tidak bekerja saat override dihapus.

## Keputusan
- [x] Pilot write dibatasi satu modul hingga regression test stabil.

## Fallback Plan
- [x] Hapus override pilot (revert ke hardcoded) via endpoint rollback/admin action.
- [x] Nonaktifkan pembacaan override secara global jika anomali ditemukan.

## Output Final
- [x] Ringkasan dampak pilot terhadap akses role modul `catatan-keluarga`.
- [x] Bukti validasi test + keputusan lanjut ke batch modul berikutnya.

## Hasil Implementasi (2026-02-28)
- Storage:
  - tabel `module_access_overrides`,
  - tabel audit `module_access_override_audits`.
- Backend:
  - repository override + audit trail,
  - action `upsert` dan `rollback` pilot `catatan-keluarga`,
  - resolver `RoleMenuVisibilityService` membaca override aktif terlebih dahulu, lalu fallback hardcoded.
- Super-admin:
  - endpoint update override: `PUT /super-admin/access-control/override` (payload wajib menyertakan `module=catatan-keluarga`),
  - endpoint rollback override: `DELETE /super-admin/access-control/override` (payload wajib menyertakan `module=catatan-keluarga`),
  - kontrol write pada UI matrix baris `catatan-keluarga`.
- Fallback:
  - flag konfigurasi `ACCESS_CONTROL_PILOT_OVERRIDE_ENABLED` (kompatibilitas legacy) dan `ACCESS_CONTROL_ROLLOUT_OVERRIDE_ENABLED` untuk menonaktifkan pembacaan override secara global jika diperlukan.
