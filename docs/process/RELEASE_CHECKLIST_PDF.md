# Release Checklist PDF (T10)

Tujuan:
- Menjadi quality gate rilis untuk semua perubahan modul PDF buku sekretaris.
- Memastikan output PDF tetap sesuai pedoman domain utama, aman secara scope-authorization, dan stabil antar level wilayah.

Ruang lingkup:
- Semua route report/print PDF lampiran 4.9a-4.15 (`desa` dan `kecamatan`).

## 1) Gate Otomatis Wajib (Harus Hijau)

Jalankan perintah berikut sebelum rilis:
- `php artisan route:list --name=report`
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

- [ ] Semua route report masih terdaftar normal (`route:list --name=report`).
- [ ] Test scoped auth hijau (`scope_metadata_tidak_sinkron`).
- [ ] Test compliance baseline PDF hijau (`PdfBaselineFixtureComplianceTest`).
- [ ] Test header kolom modul terdampak hijau (`header_kolom_pdf`) jika relevan.
- [ ] Sample PDF level `desa` diverifikasi manual.
- [ ] Sample PDF level `kecamatan` diverifikasi manual.
- [ ] Tidak ada deviasi yang belum dicatat di `docs/domain/DOMAIN_DEVIATION_LOG.md`.

## 4) Kriteria Go / No-Go

`NO-GO`:
- Salah satu gate otomatis gagal.
- Verifikasi manual sample `desa`/`kecamatan` gagal.
- Ada mismatch pedoman tanpa catatan deviasi.

`GO`:
- Semua item checklist rilis tercentang.
- Tidak ada temuan kritikal auth-scope atau mismatch format PDF.

## 5) Bukti Validasi T10

Perintah baseline saat dokumen ini dibuat:
- `php artisan route:list --name=report`
  - hasil: `52` route report.
- `php artisan test --filter=PdfBaselineFixtureComplianceTest`
  - hasil: `20` test pass.
- `php artisan test --filter=scope_metadata_tidak_sinkron`
  - hasil: `25` test pass.
