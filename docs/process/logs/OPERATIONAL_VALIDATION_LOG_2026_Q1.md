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

## Hardening Kontrak Akses Arsip Global/Pribadi: 2026-02-28

Ruang lingkup:

- Mengunci kontrak akses arsip:
  - arsip `global` (unggahan `super-admin`) visible semua role,
  - arsip private non `super-admin` tetap owner-managed,
  - monitoring arsip desa untuk kecamatan mengikuti pola dual-scope concern `activities`.
- Menyamakan mekanisme UI monitoring arsip dengan menu kegiatan (toggle di halaman concern, bukan entry sidebar langsung).

Artefak:

- `app/Domains/Wilayah/Arsip/Repositories/ArsipDocumentRepository.php`
- `app/Http/Controllers/ArsipController.php`
- `app/Domains/Wilayah/Arsip/UseCases/ResolveArsipDocumentDownloadUseCase.php`
- `resources/js/Pages/Arsip/Index.vue`
- `resources/js/Layouts/DashboardLayout.vue`
- `tests/Feature/ArsipTest.php`
- `docs/process/archive/2026_02/TODO_ARS26B2_HARDENING_AKSES_ARSIP_GLOBAL_PRIBADI_2026_02_28.md`
- `docs/process/archive/2026_02/TODO_ASM26B1_MANAGEMENT_ARSIP_SUPER_ADMIN_2026_02_27.md`
- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
- `docs/process/archive/2026_02/TODO_MONITORING_VISIBILITY_SEMUA_MODUL_2026_02_27.md`
- `docs/process/archive/2026_02/TODO_SKC0201_ROADMAP_SEKRETARIS_KECAMATAN_2026_02_28.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`

Perintah validasi:

- `php artisan test tests/Feature/ArsipTest.php tests/Feature/KecamatanDesaArsipTest.php tests/Feature/SuperAdmin/ArsipManagementTest.php tests/Unit/Policies/ArsipDocumentPolicyTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/MenuVisibilityPayloadTest.php`
  - hasil: `PASS` (`32` tests, `287` assertions).
- `php artisan test`
  - hasil: `PASS`.

Keputusan:

- Bypass `Gate::before` untuk download private arsip ditutup pada jalur `/arsip/download` dengan evaluasi policy langsung.
- Mutasi private arsip via jalur `/arsip` dipaksa owner-only.
- Concern `arsip` dinyatakan reuse pattern `P-020` (dual-scope kecamatan vs desa monitoring).

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

## Siklus Sidebar Grouping by Domain (Sekretaris TP-PKK + Pokja I-IV): 2026-02-21

Ruang lingkup:

- Refactor pengelompokan menu domain pada sidebar:
  - dari struktur berbasis lampiran (`4.14.1`, `4.14.2`, dst)
  - menjadi struktur organisasi `Sekretaris TP-PKK`, `Pokja I`, `Pokja II`, `Pokja III`, `Pokja IV`.
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
  - `docs/process/archive/undated/TODO_AUTENTIK_LAMPIRAN_4_16D.md`

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
- `docs/process/archive/undated/TODO_UI_VISIBILITY_BY_PENANGGUNGJAWAB.md`

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
- `docs/process/archive/2026_02/TODO_ZERO_AMBIGUITY_AI_SINGLE_PATH_2026_02_23.md`
- `AGENTS.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`

Perintah audit/validasi:

- `rg -n "AI_SINGLE_PATH_ARCHITECTURE" AGENTS.md docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md docs/process/archive/2026_02/TODO_ZERO_AMBIGUITY_AI_SINGLE_PATH_2026_02_23.md`
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

- `docs/process/archive/2026_02/TODO_USER_GUIDE_NATURAL_HUMANIS_2026_02_24.md`
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
- `docs/process/archive/2026_02/TODO_USER_GUIDE_NATURAL_HUMANIS_2026_02_24.md`

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
- `docs/process/archive/2026_02/TODO_USER_GUIDE_NATURAL_HUMANIS_2026_02_24.md`

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
- `docs/process/archive/2026_02/TODO_USER_GUIDE_NATURAL_HUMANIS_2026_02_24.md`

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

- `docs/process/archive/2026_02/TODO_SCENARIO_KECAMATAN_SECTION4_POKJA_I_2026_02_23.md`
- `docs/process/archive/2026_02/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`
- `docs/process/archive/2026_02/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`
- `docs/process/archive/2026_02/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
- `docs/process/archive/2026_02/TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md`

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

- `docs/process/archive/2026_02/TODO_SCENARIO_KECAMATAN_SECTION4_POKJA_I_2026_02_23.md`
- `docs/process/archive/2026_02/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
- `docs/process/archive/2026_02/TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md`

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

- `docs/process/archive/2026_02/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`

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

- `docs/process/archive/2026_02/TODO_AUDIT_MODUL_ROLE_OWNERSHIP_2026_02_25.md`
- `docs/process/archive/2026_02/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_POKJA_DESA_ONLY_2026_02_25.md`
- `docs/process/archive/2026_02/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_NON_RW_RO_2026_02_25.md`
- `docs/process/archive/2026_02/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_DEPRECATE_DATA_PELATIHAN_KADER_2026_02_25.md`

Perintah audit/validasi scoped:

- `rg -n "RoleMenuVisibilityService|module.visibility|scope.role" app routes`
  - hasil: source of truth backend untuk audit ownership tervalidasi.
- `rg -n -- "- \\[ \\]" docs/process/archive/2026_02/TODO_AUDIT_MODUL_ROLE_OWNERSHIP_2026_02_25.md`
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
- `docs/process/archive/2026_02/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_POKJA_DESA_ONLY_2026_02_25.md`
- `docs/process/archive/2026_02/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_NON_RW_RO_2026_02_25.md`
- `docs/process/archive/2026_02/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_DEPRECATE_DATA_PELATIHAN_KADER_2026_02_25.md`

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

## Hardening Command Test Memory Limit: 2026-02-25

Ruang lingkup:

- Menstandarkan command full test agar tidak bergantung `php.ini` lokal (default `128M`).
- Menyamakan command test gate di CI untuk mencegah error memory sporadis.

Artefak:

- `composer.json`
- `.github/workflows/domain-contract-gate.yml`
- `.github/pull_request_template.md`
- `docs/process/COMMAND_NUMBER_SHORTCUTS.md`

Keputusan:

- Command canonical full test dikunci menjadi:
  - `php -d memory_limit=512M artisan test --compact`
- Composer script ditambah:
  - `composer test:full`
  - `composer test:full:unit`
  - `composer test:full:feature`

Status:

- `PASS` untuk hardening command dan sinkronisasi dokumen/process template.

## Verifikasi Pasca Hardening Memory Limit: 2026-02-25

Ruang lingkup:

- Memverifikasi eksekusi test suite setelah hardening memory-limit pada command + `phpunit.xml`.
- Memastikan error `Allowed memory size of 134217728 bytes exhausted` tidak lagi menjadi blocker utama.

Perintah validasi:

- `composer test:full:unit`
  - hasil: `PASS` (`314` tests).
- `composer test:full:feature`
  - hasil: tidak ada lagi error memory-limit.
  - suite berhenti pada `4` kegagalan non-memory:
    - `LaporanTahunanPkkReportPrintTest` (`2` fail): template `.docx` tidak ditemukan.
    - `PdfBaselineFixtureComplianceTest` (`2` fail): mismatch token judul fixture untuk `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` dan `data-industri-rumah-tangga`.

Keputusan:

- Gate memory-limit dinyatakan `stabil`: eksekusi sudah tidak pecah oleh limit `128M`.
- Blocker tersisa dipindahkan ke concern terpisah `fixture/template consistency` karena tidak terkait kapasitas memory.

Status:

- `PASS` untuk concern memory-limit.
- `PENDING` untuk 4 kegagalan regresi non-memory pada suite feature.

## Penutupan Pending Checklist 11-15 + Pagination Backlog: 2026-02-25

Ruang lingkup:

- Menutup pending checklist hasil audit task aktif:
  - `docs/process/archive/2026_02/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_POKJA_DESA_ONLY_2026_02_25.md`
  - `docs/process/archive/2026_02/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_NON_RW_RO_2026_02_25.md`
  - `docs/process/archive/2026_02/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_DEPRECATE_DATA_PELATIHAN_KADER_2026_02_25.md`
  - `docs/process/archive/2026_02/TODO_UI_PAGINATION_E2E_2026_02_24.md`
  - `docs/process/SIDEBAR_DOMAIN_GROUPING_PLAN.md`
  - `docs/process/DASHBOARD_CHART_ALIGNMENT_PLAN.md`

Validasi yang dijalankan:

- `php artisan test`
  - hasil: `PASS` (`850` tests, `5363` assertions).
- `npm install`
  - hasil: `PASS` (sinkronisasi dependency frontend).
- `npm run build`
  - hasil: `PASS` (build produksi Vite hijau).

Keputusan:

- Checklist item 11, 12, 13 (`php artisan test` penuh) dikunci `done`.
- Sisa pending pagination concern (`TamanBacaan`, `Koperasi`, `KejarPaket`, `WarungPkk`, `Posyandu`, `SimulasiPenyuluhan`, `ProgramPrioritas`, `PilotProjectNaskahPelaporan`, `PilotProjectKeluargaSehat`, smoke test concern) disinkronkan menjadi `done`.
- Checklist residual pada concern sidebar dan dashboard chart alignment disinkronkan menjadi `done`.

Status:

- `PASS` untuk penutupan pending checklist concern aktif pada batch ini.

## Siklus Monitoring Visibility Modul Kegiatan: 2026-02-27

Ruang lingkup:

- Menjalankan baseline monitoring khusus modul `activities` dan `desa-activities`.
- Memastikan kontrak visibilitas role-scope sinkron antara backend dan menu frontend.

Artefak:

- `docs/process/MONITORING_VISIBILITY_MODUL.md`
- `docs/process/archive/2026_02/TODO_MONITORING_VISIBILITY_MODUL_KEGIATAN_2026_02_27.md`
- `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- `app/Http/Middleware/EnsureModuleVisibility.php`
- `app/Domains/Wilayah/Activities/Services/ActivityScopeService.php`
- `resources/js/Layouts/DashboardLayout.vue`

Perintah validasi:

- `php artisan test tests/Feature/DesaActivityTest.php tests/Feature/KecamatanActivityTest.php tests/Feature/KecamatanDesaActivityTest.php tests/Feature/ActivityPrintTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Unit/Policies/ActivityPolicyTest.php tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
  - hasil: `PASS` (`55` tests, `371` assertions).

Keputusan:

- Kontrak `activities` dikunci tersedia untuk seluruh role operasional pada scope validnya.
- Kontrak `desa-activities` dipertahankan sebagai monitoring kecamatan.
- Gate monitoring visibility modul kegiatan ditetapkan aktif untuk setiap perubahan `add/remove/change-mode`.

Status:

- `PASS`.

## Siklus Monitoring Visibility Semua Modul: 2026-02-27

Ruang lingkup:

- Menaikkan target monitoring visibility dari modul kegiatan ke seluruh modul aktif.
- Mengunci baseline inventory slug modul dan profil visibility per role berdasarkan source of truth backend.

Artefak:

- `docs/process/archive/2026_02/TODO_MONITORING_VISIBILITY_SEMUA_MODUL_2026_02_27.md`
- `docs/process/archive/2026_02/TODO_MONITORING_VISIBILITY_MODUL_KEGIATAN_2026_02_27.md`
- `docs/process/MONITORING_VISIBILITY_MODUL.md`
- `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- `app/Http/Middleware/EnsureModuleVisibility.php`
- `resources/js/Layouts/DashboardLayout.vue`

Perintah validasi:

- `php artisan test`
  - hasil: `PASS` (`925` tests, `5750` assertions).

Keputusan:

- Gate monitoring visibility lintas semua modul dinyatakan aktif.
- Concern kegiatan tetap dipertahankan sebagai sub-scope agar detail guard `activities` tidak hilang.

Status:

- `PASS`.

## Implementasi Gate Monitoring Visibility Semua Modul: 2026-02-27

Ruang lingkup:

- Mengimplementasikan gate test kontrak global untuk memastikan inventory slug modul, profil visibility role-scope, dan keterpetaan route per slug tetap stabil.

Artefak:

- `tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php`
- `docs/process/MONITORING_VISIBILITY_MODUL.md`
- `docs/process/archive/2026_02/TODO_MONITORING_VISIBILITY_SEMUA_MODUL_2026_02_27.md`

Perintah validasi:

- `php artisan test tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
  - hasil: `PASS` (`28` tests, `322` assertions).

Keputusan:

- Gate global visibility dinyatakan aktif pada level test otomatis.
- Perubahan add/remove/change-mode modul wajib melewati test kontrak global sebelum merge.

Status:

- `PASS`.

## Hardening Process Routing + Model Tier: 2026-03-01

Ruang lingkup:

- Menetapkan `Self-Reflective Routing` dan aturan tier model kompleksitas.

Artefak:

- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/archive/2026_03/TODO_SRR26A1_SELF_REFLECTIVE_ROUTING_2026_03_01.md`
- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
- `docs/process/archive/2026_02/TODO_ZERO_AMBIGUITY_AI_SINGLE_PATH_2026_02_23.md`
- `docs/adr/ADR_0003_SELF_REFLECTIVE_ROUTING.md`

Perintah validasi:

- `rg "P-022|Self-Reflective Routing|Self-Reflective Checkpoint" docs/process docs/adr` -> `PASS`.
- `rg "low.*small model|medium.*mid model|high.*large model" docs/process docs/adr` -> `PASS`.

Keputusan:

- `P-022` tetap `active`.
- Checkpoint refleksi maksimal satu koreksi rute utama per concern.
- Tier model dikunci: `low -> small`, `medium -> mid`, `high -> large`.

Status:

- `PASS`.

```dsl
LOG_SCOPE: process_routing_model_tier_hardening
PATTERN: P-022
MODEL_TIER_MAP: low=small, medium=mid, high=large
ROUTE_CORRECTION_LIMIT: 1
VALIDATION: rg_pattern_sync=PASS, rg_model_tier_sync=PASS
STATUS: PASS
```

## Optimasi Bottleneck Process Execution (Exec 1-3): 2026-03-01

Ruang lingkup:

- Menetapkan threshold agar `P-021` tidak dipakai untuk perubahan minor `doc-only`.
- Menambahkan jalur validasi cepat `doc-only fast lane` pada single-path.
- Memisahkan backlog residual `fixture/template consistency` ke concern SOT terisolasi.

Artefak:

- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
- `docs/process/archive/2026_03/TODO_BTLK26A1_OPTIMASI_BOTTLENECK_PROCESS_EXECUTION_2026_03_01.md`
- `docs/process/archive/2026_03/TODO_FTC26A1_FIXTURE_TEMPLATE_CONSISTENCY_2026_03_01.md`

Perintah validasi:

- `rg -n "P-021|P-022|P-023|Doc-Only Fast Lane Validation|Self-Reflective Routing" docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
  - hasil: `PASS`.
- `rg -n "Fast-lane|Khusus .*process/domain/adr" docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
  - hasil: `PASS`.
- `rg -n "C-FIXTURE-TEMPLATE|TODO_FTC26A1_FIXTURE_TEMPLATE_CONSISTENCY_2026_03_01.md" docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md docs/process/archive/2026_03/TODO_FTC26A1_FIXTURE_TEMPLATE_CONSISTENCY_2026_03_01.md`
  - hasil: `PASS`.

Keputusan:

- `P-021` hanya untuk keputusan strategis; `doc-only` minor tidak wajib ADR.
- `doc-only fast lane` aktif untuk validasi ringan concern dokumen.
- Concern `fixture/template consistency` dipisah ke SOT `FTC26A1`.

Status:

- `PASS` untuk sinkronisasi dokumen concern process execution.

## Implementasi Concern Fixture/Template Consistency (F1-F3): 2026-03-01

Ruang lingkup:

- Menutup akar masalah jalur print laporan tahunan agar tidak hard-fail ketika template `.docx` tidak tersedia/tidak terbaca di environment tertentu.
- Mengunci mapping kandidat template `.docx` secara canonical di config concern.
- Audit fixture token baseline untuk modul `4.14.2b` dan `4.14.2c` terhadap kontrak judul aktif pada view PDF.

Artefak:

- `app/Domains/Wilayah/LaporanTahunanPkk/Services/LaporanTahunanPkkDocxGenerator.php`
- `config/laporan_tahunan_pkk.php`
- `docs/process/archive/2026_03/TODO_FTC26A1_FIXTURE_TEMPLATE_CONSISTENCY_2026_03_01.md`

Perubahan inti:

- Generator `.docx` kini membaca daftar `docx_template_candidates` dari config concern.
- Jika kandidat template ada tetapi gagal disalin, alur tidak langsung gagal; generator mencoba kandidat lain lalu fallback ke paket `.docx` minimal.
- Mapping canonical template concern dikunci:
  - `docs/referensi/LAPORAN TAHUNAN PKK th 2025.docx`
  - `resources/templates/laporan-tahunan-pkk.docx` (opsional jika disediakan deployment tertentu).
- Token fixture untuk `4.14.2b` (`BUKU HATINYA PKK`) dan `4.14.2c` (`BUKU INDUSTRI RUMAH TANGGA`) diverifikasi tetap selaras dengan judul aktif view.

Perintah validasi:

- `php artisan test --filter=LaporanTahunanPkkReportPrintTest`
  - hasil: `BLOCKED` pada environment ini (`php: command not found`).
- `php artisan test --filter=PdfBaselineFixtureComplianceTest`
  - hasil: `BLOCKED` pada environment ini (`php: command not found`).

Keputusan:

- F1-F3 concern `FTC26A1` dinyatakan selesai pada level implementasi kode + sinkronisasi mapping.
- F4-F5 tetap `pending` sampai validasi test dijalankan di environment yang memiliki runtime PHP.

Status:

- `PARTIAL` (`implementation-done`, `test-execution-blocked-by-environment`).

## Penutupan Concern Fixture/Template Consistency (F4-F5): 2026-03-02

Ruang lingkup:

- Menjalankan ulang targeted feature tests concern `FTC26A1` pada environment dengan runtime PHP aktif.
- Menutup residual blocker yang sebelumnya berstatus `BLOCKED`.

Artefak:

- `docs/process/archive/2026_03/TODO_FTC26A1_FIXTURE_TEMPLATE_CONSISTENCY_2026_03_01.md`
- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`

Perintah validasi:

- `php artisan test --filter=LaporanTahunanPkkReportPrintTest`
  - hasil: `PASS` (`3` tests, `18` assertions).
- `php artisan test --filter=PdfBaselineFixtureComplianceTest`
  - hasil: `PASS` (`20` tests, `503` assertions).

Keputusan:

- F4-F5 concern `FTC26A1` dinyatakan selesai.
- Tidak ada fail residual terkait fixture/template consistency pada jalur targeted tests concern ini.

Status:

- `PASS`.

## Rollout Access Control Batch 1 (`activities`): 2026-03-02

Ruang lingkup:

- Melanjutkan concern `ACL26M1` dari pilot single-module ke rollout batch pertama modul berikutnya (`activities`).
- Menjaga fallback hardcoded tetap aktif sambil menggeneralisasi endpoint override untuk payload `module`.

Artefak:

- `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- `app/UseCases/SuperAdmin/ListAccessControlMatrixUseCase.php`
- `app/Http/Controllers/SuperAdmin/AccessControlManagementController.php`
- `app/Http/Requests/SuperAdmin/UpdatePilotCatatanKeluargaOverrideRequest.php`
- `app/Http/Requests/SuperAdmin/RollbackPilotCatatanKeluargaOverrideRequest.php`
- `routes/web.php`
- `resources/js/Pages/SuperAdmin/AccessControl/Index.vue`
- `docs/process/archive/2026_03/TODO_ACL26A2_ROLLOUT_OVERRIDE_MODUL_ACTIVITIES_2026_03_02.md`

Perintah validasi:

- `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php`
  - hasil: `PASS` (`4` tests, `43` assertions).
- `php artisan test tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`
  - hasil: `PASS` (`4` tests, `26` assertions).
- `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
  - hasil: `PASS` (`14` tests, `137` assertions).
- `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`
  - hasil: `PASS` (`4` tests, `74` assertions).
- `php artisan test tests/Feature/ModuleVisibilityMiddlewareTest.php`
  - hasil: `PASS` (`11` tests, `33` assertions).
- `php artisan test`
  - hasil: `PASS` (`994` tests, `6226` assertions).
- `npm run build`
  - hasil: `PASS`.

Keputusan:

- Rollout override modul `activities` dinyatakan aktif sebagai batch pertama Tahap 3.
- Override hanya berlaku untuk modul rollout terkelola (`catatan-keluarga`, `activities`); override modul di luar daftar diabaikan resolver.
- Validasi role-scope-module dikunci pada layer request + action untuk mencegah kombinasi tidak kompatibel.

Status:

- `PASS`.

## Mitigasi Gap Pagination (`PGM26A1`) - Lanjutan Eksekusi: 2026-03-02

Ruang lingkup:

- Menuntaskan hardening frontend pagination untuk modul target wilayah, pilot project, dan super-admin.
- Menambah hardening test untuk `per_page` valid/invalid fallback pada concern super-admin.

Artefak:

- `resources/js/Pages/Desa/{Koperasi,KejarPaket,Posyandu,ProgramPrioritas,SimulasiPenyuluhan,WarungPkk}/Index.vue`
- `resources/js/Pages/Kecamatan/{Koperasi,KejarPaket,Posyandu,ProgramPrioritas,SimulasiPenyuluhan,WarungPkk}/Index.vue`
- `resources/js/Pages/{PilotProjectKeluargaSehat,PilotProjectNaskahPelaporan}/Index.vue`
- `resources/js/Pages/SuperAdmin/{Users,Arsip}/Index.vue`
- `tests/Feature/SuperAdmin/UserManagementIndexPaginationTest.php`
- `tests/Feature/SuperAdmin/ArsipManagementTest.php`
- `docs/process/archive/2026_03/TODO_PGM26A1_MITIGASI_GAP_PAGINATION_2026_03_02.md`

Perintah validasi:

- `php artisan test --filter PaginationNormalizationWilayahTest`
  - hasil: `PASS` (`32` tests, `576` assertions).
- `php artisan test --filter UserManagementIndexPaginationTest`
  - hasil: `PASS` (`5` tests, `61` assertions).
- `php artisan test --filter ArsipManagementTest`
  - hasil: `PASS` (`5` tests, `52` assertions).
- `php artisan test`
  - hasil: `PASS` (`1030` tests, `6866` assertions).
- `npm run build`
  - hasil: `PASS` (`vite build`, `built in 6m 4s`).

Keputusan:

- Kontrak frontend concern target dikunci ke payload paginator (`Object`) + query persistence (`...props.filters`, reset `page=1`).
- Concern `PGM26A1` tetap `in-progress` sampai smoke test manual terselesaikan.

Status:

- `PARTIAL` (`implementation+tests+build-done`, `manual-smoke-pending`).

## Penutupan Mitigasi Gap Pagination (`PGM26A1`): 2026-03-02

Ruang lingkup:

- Menutup residual concern `manual-smoke-pending` dengan regression guard feature test yang memverifikasi navigasi pagination (`page/per_page`) tetap stabil pada jalur super-admin.
- Menyinkronkan status dokumen concern pagination lintas TODO + registry SOT.

Artefak:

- `tests/Feature/SuperAdmin/UserManagementIndexPaginationTest.php`
- `tests/Feature/SuperAdmin/ArsipManagementTest.php`
- `docs/process/archive/2026_03/TODO_PGM26A1_MITIGASI_GAP_PAGINATION_2026_03_02.md`
- `docs/process/archive/2026_02/TODO_UI_PAGINATION_E2E_2026_02_24.md`
- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`

Perintah validasi:

- `php artisan test --filter PaginationNormalizationWilayahTest`
  - hasil: `PASS` (`32` tests, `576` assertions).
- `php artisan test --filter UserManagementIndexPaginationTest`
  - hasil: `PASS` (`6` tests, `107` assertions).
- `php artisan test --filter ArsipManagementTest`
  - hasil: `PASS` (`6` tests, `98` assertions).

Keputusan:

- Query persistence concern pagination dikunci oleh guard otomatis: `next_page_url` memuat `per_page` yang aktif dan roundtrip `page=1 -> page=2 -> page=1` tetap konsisten.
- Concern `PGM26A1` disinkronkan ke status `done` pada dokumen concern dan registry SOT.

Status:

- `PASS` (`mitigation-closed`).

## Refactor Responsive UX Layout (`UXR26A1`) - Batch SuperAdmin Arsip: 2026-03-02

Ruang lingkup:

- Melanjutkan rollout concern `UXR26A1` pada halaman prioritas `SuperAdmin/Arsip/Index` dengan kontrak `ResponsiveDataTable`.
- Menstandarkan target sentuh minimum `44px` untuk interaksi pagination dan aksi utama/sekunder pada batch aktif.

Artefak:

- `resources/js/Pages/SuperAdmin/Arsip/Index.vue`
- `resources/js/admin-one/components/PaginationBar.vue`
- `tests/Unit/Frontend/ResponsiveTableRolloutContractTest.php`
- `docs/process/archive/2026_03/TODO_UXR26A1_REFACTOR_RESPONSIVE_UX_LAYOUT_2026_03_01.md`

Perintah validasi:

- `php artisan test tests/Unit/Frontend/ResponsiveTableRolloutContractTest.php tests/Unit/Frontend/DashboardResponsiveInteractionContractTest.php`
  - hasil: `PASS` (`5` tests, `19` assertions).
- `php artisan test --filter ArsipManagementTest`
  - hasil: `PASS` (`6` tests, `98` assertions).
- `cmd /c npm run build`
  - hasil: `PASS` (`vite build`, built in `11.49s`).

Keputusan:

- Rollout `ResponsiveDataTable` diperluas ke jalur super-admin arsip dengan fallback legacy tetap aktif lewat feature flag `VITE_UI_RESPONSIVE_TABLE_V2`.
- Utility sentuh minimum `min-h-[44px]` dikunci di `PaginationBar` agar konsisten lintas halaman yang memakai komponen reusable.
- Concern `UXR26A1` tetap `in-progress`; batch berikutnya fokus `R5-R7` (semantic click elements, modal accessibility guard, state standardization).

Status:

- `PASS` (`batch-completed`, `concern-still-in-progress`).

## Sinkronisasi Registry SOT (`TTM25R1`) - 2026-03-02

Ruang lingkup:

- Menutup drift registry SOT terhadap TODO concern berstatus `in-progress` yang belum terpetakan di tabel concern.
- Menetapkan parent-child SOT untuk concern availability/auth buku dan monitoring visibility lintas modul.

Artefak:

- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`

Perubahan sinkronisasi:

- Menambah concern baru:
  - `C-BUKU-ADMIN` -> SOT `TODO_KETERSEDIAAN_BUKU_ADMIN_PKK_2026_02_27.md`.
  - `C-MODULE-VISIBILITY` -> SOT `TODO_MONITORING_VISIBILITY_SEMUA_MODUL_2026_02_27.md`.
  - `C-SEKCAM-ROADMAP` -> SOT `TODO_SKC0201_ROADMAP_SEKRETARIS_KECAMATAN_2026_02_28.md`.
- Menetapkan relasi child-spec:
  - `TODO_AUTENTIK_SEKRETARIS_INTI_2026_02_27.md` sebagai child concern `C-BUKU-ADMIN`.
  - `TODO_MONITORING_VISIBILITY_MODUL_KEGIATAN_2026_02_27.md` sebagai child concern `C-MODULE-VISIBILITY`.

Perintah validasi:

- `rg -n '^Status:\\s*`(planned|in-progress|done)`' docs/process -g 'TODO_*.md'`
  - hasil: `PASS` (daftar status concern aktif terdeteksi).
- `rg -n 'C-BUKU-ADMIN|C-MODULE-VISIBILITY|C-SEKCAM-ROADMAP' docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
  - hasil: `PASS` (baris concern baru terdeteksi).

Keputusan:

- Registry `TTM25R1` tetap berstatus `in-progress` karena concern aktif lintas domain masih berjalan.
- SOT concern aktif kini eksplisit untuk seluruh TODO utama yang berstatus `in-progress` pada scope registry.

Status:

- `PASS` (`registry-sync-completed`).

## Hardening ASM26B1 Management Arsip Super Admin: 2026-03-02

Ruang lingkup:

- Menutup gap otorisasi lintas akun `super-admin` pada operasi `update/delete` arsip `global` di jalur `/super-admin/arsip`.
- Menjaga kontrak jalur `/arsip` user tetap owner-only untuk mutasi arsip private.
- Menyinkronkan status concern `ASM26B1` pada dokumen TODO dan registry SOT.

Artefak:

- `app/Policies/ArsipDocumentPolicy.php`
- `tests/Feature/SuperAdmin/ArsipManagementTest.php`
- `tests/Unit/Policies/ArsipDocumentPolicyTest.php`
- `docs/process/archive/2026_02/TODO_ASM26B1_MANAGEMENT_ARSIP_SUPER_ADMIN_2026_02_27.md`
- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`

Perintah validasi:

- `php artisan test --filter ArsipManagementTest`
  - hasil: `PASS` (`7` tests, `108` assertions).
- `php artisan test --filter ArsipTest`
  - hasil: `PASS` (`11` tests, `53` assertions; termasuk `KecamatanDesaArsipTest` pada filter ini).
- `php artisan test --filter ArsipDocumentPolicyTest`
  - hasil: `PASS` (`5` tests, `12` assertions).
- `php artisan test`
  - hasil: `PASS` (`1035` tests, `6974` assertions).

Keputusan:

- Policy `update/delete` arsip kini mengizinkan `super-admin` untuk dokumen `is_global=true` meskipun bukan creator.
- Boundary otorisasi tetap aman: jalur user `/arsip` tetap memaksa owner-only untuk mutasi private.
- Concern `C-ARSIP-MGMT` disinkronkan ke status `done`.

Status:

- `PASS`.

## Penutupan Concern Ketersediaan Buku Admin PKK (`KBA26A1`): 2026-03-02

Ruang lingkup:

- Menutup blocker autentikasi child concern `AUTENTIK_SEKRETARIS_INTI` untuk tiga buku sekretaris inti:
  - `buku-notulen-rapat`
  - `buku-daftar-hadir`
  - `buku-tamu`
- Menyinkronkan status concern parent-child + registry SOT agar tidak drift.

Artefak:

- `docs/domain/BUKU_SEKRETARIS_INTI_AUTH_MAPPING.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/domain/dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md`
- `docs/process/archive/2026_02/TODO_AUTENTIK_SEKRETARIS_INTI_2026_02_27.md`
- `docs/process/archive/2026_02/TODO_KETERSEDIAAN_BUKU_ADMIN_PKK_2026_02_27.md`
- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
- `docs/referensi/_screenshots/rakernas-x-autentik/lampiran_4_10_buku_agenda_surat_masuk_keluar.png`
- `docs/referensi/_screenshots/rakernas-x-autentik/lampiran_4_12_buku_inventaris.png`
- `docs/referensi/_screenshots/rakernas-x-autentik/lampiran_4_13_buku_kegiatan.png`

Perintah validasi:

- `python -c "... scan keyword NOTULEN|DAFTAR HADIR|BUKU TAMU pada docs/referensi/Rakernas X.pdf ..."`
  - hasil: `NOTULEN NO_MATCH`, `DAFTAR HADIR NO_MATCH`, `BUKU TAMU NO_MATCH`.
- `python -c "... scan daftar sheet workbook docs/referensi/excel/BUKU BANTU.xlsx ..."`
  - hasil: token `NOTULEN/DAFTAR HADIR/BUKU TAMU` `NO_MATCH`.
- `python -c "... render screenshot halaman Rakernas X (20,24,26) menggunakan pypdfium2 ..."`
  - hasil: `PASS` (file screenshot valid tersimpan).
- `php artisan test tests/Feature/BukuNotulenRapatReportPrintTest.php tests/Feature/BukuDaftarHadirReportPrintTest.php tests/Feature/BukuTamuReportPrintTest.php`
  - hasil: `PASS` (`12` tests, `39` assertions).

Keputusan:

- Tiga buku sekretaris inti dikunci sebagai `unverified-local-extension` karena template tabel primer resmi belum tersedia pada sumber primer aktif.
- Kontrak baseline header internal tetap berlaku dan dijaga oleh test header report.
- Concern `KBA26A1` dan child concern `AUTENTIK_SEKRETARIS_INTI` disinkronkan ke status `done`.

Status:

- `PASS` (`concern-closed-with-source-scan-decision`).

## Mitigasi Final Concern Aktif (`ACL26M1` + `SKC0201` + `UVM25R1` + `UXR26A1` + `TTM25R1`): 2026-03-02

Ruang lingkup:

- Mengunci baseline akhir sesi untuk seluruh concern yang masih `in-progress` agar tidak terjadi drift status SOT sebelum cadence review mingguan.
- Menjalankan satu paket validasi lintas concern untuk memastikan stabilitas kontrak akses, monitoring kecamatan, dan guard frontend UI.

Artefak:

- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
- `docs/process/archive/2026_02/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`
- `docs/process/archive/2026_02/TODO_SKC0201_ROADMAP_SEKRETARIS_KECAMATAN_2026_02_28.md`
- `docs/process/archive/2026_02/TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md`
- `docs/process/archive/2026_03/TODO_UXR26A1_REFACTOR_RESPONSIVE_UX_LAYOUT_2026_03_01.md`

Perintah validasi:

- `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Feature/KecamatanDesaActivityTest.php tests/Feature/KecamatanDesaArsipTest.php tests/Unit/Frontend/DashboardLayoutMenuContractTest.php tests/Unit/Frontend/ResponsiveTableRolloutContractTest.php tests/Unit/Frontend/DashboardResponsiveInteractionContractTest.php tests/Unit/Frontend/NavigationSemanticContractTest.php`
  - hasil: `PASS` (`49` tests, `425` assertions).

Keputusan:

- Snapshot concern aktif dikunci: `ACL26M1`, `SKC0201`, `UVM25R1`, `UXR26A1`, `TTM25R1`.
- Tidak ada drift status concern baru pada registry SOT.
- Concern tetap `in-progress` karena blocker yang tersisa bersifat non-kode:
  - validasi stakeholder matrix (`ACL26M1`),
  - keputusan rollout wave-2 pokja (`SKC0201`),
  - smoke manual UI desktop/mobile (`UVM25R1`, `UXR26A1`),
  - penutupan cadence `R1/R2` (`TTM25R1`).

Status:

- `PASS` (`final-mitigation-snapshot-locked`).

## Validasi Restructure Artefak Perencanaan (`RPA2601`): 2026-03-02

Ruang lingkup:

- Memastikan restruktur artefak planning (index planning + referensi single-path + shortcut command) benar-benar operasional dan sinkron dengan registry concern aktif.

Artefak:

- `docs/process/PLANNING_ARTIFACT_INDEX.md`
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/COMMAND_NUMBER_SHORTCUTS.md`
- `docs/process/archive/2026_03/TODO_RPA2601_VALIDASI_RESTRUCTURE_ARTEFAK_PERENCANAAN_2026_03_02.md`
- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`

Perintah validasi:

- `rg -n '^Status:\s*`in-progress`' docs/process -g 'TODO_*.md'`
  - hasil: `PASS` (`5` concern aktif terdeteksi: `ACL26M1`, `SKC0201`, `UVM25R1`, `UXR26A1`, `TTM25R1`).
- `rg -n "PLANNING_ARTIFACT_INDEX" docs/process/AI_SINGLE_PATH_ARCHITECTURE.md docs/process/COMMAND_NUMBER_SHORTCUTS.md`
  - hasil: `PASS` (referensi indeks planning + shortcut restruktur tersedia).
- `rg -n "ACL26M1|SKC0201|UVM25R1|UXR26A1|TTM25R1" docs/process/PLANNING_ARTIFACT_INDEX.md docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
  - hasil: `PASS` (snapshot concern aktif konsisten antara index planning dan registry SOT).

Keputusan:

- Restruktur artefak perencanaan dinyatakan berhasil pada level operasional `doc-only`.
- Source of truth status concern aktif tetap `TTM25R1`; `PLANNING_ARTIFACT_INDEX` dikunci sebagai peta navigasi planning.

Status:

- `PASS` (`planning-restructure-validated`).

## Siklus Monitoring Visibility Modul Inventaris untuk Pokja Desa (`IWN26B1`): 2026-03-04

Ruang lingkup:

- Menambahkan akses `read-write` modul `inventaris` untuk role `desa-pokja-i..iv`.
- Menjaga scope `kecamatan-pokja-i..iv` tetap tanpa akses `inventaris`.
- Menjaga authority akses di backend (`module.visibility` + resolver role-scope).

Perubahan kontrak:

- Role terdampak:
  - `desa-pokja-i`, `desa-pokja-ii`, `desa-pokja-iii`, `desa-pokja-iv` -> `inventaris: read-write`.
  - `kecamatan-pokja-i..iv` -> tetap `hidden` (tidak ada di `moduleModes`).
- Modul terdampak:
  - `inventaris`.
- Mode sebelum -> sesudah:
  - `desa-pokja-i..iv`: `hidden` -> `read-write`.
  - `kecamatan-pokja-i..iv`: `hidden` -> `hidden` (tetap).

Artefak:

- `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- `tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
- `tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php`
- `tests/Feature/MenuVisibilityPayloadTest.php`
- `tests/Feature/ModuleVisibilityMiddlewareTest.php`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`

Perintah validasi:

- `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php`
  - hasil: `PASS` (`38` tests, `389` assertions).
- `php artisan test`
  - hasil: `PASS` (`1052` tests, `7075` assertions).

Keputusan:

- Grant RW `inventaris` dikunci pada role pokja scope `desa` via role-module override backend.
- Scope `kecamatan` tidak menerima grant baru untuk `inventaris`.
- Sinkronisasi dokumen canonical ditambahkan pada `DOMAIN_CONTRACT_MATRIX` (catatan pengecualian akses inventaris).

Status:

- `PASS` (`inventaris-rw-desa-pokja-locked`).

## Siklus Monitoring Visibility Modul Buku Tamu untuk Pokja Desa (`IWN26B2`): 2026-03-04

Ruang lingkup:

- Menambahkan akses `read-write` modul `buku-tamu` untuk role `desa-pokja-i..iv`.
- Menjaga scope `kecamatan-pokja-i..iv` tetap tanpa akses `buku-tamu`.
- Menyelaraskan visibilitas menu sidebar Pokja I-IV dengan kontrak backend (`moduleModes`).

Perubahan kontrak:

- Role terdampak:
  - `desa-pokja-i`, `desa-pokja-ii`, `desa-pokja-iii`, `desa-pokja-iv` -> `buku-tamu: read-write`.
  - `kecamatan-pokja-i..iv` -> tetap `hidden` (tidak ada di `moduleModes`).
- Modul terdampak:
  - `buku-tamu`.
- Mode sebelum -> sesudah:
  - `desa-pokja-i..iv`: `hidden` -> `read-write`.
  - `kecamatan-pokja-i..iv`: `hidden` -> `hidden` (tetap).

Artefak:

- `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- `resources/js/Layouts/DashboardLayout.vue`
- `tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
- `tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php`
- `tests/Feature/MenuVisibilityPayloadTest.php`
- `tests/Feature/ModuleVisibilityMiddlewareTest.php`
- `tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`

Perintah validasi:

- `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
  - hasil: `PASS` (`49` tests, `449` assertions).

Keputusan:

- Grant RW `buku-tamu` dikunci pada role pokja scope `desa` via role-module override backend.
- Scope `kecamatan` tidak menerima grant baru untuk `buku-tamu`.
- Sidebar Pokja I-IV menampilkan menu `Buku Tamu` secara kondisional berdasarkan `moduleModes.buku-tamu`.

Status:

- `PASS` (`buku-tamu-rw-desa-pokja-locked`).

## Eksekusi TODO RGM26A1 (No-op Grouping Owner) : 2026-03-07

Catatan historis:

- Entri ini adalah snapshot audit pada 2026-03-07.
- Status aktif terbaru concern dapat berubah setelah entri ini; rujuk TODO concern aktif untuk status final lintas sesi.

Ruang lingkup:

- Menjalankan concern `RGM26A1` berdasarkan tabel `Group Target` owner pada TODO.
- Memastikan baseline akses tetap stabil ketika tidak ada regroup aktif.

Hasil intake owner:

- Semua kolom `Group Target` pada `TODO_RGM26A1_*` kosong.
- Sesuai aturan concern, kondisi ini berarti seluruh modul `tetap` (no change).

Artefak terdampak:

- `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`
- `tests/Unit/Frontend/DashboardLayoutMenuContractTest.php` (hardening assertion non-brittle saat validasi)

Perintah validasi:

- `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
  - hasil: `PASS` (`49` tests, `449` assertions).

Keputusan:

- Concern `RGM26A1` ditutup sebagai no-op terkontrol karena tidak ada modul yang diminta regroup.
- Kontrak akses backend (`RoleMenuVisibilityService` + middleware + payload) tidak berubah.

Status:

- `PASS` (`rgm26a1-noop-owner-target-empty`).

## Mitigasi Blocked Dependency OS Browser Playwright (`MNT26E2`): 2026-03-07

Ruang lingkup:

- Menurunkan risiko kegagalan E2E akibat dependency OS browser yang tidak lengkap.
- Menambah guard preflight deterministik sebelum `playwright test` dieksekusi.

Artefak terdampak:

- `scripts/ui-runtime/run-playwright-with-preflight.mjs`
- `package.json`
- `README.md`

Perubahan:

- Tambah wrapper `run-playwright-with-preflight.mjs` untuk:
  - set `TMPDIR/TEMP/TMP=/tmp` pada Linux/WSL,
  - cek instalasi browser Playwright Chromium,
  - cek shared library Linux via `ldd`,
  - fail-fast dengan instruksi package OS yang spesifik.
- Script npm E2E dialihkan ke wrapper preflight.
- Tambah command `npm run test:e2e:doctor` sebagai health-check dependency sebelum run suite.

Perintah validasi:

- `npm run test:e2e:doctor`
  - hasil: `PASS` (`[e2e-preflight] OK: Playwright browser dependencies siap.`).
- `npm run test:e2e -- --list`
  - hasil: `PASS` (`34` test cases terdaftar; forwarding argumen wrapper tervalidasi).

Keputusan:

- Kegagalan dependency OS browser tidak lagi muncul terlambat saat runtime test, melainkan terdeteksi pada preflight.
- Operasional E2E lokal menjadi lebih stabil lintas Linux/WSL tanpa mengubah kontrak test suite.

Status:

- `PASS` (`playwright-os-preflight-guard-active`).

## Re-eksekusi TODO RGM26A1 oleh `manto` (No-op + Full Regression): 2026-03-07

Catatan historis:

- Entri ini tetap valid sebagai bukti eksekusi pada tanggal 2026-03-07.
- Jika terjadi reset concern setelahnya, status aktif terbaru tetap mengikuti TODO concern aktif, bukan arsip log kuartal ini.

Ruang lingkup:

- Menjalankan ulang concern `RGM26A1` untuk memastikan tidak ada drift setelah siklus clean-up.
- Memverifikasi ulang bahwa tabel owner tetap tidak meminta regroup modul.

Hasil intake owner:

- Seluruh kolom `Group Target` masih kosong.
- Concern tetap berlaku sebagai no-op terkontrol (tanpa patch grouping/menu).

Perintah validasi:

- `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php tests/Unit/Frontend/DashboardLayoutMenuContractTest.php --compact`
  - hasil: `PASS` (`49` tests, `449` assertions).
- `php artisan test --compact`
  - hasil: `PASS` (`1057` tests, `7110` assertions).
- `npm run build`
  - hasil: `PASS` (build sukses; optional dependency Rollup direpair via `npm install` sebelum rerun).

Keputusan:

- Tidak ada perubahan kode kontrak akses/grouping pada concern `RGM26A1`.
- Status concern tetap `done` dengan validasi end-to-end terbaru.

Status:

- `PASS` (`rgm26a1-noop-revalidated-e2e`).

## Cleanup Pasca Migrate Fresh (`MFC26A1`): 2026-03-07

Ruang lingkup:

- Menutup concern cleanup pasca `migrate:fresh` secara bertahap (wave 1-4).
- Menyederhanakan baseline migration tanpa mengubah kontrak domain aktif.
- Mengunci keputusan retain sementara untuk compatibility role legacy dan fallback dashboard.

Artefak terdampak:

- `database/migrations/2026_02_21_050000_create_agenda_surats_table.php`
- `database/migrations/2026_02_20_200000_create_program_prioritas_table.php`
- `database/migrations/2026_02_22_130000_create_pilot_project_naskah_pelaporan_reports_table.php`
- `database/migrations/2026_02_28_000000_add_data_dukung_path_to_agenda_surats_table.php` (deleted)
- `database/migrations/2026_02_24_180000_add_jadwal_bulanan_columns_to_program_prioritas_table.php` (deleted)
- `database/migrations/2026_02_22_132000_add_penutup_to_pilot_project_naskah_pelaporan_reports_table.php` (deleted)
- `database/migrations/2026_02_22_133000_add_head_surat_fields_to_pilot_project_naskah_pelaporan_reports_table.php` (deleted)
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/process/archive/2026_03/TODO_MFC26A1_CLEANUP_PASCA_MIGRATE_FRESH_2026_03_07.md`

Perintah validasi:

- `php artisan migrate:fresh --seed`
  - hasil: `PASS`.
- `php artisan test tests/Feature/DesaAgendaSuratTest.php tests/Feature/KecamatanAgendaSuratTest.php tests/Feature/DesaProgramPrioritasTest.php tests/Feature/KecamatanProgramPrioritasTest.php tests/Feature/DesaPilotProjectNaskahPelaporanTest.php tests/Feature/KecamatanPilotProjectNaskahPelaporanTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Unit/Dashboard/DashboardCoverageMenuSyncTest.php tests/Unit/Frontend/DashboardLayoutMenuContractTest.php --compact`
  - hasil: `PASS` (`80` tests, `615` assertions).
- `php artisan test --compact`
  - hasil: `PASS` (`1057` tests, `7110` assertions).
- `npm run build`
  - hasil: `PASS`.

Keputusan:

- Migration transisi `add_*` yang redundant ditutup via squash ke baseline `create_*`.
- Compatibility role legacy (`admin-desa` / `admin-kecamatan`) dipertahankan sementara karena usage masih aktif lintas app/seeder/test.
- Fallback payload dashboard (`dashboardStats` / `dashboardCharts`) dipertahankan sementara karena masih dikonsumsi aktif.

Status:

- `PASS` (`mfc26a1-cleanup-closed-with-retain-decisions`).

## Hardening Struktur Folder Maintainable (`SFC26A1`): 2026-03-07

Ruang lingkup:

- Menutup concern maintainability struktur repository untuk review manual.
- Mengunci policy placement concern baru, membersihkan artefak root/generated, dan menambahkan strategi arsip TODO done.

Artefak terdampak:

- `.gitignore`
- `AGENTS.md`
- `README.md`
- `docs/README.md`
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/CODE_PLACEMENT_POLICY.md`
- `docs/process/PROCESS_TODO_ARCHIVE_STRATEGY.md`
- `docs/process/PLANNING_ARTIFACT_INDEX.md`
- `docs/process/archive/README.md`
- `docs/process/archive/2026_03/TODO_SFC26A1_HARDENING_STRUKTUR_FOLDER_MAINTAINABLE_2026_03_07.md`
- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
- `docs/referensi/README.md`
- `docs/referensi/_local/README.md`
- `public/chart-pdf-examples/README.md`
- `buku_3_petunjuk_teknis_tata_kelola_kelembagaan_pkk_1761536074.pdf` (deleted from root tracked)
- `public/chart-pdf-examples/output/quickchart-example.pdf` (deleted from tracked)

Perintah validasi:

- `git diff --name-status | rg '^(D)\\s+(buku_.*\\.pdf|public/chart-pdf-examples/output/quickchart-example.pdf)$'`
  - hasil: `PASS` (dua deletion marker terdeteksi).
- `test -f buku_3_petunjuk_teknis_tata_kelola_kelembagaan_pkk_1761536074.pdf`
  - hasil: `PASS` (`not found`).
- `test -f public/chart-pdf-examples/output/quickchart-example.pdf`
  - hasil: `PASS` (`not found`).
- `rg -n "CODE_PLACEMENT_POLICY|TODO_ARCHIVE_STRATEGY|docs/referensi/README.md" AGENTS.md README.md docs/README.md docs/process/AI_SINGLE_PATH_ARCHITECTURE.md docs/process/PLANNING_ARTIFACT_INDEX.md`
  - hasil: `PASS` (referensi policy sinkron).

Keputusan:

- Policy placement concern baru dikunci pada `docs/process/CODE_PLACEMENT_POLICY.md`.
- File referensi biner lokal dipisahkan dari source tracked melalui `docs/referensi/_local` + hardening `.gitignore`.
- Strategi arsip TODO done diaktifkan melalui `docs/process/PROCESS_TODO_ARCHIVE_STRATEGY.md`.

Status:

- `PASS` (`sfc26a1-structure-hardened`).

## Inisialisasi TODO Bahan Aktual dan Terjemahan Berkala (`TBH26A1`): 2026-03-08

Ruang lingkup:

- Menyiapkan dokumen TODO hidup untuk menerima input aktual owner dan menerjemahkannya ke bahasa bahan formal.
- Mengunci pola update append-only agar histori perubahan tetap utuh.

Artefak:

- `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`

Perintah validasi:

- `rg -n "^# TODO TBH26A1 " docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "^Status:\s*`in-progress`" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "^## Update " docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.

Keputusan:

- Concern dikunci sebagai `doc-only` tanpa perubahan runtime/backend contract.
- Format update dikunci: `Input Aktual (Asli)`, `Terjemahan Bahan (Formal)`, `Catatan (Opsional)`.

Status:

- `PASS` (`tbh26a1-doc-only-template-initialized`).

## Append Update Pokja I Desa pada TODO Bahan Aktual (`TBH26A1`): 2026-03-08

Ruang lingkup:

- Merekam input aktual owner untuk baseline Pokja I level desa.
- Menambahkan terjemahan bahan formal ke dokumen hidup `TBH26A1` dengan pola append-only.

Artefak:

- `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`

Perintah validasi:

- `rg -n "^## Update .*\\(U002\\)" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "Pokja I tingkat desa memiliki dokumen administrasi" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.

Keputusan:

- Input Pokja I level desa dikunci sebagai baseline isi `U002`.
- Perapian ejaan hanya dilakukan pada bagian terjemahan formal; input asli tetap dipertahankan apa adanya.

Status:

- `PASS` (`tbh26a1-u002-pokja-i-desa-recorded`).

## Append Update Pokja III dan Administrasi KWT pada TODO Bahan Aktual (`TBH26A1`): 2026-03-08

Ruang lingkup:

- Merekam daftar administrasi Pokja III.
- Merekam daftar administrasi KWT (Kelompok Wanita Tani) yang menyertai input Pokja III.
- Menandai ambiguitas yang mempengaruhi makna tanpa mengubah input asli.

Artefak:

- `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`

Perintah validasi:

- `rg -n "^## Update .*\\(U003\\)" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "Pokja III juga memiliki administrasi KWT" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "\\[PERLU KONFIRMASI: item ini muncul kembali setelah nomor 2\\]" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.

Keputusan:

- Input Pokja III dan administrasi KWT dikunci sebagai isi `U003`.
- Ambiguitas duplikasi `Buku Kegiatan` tidak diselesaikan dengan asumsi; statusnya tetap `[PERLU KONFIRMASI]`.

Status:

- `PASS` (`tbh26a1-u003-pokja-iii-kwt-recorded`).

## Append Update Pokja IV pada TODO Bahan Aktual (`TBH26A1`): 2026-03-08

Ruang lingkup:

- Merekam daftar administrasi Pokja IV dari input yang terpecah pada dua pesan user.
- Menggabungkan potongan input tersebut menjadi satu update append-only yang utuh.

Artefak:

- `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`

Perintah validasi:

- `rg -n "^## Update .*\\(U004\\)" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "Administrasi Pokja IV pada baseline ini dicatat sebagai berikut" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "menggabungkan potongan input Pokja IV dari dua pesan terakhir" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.

Keputusan:

- Potongan input Pokja IV digabung dalam satu entry `U004` agar tidak memecah konteks.
- Context level tetap diasumsikan `desa` mengikuti alur sebelumnya sampai ada koreksi.

Status:

- `PASS` (`tbh26a1-u004-pokja-iv-recorded`).

## Append Update Administrasi Dasa Wisma pada TODO Bahan Aktual (`TBH26A1`): 2026-03-08

Ruang lingkup:

- Merekam baseline administrasi Dasa Wisma.
- Menandai duplikasi item yang berpotensi ambigu tanpa mengubah input asli.

Artefak:

- `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`

Perintah validasi:

- `rg -n "^## Update .*\\(U005\\)" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "Administrasi Dasa Wisma pada baseline ini dicatat" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "\\[PERLU KONFIRMASI: item ini muncul kembali seperti nomor 1\\]" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.

Keputusan:

- Baseline administrasi Dasa Wisma dikunci sebagai isi `U005`.
- Duplikasi item `Rekapitulasi Data Bumil dll` tidak diselesaikan dengan asumsi; statusnya tetap `[PERLU KONFIRMASI]`.

Status:

- `PASS` (`tbh26a1-u005-dasa-wisma-recorded`).

## Append Update Bendahara pada TODO Bahan Aktual (`TBH26A1`): 2026-03-08

Ruang lingkup:

- Merekam baseline administrasi bendahara.
- Menambahkan terjemahan bahan formal ke dokumen hidup `TBH26A1`.

Artefak:

- `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`

Perintah validasi:

- `rg -n "^## Update .*\\(U006\\)" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "Administrasi bendahara pada baseline ini meliputi" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.

Keputusan:

- Baseline administrasi bendahara dikunci sebagai isi `U006`.
- Tidak ada ambiguitas baru yang perlu diberi tag `[PERLU KONFIRMASI]`.

Status:

- `PASS` (`tbh26a1-u006-bendahara-recorded`).

## Append Update Sekretaris pada TODO Bahan Aktual (`TBH26A1`): 2026-03-08

Ruang lingkup:

- Merekam baseline administrasi sekretaris.
- Menambahkan versi bahan formal untuk kelompok buku wajib dan lampiran.

Artefak:

- `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`

Perintah validasi:

- `rg -n "^## Update .*\\(U007\\)" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "Administrasi sekretaris pada baseline ini dicatat sebagai berikut" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.

Keputusan:

- Baseline administrasi sekretaris dikunci sebagai isi `U007`.
- Perapian istilah `Bandel` menjadi `Bundel` dilakukan hanya pada versi formal.

Status:

- `PASS` (`tbh26a1-u007-sekretaris-recorded`).

## Append Update Buku Bantu Sekretaris pada TODO Bahan Aktual (`TBH26A1`): 2026-03-08

Ruang lingkup:

- Merekam lanjutan administrasi sekretaris pada bagian `B. Buku Bantu`.
- Menambahkan versi bahan formal dengan perapian ejaan terbatas.

Artefak:

- `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`

Perintah validasi:

- `rg -n "^## Update .*\\(U008\\)" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "Lanjutan administrasi sekretaris pada bagian" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.

Keputusan:

- Bagian `B. Buku Bantu` dikunci sebagai isi `U008` dan diposisikan sebagai lanjutan dari `U007`.
- Perapian ejaan hanya dilakukan pada versi formal.

Status:

- `PASS` (`tbh26a1-u008-buku-bantu-sekretaris-recorded`).

## Append Update Buku Penunjang Sekretaris pada TODO Bahan Aktual (`TBH26A1`): 2026-03-08

Ruang lingkup:

- Merekam lanjutan administrasi sekretaris pada bagian `C. Buku Penunjang Buku Wajib`.
- Menambahkan lampiran terkait ke dokumen hidup `TBH26A1`.

Artefak:

- `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`

Perintah validasi:

- `rg -n "^## Update .*\\(U009\\)" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "Lanjutan administrasi sekretaris pada bagian" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "Buku Grafik TP PKK Desa" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.

Keputusan:

- Bagian `C. Buku Penunjang Buku Wajib` dikunci sebagai isi `U009`.
- Entri ini diposisikan sebagai lanjutan dari blok administrasi sekretaris sebelumnya.

Status:

- `PASS` (`tbh26a1-u009-buku-penunjang-sekretaris-recorded`).

## Append Peta Analisa Kesesuaian Project Existing pada TODO Bahan Aktual (`TBH26A1`): 2026-03-08

Ruang lingkup:

- Menyusun peta koheren untuk membaca kesesuaian bahan administrasi yang sudah direkam terhadap modul yang sudah ada di project.
- Menggunakan source of truth dokumen domain, sidebar grouping, dan route aktif.

Artefak:

- `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/process/SIDEBAR_DOMAIN_GROUPING_PLAN.md`
- `routes/web.php`

Perintah validasi:

- `rg -n "^## Update .*\\(U010\\)" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "match langsung|reuse parsial/report-only|gap/perlu concern baru" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.
- `rg -n "KWT|Dasa Wisma|Bendahara|Pokja IV" docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
  - hasil: `PASS`.

Keputusan:

- Bahan yang sudah direkam dipetakan ke tiga status analisa: `match langsung`, `reuse parsial/report-only`, dan `gap/perlu concern baru`.
- Peta ini diposisikan sebagai bahan analisa kesesuaian project existing, bukan perubahan kontrak modul existing.

Status:

- `PASS` (`tbh26a1-u010-project-fit-analysis-mapped`).

## Draft Input Owner Aman untuk Concern Grouping Modul (`RGM26A1`): 2026-03-08

Ruang lingkup:

- Menurunkan hasil analisa kesesuaian project existing menjadi shortlist `Input Owner` yang paling aman.
- Fokus pada modul yang `match langsung`, tidak bergantung override khusus, dan tidak shared terlalu lebar lintas group.

Artefak:

- `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`
- `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/process/SIDEBAR_DOMAIN_GROUPING_PLAN.md`
- `routes/web.php`

Perintah validasi:

- `rg -n "Draft Input Owner Aman|Rekomendasi tahap-1 paling aman|desa only" docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`
  - hasil: `PASS`.
- `rg -n "agenda-surat|data-warga|posyandu|simulasi-penyuluhan" docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`
  - hasil: `PASS`.

Keputusan:

- Shortlist owner aman ditambahkan tanpa mengubah tabel baseline modul utama.
- Scope rollout paling aman direkomendasikan `desa only` untuk tahap owner pertama.

Status:

- `PASS` (`rgm26a1-safe-owner-shortlist-added`).

## Konfirmasi Owner Shortlist Aman pada Concern Grouping Modul (`RGM26A1`): 2026-03-08

Ruang lingkup:

- Memindahkan shortlist aman yang sudah disetujui owner ke tabel utama `Input Owner`.
- Mengunci scope rollout awal ke `desa only`.
- Menggeser state concern dari `awaiting-owner-group-target` ke `awaiting-owner-mode-target`.

Artefak:

- `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`

Perintah validasi:

- `rg -n "awaiting-owner-mode-target|Konfirmasi owner 2026-03-08|desa only" docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`
  - hasil: `PASS`.
- `rg -n "\\| 4 \\| agenda-surat \\| sekretaris-tpk \\| sekretaris-tpk \\||\\| 16 \\| data-warga \\| pokja-i \\| pokja-i \\||\\| 29 \\| posyandu \\| pokja-iv \\| pokja-iv \\|" docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`
  - hasil: `PASS`.

Keputusan:

- Owner menyetujui shortlist aman tahap-1 untuk dimasukkan ke tabel utama.
- Group target untuk 11 modul shortlist dikunci sama dengan rekomendasi aman.
- `Mode Target` belum dikunci, sehingga concern belum siap implementasi runtime.

Status:

- `PASS` (`rgm26a1-owner-shortlist-confirmed-awaiting-mode-target`).

## Validasi Batch Aman No-op Runtime untuk Concern Grouping Modul (`RGM26A1`): 2026-03-08

Ruang lingkup:

- Memvalidasi bahwa shortlist aman yang sudah disetujui owner tidak menghasilkan delta runtime karena mapping aktif sudah sesuai.
- Membuktikan baseline aman lewat targeted test akses, payload, middleware, dan layout contract.

Artefak:

- `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`
- `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- `resources/js/Layouts/DashboardLayout.vue`
- `tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
- `tests/Feature/MenuVisibilityPayloadTest.php`
- `tests/Feature/ModuleVisibilityMiddlewareTest.php`
- `tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`

Perintah validasi:

- `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php tests/Unit/Frontend/DashboardLayoutMenuContractTest.php --compact`
  - hasil: `PASS` (`46` tests, `347` assertions).

Keputusan:

- Shortlist aman `RGM26A1` tervalidasi sebagai `no-op runtime` karena group target dan default mode aman sudah cocok dengan runtime aktif.
- Patch backend/frontend belum diperlukan selama owner tidak meminta deviasi `Mode Target`.

Status:

- `PASS` (`rgm26a1-safe-batch-noop-runtime-validated`).

## Dokumentasi Sumber Data dan Cek Manual Lampiran 4.24: 2026-03-08

Ruang lingkup:

- Menambahkan markdown khusus yang menjelaskan sumber data operasional Lampiran `4.24`.
- Menandai bahwa header autentik dan jalur sumber data concern ini sudah dicek manual dan dinyatakan sesuai.

Artefak:

- `docs/domain/DATA_KEGIATAN_PKK_POKJA_IV_4_24_SUMBER_DATA.md`
- `docs/domain/DATA_KEGIATAN_PKK_POKJA_IV_4_24_MAPPING.md`
- `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`

Perintah validasi:

- `rg -n "Sumber Data Lampiran 4.24|Status cek manual sumber data report|Status akhir: \\*\\*sesuai\\*\\*" docs/domain/DATA_KEGIATAN_PKK_POKJA_IV_4_24_SUMBER_DATA.md`
  - hasil: `PASS`.
- `rg -n "DATA_KEGIATAN_PKK_POKJA_IV_4_24_SUMBER_DATA.md|dokumen sumber data rinci" docs/domain/DATA_KEGIATAN_PKK_POKJA_IV_4_24_MAPPING.md docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`
  - hasil: `PASS`.

Keputusan:

- Lampiran `4.24` dikunci sebagai concern `report-only via catatan-keluarga` dengan sumber data lintas modul yang sudah terdokumentasi eksplisit.
- Status cek manual concern ini dinyatakan `sesuai` terhadap implementasi aktif dan bukti visual header autentik.

Status:

- `PASS` (`lampiran-424-sumber-data-manual-check-documented`).

## Snapshot Closure Concern Aktif yang Dipindah dari Index: 2026-03-09

Catatan:

- Pada 2026-03-09 `docs/process/OPERATIONAL_VALIDATION_LOG.md` dikompaksi kembali menjadi index aktif.
- Detail concern `done` yang sebelumnya membebani index aktif diringkas dan dipindahkan ke arsip periodik ini.

### Roadmap Optimasi Inertia Bertahap (`SPA26A1`) - 2026-03-08

- Status concern: `done` (`state:wave1-wave5-pilots-validated`).
- Keputusan utama:
  - stack tetap `Laravel + Inertia + Vue`,
  - tidak ada migrasi ke SPA murni pada fase ini,
  - urutan optimasi dikunci: partial reload -> lazy fetch -> komponen stateful -> endpoint JSON kecil terkontrol.
- Evidence:
  - `Inertia::render` di backend: `268`,
  - coupling frontend ke `@inertiajs/vue3`: `212`,
  - feature test `assertInertia(...)`: `188`.

### Pilot Dashboard Wave 1 (`DWI26A1`) - 2026-03-08

- Status concern: `done` (`state:full-suite-validated`).
- Hasil batch:
  - prop dashboard diubah menjadi closure untuk partial reload,
  - helper visit dashboard terpusat memakai `only: ['dashboardStats', 'dashboardCharts', 'dashboardBlocks', 'dashboardContext']`,
  - test partial reload ditambahkan ke `DashboardDocumentCoverageTest`.
- Validasi:
  - `DashboardActivityChartTest` `PASS` (`6` tests),
  - `DashboardDocumentCoverageTest` `PASS` (`12` tests),
  - full suite operator lokal `PASS` (`1154 passed`, `7730 assertions`).

### Pilot User Management Index Wave 1 (`USR26A1`) - 2026-03-08

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Hasil batch:
  - prop index user management diubah menjadi closure,
  - helper visit terpusat memakai partial prop `users` dan `filters`,
  - pagination mendukung partial reload.
- Validasi:
  - `UserManagementIndexPaginationTest` `PASS` (`7` tests, `137` assertions),
  - full suite operator lokal `PASS` (`1155 passed`, `7760 assertions`),
  - `npm run build` `PASS`.

### Pilot Dashboard Wave 2 Deferred Blocks (`DBL26A1`) - 2026-03-08

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Hasil batch:
  - `dashboardBlocks` dipindah menjadi deferred prop,
  - fallback loading dan guard test deferred prop ditambahkan.
- Validasi:
  - `DashboardActivityChartTest` `PASS` (`6` tests, `148` assertions),
  - `DashboardDocumentCoverageTest` `PASS` (`13` tests, `441` assertions),
  - `DashboardChartPdfPrintTest` `PASS` (`3` tests),
  - full suite operator lokal `PASS` (`1156 passed`, `7975 assertions`),
  - `npm run build` `PASS`.

### Pilot Dashboard Wave 3 Stateful Presentational UI (`DBS26A1`) - 2026-03-08

- Status concern: `done` (`state:targeted-and-build-validated`).
- Hasil batch:
  - `Dashboard.vue` memakai `useRemember` untuk `expandedBlockKeys`.
- Validasi:
  - `DashboardDocumentCoverageTest` `PASS` (`13` tests, `441` assertions),
  - `DashboardActivityChartTest` `PASS` (`6` tests, `148` assertions),
  - `npm run build` `PASS`.

### Pilot Dashboard Wave 4 JSON Detail Widget (`DBJ26A1`) - 2026-03-08

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Hasil batch:
  - nested detail `per_module` dipindahkan ke endpoint JSON kecil,
  - detail block dimuat saat panel dibuka.
- Validasi:
  - `DashboardBlockDetailWidgetTest` `PASS` (`3` tests, `37` assertions),
  - `DashboardDocumentCoverageTest` `PASS` (`13` tests, `441` assertions),
  - unit use case + coverage gate `PASS` (operator lokal),
  - full suite `PASS`,
  - `npm run build` `PASS`.

### Pilot Dashboard Wave 5 Fetch Failure Telemetry (`DBT26A1`) - 2026-03-09

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Hasil batch:
  - helper runtime error global dashboard diekspos untuk fetch async,
  - widget detail dashboard mengirim telemetry saat fetch gagal.
- Validasi:
  - `UiRuntimeErrorLogTest` `PASS` (`2` tests),
  - `DashboardBlockDetailWidgetTest` `PASS` (`3` tests),
  - `npm run build` `PASS`,
  - full suite `PASS` (`1163 passed`, `8025 assertions`).

### Pilot Kecamatan Desa Activities Partial Reload (`KDA26A1`) - 2026-03-09

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Hasil batch:
  - controller mengirim `activities` dan `filters` via closure,
  - page memakai helper visit terpusat + partial prop `activities` dan `filters`.
- Validasi:
  - `KecamatanDesaActivityTest` `PASS` (`10` tests, `127` assertions),
  - `npm run build` `PASS`,
  - full suite `PASS` (`1164 passed`, `8061 assertions`).

### Pilot Kecamatan Desa Arsip Partial Reload (`KAR26A1`) - 2026-03-09

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Hasil batch:
  - controller mengirim `documents` dan `filters` via closure,
  - page memakai helper visit terpusat + partial prop `documents` dan `filters`.
- Validasi:
  - `KecamatanDesaArsipTest` `PASS` (`5` tests, `57` assertions),
  - `npm run build` `PASS`,
  - full suite `PASS` (`1165 passed`, `8096 assertions`).

### Pilot Kecamatan Activities Partial Reload (`KAC26A1`) - 2026-03-09

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Hasil batch:
  - controller mengirim `activities` dan `filters` via closure,
  - page memakai helper visit terpusat + partial prop `activities` dan `filters`.
- Validasi:
  - `KecamatanActivityTest` `PASS` (`10` tests, `84` assertions),
  - `npm run build` `PASS`,
  - full suite `PASS` (`1166 passed`, `8128 assertions`).

### Concern Archived dan Hardening Pendukung - 2026-03-09

- `TAG26A1`:
  - concern `Refactor Tahun Anggaran` tetap diarsipkan,
  - `Arsip` tetap dikecualikan dari isolasi lintas tahun,
  - closure validation terdokumentasi `PASS` (`migrate:fresh --seed`, smoke regression lintas role/scope, full suite).
- `SFC26A1`:
  - policy placement kode concern baru aktif,
  - strategy arsip TODO aktif,
  - artefak root/generated dipisahkan dari source tracked.
- `MFC26A1`:
  - migration squash selesai,
  - `migrate:fresh --seed`, targeted test, full test, dan build tercatat `PASS`.
- mitigasi bottleneck markdown aktif:
  - registry `TTM25R1` dipangkas menjadi thin registry,
  - snapshot penuh registry dipindahkan ke arsip,
  - single-path memuat `Context Load Order (Anti-Bottleneck)`.

### Audit Markdown Context Budget (`MKB26A1`) - 2026-03-09

- Status concern: `done` (`state:context-space-budget-locked`).
- Hasil batch:
  - formula canonical `estimated_tokens = ceil(chars / 4)` dikunci,
  - reserve markdown aktif `35%` dikunci,
  - band kerja harian repo dikunci pada `12k-18k` estimated markdown tokens,
  - ladder ekspansi saat context window AI meningkat didokumentasikan.
- Evidence:
  - minimum routing pack: `8,600` est. tokens,
  - default execution pack: `12,114-13,194` est. tokens,
  - extended governance pack: `14,950-17,681` est. tokens,
  - ideal context window repo saat ini: `20k-28k` tokens.
- Validasi:
  - audit ukuran file + kalkulasi pack `PASS`,
  - audit sinkronisasi TODO + ADR + process refs + registry/log `PASS`,
  - `php artisan test` tidak dijalankan karena concern `doc-only`.

### Dedup Dan Compaction Governance Context Pack (`GCP26A1`) - 2026-03-09

- Status concern: `done` (`state:governance-pack-dedup-and-log-compacted`).
- Hasil batch:
  - ownership governance markdown dikunci lintas `AGENTS`, `single-path`, `budget`, dan `playbook`,
  - `OPERATIONAL_VALIDATION_LOG.md` dipadatkan kembali menjadi index aktif,
  - detail closure concern `done` dipindahkan ke arsip periodik ini.
- Validasi:
  - audit overlap referensi `PASS`,
  - review diff lintas docs `PASS`,
  - ukuran `OPERATIONAL_VALIDATION_LOG.md` pasca compaction turun di bawah soft cap.

### Thin Registry Dan Annex Retrieval Guardrail (`GCP26A2`) - 2026-03-09

- Status concern: `done` (`state:ttm-thinned-and-annex-guardrail-locked`).
- Hasil batch:
  - `TTM25R1` dipadatkan kembali menjadi registry concern `planned/in-progress` + pointer closure,
  - concern `done` terbaru dipindahkan dari tabel aktif ke pointer closure/log periodik,
  - drift struktur `P-014` pada lampiran pattern details diperbaiki,
  - guard bahwa `AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md` adalah annex on-demand dikunci lintas process docs.
- Evidence:
  - `TTM25R1` turun menjadi `4,303` chars,
  - `AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md` berada di `45,130` chars dan tetap di bawah ambang shard `50,000`,
  - `OPERATIONAL_VALIDATION_LOG.md` tetap `3,132` chars setelah sync lanjutan.
- Validasi:
  - audit ukuran file pasca compaction `PASS`,
  - audit referensi guard annex/pointer closure `PASS`,
  - review diff lintas registry + budget + playbook + log `PASS`,
  - `php artisan test` tidak dijalankan karena concern `doc-only`.

### Audit Automation Markdown Governance (`GCP26A3`) - 2026-03-09

- Status concern: `done` (`state:audit-script-generator-hook-and-ci-gate-locked`).
- Hasil batch:
  - `scripts/audit_markdown_governance.ps1` ditambahkan untuk audit soft cap, thin registry, index aktif, dan guard annex on-demand,
  - `scripts/generate_todo.ps1` kini menjalankan audit governance secara default setelah generate,
  - workflow `.github/workflows/markdown-governance-gate.yml` ditambahkan sebagai CI gate khusus governance markdown.
- Validasi:
  - audit script lokal `PASS`,
  - generate TODO uji sementara dengan hook audit `PASS`,
  - review diff lintas script + workflow + docs governance `PASS`,
  - `php artisan test` tidak dijalankan karena concern `doc+script-only`.

### Audit Baseline P0 RGM26A1 - 2026-03-10

- Ringkasan: baseline grouping/mode/middleware/sidebar tervalidasi konsisten.
- Source of truth grouping & mode berada di `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php` (`GROUP_MODULES`, `GROUPS_BY_SCOPE`, `ROLE_GROUP_MODES`, `ROLE_MODULE_MODE_OVERRIDES`).
- Middleware `module.visibility` diregistrasi di `bootstrap/app.php`, dipakai pada route group `desa` dan `kecamatan` di `routes/web.php`, dan menolak write intent bila mode bukan `read-write` (`app/Http/Middleware/EnsureModuleVisibility.php`).
- Sidebar `resources/js/Layouts/DashboardLayout.vue` menggunakan `menuGroupModes`/`moduleModes` dari `HandleInertiaRequests`, memfilter item via `withMode()`, dan group `monitoring` hanya tampil di scope `kecamatan` (item default `uiVisibility: 'disabled'`).

### Audit Normalisasi Database Formal 1NF-3NF Bertahap (`NFM26A1`) - 2026-03-11

- Status concern: `done` (`state:batch-1-3-normalization-closed`).
- Hasil batch:
  - peta formal 1NF/2NF/3NF lintas tabel non-legacy + klasifikasi risiko,
  - batch 1-3 normalisasi multi-value dan transitive dependency selesai,
  - kontrak adapter kompatibilitas dan rencana batch berikutnya dikunci di TODO concern.
- Validasi (evidence tercatat di TODO):
  - L1: audit scoped migrasi/kolom `PASS`,
  - L3: `php artisan test --compact` `PASS`,
  - L4: `php artisan migrate:fresh --seed` `PASS`.

### Roadmap Ekspansi Audit UI/UX Runtime Evidence (`IWN26A1`) - 2026-03-11

- Status concern: `done` (`state:batch-P1-P14-implemented-and-validated`).
- Hasil batch:
  - roadmap 3 fase ekspansi runtime evidence terkunci,
  - lane smoke/a11y mandatory + visual/perf candidate gate terimplementasi,
  - trend evaluation + history cache + schedule 2x sehari aktif,
  - sinkronisasi TODO concern UI agar evidence runtime masuk ke setiap concern UI.
- Validasi (evidence tercatat di TODO):
  - `npm run test:e2e:smoke`, `npm run test:e2e:a11y`, `npm run test:e2e:visual`, `npm run test:e2e:perf`,
  - `npm run test:e2e:perf:summary` + `npm run test:e2e:perf:trend`,
  - `php artisan test --filter=DashboardLayoutMenuContractTest` `PASS`.

### Standarisasi Struktur Dokumen Referensi Domain (`RFD26A1`) - 2026-03-11

- Status concern: `done` (`state:taxonomy-naming-manifest-pilot-rename-closed`).
- Hasil batch:
  - taxonomy `canonical/`, `supporting/`, `evidence/screenshots/`, `_local/` dikunci,
  - naming convention `doc-key` lower-case dikunci,
  - `MIGRATION_MANIFEST.md` disusun + pilot rename `lampiran-4-22-cara-pengisian.pdf`,
  - referensi path pilot disinkronkan pada `docs/domain/**` terkait.
- Validasi:
  - `pwsh -File scripts/audit_markdown_paths.ps1` `PASS`,
  - `php artisan test` tidak dijalankan (doc-only).

### Modul Data Kegiatan PKK Pokja II (`PKJ2A1`) - 2026-03-11

- Status concern: `done` (`state:report-docs-tests-synced`).
- Hasil batch:
  - sumber data Lampiran 4.22 disinkronkan ke implementasi aktif,
  - test header + agregasi report Pokja II ditambahkan,
  - validasi report berbasis `CatatanKeluargaRepository` terjaga.
- Validasi:
  - `php artisan test --filter=RekapCatatanDataKegiatanWargaReportPrintTest --compact` `PASS` (29 tests).

### Governance Audit Wave Followup (`GCP26A4`) - 2026-03-13

- Status concern: `done` (`state:batch-1-5-closed`).
- Hasil batch:
  - batch 1-4: audit governance markdown + thinning + shard annex diselesaikan dengan boundary commit terpisah,
  - batch 5: verifikasi remote workflow dilakukan; run `22890639031` gagal pada step `Run mandatory domain/PDF gates`,
  - error utama: `Vite manifest not found at public/build/manifest.json` saat `tests/Feature/DashboardActivityChartTest.php:231`.
- Validasi:
  - verifikasi workflow GitHub Actions `domain-contract-gate.yml` selesai (status `failed` pada gate domain/PDF),
  - reproduksi lokal targeted test terkait `PASS` (tercatat di TODO concern).
