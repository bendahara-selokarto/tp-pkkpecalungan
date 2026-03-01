# TODO BTLK26A1 Optimasi Bottleneck Process Execution 2026-03-01

Tanggal: 2026-03-01  
Status: `done` (`process-execution`, `doc-hardening`)

## Konteks
- Optimasi ringan: threshold `P-021`, `doc-only fast lane`, isolasi backlog fixture/template.
- Sinkronisasi lanjutan strategi test bertahap agar konsisten lintas `AGENTS` dan dokumen process (`single-path` + playbook).

## Target Hasil
- `P-021` tidak over-trigger pada perubahan minor dokumentasi.
- Jalur validasi `doc-only` resmi tersedia di single-path.
- Backlog fixture/template terisolasi.
- Kontrak eksekusi test bertahap tetap utuh: targeted -> fail-fast saat debug -> full suite di checkpoint final/perubahan signifikan.

## Langkah Eksekusi
- [x] `B1` Hardening threshold `P-021` pada playbook.
- [x] `B2` Tambah pattern `P-023` untuk `doc-only fast lane`.
- [x] `B3` Sinkronkan validation ladder `doc-only` pada single-path architecture.
- [x] `B4` Buat TODO concern terisolasi `FTC26A1` untuk fixture/template consistency.
- [x] `B5` Sinkronkan registry SOT concern + validation log operasional.
- [x] `B6` Sinkronkan wording strategi test bertahap lintas `AGENTS.md`, `AI_SINGLE_PATH_ARCHITECTURE.md`, dan `AI_FRIENDLY_EXECUTION_PLAYBOOK.md`.

## Validasi
- [x] `rg` sinkronisasi pattern `P-021/P-022/P-023` pada playbook.
- [x] `rg` sinkronisasi aturan `doc-only` pada single-path.
- [x] `rg` sinkronisasi concern `C-FIXTURE-TEMPLATE` pada registry + TODO concern baru.
- [x] `rg` sinkronisasi token `targeted`, `--stop-on-failure`, dan `checkpoint final` pada `AGENTS.md` + dokumen process utama.

## Risiko
- [x] Salah klasifikasi `doc-only` bisa membuat validasi kurang dalam.
- [x] Guardrail: fast lane tidak dipakai jika ada perubahan runtime/backend contract.

## Keputusan Dikunci
- [x] `P-021` dipersempit untuk keputusan strategis lintas concern.
- [x] `P-023` aktif sebagai jalur validasi cepat `doc-only`.
- [x] Concern fixture/template dipisah ke `FTC26A1`.
- [x] Strategi test bertahap lintas dokumen dikunci tanpa mengubah quality gate `php artisan test` untuk perubahan signifikan.

## ADR Terkait
- Tidak ada ADR baru (bukan perubahan boundary runtime).
