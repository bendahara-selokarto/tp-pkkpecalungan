# TODO LGC26A1 Deprecate Legacy Runtime Compatibility

Tanggal: 2026-03-15  
Status: `in-progress`
Related ADR: `docs/adr/ADR_0007_DEPRECATE_LEGACY_RUNTIME_COMPAT.md`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Runtime masih memuat jalur kompatibilitas legacy (role `admin-*`, fallback payload dashboard `dashboardStats/dashboardCharts`, alias route `bantuans/keuangan`, normalisasi field legacy Program Prioritas).
- Target sesi ini: semua jalur kompatibilitas legacy dipindahkan menjadi catatan dokumentasi saja (tidak aktif di runtime).

## Kontrak Concern (Lock)

- Domain: authorization & visibility, dashboard representation, legacy compatibility removal.
- Role/scope target: hanya role canonical (`desa-*`, `kecamatan-*`, `super-admin`); hapus akses runtime untuk `admin-*`.
- Boundary data: routes, policy/scope service, dashboard props, request normalization, seeders, tests, docs.
- Acceptance criteria:
  - Tidak ada referensi runtime ke `admin-desa/admin-kecamatan` pada `app/`, `routes/`, `resources/`, `database/`.
  - `dashboardStats/dashboardCharts` tidak lagi dipublish ke Inertia dashboard.
  - Alias route `bantuans/keuangan` dihapus dari backend + UI.
  - Normalisasi `jadwal_i-iv` legacy di request Program Prioritas dihapus.
  - Seeder migrasi role legacy tidak dieksekusi pada `DatabaseSeeder`.
  - Dokumentasi canonical sinkron dengan status legacy non-aktif.
  - Semua test relevan lulus.
- Dampak keputusan arsitektur: `ya` (authorization + dashboard contract).

## Target Hasil

- [x] Runtime tidak lagi menerima role legacy dan payload/route legacy.
- [x] Dokumentasi menyatakan legacy hanya historis (non-runtime).

## Langkah Eksekusi

- [x] Analisis scoped dependency + side effect.
- [x] Patch minimal pada boundary arsitektur.
- [x] Sinkronisasi dokumen concern terkait (trigger hardening aktif).
- [x] ADR dicatat dan disinkronkan ke TODO.

## Validasi

- [ ] L1: syntax/lint/targeted test concern.
- [ ] L2: regression test concern terkait.
- [ ] L3: `php artisan test` jika perubahan signifikan.

## Risiko

- Risiko 1: pengguna legacy kehilangan akses jika belum migrasi ke role canonical.
- Risiko 2: bookmark/report legacy `bantuans/keuangan` jadi 404.

## Keputusan

- [x] K1: hapus jalur runtime legacy (role, route, payload, request normalization).
- [x] K2: dokumentasi diperbarui untuk menandai legacy sebagai historis.

## Keputusan Arsitektur (Jika Ada)

- [ ] Buat/tautkan ADR di `docs/adr/ADR_0007_DEPRECATE_LEGACY_RUNTIME_COMPAT.md`.
- [ ] Sinkronkan status ADR (`proposed/accepted`) dengan status concern.

## Fallback Plan

- Jika terjadi regresi akses, rollback commit concern ini atau reintroduce role legacy dengan mapping sementara.

## Output Final

- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak.
- [ ] Hasil validasi + residual risk.
