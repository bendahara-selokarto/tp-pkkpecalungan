# PDF Baseline Fixtures

Tujuan:
- Menyimpan baseline kontrak tampilan PDF per modul (judul, urutan header kolom, orientasi default).
- Menjadi acuan review visual saat ada perubahan template report.

Struktur:
- Satu file JSON per modul di folder ini.
- Nama file mengikuti format `<lampiran>-<slug-modul>.json`.

Kunci fixture:
- `lampiran`: referensi lampiran pedoman.
- `moduleSlug`: slug domain modul.
- `view`: view blade PDF sumber render.
- `titleToken`: token judul minimal yang wajib muncul.
- `defaultOrientation`: orientasi standar (default `landscape`).
- `expectedHeaderOrder`: urutan header kolom yang wajib stabil.

## Prosedur Compare Otomatis

1. Jalankan validasi fixture terhadap view PDF:
   - `php artisan test --filter=PdfBaselineFixtureComplianceTest`
2. Jalankan regression header prioritas 4.14.1a-4.15:
   - `php artisan test --filter=header_kolom_pdf`
3. Jika test gagal, update template atau fixture (jika perubahan memang disetujui pedoman) lalu re-run.

## Prosedur Compare Manual

1. Generate sample PDF untuk scope `desa` dan `kecamatan` per modul yang berubah.
2. Buka fixture modul terkait, cocokkan:
   - `titleToken`
   - `expectedHeaderOrder`
   - `defaultOrientation`
3. Verifikasi metadata cetak (`area`, `printedBy`, `printedAt`) pada output.
4. Jika ada deviasi terhadap pedoman, catat di `docs/domain/DOMAIN_DEVIATION_LOG.md`.
