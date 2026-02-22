# Mapping Autentik Rekap Ibu Hamil TP PKK Kecamatan (Lampiran 4.19b)

Tanggal baca: 2026-02-22

Sumber autentik:
- Dokumen PDF resmi: `docs/referensi/207.pdf`.
- Screenshot header tabel Lampiran 4.19b dari user pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran: `4.19b`.
- Judul utama: `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA PADA TINGKAT TP PKK KECAMATAN`.

## Hasil Baca Awal (Text-Layer)

Ekstraksi text-layer dari `207.pdf` mendeteksi token identitas:
- fragmen judul maternal (`IBU HAMIL`, `MELAHIRKAN`, `NIFAS`, `MENINGGAL`, `KELAHIRAN BAYI`, `KEMATIAN BALITA`);
- token metadata wilayah (`Kecamatan`, `Kab/Kota`, `Provinsi`, `Bulan`, `Tahun`).

Temuan parser:
- text-layer terfragmentasi akibat kerning font dan tidak cukup untuk merekonstruksi struktur header tabel 19 kolom secara utuh.
- keputusan: parser dipakai untuk identitas dokumen, struktur header final dikunci dari verifikasi visual.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `19`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis agregasi baris: `NAMA DESA/KEL`.

Peta merge header:
- Baris 1:
  - `1` (`NO`) `rowspan=3`
  - `2` (`NAMA DESA/KEL`) `rowspan=3`
  - `3-6` `colspan=4` (`JUMLAH`)
  - `7-10` `colspan=4` (`JUMLAH IBU`)
  - `11-16` `colspan=6` (`JUMLAH BAYI`)
  - `17-18` `colspan=2` (`JML BALITA MENINGGAL`)
  - `19` (`KETERANGAN`) `rowspan=3`
- Baris 2:
  - `3` (`DUSUN/LINGK`) `rowspan=2`
  - `4` (`RW`) `rowspan=2`
  - `5` (`RT`) `rowspan=2`
  - `6` (`DASA WISMA`) `rowspan=2`
  - `7` (`HAMIL`) `rowspan=2`
  - `8` (`MELAHIRKAN`) `rowspan=2`
  - `9` (`NIFAS`) `rowspan=2`
  - `10` (`MENINGGAL`) `rowspan=2`
  - `11-12` `colspan=2` (`LAHIR`)
  - `13-14` `colspan=2` (`AKTE KELAHIRAN`)
  - `15-16` `colspan=2` (`MENINGGAL`)
  - `17-18` `colspan=2` (tanpa label subgrup tambahan)
- Baris 3:
  - `11: L`, `12: P`
  - `13: ADA`, `14: TIDAK`
  - `15: L`, `16: P`
  - `17: L`, `18: P`

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA DESA/KEL |
| 3 | JUMLAH DUSUN/LINGK |
| 4 | JUMLAH RW |
| 5 | JUMLAH RT |
| 6 | JUMLAH DASA WISMA |
| 7 | HAMIL |
| 8 | MELAHIRKAN |
| 9 | NIFAS |
| 10 | MENINGGAL (IBU) |
| 11-12 | LAHIR (L/P) |
| 13-14 | AKTE KELAHIRAN (ADA/TIDAK) |
| 15-16 | MENINGGAL BAYI (L/P) |
| 17-18 | MENINGGAL BALITA (L/P) |
| 19 | KETERANGAN |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- Implementasi aktif:
  - view PDF: `resources/views/pdf/rekap_ibu_hamil_melahirkan_tp_pkk_kecamatan_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/rekap-ibu-hamil-tp-pkk-kecamatan/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/rekap-ibu-hamil-tp-pkk-kecamatan/report/pdf`
- Catatan sumber data:
  - Agregasi 4.19b dibangun dari agregasi tingkat desa/kelurahan (turunan 4.18d).
  - `kolom 3` dihitung dari jumlah dusun/lingkungan unik per desa/kelurahan.
  - `kolom 4-18` dihitung dari penjumlahan indikator maternal/kelahiran/kematian pada agregasi sumber.
