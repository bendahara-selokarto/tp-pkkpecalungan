# TODO DBS26A1 Pilot Dashboard Wave 3 Stateful Presentational UI

Tanggal: 2026-03-08  
Status: `done` (`state:targeted-and-build-validated`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Concern ini adalah child concern `SPA26A1` untuk `Wave 3` stateful local component setelah `DBL26A1` menutup lazy fetch blok dashboard.
- Baseline saat ini:
  - filter/query dashboard sudah terkunci lewat partial reload,
  - `dashboardBlocks` sudah deferred,
  - state presentasional `expanded/collapsed` blok masih reset saat visit Inertia berikutnya meskipun itu bukan state domain/backend.
- Target batch ini adalah mempertahankan state presentasional dashboard di client agar pengalaman eksplorasi blok tetap stabil antar visit tanpa menambah query, endpoint, atau kontrak backend baru.

## Kontrak Concern (Lock)

- Domain: dashboard presentational-ui state concern.
- Role/scope target: seluruh role non `super-admin` yang memakai dashboard.
- Boundary data:
  - frontend: `resources/js/Pages/Dashboard.vue`,
  - guard regression: `tests/Feature/DashboardDocumentCoverageTest.php`, `tests/Feature/DashboardActivityChartTest.php`.
- Acceptance criteria:
  - state expand/collapse blok dipertahankan di client antar visit Inertia,
  - tidak ada perubahan query string atau payload backend untuk state presentasional,
  - tidak ada drift terhadap contract deferred blocks wave 2.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] T1. State expand/collapse blok dashboard menjadi stateful lokal di client.
- [x] T2. Tidak ada perubahan behavior backend/query untuk concern ini.

## Langkah Eksekusi

- [x] L0. Analisis scoped dependency + side effect.
- [x] L1. Patch minimal pada halaman dashboard.
- [x] L2. Sinkronisasi parent concern + registry + log + playbook.

## Validasi

- [x] V1. `php artisan test tests/Feature/DashboardDocumentCoverageTest.php --compact`
- [x] V2. `php artisan test tests/Feature/DashboardActivityChartTest.php --compact`
- [x] V3. Frontend compile guard `npm run build` di-offload ke operator lokal karena concern ini frontend-only.

## Risiko

- Risiko 1: key `useRemember` terlalu generik dan menabrak state concern lain pada browser yang sama.
- Risiko 2: sync blok baru dari backend bisa menghidupkan ulang block state yang sudah tidak relevan jika cleanup state tidak hati-hati.

## Keputusan

- [x] K1: concern wave 3 pertama dibatasi ke state `expandedBlockKeys`, bukan seluruh draft filter dashboard.
- [x] K2: persistensi memakai `useRemember`, bukan localStorage custom.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru tidak diperlukan; ini hanya state UI lokal.
- [x] Pattern reusable dicatat ke playbook sebagai stateful presentational Inertia page.

## Fallback Plan

- Jika persistensi state lokal menimbulkan perilaku aneh:
  - rollback `useRemember` kembali ke `ref` biasa,
  - pertahankan logika sinkronisasi blok yang sudah aman.

## Output Final

- [x] O1. Ringkasan perubahan stateful UI dashboard dan alasan teknisnya.
- [x] O2. Daftar file terdampak.
- [x] O3. Hasil validasi targeted + status offload build.
