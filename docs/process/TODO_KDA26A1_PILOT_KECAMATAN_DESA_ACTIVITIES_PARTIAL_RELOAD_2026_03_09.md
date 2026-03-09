# TODO KDA26A1 Pilot Kecamatan Desa Activities Partial Reload

Tanggal: 2026-03-09  
Status: `done` (`state:full-suite-and-build-validated`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Setelah roadmap `SPA26A1` ditutup, pola partial reload yang sudah stabil perlu digulirkan ke halaman list/filter ber-ROI tinggi berikutnya.
- `Kecamatan/DesaActivities/Index.vue` masih memakai beberapa `router.get('/kecamatan/desa-activities', ...)` tanpa helper visit terpusat dan tanpa partial prop contract.
- Halaman ini sudah memiliki filter desa, status, kata kunci, dan paginasi sehingga cocok menjadi rollout concern pasca-roadmap.

## Kontrak Concern (Lock)

- Domain: rollout partial reload concern untuk monitoring kegiatan desa kecamatan.
- Role/scope target: user `kecamatan`, terutama `kecamatan-sekretaris` yang memakai mode monitoring desa.
- Boundary data:
  - `app/Domains/Wilayah/Activities/Controllers/KecamatanDesaActivityController.php`,
  - `resources/js/Pages/Kecamatan/DesaActivities/Index.vue`,
  - `tests/Feature/KecamatanDesaActivityTest.php`.
- Acceptance criteria:
  - visit filter/per-page/pagination memakai helper partial reload terpusat,
  - prop reload dibatasi ke `activities` dan `filters`,
  - contract auth/scope/year/filter tidak berubah.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] T1. Monitoring kegiatan desa kecamatan memakai partial reload untuk loop filter utama.
- [x] T2. Controller mengirim prop yang cocok untuk partial reload tanpa memecah route.
- [x] T3. Guard test partial reload concern tercatat hijau.

## Langkah Eksekusi

- [x] L1. Audit controller + page + test concern.
- [x] L2. Patch minimal controller dan page untuk partial reload.
- [x] L3. Tambah guard test partial reload.
- [x] L4. Validasi concern + sinkronisasi status/log bila concern ditutup.

## Validasi

- [x] V1. `php artisan test tests/Feature/KecamatanDesaActivityTest.php --compact`
- [x] V2. `npm run build`
- [x] V3. `php artisan test --compact`

## Risiko

- Risiko 1: partial reload menghilangkan prop pendukung filter jika `only` tidak konsisten dengan render page.
- Risiko 2: query filter monitoring desa drift dengan mode `kecamatan` utama jika helper visit tidak scoped jelas.

## Keputusan

- [x] K1: rollout ini tetap memakai route yang sudah ada; tidak menambah endpoint JSON baru.
- [x] K2: partial prop dibatasi ke `activities` dan `filters`; `desaOptions`, `statusOptions`, dan `pagination` tetap dipertahankan sebagai prop stabil.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru tidak diperlukan; ini hanya rollout pattern partial reload yang sudah tervalidasi sebelumnya.

## Fallback Plan

- Jika rollout ini menimbulkan regresi:
  - rollback helper partial reload dan kembalikan visit penuh untuk `desa-activities`,
  - pertahankan guard test concern untuk investigasi lanjutan.

## Output Final

- [x] O1. Ringkasan rollout partial reload ke `desa-activities`.
- [x] O2. Daftar file terdampak.
- [x] O3. Hasil validasi concern.
