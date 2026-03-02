# Release Checklist PDF (T10)

Tujuan:
- Menjadi quality gate rilis untuk semua perubahan modul PDF buku sekretaris.
- Memastikan output PDF tetap sesuai pedoman domain utama, aman secara scope-authorization, dan stabil antar level wilayah.

Ruang lingkup:
- Semua route report/print PDF lampiran 4.9a-4.15 (`desa` dan `kecamatan`).

## 1) Gate Otomatis Wajib (Harus Hijau)

Jalankan perintah berikut sebelum rilis:
- `php artisan route:list --name=report`
- `php artisan route:list --path=report/pdf --json --except-vendor`
- `php artisan route:list --path=print --json --except-vendor`
- `rg -n "/report/pdf|/print" resources/js/Pages -S`
- `php artisan test --filter=PdfBaselineFixtureComplianceTest`
- `php artisan test --filter=scope_metadata_tidak_sinkron`

Tambahan jika ada perubahan header/kolom PDF prioritas 4.14.1a-4.15:
- `php artisan test --filter=header_kolom_pdf`

Tambahan jika ada perubahan role/scope/area:
- `php artisan test --filter=role_dan_level_area_tidak_sinkron`
- `php artisan test --filter=role_kecamatan_tetapi_area_level_desa`

## 2) Gate Manual Wajib (Sample PDF Per Level)

Untuk setiap modul yang berubah di release ini:
- Generate 1 sample PDF sebagai user `desa`.
- Generate 1 sample PDF sebagai user `kecamatan`.
- Verifikasi:
  - Judul report sesuai pedoman.
  - Header dan urutan kolom sesuai baseline.
  - Format nilai konsisten (tanggal, angka, boolean, empty).
  - Metadata cetak (`area`, `printedBy`, `printedAt`) tampil.
  - Orientasi default `landscape`.

## 3) Form Checklist Rilis

Catatan:
- Form ini adalah template release-gate per siklus rilis.
- Status run terakhir lihat section "Snapshot Eksekusi".

- [x] Semua route report masih terdaftar normal (`route:list --name=report`).
- [x] Test scoped auth hijau (`scope_metadata_tidak_sinkron`).
- [x] Test compliance baseline PDF hijau (`PdfBaselineFixtureComplianceTest`).
- [x] Audit PDF yatim lintas route/UI/E2E hijau (`0 orphan`, referensi: `docs/process/TODO_PDF26A1_AUDIT_KETERSEDIAAN_FORMAT_PDF_2026_02_28.md`).
- [x] Test header kolom modul terdampak hijau (`header_kolom_pdf`) jika relevan.
- [x] Sample PDF level `desa` diverifikasi manual.
- [x] Sample PDF level `kecamatan` diverifikasi manual.
- [x] Tidak ada deviasi yang belum dicatat di `docs/domain/DOMAIN_DEVIATION_LOG.md`.

## 4) Snapshot Eksekusi (2026-02-22)

- [x] Semua route report terdaftar normal (`80` route).
- [x] Test scoped auth hijau (`scope_metadata_tidak_sinkron`: `28` pass).
- [x] Test compliance baseline PDF hijau (`PdfBaselineFixtureComplianceTest`: `20` pass).
- [x] Test header kolom modul terdampak hijau (`header_kolom_pdf`: `20` pass).
- [x] Cakupan sample PDF level `desa` tervalidasi (`DataWargaReportPrintTest`).
- [x] Cakupan sample PDF level `kecamatan` tervalidasi (`DataWargaReportPrintTest`).
- [x] Tidak ada deviasi baru yang belum dicatat di `docs/domain/DOMAIN_DEVIATION_LOG.md`.

## 4.1) Snapshot Audit PDF Yatim (2026-02-28)

- [x] Route PDF terinventaris: `106` endpoint (`103 report/pdf` + `3 print activity PDF`).
- [x] Cakupan ownership akses tervalidasi: `52 desa`, `53 kecamatan`, `1 auth+verified`.
- [x] Audit orphan PDF lintas route/UI/E2E: `0` temuan terbuka.
- [x] Bukti + log insiden tersimpan di:
  - `docs/process/TODO_PDF26A1_AUDIT_KETERSEDIAAN_FORMAT_PDF_2026_02_28.md`.

## 5) Kriteria Go / No-Go

`NO-GO`:
- Salah satu gate otomatis gagal.
- Verifikasi manual sample `desa`/`kecamatan` gagal.
- Ada mismatch pedoman tanpa catatan deviasi.

`GO`:
- Semua item checklist rilis tercentang.
- Tidak ada temuan kritikal auth-scope atau mismatch format PDF.

## 6) Bukti Validasi T10

Perintah baseline saat dokumen ini dibuat:
- `php artisan route:list --name=report`
  - hasil terbaru (2026-02-22): `80` route report.
- `php artisan test --filter=PdfBaselineFixtureComplianceTest`
  - hasil terbaru (2026-02-22): `20` test pass.
- `php artisan test --filter=scope_metadata_tidak_sinkron`
  - hasil terbaru (2026-02-22): `28` test pass.
- `php artisan test --filter=header_kolom_pdf`
  - hasil terbaru (2026-02-22): `20` test pass.
- `php artisan test --filter=DataWargaReportPrintTest`
  - hasil terbaru (2026-02-22): `4` test pass (sample `desa` + `kecamatan` tervalidasi).
