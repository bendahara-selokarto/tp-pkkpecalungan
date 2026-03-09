# TODO DBJ26A1 Pilot Dashboard Wave 4 Json Detail Widget Per Desa

Tanggal: 2026-03-08  
Status: `done` (`state:full-suite-and-build-validated`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Concern ini adalah child concern `SPA26A1` untuk `Wave 4` endpoint JSON kecil pada widget dashboard yang sangat interaktif.
- Baseline setelah `DBL26A1` dan `DBS26A1`:
  - `dashboardBlocks` sudah deferred,
  - state presentasional dashboard sudah lokal/stateful,
  - blok dokumen per-desa masih membawa nested detail `per_module` pada payload Inertia meski rincian itu tidak dibutuhkan pada first paint.
- Target batch ini adalah memindahkan rincian nested per-desa/per-modul ke endpoint JSON kecil yang hanya dipanggil saat block relevan dibuka.

## Kontrak Concern (Lock)

- Domain: dashboard interactive detail widget concern.
- Role/scope target: role dashboard kecamatan yang memiliki blok rincian per-desa (`kecamatan-pokja-*`, sekretaris kecamatan untuk section tertentu).
- Boundary data:
  - backend: `app/Http/Controllers/DashboardController.php`,
  - backend use case: `app/Domains/Wilayah/Dashboard/UseCases/BuildDashboardBlockDetailWidgetUseCase.php`,
  - frontend: `resources/js/Pages/Dashboard.vue`,
  - routes: `routes/web.php`,
  - tests: `tests/Feature/DashboardBlockDetailWidgetTest.php`, `tests/Feature/DashboardDocumentCoverageTest.php`.
- Acceptance criteria:
  - blok rincian per-desa memuat metadata endpoint detail pada payload awal,
  - nested `per_module` tidak ikut first load untuk blok pilot,
  - detail widget dimuat lewat JSON kecil saat block dibuka,
  - otorisasi backend tetap mengikuti scope/visibility dashboard.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] T1. Widget rincian per-desa memiliki endpoint JSON kecil yang terkontrol.
- [x] T2. Payload awal blok pilot tidak lagi membawa nested `per_module`.
- [x] T3. UI dashboard memuat rincian per-modul hanya saat block dibuka.

## Langkah Eksekusi

- [x] L0. Analisis scoped dependency + side effect.
- [x] L1. Patch minimal backend route/controller/usecase untuk detail widget.
- [x] L2. Patch frontend dashboard untuk fetch on-expand.
- [x] L3. Sinkronisasi parent concern + registry + log + playbook.

## Validasi

- [x] V1. `php artisan test tests/Feature/DashboardBlockDetailWidgetTest.php --compact`
- [x] V2. `php artisan test tests/Feature/DashboardDocumentCoverageTest.php --compact`
- [x] V2a. `php artisan test tests/Unit/UseCases/BuildDashboardBlockDetailWidgetUseCaseTest.php --compact`
- [x] V2b. `php artisan test tests/Unit/Architecture/UnitCoverageGateTest.php --compact`
- [x] V3. Frontend compile guard `npm run build` di-offload ke operator lokal karena concern ini menyentuh halaman dashboard Vue.
- [x] V4. Full regression `php artisan test --compact` di-offload ke operator lokal bila batch ini ditutup.

## Risiko

- Risiko 1: endpoint detail terlalu generik dan berkembang menjadi pseudo-API dashboard.
- Risiko 2: block metadata/detail endpoint drift dengan key block backend jika naming berubah tanpa guard test.
- Risiko 3: fetch on-expand gagal dan menurunkan UX jika tidak ada fallback/error state yang jelas.

## Keputusan

- [x] K1: pilot dibatasi pada blok dokumen rincian per-desa, bukan semua block dashboard.
- [x] K2: endpoint detail hanya dibuka untuk key block yang didukung; block lain tetap 404.
- [x] K3: nested `per_module` dipindah ke endpoint detail, sementara labels/totals ringkas tetap ada pada payload awal.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru tidak diperlukan; concern ini tetap berada dalam boundary dashboard yang sama.
- [x] Pattern reusable dicatat ke playbook sebagai endpoint JSON kecil on-expand, bukan API layer baru lintas aplikasi.

## Fallback Plan

- Jika endpoint detail menimbulkan regresi:
  - rollback metadata/detail fetch di block pilot,
  - kembalikan nested detail ke payload Inertia deferred,
  - pertahankan guard test endpoint bila masih relevan untuk follow-up batch.

## Output Final

- [x] O1. Ringkasan endpoint detail widget per-desa dan alasan teknisnya.
- [x] O2. Daftar file terdampak.
- [x] O3. Hasil validasi targeted + status offload build/full regression.
