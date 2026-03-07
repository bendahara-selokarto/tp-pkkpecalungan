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

## Protokol Update

1. Untuk validasi concern aktif, tambahkan ringkasan singkat di file index ini.
2. Untuk detail command output panjang, append ke file arsip periodik aktif.
3. Saat pergantian periode, buat file arsip baru di `docs/process/logs/` dan update tabel arsip.
