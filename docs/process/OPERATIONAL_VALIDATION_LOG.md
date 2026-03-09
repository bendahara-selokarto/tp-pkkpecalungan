# Operational Validation Log (Index Aktif)

Tujuan:

- Menjaga bukti validasi operasional tetap tersedia tanpa membebani konteks aktif AI.
- Menyediakan ringkasan aktif + pointer deterministik ke arsip periodik.

## Arsip Periodik

| Periode | File Arsip | Status |
| --- | --- | --- |
| 2026 Q1 (Feb-Mar 2026) | `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md` | `active-archive` |

Aturan:

- Entry historis lengkap dipindahkan ke arsip periodik (per kuartal/bulan sesuai kebutuhan growth).
- File ini hanya menyimpan snapshot aktif agar jalur baca AI tetap ringkas.

## Snapshot Aktif (Concern Berjalan)

### Registry SOT (`TTM25R1`)

- Source of truth concern aktif: `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`.
- Active concern saat ini:
  - `docs/process/TODO_IWN26A1_ROADMAP_EKSPANSI_AUDIT_UI_UX_RUNTIME_EVIDENCE_2026_03_03.md` (`in-progress`)
  - `docs/process/TODO_IWN26B1_REFACTOR_GROUPING_MODUL_DOMAIN_E2E_2026_03_04.md` (`planned`)
  - `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md` (`planned`)
  - `docs/process/TODO_QG90A1_ROADMAP_SPRINT_NAIK_SKOR_PROJECT_90_PLUS_2026_03_07.md` (`planned`)
  - `docs/process/TODO_MKB26A1_AUDIT_OPTIMASI_MARKDOWN_CONTEXT_BUDGET_2026_03_09.md` (`done`)
  - `docs/process/TODO_SPA26A1_ROADMAP_OPTIMASI_BERTAHAP_INERTIA_TANPA_MIGRASI_SPA_MURNI_2026_03_08.md` (`done`)
  - `docs/process/TODO_DWI26A1_PILOT_DASHBOARD_WAVE_1_PARTIAL_RELOAD_DAN_PAYLOAD_SLIMMING_2026_03_08.md` (`done`)
  - `docs/process/TODO_USR26A1_PILOT_USER_MANAGEMENT_INDEX_PARTIAL_RELOAD_DAN_PAYLOAD_SLIMMING_2026_03_08.md` (`done`)
  - `docs/process/TODO_DBL26A1_PILOT_DASHBOARD_WAVE_2_DEFERRED_BLOCKS_DAN_LAZY_FETCH_2026_03_08.md` (`done`)
  - `docs/process/TODO_DBS26A1_PILOT_DASHBOARD_WAVE_3_STATEFUL_PRESENTATIONAL_UI_2026_03_08.md` (`done`)
  - `docs/process/TODO_DBJ26A1_PILOT_DASHBOARD_WAVE_4_JSON_DETAIL_WIDGET_PER_DESA_2026_03_08.md` (`done`)
  - `docs/process/TODO_DBT26A1_PILOT_DASHBOARD_WAVE_5_FETCH_FAILURE_TELEMETRY_2026_03_09.md` (`done`)
  - `docs/process/TODO_KDA26A1_PILOT_KECAMATAN_DESA_ACTIVITIES_PARTIAL_RELOAD_2026_03_09.md` (`done`)
- Catatan sinkronisasi `RGM26A1`:
  - histori no-op tervalidasi pada 2026-03-07 tetap dipertahankan di TODO concern sebagai audit trail,
  - status aktif terbaru tetap `planned` (`state:awaiting-owner-group-target`) sampai ada input owner baru.

### Roadmap Optimasi Inertia Bertahap (`SPA26A1`) - 2026-03-08

- Status concern: `done` (`state:wave1-wave5-pilots-validated`).
- Keputusan concern:
  - stack utama tetap `Laravel + Inertia + Vue`,
  - tidak ada migrasi ke SPA murni pada fase ini,
  - urutan optimasi dikunci: partial reload -> lazy fetch -> komponen stateful -> endpoint JSON kecil terkontrol.
- Baseline evidence concern:
  - `Inertia::render` di backend: `268`,
  - coupling frontend ke `@inertiajs/vue3` (`useForm` + `router.get` dan pola sejenis hasil audit scoped): `212`,
  - feature test `assertInertia(...)`: `188`,
  - `routes/api.php`: belum ada.

### Pilot Dashboard Wave 1 (`DWI26A1`) - 2026-03-08

- Status concern: `done` (`state:full-suite-validated`).
- Scope batch:
  - `app/Http/Controllers/DashboardController.php`,
  - `resources/js/Pages/Dashboard.vue`,
  - `tests/Feature/DashboardActivityChartTest.php`,
  - `tests/Feature/DashboardDocumentCoverageTest.php`.
- Keputusan batch:
  - tidak menambah endpoint JSON baru,
  - fokus pada helper visit dashboard, partial reload, dan payload slimming berbasis Inertia.
- Baseline evidence batch:
  - `Dashboard.vue` memiliki beberapa `router.get('/dashboard', ...)` pada action filter dan watcher sinkronisasi query,
  - kontrak dashboard saat ini dilindungi oleh feature test dashboard activity + document coverage,
  - payload dashboard utama saat ini tetap membawa `dashboardStats`, `dashboardCharts`, `dashboardBlocks`, `dashboardContext`.
- Hasil batch implementasi:
  - `DashboardController` kini mengirim prop dashboard berbasis closure agar partial reload hanya mengevaluasi prop yang diminta,
  - `Dashboard.vue` memakai helper visit dashboard terpusat dengan `only: ['dashboardStats', 'dashboardCharts', 'dashboardBlocks', 'dashboardContext']`,
  - test baru partial reload ditambahkan ke `DashboardDocumentCoverageTest`.
- Validasi batch:
  - `php artisan test tests/Feature/DashboardActivityChartTest.php --compact` -> `PASS` (`6` tests),
  - `php artisan test tests/Feature/DashboardDocumentCoverageTest.php --compact` -> `PASS` (`12` tests, termasuk partial reload),
  - operator lokal menjalankan `php artisan test --compact` -> `PASS` (`1154 passed`, `7730 assertions`, `89.00s`).

### Pilot User Management Index Wave 1 (`USR26A1`) - 2026-03-08

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Scope batch:
  - `app/Http/Controllers/SuperAdmin/UserManagementController.php`,
  - `resources/js/Pages/SuperAdmin/Users/Index.vue`,
  - `tests/Feature/SuperAdmin/UserManagementIndexPaginationTest.php`.
- Target batch:
  - helper visit index user management,
  - partial reload pada paginasi/per-page,
  - test partial reload yang spesifik.
- Hasil batch implementasi:
  - `UserManagementController@index` kini mengirim prop index berbasis closure untuk mendukung partial reload,
  - `SuperAdmin/Users/Index.vue` memakai helper visit terpusat dengan partial prop `users` dan `filters`,
  - `PaginationBar` kini menerima opsi visit Inertia opsional, dan halaman user management menggunakannya untuk menjaga klik pagination tetap partial reload,
  - test partial reload ditambahkan pada `UserManagementIndexPaginationTest`.
- Validasi batch:
  - `php artisan test tests/Feature/SuperAdmin/UserManagementIndexPaginationTest.php --compact` -> `PASS` (`7` tests, `137` assertions, `15.29s`),
  - operator lokal menjalankan `php artisan test --compact` -> `PASS` (`1155 passed`, `7760 assertions`, `85.70s`),
  - operator lokal menjalankan `npm run build` -> `PASS` (`built in 7.82s`).

### Pilot Dashboard Wave 2 Deferred Blocks (`DBL26A1`) - 2026-03-08

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Scope batch:
  - `app/Http/Controllers/DashboardController.php`,
  - `resources/js/Pages/Dashboard.vue`,
  - `tests/Feature/DashboardDocumentCoverageTest.php`.
- Target batch:
  - menunda `dashboardBlocks` dari first load,
  - menjaga stats/charts/context tetap eager,
  - menambah fallback loading dan guard test deferred prop.
- Hasil batch implementasi:
  - `DashboardController` memisahkan resolver stats/charts/context dari resolver blocks dan menandai `dashboardBlocks` sebagai deferred prop,
  - `Dashboard.vue` memakai komponen `Deferred` untuk fallback loading blok dashboard dan menggating watcher agar tidak salah membaca state pending,
  - `DashboardDocumentCoverageTest` dipindah ke contract deferred (`missing` pada first load + `loadDeferredProps('dashboard-blocks')`).
- Validasi batch:
  - `php artisan test tests/Feature/DashboardActivityChartTest.php --compact` -> `PASS` (`6` tests, `148` assertions, `19.49s`),
  - `php artisan test tests/Feature/DashboardDocumentCoverageTest.php --compact` -> `PASS` (`13` tests, `441` assertions, `31.43s`),
  - `php artisan test tests/Feature/DashboardChartPdfPrintTest.php --compact` -> `PASS` (`3` tests, `6` assertions, `25.41s`),
  - operator lokal menjalankan `php artisan test --compact` -> `PASS` (`1156 passed`, `7975 assertions`, `121.79s`),
  - operator lokal menjalankan `npm run build` -> `PASS` (`built in 11.30s`).

### Pilot Dashboard Wave 3 Stateful Presentational UI (`DBS26A1`) - 2026-03-08

- Status concern: `done` (`state:targeted-and-build-validated`).
- Scope batch:
  - `resources/js/Pages/Dashboard.vue`.
- Target batch:
  - menjaga state expand/collapse blok tetap lokal dan persisten antar visit Inertia,
  - tanpa menambah query, prop backend, atau endpoint baru.
- Hasil batch implementasi:
  - `Dashboard.vue` memakai `useRemember` untuk `expandedBlockKeys`.
- Validasi batch:
  - `php artisan test tests/Feature/DashboardDocumentCoverageTest.php --compact` -> `PASS` (`13` tests, `441` assertions, `22.46s`),
  - `php artisan test tests/Feature/DashboardActivityChartTest.php --compact` -> `PASS` (`6` tests, `148` assertions, `16.42s`),
  - operator lokal menjalankan `npm run build` -> `PASS`.

### Pilot Dashboard Wave 4 JSON Detail Widget (`DBJ26A1`) - 2026-03-08

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Scope batch:
  - `app/Http/Controllers/DashboardController.php`,
  - `app/Domains/Wilayah/Dashboard/UseCases/BuildDashboardBlockDetailWidgetUseCase.php`,
  - `resources/js/Pages/Dashboard.vue`,
  - `routes/web.php`,
  - `tests/Feature/DashboardBlockDetailWidgetTest.php`.
- Target batch:
  - memindahkan nested detail `per_module` dari block per-desa ke endpoint JSON kecil,
  - memuat detail hanya saat block dibuka.
- Hasil batch implementasi:
  - `DashboardController` menambahkan route/detail response untuk block key yang didukung,
  - use case baru membangun payload detail widget per-desa berbasis repository existing,
  - `Dashboard.vue` memuat rincian per-desa/per-modul saat block dibuka,
  - payload awal block pilot hanya membawa summary item tanpa nested `per_module`.
- Validasi batch:
  - `php artisan test tests/Feature/DashboardBlockDetailWidgetTest.php --compact` -> `PASS` (`3` tests, `37` assertions, `10.79s`),
  - `php artisan test tests/Feature/DashboardDocumentCoverageTest.php --compact` -> `PASS` (`13` tests, `441` assertions, `17.39s`),
  - `php artisan test tests/Unit/UseCases/BuildDashboardBlockDetailWidgetUseCaseTest.php --compact` -> `PASS` (operator lokal),
  - `php artisan test tests/Unit/Architecture/UnitCoverageGateTest.php --compact` -> `PASS` (operator lokal),
  - `php artisan test --compact` -> `PASS` (operator lokal),
  - `npm run build` -> `PASS` (operator lokal).

### Pilot Dashboard Wave 5 Fetch Failure Telemetry (`DBT26A1`) - 2026-03-09

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Scope batch:
  - `resources/js/app.js`,
  - `resources/js/Pages/Dashboard.vue`,
  - `tests/Feature/UiRuntimeErrorLogTest.php`,
  - `tests/Feature/DashboardBlockDetailWidgetTest.php`.
- Target batch:
  - menghubungkan fetch failure widget dashboard ke telemetry runtime existing,
  - menjaga fallback UI tetap non-blocking.
- Hasil batch implementasi:
  - helper runtime error global dashboard diekspos untuk concern async fetch,
  - widget detail dashboard mengirim telemetry dengan source sempit saat fetch gagal.
- Validasi batch:
  - `php artisan test tests/Feature/UiRuntimeErrorLogTest.php --compact` -> `PASS` (`2` tests, `5` assertions, `38.20s`),
  - `php artisan test tests/Feature/DashboardBlockDetailWidgetTest.php --compact` -> `PASS` (`3` tests, `37` assertions, `40.61s`),
  - `npm run build` -> `PASS` (`built in 3m 36s`),
  - `php artisan test --compact` -> `PASS` (`1163 passed`, `8025 assertions`, `477.24s`).

### Pilot Kecamatan Desa Activities Partial Reload (`KDA26A1`) - 2026-03-09

- Status concern: `done` (`state:full-suite-and-build-validated`).
- Scope batch:
  - `app/Domains/Wilayah/Activities/Controllers/KecamatanDesaActivityController.php`,
  - `resources/js/Pages/Kecamatan/DesaActivities/Index.vue`,
  - `tests/Feature/KecamatanDesaActivityTest.php`.
- Target batch:
  - menggulirkan partial reload ke loop filter/paginasi monitoring kegiatan desa kecamatan,
  - menjaga contract auth/scope/filter tetap sama.
- Hasil batch implementasi:
  - controller mengirim `activities` dan `filters` dengan closure untuk partial reload,
  - page memakai helper visit terpusat dan pagination partial prop `activities` + `filters`,
  - guard test partial reload concern ditambahkan.
- Validasi batch:
  - `php artisan test tests/Feature/KecamatanDesaActivityTest.php --compact` -> `PASS` (`10` tests, `127` assertions, `54.21s`),
  - `npm run build` -> `PASS` (`built in 2m 42s`),
  - `php artisan test --compact` -> `PASS` (`1164 passed`, `8061 assertions`, `635.46s`).

### Concern Archived

- `TAG26A1` (`Refactor Tahun Anggaran`) diarsipkan setelah closure `done (state:wave4-hardening-complete)` agar context aktif AI tetap ringan.
- Pointer arsip:
  - TODO concern: `docs/process/archive/2026_03/TODO_TAG26A1_REFACTOR_ISOLASI_TAHUN_ANGGARAN_LINTAS_MODUL_2026_03_07.md`
  - ADR terkait: `docs/adr/ADR_0005_TAHUN_ANGGARAN_CONTEXT_ISOLATION.md`
- Ringkasan closure:
  - isolasi `tahun_anggaran` lintas modul selesai untuk scope concern yang dikunci,
  - `Arsip` dikecualikan eksplisit karena memang menyediakan informasi lintas tahun,
  - closure validation: `migrate:fresh --seed` `PASS`, smoke regression lintas role/scope `87 passed`, full suite `1153 passed (7702 assertions)`.

### Hardening Struktur Folder (`SFC26A1`) - 2026-03-07

- Status concern: `done` (`state:structure-hardened`) (arsip concern ada di `docs/process/archive/2026_03/`).
- Dampak penting:
  - policy placement kode concern baru aktif,
  - strategy arsip TODO aktif,
  - artefak root/generated dipisahkan dari source tracked.

### Cleanup Pasca Migrate Fresh (`MFC26A1`) - 2026-03-07

- Status concern: `done` (arsip concern ada di `docs/process/archive/2026_03/`).
- Dampak penting:
  - migration squash selesai,
  - validasi `migrate:fresh --seed`, targeted test, full test, dan build tercatat `PASS`.

### Mitigasi Bottleneck Markdown Aktif - 2026-03-07

- Registry SOT `TTM25R1` dipangkas menjadi thin registry aktif:
  - `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`.
- Snapshot penuh registry dipindahkan ke arsip:
  - `docs/process/archive/registry/TTM25R1_REGISTRY_FULL_2026_03_02.md`.
- Single-path diperbarui dengan `Context Load Order (Anti-Bottleneck)` agar arsip historis hanya dibaca on-demand.

### Audit Markdown Context Budget (`MKB26A1`) - 2026-03-09

- Status concern: `done` (`state:context-space-budget-locked`).
- Scope batch:
  - `AGENTS.md`,
  - `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`,
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`,
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md`,
  - `docs/process/PLANNING_ARTIFACT_INDEX.md`,
  - `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`,
  - `docs/process/OPERATIONAL_VALIDATION_LOG.md`,
  - `docs/process/MARKDOWN_CONTEXT_SPACE_BUDGET.md`,
  - `docs/adr/ADR_0006_MARKDOWN_CONTEXT_SPACE_BUDGET.md`,
  - `docs/process/TODO_MKB26A1_AUDIT_OPTIMASI_MARKDOWN_CONTEXT_BUDGET_2026_03_09.md`.
- Hasil batch implementasi:
  - formula canonical `estimated_tokens = ceil(chars / 4)` dikunci,
  - reserve markdown aktif `35%` dikunci,
  - band kerja harian repo dikunci pada `12k-18k` estimated markdown tokens,
  - ladder ekspansi saat context window AI meningkat didokumentasikan.
- Baseline evidence batch:
  - minimum routing pack: `8,600` est. tokens,
  - default execution pack: `12,114-13,194` est. tokens,
  - extended governance pack: `14,950-17,681` est. tokens,
  - ideal context window repo saat ini: `20k-28k` tokens.
- Validasi batch:
  - `wc -lcw` scoped audit artefak markdown aktif -> `PASS`,
  - audit `chars / 4` dan kalkulasi pack context -> `PASS`,
  - audit sinkronisasi TODO + ADR + process refs + registry/log -> `PASS`,
  - `php artisan test` tidak dijalankan karena concern `doc-only`.

### Sprint Quality Gate 90+ (`QG90A1`) - 2026-03-07

- Status concern: `planned`.
- Fokus concern:
  - menurunkan style debt secara bertahap pada scope prioritas sprint,
  - memastikan jalur E2E smoke tidak gagal karena dependency OS browser.
- Baseline evidence concern (sebelum eksekusi batch):
  - `php artisan test --compact`: `1057 passed`,
  - `./vendor/bin/pint --test`: `907 files`, `633 style issues`,
  - `npm run build`: `PASS`,
  - `npm run test:e2e:smoke`: `FAIL` (missing `libnspr4.so`).

## Protokol Update

1. Untuk validasi concern aktif, tambahkan ringkasan singkat di file index ini.
2. Untuk detail command output panjang, append ke file arsip periodik aktif.
3. Saat pergantian periode, buat file arsip baru di `docs/process/logs/` dan update tabel arsip.
