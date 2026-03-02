# TODO Monitoring Visibility Semua Modul (2026-02-27)

## Konteks
- Dokumen acuan utama: `docs/process/MONITORING_VISIBILITY_MODUL.md`.
- Scope concern: seluruh modul yang dipetakan oleh `RoleMenuVisibilityService` untuk group:
  - `sekretaris-tpk`
  - `pokja-i`
  - `pokja-ii`
  - `pokja-iii`
  - `pokja-iv`
  - `monitoring`
- Tujuan: menjadikan monitoring visibility lintas modul sebagai gate tetap pada setiap perubahan kontrak role/scope/module.

## Target Hasil
- Baseline inventory semua slug modul aktif terdokumentasi.
- Baseline profil visibility per role utama terdokumentasi.
- Bukti validasi global untuk seluruh modul tersedia.
- Perubahan lanjutan wajib mengikuti trigger monitoring yang sama.

## Baseline Inventory Modul Aktif (Source of Truth Backend)

### A. Group `sekretaris-tpk`
- [x] `anggota-tim-penggerak`
- [x] `anggota-tim-penggerak-kader`
- [x] `kader-khusus`
- [x] `agenda-surat`
- [x] `buku-daftar-hadir`
- [x] `buku-tamu`
- [x] `buku-notulen-rapat`
- [x] `buku-keuangan`
- [x] `bantuans`
- [x] `inventaris`
- [x] `activities`
- [x] `program-prioritas`
- [x] `anggota-pokja`
- [x] `prestasi-lomba`
- [x] `laporan-tahunan-pkk`

### B. Group `pokja-i`
- [x] `activities`
- [x] `anggota-pokja`
- [x] `prestasi-lomba`
- [x] `data-warga`
- [x] `data-kegiatan-warga`
- [x] `bkl`
- [x] `bkr`
- [x] `paar`

### C. Group `pokja-ii`
- [x] `activities`
- [x] `anggota-pokja`
- [x] `prestasi-lomba`
- [x] `data-pelatihan-kader`
- [x] `taman-bacaan`
- [x] `koperasi`
- [x] `kejar-paket`

### D. Group `pokja-iii`
- [x] `activities`
- [x] `anggota-pokja`
- [x] `prestasi-lomba`
- [x] `data-keluarga`
- [x] `data-industri-rumah-tangga`
- [x] `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`
- [x] `warung-pkk`

### E. Group `pokja-iv`
- [x] `activities`
- [x] `anggota-pokja`
- [x] `prestasi-lomba`
- [x] `posyandu`
- [x] `simulasi-penyuluhan`
- [x] `catatan-keluarga`
- [x] `pilot-project-naskah-pelaporan`
- [x] `pilot-project-keluarga-sehat`

### F. Group `monitoring`
- [x] `desa-activities`
- [x] `desa-arsip`

## Baseline Profil Visibility Role (Ringkas)
- [x] `desa-sekretaris`: `sekretaris-tpk` RW, group `pokja-i..iv` RO.
- [x] `kecamatan-sekretaris`: `sekretaris-tpk` RW, group `pokja-i..iv` RO, `monitoring` RO.
- [x] `desa-pokja-i..iv`: hanya group pokja masing-masing RW.
- [x] `kecamatan-pokja-i..iv`: hanya group pokja masing-masing RW, dengan override remove modul pokja desa-only.
- [x] `admin-desa`: seluruh group desa RW.
- [x] `admin-kecamatan`: seluruh group kecamatan RW + `monitoring` RO.
- [x] `super-admin`: seluruh group RW.

## Trigger Monitoring Lintas Semua Modul
- [x] Penambahan slug modul baru ke salah satu group.
- [x] Pengurangan slug modul dari group.
- [x] Perubahan mode akses role terhadap group/modul.
- [x] Perubahan override modul khusus role tertentu.
- [x] Perubahan guard middleware `module.visibility`.
- [x] Perubahan kontrak menu frontend terhadap `auth.user.moduleModes`.

## Langkah Eksekusi

### A. Audit Kontrak
- [x] Audit mapping group-modul di `RoleMenuVisibilityService`.
- [x] Audit enforcement backend di `EnsureModuleVisibility`.
- [x] Audit guard frontend di `DashboardLayout.vue`.

### B. Validasi Teknis
- [x] Jalankan validasi global lintas modul dengan `php artisan test`.
- [x] Tambah gate test kontrak global:
  - `tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php`.

## Bukti Validasi (2026-02-27)
- [x] `php artisan test`
  - hasil: `PASS` (`925` tests, `5750` assertions).
- [x] `php artisan test tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
  - hasil: `PASS` (`28` tests, `322` assertions).

## Risiko Residual
- Drift kontrak jika perubahan modul tidak memutakhirkan baseline inventory di dokumen ini.
- Drift frontend/backend jika item menu baru tidak tercover `moduleModes`.
- Drift role-scope-area jika ada perubahan role tanpa validasi middleware/policy.

## Keputusan
- [x] Monitoring visibility dinaikkan dari concern tunggal `activities` menjadi concern lintas semua modul.
- [x] Dokumen `TODO_MONITORING_VISIBILITY_MODUL_KEGIATAN_2026_02_27.md` dipertahankan sebagai sub-scope khusus modul kegiatan.
- [x] Gate global default untuk perubahan visibility lintas modul adalah `php artisan test`.

## Output Wajib Saat Update Berikutnya
- [ ] Catat slug modul yang berubah (add/remove/change-mode).
- [ ] Catat role terdampak dan mode sebelum/sesudah.
- [ ] Lampirkan hasil validasi terbaru.
- [ ] Sinkronkan dokumen canonical/process terkait concern yang sama.
