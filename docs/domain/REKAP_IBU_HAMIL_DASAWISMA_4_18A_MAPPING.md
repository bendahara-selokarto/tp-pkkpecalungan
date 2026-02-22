# Mapping Autentik Rekap Ibu Hamil Dasawisma (Lampiran 4.18a)

Tanggal baca: 2026-02-22

Sumber autentik:
- Screenshot dokumen resmi Lampiran 4.18a dari user pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran: `4.18a`.
- Judul utama: `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA DALAM KELOMPOK DASAWISMA`.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `17`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Grup header utama: `CATATAN KELAHIRAN` dan `CATATAN KEMATIAN`.

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO. |
| 2 | NAMA IBU |
| 3 | NAMA SUAMI |
| 4 | STATUS (HAMIL/MELAHIRKAN/NIFAS) |
| 5 | NAMA BAYI |
| 6-7 | JENIS KELAMIN (L/P) - CATATAN KELAHIRAN |
| 8 | TGL. LAHIR |
| 9-10 | AKTE KELAHIRAN (ADA/TIDAK ADA) |
| 11 | NAMA IBU/BAYI/BALITA - CATATAN KEMATIAN |
| 12 | STATUS (IBU/BALITA/BAYI) |
| 13-14 | JENIS KELAMIN (L/P) - CATATAN KEMATIAN |
| 15 | TGL. MENINGGAL |
| 16 | SEBAB MENINGGAL |
| 17 | KETERANGAN |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- Implementasi aktif:
  - view PDF: `resources/views/pdf/rekap_ibu_hamil_melahirkan_dasawisma_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/rekap-ibu-hamil-dasawisma/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/rekap-ibu-hamil-dasawisma/report/pdf`
- Catatan sumber data:
  - Basis data bersumber dari `data_wargas` + `data_warga_anggotas`.
  - Status ibu dan kematian diturunkan dari atribut `keterangan` (household/anggota) menggunakan keyword matching.
  - Indikator `AKTE KELAHIRAN` saat ini masih proyeksi operasional (belum ada field dedicated akte per bayi pada kontrak input).
