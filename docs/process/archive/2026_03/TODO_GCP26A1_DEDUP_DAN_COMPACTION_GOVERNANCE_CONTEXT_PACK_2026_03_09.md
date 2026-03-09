# TODO GCP26A1 Dedup Dan Compaction Governance Context Pack

Tanggal: 2026-03-09  
Status: `done` (`state:governance-pack-dedup-and-log-compacted`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Audit sesi ini menunjukkan bottleneck utama bukan kurangnya pemecahan markdown, melainkan overlap aturan antar dokumen governance dan file index aktif yang masih menampung detail closure concern `done`.
- `docs/process/OPERATIONAL_VALIDATION_LOG.md` berada di `18,274` chars, melewati soft cap `14,000` chars lebih dari `10%`, sehingga memicu compaction wajib pada sesi yang sama sesuai kontrak budget.
- Agar kompresi konteks tetap aman, repo memerlukan pembagian ownership yang lebih tegas: dokumen mana yang memegang invariant, mana yang memegang routing, mana yang memegang angka budget, dan mana yang hanya menjadi registry/pointer.

## Kontrak Concern (Lock)

- Domain: governance markdown untuk context pack AI.
- Role/scope target: semua role AI (`khotib`, `iwan`, `santoso`, `manto`) karena concern ini mempengaruhi jalur baca harian lintas task.
- Boundary data:
  - `AGENTS.md`,
  - `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`,
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`,
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md`,
  - `docs/process/MARKDOWN_CONTEXT_SPACE_BUDGET.md`,
  - `docs/process/OPERATIONAL_VALIDATION_LOG.md`,
  - `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`,
  - `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`.
- Acceptance criteria:
  - ownership kontrak governance markdown terkunci lintas dokumen utama,
  - `OPERATIONAL_VALIDATION_LOG.md` kembali menjadi index aktif ringkas,
  - detail closure concern `done` dipindahkan ke arsip periodik tanpa memutus pointer audit,
  - registry concern dan budget rule tersinkron dengan implementasi compaction.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] H1. Ownership map governance markdown dikunci agar overlap aturan lintas dokumen berkurang.
- [x] H2. `OPERATIONAL_VALIDATION_LOG.md` dikompaksi menjadi index aktif ringkas dan detail closure dipindah ke arsip Q1.
- [x] H3. TODO + registry + process docs + validation log tersinkron untuk concern ini.

## Langkah Eksekusi

- [x] L1. Analisis scoped dependency dan identifikasi overlap aturan antar `AGENTS`, `single-path`, `budget`, `playbook`, dan validation log.
- [x] L2. Buat TODO concern baru via generator canonical.
- [x] L3. Compaction `OPERATIONAL_VALIDATION_LOG.md` dan pindahkan detail closure concern `done` ke arsip periodik Q1.
- [x] L4. Tambah ownership map/rule dedup pada dokumen governance yang relevan.
- [x] L5. Sinkronkan registry concern aktif dan validation log index.

## Validasi

- [x] V1. L1 `doc-only`: audit scoped overlap referensi dan ukuran file dengan `rg` + `Get-Item`.
- [x] V2. L1 `doc-only`: review diff pada `AGENTS + process docs + registry + validation log + archive`.
- [x] V3. L2: verifikasi ulang ukuran `OPERATIONAL_VALIDATION_LOG.md` pasca compaction berada di bawah soft cap.
- [x] V4. L3 tidak dijalankan karena tidak ada perubahan runtime/aplikasi.

## Risiko

- Risiko 1: ownership map bisa drift lagi jika dokumen baru menyalin ulang langkah operasional yang sudah canonical.
- Risiko 2: archive periodik akan tumbuh besar; jika tidak dipecah per periode dengan disiplin, audit scoped tetap bisa mahal.

## Keputusan

- [x] K1: `OPERATIONAL_VALIDATION_LOG.md` hanya menyimpan snapshot concern `planned/in-progress` dan pointer closure terbaru; detail concern `done` hidup di arsip periodik.
- [x] K2: ownership governance markdown dikunci menjadi `AGENTS = invariant/guardrail`, `single-path = routing/load order`, `budget = formula/soft cap/compaction`, `playbook = registry pattern ringkas`, `pattern details = langkah detail`.

## Keputusan Arsitektur (Jika Ada)

- [x] Tidak ada ADR baru; concern ini merupakan hardening dokumentasi dalam boundary kontrak yang sudah ada.
- [x] Status concern tetap disinkronkan ke registry dan validation log.

## Fallback Plan

- Jika compaction index aktif ternyata terlalu tipis:
  - tambah satu lapis snapshot closure ringkas di `OPERATIONAL_VALIDATION_LOG.md`,
  - jangan memulihkan detail penuh concern `done` ke file index,
  - pertahankan detail panjang di arsip periodik.

## Output Final

- [x] Ringkasan dedup governance pack dan alasan compaction tersedia.
- [x] Daftar file terdampak tersinkron.
- [x] Hasil validasi scoped doc-only dan residual risk tercatat.
