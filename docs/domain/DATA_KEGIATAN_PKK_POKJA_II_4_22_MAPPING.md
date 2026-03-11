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
- `JML. WARGA YANG MASIH 3 (TIGA) BUTA` memakai `rowspan=4`.
- Blok `PAKET A/B/C` dan `KF` berada di sisi kiri (dua kolom per paket).
- Grup `PENDIDIKAN KETERAMPILAN` mencakup `PAUD SEJENIS`, `JUMLAH TAMAN BACAAN/PERPUSTAKAAN`, `BKB`, `TUTOR`, `KADER KHUSUS`, `JUMLAH KADER YANG SUDAH DILATIH`.
- Grup `JUMLAH KADER YANG SUDAH DILATIH` berisi `LP3`, `TPK 3 PKK`, `DAMAS PKK`.
- Grup `PENGEMBANGAN KEHIDUPAN BERKOPERASI` memiliki layer `PRA KOPERASI/USAHA BERSAMA/UP2K` dan sublayer `PEMULA/MADYA/UTAMA/MANDIRI` dengan leaf `JML. KLP` + `PESERTA`.
- `KOPERASI BERBADAN HUKUM` berisi `JML. KLP` dan `JML. ANGGOTA`.

Peta merge header (detail):

- Baris 1:
  - `1` (`NO`) `rowspan=4`
  - `2` (`NAMA WILAYAH (Dusun/Lingk/Desa/Kel/Kec/Kab/Kota/Pro)`) `rowspan=4`
  - `3` (`JML. WARGA YANG MASIH 3 (TIGA) BUTA`) `rowspan=4`
  - `4-11` `colspan=8` (spacer untuk blok paket)
  - `12-25` `colspan=14` (`PENDIDIKAN KETERAMPILAN`)
  - `26-35` `colspan=10` (`PENGEMBANGAN KEHIDUPAN BERKOPERASI`)
  - `36` (`KET.`) `rowspan=4`
- Baris 2:
  - `4-5` `colspan=2` (`PAKET A`)
  - `6-7` `colspan=2` (`PAKET B`)
  - `8-9` `colspan=2` (`PAKET C`)
  - `10-11` `colspan=2` (`KF`)
  - `12` (`PAUD SEJENIS`) `rowspan=3`
  - `13` (`JUMLAH TAMAN BACAAN/PERPUSTAKAAN`) `rowspan=3`
  - `14-17` `colspan=4` (`BKB`)
  - `18-19` `colspan=2` (`TUTOR`)
  - `20-22` `colspan=3` (`KADER KHUSUS`)
  - `23-25` `colspan=3` (`JUMLAH KADER YANG SUDAH DILATIH`)
  - `26-33` `colspan=8` (`PRA KOPERASI/USAHA BERSAMA/UP2K`)
  - `34-35` `colspan=2` (`KOPERASI BERBADAN HUKUM`)
- Baris 3:
  - `4` (`JML. KLP BELAJAR`) `rowspan=2`
  - `5` (`WARGA BELAJAR`) `rowspan=2`
  - `6` (`JML. KLP BELAJAR`) `rowspan=2`
  - `7` (`WARGA BELAJAR`) `rowspan=2`
  - `8` (`JML. KLP BELAJAR`) `rowspan=2`
  - `9` (`WARGA BELAJAR`) `rowspan=2`
  - `10` (`JML. KLP BELAJAR`) `rowspan=2`
  - `11` (`WARGA BELAJAR`) `rowspan=2`
  - `14` (`JML. KLP`) `rowspan=2`
  - `15` (`JML. IBU PESERTA`) `rowspan=2`
  - `16` (`JML. APE (SET)`) `rowspan=2`
  - `17` (`JML. KLP SIMULASI`) `rowspan=2`
  - `18` (`KF`) `rowspan=2`
  - `19` (`PAUD SEJENIS`) `rowspan=2`
  - `20` (`BKB`) `rowspan=2`
  - `21` (`KOPERASI`) `rowspan=2`
  - `22` (`KETERAMPILAN`) `rowspan=2`
  - `23` (`LP3 PKK`) `rowspan=2`
  - `24` (`TPK 3 PKK`) `rowspan=2`
  - `25` (`DAMAS PKK`) `rowspan=2`
  - `26-27` `colspan=2` (`PEMULA`)
  - `28-29` `colspan=2` (`MADYA`)
  - `30-31` `colspan=2` (`UTAMA`)
  - `32-33` `colspan=2` (`MANDIRI`)
  - `34` (`JML. KLP`) `rowspan=2`
  - `35` (`JML. ANGGOTA`) `rowspan=2`
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
| 17 | BKB - JML. KLP SIMULASI |
| 18 | TUTOR - KF |
| 19 | TUTOR - PAUD SEJENIS |
| 20 | KADER KHUSUS - BKB |
| 21 | KADER KHUSUS - KOPERASI |
| 22 | KADER KHUSUS - KETERAMPILAN |
| 23 | JUMLAH KADER YANG SUDAH DILATIH - LP3 PKK |
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

## Referensi Cara Pengisian (Makna Kolom 1-25)

Sumber: `docs/referensi/supporting/lampiran-4-22-cara-pengisian.pdf`.

| Kolom | Makna (Cara Pengisian) |
| --- | --- |
| TP-PKK, Tahun | Nama tingkatan TP PKK dan tahun pendataan. |
| 1 | Nomor urut penulisan. |
| 2 | Nama tingkatan kepengurusan TP PKK sesuai tingkat: provinsi = nama kab/kota, kab/kota = nama kecamatan, kecamatan = nama desa/kelurahan, desa/kelurahan = nama dusun/lingkungan. |
| 3 | Jumlah warga yang masih mengalami 3 (tiga) buta. |
| 4 | Jumlah kelompok belajar Paket A. |
| 5 | Jumlah warga belajar Paket A. |
| 6 | Jumlah kelompok belajar Paket B. |
| 7 | Jumlah warga belajar Paket B. |
| 8 | Jumlah kelompok belajar Paket C. |
| 9 | Jumlah warga belajar Paket C. |
| 10 | Jumlah kelompok belajar Paket Keaksaraan Fungsional (KF). |
| 11 | Jumlah warga belajar Keaksaraan Fungsional (KF). |
| 12 | Jumlah kelompok PAUD sejenis. |
| 13 | Jumlah Taman Bacaan/Perpustakaan. |
| 14 | Jumlah Kelompok Bina Keluarga Balita (BKB). |
| 15 | Jumlah peserta Kelompok Bina Keluarga Balita (BKB). |
| 16 | Jumlah (set) Alat Permainan Edukatif (APE) BKB. |
| 17 | Jumlah kelompok simulasi BKB. |
| 18 | Jumlah tutor KF. |
| 19 | Jumlah tutor PAUD sejenis. |
| 20 | Jumlah kader BKB. |
| 21 | Jumlah kelompok kader koperasi. |
| 22 | Jumlah kader keterampilan. |
| 23 | Jumlah kader yang sudah dilatih latihan pengelolaan program dan penyuluhan bagi TP PKK. |
| 24 | Jumlah kader yang sudah dilatih Tim Penggerak dan Ketua-Ketua Kelompok PKK. |
| 25 | Jumlah kader yang sudah dilatih Pemberdayaan Masyarakat (DAMAS) PKK. |
| 26 | Jumlah kelompok pra Koperasi/Usaha Bersama/Usaha Peningkatan Pendapatan (UP2K-PKK) tingkat Pemula. |
| 27 | Jumlah peserta pra Koperasi/Usaha Bersama/Usaha Peningkatan Pendapatan (UP2K-PKK) tingkat Pemula. |
| 28 | Jumlah kelompok pra Koperasi/Usaha Bersama/Usaha Peningkatan Pendapatan (UP2K-PKK) tingkat Madya. |
| 29 | Jumlah peserta pra Koperasi/Usaha Bersama/Usaha Peningkatan Pendapatan (UP2K-PKK) tingkat Madya. |
| 30 | Jumlah kelompok pra Koperasi/Usaha Bersama/Usaha Peningkatan Pendapatan (UP2K-PKK) tingkat Utama. |
| 31 | Jumlah peserta pra Koperasi/Usaha Bersama/Usaha Peningkatan Pendapatan (UP2K-PKK) tingkat Utama. |
| 32 | Jumlah kelompok pra Koperasi/Usaha Bersama/Usaha Peningkatan Pendapatan (UP2K-PKK) tingkat Mandiri. |
| 33 | Jumlah peserta pra Koperasi/Usaha Bersama/Usaha Peningkatan Pendapatan (UP2K-PKK) tingkat Mandiri. |
| 34 | Jumlah kelompok Koperasi yang berbadan hukum. |
| 35 | Jumlah anggota Koperasi yang berbadan hukum. |
| 36 | Hal-hal yang belum tercantum dalam kolom lainnya. |

## Status Presisi

- Status header merge: **terverifikasi visual (final)**.
- Makna kolom `1-25` tervalidasi dari dokumen `docs/referensi/supporting/lampiran-4-22-cara-pengisian.pdf`.
- Status sinkronisasi kontrak domain: **not implemented** (belum ada modul/report khusus).

## Dampak ke Kontrak Domain Saat Ini

- Mapping header sudah lengkap dan siap dipakai jika modul/report Pokja II dibangun.
- Belum ada modul/report khusus untuk Lampiran 4.22.
- Rencana sumber data: `docs/domain/DATA_KEGIATAN_PKK_POKJA_II_4_22_SUMBER_DATA.md` (belum diimplementasikan).
