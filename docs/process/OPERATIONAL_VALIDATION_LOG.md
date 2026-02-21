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

## Siklus UI Admin-One Alert Standardization: 2026-02-20

Ruang lingkup:
- Eksekusi `U5-U8` untuk standardisasi alert/konfirmasi UI pada halaman dashboard.
- Migrasi dari flash inline dan native dialog ke komponen reusable admin-one.

Perintah audit:
- `rg "flashSuccess|flashError" resources/js/Pages -l`
  - hasil: `0` file.
- `rg "window\\.confirm" resources/js/Pages -l`
  - hasil: `0` file.
- `rg "window\\.alert|alert\\(" resources/js -l`
  - hasil: `0` file.
- `rg "border-emerald-200 bg-emerald-50|border-rose-200 bg-rose-50" resources/js/Pages -l`
  - hasil: `2` file (`Auth/Login.vue`, `Profile/Edit.vue`, di luar modul dashboard utama).

Perintah validasi:
- `npm run build`
  - hasil: `PASS`.
- `php artisan test`
  - hasil: `424` tests pass (`1544` assertions).

Status:
- `PASS` untuk standardisasi alert/konfirmasi modul dashboard berbasis admin-one.

## Siklus UI Admin-One Harmonization (Auth/Profile): 2026-02-20

Ruang lingkup:
- Harmonisasi dua halaman non-modul yang tersisa:
  - `resources/js/Pages/Auth/Login.vue`
  - `resources/js/Pages/Profile/Edit.vue`

Perintah audit:
- `rg "flashSuccess|flashError" resources/js/Pages -l`
  - hasil: `0` file.
- `rg "window\\.confirm" resources/js/Pages -l`
  - hasil: `0` file.
- `rg "window\\.alert|\\balert\\(" resources/js -l`
  - hasil: `0` file.
- `rg "border-emerald-200 bg-emerald-50|border-rose-200 bg-rose-50" resources/js/Pages -l`
  - hasil: `0` file.

Perintah validasi:
- `npm run build`
  - hasil: `PASS`.
- `php artisan test tests/Feature/Auth/AuthenticationTest.php tests/Feature/ProfileTest.php`
  - hasil: `12` tests pass (`36` assertions).

Status:
- `PASS` untuk harmonisasi akhir UI alert/konfirmasi lintas halaman.

## Siklus Pilot Project Pokja IV (F1-F4): 2026-02-20

Ruang lingkup:
- Inisiasi implementasi pedoman halaman `202-211`.
- Eksekusi `F1` (kontrak domain + terminology map), `F2` (data layer scaffold), `F3` (authorization + scope), dan `F4` (use case/action).

Artefak:
- `docs/domain/PEDOMAN_DOMAIN_UTAMA_202_211.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
- `database/migrations/2026_02_22_010000_create_pilot_project_keluarga_sehat_reports_table.php`
- `database/migrations/2026_02_22_011000_create_pilot_project_keluarga_sehat_values_table.php`
- `app/Domains/Wilayah/PilotProjectKeluargaSehat/*`
- `config/pilot_project_keluarga_sehat.php`

Perintah validasi:
- `php artisan test`
  - hasil: `424` tests pass (`1544` assertions).

Status:
- `PASS` untuk tahap fondasi F1-F4, lanjut ke F5.

## Siklus Pilot Project Pokja IV (F5-F9): 2026-02-21

Ruang lingkup:
- Eksekusi `F5` (HTTP layer), `F6` (Inertia pages), `F7` (PDF render), `F8` (test matrix), `F9` (operational validation).

Artefak:
- `app/Domains/Wilayah/PilotProjectKeluargaSehat/Controllers/*`
- `app/Domains/Wilayah/PilotProjectKeluargaSehat/Requests/*`
- `resources/js/Pages/PilotProjectKeluargaSehat/*`
- `resources/views/pdf/pilot_project_keluarga_sehat_report.blade.php`
- `tests/Feature/DesaPilotProjectKeluargaSehatTest.php`
- `tests/Feature/KecamatanPilotProjectKeluargaSehatTest.php`
- `tests/Feature/PilotProjectKeluargaSehatReportPrintTest.php`
- `tests/Unit/Policies/PilotProjectKeluargaSehatPolicyTest.php`
- `tests/Unit/UseCases/BuildPilotProjectKeluargaSehatReportUseCaseTest.php`

Perintah validasi:
- `php artisan route:list --name=pilot-project-keluarga-sehat`
  - hasil: `16` route terdaftar (desa + kecamatan, termasuk print report).
- `php artisan test tests/Feature/DesaPilotProjectKeluargaSehatTest.php tests/Feature/KecamatanPilotProjectKeluargaSehatTest.php tests/Feature/PilotProjectKeluargaSehatReportPrintTest.php tests/Unit/Policies/PilotProjectKeluargaSehatPolicyTest.php tests/Unit/UseCases/BuildPilotProjectKeluargaSehatReportUseCaseTest.php`
  - hasil: `12` tests pass (`35` assertions).
- `npm run build`
  - hasil: `PASS`.
- `php artisan test`
  - hasil: `436` tests pass (`1579` assertions).

Status:
- `PASS` untuk tahap implementasi F5-F9.

## Siklus Sidebar Grouping by Domain (Sekretaris TPK + Pokja I-IV): 2026-02-22

Ruang lingkup:
- Refactor pengelompokan menu domain pada sidebar:
  - dari struktur berbasis lampiran (`4.14.1`, `4.14.2`, dst)
  - menjadi struktur organisasi `Sekretaris TPK`, `Pokja I`, `Pokja II`, `Pokja III`, `Pokja IV`.
- Scope `desa` dan `kecamatan` diselaraskan.
- Group `Monitoring Kecamatan` dipertahankan untuk `kecamatan`.

Artefak:
- `resources/js/Layouts/DashboardLayout.vue`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md` (section mapping sidebar by domain)
- `docs/domain/DOMAIN_DEVIATION_LOG.md` (`DV-004`)
- `docs/process/SIDEBAR_DOMAIN_GROUPING_PLAN.md`

Perintah validasi:
- `npm run build`
  - hasil: `PASS`.
- `php artisan test`
  - hasil: `446` tests pass (`1604` assertions).

Status:
- `PASS` untuk siklus refactor grouping sidebar by domain.

## Siklus Sidebar Referensi Dokumen Baku: 2026-02-22

Ruang lingkup:
- Menambahkan grup menu `Referensi` pada sidebar domain (`desa` dan `kecamatan`).
- Menyertakan link resmi dokumen baku:
  - `https://pubhtml5.com/zsnqq/vjcf/basic/101-150`
  - `https://pubhtml5.com/zsnqq/vjcf/basic/201-241`
- Memastikan link eksternal aman dibuka pada tab baru dan tetap ergonomis saat sidebar collapsed.

Artefak:
- `resources/js/Layouts/DashboardLayout.vue`
- `docs/process/SIDEBAR_DOMAIN_GROUPING_PLAN.md`

Perintah validasi:
- `npm run build`
  - hasil: `PASS`.
- `php artisan test`
  - hasil: `446` tests pass (`1604` assertions).

Status:
- `PASS` untuk penambahan menu referensi dokumen baku.
