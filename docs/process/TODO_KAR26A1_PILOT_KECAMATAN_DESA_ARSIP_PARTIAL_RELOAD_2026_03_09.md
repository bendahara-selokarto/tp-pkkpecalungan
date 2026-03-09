# TODO KAR26A1 Pilot Kecamatan Desa Arsip Partial Reload

Tanggal: 2026-03-09  
Status: `done` (`state:full-suite-and-build-validated`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Rollout partial reload pasca-roadmap Inertia berlanjut ke halaman monitoring arsip desa kecamatan.
- `Kecamatan/DesaArsip/Index.vue` masih memakai visit penuh untuk filter, reset, per-page, dan pagination.
- Pola concern ini sangat mirip `KDA26A1`, sehingga cocok memakai helper visit dan partial prop contract yang sama sempitnya.

## Kontrak Concern (Lock)

- Domain: rollout partial reload concern untuk monitoring arsip desa kecamatan.
- Role/scope target: user `kecamatan`, terutama `kecamatan-sekretaris`.
- Boundary data:
  - `app/Domains/Wilayah/Arsip/Controllers/KecamatanDesaArsipController.php`,
  - `resources/js/Pages/Kecamatan/DesaArsip/Index.vue`,
  - `tests/Feature/KecamatanDesaArsipTest.php`.
- Acceptance criteria:
  - visit filter/per-page/pagination memakai helper partial reload terpusat,
  - prop reload dibatasi ke `documents` dan `filters`,
  - contract auth/scope/filter monitoring arsip tidak berubah.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] T1. Monitoring arsip desa kecamatan memakai partial reload untuk loop filter utama.
- [x] T2. Controller mengirim prop yang cocok untuk partial reload tanpa memecah route.
- [x] T3. Guard test partial reload concern tercatat hijau.

## Langkah Eksekusi

- [x] L1. Audit controller + page + test concern.
- [x] L2. Patch minimal controller dan page untuk partial reload.
- [x] L3. Tambah guard test partial reload.
- [x] L4. Validasi concern + sinkronisasi status/log bila concern ditutup.

## Validasi

- [x] V1. `php artisan test tests/Feature/KecamatanDesaArsipTest.php --compact`
- [x] V2. `npm run build`
- [x] V3. `php artisan test --compact`

## Risiko

- Risiko 1: prop filter/pagination pendukung hilang jika contract `only` tidak sinkron dengan render page.
- Risiko 2: switch cakupan `arsip saya` vs `desa` bisa drift jika helper partial reload dipakai pada jalur yang salah.

## Keputusan

- [x] K1: rollout ini tetap memakai route yang sudah ada; tidak menambah endpoint JSON baru.
- [x] K2: partial prop dibatasi ke `documents` dan `filters`; `desaOptions` dan `pagination` tetap prop stabil.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru tidak diperlukan; ini hanya rollout pattern partial reload yang sudah tervalidasi.

## Fallback Plan

- Jika rollout ini menimbulkan regresi:
  - rollback helper partial reload dan kembalikan visit penuh untuk `desa-arsip`,
  - pertahankan guard test concern untuk investigasi lanjutan.

## Output Final

- [x] O1. Ringkasan rollout partial reload ke `desa-arsip`.
- [x] O2. Daftar file terdampak.
- [x] O3. Hasil validasi concern.
