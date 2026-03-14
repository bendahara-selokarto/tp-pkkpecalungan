# TODO TTM25R1 Registry Source of Truth TODO 2026-02-25

Tanggal: 2026-02-25  
Status: `done` (`state:thin-registry-active-index`)

## Force Latest Marker

- Todo Code: `TTM25R1`
- Marker: `TODO-TRUTH-REGISTRY-2026-02-25-R3`
- Dokumen ini adalah registry ringkas untuk routing concern aktif.
- Detail historis concern dipindahkan ke arsip snapshot agar konteks aktif tetap ringan.

## Tujuan

- Menjaga satu jalur lookup SOT concern aktif yang deterministik.
- Menurunkan beban baca harian AI dengan memisahkan histori panjang ke arsip.
- Tetap mempertahankan jejak audit lengkap tanpa kehilangan konteks keputusan lama.

## Registry Concern Aktif

| Concern ID | Concern | Source of Truth (SOT) | Status Concern | Keputusan Routing |
| --- | --- | --- | --- | --- |
| `C-MODULE-GROUPING-E2E` | Refactor grouping modul domain E2E | `docs/process/TODO_IWN26B1_REFACTOR_GROUPING_MODUL_DOMAIN_E2E_2026_03_04.md` | `planned` | Grouping menu/modul lintas domain mengikuti concern ini sebagai parent concern. |
| `C-ROLE-BASED-GROUPING` | Penataan ulang grouping modul berdasarkan role user | `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md` | `planned` | Concern ini dipakai untuk validasi rule grouping berbasis role sebelum merge ke concern parent E2E. |
| `C-QUALITY-GATE-90PLUS` | Roadmap sprint naik skor project 90+ | `docs/process/TODO_QG90A1_ROADMAP_SPRINT_NAIK_SKOR_PROJECT_90_PLUS_2026_03_07.md` | `in-progress` | Concern ini menjadi jalur eksekusi hardening quality gate (style + e2e dependency) untuk mendorong skor proyek ke 90+. |
| `C-LEGACY-RUNTIME-DEPRECATION` | Deprecate legacy runtime compatibility | `docs/process/TODO_LGC26A1_DEPRECATE_LEGACY_RUNTIME_COMPATIBILITY_2026_03_15.md` | `in-progress` | Semua jalur kompatibilitas legacy dipindahkan dari runtime ke dokumentasi saja. |
| `C-NON-TEKNIS-CORRECTION-PLAN` | Rencana perbaikan koreksi non teknis | `docs/process/TODO_RPB26A1_RENCANA_PERBAIKAN_KOREKSI_NON_TEKNIS_2026_03_10.md` | `planned` | Concern ini menjadi jalur perencanaan koreksi non-teknis yang perlu sinkron sebelum eksekusi teknis terkait. |
| `C-SIDEBAR-PDF-FLOW` | Penataan menu sidebar flow PDF turunan tanpa form input | `docs/process/TODO_SPT26A1_PENATAAN_MENU_SIDEBAR_FLOW_PDF_TURUNAN_TANPA_FORM_INPUT_2026_03_09.md` | `in-progress` | Concern ini menjadi jalur sinkron flow PDF turunan tanpa form input agar navigasi tetap konsisten. |
| `C-SINGLE-PATH-AUTH-EXCEPTION` | Exception single path flow auth framework | `docs/process/TODO_SPA26B1_EXCEPTION_SINGLE_PATH_FLOW_AUTH_FRAMEWORK_2026_03_15.md` | `planned` | Concern ini mengunci pengecualian auth framework agar tidak melebar ke flow non-auth. |

## Pointer Closure Terbaru

- Concern `done` terbaru dipindahkan ke log periodik `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.
- Pointer closure penting:
  - `C-DATABASE-NORMALIZATION-FORMAL` -> `docs/process/archive/2026_03/TODO_NFM26A1_AUDIT_NORMALISASI_DATABASE_FORMAL_1NF_3NF_BERTAHAP_2026_03_10.md`
  - `C-UI-UX-RUNTIME-EVIDENCE` -> `docs/process/archive/2026_03/TODO_IWN26A1_ROADMAP_EKSPANSI_AUDIT_UI_UX_RUNTIME_EVIDENCE_2026_03_03.md`
  - `C-REFERENCE-DOC-STANDARDIZATION` -> `docs/process/archive/2026_03/TODO_RFD26A1_STANDARISASI_STRUKTUR_DOKUMEN_REFERENSI_DOMAIN_2026_03_10.md`
  - `C-GOVERNANCE-AUDIT-WAVE` -> `docs/process/archive/2026_03/TODO_GCP26A4_GOVERNANCE_AUDIT_WAVE_FOLLOWUP_2026_03_09.md`
  - `C-POKJA-II-MODULE` -> `docs/process/archive/2026_03/TODO_PKJ2A1_MODUL_DATA_KEGIATAN_PKK_POKJA_II_2026_03_11.md`
  - `C-MARKDOWN-CONTEXT-BUDGET` -> `docs/process/archive/2026_03/TODO_MKB26A1_AUDIT_OPTIMASI_MARKDOWN_CONTEXT_BUDGET_2026_03_09.md`
  - `C-GOVERNANCE-CONTEXT-PACK` -> `docs/process/archive/2026_03/TODO_GCP26A1_DEDUP_DAN_COMPACTION_GOVERNANCE_CONTEXT_PACK_2026_03_09.md`
  - `C-GOVERNANCE-THIN-REGISTRY` -> `docs/process/archive/2026_03/TODO_GCP26A2_THIN_REGISTRY_DAN_ANNEX_RETRIEVAL_GUARDRAIL_2026_03_09.md`
  - `C-GOVERNANCE-AUDIT-AUTOMATION` -> `docs/process/archive/2026_03/TODO_GCP26A3_AUDIT_AUTOMATION_MARKDOWN_GOVERNANCE_2026_03_09.md`
  - `C-INERTIA-INCREMENTAL-OPTIMIZATION` -> `docs/process/archive/2026_03/TODO_SPA26A1_ROADMAP_OPTIMASI_BERTAHAP_INERTIA_TANPA_MIGRASI_SPA_MURNI_2026_03_08.md`
  - wave closure dashboard/user/kecamatan partial reload tetap hidup di log periodik Q1, bukan di tabel aktif ini.

## Registry Historis (Full Context)

Gunakan arsip berikut jika user meminta audit concern lama, jejak keputusan detail, atau mapping parent-child historis:

| Snapshot | File | Keterangan |
| --- | --- | --- |
| `2026-03-02` | `docs/process/archive/registry/TTM25R1_REGISTRY_FULL_2026_03_02.md` | Snapshot penuh registry sebelum thinning (berisi seluruh concern done + catatan closure). |
| `2026-03-08` | `docs/process/archive/2026_03/TODO_TAG26A1_REFACTOR_ISOLASI_TAHUN_ANGGARAN_LINTAS_MODUL_2026_03_07.md` | Concern `C-BUDGET-YEAR-CONTEXT` diarsipkan setelah closure `done (state:wave4-hardening-complete)` agar tidak membebani context aktif AI. |

## Aturan Operasional

- Untuk kerja concern aktif, baca dokumen ini terlebih dahulu.
- Tabel aktif di atas hanya untuk concern `planned/in-progress`.
- Jika concern tidak ada pada tabel aktif, cek snapshot historis terbaru.
- Jika concern `done` terbaru diperlukan, cek pointer closure di dokumen ini lalu buka `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.
- Jika concern lama diaktifkan kembali:
  1. tambahkan baris concern ke tabel aktif,
  2. pastikan `Source of Truth` menunjuk satu TODO aktif,
  3. catat sinkronisasi di `docs/process/OPERATIONAL_VALIDATION_LOG.md`.

## Validasi Sesi Ini (2026-03-09)

- [x] Snapshot penuh registry disalin ke arsip (`2026-03-02`) tanpa mengubah isi historis.
- [x] Registry aktif dipangkas agar fokus concern `planned/in-progress`.
- [x] Jalur referensi concern aktif tetap deterministik (1 concern -> 1 SOT).
- [x] Concern `done` terbaru dipindah menjadi pointer closure ke log periodik.
- [x] Concern `GCP26A2` menutup hardening thin registry dan retrieval annex on-demand.
- [x] Concern `GCP26A3` menutup automation audit governance markdown.
- [x] Concern `GCP26A4` diregistrasikan sebagai follow-up audit wave aktif untuk batch 1-5.
- [x] Concern `RFD26A1` diregistrasikan sebagai planning concern untuk standardisasi struktur `docs/referensi`.
