# Mapping Autentik Buku Sekretaris Inti (Notulen, Daftar Hadir, Buku Tamu)

Tanggal: 2026-02-27  
Status: `source-scanned` (`unverified-local-extension`)

## Konteks

- Modul berikut sudah aktif pada level desa/kecamatan:
  - `buku-notulen-rapat`
  - `buku-daftar-hadir`
  - `buku-tamu`
- Ketiganya masih berstatus autentikasi `unverified` pada dokumen canonical.
- Dokumen ini mengunci baseline struktur header report saat ini untuk mencegah drift implementasi.

## Sumber Validasi Saat Ini

1. Implementasi blade PDF:
   - `resources/views/pdf/buku_notulen_rapat_report.blade.php`
   - `resources/views/pdf/buku_daftar_hadir_report.blade.php`
   - `resources/views/pdf/buku_tamu_report.blade.php`
2. Guard test header:
   - `tests/Feature/BukuNotulenRapatReportPrintTest.php`
   - `tests/Feature/BukuDaftarHadirReportPrintTest.php`
   - `tests/Feature/BukuTamuReportPrintTest.php`
3. Scan sumber primer:
   - `docs/referensi/Rakernas X.pdf` (text-layer scan seluruh halaman)
   - `docs/referensi/excel/BUKU BANTU.xlsx` (scan daftar sheet)
4. Bukti visual sumber primer (pembanding coverage lampiran sekretaris):
   - `docs/referensi/_screenshots/rakernas-x-secretariat-core/rakernas_x_page_20.png` (Lampiran 4.10)
   - `docs/referensi/_screenshots/rakernas-x-secretariat-core/rakernas_x_page_24.png` (Lampiran 4.12)
   - `docs/referensi/_screenshots/rakernas-x-secretariat-core/rakernas_x_page_26.png` (Lampiran 4.13)

Catatan sumber primer:

- Scan text-layer `Rakernas X.pdf` (2026-03-02) menunjukkan:
  - `NOTULEN` -> `NO_MATCH`
  - `DAFTAR HADIR` -> `NO_MATCH`
  - `BUKU TAMU` -> `NO_MATCH`
- Coverage lampiran buku sekretaris pada sumber primer terdeteksi berurutan:
  - `LAMPIRAN 4.10` (Agenda Surat): halaman PDF `20-21`
  - `LAMPIRAN 4.11` (Keuangan): halaman PDF `22-23`
  - `LAMPIRAN 4.12` (Inventaris): halaman PDF `24-25`
  - `LAMPIRAN 4.13` (Buku Kegiatan): halaman PDF `26-27`
- Scan workbook `BUKU BANTU.xlsx` (2026-03-02) juga menunjukkan token `NOTULEN/DAFTAR HADIR/BUKU TAMU` tidak tersedia pada daftar sheet.
- Keputusan: tiga buku sekretaris inti diposisikan sebagai `ekstensi lokal` tanpa template tabel primer eksplisit saat ini; status autentik tetap `unverified-local-extension` sampai ada sumber resmi baru.

## Bukti Scan Sumber Primer (2026-03-02)

- Perintah:
  - `python -c "... scan keyword NOTULEN|DAFTAR HADIR|BUKU TAMU pada docs/referensi/Rakernas X.pdf ..."`
  - `python -c "... scan sheet workbook docs/referensi/excel/BUKU BANTU.xlsx ..."`
- Hasil:
  - `Rakernas X.pdf`: `NOTULEN NO_MATCH`, `DAFTAR HADIR NO_MATCH`, `BUKU TAMU NO_MATCH`.
  - `BUKU BANTU.xlsx`: sheet terdeteksi tidak memuat token `NOTULEN/DAFTAR HADIR/BUKU TAMU`.

## Peta Header Baseline (Implementasi Aktif)

### 1) Buku Notulen Rapat

- Jumlah kolom: 6
- Pola merge:
  - semua header `rowspan=1`, `colspan=1` (tanpa merge)
- Urutan header:
  1. `NO`
  2. `TANGGAL`
  3. `JUDUL RAPAT`
  4. `NAMA`
  5. `INSTANSI`
  6. `KETERANGAN`

### 2) Buku Daftar Hadir

- Jumlah kolom: 6
- Pola merge:
  - semua header `rowspan=1`, `colspan=1` (tanpa merge)
- Urutan header:
  1. `NO`
  2. `TANGGAL`
  3. `KEGIATAN`
  4. `NAMA`
  5. `INSTANSI`
  6. `KETERANGAN`

### 3) Buku Tamu

- Jumlah kolom: 6
- Pola merge:
  - semua header `rowspan=1`, `colspan=1` (tanpa merge)
- Urutan header:
  1. `NO`
  2. `TANGGAL`
  3. `NAMA TAMU`
  4. `KEPERLUAN`
  5. `INSTANSI`
  6. `KETERANGAN`

## Kontrak Operasional Sementara

1. Struktur header baseline internal dianggap kontrak operasional aktif untuk tiga buku ekstensi lokal ini.
2. Perubahan urutan/label header wajib melalui test update + sinkronisasi dokumen ini.
3. Status autentik saat ini dikunci `unverified-local-extension`.
4. Kenaikan status ke `verified` hanya boleh dilakukan bila sumber primer resmi untuk tiga buku ini tersedia dan tervalidasi (`text-layer + bukti visual header + mapping merge cell`).

