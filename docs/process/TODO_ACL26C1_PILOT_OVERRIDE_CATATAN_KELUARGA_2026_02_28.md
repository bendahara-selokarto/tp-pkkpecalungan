# TODO ACL26C1 Pilot Override Catatan Keluarga 2026-02-28

Tanggal: 2026-02-28  
Status: `planned`  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`  
Parent Concern: `docs/process/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`

## Konteks
- Modul `catatan-keluarga` dipilih sebagai pilot write override karena:
  - sudah punya kasus desain akses aktif (termasuk `kecamatan-pokja-iv` hidden),
  - punya jalur report penting (`Cetak 4.20b`) sehingga cocok untuk regression test.
- Pilot dibatasi hanya untuk modul ini agar risiko perubahan akses global tetap terkendali.

## Target Hasil
- [ ] Super-admin dapat mengubah mode akses modul `catatan-keluarga` per role group yang valid.
- [ ] Perubahan tercatat audit trail (`changed_by`, timestamp, before/after).
- [ ] Resolver runtime membaca override pilot jika ada, fallback ke hardcoded jika tidak ada.
- [ ] Tidak ada data leak lintas scope/area setelah override diterapkan.

## Langkah Eksekusi
- [ ] Definisikan schema override akses (scoped role-group + module + mode).
- [ ] Implement repository + action update override khusus pilot.
- [ ] Integrasi resolver runtime dengan prioritas:
  - override aktif,
  - fallback hardcoded matrix.
- [ ] Tambah UI write control khusus baris `catatan-keluarga`.
- [ ] Tambah endpoint rollback cepat untuk menghapus override pilot.

## Test Matrix Pilot
- [ ] Feature test super-admin update override `catatan-keluarga` sukses.
- [ ] Feature test non super-admin ditolak update override.
- [ ] Feature test `kecamatan-pokja-iv` behavior berubah sesuai override dan kembali normal saat rollback.
- [ ] Regression test endpoint report `4.20b` tetap mengikuti enforcement baru.
- [ ] `php artisan test`

## Validasi
- [ ] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`
- [ ] `php artisan test tests/Feature/DesaCatatanKeluargaTest.php tests/Feature/KecamatanCatatanKeluargaTest.php`
- [ ] `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`

## Risiko
- Kesalahan override dapat memblokir akses role valid pada modul kritis report.
- Jika prioritas resolver salah, fallback hardcoded bisa tidak bekerja saat override dihapus.

## Keputusan
- [ ] Pilot write dibatasi satu modul hingga regression test stabil.

## Fallback Plan
- [ ] Hapus override pilot (revert ke hardcoded) via endpoint rollback/admin action.
- [ ] Nonaktifkan pembacaan override secara global jika anomali ditemukan.

## Output Final
- [ ] Ringkasan dampak pilot terhadap akses role modul `catatan-keluarga`.
- [ ] Bukti validasi test + keputusan lanjut ke batch modul berikutnya.
