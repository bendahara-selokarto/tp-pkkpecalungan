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
- File ini hanya menyimpan snapshot concern `planned/in-progress` dan pointer closure terbaru.

## Snapshot Aktif (Concern Berjalan)

### Registry SOT (`TTM25R1`)

- Source of truth concern aktif: `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`.
- Concern berjalan yang tetap berada di index aktif:
  - `docs/process/TODO_GCP26A4_GOVERNANCE_AUDIT_WAVE_FOLLOWUP_2026_03_09.md` (`in-progress`)
  - `docs/process/TODO_RFD26A1_STANDARISASI_STRUKTUR_DOKUMEN_REFERENSI_DOMAIN_2026_03_10.md` (`planned`)
  - `docs/process/TODO_PKJ2A1_MODUL_DATA_KEGIATAN_PKK_POKJA_II_2026_03_11.md` (`planned`)
  - `docs/process/TODO_IWN26B1_REFACTOR_GROUPING_MODUL_DOMAIN_E2E_2026_03_04.md` (`planned`)
  - `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md` (`planned`)
  - `docs/process/TODO_QG90A1_ROADMAP_SPRINT_NAIK_SKOR_PROJECT_90_PLUS_2026_03_07.md` (`planned`)
- Catatan sinkronisasi `GCP26A4`:
  - gelombang audit lanjutan governance markdown dipisah ke concern parent tersendiri agar commit/push per batch tetap traceable,
  - batch 1 dimulai dengan archiving concern `done` dari root `docs/process/` ke `docs/process/archive/2026_03/` dan sinkronisasi pointer closure terkait.
- Catatan sinkronisasi `RGM26A1`:
  - histori no-op tervalidasi pada 2026-03-07 tetap dipertahankan di TODO concern sebagai audit trail,
  - status aktif terbaru tetap `planned` (`state:awaiting-owner-group-target`) sampai ada input owner baru.
- Catatan sinkronisasi `RFD26A1`:
  - audit baseline `docs/referensi` menemukan folder root masih mencampur PDF numerik, workbook, screenshot, dan nama file dengan pola berbeda,
  - concern dikunci sebagai planning-only lebih dulu agar taxonomy target dan migration map path selesai sebelum rename fisik dijalankan.

### Pointer Closure Terbaru

- Detail closure concern `SPA26A1`, `DWI26A1`, `USR26A1`, `DBL26A1`, `DBS26A1`, `DBJ26A1`, `DBT26A1`, `KDA26A1`, `KAR26A1`, `KAC26A1`, `MKB26A1`, `SFC26A1`, `MFC26A1`, dan `TAG26A1` telah dipindahkan ke `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.
- Concern `NFM26A1` (Audit Normalisasi Database Formal) ditutup dan dicatat di `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.
- Concern `IWN26A1` (Roadmap Ekspansi Audit UI/UX Runtime Evidence) ditutup dan dicatat di `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.
- Concern `GCP26A1` menutup hardening sesi ini dengan keputusan: ownership governance markdown dikunci dan validation log aktif kembali menjadi index ringkas.
- Concern `GCP26A2` menutup thinning `TTM25R1`, memperbaiki drift `P-014`, dan mengunci guard annex on-demand.
- Concern `GCP26A3` menutup automation audit script + generator hook + CI gate untuk governance markdown.

### Concern Archived

- `TAG26A1` (`Refactor Tahun Anggaran`) tetap diarsipkan di `docs/process/archive/2026_03/TODO_TAG26A1_REFACTOR_ISOLASI_TAHUN_ANGGARAN_LINTAS_MODUL_2026_03_07.md`.
- ADR terkait tetap `docs/adr/ADR_0005_TAHUN_ANGGARAN_CONTEXT_ISOLATION.md`.

### Sprint Quality Gate 90+ (`QG90A1`) - 2026-03-07

- Status concern: `planned`.
- Fokus concern:
  - menurunkan style debt secara bertahap pada scope prioritas sprint,
  - memastikan jalur E2E smoke tidak gagal karena dependency OS browser.
- Baseline evidence concern:
  - `php artisan test --compact`: `1057 passed`,
  - `./vendor/bin/pint --test`: `907 files`, `633 style issues`,
  - `npm run build`: `PASS`,
  - `npm run test:e2e:smoke`: `FAIL` (missing `libnspr4.so`).

## Protokol Update

1. File index ini hanya menyimpan concern `planned/in-progress`, pointer closure terbaru, dan baseline concern yang masih berjalan.
2. Detail command output panjang dan closure concern `done` diappend ke file arsip periodik aktif.
3. Saat pergantian periode, buat file arsip baru di `docs/process/logs/` dan update tabel arsip.
