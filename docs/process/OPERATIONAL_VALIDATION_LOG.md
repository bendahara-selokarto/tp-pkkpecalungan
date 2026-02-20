# Operational Validation Log

Tujuan:
- Menyimpan bukti eksekusi task operasional berulang `R1-R4`.
- Menjadi artefak audit untuk gate rilis dan review perubahan berikutnya.

## Siklus Baseline: 2026-02-20

Ruang lingkup:
- Baseline pasca otomatisasi CI gate domain/PDF.
- Modul buku sekretaris lampiran 4.9a-4.15.

### R1. Route Check per Modul

Perintah acuan:
- `php artisan route:list --json`
- `php artisan route:list --name=report`

Ringkasan ketersediaan route `desa` dan `kecamatan` per modul:

| Lampiran | Modul | Route key | Desa route | Kecamatan route |
| --- | --- | --- | --- | --- |
| 4.9a | anggota-tim-penggerak | anggota-tim-penggerak | 2 | 2 |
| 4.9b | kader-khusus | kader-khusus | 1 | 1 |
| 4.10 | agenda-surat | agenda-surat | 2 | 2 |
| 4.11 | buku-keuangan | bantuans.keuangan | 1 | 1 |
| 4.12 | inventaris | inventaris | 1 | 1 |
| 4.13 | kegiatan | activities | 1 | 2 |
| 4.14.1a | data-warga | data-warga | 1 | 1 |
| 4.14.1b | data-kegiatan-warga | data-kegiatan-warga | 1 | 1 |
| 4.14.2a | data-keluarga | data-keluarga | 1 | 1 |
| 4.14.2b | data-pemanfaatan-tanah-pekarangan-hatinya-pkk | data-pemanfaatan-tanah-pekarangan-hatinya-pkk | 1 | 1 |
| 4.14.2c | data-industri-rumah-tangga | data-industri-rumah-tangga | 1 | 1 |
| 4.14.3 | data-pelatihan-kader | data-pelatihan-kader | 1 | 1 |
| 4.14.4a | warung-pkk | warung-pkk | 1 | 1 |
| 4.14.4b | taman-bacaan | taman-bacaan | 1 | 1 |
| 4.14.4c | koperasi | koperasi | 1 | 1 |
| 4.14.4d | kejar-paket | kejar-paket | 1 | 1 |
| 4.14.4e | posyandu | posyandu | 1 | 1 |
| 4.14.4f | simulasi-penyuluhan | simulasi-penyuluhan | 1 | 1 |
| 4.15 | catatan-keluarga | catatan-keluarga | 1 | 1 |

Status:
- `PASS` (route per modul terdeteksi sesuai ekspektasi baseline).

### R2. Targeted Test Modul + Policy/Scope

Perintah:
- `php artisan test --filter=ReportPrintTest`
  - hasil: `82` tests pass (`218` assertions).
- `php artisan test tests/Unit/Policies`
  - hasil: `49` tests pass (`79` assertions).

Status:
- `PASS` (jalur report print modul dan policy/scope unit tervalidasi).

### R3. Full Suite Sebelum Merge Signifikan

Perintah:
- `php artisan test`

Hasil:
- `400` tests pass (`1440` assertions).

Status:
- `PASS`.

### R4. Verifikasi PDF Sample Desa dan Kecamatan

Metode verifikasi baseline (otomatis):
- Jalur sample print `desa` dan `kecamatan` tervalidasi oleh `ReportPrintTest`.
- Koherensi judul/header pedoman tervalidasi oleh:
  - `php artisan test --filter=PdfBaselineFixtureComplianceTest`
  - `php artisan test --filter=header_kolom_pdf`

Hasil:
- `PdfBaselineFixtureComplianceTest`: `20` tests pass.
- `header_kolom_pdf`: `7` tests pass.

Status:
- `PASS` untuk baseline otomatis.

## Catatan Pelaksanaan

- Log ini harus diperbarui setiap kali ada perubahan besar domain/PDF/auth-scope.
- Jika ada mismatch pedoman yang ditemukan di siklus berikutnya, wajib dicatat ke:
  - `docs/domain/DOMAIN_DEVIATION_LOG.md`

## Siklus Dashboard Coverage: 2026-02-20

Ruang lingkup:
- Eksekusi `D1-D5` dashboard coverage dokumen 4.9a-4.15.
- Kontrak backend `UseCase + Repository`, chart dashboard, test coverage scope, dan cache TTL.

Perintah validasi:
- `php artisan test tests/Feature/DashboardActivityChartTest.php tests/Feature/DashboardDocumentCoverageTest.php tests/Unit/UseCases/BuildDashboardDocumentCoverageUseCaseTest.php`
  - hasil: `9` tests pass (`171` assertions).
- `php artisan test`
  - hasil: `424` tests pass (`1544` assertions).

Status:
- `PASS` untuk rollout dashboard coverage dokumen.
