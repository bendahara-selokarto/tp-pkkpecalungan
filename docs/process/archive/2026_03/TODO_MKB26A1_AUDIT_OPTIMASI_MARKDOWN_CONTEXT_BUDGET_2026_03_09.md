# TODO MKB26A1 Audit Optimasi Markdown Context Budget

Tanggal: 2026-03-09  
Status: `done` (`state:context-space-budget-locked`)
Related ADR: `docs/adr/ADR_0006_MARKDOWN_CONTEXT_SPACE_BUDGET.md`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Repository ini sudah punya pola thinning registry, archive TODO, dan snapshot validation log agar konteks AI tetap ringan.
- Namun sampai sesi 2026-03-09 belum ada kontrak numerik yang menjawab tiga hal: berapa estimasi token markdown aktif saat ini, berapa budget ideal yang aman untuk kerja harian AI, dan bagaimana space boleh diperluas saat context window model meningkat.
- Tanpa budget numerik, dokumen governance berisiko kembali membengkak walau pola archive/thinning sudah ada.

## Kontrak Concern (Lock)

- Domain: governance markdown untuk context management AI.
- Role/scope target: semua role AI (`khotib`, `iwan`, `santoso`, `manto`) karena concern ini mempengaruhi jalur baca proses lintas task.
- Boundary data:
  - `AGENTS.md`,
  - `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`,
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`,
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md`,
  - `docs/process/PLANNING_ARTIFACT_INDEX.md`,
  - `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`,
  - `docs/process/OPERATIONAL_VALIDATION_LOG.md`,
  - dokumen baru budget konteks markdown,
  - ADR concern ini.
- Acceptance criteria:
  - ada formula estimasi token markdown yang sederhana dan repeatable,
  - ada baseline numerik konteks aktif repo per 2026-03-09,
  - ada soft cap/space policy dan ladder ekspansi saat context window AI meningkat,
  - referensi governance utama sinkron ke dokumen baru.
- Dampak keputusan arsitektur: `ya`

## Target Hasil

- [x] T1. Audit baseline markdown aktif dan estimasi token saat ini terdokumentasi.
- [x] T2. Ada dokumen canonical untuk budget konteks markdown + expansion policy.
- [x] T3. TODO + ADR + dokumen process + registry + log tervalidasi sinkron.

## Langkah Eksekusi

- [x] L1. Audit scoped dependency dan ukur ukuran artefak markdown aktif dengan `wc`/`chars`.
- [x] L2. Buat TODO concern via generator canonical.
- [x] L3. Tambah ADR untuk keputusan budget context space markdown.
- [x] L4. Tambah dokumen process baru yang memuat formula, baseline, soft cap, dan ladder ekspansi.
- [x] L5. Sinkronkan `AGENTS.md`, single-path, playbook, planning index, registry, dan validation log.

## Validasi

- [x] V1. L1 `doc-only`: audit scoped ukuran file (`wc -lcw` + hitung `chars/4`) untuk artefak aktif.
- [x] V2. L1 `doc-only`: audit scoped referensi concern via `rg`.
- [x] V3. L2: review diff lintas `AGENTS + process + ADR + registry + validation log`.
- [x] V4. L3 tidak diperlukan karena tidak ada perubahan runtime/aplikasi.

## Risiko

- Risiko 1: angka estimasi token berbasis `chars/4` adalah heuristic, bukan hitungan tokenizer model tertentu.
- Risiko 2: jika file index aktif terus bertambah tetapi tidak mengikuti soft cap, budget ini akan cepat usang.

## Keputusan

- [x] K1: estimator canonical memakai `estimated_tokens = ceil(chars / 4)` agar murah dihitung dan konsisten lintas sesi.
- [x] K2: markdown aktif maksimal memakai `65%` dari ideal context window; minimal `35%` disisakan untuk prompt user, kode, diff, dan reasoning.
- [x] K3: band kerja harian repo saat ini dikunci pada `12k-18k` estimated markdown tokens, dengan ideal context window repo sekitar `20k-28k` tokens tergantung depth concern.
- [x] K4: jika context window AI meningkat, perluasan space mengikuti urutan `validation log -> thin registry -> playbook summary -> concern pack tambahan/ADR`; `AGENTS.md` tidak menjadi target ekspansi pertama.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR dibuat di `docs/adr/ADR_0006_MARKDOWN_CONTEXT_SPACE_BUDGET.md`.
- [x] Status ADR dan concern disinkronkan ke `accepted` / `done`.

## Fallback Plan

- Jika budget numerik ini terbukti terlalu ketat:
  - pertahankan formula estimasi,
  - longgarkan soft cap pada dokumen budget tanpa memperlebar `AGENTS.md`,
  - simpan detail tambahan di annex/arsip, bukan di file governance utama.
- Jika heuristic `chars/4` dianggap tidak cukup:
  - tambahkan metode ukur baru yang lebih akurat,
  - dokumentasikan bukti dan alasan migrasi pada playbook + AGENTS di sesi yang sama.

## Output Final

- [x] O1. Ringkasan audit ukuran markdown aktif, budget ideal, dan policy ekspansi tersedia.
- [x] O2. Daftar file terdampak tersinkron.
- [x] O3. Validasi scoped doc-only tercatat beserta residual risk.
