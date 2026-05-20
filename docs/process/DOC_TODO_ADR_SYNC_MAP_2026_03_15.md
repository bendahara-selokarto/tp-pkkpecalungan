# Documentation Sync Map (TODO/ADR) (2026-05-20)

Tujuan: membantu sinkronisasi status TODO/ADR dan menjaga konsistensi istilah.

- Dokumen ini dihasilkan oleh `scripts/generate_doc_sync_map.py`.

## Ringkasan

- Total TODO aktif: 12
- TODO status `done`: 3
- TODO status `in-progress`: 5
- TODO status `planned`: 4
- Total ADR aktif: 12
- ADR status `accepted`: 11
- ADR status `proposed`: 1

## TODO -> ADR

| Code | Judul | Status | Related ADR | File |
| --- | --- | --- | --- | --- |
| `BFT26A1` | Audit Format Buku Fisik Per Jabatan | `in-progress` | docs/adr/ADR_0011_COMMON_BOOK_VISIBILITY_PER_ROLE_GROUP.md | `docs/process/TODO_BFT26A1_AUDIT_FORMAT_BUKU_FISIK_PER_JABATAN_2026_05_02.md` |
| `BKADM1` | Planning Implementasi Kategori Buku Administrasi | `planned` | docs/adr/ADR_0011_COMMON_BOOK_VISIBILITY_PER_ROLE_GROUP.md | `docs/process/TODO_BKADM1_PLANNING_IMPLEMENTASI_KATEGORI_BUKU_ADMINISTRASI_2026_05_20.md` |
| `IWN26B1` | Rencana Penataan Ulang Grouping Modul Domain End To End | `planned` | - | `docs/process/TODO_IWN26B1_REFACTOR_GROUPING_MODUL_DOMAIN_E2E_2026_03_04.md` |
| `LGC26A1` | Deprecate Legacy Runtime Compatibility | `in-progress` | docs/adr/ADR_0007_DEPRECATE_LEGACY_RUNTIME_COMPAT.md | `docs/process/TODO_LGC26A1_DEPRECATE_LEGACY_RUNTIME_COMPATIBILITY_2026_03_15.md` |
| `OVR26A1` | Catatan Overkill Repo Dan Rekomendasi Refactor Tertunda | `planned` | - | `docs/process/TODO_OVR26A1_CATATAN_OVERKILL_REPO_DAN_REKOMENDASI_REFACTOR_TERTUNDA_2026_05_06.md` |
| `PDF26F1` | Footer PDF Dinamis (Database Driven) | `done` | - | `docs/process/TODO_PDF26F1_FOOTER_PDF_DINAMIS_DATABASE_DRIVEN_2026_05_18.md` |
| `PDF26H1` | Standarisasi Header PDF Otomatis | `done` | - | `docs/process/TODO_PDF26H1_STANDARISASI_HEADER_PDF_OTOMATIS_2026_05_17.md` |
| `QG90A1` | Roadmap Sprint Naik Skor Project 90 Plus | `in-progress` | - | `docs/process/TODO_QG90A1_ROADMAP_SPRINT_NAIK_SKOR_PROJECT_90_PLUS_2026_03_07.md` |
| `RGM26A1` | Penataan Ulang Grouping Modul Berdasarkan Role User | `in-progress` | docs/adr/ADR_0009_ACTIVITY_GROUP_BOOK_ISOLATION.md | `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md` |
| `SPA26B1` | Exception Single Path Flow Auth Framework | `planned` | docs/adr/ADR_0008_SINGLE_PATH_AUTH_FLOW_EXCEPTIONS.md | `docs/process/TODO_SPA26B1_EXCEPTION_SINGLE_PATH_FLOW_AUTH_FRAMEWORK_2026_03_15.md` |
| `SPT26A1` | Penataan Menu Sidebar Flow PDF Turunan Tanpa Form Input | `in-progress` | - | `docs/process/TODO_SPT26A1_PENATAAN_MENU_SIDEBAR_FLOW_PDF_TURUNAN_TANPA_FORM_INPUT_2026_03_09.md` |
| `TTM25R1` | Registry Source of Truth TODO 2026-02-25 | `done` | - | `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md` |

## ADR -> TODO

| ADR | Judul | Status | Related TODO | File |
| --- | --- | --- | --- | --- |
| `0001` | Documentation Governance TODO ADR | `accepted` | docs/process/archive/2026_02/TODO_MDA26R1_REFACTOR_MARKDOWN_ARSITEKTUR_BARU_2026_02_28.md | `docs/adr/ADR_0001_DOCUMENTATION_GOVERNANCE_TODO_ADR.md` |
| `0002` | Modular Access Management Super Admin | `accepted` | docs/process/archive/2026_02/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md | `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md` |
| `0003` | Self Reflective Routing | `accepted` | docs/process/archive/2026_03/TODO_SRR26A1_SELF_REFLECTIVE_ROUTING_2026_03_01.md | `docs/adr/ADR_0003_SELF_REFLECTIVE_ROUTING.md` |
| `0004` | UI UX Auditability Gate | `accepted` | docs/process/archive/2026_03/TODO_AUI26A1_AUDITABILITY_GATE_UI_UX_BERBASIS_KODE_2026_03_03.md | `docs/adr/ADR_0004_UI_UX_AUDITABILITY_GATE.md` |
| `0005` | Tahun Anggaran Context Isolation | `accepted` | docs/process/archive/2026_03/TODO_TAG26A1_REFACTOR_ISOLASI_TAHUN_ANGGARAN_LINTAS_MODUL_2026_03_07.md | `docs/adr/ADR_0005_TAHUN_ANGGARAN_CONTEXT_ISOLATION.md` |
| `0006` | Markdown Context Space Budget | `accepted` | docs/process/archive/2026_03/TODO_MKB26A1_AUDIT_OPTIMASI_MARKDOWN_CONTEXT_BUDGET_2026_03_09.md | `docs/adr/ADR_0006_MARKDOWN_CONTEXT_SPACE_BUDGET.md` |
| `0007` | Deprecate Legacy Runtime Compatibility | `accepted` | docs/process/TODO_LGC26A1_DEPRECATE_LEGACY_RUNTIME_COMPATIBILITY_2026_03_15.md | `docs/adr/ADR_0007_DEPRECATE_LEGACY_RUNTIME_COMPAT.md` |
| `0008` | Single Path Auth Flow Exceptions | `proposed` | docs/process/TODO_SPA26B1_EXCEPTION_SINGLE_PATH_FLOW_AUTH_FRAMEWORK_2026_03_15.md | `docs/adr/ADR_0008_SINGLE_PATH_AUTH_FLOW_EXCEPTIONS.md` |
| `0009` | Activity Group Book Isolation | `accepted` | docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md | `docs/adr/ADR_0009_ACTIVITY_GROUP_BOOK_ISOLATION.md` |
| `0010` | Prestasi Group Book Isolation | `accepted` | docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md | `docs/adr/ADR_0010_PRESTASI_GROUP_BOOK_ISOLATION.md` |
| `0011` | Common Book Visibility Per Role Group | `accepted` | - | `docs/adr/ADR_0011_COMMON_BOOK_VISIBILITY_PER_ROLE_GROUP.md` |
| `0012` | Kecamatan Unowned Module Audit Group | `accepted` | - | `docs/adr/ADR_0012_KECAMATAN_UNOWNED_MODULE_AUDIT_GROUP.md` |

## Terminologi Anchor

- `PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md`
- `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
