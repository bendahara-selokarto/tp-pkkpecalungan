# TODO PDF26R1 Refactoring Laporan PDF sesuai Standar DomPDF

Tanggal: 2026-06-01  
Status: `done`
Related ADR: `-`

## Konteks

- Ditemukan inkonsistensi lebar kolom pada beberapa laporan PDF karena penggunaan `table-layout: fixed` dan definisi `width` langsung pada `<th>`.
- Standar baru telah ditetapkan di `docs/pdf/PDF_RENDERING_STANDARDS_DOMPDF.md`.
- Diperlukan refactoring menyeluruh pada seluruh template Blade PDF untuk memastikan kepatuhan terhadap standar rendering DomPDF agar output visual stabil dan presisi.

## Kontrak Concern (Lock)

- Domain: Output PDF Reports
- Role/scope target: Semua Role (Desa & Kecamatan)
- Boundary data: Seluruh file `.blade.php` di dalam `resources/views/pdf/`.
- Acceptance criteria:
  - Tidak ada lagi penggunaan `table-layout: fixed` di tabel laporan.
  - Pengaturan lebar kolom menggunakan `<colgroup>`.
  - Tidak ada atribut `width` pada elemen `<th>` yang memiliki `colspan`.
  - Laporan lulus verifikasi visual pada zoom 100% dan 400%.
- Dampak keputusan arsitektur: `tidak` (hanya perubahan pada layer presentasi/template).

## Target Hasil

- [x] Seluruh template PDF di `resources/views/pdf/` menggunakan `<colgroup>` untuk lebar kolom.
- [x] Konsistensi visual tercapai lintas modul.
- [x] Laporan lulus `PDF Compliance Checklist` (`docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`).

## Langkah Eksekusi

- [x] Identifikasi daftar file template PDF yang belum patuh standar.
- [x] Refactor bertahap per modul:
    - [x] **Modul Non-Compliant (Prioritas Tinggi):**
        - [x] `resources/views/pdf/posyandu_report.blade.php`
        - [x] `resources/views/pdf/prestasi_lomba_report.blade.php`
        - [x] `resources/views/pdf/rekap_catatan_data_kegiatan_warga_dasa_wisma_report.blade.php`
        - [x] `resources/views/pdf/rekap_catatan_data_kegiatan_warga_pkk_rt_report.blade.php`
        - [x] `resources/views/pdf/rekap_catatan_data_kegiatan_warga_rw_report.blade.php`
        - [x] `resources/views/pdf/rekap_ibu_hamil_melahirkan_dasawisma_report.blade.php`
        - [x] `resources/views/pdf/rekap_ibu_hamil_melahirkan_dusun_lingkungan_report.blade.php`
        - [x] `resources/views/pdf/rekap_ibu_hamil_melahirkan_pkk_rt_report.blade.php`
        - [x] `resources/views/pdf/rekap_ibu_hamil_melahirkan_pkk_rw_report.blade.php`
        - [x] `resources/views/pdf/rekap_ibu_hamil_melahirkan_tp_pkk_kecamatan_report.blade.php`
        - [x] `resources/views/pdf/simulasi_penyuluhan_report.blade.php`
        - [x] `resources/views/pdf/taman_bacaan_report.blade.php`
        - [x] `resources/views/pdf/warung_pkk_report.blade.php`
    - [x] Modul Sekretariat (Sudah memiliki colgroup, cek kepatuhan `table-layout: auto`).
    - [x] Modul Pokja I - IV (Sudah memiliki colgroup, cek kepatuhan `table-layout: auto`).
- [x] Sinkronisasi `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md` dengan hasil verifikasi terbaru.

## Validasi

- [x] L1: Verifikasi visual render PDF untuk setiap template yang di-refactor.
- [x] L2: `php artisan test --filter=header_kolom_pdf` (Status: PASS).
- [x] L3: `php artisan test --filter=PdfBaselineFixtureComplianceTest` (Relevant files PASS).

## Risiko

- Risiko 1: Pergeseran layout pada laporan yang sudah memiliki presisi tinggi. (Dimitigasi dengan colgroup px precision).
- Risiko 2: Selisih lebar kolom yang terlalu kecil menyebabkan teks terpotong jika data panjang. (Dimitigasi dengan table-layout auto).

## Keputusan

- [x] K1: Gunakan satuan `px` atau `%` yang konsisten dalam `<col>` di dalam `<colgroup>`.
- [x] K2: Utamakan keterbacaan teks utama (nama/uraian) dengan memberikan porsi lebar lebih besar.

## Fallback Plan

- Revert ke versi sebelumnya menggunakan Git jika terjadi kerusakan layout masif yang tidak bisa diperbaiki cepat.

## Output Final

- [x] Seluruh template PDF yang menggunakan tabel telah diperbarui untuk menghapus `table-layout: fixed` dan menambahkan/memperbaiki `<colgroup>`.
- [x] Daftar file terdampak mencakup 13 file prioritas tinggi dan verifikasi pada modul Sekretariat & Pokja.
- [x] Hasil validasi `header_kolom_pdf` menunjukkan status hijau (37 tests passed).
