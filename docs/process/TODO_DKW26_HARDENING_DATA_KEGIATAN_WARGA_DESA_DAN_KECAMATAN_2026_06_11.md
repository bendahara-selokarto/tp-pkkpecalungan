# TODO DKW26 Hardening Data Kegiatan Warga Desa dan Kecamatan

Tanggal: 2026-06-11  
Status: `in-progress`
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Hardening modul `Data Kegiatan Warga` (4.14.1b) untuk memenuhi kebutuhan operasional Desa dan Kecamatan.
- Untuk level **Desa**: Pastikan 7 kolom kegiatan dapat terisi dan memiliki relasi ke modul sumber (Buku Kegiatan).
- Untuk level **Kecamatan**: Mengubah perilaku modul menjadi **Recap per Desa** (Group by Desa).

## Kontrak Concern (Lock)

- Domain: `data-kegiatan-warga` (4.14.1b).
- Role/scope target: `desa-*` dan `kecamatan-*`.
- Boundary data: `data_kegiatan_wargas` table, Repository, UseCase, Controller, Vue UI, PDF View.
- Acceptance criteria:
  - Level Desa: Mendukung penyimpanan `source_module` dan `source_id`.
  - Level Kecamatan: Index menampilkan tabel rekap per Desa canonical (10 desa).
  - Urutan Desa di Kecamatan mengikuti `areas.code` ascending.
- Dampak keputusan arsitektur: `ya` (perubahan perilaku level kecamatan dari CRUD mandiri ke RECAP).

## Target Hasil

- [x] Migrasi penambahan kolom `source_module` dan `source_id` pada `data_kegiatan_wargas`.
- [x] Refactor repository untuk mendukung agregasi data Desa untuk Kecamatan.
- [x] Refactor UI Kecamatan untuk menampilkan tabel rekap per Desa.
- [x] Sinkronisasi PDF Report 4.14.1b untuk Kecamatan.

## Langkah Eksekusi

- [x] P1. Migrasi database: tambahkan `source_module` dan `source_id` (nullable).
- [x] P2. Update Model `DataKegiatanWarga` dan DTO terkait.
- [x] P3. Update Repository: Tambah method `getRecapByDesaForKecamatan`.
- [x] P4. Update UseCase: Alihkan `execute` untuk level kecamatan ke method recap.
- [x] P5. Update UI Kecamatan: Ubah tabel `Index.vue` menjadi rekap per Desa.
- [x] P6. Update PDF Report: Sesuaikan view `data_kegiatan_warga_report.blade.php` untuk mode rekap.
- [ ] P7. Validasi: Feature test untuk memastikan rekap kecamatan mencakup 10 desa dan zero-fill.

## Validasi

- [ ] L1: syntax/lint/targeted test concern.
- [ ] L2: regression test concern terkait.
- [ ] L3: `php artisan test` jika perubahan signifikan.

## Risiko

- Risiko 1: Data lama di level kecamatan tidak terbaca di rekap jika tidak ada data desa yang ekuivalen.
- Risiko 2: Urutan desa tidak sesuai jika `areas.code` tidak konsisten.

## Keputusan

- [x] K1: Menggunakan Opsi A (Manual Link) untuk relasi sumber data di Desa.
- [x] K2: Kecamatan murni menampilkan rekapitulasi data dari Desa.

## Fallback Plan

- Jika rekap bermasalah, kembalikan controller kecamatan untuk membaca model `DataKegiatanWarga` secara langsung (level kecamatan).

## Output Final

- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak.
- [ ] Hasil validasi + residual risk.
