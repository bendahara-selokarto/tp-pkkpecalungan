# Mapping Autentik Rekap PKK RT (Lampiran 4.16b)

Tanggal baca: 2026-02-22

Sumber autentik:
- `d:\pedoman\181.pdf`

Judul dokumen:
- `REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RT`

## Hasil Baca Struktur

Metadata form:
- `DASA WISMA`
- `RT / RW`
- `DESA/KELURAHAN`
- `TAHUN`

Struktur tabel:
- Header numerik terlihat sampai kolom `30`.
- Header bertingkat dengan grup utama:
  - `JUMLAH ANGGOTA KELUARGA` (kolom `5-14`)
  - `JUMLAH RUMAH` (kolom `15-20`)
  - `SUMBER AIR` (kolom `21-23`)
  - `MAKANAN` (kolom `24-25`)
  - `WARGA MENGIKUTI KEGIATAN` (kolom `26-29`)
- Kolom tunggal non-grup:
  - `1: NO`
  - `2: NAMA DASAWISMA`
  - `3: JML KRT`
  - `4: JML KK`
  - `30: KET`

## Peta Header Hingga Merge Cell

Model merge header:
- Baris grup (atas): kolom grup memakai `colspan`.
- Baris sub-header (bawah): kolom detail per indikator.
- Kolom non-grup (`1-4`, `30`) berperan sebagai kolom tunggal (`rowspan`) pada header.

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA DASAWISMA |
| 3 | JML KRT |
| 4 | JML KK |
| 5-6 | TOTAL (L/P) |
| 7-8 | BALITA (L/P) |
| 9 | PUS |
| 10 | WUS |
| 11 | IBU HAMIL |
| 12 | IBU MENYUSUI |
| 13 | LANSIA |
| 14 | 3 BUTA |
| 15 | BERKEBUTUHAN KHUSUS |
| 16 | SEHAT LAYAK HUNI |
| 17 | TIDAK SEHAT LAYAK HUNI |
| 18 | MEMILIKI TTMP/PEMBUANGAN SAMPAH |
| 19 | MEMILIKI SPAL/PEMBUANGAN AIR |
| 20 | MEMILIKI SARANA MCK DAN SEPTIC TANK |
| 21 | PDAM |
| 22 | SUMUR |
| 23 | DLL |
| 24 | BERAS |
| 25 | NON BERAS |
| 26 | UP2K |
| 27 | PEMANFAATAN TANAH PEKARANGAN |
| 28 | INDUSTRI RUMAH TANGGA |
| 29 | KESEHATAN LINGKUNGAN |
| 30 | KET |

## Temuan Akurasi Parser Node.js

- Ekstraksi text-layer (`pdfjs-dist`) membaca identitas dokumen:
  - `LAMPIRAN 4.16b`
  - `REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RT`
  - metadata form (Dasa Wisma, RT/RW, Desa/Kelurahan, Tahun)
- Struktur tabel dan detail sub-header tidak terbaca andal dari text-layer.
- Kesimpulan: parser Node.js dipakai untuk identitas; struktur header dikunci melalui verifikasi visual dokumen autentik.

## Dampak ke Kontrak Domain Saat Ini

- Dokumen ini adalah rekap lintas data keluarga + kegiatan warga di level PKK RT.
- Modul aktif yang berkaitan:
  - `data-warga`
  - `data-kegiatan-warga`
  - `catatan-keluarga`
- Implementasi aktif:
  - view PDF: `resources/views/pdf/rekap_catatan_data_kegiatan_warga_pkk_rt_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/rekap-pkk-rt/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/rekap-pkk-rt/report/pdf`
- Status implementasi: `implemented (report-only via catatan-keluarga)`.
- Catatan sumber data:
  - Agregasi jumlah anggota keluarga dihitung dari `data_warga_anggotas` dan digrup per `dasawisma`.
  - Indikator kegiatan 26-29 saat ini mengikuti indikator level area (belum per-keluarga/per-dasawisma detail) karena kontrak input granular belum tersedia.
