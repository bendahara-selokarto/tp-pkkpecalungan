# Mapping Autentik Catatan TP PKK Kabupaten/Kota (Lampiran 4.17c)

Tanggal baca: 2026-02-22

Sumber autentik:
- Screenshot dokumen resmi Lampiran 4.17c dari user pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran: `4.17c`.
- Judul utama: `CATATAN DATA DAN KEGIATAN WARGA`.
- Subjudul: `TP PKK KABUPATEN/KOTA TAHUN ...`.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `36`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis agregasi baris: `NAMA KECAMATAN`.

Grup header:
- Kolom tunggal non-grup:
  - `1: NO`
  - `2: NAMA KECAMATAN`
  - `3: JML DESA/KEL`
  - `4: JML DUSUN/LINGK`
  - `5: JUML RW`
  - `6: JUML RT`
  - `7: JUML DASAWISMA`
  - `8: JUML KRT`
  - `9: JUML KK`
  - `29: JUMLAH SARANA MCK`
  - `36: KETERANGAN`
- Kolom grup:
  - `10-20: JUMLAH ANGGOTA KELUARGA`
  - `21-24: KRITERIA RUMAH`
  - `25-28: SUMBER AIR KELUARGA`
  - `30-31: MAKANAN POKOK`
  - `32-35: WARGA MENGIKUTI KEGIATAN`

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA KECAMATAN |
| 3 | JML DESA/KEL |
| 4 | JML DUSUN/LINGK |
| 5 | JUML RW |
| 6 | JUML RT |
| 7 | JUML DASAWISMA |
| 8 | JUML KRT |
| 9 | JUML KK |
| 10-11 | TOTAL (L/P) |
| 12-13 | BALITA (L/P) |
| 14 | PUS |
| 15 | WUS |
| 16 | IBU HAMIL |
| 17 | IBU MENYUSUI |
| 18 | LANSIA |
| 19-20 | 3 BUTA (L/P) |
| 21 | SEHAT LAYAK HUNI |
| 22 | TIDAK SEHAT LAYAK HUNI |
| 23 | MEMILIKI TTMP. PEMB SAMPAH |
| 24 | MEMILIKI SPAL DAN PENYERAPAN AIR |
| 25 | PDAM |
| 26 | SUMUR |
| 27 | SUNGAI |
| 28 | DLL |
| 29 | JUMLAH SARANA MCK |
| 30 | BERAS |
| 31 | NON BERAS |
| 32 | UP2K |
| 33 | PEMANFAATAN TANAH PEKARANGAN |
| 34 | INDUSTRI RUMAH TANGGA |
| 35 | KESEHATAN LINGKUNGAN |
| 36 | KETERANGAN |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **implemented dengan catatan deviasi sumber data kecamatan**.
- Status implementasi report: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- Implementasi aktif:
  - view PDF: `resources/views/pdf/catatan_data_kegiatan_warga_tp_pkk_kabupaten_kota_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/tp-pkk-kabupaten-kota/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/tp-pkk-kabupaten-kota/report/pdf`
- Catatan sumber data:
  - Agregasi utama dihitung dari `data_wargas` + `data_warga_anggotas`.
  - `NAMA KECAMATAN` diekstrak dari `alamat`/`dasawisma` (pattern `KECAMATAN`) dengan fallback area user.
  - `JML DESA/KEL`, `JML DUSUN/LINGK`, `JUML RW`, dan `JUML RT` dihitung dari nilai unik hasil ekstraksi per grup kecamatan.
- Catatan deviasi:
  - Lihat `docs/domain/DOMAIN_DEVIATION_LOG.md` untuk deviasi ketersediaan field dedicated kecamatan/desa pada sumber data saat ini.
