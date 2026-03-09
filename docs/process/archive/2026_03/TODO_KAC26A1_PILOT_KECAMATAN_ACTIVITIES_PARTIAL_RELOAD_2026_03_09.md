# TODO KAC26A1 Pilot Kecamatan Activities Partial Reload

Tanggal: 2026-03-09  
Status: `done` (`state:full-suite-and-build-validated`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Rollout partial reload pasca-roadmap Inertia berlanjut ke halaman buku kegiatan kecamatan utama.
- `Kecamatan/Activities/Index.vue` masih memakai visit penuh untuk mode cakupan `kecamatan`, per-page, dan pagination.
- Concern ini mirip `KDA26A1`, tetapi tanpa filter desa/status/kata kunci; targetnya menjaga payload loop list tetap ramping di mode kecamatan.

## Kontrak Concern (Lock)

- Domain: rollout partial reload concern untuk buku kegiatan kecamatan.
- Role/scope target: user `kecamatan`, termasuk `kecamatan-sekretaris` pada mode cakupan `kecamatan`.
- Boundary data:
  - `app/Domains/Wilayah/Activities/Controllers/KecamatanActivityController.php`,
  - `resources/js/Pages/Kecamatan/Activities/Index.vue`,
  - `tests/Feature/KecamatanActivityTest.php`.
- Acceptance criteria:
  - visit per-page/pagination pada mode `kecamatan` memakai helper partial reload terpusat,
  - prop reload dibatasi ke `activities` dan `filters`,
  - contract auth/scope/tahun anggaran tidak berubah.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] T1. Halaman buku kegiatan kecamatan memakai partial reload untuk loop list utama.
- [x] T2. Controller mengirim prop yang cocok untuk partial reload tanpa memecah route.
- [x] T3. Guard test partial reload concern ditambahkan.

## Langkah Eksekusi

- [x] L1. Analisis scoped dependency + side effect.
- [x] L2. Patch minimal pada boundary arsitektur.
- [x] L3. Sinkronisasi dokumen concern terkait (jika trigger hardening aktif).

## Validasi

- [x] V1. `php artisan test tests/Feature/KecamatanActivityTest.php --compact`
- [x] V2. `npm run build`
- [x] V3. `php artisan test --compact`

## Risiko

- Risiko 1: partial reload dapat menghilangkan prop pendukung list jika contract `only` tidak sinkron dengan render page.
- Risiko 2: switch sekretaris ke mode monitoring desa bisa drift jika helper partial reload dipakai pada route yang salah.

## Keputusan

- [x] K1: rollout ini tetap memakai route yang sudah ada; tidak menambah endpoint JSON baru.
- [x] K2: partial prop dibatasi ke `activities` dan `filters`; `pagination` tetap prop stabil.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru tidak diperlukan; ini hanya rollout pattern partial reload yang sudah tervalidasi sebelumnya.

## Fallback Plan

- Jika rollout ini menimbulkan regresi:
  - rollback helper partial reload dan kembalikan visit penuh untuk `activities`,
  - pertahankan guard test concern untuk investigasi lanjutan.

## Output Final

- [x] O1. Ringkasan rollout partial reload ke `activities`.
- [x] O2. Daftar file terdampak.
- [x] O3. Hasil validasi concern.
