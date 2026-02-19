# AI EXECUTION CONTRACT (RATE-LIMIT OPTIMIZED)

Dokumen ini adalah source of truth AI untuk repository ini.
Dokumen manusia ada di `README.md`.

## 0. Priority

Jika konflik dokumen:
1. `AGENTS.md` (file ini)
2. `README.md`

## 1. Fast Context

Stack:
- Laravel 12
- Inertia + Vue 3
- Tailwind
- Vite

Architecture:
- `Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`

Authorization:
- `Policy -> Scope Service`

Domain canonical:
- `areas` adalah single source of truth wilayah.

Legacy tables (compatibility only):
- `kecamatans`
- `desas`
- `user_assignments`

## 2. Hard Invariants

- Fitur baru tidak boleh menambah dependency ke tabel legacy.
- Data domain wilayah wajib punya `level`, `area_id`, `created_by`.
- `level` data harus konsisten dengan `areas.level`.
- `role`, `scope`, `area_id` user harus konsisten.
- `area_id` user harus cocok levelnya dengan scope.

## 3. Execution Flow (Mandatory)

1. Analyze: baca file relevan, petakan dependency, identifikasi side effect.
2. Clarify: jika ambigu, tanya singkat dan spesifik.
3. Patch minimal: perubahan sekecil mungkin, hindari rewrite luas.
4. Validate: jalankan test/cek dampak, pastikan tidak ada behavior drift.

## 4. Rate-Limiter Efficiency Rules

- Scoped analysis only: baca file yang relevan, jangan scan seluruh project tanpa alasan.
- Diff-first: prioritaskan patch kecil dibanding regenerasi file panjang.
- Response compression: ringkas, padat, tanpa pengulangan konteks.
- State-aware: jangan ulang informasi yang sudah dikonfirmasi pada sesi yang sama.
- Jangan ubah file non-target.

## 5. Forbidden Patterns

- Fat controller.
- Business logic di controller/helper/view.
- Query domain baru di luar repository boundary.
- Service locator `app()` di use case/action/service.
- UI dianggap authority akses (authority tetap backend).

## 6. Quality Gate Before Finish

- Scope authorization tetap aman.
- Tidak ada bypass baru ke legacy table.
- Tidak ada coupling baru yang tidak perlu.
- Test relevan lulus (`php artisan test` untuk perubahan signifikan).
- Tidak ada perubahan perilaku yang tidak diminta.

## 7. Output Contract

- Laporan harus menyebut: apa yang diubah, kenapa, file terdampak, dan hasil validasi.
- Jika gagal, laporkan root cause + opsi solusi + dampak tiap opsi.
