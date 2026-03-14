<!-- markdownlint-disable MD056 MD001 MD038 MD034 -->

# Mapping Autentik Rekap Ibu Hamil TP PKK Kabupaten/Kota (Lampiran 4.19c)

Tanggal baca: 2026-03-11

Sumber autentik:

- Screenshot: `docs/referensi/_screenshots/rakernas-x-autentik/lampiran_4_19c_rekap_ibu_hamil_tp_pkk_kabupaten_kota.png`.

Catatan identitas dokumen:

- Lampiran: `4.19c`.
- Judul utama: `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA PADA TINGKAT TP PKK KABUPATEN/KOTA`.

## Hasil Baca Awal (Text-Layer)

- Text-layer PDF belum tersedia untuk lampiran 4.19c.
- Identitas dokumen dan struktur header dikunci dari verifikasi visual screenshot.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:

- Header numerik terbaca sampai kolom `20`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis agregasi baris: `NAMA KECAMATAN`.

Peta merge header:

- Baris 1:
  - `1` (`NO`) `rowspan=3`
  - `2` (`NAMA KECAMATAN`) `rowspan=3`
  - `3-7` `colspan=5` (`JUMLAH`)
  - `8-11` `colspan=4` (`JUMLAH IBU`)
  - `12-17` `colspan=6` (`JUMLAH BAYI`)
  - `18-19` `colspan=2` (`JML BALITA MENINGGAL`)
  - `20` (`KETERANGAN`) `rowspan=3`
- Baris 2:
  - `3` (`DESA/KEL`) `rowspan=2`
  - `4` (`DUSUN/LINGK`) `rowspan=2`
  - `5` (`RT`) `rowspan=2`
  - `6` (`RW`) `rowspan=2`
  - `7` (`DASA WISMA`) `rowspan=2`
  - `8` (`HAMIL`) `rowspan=2`
  - `9` (`MELAHIRKAN`) `rowspan=2`
  - `10` (`NIFAS`) `rowspan=2`
  - `11` (`MENINGGAL`) `rowspan=2`
  - `12-13` `colspan=2` (`LAHIR`)
  - `14-15` `colspan=2` (`AKTE KELAHIRAN`)
  - `16-17` `colspan=2` (`MENINGGAL`)
  - `18-19` `colspan=2` (tanpa label subgrup tambahan)
- Baris 3:
  - `12: L`, `13: P`
  - `14: ADA`, `15: TIDAK`
  - `16: L`, `17: P`
  - `18: L`, `19: P`

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA KECAMATAN |
| 3 | JUMLAH DESA/KEL |
| 4 | JUMLAH DUSUN/LINGK |
| 5 | JUMLAH RT |
| 6 | JUMLAH RW |
| 7 | JUMLAH DASA WISMA |
| 8 | HAMIL |
| 9 | MELAHIRKAN |
| 10 | NIFAS |
| 11 | MENINGGAL (IBU) |
| 12-13 | LAHIR (L/P) |
| 14-15 | AKTE KELAHIRAN (ADA/TIDAK) |
| 16-17 | MENINGGAL BAYI (L/P) |
| 18-19 | MENINGGAL BALITA (L/P) |
| 20 | KETERANGAN |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **not implemented**.

## Dampak ke Kontrak Domain Saat Ini

- Belum ada modul/report khusus untuk Lampiran 4.19c.
- Perlu mapping repository + report sebelum sinkronisasi implementasi.
