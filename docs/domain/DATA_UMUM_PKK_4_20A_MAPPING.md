# Mapping Autentik Data Umum PKK (Lampiran 4.20a)

Tanggal baca: 2026-02-22

Sumber autentik:
- Dokumen PDF resmi: `docs/referensi/213.pdf`.
- Screenshot header tabel Lampiran 4.20a dari user pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran: `4.20a`.
- Judul utama: `DATA UMUM PKK`.

## Hasil Baca Awal (Text-Layer)

Ekstraksi text-layer dijalankan ke `213.pdf` sebagai langkah awal kontrak baca dokumen.

Temuan parser:
- text-layer tidak cukup terbaca untuk menangkap token header secara stabil (hasil deteksi identitas dan header tidak reliabel),
- keputusan: text-layer dicatat sebagai langkah wajib awal, namun struktur header final dikunci dari verifikasi visual screenshot.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `20`.
- Header bertingkat dengan merge (`rowspan`/`colspan`) tervalidasi visual.
- Basis baris data: `NAMA DUSUN LINGKUNGAN ATAU SEBUTAN LAIN`.

Peta merge header:
- Baris 1:
  - `1` (`NO`) `rowspan=3`
  - `2` (`NAMA DUSUN LINGKUNGAN ATAU SEBUTAN LAIN`) `rowspan=3`
  - `3-5` `colspan=3` (`JUMLAH KELOMPOK`)
  - `6-7` `colspan=2` (`JUMLAH`)
  - `8-9` `colspan=2` (`JUMLAH JIWA`)
  - `10-15` `colspan=6` (`JUMLAH KADER`)
  - `16-19` `colspan=4` (`JUMLAH TENAGA SEKRETARIAT`)
  - `20` (`KETERANGAN`) `rowspan=3`
- Baris 2:
  - `3` (`PKK RW`) `rowspan=2`
  - `4` (`PKK RT`) `rowspan=2`
  - `5` (`DASA WISMA`) `rowspan=2`
  - `6` (`KRT`) `rowspan=2`
  - `7` (`KK`) `rowspan=2`
  - `8` (`L`) `rowspan=2`
  - `9` (`P`) `rowspan=2`
  - `10-11` `colspan=2` (`ANGGOTA TP. PKK`)
  - `12-13` `colspan=2` (`UMUM`)
  - `14-15` `colspan=2` (`KHUSUS`)
  - `16-17` `colspan=2` (`HONORER`)
  - `18-19` `colspan=2` (`BANTUAN`)
- Baris 3:
  - `10: L`, `11: P`
  - `12: L`, `13: P`
  - `14: L`, `15: P`
  - `16: L`, `17: P`
  - `18: L`, `19: P`

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NAMA DUSUN LINGKUNGAN ATAU SEBUTAN LAIN |
| 3 | JUMLAH KELOMPOK PKK RW |
| 4 | JUMLAH KELOMPOK PKK RT |
| 5 | JUMLAH KELOMPOK DASA WISMA |
| 6 | JUMLAH KRT |
| 7 | JUMLAH KK |
| 8 | JUMLAH JIWA L |
| 9 | JUMLAH JIWA P |
| 10-11 | JUMLAH KADER ANGGOTA TP. PKK (L/P) |
| 12-13 | JUMLAH KADER UMUM (L/P) |
| 14-15 | JUMLAH KADER KHUSUS (L/P) |
| 16-17 | JUMLAH TENAGA SEKRETARIAT HONORER (L/P) |
| 18-19 | JUMLAH TENAGA SEKRETARIAT BANTUAN (L/P) |
| 20 | KETERANGAN |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- Implementasi aktif:
  - view PDF: `resources/views/pdf/data_umum_pkk_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/data-umum-pkk/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/data-umum-pkk/report/pdf`
- Catatan sumber data operasional:
  - `kolom 3-9` berasal dari agregasi rumah tangga (`data_wargas`) per grup dusun/lingkungan,
  - `kolom 10-15` berasal dari agregasi `anggota_tim_penggeraks` + `anggota_pokjas` + `kader_khusus`,
  - `kolom 16-19` sementara diproyeksikan dari inferensi token `jabatan` (`honorer`/`bantuan`) pada `anggota_tim_penggeraks`.
