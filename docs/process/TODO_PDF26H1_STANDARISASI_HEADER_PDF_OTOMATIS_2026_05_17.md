# TODO PDF26H1 Standarisasi Header PDF Otomatis

Tanggal: 2026-05-17  
Status: `done`
Related ADR: `-`

## Konteks

- Saat ini header laporan PDF (Judul dan Metadata) masih dikelola secara manual di masing-masing file Blade.
- Dibutuhkan standarisasi otomatis berdasarkan peran (role) dan wilayah (area) pengguna untuk memastikan konsistensi dan kemudahan pemeliharaan.

## Kontrak Concern (Lock)

- Domain: Output PDF Reports
- Role/scope target: Semua role (Desa & Kecamatan)
- Boundary data: Metadata pengguna login (name, role, area, parent area)
- Acceptance criteria:
  - Tersedia komponen Blade reusable untuk header PDF.
  - `PdfViewFactory` otomatis menyuntikkan data header standar.
  - Judul laporan otomatis mencantumkan nama modul + peran pengguna.
  - Metadata wilayah otomatis mencantumkan Desa/Kecamatan sesuai konteks.
- Dampak keputusan arsitektur: `ya` (standarisasi jalur data view PDF)

## Target Hasil

- [x] Komponen reusable `resources/views/pdf/partials/_report_header.blade.php` tersedia.
- [x] `PdfViewFactory` dimodifikasi untuk otomatisasi data header.
- [x] Minimal 2 modul (Agenda Surat & Aktivitas) menggunakan header baru sebagai pilot.

## Langkah Eksekusi

- [x] Membuat file `resources/views/pdf/partials/_report_header.blade.php`.
- [x] Menambahkan logika deteksi role dan area di `PdfViewFactory`.
- [x] Melakukan refactor pada `resources/views/pdf/agenda_surat_report.blade.php`.
- [x] Melakukan refactor pada `resources/views/pdf/activity.blade.php`.
- [x] Melakukan refactor pada `resources/views/pdf/activity_all_report.blade.php`.

## Validasi

- [x] L1: Unit test `PdfViewFactoryTest` memastikan data header terinjeksi.
- [x] L2: Render test Blade untuk memastikan komponen header tampil benar.

## Risiko

- Risiko 1: Inkonsistensi data jika metadata area tidak lengkap di objek User.
- Risiko 2: Perubahan layout PDF yang sudah presisi karena margin/padding komponen baru.

## Keputusan

- [x] K1: Gunakan `auth()->user()` sebagai sumber utama metadata jika tidak disediakan manual.
- [x] K2: Gunakan `RoleLabelFormatter` untuk standarisasi nama peran di judul.

## Fallback Plan

- Jika otomatisasi bermasalah, revert ke manual injection di Controller masing-masing.

## Output Final

- [x] Standarisasi header PDF berhasil diimplementasikan pada modul pilot.
- [x] `PdfViewFactory` menjadi lebih cerdas dalam menangani metadata laporan.
- [x] Tersedia unit test (`PdfViewFactoryTest`) dan feature test (`PdfHeaderComponentTest`) sebagai guardrail.

