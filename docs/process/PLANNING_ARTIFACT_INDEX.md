# Planning Artifact Index

Tanggal: 2026-03-02  
Status: `active`  
Owner: process governance

## Tujuan
- Menjadikan artefak perencanaan di `docs/process/` terstruktur dan mudah dinavigasi.
- Menetapkan jalur baca/tulis dokumen planning yang deterministik per jenis perubahan.
- Mengurangi drift antara TODO concern, registry SOT, ADR, dan log validasi operasional.

## Struktur Artefak Perencanaan

### Layer 0 - Governance
- `AGENTS.md`
  - kontrak tertinggi eksekusi AI.
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
  - jalur operasional default.
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
  - pattern reusable lintas concern.

### Layer 1 - Registry dan Routing Concern
- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
  - source of truth concern aktif (`Concern ID -> SOT TODO`).
- `docs/process/COMMAND_NUMBER_SHORTCUTS.md`
  - perintah ringkas untuk routing instruksi user.

### Layer 2 - Perencanaan Eksekusi Concern
- `docs/process/TODO_*.md`
  - rencana eksekusi per concern.
- `docs/process/TEMPLATE_TODO_CONCERN.md`
  - template canonical TODO concern baru.

### Layer 3 - Keputusan dan Bukti Eksekusi
- `docs/adr/ADR_*.md`
  - keputusan arsitektur lintas concern.
- `docs/process/OPERATIONAL_VALIDATION_LOG.md`
  - bukti validasi operasional per siklus.

## Jalur Update Deterministik

### Jika update concern teknis tunggal
1. Ubah file `TODO_<KODE>_*.md` concern terkait.
2. Jika status concern berubah, sinkronkan row concern di registry `TTM25R1`.
3. Catat evidence validasi di `OPERATIONAL_VALIDATION_LOG.md` jika ada eksekusi test/build.

### Jika update keputusan arsitektur lintas concern
1. Ubah/tambah `ADR_*.md`.
2. Tautkan ADR di TODO concern terkait.
3. Sinkronkan status concern dan catatan keputusan di `TTM25R1`.

### Jika update pola/proses eksekusi AI
1. Ubah `AI_SINGLE_PATH_ARCHITECTURE.md` dan/atau `AI_FRIENDLY_EXECUTION_PLAYBOOK.md`.
2. Sinkronkan referensi di dokumen index ini bila jalur berubah.

## Snapshot Concern Aktif (Baseline 2026-03-02)
- `ACL26M1` -> `docs/process/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`
- `SKC0201` -> `docs/process/TODO_SKC0201_ROADMAP_SEKRETARIS_KECAMATAN_2026_02_28.md`
- `UVM25R1` -> `docs/process/TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md`
- `UXR26A1` -> `docs/process/TODO_UXR26A1_REFACTOR_RESPONSIVE_UX_LAYOUT_2026_03_01.md`
- `TTM25R1` -> `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`

## Guard Anti-Drift
- Setiap concern `in-progress` wajib punya owner TODO SOT di `TTM25R1`.
- Status concern di registry harus sama dengan status pada dokumen TODO SOT.
- Evidence command penting (test/build/route audit) wajib dicatat di `OPERATIONAL_VALIDATION_LOG.md`.
- TODO concern baru wajib dibuat dari template dan patuh format kode unik.
