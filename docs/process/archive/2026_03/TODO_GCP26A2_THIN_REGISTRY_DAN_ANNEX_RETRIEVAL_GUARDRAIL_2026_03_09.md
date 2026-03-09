# TODO GCP26A2 Thin Registry Dan Annex Retrieval Guardrail

Tanggal: 2026-03-09  
Status: `done` (`state:ttm-thinned-and-annex-guardrail-locked`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Audit sesi lanjutan menunjukkan `TTM25R1` berada di `7,343` chars, melewati soft cap `6,500` chars dan trigger compaction aktif.
- Thin registry masih memuat banyak concern `done`, padahal tujuan file ini adalah routing concern aktif, bukan daftar closure historis.
- `AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md` memang bukan bagian default pack, tetapi annex ini perlu guard retrieval yang lebih eksplisit agar tidak ikut termuat penuh secara keliru; selain itu ada drift struktur pada detail `P-014`.

## Kontrak Concern (Lock)

- Domain: governance markdown untuk registry concern aktif dan annex pattern detail.
- Role/scope target: semua role AI (`khotib`, `iwan`, `santoso`, `manto`).
- Boundary data:
  - `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`,
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`,
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md`,
  - `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`,
  - `docs/process/MARKDOWN_CONTEXT_SPACE_BUDGET.md`,
  - `docs/process/PLANNING_ARTIFACT_INDEX.md`,
  - `docs/process/OPERATIONAL_VALIDATION_LOG.md`,
  - `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.
- Acceptance criteria:
  - `TTM25R1` kembali menjadi thin registry concern aktif dan berada di bawah soft cap,
  - concern `done` dipindahkan menjadi pointer closure/arsip, bukan row aktif,
  - annex pattern details memiliki guard retrieval on-demand yang eksplisit,
  - drift struktur `P-014` diperbaiki.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] H1. `TTM25R1` dipadatkan menjadi registry concern aktif + pointer closure.
- [x] H2. Guard annex on-demand untuk `pattern_details` dikunci lintas dokumen governance.
- [x] H3. Drift struktur pada detail `P-014` diperbaiki dan sinkronisasi concern tercatat di log/registry.

## Langkah Eksekusi

- [x] L1. Audit ukuran `TTM25R1` dan posisi annex `pattern_details` terhadap default pack.
- [x] L2. Tipiskan `TTM25R1` dengan memindahkan concern `done` menjadi pointer closure.
- [x] L3. Tambahkan guard retrieval annex on-demand pada dokumen governance terkait.
- [x] L4. Perbaiki drift struktur detail `P-014` pada lampiran pattern details.
- [x] L5. Sinkronkan validation log dan baseline budget yang terdampak.

## Validasi

- [x] V1. L1 `doc-only`: audit ukuran file pasca compaction.
- [x] V2. L1 `doc-only`: audit referensi guard annex/pointer closure dengan `rg`.
- [x] V3. L2: review diff lintas registry + budget + playbook + log.
- [x] V4. L3 tidak dijalankan karena tidak ada perubahan runtime/aplikasi.

## Risiko

- Risiko 1: jika terlalu banyak concern aktif bersamaan, `TTM25R1` bisa cepat membesar kembali.
- Risiko 2: annex pattern details tetap berisiko membengkak jika tidak di-shard saat ambang praktik terlewati.

## Keputusan

- [x] K1: `TTM25R1` hanya menyimpan row concern `planned/in-progress` dan pointer closure ringkas ke arsip/log.
- [x] K2: `AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md` tetap annex on-demand dan tidak masuk default pack; shard baru diwajibkan jika utility retrieval menurun atau ukuran lampiran melewati ambang praktik yang dikunci.

## Keputusan Arsitektur (Jika Ada)

- [x] Tidak ada ADR baru; concern ini masih berada dalam boundary governance yang sudah diterima.
- [x] Status concern disinkronkan ke log arsip dan pointer closure aktif.

## Fallback Plan

- Jika thin registry menjadi terlalu tipis untuk routing:
  - tambahkan satu blok pointer closure ringkas,
  - jangan mengembalikan daftar concern `done` penuh ke tabel aktif.
- Jika annex pattern details mulai sulit dinavigasi:
  - shard per rentang pattern atau per domain concern,
  - pertahankan playbook utama sebagai index tunggal.

## Output Final

- [x] Ringkasan thinning registry dan hardening annex retrieval tersedia.
- [x] Daftar file terdampak tersinkron.
- [x] Hasil validasi doc-only dan residual risk tercatat.
