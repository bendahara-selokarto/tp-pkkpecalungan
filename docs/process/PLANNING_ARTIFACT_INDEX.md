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
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md`
  - lampiran detail pattern; bersifat on-demand dan tidak masuk default pack baca harian.
- `docs/process/MARKDOWN_CONTEXT_SPACE_BUDGET.md`
  - budget numerik markdown aktif + policy ekspansi saat context window AI berubah.
- `docs/process/CODE_PLACEMENT_POLICY.md`
  - policy penempatan kode concern baru agar struktur repository konsisten.
- `docs/process/PROCESS_TODO_ARCHIVE_STRATEGY.md`
  - strategi arsip TODO `done` agar `docs/process` tetap navigable.

### Layer 1 - Registry dan Routing Concern

- `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
  - thin registry concern aktif (`Concern ID -> SOT TODO`).
- `docs/process/archive/registry/TTM25R1_REGISTRY_FULL_2026_03_02.md`
  - snapshot historis penuh registry concern untuk audit lintas sesi.
- `docs/process/COMMAND_NUMBER_SHORTCUTS.md`
  - perintah ringkas untuk routing instruksi user.

### Layer 2 - Perencanaan Eksekusi Concern

- `docs/process/TODO_*.md`
  - rencana eksekusi per concern.
- `docs/process/TEMPLATE_TODO_CONCERN.md`
  - template canonical TODO concern baru.
- `scripts/generate_todo.ps1`
  - generator TODO canonical; default menjalankan audit governance markdown setelah generate.

### Layer 3 - Keputusan dan Bukti Eksekusi

- `docs/adr/ADR_*.md`
  - keputusan arsitektur lintas concern.
- `docs/process/OPERATIONAL_VALIDATION_LOG.md`
  - bukti validasi operasional per siklus.
- `scripts/audit_markdown_governance.ps1`
  - audit otomatis soft cap, thin registry, index aktif, dan annex retrieval governance markdown.

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
2. Jika perubahan menyentuh lampiran detail pattern, pertahankan sifat `on-demand` dan sinkronkan file utama playbook.
3. Jika perubahan mempengaruhi budget markdown aktif, sinkronkan `MARKDOWN_CONTEXT_SPACE_BUDGET.md`.
4. Sinkronkan referensi di dokumen index ini bila jalur berubah.

## Snapshot Concern Aktif (Baseline 2026-03-02)

- `ACL26M1` -> `docs/process/archive/2026_02/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`
- `SKC0201` -> `docs/process/archive/2026_02/TODO_SKC0201_ROADMAP_SEKRETARIS_KECAMATAN_2026_02_28.md`
- `UVM25R1` -> `docs/process/archive/2026_02/TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md`
- `UXR26A1` -> `docs/process/archive/2026_03/TODO_UXR26A1_REFACTOR_RESPONSIVE_UX_LAYOUT_2026_03_01.md`
- `TTM25R1` -> `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`

## Guard Anti-Drift

- Setiap concern `in-progress` wajib punya owner TODO SOT di `TTM25R1`.
- Status concern di registry harus sama dengan status pada dokumen TODO SOT.
- Evidence command penting (test/build/route audit) wajib dicatat di `OPERATIONAL_VALIDATION_LOG.md`.
- TODO concern baru wajib dibuat dari template dan patuh format kode unik.
