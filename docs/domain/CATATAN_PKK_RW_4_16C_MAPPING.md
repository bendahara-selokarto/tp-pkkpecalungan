# Mapping Autentik Catatan PKK RW (Lampiran 4.16c)

Tanggal baca: 2026-02-22

Sumber autentik:
- `d:\pedoman\183.pdf`

Artefak verifikasi visual:
- Screenshot header tabel Lampiran 4.16c (dari user) pada sesi validasi 2026-02-22.

Judul dokumen:
- `CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RW`

## Hasil Baca Awal (Text-Layer)

Token identitas yang berhasil dibaca:
- `LAMPIRAN 4.16c`
- `CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RW`
- `DASA WISMA`
- `RT / RW`
- `DESA/KELURAHAN`
- `TAHUN`

Catatan:
- Text-layer cukup untuk identitas dokumen, tetapi tidak cukup untuk struktur header tabel.

## Hasil Verifikasi Visual Header Tabel

Struktur umum:
- Header numerik terbaca sampai kolom `32`.
- Header bertingkat 3 baris (grup, sub-header, nomor kolom).
- Peta merge cell (`rowspan`/`colspan`) berhasil divalidasi.

Grup header:
- Kolom tunggal non-grup:
  - `1: NO`
  - `2: NOMOR RT`
  - `3: JML DASAWISMA`
  - `4: JML KRT`
  - `5: JML KK`
  - `25: JUMLAH SARANA MCK`
  - `32: KET`
- Kolom grup:
  - `6-16: JUMLAH ANGGOTA KELUARGA`
  - `17-20: KRITERIA RUMAH`
  - `21-24: SUMBER AIR KELUARGA`
  - `26-27: MAKANAN`
  - `28-31: WARGA MENGIKUTI KEGIATAN`

### Peta Header Kolom (Final)

| Kolom | Header |
| --- | --- |
| 1 | NO |
| 2 | NOMOR RT |
| 3 | JML DASAWISMA |
| 4 | JML KRT |
| 5 | JML KK |
| 6-7 | TOTAL (L/P) |
| 8-9 | BALITA (L/P) |
| 10 | PUS |
| 11 | WUS |
| 12 | IBU HAMIL |
| 13 | IBU MENYUSUI |
| 14 | LANSIA |
| 15-16 | 3 BUTA (L/P) |
| 17 | SEHAT LAYAK HUNI |
| 18 | TIDAK SEHAT LAYAK HUNI |
| 19 | MEMILIKI TTMP. PEMB. SAMPAH |
| 20 | MEMILIKI SPAL DAN PENYERAPAN AIR |
| 21 | PDAM |
| 22 | SUMUR |
| 23 | SUNGAI |
| 24 | DLL |
| 25 | JUMLAH SARANA MCK |
| 26 | BERAS |
| 27 | NON BERAS |
| 28 | UP2K |
| 29 | PEMANFAATAN TANAH PEKARANGAN |
| 30 | INDUSTRI RUMAH TANGGA |
| 31 | KESEHATAN LINGKUNGAN |
| 32 | KET |

## Status Presisi

- Status header merge: **terverifikasi visual**.
- Status sinkronisasi kontrak domain: **siap sinkronisasi dokumen**.
- Status implementasi report: **belum diimplementasikan** (menunggu keputusan implementasi).

## Dampak ke Kontrak Domain

- Dokumen `183.pdf` ditetapkan sebagai sumber resmi Lampiran `4.16c`.
- Struktur autentik 32 kolom sudah terkunci untuk referensi domain.
- Implementasi modul/report belum dijalankan pada fase ini.
