# Mapping Autentik Rekap Ibu Hamil PKK RT (Lampiran 4.18b)

Tanggal baca: 2026-02-22

Sumber autentik:
- Screenshot dokumen resmi Lampiran 4.18b dari user pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran: `4.18b`.
- Judul utama: `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA DALAM KELOMPOK PKK RT`.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `15`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis agregasi baris: `NAMA KELOMPOK DASA WISMA`.

Grup header:
- Kolom tunggal non-grup:
  - `1: NO.`
  - `2: NAMA KELOMPOK DASA WISMA`
  - `15: KETERANGAN`
- Kolom grup:
  - `3-6: JUMLAH IBU`
  - `7-12: JUMLAH BAYI`
  - `13-14: JUMLAH BALITA MENINGGAL`

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO. |
| 2 | NAMA KELOMPOK DASA WISMA |
| 3 | HAMIL |
| 4 | MELAHIRKAN |
| 5 | NIFAS |
| 6 | MENINGGAL (IBU) |
| 7-8 | LAHIR (L/P) |
| 9-10 | AKTE KELAHIRAN (ADA/TIDAK ADA) |
| 11-12 | MENINGGAL BAYI (L/P) |
| 13-14 | MENINGGAL BALITA (L/P) |
| 15 | KETERANGAN |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- Implementasi aktif:
  - view PDF: `resources/views/pdf/rekap_ibu_hamil_melahirkan_pkk_rt_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/rekap-ibu-hamil-pkk-rt/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/rekap-ibu-hamil-pkk-rt/report/pdf`
- Catatan sumber data:
  - Agregasi utama dihitung per `NAMA KELOMPOK DASA WISMA` dari dataset rekap 4.18a.
  - Hitungan `JUMLAH IBU`, `JUMLAH BAYI`, dan `JUMLAH BALITA MENINGGAL` bersumber dari proyeksi operasional `data_wargas` + `data_warga_anggotas`.
  - Field `AKTE KELAHIRAN` masih bersifat proyeksi karena belum ada field dedicated akte per bayi pada kontrak input saat ini.
