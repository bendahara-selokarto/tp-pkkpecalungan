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
| `C-INERTIA-INCREMENTAL-OPTIMIZATION` | Roadmap optimasi bertahap Inertia tanpa migrasi SPA murni | `docs/process/TODO_SPA26A1_ROADMAP_OPTIMASI_BERTAHAP_INERTIA_TANPA_MIGRASI_SPA_MURNI_2026_03_08.md` | `in-progress` | Concern ini mengunci jalur optimasi UX/runtime bertahap berbasis Inertia dengan fokus partial reload, lazy fetch, komponen stateful, dan endpoint JSON kecil yang terkontrol. |
| `C-DASHBOARD-WAVE1-PARTIAL-RELOAD` | Pilot dashboard wave 1 partial reload dan payload slimming | `docs/process/TODO_DWI26A1_PILOT_DASHBOARD_WAVE_1_PARTIAL_RELOAD_DAN_PAYLOAD_SLIMMING_2026_03_08.md` | `done` | Concern ini adalah pilot pertama implementasi optimasi Inertia pada dashboard dengan fokus helper visit terpusat, partial reload, dan guard query contract. |
| `C-USER-MANAGEMENT-WAVE1-PARTIAL-RELOAD` | Pilot user management index partial reload dan payload slimming | `docs/process/TODO_USR26A1_PILOT_USER_MANAGEMENT_INDEX_PARTIAL_RELOAD_DAN_PAYLOAD_SLIMMING_2026_03_08.md` | `done` | Concern ini menutup pilot kedua wave 1 pada halaman index user management super-admin dengan partial reload paginasi/per-page yang sudah tervalidasi. |

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
