# TODO HRDN01 Data Hardening Kader Khusus, Kehamilan & Kematian

Tanggal: 2026-07-15  
Status: `done`  
Related ADR: `-`

## Konteks

Agregasi laporan pada `CatatanKeluargaRepository` bergantung pada parsing regex/string bebas dari
kolom `keterangan` (textarea) untuk mengekstrak:

1. **Status kehamilan/nifas** ibu (`extractMaternalStatus` — baris 2518–2542)
2. **Data kematian** anggota keluarga (`extractDeathInfo` — baris 2548–2597)
3. **Kategori Kader Khusus** (`containsAnyKeyword` — kolom `jenis_kader_khusus` string bebas)

Pola ini rawan salah input user dan menyebabkan angka agregasi laporan tidak akurat,
serupa dengan kasus RT/RW yang sudah diperbaiki sebelumnya (commit `ed3831a7`).

## Kontrak Concern (Lock)

- Domain: `DataWarga (data_warga_anggotas)` + `KaderKhusus`
- Role/scope target: `desa` (input), `kecamatan` (read agregasi)
- Boundary data: `data_warga_anggotas`, `kader_khusus`
- Acceptance criteria:
  - Kolom terstruktur tersedia dan divalidasi di backend.
  - Frontend menggunakan dropdown/checkbox, bukan input teks bebas.
  - `CatatanKeluargaRepository` membaca kolom terstruktur terlebih dahulu; fallback ke parsing `keterangan` hanya untuk data lama/legacy.
  - Semua test suite tetap hijau.
- Dampak keputusan arsitektur: `tidak` (perubahan data layer, bukan boundary arsitektur utama)

## Target Hasil

- [x] **Kader Khusus**: `jenis_kader_khusus` terkunci ke nilai enum valid via validasi request + dropdown frontend.
- [x] **Kehamilan**: kolom `status_kehamilan` nullable enum (`hamil`, `melahirkan`, `nifas`, `normal`) tersedia di `data_warga_anggotas`.
- [x] **Kematian**: kolom `is_meninggal`, `tanggal_meninggal`, `sebab_meninggal`, `golongan_kematian` tersedia di `data_warga_anggotas`.
- [x] `CatatanKeluargaRepository` diperbarui: kolom terstruktur diutamakan, fallback regex tetap aktif untuk data lama.
- [x] Frontend `DataWargaAnggotaTable.vue` menampilkan dropdown/panel status kehamilan dan kematian.
- [x] `php artisan test` hijau penuh.

## Langkah Eksekusi

### Komponen 1 — Kader Khusus

- [x] Tambah validasi `in:BKB,Koperasi,Keterampilan` pada `StoreKaderKhususRequest` & `UpdateKaderKhususRequest`.
- [x] Ganti input teks bebas di `Create.vue` & `Edit.vue` (KaderKhusus) ke dropdown.
- [x] Refactor matching di `CatatanKeluargaRepository`: exact match `BKB/Koperasi/Keterampilan` sebelum keyword fallback.

### Komponen 2 — Data Warga: Kehamilan & Kematian

- [x] Migration: tambah kolom ke `data_warga_anggotas`.
- [x] Tambah kolom baru ke `$fillable` di model `DataWargaAnggota`.
- [x] Tambah validasi ke `StoreDataWargaRequest` & `UpdateDataWargaRequest`.
- [x] Update `DataWargaAnggotaRepository::syncForDataWarga` agar menyimpan field baru.
- [x] Update `DataWargaAnggotaTable.vue`: dropdown kehamilan + panel kematian.
- [x] Update `CatatanKeluargaRepository::extractMaternalStatus`: prioritas kolom terstruktur.
- [x] Update `CatatanKeluargaRepository::extractDeathInfo`: prioritas kolom terstruktur.

## Validasi

- [x] L1: `php artisan test --filter DataWarga` — 31 passed ✓
- [x] L2: `php artisan test --filter KaderKhusus` — 28 passed ✓
- [x] L3: `php artisan test --compact` — **985 passed, 402 skipped, 0 failures** ✓

## Risiko

- Data lama yang tersimpan di `keterangan` tidak otomatis dimigrasikan ke kolom baru → **dimitigasi** dengan fallback parsing tetap aktif.
- Jika user sudah punya nilai `jenis_kader_khusus` di luar set enum, validasi baru akan menolak edit → **dimitigasi** dengan membersihkan data sebelum menutup form lama.

## Keputusan

- [x] K1: Pertahankan fallback regex untuk data lama; kolom terstruktur diutamakan untuk data baru.
- [x] K2: Set enum kader khusus: `BKB`, `Koperasi`, `Keterampilan` — sesuai keyword lama di `containsAnyKeyword`.

## Fallback Plan

- Jika migration bermasalah: jalankan `php artisan migrate:rollback` (satu batch).
- Jika frontend compile error: revert perubahan Vue dengan `git checkout -- resources/js/`.

## Output Final

- [x] **Diubah**: Validasi request, repository aggregation, dan frontend forms untuk mengganti free-text dengan kolom terstruktur.
- [x] **File terdampak**:
  - `StoreKaderKhususRequest.php`, `UpdateKaderKhususRequest.php`
  - `Create.vue`, `Edit.vue` (KaderKhusus)
  - `database/migrations/2026_07_15_080000_add_maternal_and_mortality_to_data_warga_anggotas.php`
  - `DataWargaAnggota.php` (fillable + casts)
  - `StoreDataWargaRequest.php`, `UpdateDataWargaRequest.php`
  - `DataWargaAnggotaRepository.php` (syncForDataWarga + 2 normalizer baru)
  - `DataWargaAnggotaTable.vue` (dropdown kehamilan + panel kematian)
  - `CatatanKeluargaRepository.php` (extractMaternalStatus + extractDeathInfo + kader matching)
  - `DesaKaderKhususTest.php`, `KecamatanKaderKhususTest.php` (test diperbarui ke enum baru)
- [x] **Validasi**: 985 passed, 0 failures. Fallback legacy tetap aktif.
