# TODO TTM25R1 Registry Source of Truth TODO 2026-02-25

Tanggal: 2026-02-25  
Status: `done` (`state:thin-registry-active-index`)

## Force Latest Marker

- Todo Code: `TTM25R1`
- Marker: `TODO-TRUTH-REGISTRY-2026-02-25-R2`
- Dokumen ini adalah registry ringkas untuk routing concern aktif.
- Detail historis concern dipindahkan ke arsip snapshot agar konteks aktif tetap ringan.

## Tujuan

- Menjaga satu jalur lookup SOT concern aktif yang deterministik.
- Menurunkan beban baca harian AI dengan memisahkan histori panjang ke arsip.
- Tetap mempertahankan jejak audit lengkap tanpa kehilangan konteks keputusan lama.

## Registry Concern Aktif

| Concern ID | Concern | Source of Truth (SOT) | Status Concern | Keputusan Routing |
| --- | --- | --- | --- | --- |
| `C-UI-UX-RUNTIME-EVIDENCE` | Ekspansi audit UI/UX runtime evidence | `docs/process/TODO_IWN26A1_ROADMAP_EKSPANSI_AUDIT_UI_UX_RUNTIME_EVIDENCE_2026_03_03.md` | `in-progress` | Concern ini menjadi jalur utama evidence runtime lintas concern UI/UX. |
| `C-MODULE-GROUPING-E2E` | Refactor grouping modul domain E2E | `docs/process/TODO_IWN26B1_REFACTOR_GROUPING_MODUL_DOMAIN_E2E_2026_03_04.md` | `planned` | Grouping menu/modul lintas domain mengikuti concern ini sebagai parent concern. |
| `C-ROLE-BASED-GROUPING` | Penataan ulang grouping modul berdasarkan role user | `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md` | `planned` | Concern ini dipakai untuk validasi rule grouping berbasis role sebelum merge ke concern parent E2E. |
| `C-QUALITY-GATE-90PLUS` | Roadmap sprint naik skor project 90+ | `docs/process/TODO_QG90A1_ROADMAP_SPRINT_NAIK_SKOR_PROJECT_90_PLUS_2026_03_07.md` | `planned` | Concern ini menjadi jalur eksekusi hardening quality gate (style + e2e dependency) untuk mendorong skor proyek ke 90+. |
| `C-MARKDOWN-CONTEXT-BUDGET` | Audit optimasi markdown context budget | `docs/process/TODO_MKB26A1_AUDIT_OPTIMASI_MARKDOWN_CONTEXT_BUDGET_2026_03_09.md` | `done` | Concern ini mengunci baseline estimasi token markdown aktif, budget context space, dan ladder ekspansi saat context window AI meningkat. |
| `C-INERTIA-INCREMENTAL-OPTIMIZATION` | Roadmap optimasi bertahap Inertia tanpa migrasi SPA murni | `docs/process/TODO_SPA26A1_ROADMAP_OPTIMASI_BERTAHAP_INERTIA_TANPA_MIGRASI_SPA_MURNI_2026_03_08.md` | `done` | Concern ini menutup roadmap optimasi UX/runtime bertahap berbasis Inertia dari wave 1 sampai wave 5 tanpa migrasi ke SPA murni. |
| `C-DASHBOARD-WAVE1-PARTIAL-RELOAD` | Pilot dashboard wave 1 partial reload dan payload slimming | `docs/process/TODO_DWI26A1_PILOT_DASHBOARD_WAVE_1_PARTIAL_RELOAD_DAN_PAYLOAD_SLIMMING_2026_03_08.md` | `done` | Concern ini adalah pilot pertama implementasi optimasi Inertia pada dashboard dengan fokus helper visit terpusat, partial reload, dan guard query contract. |
| `C-USER-MANAGEMENT-WAVE1-PARTIAL-RELOAD` | Pilot user management index partial reload dan payload slimming | `docs/process/TODO_USR26A1_PILOT_USER_MANAGEMENT_INDEX_PARTIAL_RELOAD_DAN_PAYLOAD_SLIMMING_2026_03_08.md` | `done` | Concern ini menutup pilot kedua wave 1 pada halaman index user management super-admin dengan partial reload paginasi/per-page yang sudah tervalidasi. |
| `C-DASHBOARD-WAVE2-DEFERRED-BLOCKS` | Pilot dashboard wave 2 deferred blocks dan lazy fetch | `docs/process/TODO_DBL26A1_PILOT_DASHBOARD_WAVE_2_DEFERRED_BLOCKS_DAN_LAZY_FETCH_2026_03_08.md` | `done` | Concern ini menutup lazy fetch tahap kedua pada dashboard dengan deferred `dashboardBlocks` yang sudah tervalidasi tanpa menambah API route baru. |
| `C-DASHBOARD-WAVE3-STATEFUL-UI` | Pilot dashboard wave 3 stateful presentational UI | `docs/process/TODO_DBS26A1_PILOT_DASHBOARD_WAVE_3_STATEFUL_PRESENTATIONAL_UI_2026_03_08.md` | `done` | Concern ini menutup persistensi state presentasional dashboard di client tanpa mengubah query atau boundary backend. |
| `C-DASHBOARD-WAVE4-JSON-DETAIL-WIDGET` | Pilot dashboard wave 4 JSON detail widget per desa | `docs/process/TODO_DBJ26A1_PILOT_DASHBOARD_WAVE_4_JSON_DETAIL_WIDGET_PER_DESA_2026_03_08.md` | `done` | Concern ini menutup endpoint JSON kecil on-expand untuk rincian per-desa/per-modul yang sudah tervalidasi penuh tanpa membuka API dashboard generik. |
| `C-DASHBOARD-WAVE5-FETCH-TELEMETRY` | Pilot dashboard wave 5 fetch failure telemetry | `docs/process/TODO_DBT26A1_PILOT_DASHBOARD_WAVE_5_FETCH_FAILURE_TELEMETRY_2026_03_09.md` | `done` | Concern ini menutup telemetry runtime untuk fetch failure widget dashboard tanpa menambah endpoint observability baru dan sudah tervalidasi penuh. |
| `C-KECAMATAN-DESA-ACTIVITIES-PARTIAL-RELOAD` | Pilot kecamatan desa activities partial reload | `docs/process/TODO_KDA26A1_PILOT_KECAMATAN_DESA_ACTIVITIES_PARTIAL_RELOAD_2026_03_09.md` | `done` | Concern ini menutup rollout partial reload ke halaman monitoring kegiatan desa kecamatan tanpa menambah route baru. |
| `C-KECAMATAN-DESA-ARSIP-PARTIAL-RELOAD` | Pilot kecamatan desa arsip partial reload | `docs/process/TODO_KAR26A1_PILOT_KECAMATAN_DESA_ARSIP_PARTIAL_RELOAD_2026_03_09.md` | `done` | Concern ini menutup rollout partial reload ke halaman monitoring arsip desa kecamatan tanpa menambah route baru. |

## Registry Historis (Full Context)

Gunakan arsip berikut jika user meminta audit concern lama, jejak keputusan detail, atau mapping parent-child historis:

| Snapshot | File | Keterangan |
| --- | --- | --- |
| `2026-03-02` | `docs/process/archive/registry/TTM25R1_REGISTRY_FULL_2026_03_02.md` | Snapshot penuh registry sebelum thinning (berisi seluruh concern done + catatan closure). |
| `2026-03-08` | `docs/process/archive/2026_03/TODO_TAG26A1_REFACTOR_ISOLASI_TAHUN_ANGGARAN_LINTAS_MODUL_2026_03_07.md` | Concern `C-BUDGET-YEAR-CONTEXT` diarsipkan setelah closure `done (state:wave4-hardening-complete)` agar tidak membebani context aktif AI. |

## Aturan Operasional

- Untuk kerja concern aktif, baca dokumen ini terlebih dahulu.
- Jika concern tidak ada pada tabel aktif, cek snapshot historis terbaru.
- Jika concern lama diaktifkan kembali:
  1. tambahkan baris concern ke tabel aktif,
  2. pastikan `Source of Truth` menunjuk satu TODO aktif,
  3. catat sinkronisasi di `docs/process/OPERATIONAL_VALIDATION_LOG.md`.

## Validasi Sesi Ini (2026-03-07)

- [x] Snapshot penuh registry disalin ke arsip (`2026-03-02`) tanpa mengubah isi historis.
- [x] Registry aktif dipangkas agar fokus concern berjalan.
- [x] Jalur referensi concern aktif tetap deterministik (1 concern -> 1 SOT).
- [x] Concern baru `C-BUDGET-YEAR-CONTEXT` ditambahkan untuk mengunci refactor canonical `tahun_anggaran`.
- [x] Concern `C-BUDGET-YEAR-CONTEXT` dipindahkan ke arsip setelah closure agar registry aktif tetap ringan.
