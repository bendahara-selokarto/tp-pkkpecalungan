# TODO PDF26F3 Standarisasi Metadata Cetak (Fixed Footer)

Tanggal: 2026-06-01  
Status: `completed`
Related ADR: `-`

## Konteks

- Pengguna ingin informasi "Dicetak oleh" diletakkan secara presisi beberapa mm dari batas bawah kertas secara seragam di seluruh laporan PDF.
- Nama file `_report_footer.blade.php` saat ini sudah digunakan untuk blok tanda tangan, sehingga diperlukan partial baru untuk metadata cetak guna memisahkan tanggung jawab.
- Penggunaan `position: fixed` pada DomPDF akan memastikan metadata muncul di posisi yang sama pada setiap halaman.

## Kontrak Concern (Lock)

- Domain: Output PDF Reports
- Role/scope target: Semua Role
- Boundary data: `resources/views/pdf/`, `app/Support/Pdf/PdfViewFactory.php`.
- Acceptance criteria:
  - Tersedia partial baru `resources/views/pdf/partials/_report_metadata.blade.php`.
  - Metadata menggunakan `position: fixed; bottom: -10mm;` (atau nilai presisi lainnya) untuk menempel di batas bawah.
  - Data metadata (`footerUserName`, `footerDate`, dll) diinjeksi secara otomatis oleh `PdfViewFactory`.
  - Seluruh file template PDF (70+) beralih dari blok manual ke partial baru ini.
- Dampak keputusan arsitektur: `tidak` (hanya refactor layer presentasi).

## Target Hasil

- [x] Partial `resources/views/pdf/partials/_report_metadata.blade.php` siap digunakan.
- [x] `PdfViewFactory::appendStandardMetadata` diperkuat untuk menjamin ketersediaan data.
- [x] Mass update pada 70+ file `.blade.php` selesai dilakukan.
- [x] CSS metadata redundan dibersihkan dari template individual.

## Langkah Eksekusi

- [x] **E-1: Pembangunan Fondasi**
    - [x] Buat `resources/views/pdf/partials/_report_metadata.blade.php`.
    - [x] Update `PdfViewFactory` untuk menyediakan `$footerUserName`, `$footerDate`, `$areaName`, dan `$budgetYearLabel`.
- [x] **E-2: Migrasi Massal**
    - [x] Gunakan script/subagent untuk menghapus blok "Dicetak oleh" lama dan menambahkan `@include('pdf.partials._report_metadata')`.
- [x] **E-3: Finalisasi**
    - [x] Audit visual pada laporan dengan orientasi berbeda (Portrait vs Landscape).
    - [x] Jalankan test suite PDF.

## Validasi

- [x] L1: Verifikasi posisi metadata pada PDF yang dihasilkan (fix di batas bawah).
- [x] L2: `php artisan test --filter=header_kolom_pdf`.

## Risiko

- Risiko 1: Overlapping dengan konten tabel jika margin bawah halaman terlalu kecil.
- Risiko 2: Inkonsistensi variabel pada laporan yang tidak melalui `PdfViewFactory` (misal: panggil `Pdf::loadView` langsung).

## Keputusan

- [x] K1: Metadata dipisahkan ke file `_report_metadata.blade.php`.
- [x] K2: Menggunakan `position: fixed` agar muncul di setiap halaman.

## Fallback Plan

- Revert parsial atau total menggunakan Git jika terjadi kerusakan layout masif.
