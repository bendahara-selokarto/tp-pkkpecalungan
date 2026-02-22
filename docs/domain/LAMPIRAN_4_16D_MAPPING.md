# Mapping Autentik Lampiran 4.16d (Header Nomor RW)

Tanggal baca: 2026-02-22

Sumber autentik:
- `d:\pedoman\185.pdf`

Artefak verifikasi visual:
- Screenshot header tabel Lampiran 4.16d (dari user) pada sesi validasi 2026-02-22.
- Screenshot halaman penuh Lampiran 4.16d (dari user) pada sesi validasi 2026-02-22.

Catatan identitas dokumen:
- Lampiran terkonfirmasi: `4.16d`.
- Judul canonical tervalidasi: `CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK DUSUN/LINGKUNGAN`.
- Judul report implementasi telah disinkronkan ke judul canonical berdasarkan bukti visual halaman penuh.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `33`.
- Header bertingkat dengan model merge (`rowspan`/`colspan`) setara keluarga lampiran 4.16.
- Kolom dasar agregasi menunjukkan level baris per `NOMOR RW`.

Grup header:
- Kolom tunggal non-grup:
  - `1: NO`
  - `2: NOMOR RW`
  - `3: JML RT`
  - `4: JML DASAWISMA`
  - `5: JML KRT`
  - `6: JML KK`
  - `26: JUMLAH SARANA MCK`
  - `33: KET`
- Kolom grup:
  - `7-17: JUMLAH ANGGOTA KELUARGA`
  - `18-21: KRITERIA RUMAH`
  - `22-25: SUMBER AIR KELUARGA`
  - `27-28: MAKANAN`
  - `29-32: WARGA MENGIKUTI KEGIATAN`

### Peta Header Kolom (Final Visual)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NOMOR RW |
| 3 | JML RT |
| 4 | JML DASAWISMA |
| 5 | JML KRT |
| 6 | JML KK |
| 7-8 | TOTAL (L/P) |
| 9-10 | BALITA (L/P) |
| 11 | PUS |
| 12 | WUS |
| 13 | IBU HAMIL |
| 14 | IBU MENYUSUI |
| 15 | LANSIA |
| 16-17 | 3 BUTA (L/P) |
| 18 | SEHAT LAYAK HUNI |
| 19 | TIDAK SEHAT LAYAK HUNI |
| 20 | MEMILIKI TTMP. PEMB. SAMPAH |
| 21 | MEMILIKI SPAL DAN PENYERAPAN AIR |
| 22 | PDAM |
| 23 | SUMUR |
| 24 | SUNGAI |
| 25 | DLL |
| 26 | JUMLAH SARANA MCK |
| 27 | BERAS |
| 28 | NON BERAS |
| 29 | UP2K |
| 30 | PEMANFAATAN TANAH PEKARANGAN |
| 31 | INDUSTRI RUMAH TANGGA |
| 32 | KESEHATAN LINGKUNGAN |
| 33 | KET |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **implemented dan sinkron dengan judul canonical**.
- Status implementasi report: **implemented (report-only via catatan-keluarga)**.

## Dampak ke Kontrak Domain Saat Ini

- `185.pdf` ditetapkan sebagai sumber autentik Lampiran `4.16d`.
- Struktur autentik 33 kolom terkunci sebagai referensi implementasi.
- Implementasi aktif:
  - view PDF: `resources/views/pdf/rekap_catatan_data_kegiatan_warga_rw_report.blade.php`
  - endpoint desa: `/desa/catatan-keluarga/rekap-rw/report/pdf`
  - endpoint kecamatan: `/kecamatan/catatan-keluarga/rekap-rw/report/pdf`
- Catatan sumber data:
  - Agregasi utama dihitung dari `data_wargas` + `data_warga_anggotas`.
  - Pengelompokan baris menggunakan ekstraksi `NOMOR RW` dari atribut rumah tangga (`alamat`/`dasawisma`).
  - `JML RT` dihitung dari jumlah unik RT terdeteksi dalam setiap grup RW.
- Catatan deviasi:
  - Deviasi judul 4.16d (`DV-006`) telah ditutup (`resolved`) setelah konfirmasi halaman penuh.
