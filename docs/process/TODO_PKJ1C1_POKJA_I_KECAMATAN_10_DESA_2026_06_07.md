# TODO PKJ1C1 Pokja I Kecamatan 10 Desa

Tanggal: 2026-06-07  
Status: `planned`  
Related ADR: `docs/adr/ADR_0013_STANDARISASI_KODE_WILAYAH_Pecalungan.md`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Concern ini mengatur perilaku report `data-kegiatan-pkk-pokja-i` untuk user scope `kecamatan`.
- Untuk Kecamatan Pecalungan, report wajib menampilkan 10 baris, satu baris per desa canonical.
- Desa yang belum memiliki data tetap wajib tampil dengan nilai `0`.
- Urutan baris harus mengikuti `areas.code` ascending, bukan `id` atau `name`.
- Kode wilayah canonical Pecalungan sudah dikunci di ADR 0013 dan `database/seeders/WilayahSeeder.php`.

## Kontrak Concern (Lock)

- Domain: `data-kegiatan-pkk-pokja-i` (report-only, level kecamatan).
- Role/scope target: `kecamatan-pokja-i` dan `kecamatan-sekretaris` pada scope `kecamatan`.
- Boundary data:
  - `docs/adr/ADR_0013_STANDARISASI_KODE_WILAYAH_Pecalungan.md`
  - `database/seeders/WilayahSeeder.php`
  - `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php`
  - `app/Domains/Wilayah/CatatanKeluarga/Controllers/CatatanKeluargaPrintController.php`
  - `resources/views/pdf/data_kegiatan_pkk_pokja_i_report.blade.php`
  - `tests/Feature/DataKegiatanPkkPokjaIReportPrintTest.php`
- Acceptance criteria:
  - Kecamatan Pecalungan menghasilkan 10 row report.
  - Row kosong tetap tampil dengan angka `0`.
  - Urutan baris mengikuti `areas.code` canonical.
  - PDF view tidak berubah kontrak tabelnya, hanya isi `items` yang berubah menjadi row per desa.
- Dampak keputusan arsitektur: `tidak` (reuse boundary `catatan-keluarga` report-only).

## Target Hasil

- [ ] Report kecamatan menghasilkan row per desa child canonical.
- [ ] Data kosong tetap tampil dengan default `0`.

## Langkah Eksekusi

- [ ] Analisis scoped dependency + side effect pada jalur report Pokja I.
- [ ] Tambah jalur khusus repository untuk report kecamatan per desa child canonical.
- [ ] Sinkronkan test feature/unit agar mengunci 10 row, zero-fill, dan urutan `areas.code`.
- [ ] Sinkronkan dokumen domain yang menyebut kontrak 4.21 bila perlu.

## Validasi

- [ ] L1: targeted feature test report 4.21 untuk kecamatan Pecalungan.
- [ ] L2: tolak role tidak valid + zero-fill row kosong + urutan `areas.code`.
- [ ] L3: `php artisan test` jika patch sudah stabil.

## Risiko

- Risiko 1: Query agregasi lintas child area bisa undercount bila grouping salah per desa.
- Risiko 2: Urutan baris bisa drift jika default sort tetap memakai `id` atau `name`.

## Keputusan

- [ ] K1: Transformasi report kecamatan harus berupa row per desa canonical, bukan ringkasan satu area.
- [ ] K2: Desa tanpa data tetap dilaporkan sebagai row nol.

## Keputusan Arsitektur (Jika Ada)

- [ ] Tautkan perubahan ini ke ADR 0013 sebagai dasar canonical `areas.code`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jika report per desa belum stabil, kembalikan sementara ke output ringkasan area sambil menjaga dokumentasi kontrak sebagai `planned`.

## Output Final

- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak.
- [ ] Hasil validasi + residual risk.
