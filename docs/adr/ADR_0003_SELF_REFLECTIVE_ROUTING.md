# ADR 0003 Self Reflective Routing

Tanggal: 2026-03-01  
Status: `accepted`  
Owner: AI process governance  
Related TODO: `docs/process/TODO_SRR26A1_SELF_REFLECTIVE_ROUTING_2026_03_01.md`  
Supersedes: `-`  
Superseded by: `-`

## Konteks
- Single-path sudah deterministik, tetapi belum punya checkpoint refleksi eksplisit.
- Task ambigu berisiko salah route concern dan memicu rework.

## Opsi yang Dipertimbangkan
### Opsi A - Tetap Single Path Tanpa Refleksi Eksplisit
- Pro: paling sederhana.
- Kontra: koreksi salah route kurang auditable.

### Opsi B - Tambah Self-Reflective Checkpoint Terbatas
- Pro: rework turun, determinisme terjaga.
- Kontra: ada overhead evaluasi awal.

### Opsi C - Routing Adaptif Multi-Loop Bebas
- Pro: fleksibel.
- Kontra: risiko loop dan latency tinggi.

## Keputusan
- Opsi terpilih: Opsi B.
- Alasan utama: koreksi rute terukur tanpa melepas kontrak single-path.
- Kontrak yang dikunci:
  - checkpoint refleksi wajib sebelum patch besar,
  - tier model: `low->small`, `medium->mid`, `high->large`,
  - koreksi rute utama maksimal satu kali per concern,
  - sinkronisasi `playbook + single-path + TODO + ADR` di sesi yang sama.

```dsl
ADR_ID: 0003
DECISION: OPTION_B
CHECKPOINT: reflective_required_before_major_patch
MODEL_TIER_MAP: low=small, medium=mid, high=large
ROUTE_CORRECTION_LIMIT: 1
SYNC_REQUIRED: playbook,single-path,todo,adr
STATUS: accepted
```

## Dampak
- Dampak positif: akurasi routing naik, rework turun, audit trail jelas.
- Trade-off: ada tambahan langkah dokumentasi/evaluasi awal.
- Area terdampak (route/request/use case/repository/test/docs):
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
  - `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
  - `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
  - `docs/process/TODO_SRR26A1_SELF_REFLECTIVE_ROUTING_2026_03_01.md`
  - `docs/process/OPERATIONAL_VALIDATION_LOG.md`

## Validasi
- [x] Targeted test concern.
- [x] Regression test concern terkait.
- [ ] `php artisan test` (jika perubahan signifikan).

## Rollback/Fallback Plan
- Rollback: hapus checkpoint refleksi dari single-path dan tandai `P-022` sebagai `deprecated`.
- Fallback dipakai jika latency naik tanpa peningkatan akurasi routing concern.

## Referensi
- `AGENTS.md`
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
- `docs/process/TODO_SRR26A1_SELF_REFLECTIVE_ROUTING_2026_03_01.md`

## Status Log
- 2026-03-01: `proposed` -> `accepted` | checkpoint refleksi terbatas disepakati sebagai ekstensi resmi single-path routing.
