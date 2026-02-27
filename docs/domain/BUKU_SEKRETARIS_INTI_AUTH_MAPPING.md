# Mapping Autentik Buku Sekretaris Inti (Notulen, Daftar Hadir, Buku Tamu)

Tanggal: 2026-02-27  
Status: `baseline-internal` (belum `verified`)

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

Catatan sumber primer:
- Referensi lokal saat ini belum menyediakan template tabel autentik final khusus untuk 3 buku ini.
- Sampai sumber primer terkunci + bukti visual header tersedia, status autentik tetap `unverified`.

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
1. Struktur header baseline internal dianggap kontrak implementasi sementara.
2. Perubahan urutan/label header wajib melalui test update + sinkronisasi dokumen ini.
3. Kenaikan status autentik ke `verified` hanya boleh dilakukan setelah:
   - sumber primer final terkunci,
   - bukti visual header autentik tersedia (sesuai kriteria AGENTS),
   - mapping merge cell autentik (`rowspan/colspan`) terdokumentasi.
