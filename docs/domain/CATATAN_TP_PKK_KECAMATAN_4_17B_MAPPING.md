# Mapping Autentik Catatan TP PKK Kecamatan (Lampiran 4.17b)

Tanggal baca: 2026-02-22

Sumber autentik:
- Screenshot dokumen resmi Lampiran 4.17b dari user pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran: `4.17b`.
- Judul utama: `CATATAN DATA DAN KEGIATAN WARGA`.
- Subjudul: `TP PKK KECAMATAN TAHUN ...`.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `35`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis agregasi baris: `NAMA DESA/KELURAHAN`.

Grup header:
- Kolom tunggal non-grup:
  - `1: NO`
  - `2: NAMA DESA/KELURAHAN`
  - `3: JML DUSUN/LINGK`
  - `4: JUML RW`
  - `5: JUML RT`
  - `6: JML DASAWISMA`
  - `7: JUML KRT`
  - `8: JUML KK`
  - `28: JUMLAH SARANA MCK`
  - `35: KETERANGAN`
- Kolom grup:
  - `9-19: JUMLAH ANGGOTA KELUARGA`
  - `20-23: KRITERIA RUMAH`
  - `24-27: SUMBER AIR KELUARGA`
  - `29-30: MAKANAN POKOK`
  - `31-34: WARGA MENGIKUTI KEGIATAN`

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA DESA/KELURAHAN |
| 3 | JML DUSUN/LINGK |
| 4 | JUML RW |
| 5 | JUML RT |
| 6 | JML DASAWISMA |
| 7 | JUML KRT |
| 8 | JUML KK |
| 9-10 | TOTAL (L/P) |
| 11-12 | BALITA (L/P) |
| 13 | PUS |
| 14 | WUS |
| 15 | IBU HAMIL |
| 16 | IBU MENYUSUI |
| 17 | LANSIA |
| 18-19 | 3 BUTA (L/P) |
| 20 | SEHAT LAYAK HUNI |
| 21 | TIDAK SEHAT LAYAK HUNI |
| 22 | MEMILIKI TTMP. PEMB SAMPAH |
| 23 | MEMILIKI SPAL DAN PENYERAPAN AIR |
| 24 | PDAM |
| 25 | SUMUR |
| 26 | SUNGAI |
| 27 | DLL |
| 28 | JUMLAH SARANA MCK |
| 29 | BERAS |
| 30 | NON BERAS |
| 31 | UP2K |
| 32 | PEMANFAATAN TANAH PEKARANGAN |
| 33 | INDUSTRI RUMAH TANGGA |
| 34 | KESEHATAN LINGKUNGAN |
| 35 | KETERANGAN |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **implemented dengan catatan deviasi sumber data desa/kelurahan**.
- Status implementasi report: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- Implementasi aktif:
  - view PDF: `resources/views/pdf/catatan_data_kegiatan_warga_tp_pkk_kecamatan_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/tp-pkk-kecamatan/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/tp-pkk-kecamatan/report/pdf`
- Catatan sumber data:
  - Agregasi utama dihitung dari `data_wargas` + `data_warga_anggotas`.
  - `NAMA DESA/KELURAHAN` diekstrak dari `alamat`/`dasawisma` (pattern `DESA|KELURAHAN|KEL`).
  - `JML DUSUN/LINGK`, `JUML RW`, dan `JUML RT` dihitung dari nilai unik hasil ekstraksi per grup desa/kelurahan.
- Catatan deviasi:
  - Lihat `docs/domain/DOMAIN_DEVIATION_LOG.md` untuk deviasi ketersediaan field dedicated desa/kelurahan pada sumber data saat ini.
