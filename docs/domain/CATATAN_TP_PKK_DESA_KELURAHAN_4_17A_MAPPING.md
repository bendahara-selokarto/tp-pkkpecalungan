# Mapping Autentik Catatan TP PKK Desa/Kelurahan (Lampiran 4.17a)

Tanggal baca: 2026-02-22

Sumber autentik:
- Screenshot dokumen resmi Lampiran 4.17a dari user pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran: `4.17a`.
- Judul utama: `CATATAN DATA DAN KEGIATAN WARGA TP PKK`.
- Subjudul: `DESA/KELURAHAN TAHUN ...`.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `33`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis agregasi baris: `NAMA DUSUN/LINGKUNGAN`.

Grup header:
- Kolom tunggal non-grup:
  - `1: NO`
  - `2: NAMA DUSUN/LINGKUNGAN`
  - `3: JML RW`
  - `4: JML RT`
  - `5: JML DASAWISMA`
  - `6: JML KRT`
  - `7: JML KK`
  - `26: JUMLAH SARANA MCK`
  - `33: KETERANGAN`
- Kolom grup:
  - `8-17: JUMLAH ANGGOTA KELUARGA`
  - `18-21: KRITERIA RUMAH`
  - `22-25: SUMBER AIR KELUARGA`
  - `27-28: MAKANAN POKOK`
  - `29-32: WARGA MENGIKUTI KEGIATAN`

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA DUSUN/LINGKUNGAN |
| 3 | JML RW |
| 4 | JML RT |
| 5 | JML DASAWISMA |
| 6 | JML KRT |
| 7 | JML KK |
| 8-9 | TOTAL (L/P) |
| 10-11 | BALITA (L/P) |
| 12 | PUS |
| 13 | WUS |
| 14 | IBU HAMIL |
| 15 | IBU MENYUSUI |
| 16 | LANSIA |
| 17 | 3 BUTA |
| 18 | SEHAT LAYAK HUNI |
| 19 | TIDAK SEHAT LAYAK HUNI |
| 20 | MEMILIKI TTMP. PEMB SAMPAH |
| 21 | MEMILIKI SPAL DAN PENYERAPAN AIR |
| 22 | PDAM |
| 23 | SUMUR |
| 24 | SUNGAI |
| 25 | DLL |
| 26 | JUMLAH SARANA MCK |
| 27 | BERAS |
| 28 | NON BERAS |
| 29 | UP2K |
| 30 | PEMANFAATAN TANAH PEKARANGAN |
| 31 | INDUSTRI RUMAH TANGGA |
| 32 | KESEHATAN LINGKUNGAN |
| 33 | KETERANGAN |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **implemented dengan catatan deviasi sumber data dusun/lingkungan**.
- Status implementasi report: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- Implementasi aktif:
  - view PDF: `resources/views/pdf/catatan_data_kegiatan_warga_tp_pkk_desa_kelurahan_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/tp-pkk-desa-kelurahan/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/tp-pkk-desa-kelurahan/report/pdf`
- Catatan sumber data:
  - Agregasi utama dihitung dari `data_wargas` + `data_warga_anggotas`.
  - `NAMA DUSUN/LINGKUNGAN` diekstrak dari `alamat` (fallback `dasawisma`).
  - `JML RW` dan `JML RT` dihitung dari nilai unik hasil ekstraksi pada grup dusun/lingkungan.
- Catatan deviasi:
  - Lihat `docs/domain/DOMAIN_DEVIATION_LOG.md` untuk deviasi ketersediaan field dusun/lingkungan dedicated pada model sumber data saat ini.
