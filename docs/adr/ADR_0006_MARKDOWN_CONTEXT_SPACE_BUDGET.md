# ADR 0006 Markdown Context Space Budget

Tanggal: 2026-03-09  
Status: `accepted`  
Owner: AI execution flow  
Related TODO: `docs/process/archive/2026_03/TODO_MKB26A1_AUDIT_OPTIMASI_MARKDOWN_CONTEXT_BUDGET_2026_03_09.md`  
Supersedes: `-`  
Superseded by: `-`

## Konteks

- Repository ini sudah mengadopsi thin registry, archive TODO, dan snapshot validation log agar konteks aktif AI tidak dipenuhi histori panjang.
- Namun pola tersebut masih bersifat kualitatif. Belum ada kontrak numerik untuk menghitung berapa besar markdown aktif yang aman, kapan file sudah terlalu besar, dan bagaimana space boleh diekspansi saat model AI memiliki context window yang lebih longgar.
- Tanpa budget numerik, pertumbuhan dokumen governance berisiko kembali menekan efektivitas routing AI walau struktur arsip sudah benar.

## Opsi yang Dipertimbangkan
### Opsi A - Pertahankan aturan thinning kualitatif saja

- Ringkasan pendek: tetap mengandalkan intuisi "ringkas" tanpa angka budget eksplisit.
- Kelebihan: minim overhead perawatan.
- Konsekuensi: sulit mengaudit kapan dokumen sudah terlalu padat dan sulit menentukan kapan ekspansi space masih aman.

### Opsi B - Hard cap tetap per file tanpa budget pack

- Ringkasan pendek: tetapkan batas karakter/tokens per file, tetapi tanpa menghitung total pack yang biasa dibaca bersama.
- Kelebihan: mudah diterapkan.
- Konsekuensi: tidak cukup menjawab kebutuhan routing AI karena bottleneck sering muncul dari kombinasi beberapa file aktif, bukan dari satu file saja.

### Opsi C - Budget context space berbasis pack + reserve

- Ringkasan pendek: hitung estimasi token per file dengan heuristic murah, bentuk pack baca aktif, lalu sisakan reserve eksplisit untuk prompt user, kode, diff, dan reasoning.
- Kelebihan: memberi angka operasional yang bisa diaudit, tetap murah dihitung, dan bisa diskalakan saat context window model meningkat.
- Konsekuensi: butuh satu dokumen process tambahan dan sinkronisasi periodik saat baseline berubah.

## Keputusan

- Opsi terpilih: Opsi C (`budget context space berbasis pack + reserve`).
- Alasan utama: bottleneck markdown pada repo ini terjadi pada level pack baca aktif, sehingga kontrak harus berbentuk budget total yang bisa memicu thinning, archive, atau ekspansi secara deterministik.
- Kontrak yang dikunci:
  - estimator canonical: `estimated_tokens = ceil(chars / 4)`,
  - markdown governance aktif maksimal memakai `65%` dari ideal context window,
  - reserve minimum `35%` wajib dipertahankan untuk prompt user, kode, diff, dan reasoning,
  - band kerja harian repo saat ini dikunci pada `12k-18k` estimated markdown tokens,
  - ekspansi space saat context window naik mengikuti urutan `validation log -> thin registry -> playbook summary -> concern pack tambahan/ADR`, bukan langsung memperbesar `AGENTS.md`.

## Dampak

- Dampak positif:
  - audit konteks markdown menjadi repeatable,
  - keputusan thinning vs ekspansi menjadi terukur,
  - jalur hardening dokumen saat context window model berubah menjadi eksplisit.
- Trade-off:
  - ada satu artefak process baru yang harus dijaga sinkron dengan baseline aktif.
- Area terdampak (route/request/use case/repository/test/docs):
  - `docs`: `AGENTS.md`, `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`, `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`, `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md`, `docs/process/PLANNING_ARTIFACT_INDEX.md`, `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`, `docs/process/OPERATIONAL_VALIDATION_LOG.md`, `docs/process/MARKDOWN_CONTEXT_SPACE_BUDGET.md`, TODO concern ini.

## Validasi

- [x] Targeted test concern: audit scoped ukuran file markdown aktif + referensi dokumen terkait.
- [x] Regression test concern terkait: sinkronisasi TODO + ADR + AGENTS + process docs + registry + validation log.
- [x] `php artisan test` tidak dijalankan karena perubahan hanya `docs/**` dan mengikuti jalur `doc-only`.

## Rollback/Fallback Plan

- Langkah rollback minimum: hapus referensi budget baru dari dokumen governance dan kembali ke thinning kualitatif yang lama.
- Kondisi kapan fallback dijalankan: jika budgeting numerik terbukti menambah overhead tetapi tidak membantu routing AI pada beberapa sesi berturut-turut.

## Referensi

- `AGENTS.md`
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
- `docs/process/MARKDOWN_CONTEXT_SPACE_BUDGET.md`
- `docs/process/archive/2026_03/TODO_MKB26A1_AUDIT_OPTIMASI_MARKDOWN_CONTEXT_BUDGET_2026_03_09.md`

## Status Log

- 2026-03-09: `proposed` -> `accepted` | Budget context space markdown dikunci dengan estimator `chars/4`, reserve `35%`, dan ladder ekspansi terkontrol.
