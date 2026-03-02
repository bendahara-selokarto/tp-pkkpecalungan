# TODO TTM25R1 Registry Source of Truth TODO 2026-02-25

Tanggal: 2026-02-25  
Status: `active` (`truth-registry`, deterministic-routing)

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
| `C-DASH-CHART` | Koherensi chart/filter lintas role pasca Apex | `docs/process/TODO_DCF25R1_KOHERENSI_CHART_FILTER_LINTAS_ROLE_2026_02_25.md` | `done` | `TODO_DKB25R1`, `TODO_DUC23R1`, `TODO_DRA23R1`, `TODO_DRM24R1`, `TODO_DRL24R1`, `TODO_SCENARIO_KECAMATAN_SECTION4_POKJA_I_2026_02_23.md` | Untuk `pie/bar`, `section1_month`, dan query chart: hanya ikuti `DCF25R1` (status registry disinkronkan 2026-03-01). |
| `C-DASH-BASELINE` | Koherensi baseline dashboard sekretaris kecamatan | `docs/process/TODO_KOHERENSI_KRITIS_DASHBOARD_SEKRETARIS_KECAMATAN_BASELINE_2026_02_25.md` | `done` | `TODO_REFACTOR_DASHBOARD_*`, `TODO_UI_DASHBOARD_CHART_*` | Baseline visual lintas role tetap refer ke `DKB25R1`; concern chart aktif dipindah ke `DCF25R1`. |
| `C-SIDEBAR-UI` | Penataan menu/sidebar UI eksperimen | `docs/process/TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md` | `active` (`rolling-ui-exp`) | `TODO_UI_VISIBILITY_BY_PENANGGUNGJAWAB.md`, `TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md` | Untuk urutan/group/label sidebar: hanya ikuti `UVM25R1` marker `VIS-UI-EXP-2026-02-25-R2` (UI-only, non-E2E). |
| `C-ARSIP-MGMT` | Management arsip oleh super-admin | `docs/process/TODO_ASM26B1_MANAGEMENT_ARSIP_SUPER_ADMIN_2026_02_27.md` | `in-progress` | `TODO_ARS26A1_MENU_ARSIP_DOKUMEN_STATIS_2026_02_27.md` (`historical-baseline`), `TODO_ARS26B2_HARDENING_AKSES_ARSIP_GLOBAL_PRIBADI_2026_02_28.md` (`child-spec`) | Untuk akses/management arsip gunakan `ASM26B1`; `ARS26A1` baseline historis, `ARS26B2` mengunci kontrak akses global/pribadi/monitoring. |
| `C-ROLE-OWNERSHIP` | Ownership modul role runtime | `docs/process/TODO_AUDIT_MODUL_ROLE_OWNERSHIP_2026_02_25.md` | `done` | `TODO_IMPLEMENTASI_ROLE_OWNERSHIP_POKJA_DESA_ONLY_2026_02_25.md`, `TODO_IMPLEMENTASI_ROLE_OWNERSHIP_NON_RW_RO_2026_02_25.md`, `TODO_IMPLEMENTASI_ROLE_OWNERSHIP_DEPRECATE_DATA_PELATIHAN_KADER_2026_02_25.md` | Audit modul adalah parent concern; tiga TODO implementasi diperlakukan child concern eksekusi. |
| `C-AUTENTIK-49A-4144B` | Sinkron autentik E2E lampiran 4.9a-4.14.4b | `docs/process/TODO_AUTENTIK_LAMPIRAN_4_9A_4_14_4B_E2E.md` | `done` | `TODO_AUTENTIK_DATA_UMUM_PKK_4_20A.md`, `TODO_AUTENTIK_DATA_UMUM_PKK_4_20B.md`, `TODO_AUTENTIK_DATA_KEGIATAN_PKK_4_23_4_24.md`, dll terkait subset lampiran | Jika terjadi konflik mapping/header subset, acuan final concern ini mengikuti dokumen E2E payung. |
| `C-AUTENTIK-4144C-4144F` | Sinkron autentik E2E lampiran 4.14.4c-4.14.4f | `docs/process/TODO_AUTENTIK_LAMPIRAN_4_14_4C_4F_E2E.md` | `done` | `TODO_IMPLEMENTASI_AUTENTIK_BUKU_KEGIATAN_2026_02_24.md`, `TODO_IMPLEMENTASI_AUTENTIK_BUKU_PROGRAM_KERJA_2026_02_24.md`, dll yang menyentuh subset sama | Untuk contract header/payload/report 4.14.4c-4.14.4f, payung E2E jadi acuan final. |
| `C-AUTENTIK-BKL-BKR-PAAR` | Kontrak autentik BKL/BKR/PAAR | `docs/process/TODO_AUTENTIK_BKL_BKR_PAAR_2026_02_24.md` | `done` | `TODO_AUTENTIK_BUKU_WAJIB_POKJA_I_DAN_BUKU_BANTU_2026_02_24.md` | Untuk header/mapping BKL-BKR-PAAR gunakan dokumen ini sebagai acuan tunggal. |
| `C-AUTENTIK-BUKU-BANTU` | Implementasi autentik buku bantu lanjutan | `docs/process/TODO_IMPLEMENTASI_AUTENTIK_BUKU_BANTU_LANJUTAN_2026_02_24.md` | `done` | `TODO_IMPLEMENTASI_AUTENTIK_BUKU_BANTUAN_2026_02_24.md` | Untuk `kader khusus/prestasi/inventaris/anggota pokja/simulasi`, acuan final ada di lanjutan. |
| `C-UI-RUNTIME` | Guardrail runtime UI | `docs/process/TODO_UI_RUNTIME_GUARDRAIL_2026_02_24.md` | `done` | - | Concern tunggal, tidak overlap aktif. |
| `C-UI-PAGINATION` | Pagination UI E2E | `docs/process/TODO_UI_PAGINATION_E2E_2026_02_24.md` | `done` | - | Concern tunggal, tidak overlap aktif. |
| `C-UI-RESPONSIVE` | Refactor responsive UX layout lintas halaman | `docs/process/TODO_UXR26A1_REFACTOR_RESPONSIVE_UX_LAYOUT_2026_03_01.md` | `active` (`responsive-ux-refactor`) | `TODO_REFACTOR_DASHBOARD_*`, `TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md` (`related-concern`) | Untuk struktur responsif tabel, target sentuh mobile, dan aksesibilitas interaksi: ikuti `UXR26A1` sebagai jalur utama. |
| `C-USER-GUIDE` | User guide natural humanis | `docs/process/TODO_USER_GUIDE_NATURAL_HUMANIS_2026_02_24.md` | `done` | - | Concern tunggal, tidak overlap aktif. |
| `C-DATABASE-NORMALISASI` | Normalisasi database & legacy reduction | `docs/process/TODO_NORMALISASI_DATABASE_2026_02_24.md` | `done` | `TODO_CLEANUP_HIERARKI_FOLDER_2026_02_23.md`, `TODO_COVERAGE_UNIT_TEST_DAN_SEEDER_2026_02_22.md` | Untuk keputusan struktur data legacy gunakan TODO normalisasi sebagai acuan utama. |
| `C-ACCESS-CONTROL` | Management ijin akses modul-group role oleh super-admin | `docs/process/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md` | `active` (`phased-rollout`) | `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md` (`decision-record`), `TODO_ACL26S1_SUPER_ADMIN_MATRIX_READ_ONLY_2026_02_28.md` (`child-spec:done`), `TODO_ACL26C1_PILOT_OVERRIDE_CATATAN_KELUARGA_2026_02_28.md` (`child-spec:done`), `TODO_ACL26A2_ROLLOUT_OVERRIDE_MODUL_ACTIVITIES_2026_03_02.md` (`child-spec:done`) | Implementasi akses modular wajib bertahap: read-only matrix -> pilot write per modul -> rollout batch, dengan fallback hardcoded tetap aktif; batch rollout pertama (`activities`) disinkronkan 2026-03-02. |
| `C-PROCESS-EXECUTION` | Jalur eksekusi AI zero ambiguity + self-reflective routing | `docs/process/TODO_SRR26A1_SELF_REFLECTIVE_ROUTING_2026_03_01.md` | `done` | `TODO_ZERO_AMBIGUITY_AI_SINGLE_PATH_2026_02_23.md` (`historical-baseline`), `TODO_FLOW_BACA_LAPOR_SINKRON_HEADER_TABEL.md` (`child-spec`), `TODO_BTLK26A1_OPTIMASI_BOTTLENECK_PROCESS_EXECUTION_2026_03_01.md` (`child-spec:done`), `docs/adr/ADR_0003_SELF_REFLECTIVE_ROUTING.md` (`decision-record`) | Eksekusi AI concern process wajib mengikuti single-path aktif dengan checkpoint refleksi terkontrol sebelum patch besar. |
| `C-FIXTURE-TEMPLATE` | Konsistensi fixture dan template report print | `docs/process/TODO_FTC26A1_FIXTURE_TEMPLATE_CONSISTENCY_2026_03_01.md` | `done` | `docs/process/OPERATIONAL_VALIDATION_LOG.md` (`blocker-trace`) | Kegagalan residual fixture/template ditangani di concern ini (terpisah dari process execution); status disinkronkan 2026-03-02 setelah targeted tests hijau. |
| `C-DOC-ARCH-V2` | Refactor arsitektur markdown TODO + ADR | `docs/process/TODO_MDA26R1_REFACTOR_MARKDOWN_ARSITEKTUR_BARU_2026_02_28.md` | `done` | `docs/adr/ADR_0001_DOCUMENTATION_GOVERNANCE_TODO_ADR.md` (`decision-record`) | Concern dokumentasi arsitektural mengikuti pasangan TODO + ADR; sinkronisasi wajib lintas AGENTS/single-path/playbook/index. |

## Daftar Dokumen Ambigu yang Diturunkan Statusnya

Dokumen di bawah ini tidak boleh lagi dipakai sebagai acuan final bila concern sudah punya SOT pada tabel di atas:

- `docs/process/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`
- `docs/process/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
- `docs/process/TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md`
- `docs/process/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`
- `docs/process/TODO_UI_VISIBILITY_BY_PENANGGUNGJAWAB.md`
- `docs/process/TODO_IMPLEMENTASI_AUTENTIK_BUKU_BANTUAN_2026_02_24.md`

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
