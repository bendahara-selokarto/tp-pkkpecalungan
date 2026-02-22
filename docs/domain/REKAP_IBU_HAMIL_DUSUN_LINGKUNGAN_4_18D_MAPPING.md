# Mapping Autentik Rekap Ibu Hamil Dusun/Lingkungan (Lampiran 4.18d)

Tanggal baca: 2026-02-22

Sumber autentik:
- Screenshot dokumen resmi Lampiran 4.18d dari user pada sesi validasi 2026-02-22.
- Screenshot halaman `cara pengisian` Lampiran 4.18d dari user pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran: `4.18d`.
- Judul utama: `BUKU CATATAN IBU HAMIL, KELAHIRAN, KEMATIAN BAYI, KEMATIAN BALITA DAN KEMATIAN IBU HAMIL, MELAHIRKAN DAN NIFAS DALAM KELOMPOK PKK DUSUN/LINGKUNGAN`.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `17`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis agregasi baris: `NOMOR RW`.

Grup header:
- Kolom tunggal non-grup:
  - `1: NO`
  - `2: NOMOR RW`
  - `17: KETERANGAN`
- Kolom grup:
  - `3-4: JUMLAH` (`RT`, `DASA WISMA`)
  - `5-8: JUMLAH IBU`
  - `9-14: JUMLAH BAYI`
  - `15-16: JML. BALITA MENINGGAL`

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NOMOR RW |
| 3 | JUMLAH KELOMPOK PKK RT (RT) |
| 4 | JUMLAH DASA WISMA |
| 5 | HAMIL |
| 6 | MELAHIRKAN |
| 7 | NIFAS |
| 8 | MENINGGAL (IBU) |
| 9-10 | LAHIR (L/P) |
| 11-12 | AKTE KELAHIRAN (ADA/TIDAK) |
| 13-14 | MENINGGAL BAYI (L/P) |
| 15-16 | MENINGGAL BALITA (L/P) |
| 17 | KETERANGAN |

## Konfirmasi Cara Pengisian

Hasil baca halaman `cara pengisian` Lampiran 4.18d mengunci kontrak berikut:
- `kolom 2`: nomor RW pada dusun/lingkungan.
- `kolom 3`: jumlah kelompok PKK RT pada lingkup RW tersebut.
- `kolom 4-16`: nilai penjumlahan dari buku catatan tingkat PKK RW.
- `kolom 17`: keterangan.

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- Implementasi aktif:
  - view PDF: `resources/views/pdf/rekap_ibu_hamil_melahirkan_dusun_lingkungan_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/rekap-ibu-hamil-pkk-dusun-lingkungan/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/rekap-ibu-hamil-pkk-dusun-lingkungan/report/pdf`
- Catatan sumber data:
  - Agregasi utama dihitung per `NOMOR RW` dari dataset 4.18a.
  - `JUMLAH KELOMPOK PKK RT` dihitung dari RT unik terdeteksi pada grup RW.
  - `JUMLAH DASA WISMA` dihitung sebagai penjumlahan jumlah dasawisma per RT (sesuai aturan `kolom 4-16` dari cara pengisian), bukan unique lintas seluruh RW.
  - Kolom `5-16` dijumlahkan dari indikator maternal/kelahiran/kematian yang sama dengan 4.18b/4.18c.
