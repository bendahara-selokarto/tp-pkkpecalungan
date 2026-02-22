# Mapping Autentik Data Umum PKK Tingkat Kecamatan (Lampiran 4.20b)

Tanggal baca: 2026-02-22

Sumber autentik:
- Dokumen PDF resmi: `docs/referensi/215.pdf`.
- Screenshot header tabel Lampiran 4.20b dari user pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran: `4.20b`.
- Judul utama: `DATA UMUM PKK`.

## Hasil Baca Awal (Text-Layer)

Ekstraksi text-layer dijalankan ke `215.pdf` sebagai langkah awal kontrak baca dokumen.

Temuan parser:
- token identitas/header tidak terbaca stabil pada hasil ekstraksi stream text-layer,
- indikator teknis: token `LAMPIRAN`, `DATA UMUM PKK`, `KECAMATAN`, `PROVINSI`, `TAHUN`, `JUMLAH KELOMPOK`, `KETERANGAN` tidak terdeteksi andal,
- keputusan: text-layer dicatat sebagai langkah wajib awal, namun struktur header final dikunci dari verifikasi visual screenshot.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `21`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis baris data: `NAMA DESA/KELURAHAN`.

Peta merge header:
- Baris 1:
  - `1` (`NO`) `rowspan=3`
  - `2` (`NAMA DESA/KELURAHAN`) `rowspan=3`
  - `3-6` `colspan=4` (`JUMLAH KELOMPOK`)
  - `7-8` `colspan=2` (`JUMLAH`)
  - `9-10` `colspan=2` (`JUMLAH JIWA`)
  - `11-16` `colspan=6` (`JUMLAH KADER`)
  - `17-20` `colspan=4` (`JUMLAH TENAGA SEKRETARIAT`)
  - `21` (`KETERANGAN`) `rowspan=3`
- Baris 2:
  - `3` (`DUSUN/LINGKUNGAN`) `rowspan=2`
  - `4` (`PKK RW`) `rowspan=2`
  - `5` (`PKK RT`) `rowspan=2`
  - `6` (`DASA WISMA`) `rowspan=2`
  - `7` (`KRT`) `rowspan=2`
  - `8` (`KK`) `rowspan=2`
  - `9` (`L`) `rowspan=2`
  - `10` (`P`) `rowspan=2`
  - `11-12` `colspan=2` (`ANGGOTA TP. PKK`)
  - `13-14` `colspan=2` (`UMUM`)
  - `15-16` `colspan=2` (`KHUSUS`)
  - `17-18` `colspan=2` (`HONORER`)
  - `19-20` `colspan=2` (`BANTUAN`)
- Baris 3:
  - `11: L`, `12: P`
  - `13: L`, `14: P`
  - `15: L`, `16: P`
  - `17: L`, `18: P`
  - `19: L`, `20: P`
- Baris nomor kolom:
  - `1` s.d. `21`.

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA DESA/KELURAHAN |
| 3 | JUMLAH KELOMPOK DUSUN/LINGKUNGAN |
| 4 | JUMLAH KELOMPOK PKK RW |
| 5 | JUMLAH KELOMPOK PKK RT |
| 6 | JUMLAH KELOMPOK DASA WISMA |
| 7 | JUMLAH KRT |
| 8 | JUMLAH KK |
| 9 | JUMLAH JIWA L |
| 10 | JUMLAH JIWA P |
| 11-12 | JUMLAH KADER ANGGOTA TP. PKK (L/P) |
| 13-14 | JUMLAH KADER UMUM (L/P) |
| 15-16 | JUMLAH KADER KHUSUS (L/P) |
| 17-18 | JUMLAH TENAGA SEKRETARIAT HONORER (L/P) |
| 19-20 | JUMLAH TENAGA SEKRETARIAT BANTUAN (L/P) |
| 21 | KETERANGAN |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- Implementasi aktif:
  - view PDF: `resources/views/pdf/data_umum_pkk_kecamatan_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/data-umum-pkk-kecamatan/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/data-umum-pkk-kecamatan/report/pdf`
- Catatan sumber data operasional:
  - kolom `3-10` berasal dari agregasi rumah tangga (`data_wargas`) per `nama_desa_kelurahan`,
  - kolom `11-16` berasal dari agregasi `anggota_tim_penggeraks` + `anggota_pokjas` + `kader_khusus`,
  - kolom `17-20` sementara diproyeksikan dari inferensi token `jabatan` (`honorer`/`bantuan`) pada `anggota_tim_penggeraks`.
