# Mapping Autentik Catatan TP PKK Provinsi (Lampiran 4.17d)

Tanggal baca: 2026-02-22

Sumber autentik:
- Screenshot dokumen resmi Lampiran 4.17d dari user pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran: `4.17d`.
- Judul utama: `CATATAN DATA DAN KEGIATAN WARGA`.
- Subjudul: `TP PKK PROVINSI TAHUN ...`.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `37`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis agregasi baris: `NAMA KAB/KOTA`.

Grup header:
- Kolom tunggal non-grup:
  - `1: NO`
  - `2: NAMA KAB/KOTA`
  - `3: JML KEC`
  - `4: JUML DESA/KEL`
  - `5: JUML DUSUN/LINGK`
  - `6: JUML RW`
  - `7: JUML RT`
  - `8: JUML DASAWISMA`
  - `9: JUML KRT`
  - `10: JUML KK`
  - `30: JUMLAH SARANA MCK`
  - `37: KETERANGAN`
- Kolom grup:
  - `11-21: JUMLAH ANGGOTA KELUARGA`
  - `22-25: KRITERIA RUMAH`
  - `26-29: SUMBER AIR KELUARGA`
  - `31-32: MAKANAN POKOK`
  - `33-36: WARGA MENGIKUTI KEGIATAN`

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA KAB/KOTA |
| 3 | JML KEC |
| 4 | JUML DESA/KEL |
| 5 | JUML DUSUN/LINGK |
| 6 | JUML RW |
| 7 | JUML RT |
| 8 | JUML DASAWISMA |
| 9 | JUML KRT |
| 10 | JUML KK |
| 11-12 | TOTAL (L/P) |
| 13-14 | BALITA (L/P) |
| 15 | PUS |
| 16 | WUS |
| 17 | IBU HAMIL |
| 18 | IBU MENYUSUI |
| 19 | LANSIA |
| 20-21 | 3 BUTA (L/P) |
| 22 | SEHAT LAYAK HUNI |
| 23 | TIDAK SEHAT LAYAK HUNI |
| 24 | MEMILIKI TTMP. PEMB SAMPAH |
| 25 | MEMILIKI SPAL DAN PENYERAPAN AIR |
| 26 | PDAM |
| 27 | SUMUR |
| 28 | SUNGAI |
| 29 | DLL |
| 30 | JUMLAH SARANA MCK |
| 31 | BERAS |
| 32 | NON BERAS |
| 33 | UP2K |
| 34 | PEMANFAATAN TANAH PEKARANGAN |
| 35 | INDUSTRI RUMAH TANGGA |
| 36 | KESEHATAN LINGKUNGAN |
| 37 | KETERANGAN |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **implemented dengan catatan deviasi sumber data kabupaten/kota**.
- Status implementasi report: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- Implementasi aktif:
  - view PDF: `resources/views/pdf/catatan_data_kegiatan_warga_tp_pkk_provinsi_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/tp-pkk-provinsi/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/tp-pkk-provinsi/report/pdf`
- Catatan sumber data:
  - Agregasi utama dihitung dari `data_wargas` + `data_warga_anggotas`.
  - `NAMA KAB/KOTA` diekstrak dari `alamat`/`dasawisma` (pattern `KAB|KABUPATEN|KOTA`).
  - `JML KEC`, `JML DESA/KEL`, `JML DUSUN/LINGK`, `JUML RW`, dan `JUML RT` dihitung dari nilai unik hasil ekstraksi per grup kabupaten/kota.
- Catatan deviasi:
  - Lihat `docs/domain/DOMAIN_DEVIATION_LOG.md` untuk deviasi ketersediaan field dedicated provinsi/kabupaten pada sumber data saat ini.
