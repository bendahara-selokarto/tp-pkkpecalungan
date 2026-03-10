# Mapping Autentik Data Kegiatan PKK Pokja II (Lampiran 4.22)

Tanggal baca: 2026-03-11

Sumber autentik:

- Screenshot: `docs/referensi/_screenshots/rakernas-x-autentik/lampiran_4_22_data_kegiatan_pkk_pokja_ii.png`.

Catatan identitas dokumen:

- Lampiran: `4.22`.
- Judul utama: `DATA KEGIATAN PKK`.
- Pokja: `POKJA II`.

## Hasil Baca Awal (Text-Layer)

- Text-layer PDF belum tersedia untuk lampiran 4.22.
- Identitas dokumen dan struktur header dikunci dari verifikasi visual screenshot.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:

- Header numerik terbaca sampai kolom `36`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis agregasi baris: `NAMA WILAYAH (...)`.

Struktur merge (ringkas):

- `NO` dan `NAMA WILAYAH (...)` memakai `rowspan=4`.
- Grup `PAKET A/B/C`, `KF`, `PAUD SEJENIS`, `JUMLAH TAMAN BACA PERPUSTAKAAN` berada pada blok awal pendidikan.
- Grup `PENDIDIKAN KETERAMPILAN` terdiri dari `BKB`, `TUTOR`, `KADER KHUSUS`.
- Grup `JUMLAH KADER YANG SUDAH DILATIH` berisi `LP3`, `TPK 3 PKK`, `DAMAS PKK`.
- Grup `PENGEMBANGAN KEAHLIAN BERKOPERASI` memiliki layer `PRA KOPERASI/USAHA BERSAMA/UP2K` dan sublayer `PEMULA/MADYA/UTAMA/MANDIRI` dengan leaf `JML. KLP` + `PESERTA`.
- `KOPERASI BERBADAN HUKUM` berisi `JML. KLP` dan `JML. ANGGOTA`.

Peta merge header (detail):

- Baris 1:
  - `1` (`NO`) `rowspan=4`
  - `2` (`NAMA WILAYAH (Dusun/Lingk/Desa/Kel/Kec/Kab/Kota/Pro)`) `rowspan=4`
  - `3` (`JML. WARGA YANG MASIH 3 (TIGA) BUTA`) `rowspan=4`
  - `4-5` `colspan=2` (`PAKET A`)
  - `6-7` `colspan=2` (`PAKET B`)
  - `8-9` `colspan=2` (`PAKET C`)
  - `10-11` `colspan=2` (`KF`)
  - `12` (`PAUD SEJENIS`) `rowspan=4`
  - `13` (`JUMLAH TAMAN BACA PERPUSTAKAAN`) `rowspan=4`
  - `14-22` `colspan=9` (`PENDIDIKAN KETERAMPILAN`)
  - `23-25` `colspan=3` (`JUMLAH KADER YANG SUDAH DILATIH`)
  - `26-33` `colspan=8` (`PENGEMBANGAN KEAHLIAN BERKOPERASI`)
  - `34-35` `colspan=2` (`KOPERASI BERBADAN HUKUM`)
  - `36` (`KET.`) `rowspan=4`
- Baris 2:
  - `4` (`JML. KLP BELAJAR`) `rowspan=3`
  - `5` (`WARGA BELAJAR`) `rowspan=3`
  - `6` (`JML. KLP BELAJAR`) `rowspan=3`
  - `7` (`WARGA BELAJAR`) `rowspan=3`
  - `8` (`JML. KLP BELAJAR`) `rowspan=3`
  - `9` (`WARGA BELAJAR`) `rowspan=3`
  - `10` (`JML. KLP BELAJAR`) `rowspan=3`
  - `11` (`WARGA BELAJAR`) `rowspan=3`
  - `14-16` `colspan=3` (`BKB`)
  - `17` (`TUTOR`)
  - `18-22` `colspan=5` (`KADER KHUSUS`)
  - `23` (`LP3`) `rowspan=3`
  - `24` (`TPK 3 PKK`) `rowspan=3`
  - `25` (`DAMAS PKK`) `rowspan=3`
  - `26-33` `colspan=8` (`PRA KOPERASI/USAHA BERSAMA/UP2K`)
  - `34` (`JML. KLP`) `rowspan=3`
  - `35` (`JML. ANGGOTA`) `rowspan=3`
- Baris 3:
  - `14` (`JML. KLP`) `rowspan=2`
  - `15` (`JML. IBU PESERTA`) `rowspan=2`
  - `16` (`JML. APE (SET)`) `rowspan=2`
  - `17` (`JML. KLP SIMULASI`) `rowspan=2`
  - `18` (`KF`) `rowspan=2`
  - `19` (`PAUD SEJENIS`) `rowspan=2`
  - `20` (`BKB`) `rowspan=2`
  - `21` (`KOPERASI`) `rowspan=2`
  - `22` (`KETERAMPILAN`) `rowspan=2`
  - `26-27` `colspan=2` (`PEMULA`)
  - `28-29` `colspan=2` (`MADYA`)
  - `30-31` `colspan=2` (`UTAMA`)
  - `32-33` `colspan=2` (`MANDIRI`)
- Baris 4:
  - `26` (`JML. KLP`)
  - `27` (`PESERTA`)
  - `28` (`JML. KLP`)
  - `29` (`PESERTA`)
  - `30` (`JML. KLP`)
  - `31` (`PESERTA`)
  - `32` (`JML. KLP`)
  - `33` (`PESERTA`)

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA WILAYAH (Dusun/Lingk/Desa/Kel/Kec/Kab/Kota/Pro) |
| 3 | JML. WARGA YANG MASIH 3 (TIGA) BUTA |
| 4 | PAKET A - JML. KLP BELAJAR |
| 5 | PAKET A - WARGA BELAJAR |
| 6 | PAKET B - JML. KLP BELAJAR |
| 7 | PAKET B - WARGA BELAJAR |
| 8 | PAKET C - JML. KLP BELAJAR |
| 9 | PAKET C - WARGA BELAJAR |
| 10 | KF - JML. KLP BELAJAR |
| 11 | KF - WARGA BELAJAR |
| 12 | PAUD SEJENIS |
| 13 | JUMLAH TAMAN BACA PERPUSTAKAAN |
| 14 | BKB - JML. KLP |
| 15 | BKB - JML. IBU PESERTA |
| 16 | BKB - JML. APE (SET) |
| 17 | TUTOR - JML. KLP SIMULASI |
| 18 | KADER KHUSUS - KF |
| 19 | KADER KHUSUS - PAUD SEJENIS |
| 20 | KADER KHUSUS - BKB |
| 21 | KADER KHUSUS - KOPERASI |
| 22 | KADER KHUSUS - KETERAMPILAN |
| 23 | JUMLAH KADER YANG SUDAH DILATIH - LP3 |
| 24 | JUMLAH KADER YANG SUDAH DILATIH - TPK 3 PKK |
| 25 | JUMLAH KADER YANG SUDAH DILATIH - DAMAS PKK |
| 26 | PENGEMBANGAN KEAHLIAN BERKOPERASI (PRA KOPERASI/USAHA BERSAMA/UP2K) - PEMULA - JML. KLP |
| 27 | PENGEMBANGAN KEAHLIAN BERKOPERASI (PRA KOPERASI/USAHA BERSAMA/UP2K) - PEMULA - PESERTA |
| 28 | PENGEMBANGAN KEAHLIAN BERKOPERASI (PRA KOPERASI/USAHA BERSAMA/UP2K) - MADYA - JML. KLP |
| 29 | PENGEMBANGAN KEAHLIAN BERKOPERASI (PRA KOPERASI/USAHA BERSAMA/UP2K) - MADYA - PESERTA |
| 30 | PENGEMBANGAN KEAHLIAN BERKOPERASI (PRA KOPERASI/USAHA BERSAMA/UP2K) - UTAMA - JML. KLP |
| 31 | PENGEMBANGAN KEAHLIAN BERKOPERASI (PRA KOPERASI/USAHA BERSAMA/UP2K) - UTAMA - PESERTA |
| 32 | PENGEMBANGAN KEAHLIAN BERKOPERASI (PRA KOPERASI/USAHA BERSAMA/UP2K) - MANDIRI - JML. KLP |
| 33 | PENGEMBANGAN KEAHLIAN BERKOPERASI (PRA KOPERASI/USAHA BERSAMA/UP2K) - MANDIRI - PESERTA |
| 34 | KOPERASI BERBADAN HUKUM - JML. KLP |
| 35 | KOPERASI BERBADAN HUKUM - JML. ANGGOTA |
| 36 | KET. |

## Status Presisi

- Status header merge: **terverifikasi visual (final)**.
- Status sinkronisasi kontrak domain: **not implemented** (belum ada modul/report khusus).

## Dampak ke Kontrak Domain Saat Ini

- Mapping header sudah lengkap dan siap dipakai jika modul/report Pokja II dibangun.
- Belum ada modul/report khusus untuk Lampiran 4.22.
