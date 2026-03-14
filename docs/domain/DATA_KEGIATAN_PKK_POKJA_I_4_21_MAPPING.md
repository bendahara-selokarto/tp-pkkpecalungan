# Mapping Autentik Data Kegiatan PKK Pokja I (Lampiran 4.21)

Tanggal baca: 2026-03-11

Sumber autentik:

- Screenshot: `docs/referensi/_screenshots/rakernas-x-autentik/lampiran_4_21_data_kegiatan_pkk_pokja_i.png`.

Catatan identitas dokumen:

- Lampiran: `4.21`.
- Judul utama: `DATA KEGIATAN PKK`.
- Pokja: `POKJA I`.

## Hasil Baca Awal (Text-Layer)

- Ekstraksi text-layer `Rakernas X.pdf` (2026-03-14) menemukan token `LAMPIRAN 4.21` dan `DATA KEGIATAN PKK`.
- Struktur header tetap dikunci dari verifikasi visual screenshot.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:

- Header numerik terbaca sampai kolom `27`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis agregasi baris: `NAMA WILAYAH (Dusun/Desa/Kel/Kec/Kab/Kota/Prov)`.

Peta merge header:

- Baris 1:
  - `1` (`NO`) `rowspan=3`
  - `2` (`NAMA WILAYAH`) `rowspan=3`
  - `3` (`JML KADER`) `rowspan=3`
  - `4-27` `colspan=24` (`PENGHAYATAN DAN PENGAMALAN PANCASILA DAN GOTONG ROYONG`)
- Baris 2:
  - `4-7` `colspan=4` (`KISAH`)
  - `8-11` `colspan=4` (`KRISAN`)
  - `12-15` `colspan=4` (`KILAS`)
  - `16-19` `colspan=4` (`KTIAT`)
  - `20-23` `colspan=4` (`KISAK`)
  - `24-27` `colspan=4` (`PKBN`)
- Baris 3 (berulang per subgrup):
  - `Kegiatan`, `Vol. Keg`, `Metode`, `Jml. Sasaran`

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA WILAYAH (Dusun/Desa/Kel/Kec/Kab/Kota/Prov) |
| 3 | JML KADER |
| 4 | KISAH - Kegiatan |
| 5 | KISAH - Vol. Keg |
| 6 | KISAH - Metode |
| 7 | KISAH - Jml. Sasaran |
| 8 | KRISAN - Kegiatan |
| 9 | KRISAN - Vol. Keg |
| 10 | KRISAN - Metode |
| 11 | KRISAN - Jml. Sasaran |
| 12 | KILAS - Kegiatan |
| 13 | KILAS - Vol. Keg |
| 14 | KILAS - Metode |
| 15 | KILAS - Jml. Sasaran |
| 16 | KTIAT - Kegiatan |
| 17 | KTIAT - Vol. Keg |
| 18 | KTIAT - Metode |
| 19 | KTIAT - Jml. Sasaran |
| 20 | KISAK - Kegiatan |
| 21 | KISAK - Vol. Keg |
| 22 | KISAK - Metode |
| 23 | KISAK - Jml. Sasaran |
| 24 | PKBN - Kegiatan |
| 25 | PKBN - Vol. Keg |
| 26 | PKBN - Metode |
| 27 | PKBN - Jml. Sasaran |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **not implemented**.

## Dampak ke Kontrak Domain Saat Ini

- Belum ada modul/report khusus untuk Lampiran 4.21.
- Perlu mapping repository + report sebelum sinkronisasi implementasi.
