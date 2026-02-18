# AGENTS.md

## AI Context Loading Order
Untuk memahami proyek ini secara konsisten, baca dokumen dalam urutan berikut:
1. `CONTEXT_INDEX.md`
2. `ARCHITECTURE.md`
3. `DATABASE_STANDARDS.md`
4. `UI_ARCHITECTURE.md`
5. `README.md`

## Project Intent
- Proyek Laravel untuk manajemen data PKK dengan domain wilayah.
- Arsitektur backend mengikuti pola: Controller -> UseCase/Action -> Repository -> Model.
- Otorisasi berbasis Role/Permission + policy + scope wilayah.
- Pada modul domain aktif, authorization source of truth adalah `Policy -> Scope Service`.

## Domain Canonical
- Sumber data wilayah canonical adalah tabel `areas`.
- Tabel `kecamatans`, `desas`, dan `user_assignments` dianggap legacy.
- Fitur baru tidak boleh menambah dependency ke tabel legacy.

## Database Guardrails
- Untuk data domain berbasis wilayah, wajib ada `level`, `area_id`, `created_by`.
- Naming kolom harus konsisten antara migration, model, DTO, request.
- Ikuti checklist pada `DATABASE_STANDARDS.md` untuk perubahan schema.
- Untuk manajemen user, pastikan `role`, `scope`, dan `area_id` tetap konsisten (`area_id` harus match `areas.level`).

## Documentation Policy
- Jika ada perubahan arsitektur, update dokumen terkait di commit yang sama.
- Jika ada konflik antar dokumen, prioritas kebenaran:
  1. `DATABASE_STANDARDS.md` untuk aturan database
  2. `ARCHITECTURE.md` untuk aturan backend layer
  3. `UI_ARCHITECTURE.md` untuk aturan frontend/layout
  4. `CONTEXT_INDEX.md` untuk ringkasan lintas dokumen

## Scope
Instruksi ini berlaku untuk agent AI yang bekerja di repository ini.
