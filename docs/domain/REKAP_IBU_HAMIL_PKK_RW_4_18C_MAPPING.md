# Mapping Autentik Rekap Ibu Hamil PKK RW (Lampiran 4.18c)

Tanggal baca: 2026-02-22

Sumber autentik:
- Screenshot dokumen resmi Lampiran 4.18c (halaman cara pengisian) dari user pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran: `4.18c`.
- Judul halaman autentik: `CARA PENGISIAN BUKU CATATAN IBU HAMIL, KELAHIRAN, KEMATIAN BAYI, KEMATIAN BALITA DAN KEMATIAN IBU HAMIL, MELAHIRKAN DAN NIFAS DALAM KELOMPOK PKK RW`.

## Hasil Verifikasi Visual Kontrak Kolom

Sesuai tabel `Kolom | Penjelasan` pada lampiran:
- Metadata form wajib: `RW`, `Dusun/Lingkungan`, `Desa/Kel.`, `Bulan`, `Tahun` (dengan konteks wilayah sampai `Kecamatan/Kabupaten/Kota/Provinsi` pada penjelasan).
- Kolom data numerik:
  - `1`: nomor urut penulisan.
  - `2`: nomor RT pada wilayah RW.
  - `3`: jumlah kelompok dasawisma pada wilayah RT.
  - `4-15`: nilai penjumlahan dari buku catatan tingkat PKK RT (lampiran 4.18b).
  - `16`: keterangan.

### Peta Header Kolom (Final Kontrak Operasional)

| Kolom | Header |
| --- | --- |
| 1 | NO. |
| 2 | NOMOR RT |
| 3 | JUMLAH KELOMPOK DASAWISMA |
| 4 | HAMIL |
| 5 | MELAHIRKAN |
| 6 | NIFAS |
| 7 | MENINGGAL (IBU) |
| 8-9 | LAHIR (L/P) |
| 10-11 | AKTE KELAHIRAN (ADA/TIDAK ADA) |
| 12-13 | MENINGGAL BAYI (L/P) |
| 14-15 | MENINGGAL BALITA (L/P) |
| 16 | KETERANGAN |

## Status Presisi

- Status kontrak kolom: **terverifikasi dari halaman cara pengisian autentik**.
- Status sinkronisasi kontrak domain: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- Implementasi aktif:
  - view PDF: `resources/views/pdf/rekap_ibu_hamil_melahirkan_pkk_rw_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/rekap-ibu-hamil-pkk-rw/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/rekap-ibu-hamil-pkk-rw/report/pdf`
- Catatan sumber data:
  - Agregasi utama dihitung per `NOMOR RT` dari dataset 4.18a.
  - Kolom `4-15` dijumlahkan dari indikator maternal/kelahiran/kematian pada level rumah tangga.
  - `JUMLAH KELOMPOK DASAWISMA` dihitung dari jumlah nama dasawisma unik per RT.
