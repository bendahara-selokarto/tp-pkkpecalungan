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
| 4.11 | buku-keuangan | buku-keuangan | 1 | 1 |
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

## Siklus Sidebar Grouping by Domain (Sekretaris TPK + Pokja I-IV): 2026-02-21

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

## Siklus Sidebar Referensi Dokumen Baku: 2026-02-21

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

## Siklus Eksekusi Pending Checklist Markdown: 2026-02-21

Ruang lingkup:
- Menutup item `pending` yang dapat dieksekusi otomatis pada checklist `security/process/pdf`.
- Memperbarui bukti validasi ke hasil command terbaru.

Perintah validasi:
- `php artisan route:list --name=report`
  - hasil: `56` route report.
- `php artisan test --filter=scope_metadata_tidak_sinkron`
  - hasil: `27` test pass (`48` assertions).
- `php artisan test --filter=role_dan_level_area_tidak_sinkron`
  - hasil: `1` test pass (`1` assertion).
- `php artisan test --filter=role_kecamatan_tetapi_area_level_desa`
  - hasil: `20` test pass (`41` assertions).
- `php artisan test --filter=PdfBaselineFixtureComplianceTest`
  - hasil: `20` test pass (`484` assertions).
- `php artisan test --filter=header_kolom_pdf`
  - hasil: `8` test pass (`52` assertions).
- `php artisan test --filter=ReportPrintTest`
  - hasil: `89` test pass (`239` assertions).

Artefak terdampak:
- `docs/security/REGRESSION_CHECKLIST_AUTH_SCOPE.md`
- `docs/process/CHANGE_GATE_DOMAIN_CONTRACT.md`
- `docs/process/RELEASE_CHECKLIST_PDF.md`
- `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`
- `docs/domain/DOMAIN_DEVIATION_LOG.md`

Status:
- `PASS` untuk semua gate otomatis.
- Catatan blocker tersisa: `DV-003` (`4.14.5`) tetap `open` karena sumber canonical belum tersedia.

## Siklus Penyesuaian Autentik 4.14.1a (C1-C4): 2026-02-21

Ruang lingkup:
- Menetapkan ulang kontrak autentik lampiran `4.14.1a` berdasarkan dokumen `d:\pedoman\153.pdf`.
- Menjalankan tahap awal implementasi non-breaking:
  - `C1` update matrix/terminologi/deviasi.
  - `C2` scaffold tabel detail anggota rumah tangga.
  - `C3` backend payload detail anggota + sync repository + summary otomatis.
  - `C4` UI form detail anggota pada halaman `create/edit` desa+kecamatan.

Artefak:
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
- `docs/domain/DOMAIN_DEVIATION_LOG.md` (`DV-005`)
- `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`
- `docs/domain/ADJUSTMENT_PLAN_4_14_1A_DAFTAR_WARGA_TP_PKK.md`
- `database/migrations/2026_02_22_120000_create_data_warga_anggotas_table.php`
- `app/Domains/Wilayah/DataWarga/Models/DataWargaAnggota.php`
- `app/Domains/Wilayah/DataWarga/Models/DataWarga.php`
- `app/Domains/Wilayah/DataWarga/Repositories/DataWargaAnggotaRepositoryInterface.php`
- `app/Domains/Wilayah/DataWarga/Repositories/DataWargaAnggotaRepository.php`
- `app/Domains/Wilayah/DataWarga/Actions/CreateScopedDataWargaAction.php`
- `app/Domains/Wilayah/DataWarga/Actions/UpdateDataWargaAction.php`
- `app/Domains/Wilayah/DataWarga/Requests/StoreDataWargaRequest.php`
- `app/Domains/Wilayah/DataWarga/Requests/UpdateDataWargaRequest.php`
- `tests/Feature/DesaDataWargaTest.php`
- `app/Providers/AppServiceProvider.php`
- `resources/js/admin-one/components/DataWargaAnggotaTable.vue`
- `resources/js/Pages/Desa/DataWarga/Create.vue`
- `resources/js/Pages/Desa/DataWarga/Edit.vue`
- `resources/js/Pages/Kecamatan/DataWarga/Create.vue`
- `resources/js/Pages/Kecamatan/DataWarga/Edit.vue`
- `app/Domains/Wilayah/DataWarga/Controllers/DesaDataWargaController.php`
- `app/Domains/Wilayah/DataWarga/Controllers/KecamatanDataWargaController.php`

Perintah validasi:
- `php artisan test tests/Feature/DesaDataWargaTest.php tests/Feature/KecamatanDataWargaTest.php tests/Feature/DataWargaReportPrintTest.php tests/Unit/UseCases/ListScopedCatatanKeluargaUseCaseTest.php`
  - hasil: `14` test pass (`44` assertions).
- `npm run build`
  - hasil: `PASS`.

Status:
- `PASS` untuk tahap C1-C4 (kontrak + scaffold + backend + form detail non-breaking).
- Catatan:
  - `4.14.1a` pada checklist PDF kini `fail` by design sampai C5 selesai (PDF autentik kolom 1-20).

## Siklus Penyesuaian Autentik 4.14.1a (C5-C7): 2026-02-21

Ruang lingkup:
- Menyelesaikan `C5` (render PDF autentik 4.14.1a) dengan orientasi `portrait`.
- Menutup `C6` (kompatibilitas `catatan-keluarga`) melalui validasi summary turunan detail anggota.
- Menjalankan `C7` (audit dashboard trigger) agar coverage dan chart tetap konsisten.

Artefak:
- `resources/views/pdf/data_warga_report.blade.php`
- `tests/Feature/DataWargaReportPrintTest.php`
- `tests/Fixtures/pdf-baseline/4.14.1a-data-warga.json`
- `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
- `docs/domain/DOMAIN_DEVIATION_LOG.md`
- `docs/domain/ADJUSTMENT_PLAN_4_14_1A_DAFTAR_WARGA_TP_PKK.md`

Perintah validasi:
- `php artisan test tests/Feature/DataWargaReportPrintTest.php tests/Feature/PdfBaselineFixtureComplianceTest.php tests/Feature/DesaDataWargaTest.php tests/Unit/UseCases/ListScopedCatatanKeluargaUseCaseTest.php`
  - hasil: `30` test pass (`552` assertions).
- `php artisan test tests/Feature/DashboardDocumentCoverageTest.php tests/Feature/DashboardActivityChartTest.php`
  - hasil: `7` test pass (`158` assertions).
- `php artisan test`
  - hasil: `447` test pass (`1644` assertions).

Status:
- `PASS` untuk C5-C7.

## Siklus Audit Koherensi Menyeluruh: 2026-02-22

Ruang lingkup:
- Audit lintas kontrak `AGENTS.md -> dokumen domain -> implementasi route/controller/usecase/repository/view/test`.
- Fokus validasi area autentik lampiran `4.16-4.18` dan konsistensi scope/auth.

Perintah validasi:
- `php artisan route:list --name=report`
  - hasil: `80` route report aktif.
- `php artisan route:list --name=catatan-keluarga.rekap-ibu-hamil`
  - hasil: `8` route aktif (desa + kecamatan, 4 varian 4.18).
- `php artisan test --filter=PdfBaselineFixtureComplianceTest`
  - hasil: `20` test pass.
- `php artisan test --filter=RekapCatatanDataKegiatanWargaReportPrintTest`
  - hasil: `16` test pass (`430` assertions).
- `php artisan test --filter=CatatanKeluargaPolicyTest|ScopeLevelTest`
  - hasil: `6` test pass.

Artefak sinkronisasi dari audit:
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
- `docs/domain/DOMAIN_DEVIATION_LOG.md`

Keputusan sinkronisasi:
- Slug teknis 4.18a-4.18d dinormalkan mengikuti route aktif (`rekap-ibu-hamil-*`) untuk mengurangi ambiguitas antara dokumen dan runtime.
- Kontrak agregasi 4.18d dikunci: kolom `3` = RT unik, kolom `4` = penjumlahan dasawisma per RT (sesuai cara pengisian).

Status:
- `PASS` untuk koherensi teknis lintas route/auth/test.
- Ambiguitas non-matematis tersisa:
  - sumber canonical lampiran `4.14.5` yang belum tersedia.

## Update Konfirmasi Canonical 4.16d: 2026-02-22

Ruang lingkup:
- Menutup ambiguity judul canonical lampiran `4.16d` menggunakan bukti visual halaman penuh dari user.

Hasil konfirmasi:
- Judul canonical tervalidasi: `CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK DUSUN/LINGKUNGAN`.
- Drift judul implementasi ditutup:
  - `resources/views/pdf/rekap_catatan_data_kegiatan_warga_rw_report.blade.php`
  - `docs/domain/LAMPIRAN_4_16D_MAPPING.md`
  - `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
  - `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
  - `docs/domain/DOMAIN_DEVIATION_LOG.md` (`DV-006` -> `resolved`)
  - `docs/process/TODO_AUTENTIK_LAMPIRAN_4_16D.md`

Status:
- `PASS` untuk closure ambiguity 4.16d.

## Penutupan TODO Concern B/C/F: 2026-02-22

Ruang lingkup:
- Menutup guardrail assignment `super-admin` pada jalur manajemen user (`Concern B`).
- Menetapkan keputusan canonical tanggal: tetap strict `date_format:Y-m-d` per-request dan trait parser UI lama dinyatakan deprecated (`Concern C`).
- Menutup gate coverage unit direct `183/183` dan lampiran matrix coverage (`Concern F`).

Artefak:
- `app/Support/RoleScopeMatrix.php`
- `app/Http/Requests/User/StoreUserRequest.php`
- `app/Http/Requests/User/UpdateUserRequest.php`
- `app/Actions/User/CreateUserAction.php`
- `app/Actions/User/UpdateUserAction.php`
- `resources/js/Pages/SuperAdmin/Users/Create.vue`
- `resources/js/Pages/SuperAdmin/Users/Edit.vue`
- `tests/Feature/SuperAdmin/UserProtectionTest.php`
- `tests/Feature/SuperAdmin/UserScopePresentationTest.php`
- `tests/Unit/UseCases/User/GetUserManagementFormOptionsUseCaseTest.php`
- `tests/Unit/Actions/User/CreateUserActionTest.php`
- `tests/Unit/Actions/User/UpdateUserActionTest.php`
- `tests/Unit/Architecture/UnitCoverageGateTest.php`
- `tests/Unit/Http/Requests/DateInputCanonicalGuardTest.php`
- `docs/process/UNIT_DIRECT_COVERAGE_MATRIX_2026_02_22.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`

Perintah validasi:
- `php artisan test --filter "UserProtectionTest|GetUserManagementFormOptionsUseCaseTest|SuperAdminAuthorizationTest|UserScopePresentationTest|CreateUserActionTest|UpdateUserActionTest|DateInputCanonicalGuardTest|UnitCoverageGateTest"`
  - hasil: `208` test pass (`644` assertions).
- `php artisan route:list --except-vendor`
  - hasil: `462` route aktif.
- `php artisan migrate:fresh --seed --no-interaction`
  - hasil: `PASS`.
- `php artisan test`
  - hasil: `667` test pass (`2703` assertions).

Status:
- `PASS` untuk closure concern B/C/F pada gate validasi teknis.

## Normalisasi Label UI Administratif: 2026-02-22

Ruang lingkup:
- Menormalkan teks user-facing agar slug teknis role/scope tidak tampil mentah di UI.
- Mengunci keputusan canonical normalisasi label role/scope/wilayah pada dokumen terminology map.

Artefak:
- `resources/js/utils/roleLabelFormatter.js`
- `resources/js/Pages/SuperAdmin/Users/Index.vue`
- `resources/js/Pages/SuperAdmin/Users/Create.vue`
- `resources/js/Pages/SuperAdmin/Users/Edit.vue`
- `resources/js/Layouts/DashboardLayout.vue`
- `resources/js/admin-one/layouts/LayoutGuest.vue`
- `app/Http/Controllers/SuperAdmin/UserManagementController.php`
- `tests/Feature/SuperAdminAuthorizationTest.php`
- `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`

Perintah validasi:
- `php artisan test --filter "SuperAdminAuthorizationTest|UserScopePresentationTest|UserProtectionTest"`
  - hasil: `12` test pass (`72` assertions).

Status:
- `PASS` untuk normalisasi label UI administratif dan sinkronisasi canonical.

## Siklus Audit UI Chart Dashboard: 2026-02-22

Ruang lingkup:
- Audit chart dashboard:
  - `Cakupan per Lampiran`
  - `Distribusi Level Data Dokumen`
- Verifikasi end-to-end dari payload backend sampai render UI.

Artefak:
- `app/Http/Controllers/DashboardController.php`
- `app/Domains/Wilayah/Dashboard/UseCases/BuildDashboardDocumentCoverageUseCase.php`
- `app/Domains/Wilayah/Dashboard/Repositories/DashboardDocumentCoverageRepository.php`
- `resources/js/Pages/Dashboard.vue`
- `resources/js/admin-one/components/Charts/BarChart.vue`
- `docs/process/DASHBOARD_CHART_ALIGNMENT_PLAN.md`

Perintah audit/validasi:
- `php artisan tinker` (sample user scope `kecamatan` dan `desa`) untuk cek:
  - `stats`
  - `charts.coverage_per_lampiran`
  - `charts.level_distribution`
  - hasil: payload chart terisi pada kedua scope.
- `php artisan tinker` (seluruh user non-super-admin) untuk cek agregat nol:
  - indikator: `sum(coverage_per_lampiran.values)` dan `sum(level_distribution.values)`.
  - hasil: tidak ada user dengan payload chart kosong total.
- `npm run build`
  - hasil: `PASS`.

Keputusan:
- Tidak ada bug query backend pada chart dokumen.
- Perbaikan difokuskan di UI:
  - tambah daftar item numerik per chart,
  - tambah empty-state message saat seluruh nilai bernilai `0`.

Status:
- `PASS` untuk audit end-to-end chart dashboard.

## Siklus Normalisasi Label Chart Cakupan per Buku: 2026-02-22

Ruang lingkup:
- Mengubah label item chart `Cakupan per Buku` dari slug `kebab-case` menjadi label manusia.

Artefak:
- `resources/js/Pages/Dashboard.vue`

Keputusan implementasi:
- Prioritas label chart:
  - gunakan `coverage_per_buku.items[].label` jika tersedia (label canonical backend),
  - fallback ke humanize slug (`kebab-case`/`snake_case` -> Title Case).
- Nilai chart tetap mengambil total asli tanpa perubahan agregasi.

Perintah validasi:
- `npm run build`
  - hasil: `PASS`.

Status:
- `PASS` untuk normalisasi label chart `Cakupan per Buku`.

## Migrasi Role Legacy ke Sekretaris: 2026-02-23

Ruang lingkup:
- Menutup concern `R6` pada visibility role-aware dengan migrasi assignment role legacy user aktif.
- Menjaga kompatibilitas role lama di matrix akses, tetapi menghentikan assignment aktif `admin-*`/`*bendahara`.

Artefak:
- `database/seeders/MigrateLegacyRoleAssignmentsSeeder.php`
- `database/seeders/DatabaseSeeder.php`
- `docs/process/TODO_UI_VISIBILITY_BY_PENANGGUNGJAWAB.md`

Aturan migrasi:
- `admin-desa` dan `desa-bendahara` -> `desa-sekretaris`.
- `admin-kecamatan` dan `kecamatan-bendahara` -> `kecamatan-sekretaris`.
- Jika `scope` user valid, target role mengikuti `scope` agar tidak drift dengan level area.

Perintah validasi:
- `php artisan db:seed --class=MigrateLegacyRoleAssignmentsSeeder --no-interaction`
  - hasil: `PASS`.
- `php artisan db:seed --class=DatabaseSeeder --no-interaction`
  - hasil: `PASS` (chain default memanggil seeder migrasi baru).
- `php -r "..."` (bootstrap Laravel + hitung user dengan role legacy)
  - hasil: `0` user dengan role `admin-desa/admin-kecamatan/desa-bendahara/kecamatan-bendahara`.
- `php artisan test`
  - hasil: `724` test pass (`3221` assertions).

Status:
- `PASS` untuk migrasi role legacy jalur seeder.

## Hardening Arsitektur Jalur Tunggal AI: 2026-02-23

Ruang lingkup:
- Menetapkan arsitektur `zero ambiguity` untuk routing kerja AI lintas concern.
- Mengunci satu jalur operasional deterministik untuk sesi AI berikutnya.

Artefak:
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/TODO_ZERO_AMBIGUITY_AI_SINGLE_PATH_2026_02_23.md`
- `AGENTS.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`

Perintah audit/validasi:
- `rg -n "AI_SINGLE_PATH_ARCHITECTURE" AGENTS.md docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md docs/process/TODO_ZERO_AMBIGUITY_AI_SINGLE_PATH_2026_02_23.md`
  - hasil: referensi silang terdeteksi dan konsisten.
- `git status --short`
  - hasil: perubahan hanya pada dokumen arsitektur/proses concern ini.
- `php artisan test --filter=ExampleTest`
  - hasil: `PASS` (`2` test, `3` assertions).

Keputusan:
- Dokumen `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md` dikunci sebagai rute operasional default AI.
- `AGENTS.md` diperbarui agar prioritas dokumen memasukkan single-path architecture.
- Playbook menambahkan pattern `P-017` untuk memastikan mekanisme ini reusable lintas sesi.

Status:
- `PASS` untuk doc-hardening concern zero-ambiguity single path.

## Eksekusi User Guide U2 dan U4: 2026-02-24

Ruang lingkup:
- Menjalankan audit role-flow (`U2`) untuk memastikan topik user guide sinkron dengan perilaku route dan visibilitas backend.
- Membuat skeleton dokumen user guide (`U4`) dengan copywriting natural-humanis sebagai fondasi konten.

Artefak:
- `docs/process/TODO_USER_GUIDE_NATURAL_HUMANIS_2026_02_24.md`
- `docs/process/USER_GUIDE_ROLE_FLOW_AUDIT_2026_02_24.md`
- `docs/user-guide/README.md`
- `docs/user-guide/mulai-cepat.md`
- `docs/user-guide/peran/sekretaris-desa.md`
- `docs/user-guide/peran/sekretaris-kecamatan.md`
- `docs/user-guide/peran/pokja-desa.md`
- `docs/user-guide/peran/pokja-kecamatan.md`
- `docs/user-guide/peran/super-admin.md`
- `docs/user-guide/alur/kelola-data-harian.md`
- `docs/user-guide/alur/filter-dashboard-dan-membaca-grafik.md`
- `docs/user-guide/alur/cetak-dan-ekspor-laporan.md`
- `docs/user-guide/faq.md`
- `docs/README.md`

Perintah audit/validasi:
- `rg -n "scope.role|module.visibility|Route::prefix\\('desa'\\)|Route::prefix\\('kecamatan'\\)" routes/web.php`
  - hasil: rute scope desa/kecamatan dan middleware visibilitas terdeteksi.
- `php artisan test --filter=ExampleTest`
  - hasil: `PASS` (`2` test, `3` assertions).

Keputusan:
- Struktur user guide per peran dan per alur dinyatakan valid untuk fase penulisan konten berikutnya.
- Bahasa skeleton menggunakan gaya natural-humanis dan menghindari istilah teknis internal sebagai label utama.

Status:
- `PASS` untuk eksekusi `U2` dan `U4`.

## User Guide Cetak Pilot (Login): 2026-02-24

Ruang lingkup:
- Menyiapkan dokumen user guide versi siap cetak untuk langkah login dengan screenshot aktual.
- Merapikan lokasi screenshot agar konsisten (`screenshots`).

Artefak:
- `docs/user-guide/screenshots/01-login.png`
- `docs/user-guide/print/README.md`
- `docs/user-guide/print/01-login-siap-cetak.html`
- `docs/user-guide/README.md`
- `docs/README.md`
- `docs/process/TODO_USER_GUIDE_NATURAL_HUMANIS_2026_02_24.md`

Perintah audit/validasi:
- `Get-ChildItem docs/user-guide -Recurse -File`
  - hasil: file screenshot dan dokumen print terdeteksi pada lokasi target.
- `php artisan test --filter=ExampleTest`
  - hasil: `PASS` (`2` test, `3` assertions).

Keputusan:
- Format HTML A4 dipakai sebagai baseline dokumen siap cetak.
- Penamaan aset screenshot distandardkan ke pola `NN-nama.png`.

Status:
- `PASS` untuk pilot dokumen cetak login.

## User Guide Cetak Lengkap Berurutan (Tanpa Screenshot): 2026-02-24

Ruang lingkup:
- Menyediakan satu dokumen cetak gabungan berurutan untuk seluruh konten user guide.
- Tidak menyertakan screenshot agar dokumen fokus ke instruksi teks.

Artefak:
- `docs/user-guide/print/00-user-guide-lengkap-siap-cetak.html`
- `docs/user-guide/print/README.md`
- `docs/user-guide/README.md`
- `docs/process/TODO_USER_GUIDE_NATURAL_HUMANIS_2026_02_24.md`

Perintah audit/validasi:
- `Get-ChildItem docs/user-guide/print -File`
  - hasil: dokumen cetak gabungan terdeteksi.
- `php artisan test --filter=ExampleTest`
  - hasil: `PASS` (`2` test, `3` assertions).

Keputusan:
- Dokumen `00-user-guide-lengkap-siap-cetak.html` ditetapkan sebagai file utama untuk cetak cepat seluruh panduan.

Status:
- `PASS` untuk dokumen cetak gabungan tanpa screenshot.

## Penegasan Retensi Screenshot Login: 2026-02-24

Ruang lingkup:
- Menjaga agar dokumen login bergambar tetap tersedia saat dokumen cetak gabungan dipakai sebagai dokumen utama.

Artefak:
- `docs/user-guide/print/README.md`
- `docs/user-guide/print/00-user-guide-lengkap-siap-cetak.html`
- `docs/user-guide/README.md`

Keputusan:
- `01-login-siap-cetak.html` dan `screenshots/01-login.png` tetap dipertahankan sebagai referensi visual.

Status:
- `PASS` untuk retensi screenshot login.

## Penyesuaian Dokumen Cetak Menjadi 1 Gambar: 2026-02-24

Ruang lingkup:
- Menyusun dokumen cetak gabungan agar hanya memuat satu gambar referensi visual (halaman login).

Artefak:
- `docs/user-guide/print/00-user-guide-lengkap-siap-cetak.html`
- `docs/user-guide/print/README.md`
- `docs/user-guide/README.md`
- `docs/process/TODO_USER_GUIDE_NATURAL_HUMANIS_2026_02_24.md`

Keputusan:
- Dokumen gabungan cetak ditetapkan memuat tepat 1 gambar (`screenshots/01-login.png`).

Status:
- `PASS` untuk penyusunan dokumen dengan satu gambar.

## Doc-Hardening Pending TODO Dashboard: 2026-02-24

Ruang lingkup:
- Menutup item `pending` yang sudah stale pada TODO dashboard.
- Menyinkronkan status implementasi `section 4` skenario kecamatan Pokja I dengan implementasi aktual.
- Menormalkan kontrak daftar modul Pokja I agar sinkron dengan implementasi terbaru (`paar`).

Artefak:
- `docs/process/TODO_SCENARIO_KECAMATAN_SECTION4_POKJA_I_2026_02_23.md`
- `docs/process/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`
- `docs/process/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`
- `docs/process/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
- `docs/process/TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md`

Perintah validasi:
- `php artisan test tests/Feature/DesaPaarTest.php tests/Feature/KecamatanPaarTest.php tests/Feature/PaarReportPrintTest.php tests/Unit/Policies/PaarPolicyTest.php tests/Feature/DashboardDocumentCoverageTest.php tests/Unit/Dashboard/DashboardCoverageMenuSyncTest.php tests/Unit/Architecture/UnitCoverageGateTest.php`
  - hasil: `PASS` (`230` tests).
- `php artisan test`
  - hasil: `PASS` (`765` tests).

Keputusan:
- Item stale untuk implementasi `section 4` Pokja I ditutup.
- Checklist validasi dashboard yang sudah terverifikasi oleh test ditandai selesai.
- Sinkronisasi lintas TODO dashboard (`Akses`, `UI Dinamis`, `Minimalis`, `Lintas Role`) dikunci sebagai hasil doc-hardening concern ini.

Status:
- `PASS` untuk doc-hardening pending TODO dashboard.

## Doc-Hardening Pending TODO Dashboard (Pass-2): 2026-02-24

Ruang lingkup:
- Menyelesaikan sinkronisasi status TODO dashboard yang masih `planned` padahal sebagian langkah sudah dieksekusi.
- Menormalkan checklist residual pada concern yang sudah `done` agar tidak tercatat sebagai pending stale.

Artefak:
- `docs/process/TODO_SCENARIO_KECAMATAN_SECTION4_POKJA_I_2026_02_23.md`
- `docs/process/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
- `docs/process/TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md`

Keputusan:
- TODO `section 4` tetap `done`; item risiko/mitigasi residual diubah menjadi catatan non-checklist agar tidak menjadi false pending.
- TODO `refactor dashboard minimalis` dan `refactor dashboard lintas role` dinaikkan status ke `in-progress` karena sebagian task hardening sudah selesai.

Status:
- `PASS` untuk sinkronisasi status dan penutupan stale pending pass-2.

## Doc-Hardening Pending TODO Dashboard (Pass-3): 2026-02-24

Ruang lingkup:
- Menormalkan checklist validasi yang masih terlalu agregat pada TODO dashboard akses.
- Memisahkan status validasi `sekretaris` (sudah teruji) dan `pokja` (masih pending) agar tidak mencampur progress.

Artefak:
- `docs/process/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`

Keputusan:
- Item `Feature test role valid` dipecah menjadi dua checklist:
  - jalur sekretaris ditandai selesai,
  - jalur pokja tetap pending sampai test khusus tersedia.

Status:
- `PASS` untuk hardening granular checklist validasi dashboard akses.

## Implementasi Semua TODO Pending (Dashboard + User Guide + Normalisasi): 2026-02-24

Ruang lingkup:
- Menutup seluruh checklist `pending` pada seluruh dokumen `docs/process/TODO*.md`.
- Menyelesaikan gap implementasi dashboard concern `D4` (repository per grup) + hardening UI + observability runtime.
- Menyelesaikan fase konten user guide natural-humanis.
- Menjalankan audit normalisasi database dan sinkronisasi dokumen terkait.

Artefak utama:
- `app/Domains/Wilayah/Dashboard/Repositories/DashboardGroupCoverageRepositoryInterface.php`
- `app/Domains/Wilayah/Dashboard/Repositories/DashboardGroupCoverageRepository.php`
- `app/Domains/Wilayah/Dashboard/Repositories/DashboardDocumentCoverageRepository.php`
- `app/Domains/Wilayah/Dashboard/Repositories/DashboardDocumentCoverageRepositoryInterface.php`
- `app/Domains/Wilayah/Dashboard/UseCases/BuildRoleAwareDashboardBlocksUseCase.php`
- `app/Services/DashboardActivityChartService.php`
- `resources/js/Pages/Dashboard.vue`
- `resources/js/app.js`
- `app/Http/Controllers/UiRuntimeErrorLogController.php`
- `routes/web.php`
- `tests/Feature/DashboardDocumentCoverageTest.php`
- `tests/Unit/Dashboard/DashboardGroupCoverageRepositoryTest.php`
- `tests/Feature/UiRuntimeErrorLogTest.php`
- `tests/Unit/Architecture/UnitCoverageGateTest.php`
- `docs/user-guide/*`
- `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
- `docs/process/NORMALISASI_DATABASE_AUDIT_2026_02_24.md`
- `docs/process/TODO_*` concern terkait dashboard/user-guide/normalisasi/runtime

Perintah validasi:
- `php artisan test --filter=DashboardDocumentCoverageTest` -> `PASS` (9 tests).
- `php artisan test --filter=DashboardActivityChartTest` -> `PASS` (5 tests).
- `php artisan test --filter=DashboardGroupCoverageRepositoryTest` -> `PASS` (2 tests).
- `php artisan test --filter=UiRuntimeErrorLogTest` -> `PASS` (2 tests).
- `npm run build` -> `PASS` (bundle ApexCharts < 500 kB warning threshold).
- `php artisan test` -> `PASS` (`773` tests, `3450` assertions).
- `rg -n "^- \[ \]" docs/process --glob "TODO*.md"` -> tidak ada pending checklist.

Keputusan:
- Concern dashboard akses/minimalis/lintas-role ditutup pada batch ini.
- Concern user guide natural-humanis ditutup dengan konten fase 1+2 + glossary canonical.
- Concern normalisasi database ditutup dengan audit terukur; tidak ada patch migrasi darurat yang dibutuhkan.

Status:
- `PASS` untuk penutupan seluruh pending TODO concern aktif.

## Eksekusi TODO Audit Role Ownership: 2026-02-25

Ruang lingkup:
- Menutup checklist pending pada `TODO_AUDIT_MODUL_ROLE_OWNERSHIP_2026_02_25.md`.
- Menandai modul mismatch role pada kolom `Checklist Perbaikan Role`.
- Mengisi `Catatan Audit` eksplisit per modul mismatch.
- Membuat concern implementasi terpisah per kelompok perubahan role.

Artefak:
- `docs/process/TODO_AUDIT_MODUL_ROLE_OWNERSHIP_2026_02_25.md`
- `docs/process/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_POKJA_DESA_ONLY_2026_02_25.md`
- `docs/process/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_NON_RW_RO_2026_02_25.md`
- `docs/process/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_DEPRECATE_DATA_PELATIHAN_KADER_2026_02_25.md`

Perintah audit/validasi scoped:
- `rg -n "RoleMenuVisibilityService|module.visibility|scope.role" app routes`
  - hasil: source of truth backend untuk audit ownership tervalidasi.
- `rg -n -- "- \\[ \\]" docs/process/TODO_AUDIT_MODUL_ROLE_OWNERSHIP_2026_02_25.md`
  - hasil: item checklist global audit role ownership tidak lagi pending.

Keputusan:
- Mismatch ownership dikelompokkan menjadi 3 concern implementasi: `pokja desa-only`, `non RW/RO`, dan `deprecate data-pelatihan-kader`.
- Perubahan runtime akses belum dieksekusi pada batch ini; concern implementasi disiapkan untuk approval domain sebelum patch backend.

Status:
- `PASS` untuk penutupan TODO audit ownership pada level dokumentasi + concern planning.

## Eksekusi Bertahap Pending Sidebar + Role Ownership: 2026-02-25

Ruang lingkup:
- Menutup pending checklist `SIDEBAR_DOMAIN_GROUPING_PLAN.md` yang bisa dieksekusi otomatis.
- Menjalankan concern implementasi role ownership:
  - `pokja desa-only` (runtime change + test),
  - `non RW/RO` (keputusan owner final batch ini),
  - `data-pelatihan-kader` (keputusan retain sementara).
- Menjalankan validasi test targeted + build frontend.

Artefak:
- `resources/js/Layouts/DashboardLayout.vue`
- `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- `tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
- `tests/Feature/ModuleVisibilityMiddlewareTest.php`
- `docs/process/SIDEBAR_DOMAIN_GROUPING_PLAN.md`
- `docs/process/DASHBOARD_CHART_ALIGNMENT_PLAN.md`
- `docs/process/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_POKJA_DESA_ONLY_2026_02_25.md`
- `docs/process/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_NON_RW_RO_2026_02_25.md`
- `docs/process/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_DEPRECATE_DATA_PELATIHAN_KADER_2026_02_25.md`

Perintah validasi:
- `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php`
  - hasil: `PASS` (`12` tests).
- `php artisan test tests/Feature/MenuVisibilityPayloadTest.php tests/Feature/DashboardDocumentCoverageTest.php`
  - hasil: `PASS` (`12` tests).
- `php artisan test --filter=RoleMenuVisibilityService`
  - hasil: `PASS` (`5` tests).
- `php artisan test --filter=scope_metadata_tidak_sinkron`
  - hasil: `PASS` (`30` tests).
- `php artisan test --filter=role_dan_level_area_tidak_sinkron`
  - hasil: `PASS` (`1` test).
- `php artisan test --filter=DataPelatihanKader`
  - hasil: `PASS` (`20` tests).
- `php artisan test --filter=CatatanKeluargaPolicyTest`
  - hasil: `PASS` (`2` tests).
- `php artisan test --filter=PilotProjectKeluargaSehatPolicyTest`
  - hasil: `PASS` (`2` tests).
- `php artisan route:list --name=data-pelatihan-kader`
  - hasil: `PASS` (`16` route aktif).
- `npm run build`
  - percobaan awal: `FAIL` (import `vue3-apexcharts` tidak ter-resolve).
  - tindakan: `npm install`.
  - percobaan ulang: `PASS`.

Catatan blocker:
- `php artisan test` penuh pada environment ini tidak selesai:
  - percobaan default memory gagal (`Allowed memory size of 134217728 bytes exhausted`),
  - percobaan `php -d memory_limit=512M artisan test` terhenti karena timeout sesi.
- Checklist yang benar-benar manual/time-based tetap pending:
  - monitor feedback user 1 siklus operasional,
  - validasi manual UX multi-breakpoint,
  - smoke test manual pagination lintas modul.

Status:
- `PASS` untuk seluruh concern otomatis pada batch ini.
- `PENDING` hanya pada gate full test dan gate manual/time-based yang belum dapat ditutup otomatis.
