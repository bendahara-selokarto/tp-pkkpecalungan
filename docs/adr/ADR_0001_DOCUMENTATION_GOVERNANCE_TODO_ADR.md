# ADR 0001 Documentation Governance TODO ADR

Tanggal: 2026-02-28  
Status: `accepted`  
Owner: AI execution flow  
Related TODO: `docs/process/TODO_MDA26R1_REFACTOR_MARKDOWN_ARSITEKTUR_BARU_2026_02_28.md`  
Supersedes: `-`  
Superseded by: `-`

## Konteks
- Dokumentasi eksekusi sebelumnya mengandalkan TODO/process tanpa kontrak keputusan arsitektur formal.
- Concern lintas sesi berisiko drift karena keputusan trade-off tidak selalu tercatat sebagai artefak terpisah.
- Dibutuhkan arsitektur dokumentasi yang memisahkan jalur eksekusi dan jalur keputusan.

## Opsi yang Dipertimbangkan
### Opsi A - Tetap TODO-only
- Ringkasan pendek: seluruh rencana dan keputusan tetap ditulis di TODO/process.
- Kelebihan: sederhana, minim file tambahan.
- Konsekuensi: jejak keputusan strategis kurang eksplisit dan sulit diaudit lintas sesi.

### Opsi B - Gunakan TODO + ADR terhubung
- Ringkasan pendek: TODO untuk eksekusi, ADR untuk keputusan arsitektur + trade-off.
- Kelebihan: keputusan jangka panjang lebih stabil, mudah ditelusuri, dan sinkron lintas concern.
- Konsekuensi: overhead dokumentasi bertambah untuk concern strategis.

## Keputusan
- Opsi terpilih: Opsi B (`TODO + ADR`).
- Alasan utama: memisahkan rencana eksekusi dan keputusan arsitektur menurunkan ambiguity lintas sesi dan memudahkan audit.
- Kontrak yang dikunci:
  - TODO concern wajib dipakai untuk rencana lintas-file.
  - ADR wajib dipakai untuk keputusan arsitektur lintas concern.
  - Jika concern menyentuh arsitektur, TODO dan ADR harus saling merujuk.

## Dampak
- Dampak positif:
  - Jalur eksekusi dan jejak keputusan menjadi deterministik.
  - Drift dokumentasi berkurang pada perubahan strategis.
- Trade-off:
  - Tambahan langkah sinkronisasi dokumen pada concern arsitektural.
- Area terdampak (route/request/use case/repository/test/docs):
  - `docs`: `AGENTS.md`, `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`, `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`, `docs/README.md`, `README.md`, template TODO/ADR.

## Validasi
- [x] Targeted test concern: audit scoped diff + referensi silang TODO/ADR.
- [x] Regression test concern terkait: konsistensi aturan pada AGENTS + single-path + playbook.
- [ ] `php artisan test` (jika perubahan signifikan).

## Rollback/Fallback Plan
- Langkah rollback minimum: hapus referensi ADR pada dokumen yang diubah dan kembali ke TODO-only governance.
- Kondisi kapan fallback dijalankan: jika overhead governance terbukti menghambat concern kecil tanpa manfaat audit.

## Referensi
- `AGENTS.md`
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
- `docs/adr/README.md`
- `docs/process/TODO_MDA26R1_REFACTOR_MARKDOWN_ARSITEKTUR_BARU_2026_02_28.md`

## Status Log
- 2026-02-28: `proposed` -> `accepted` | Dipakai sebagai baseline arsitektur dokumentasi baru TODO+ADR.
