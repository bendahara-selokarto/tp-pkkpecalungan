# TODO GCP26A3 Audit Automation Markdown Governance

Tanggal: 2026-03-09  
Status: `done` (`state:audit-script-generator-hook-and-ci-gate-locked`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Optimasi governance markdown sudah memiliki kontrak dan compaction manual, tetapi belum ada enforcement otomatis yang memastikan drift baru tertangkap sebelum merge.
- Agar generate TODO berikutnya ikut patuh pada kepentingan context budget, perlu tiga guard sekaligus: audit script lokal, hook pada generator TODO, dan CI gate yang memeriksa markdown governance lintas perubahan docs/process.

## Kontrak Concern (Lock)

- Domain: automation governance markdown.
- Role/scope target: semua role AI (`khotib`, `iwan`, `santoso`, `manto`).
- Boundary data:
  - `scripts/audit_markdown_governance.ps1`,
  - `scripts/generate_todo.ps1`,
  - `.github/workflows/markdown-governance-gate.yml`,
  - `AGENTS.md`,
  - `docs/process/MARKDOWN_CONTEXT_SPACE_BUDGET.md`,
  - `docs/process/PLANNING_ARTIFACT_INDEX.md`,
  - `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`,
  - `docs/process/OPERATIONAL_VALIDATION_LOG.md`,
  - `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.
- Acceptance criteria:
  - ada audit script yang memeriksa soft cap, thin registry, index aktif, dan annex on-demand,
  - generator TODO default menjalankan audit tersebut setelah generate,
  - CI punya gate khusus untuk markdown governance,
  - dokumen governance terkait tersinkron dengan automation baru.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] H1. Audit script governance markdown tersedia dan bisa dijalankan lokal/CI.
- [x] H2. Generator TODO default menjalankan audit governance setelah generate.
- [x] H3. Workflow CI markdown governance gate aktif dan dokumen concern tersinkron.

## Langkah Eksekusi

- [x] L1. Audit jalur workflow dan script lokal yang sudah ada.
- [x] L2. Tambah `scripts/audit_markdown_governance.ps1`.
- [x] L3. Kaitkan generator TODO ke audit script sebagai default post-generate check.
- [x] L4. Tambah workflow CI untuk markdown governance gate.
- [x] L5. Sinkronkan budget/process/index/log concern terkait.

## Validasi

- [x] V1. L1 `doc+script-only`: jalankan `scripts/audit_markdown_governance.ps1`.
- [x] V2. L1 `doc+script-only`: generate TODO uji di folder sementara untuk memastikan hook generator memanggil audit.
- [x] V3. L2: review diff lintas script + workflow + docs governance.
- [x] V4. L3 tidak dijalankan karena tidak ada perubahan runtime aplikasi.

## Risiko

- Risiko 1: audit script bisa drift jika struktur heading/tabel markdown berubah drastis.
- Risiko 2: hook generator dapat terasa ketat saat user sengaja membuat TODO besar sebelum sesi compaction.

## Keputusan

- [x] K1: enforcement governance markdown dikunci pada tiga level: lokal (`audit script`), generator (`post-generate hook`), dan CI (`markdown-governance-gate`).
- [x] K2: audit script bersifat deterministic dan membaca soft cap dari dokumen budget aktif, bukan dari angka hardcoded terpisah.

## Keputusan Arsitektur (Jika Ada)

- [x] Tidak ada ADR baru; concern ini mengeksekusi guard automation dalam boundary governance yang sudah ada.
- [x] Status concern disinkronkan ke pointer closure registry dan log periodik.

## Fallback Plan

- Jika audit script gagal karena drift format markdown:
  - perbaiki parser script mengikuti struktur canonical terbaru,
  - sementara itu jalankan audit manual scoped dengan command budget doc.
- Jika hook generator terlalu mengganggu pada edge case:
  - pakai `-SkipGovernanceAudit` dengan penjelasan blocker yang eksplisit,
  - tetap jalankan audit script manual sebelum closure concern.

## Output Final

- [x] Ringkasan automation governance markdown tersedia.
- [x] Daftar file terdampak tersinkron.
- [x] Hasil validasi doc+script-only dan residual risk tercatat.
