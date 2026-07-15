# TODO HRDN02 Bidang Pokja III & PKG/TBC Hardening

Tanggal: 2026-07-15
Status: `in-progress`
Related ADR: `-`

## Konteks

Dua migration stub yang ditemukan uncommitted setelah sesi HRDN01 selesai:

1. `add_bidang_to_anggota_pokjas` — kolom terstruktur `bidang_pokja_iii` (pangan/sandang/tata_laksana_rumah_tangga)
   untuk mengganti parsing teks bebas `jabatan` pada agregasi kader Pokja III di `CatatanKeluargaRepository`.

2. `add_pkg_tbc_flags_to_data_kegiatan_wargas` — flag boolean `is_pkg` dan `is_tbc` pada `DataKegiatanWarga`
   sebagai field terstruktur (sebelumnya tidak ada agregasi PKG/TBC sama sekali di repository).

## Kontrak Concern (Lock)

- Domain: `AnggotaPokja` + `DataKegiatanWarga`
- Role/scope target: `desa` (input), `kecamatan` (read agregasi)
- Boundary data: `anggota_pokjas`, `data_kegiatan_wargas`
- Acceptance criteria:
  - `bidang_pokja_iii` tersimpan dan divalidasi backend; CatatanKeluargaRepository prioritaskan kolom ini.
  - `is_pkg` / `is_tbc` tersimpan dan divalidasi backend; frontend menampilkan checkbox.
  - Test suite tetap hijau (0 failures).

## Target Hasil

- [x] **bidang_pokja_iii**: kolom nullable enum (`pangan`, `sandang`, `tata_laksana_rumah_tangga`) di `anggota_pokjas`.
- [x] **is_pkg / is_tbc**: kolom boolean (default false) di `data_kegiatan_wargas`.
- [x] `CatatanKeluargaRepository::getDataKegiatanPkkPokjaIiiByLevelAndArea`: prioritas `bidang_pokja_iii`, fallback `jabatan` parsing.
- [x] Frontend AnggotaPokja (Desa+Kecamatan Create/Edit): dropdown bidang kondisional (muncul jika pokja III).
- [x] Frontend DataKegiatanWarga (Desa+Kecamatan Create/Edit): 2 checkbox PKG dan TBC.
- [ ] `php artisan test --compact` hijau penuh.

## Langkah Eksekusi

### Concern A — bidang_pokja_iii

- [x] Migration applied.
- [x] `AnggotaPokja` model → `$fillable` tambah `bidang_pokja_iii`.
- [x] `AnggotaPokjaData` DTO → tambah `?string $bidang_pokja_iii`.
- [x] `StoreAnggotaPokjaRequest` → `nullable|in:pangan,sandang,tata_laksana_rumah_tangga`.
- [x] `UpdateAnggotaPokjaRequest` → idem.
- [x] `AnggotaPokjaRepository::store/update` → simpan field.
- [x] `CatatanKeluargaRepository::getDataKegiatanPkkPokjaIiiByLevelAndArea` → exact match bidang dulu, fallback jabatan.
- [x] Desa/Kecamatan `AnggotaPokja/Create.vue` → dropdown kondisional bidang.
- [x] Desa/Kecamatan `AnggotaPokja/Edit.vue` → idem.

### Concern B — is_pkg / is_tbc

- [x] Migration applied.
- [x] `DataKegiatanWarga` model → `$fillable` + boolean casts.
- [x] `DataKegiatanWargaData` DTO → tambah `bool $is_pkg`, `bool $is_tbc`.
- [x] `StoreDataKegiatanWargaRequest` → `nullable|boolean` + `prepareForValidation`.
- [x] `UpdateDataKegiatanWargaRequest` → idem.
- [x] `DataKegiatanWargaRepository::store/update` → simpan field.
- [x] Desa/Kecamatan `DataKegiatanWarga/Create.vue` → 2 checkbox PKG/TBC.
- [x] Desa/Kecamatan `DataKegiatanWarga/Edit.vue` → idem.

## Validasi

- [x] L1: `php artisan migrate` — 2 migrasi DONE ✓
- [x] L2: `php artisan test --filter "AnggotaPokja|DataKegiatanWarga" --compact` — 29 passed ✓
- [ ] L3: `php artisan test --compact` — in-progress

## Risiko

- Data lama `anggota_pokjas` dengan kolom `jabatan` berisi teks pangan/sandang tetap terbaca lewat fallback.
- Tidak ada data `data_kegiatan_wargas` lama dengan is_pkg/is_tbc karena kolom baru (default false).

## Keputusan

- [x] K1: `bidang_pokja_iii` nullable — tidak memaksa record lama isi ulang.
- [x] K2: PKG/TBC hanya checkbox boolean; tidak ada agregasi ke `CatatanKeluargaRepository` (belum ada laporan yang membutuhkan).

## Fallback Plan

- Rollback migration: `php artisan migrate:rollback` (2 batch).
- Revert Vue: `git checkout -- resources/js/Pages/`.
