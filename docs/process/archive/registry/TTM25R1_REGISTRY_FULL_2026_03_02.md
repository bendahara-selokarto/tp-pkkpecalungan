# TODO TTM25R1 Registry Source of Truth TODO 2026-02-25

Tanggal: 2026-02-25  
Status: `done` (`state:truth-registry-closed-by-owner-approval`)

## Force Latest Marker

- Todo Code: `TTM25R1`
- Marker: `TODO-TRUTH-REGISTRY-2026-02-25-R1`
- Dokumen ini adalah registry tunggal untuk memilih TODO acuan per concern.
- Jika ada lebih dari satu TODO pada concern yang sama, keputusan wajib mengikuti kolom `Source of Truth` di registry ini.

## Tujuan

- Menghapus ambiguity lintas TODO dengan pengelompokan per concern.
- Menetapkan satu dokumen acuan tegas (`Source of Truth`) per concern.
- Menurunkan dokumen lain menjadi `historical`, `superseded`, atau `child-spec`.

## Registry Concern

| Concern ID | Concern | Source of Truth (SOT) | Status Concern | Dokumen Terkait (Non-SOT) | Keputusan Anti-Ambiguity |
| --- | --- | --- | --- | --- | --- |
| `C-DASH-CHART` | Koherensi chart/filter lintas role pasca Apex | `docs/process/archive/2026_02/TODO_DCF25R1_KOHERENSI_CHART_FILTER_LINTAS_ROLE_2026_02_25.md` | `done` | `TODO_DKB25R1`, `TODO_DUC23R1`, `TODO_DRA23R1`, `TODO_DRM24R1`, `TODO_DRL24R1`, `TODO_SCENARIO_KECAMATAN_SECTION4_POKJA_I_2026_02_23.md` | Untuk `pie/bar`, `section1_month`, dan query chart: hanya ikuti `DCF25R1` (status registry disinkronkan 2026-03-01). |
| `C-DASH-BASELINE` | Koherensi baseline dashboard sekretaris kecamatan | `docs/process/archive/2026_02/TODO_KOHERENSI_KRITIS_DASHBOARD_SEKRETARIS_KECAMATAN_BASELINE_2026_02_25.md` | `done` | `TODO_REFACTOR_DASHBOARD_*`, `TODO_UI_DASHBOARD_CHART_*` | Baseline visual lintas role tetap refer ke `DKB25R1`; concern chart aktif dipindah ke `DCF25R1`. |
| `C-SIDEBAR-UI` | Penataan menu/sidebar UI eksperimen | `docs/process/archive/2026_02/TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md` | `done` (`state:experimental-ui-closed-by-owner-approval`) | `TODO_UI_VISIBILITY_BY_PENANGGUNGJAWAB.md`, `TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md` | Concern sidebar UI-only ditutup berdasarkan persetujuan owner setelah seluruh guard aktif (coverage PDF, anti-duplikasi, active-state, persistensi collapse) tervalidasi. |
| `C-ARSIP-MGMT` | Management arsip oleh super-admin | `docs/process/archive/2026_02/TODO_ASM26B1_MANAGEMENT_ARSIP_SUPER_ADMIN_2026_02_27.md` | `done` | `TODO_ARS26A1_MENU_ARSIP_DOKUMEN_STATIS_2026_02_27.md` (`historical-baseline`), `TODO_ARS26B2_HARDENING_AKSES_ARSIP_GLOBAL_PRIBADI_2026_02_28.md` (`child-spec`) | Untuk akses/management arsip gunakan `ASM26B1`; `ARS26A1` baseline historis, `ARS26B2` mengunci kontrak akses global/pribadi/monitoring (sinkronisasi status 2026-03-02). |
| `C-ROLE-OWNERSHIP` | Ownership modul role runtime | `docs/process/archive/2026_02/TODO_AUDIT_MODUL_ROLE_OWNERSHIP_2026_02_25.md` | `done` | `TODO_IMPLEMENTASI_ROLE_OWNERSHIP_POKJA_DESA_ONLY_2026_02_25.md`, `TODO_IMPLEMENTASI_ROLE_OWNERSHIP_NON_RW_RO_2026_02_25.md`, `TODO_IMPLEMENTASI_ROLE_OWNERSHIP_DEPRECATE_DATA_PELATIHAN_KADER_2026_02_25.md` | Audit modul adalah parent concern; tiga TODO implementasi diperlakukan child concern eksekusi. |
| `C-AUTENTIK-49A-4144B` | Sinkron autentik E2E lampiran 4.9a-4.14.4b | `docs/process/archive/undated/TODO_AUTENTIK_LAMPIRAN_4_9A_4_14_4B_E2E.md` | `done` | `TODO_AUTENTIK_DATA_UMUM_PKK_4_20A.md`, `TODO_AUTENTIK_DATA_UMUM_PKK_4_20B.md`, `TODO_AUTENTIK_DATA_KEGIATAN_PKK_4_23_4_24.md`, dll terkait subset lampiran | Jika terjadi konflik mapping/header subset, acuan final concern ini mengikuti dokumen E2E payung. |
| `C-AUTENTIK-4144C-4144F` | Sinkron autentik E2E lampiran 4.14.4c-4.14.4f | `docs/process/archive/undated/TODO_AUTENTIK_LAMPIRAN_4_14_4C_4F_E2E.md` | `done` | `TODO_IMPLEMENTASI_AUTENTIK_BUKU_KEGIATAN_2026_02_24.md`, `TODO_IMPLEMENTASI_AUTENTIK_BUKU_PROGRAM_KERJA_2026_02_24.md`, dll yang menyentuh subset sama | Untuk contract header/payload/report 4.14.4c-4.14.4f, payung E2E jadi acuan final. |
| `C-AUTENTIK-BKL-BKR-PAAR` | Kontrak autentik BKL/BKR/PAAR | `docs/process/archive/2026_02/TODO_AUTENTIK_BKL_BKR_PAAR_2026_02_24.md` | `done` | `TODO_AUTENTIK_BUKU_WAJIB_POKJA_I_DAN_BUKU_BANTU_2026_02_24.md` | Untuk header/mapping BKL-BKR-PAAR gunakan dokumen ini sebagai acuan tunggal. |
| `C-AUTENTIK-BUKU-BANTU` | Implementasi autentik buku bantu lanjutan | `docs/process/archive/2026_02/TODO_IMPLEMENTASI_AUTENTIK_BUKU_BANTU_LANJUTAN_2026_02_24.md` | `done` | `TODO_IMPLEMENTASI_AUTENTIK_BUKU_BANTUAN_2026_02_24.md` | Untuk `kader khusus/prestasi/inventaris/anggota pokja/simulasi`, acuan final ada di lanjutan. |
| `C-BUKU-ADMIN` | Ketersediaan dan autentik buku administrasi PKK | `docs/process/archive/2026_02/TODO_KETERSEDIAAN_BUKU_ADMIN_PKK_2026_02_27.md` | `done` | `docs/process/archive/2026_02/TODO_AUTENTIK_SEKRETARIS_INTI_2026_02_27.md` (`child-spec:done`) | Concern ketersediaan buku ditutup setelah child concern autentik sekretaris inti dikunci (status `unverified-local-extension` untuk ekstensi lokal tanpa template primer resmi, sinkronisasi 2026-03-02). |
| `C-UI-RUNTIME` | Guardrail runtime UI | `docs/process/archive/2026_02/TODO_UI_RUNTIME_GUARDRAIL_2026_02_24.md` | `done` | - | Concern tunggal, tidak overlap aktif. |
| `C-UI-PAGINATION` | Pagination UI E2E | `docs/process/archive/2026_03/TODO_PGM26A1_MITIGASI_GAP_PAGINATION_2026_03_02.md` | `done` (`mitigation-closed-2026-03-02`) | `docs/process/archive/2026_02/TODO_UI_PAGINATION_E2E_2026_02_24.md` (`historical-baseline:done`) | Concern pagination ditutup di `PGM26A1`; `UIP26A1` dipertahankan sebagai baseline historis fase awal E2E. |
| `C-UI-RESPONSIVE` | Refactor responsive UX layout lintas halaman | `docs/process/archive/2026_03/TODO_UXR26A1_REFACTOR_RESPONSIVE_UX_LAYOUT_2026_03_01.md` | `done` (`state:responsive-ux-closed`) | `TODO_REFACTOR_DASHBOARD_*`, `TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md` (`related-concern`) | Concern ditutup setelah rollout bertahap Dashboard/SuperAdmin/Arsip + hardening semantik navigasi + guard modal aksesibel + standardisasi state list tervalidasi dengan full suite hijau. |
| `C-USER-GUIDE` | User guide natural humanis | `docs/process/archive/2026_02/TODO_USER_GUIDE_NATURAL_HUMANIS_2026_02_24.md` | `done` | - | Concern tunggal, tidak overlap aktif. |
| `C-DATABASE-NORMALISASI` | Normalisasi database & legacy reduction | `docs/process/archive/2026_02/TODO_NORMALISASI_DATABASE_2026_02_24.md` | `done` | `TODO_CLEANUP_HIERARKI_FOLDER_2026_02_23.md`, `TODO_COVERAGE_UNIT_TEST_DAN_SEEDER_2026_02_22.md` | Untuk keputusan struktur data legacy gunakan TODO normalisasi sebagai acuan utama. |
| `C-ACCESS-CONTROL` | Management ijin akses modul-group role oleh super-admin | `docs/process/archive/2026_02/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md` | `done` (`state:phased-rollout-closed`) | `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md` (`decision-record`), `TODO_ACL26S1_SUPER_ADMIN_MATRIX_READ_ONLY_2026_02_28.md` (`child-spec:done`), `TODO_ACL26C1_PILOT_OVERRIDE_CATATAN_KELUARGA_2026_02_28.md` (`child-spec:done`), `TODO_ACL26A2_ROLLOUT_OVERRIDE_MODUL_ACTIVITIES_2026_03_02.md` (`child-spec:done`), `TODO_ACL26E2_PENUTUPAN_GAP_END_TO_END_MANAGEMENT_IJIN_AKSES_2026_03_02.md` (`child-spec:done`) | Concern ACL ditutup setelah keputusan stakeholder `go`, rollout batch 2 `agenda-surat`, regression gate ACL hijau, dan fallback rollback/hardcoded tetap tersedia. |
| `C-MODULE-VISIBILITY` | Monitoring visibility lintas semua modul | `docs/process/archive/2026_02/TODO_MONITORING_VISIBILITY_SEMUA_MODUL_2026_02_27.md` | `done` (`state:rolling-monitoring`, `state:global-visibility-gate`) | `docs/process/archive/2026_02/TODO_MONITORING_VISIBILITY_MODUL_KEGIATAN_2026_02_27.md` (`child-spec:done`) | Monitoring lintas semua modul dikunci di concern global; sub-scope `activities` dipertahankan untuk detail guard domain kegiatan (sinkronisasi status 2026-03-02). |
| `C-SEKCAM-ROADMAP` | Roadmap concern sekretaris kecamatan | `docs/process/archive/2026_02/TODO_SKC0201_ROADMAP_SEKRETARIS_KECAMATAN_2026_02_28.md` | `done` (`state:wave-delivery-closed`) | - | Roadmap concern `kecamatan-sekretaris` ditutup setelah gelombang 1 tervalidasi dan gelombang 2 dikunci sebagai `no-op terkontrol` (tidak ada concern pokja tambahan yang disetujui pada sesi ini). |
| `C-PROCESS-EXECUTION` | Jalur eksekusi AI zero ambiguity + self-reflective routing | `docs/process/archive/2026_03/TODO_SRR26A1_SELF_REFLECTIVE_ROUTING_2026_03_01.md` | `done` | `TODO_ZERO_AMBIGUITY_AI_SINGLE_PATH_2026_02_23.md` (`historical-baseline`), `TODO_FLOW_BACA_LAPOR_SINKRON_HEADER_TABEL.md` (`child-spec`), `TODO_BTLK26A1_OPTIMASI_BOTTLENECK_PROCESS_EXECUTION_2026_03_01.md` (`child-spec:done`), `docs/adr/ADR_0003_SELF_REFLECTIVE_ROUTING.md` (`decision-record`) | Eksekusi AI concern process wajib mengikuti single-path aktif dengan checkpoint refleksi terkontrol sebelum patch besar. |
| `C-FIXTURE-TEMPLATE` | Konsistensi fixture dan template report print | `docs/process/archive/2026_03/TODO_FTC26A1_FIXTURE_TEMPLATE_CONSISTENCY_2026_03_01.md` | `done` | `docs/process/OPERATIONAL_VALIDATION_LOG.md` (`blocker-trace`) | Kegagalan residual fixture/template ditangani di concern ini (terpisah dari process execution); status disinkronkan 2026-03-02 setelah targeted tests hijau. |
| `C-DOC-ARCH-V2` | Refactor arsitektur markdown TODO + ADR | `docs/process/archive/2026_02/TODO_MDA26R1_REFACTOR_MARKDOWN_ARSITEKTUR_BARU_2026_02_28.md` | `done` | `docs/adr/ADR_0001_DOCUMENTATION_GOVERNANCE_TODO_ADR.md` (`decision-record`) | Concern dokumentasi arsitektural mengikuti pasangan TODO + ADR; sinkronisasi wajib lintas AGENTS/single-path/playbook/index. |
| `C-PDF-AUDIT` | Audit ketersediaan format PDF | `docs/process/archive/2026_02/TODO_PDF26A1_AUDIT_KETERSEDIAAN_FORMAT_PDF_2026_02_28.md` | `done` (`state:baseline-locked`, `state:operational-follow-up-via-child`) | `docs/process/archive/2026_03/TODO_PDF26A2_FOLLOW_UP_AUDIT_BERKALA_PDF_2026_03_02.md` (`child-spec:done`) | `PDF26A1` dikunci sebagai baseline audit `done`; eksekusi audit berkala tetap berjalan di `PDF26A2` agar follow-up operasional tidak mengubah status concern parent. |
| `C-STRUCTURE-HARDENING` | Hardening struktur folder maintainable | `docs/process/archive/2026_03/TODO_SFC26A1_HARDENING_STRUKTUR_FOLDER_MAINTAINABLE_2026_03_07.md` | `done` (`state:structure-hardened`) | `docs/process/CODE_PLACEMENT_POLICY.md`, `docs/process/PROCESS_TODO_ARCHIVE_STRATEGY.md` | Concern ini mengunci policy placement concern baru + cleanup artefak lokal/generated + strategi arsip TODO done agar review manual tetap jelas. |

## Daftar Dokumen Ambigu yang Diturunkan Statusnya

Dokumen di bawah ini tidak boleh lagi dipakai sebagai acuan final bila concern sudah punya SOT pada tabel di atas:

- `docs/process/archive/2026_02/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`
- `docs/process/archive/2026_02/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
- `docs/process/archive/2026_02/TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md`
- `docs/process/archive/2026_02/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`
- `docs/process/archive/undated/TODO_UI_VISIBILITY_BY_PENANGGUNGJAWAB.md`
- `docs/process/archive/2026_02/TODO_IMPLEMENTASI_AUTENTIK_BUKU_BANTUAN_2026_02_24.md`

## Aturan Operasional

- Saat memulai concern baru:
  - cek registry ini terlebih dahulu,
  - jika concern sudah ada: update SOT concern tersebut, bukan membuat TODO paralel baru,
  - jika concern belum ada: tambahkan baris concern baru + tetapkan satu SOT.
- Jika butuh TODO turunan:
  - tandai sebagai `child-spec` pada kolom non-SOT,
  - parent concern tetap satu dan eksplisit.

## Validasi Audit Sesi Ini

- [x] Seluruh file `docs/process/TODO*.md` dipindai untuk judul + status + marker.
- [x] Concern overlap diidentifikasi dan dipetakan ke satu SOT per concern.
- [x] Konflik concern dashboard/sidebar/role ownership ditutup lewat registry ini.

## Sinkronisasi Sesi 2026-03-02

- [x] Concern `in-progress` yang belum terpetakan di registry ditambahkan sebagai SOT concern baru (`C-BUKU-ADMIN`, `C-MODULE-VISIBILITY`, `C-SEKCAM-ROADMAP`).
- [x] Relasi parent-child concern availability/autentik buku dan monitoring visibility dikunci untuk mencegah ambiguity update status.
- [x] Sinkronisasi hasil audit sesi dicatat pada `docs/process/OPERATIONAL_VALIDATION_LOG.md`.
- [x] Concern `C-MODULE-VISIBILITY` disinkronkan ke status `done` (`rolling-monitoring`) setelah mitigasi ringan output wajib + validasi targeted 2026-03-02.
- [x] Child concern `PDF26A2` disinkronkan ke `done` untuk siklus audit PDF 2026-03-02; registry insiden diperbarui pada `PDF26A1`.
- [x] Concern `C-ACCESS-CONTROL` ditutup ke `done` setelah closure `ACL26E2` (`go`, rollout batch 2 `agenda-surat`, regression gate + full suite hijau) pada 2026-03-02.
- [x] Concern `C-SEKCAM-ROADMAP` ditutup ke `done` setelah keputusan closure wave-2 (`no-op terkontrol`) dan revalidasi targeted sekretaris kecamatan `PASS` pada 2026-03-02.
- [x] Drift status concern `C-PDF-AUDIT` ditutup: status registry diselaraskan ke `done` mengikuti SOT `PDF26A1`; follow-up operasional tetap di `PDF26A2`.
- [x] Concern `C-SIDEBAR-UI` disinkronkan dengan hardening mitigasi batch PDF sidebar (`UVM25R1`): guard coverage menu PDF wajib + anti-duplikasi + guard `uiVisibility` ditambahkan pada unit test frontend (sinkronisasi 2026-03-02).
- [x] Concern `C-SIDEBAR-UI` disinkronkan dengan guard active-state + persistensi collapse sidebar (`UVM25R1`): kontrak `isItemActive` dan `localStorage` collapse key dikunci pada test frontend (sinkronisasi 2026-03-02).
- [x] Concern `C-UI-RESPONSIVE` disinkronkan dengan mitigasi navigasi semantik (`UXR26A1`): trigger dropdown navbar/aside dipaksa elemen semantik dan dikunci lewat test kontrak frontend (sinkronisasi 2026-03-02).
- [x] Concern `C-UI-RESPONSIVE` disinkronkan dengan batch aksesibilitas modal + standardisasi state list (`UXR26A1`): guard fokus/escape modal dan state `loading|error|disabled` pada `ResponsiveDataTable` ditutup lewat test kontrak frontend (sinkronisasi 2026-03-02).
- [x] Concern `C-UI-RESPONSIVE` ditutup ke `done` setelah revalidasi full suite pasca hardening (`PASS`, `1047` tests, `7033` assertions) pada 2026-03-02.

## Kriteria Exit Rolling

- Ubah status concern ini ke `done` jika:
  - selama 2 siklus review berurutan tidak ada concern baru yang memicu konflik SOT,
  - seluruh concern `in-progress` pada registry sudah punya owner, target validasi, dan marker SOT aktif yang konsisten.
- Pertahankan status `in-progress` hanya jika:
  - ada concern baru lintas dokumen yang belum punya satu SOT tegas, atau
  - ditemukan drift antara status concern di registry dengan status dokumen SOT aktual.

## Cadence Review (Mulai 2026-03-02)

- Frekuensi: mingguan, setiap Senin.
- Scope review minimal:
  - cek perubahan status concern pada seluruh `docs/process/TODO_*.md`,
  - cek referensi `Source of Truth` masih menunjuk dokumen concern aktif,
  - catat hasil sinkronisasi di sesi concern terkait.
- Milestone review aktif:
  - [x] Review R1: 2026-03-09 (ditutup lebih awal via persetujuan owner 2026-03-02).
  - [x] Review R2: 2026-03-16 (ditutup lebih awal via persetujuan owner 2026-03-02).

## Mitigasi 5 (Cadence Readiness 2026-03-02)

- [x] Pra-review snapshot concern `in-progress` dikunci untuk baseline sebelum R1.
  - hasil baseline 2026-03-02: `SKC0201`, `UVM25R1`, `UXR26A1`, `TTM25R1`.
- [x] Paket command review mingguan dikunci agar R1/R2 berjalan deterministik:
  - `rg -n '^Status:\\s*`in-progress`' docs/process -g 'TODO_*.md'`
  - `rg -n '^\\s*- \\[ \\]' docs/process/TODO_*.md`
  - `rg -n 'Source of Truth|child-spec|Status Concern' docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
- [x] Kriteria close review dipertegas:
  - R1/R2 boleh ditandai selesai meski tanpa perubahan status concern jika tidak ditemukan drift SOT/status/checklist lintas dokumen.

## Mitigasi Final (Snapshot Lock 2026-03-02)

- [x] Snapshot final concern `in-progress` dikunci ulang:
  - `UVM25R1` (`rolling-ui-exp`),
  - `TTM25R1` (`truth-registry`).
- [x] Drift SOT/status/checklist lintas concern aktif diverifikasi tidak bertambah pada sesi ini.
- [x] Paket validasi gabungan concern aktif dijalankan untuk memastikan stabilitas baseline sebelum review R1:
  - `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Feature/KecamatanDesaActivityTest.php tests/Feature/KecamatanDesaArsipTest.php tests/Unit/Frontend/DashboardLayoutMenuContractTest.php tests/Unit/Frontend/ResponsiveTableRolloutContractTest.php tests/Unit/Frontend/DashboardResponsiveInteractionContractTest.php tests/Unit/Frontend/NavigationSemanticContractTest.php`
  - hasil: `PASS` (`49` tests, `425` assertions).
- [x] Status concern `TTM25R1` pada snapshot final saat itu tetap `in-progress` sampai milestone cadence:
  - `R1`: 2026-03-09,
  - `R2`: 2026-03-16.

## Closure Update 2026-03-02 (Owner Final Approval)

- Owner menyetujui penutupan final tanpa menunggu cadence review mingguan (`R1/R2`) karena seluruh concern aktif sudah `done` dan tidak ada drift SOT/checklist tersisa.
- Dampak closure:
  - concern `C-SIDEBAR-UI` disinkronkan ke `done`,
  - registry `TTM25R1` ditutup ke `done`,
  - milestone review `R1/R2` ditandai selesai sebagai administrasi closure berdasarkan approval owner.

