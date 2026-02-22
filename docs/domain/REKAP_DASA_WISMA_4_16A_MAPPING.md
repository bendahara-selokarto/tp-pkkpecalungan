# Mapping Autentik Rekap Dasa Wisma (Lampiran 4.16a)

Tanggal baca: 2026-02-22

Sumber autentik:
- `d:\pedoman\179.pdf`

Judul dokumen:
- `REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK DASA WISMA`

## Hasil Baca Struktur

Metadata form:
- `DASA WISMA`
- `RT / RW`
- `DESA/KELURAHAN`
- `TAHUN`

Struktur tabel:
- Header numerik terlihat sampai kolom `29`.
- Header bertingkat dengan grup utama:
  - `JUMLAH ANGGOTA KELUARGA` (kolom `4-13`)
  - `KRITERIA RUMAH` (kolom `14-19`)
  - `SUMBER AIR KELUARGA` (kolom `20-22`)
  - `MAKANAN` (kolom `23-24`)
  - `WARGA MENGIKUTI KEGIATAN` (kolom `25-28`)
- Kolom tunggal non-grup:
  - `1: NO`
  - `2: NAMA KEPALA RUMAH TANGGA`
  - `3: JML KK`
  - `29: KET`

## Pemetaan Header (Berdasarkan Pembacaan Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA KEPALA RUMAH TANGGA |
| 3 | JML KK |
| 4-5 | TOTAL (L/P) |
| 6-7 | BALITA (L/P) |
| 8 | PUS |
| 9 | WUS |
| 10 | IBU HAMIL |
| 11 | IBU MENYUSUI |
| 12 | LANSIA |
| 13 | 3 BUTA |
| 14 | BERKEBUTUHAN KHUSUS |
| 15 | SEHAT LAYAK HUNI |
| 16 | TIDAK SEHAT LAYAK HUNI |
| 17 | MEMILIKI TEMPAT PEMBUANGAN SAMPAH |
| 18 | MEMILIKI SPAL/PEMBUANGAN AIR |
| 19 | MEMILIKI SARANA MCK DAN SEPTIC TANK |
| 20 | PDAM |
| 21 | SUMUR |
| 22 | DLL |
| 23 | BERAS |
| 24 | NON BERAS |
| 25 | UP2K |
| 26 | PEMANFAATAN TANAH PEKARANGAN |
| 27 | INDUSTRI RUMAH TANGGA |
| 28 | KESEHATAN LINGKUNGAN |
| 29 | KET |

Catatan:
- Label sub-header kolom `14`, `18`, dan `19` telah difinalkan dengan verifikasi lintas-lampiran 4.16a/4.16b yang memiliki struktur indikator sepadan pada grup kriteria rumah.

## Temuan Akurasi Parser Node.js

- Ekstraksi text-layer (`pdfjs-dist`) hanya membaca token judul:
  - `LAMPIRAN 4.16a`
  - `REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK DASA WISMA`
- Struktur tabel dan sub-header tidak terbaca dari text-layer.
- Kesimpulan: untuk lampiran ini, parser Node.js dipakai untuk identitas dokumen; struktur header dikunci lewat verifikasi visual dokumen autentik.

## Dampak ke Kontrak Domain Saat Ini

- Dokumen ini merepresentasikan rekap lintas data keluarga + kegiatan warga.
- Modul aktif yang berkaitan:
  - `data-warga`
  - `data-kegiatan-warga`
  - `catatan-keluarga`
- Implementasi aktif:
  - view PDF: `resources/views/pdf/rekap_catatan_data_kegiatan_warga_dasa_wisma_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/rekap-dasa-wisma/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/rekap-dasa-wisma/report/pdf`
- Status implementasi: `implemented (report-only via catatan-keluarga)`.
- Catatan sumber data:
  - Kolom berbasis anggota keluarga dihitung dari `data_warga_anggotas`.
  - Indikator kegiatan 25-28 saat ini mengikuti indikator level area (belum per-keluarga) karena kontrak input per-keluarga belum tersedia.
