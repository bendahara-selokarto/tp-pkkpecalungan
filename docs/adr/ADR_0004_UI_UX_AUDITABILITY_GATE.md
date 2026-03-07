# ADR 0004 UI UX Auditability Gate

Tanggal: 2026-03-03  
Status: `accepted`  
Owner: AI process governance  
Related TODO: `docs/process/archive/2026_03/TODO_AUI26A1_AUDITABILITY_GATE_UI_UX_BERBASIS_KODE_2026_03_03.md`  
Supersedes: `-`  
Superseded by: `-`

## Konteks

- Proyek sudah memiliki banyak test backend dan kontrak frontend berbasis source assertion, tetapi belum ada kontrak arsitektur tunggal yang mengunci jalur audit UI/UX berbasis kode secara auditable lintas sesi.
- Tanpa gate arsitektur ini, status "smoke done" berisiko hanya terdokumentasi manual tanpa bukti test otomatis yang mudah direplay.

## Opsi yang Dipertimbangkan
### Opsi A - Pertahankan smoke manual sebagai jalur utama

- Ringkasan pendek: audit UI/UX mengandalkan checklist manual.
- Kelebihan: cepat dijalankan tanpa setup tooling tambahan.
- Konsekuensi: jejak audit runtime sulit direproduksi dan lebih rentan drift.

### Opsi B - Tetapkan UI/UX auditability gate berbasis kode pada single-path

- Ringkasan pendek: setiap concern UI harus melewati lane validasi kode + evidence yang terdokumentasi.
- Kelebihan: status auditable dapat diverifikasi ulang dengan artefak yang konsisten.
- Konsekuensi: ada overhead dokumentasi dan disiplin eksekusi validation ladder.

## Keputusan

- Opsi terpilih: Opsi B.
- Alasan utama: memastikan audit UI/UX dapat direplay dan ditelusuri melalui jejak TODO + ADR + validasi concern.
- Kontrak yang dikunci:
  - Task router memuat lane khusus `UI/UX auditability via code`.
  - Validation ladder concern UI wajib menghasilkan evidence terstruktur.
  - Evidence minimum: hasil test kontrak frontend/feature concern + catatan validasi pada dokumen proses concern aktif.
  - Jika tooling runtime browser belum tersedia, status harus eksplisit `partial` dengan gap evidence.

```dsl
ADR_ID: 0004
DECISION: OPTION_B
CONTRACT: UI_UX_AUDITABILITY_GATE
EVIDENCE_REQUIRED: frontend_contract_test,feature_regression,validation_log
FALLBACK_MODE: partial_with_explicit_gap
STATUS: accepted
```

## Dampak

- Dampak positif:
  - Audit UI/UX lebih mudah diaudit ulang lintas sesi.
  - Kualitas dokumentasi keputusan dan status implementasi meningkat.
- Trade-off:
  - Tambahan langkah sinkronisasi dokumen process/ADR saat concern UI berubah.
- Area terdampak (route/request/use case/repository/test/docs):
  - `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
  - `docs/process/archive/2026_03/TODO_AUI26A1_AUDITABILITY_GATE_UI_UX_BERBASIS_KODE_2026_03_03.md`

## Validasi

- [x] Targeted test concern.
- [x] Regression test concern terkait.
- [x] `php artisan test` (jika perubahan signifikan).

Catatan validasi:

- Concern ini `doc-only`; validasi dijalankan melalui scoped audit dokumen + konsistensi referensi TODO/ADR/playbook.

## Rollback/Fallback Plan

- Rollback: hapus lane `UI/UX auditability via code` dari single-path dan ubah status pattern playbook ke `deprecated`.
- Kondisi fallback dijalankan:
  - tooling runtime browser belum siap,
  - concern tetap harus jalan cepat.
- Mode fallback: jalankan kontrak frontend + regression feature existing, tandai status `partial`, lalu buka TODO follow-up untuk menutup gap runtime evidence.

## Referensi

- `AGENTS.md`
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
- `docs/process/archive/2026_03/TODO_AUI26A1_AUDITABILITY_GATE_UI_UX_BERBASIS_KODE_2026_03_03.md`

## Status Log

- 2026-03-03: `proposed` -> `accepted` | gate auditability UI/UX berbasis kode dikunci sebagai kontrak arsitektur proses.

