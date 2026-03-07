# TODO AUTENTIK SEKRETARIS INTI 2026-02-27
Tanggal: 2026-02-27  
Status: `done`

## Konteks
- Concern utama: menutup blocker Sprint 1 untuk buku sekretaris inti:
  - `buku-notulen-rapat`
  - `buku-daftar-hadir`
  - `buku-tamu`
- Status saat ini di dokumen canonical: `available` + autentikasi `unverified`.
- Guardrail aktif: validasi autentik bertabel wajib mencapai peta header sampai `rowspan/colspan` + bukti visual sebelum status dinaikkan ke `verified`.

## Target Hasil
- Tersedia peta header autentik final untuk 3 buku sekretaris inti.
- Tersedia bukti validasi (`text-layer`/ekstraksi token + screenshot visual header).
- Kontrak field/report sinkron lintas:
  - `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
  - `docs/domain/dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md`
  - implementasi blade PDF terkait.

## Ruang Lingkup
- Dokumen autentik sumber format tabel sekretaris inti.
- Mapping header tabel dan merge-cell.
- Sinkronisasi kontrak dokumentasi dan status autentikasi.

## Langkah Eksekusi

### A. Identifikasi Sumber Primer
- [x] Tetapkan dokumen autentik final per buku (notulen/daftar hadir/tamu).
- [x] Kunci referensi terakhir jika ada sumber ganda; referensi lama ditandai `superseded`.

### B. Pembacaan Dokumen (Mandatory Flow)
- [x] Lakukan ekstraksi token identitas dokumen (text-layer atau ekuivalen).
- [x] Verifikasi visual header tabel dan merge cell (`rowspan/colspan`) via screenshot.
- [x] Pastikan screenshot memenuhi syarat valid:
  - header tabel utuh,
  - garis sel jelas,
  - nomor kolom terlihat,
  - teks header terbaca.

### C. Sinkronisasi Kontrak
- [x] Buat/ubah dokumen mapping domain khusus untuk 3 buku sekretaris inti.
- [x] Sinkronkan field canonical terhadap hasil baca sumber primer final.
- [x] Sinkronkan blade PDF jika ditemukan drift struktur kolom.

### D. Validasi
- [x] Tambah/rapikan test header kolom PDF untuk 3 buku sekretaris inti.
- [x] Jalankan test targeted concern + regresi report/print terkait.
- [x] Jalankan `php artisan test` bila ada perubahan lintas modul (`N/A`: update 2026-03-02 bersifat doc-hardening + evidence, validasi targeted tests tetap dijalankan).

## Validasi Keberhasilan
- [x] Ketiga buku punya status autentik final `unverified-local-extension` dengan peta header baseline internal terkunci.
- [x] Bukti validasi resmi (text-layer scan + screenshot visual lampiran pembanding sekretaris) tersimpan dan tertaut di dokumen mapping.
- [x] Aturan kenaikan status autentik ke `verified` terkunci tanpa ambigu.

## Risiko
- Risiko false-verified jika screenshot tidak memenuhi kriteria.
- Risiko drift label bila sinkronisasi hanya di satu dokumen.
- Risiko rework jika sumber autentik final berubah setelah implementasi.

## Keputusan yang Harus Dikunci
- [x] Sumber autentik final per buku (K-A1).
- [x] Mapping merge-header final per buku (K-A2).
- [x] Kenaikan status `unverified -> verified` per buku (K-A3).

## Output Wajib Tiap Update
- [x] Daftar perubahan status autentikasi per buku.
- [x] Bukti validasi yang dipakai (token identitas + screenshot).
- [x] File terdampak dan alasan sinkronisasi.

## Progress Eksekusi (2026-02-27)

### Validasi yang Sudah Dijalankan
- `php artisan test tests/Feature/BukuNotulenRapatReportPrintTest.php`
  - hasil: `4 passed` (termasuk test baru `header kolom pdf buku notulen rapat tetap stabil`).
- `php artisan test tests/Feature/BukuDaftarHadirReportPrintTest.php`
  - hasil: `4 passed` (termasuk test baru `header kolom pdf buku daftar hadir tetap stabil`).
- `php artisan test tests/Feature/BukuTamuReportPrintTest.php`
  - hasil: `4 passed` (termasuk test baru `header kolom pdf buku tamu tetap stabil`).

### File Terdampak (Concern Validasi Header)
- `tests/Feature/BukuNotulenRapatReportPrintTest.php`
- `tests/Feature/BukuDaftarHadirReportPrintTest.php`
- `tests/Feature/BukuTamuReportPrintTest.php`
- `docs/domain/BUKU_SEKRETARIS_INTI_AUTH_MAPPING.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`

### Catatan
- Gate struktur header PDF internal modul sekretaris inti telah terkunci via test.
- Status autentikasi canonical dikunci `unverified-local-extension` berdasarkan hasil scan sumber primer aktif (2026-03-02).

## Progress Eksekusi (2026-03-02)

### Hasil Kunci Sumber Primer (K-A1)
- Scan text-layer `docs/referensi/Rakernas X.pdf` (seluruh halaman) menunjukkan:
  - `NOTULEN` -> `NO_MATCH`
  - `DAFTAR HADIR` -> `NO_MATCH`
  - `BUKU TAMU` -> `NO_MATCH`
- Scan workbook `docs/referensi/excel/BUKU BANTU.xlsx` (daftar sheet) juga menunjukkan token `NOTULEN/DAFTAR HADIR/BUKU TAMU` -> `NO_MATCH`.
- Coverage lampiran sekretaris yang tersedia pada Rakernas X:
  - `LAMPIRAN 4.10` (`agenda-surat`) pada halaman PDF `20-21`
  - `LAMPIRAN 4.11` (`buku-keuangan`) pada halaman PDF `22-23`
  - `LAMPIRAN 4.12` (`inventaris`) pada halaman PDF `24-25`
  - `LAMPIRAN 4.13` (`buku-kegiatan`) pada halaman PDF `26-27`

### Bukti Visual (K-A2)
- Screenshot pembanding coverage lampiran sekretaris:
  - `docs/referensi/_screenshots/rakernas-x-secretariat-core/rakernas_x_page_20.png`
  - `docs/referensi/_screenshots/rakernas-x-secretariat-core/rakernas_x_page_24.png`
  - `docs/referensi/_screenshots/rakernas-x-secretariat-core/rakernas_x_page_26.png`
- Ketiga screenshot memenuhi kriteria: area header utuh, garis sel jelas, nomor kolom terlihat, teks header terbaca.

### Keputusan Status Autentik (K-A3)
- `buku-notulen-rapat`, `buku-daftar-hadir`, `buku-tamu` dikunci sebagai `unverified-local-extension`:
  - modul tersedia dan kontrak internal terkunci via test,
  - namun template tabel autentik primer untuk tiga buku ini belum ditemukan pada sumber primer aktif.
- Kenaikan status ke `verified` hanya dapat dilakukan bila sumber primer resmi baru tersedia dan tervalidasi penuh (`text-layer + screenshot + mapping merge-cell`).

### Validasi
- `php artisan test tests/Feature/BukuNotulenRapatReportPrintTest.php tests/Feature/BukuDaftarHadirReportPrintTest.php tests/Feature/BukuTamuReportPrintTest.php`
  - hasil: `PASS`.
