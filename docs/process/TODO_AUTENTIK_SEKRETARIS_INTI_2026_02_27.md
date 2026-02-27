# TODO AUTENTIK SEKRETARIS INTI 2026-02-27

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
- [ ] Tetapkan dokumen autentik final per buku (notulen/daftar hadir/tamu).
- [ ] Kunci referensi terakhir jika ada sumber ganda; referensi lama ditandai `superseded`.

### B. Pembacaan Dokumen (Mandatory Flow)
- [ ] Lakukan ekstraksi token identitas dokumen (text-layer atau ekuivalen).
- [ ] Verifikasi visual header tabel dan merge cell (`rowspan/colspan`) via screenshot.
- [ ] Pastikan screenshot memenuhi syarat valid:
  - header tabel utuh,
  - garis sel jelas,
  - nomor kolom terlihat,
  - teks header terbaca.

### C. Sinkronisasi Kontrak
- [ ] Buat/ubah dokumen mapping domain khusus untuk 3 buku sekretaris inti.
- [ ] Sinkronkan field canonical terhadap header autentik final.
- [ ] Sinkronkan blade PDF jika ditemukan drift struktur kolom.

### D. Validasi
- [x] Tambah/rapikan test header kolom PDF untuk 3 buku sekretaris inti.
- [x] Jalankan test targeted concern + regresi report/print terkait.
- [ ] Jalankan `php artisan test` bila ada perubahan lintas modul.

## Validasi Keberhasilan
- [ ] Ketiga buku punya peta header final sampai merge-cell.
- [ ] Bukti visual resmi tersimpan dan tertaut di dokumen mapping.
- [ ] Status autentikasi dapat dinaikkan ke `verified` tanpa ambigu.

## Risiko
- Risiko false-verified jika screenshot tidak memenuhi kriteria.
- Risiko drift label bila sinkronisasi hanya di satu dokumen.
- Risiko rework jika sumber autentik final berubah setelah implementasi.

## Keputusan yang Harus Dikunci
- [ ] Sumber autentik final per buku (K-A1).
- [ ] Mapping merge-header final per buku (K-A2).
- [ ] Kenaikan status `unverified -> verified` per buku (K-A3).

## Output Wajib Tiap Update
- [ ] Daftar perubahan status autentikasi per buku.
- [ ] Bukti validasi yang dipakai (token identitas + screenshot).
- [ ] File terdampak dan alasan sinkronisasi.

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

### Catatan
- Gate struktur header PDF internal modul sekretaris inti telah terkunci via test.
- Status autentikasi canonical masih `unverified` sampai sumber autentik primer + bukti screenshot header resmi tersedia.
