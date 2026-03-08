# TODO DBL26A1 Pilot Dashboard Wave 2 Deferred Blocks Dan Lazy Fetch

Tanggal: 2026-03-08  
Status: `in-progress` (`state:targeted-validated-full-suite-build-pending`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Concern ini adalah child concern `SPA26A1` untuk `Wave 2` lazy fetch pada dashboard setelah `DWI26A1` menutup partial reload wave 1.
- Baseline setelah wave 1:
  - route dashboard sudah memakai partial reload terpusat,
  - `dashboardStats`, `dashboardCharts`, `dashboardBlocks`, dan `dashboardContext` masih berasal dari satu payload backend,
  - payload `dashboardBlocks` adalah kandidat paling aman untuk ditunda karena bersifat sekunder terhadap first paint dan paling berat secara struktur data.
- Target batch ini adalah:
  - memuat statistik/chart dashboard lebih cepat pada first paint,
  - menunda blok dinamis dashboard sampai setelah render awal,
  - tetap memakai route dashboard yang sama tanpa membuat API liar baru.

## Kontrak Concern (Lock)

- Domain: dashboard secondary payload delivery concern.
- Role/scope target: seluruh role non `super-admin` yang memakai dashboard.
- Boundary data:
  - backend: `app/Http/Controllers/DashboardController.php`,
  - frontend: `resources/js/Pages/Dashboard.vue`,
  - test contract: `tests/Feature/DashboardDocumentCoverageTest.php`, `tests/Feature/DashboardActivityChartTest.php`, `tests/Feature/DashboardChartPdfPrintTest.php`.
- Acceptance criteria:
  - `dashboardBlocks` tidak ikut first load,
  - `dashboardBlocks` dimuat sebagai deferred prop dan tetap bisa diminta ulang via partial reload,
  - watcher/filter dashboard tidak salah menafsirkan state saat blok masih pending,
  - kontrak stats/charts/context dan jalur PDF tetap stabil.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] T1. `dashboardBlocks` menjadi deferred prop yang dimuat setelah first paint.
- [x] T2. UI dashboard punya fallback loading yang eksplisit untuk blok deferred.
- [x] T3. Ada guard test yang membuktikan blok dashboard tidak muncul di first load tetapi tersedia lewat deferred reload.

## Langkah Eksekusi

- [x] L0. Analisis scoped dependency + side effect.
- [x] L1. Patch minimal pada boundary arsitektur dashboard.
- [x] L2. Hardening test contract deferred prop.
- [x] L3. Sinkronisasi parent concern + registry + log + playbook pattern.

## Validasi

- [x] V1. `php artisan test tests/Feature/DashboardActivityChartTest.php --compact`
- [x] V2. `php artisan test tests/Feature/DashboardDocumentCoverageTest.php --compact`
- [x] V3. `php artisan test tests/Feature/DashboardChartPdfPrintTest.php --compact`
- [ ] V4. Full regression `php artisan test --compact` di-offload ke operator lokal bila batch ini ditutup.
- [ ] V5. Frontend compile guard `npm run build` di-offload ke operator lokal karena batch ini menyentuh halaman dashboard Vue.

## Risiko

- Risiko 1: watcher dashboard menganggap blok belum dimuat sebagai kondisi kosong permanen dan memicu visit yang tidak perlu.
- Risiko 2: test contract lama yang mengharapkan `dashboardBlocks` pada first load menjadi drift jika tidak dipindah ke assertion deferred.
- Risiko 3: refactor backend masih menghitung blok saat menyiapkan stats/charts sehingga `defer` tidak memberi ROI nyata.

## Keputusan

- [x] K1: batch ini tetap memakai route dashboard yang sama; tidak ada endpoint API/JSON baru.
- [x] K2: blok yang ditunda adalah `dashboardBlocks`, bukan `dashboardStats` atau `dashboardCharts`.
- [x] K3: loading fallback dipertahankan di level halaman memakai komponen `Deferred` dari Inertia Vue, bukan fetch manual custom.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru tidak diperlukan; boundary controller/use case/repository tetap utuh.
- [x] Pattern reusable dicatat ke playbook sebagai optimasi Inertia sekunder, bukan keputusan arsitektur lintas concern.

## Fallback Plan

- Jika deferred blocks menimbulkan drift:
  - rollback `dashboardBlocks` ke lazy closure biasa,
  - pertahankan split backend `stats/charts` vs `blocks` bila masih aman,
  - tunda lazy fetch dashboard ke batch berikutnya setelah guard test ditambah.

## Output Final

- [x] O1. Ringkasan perubahan deferred blocks dashboard dan alasan teknisnya.
- [x] O2. Daftar file terdampak.
- [x] O3. Hasil validasi targeted + status offload full regression/build.
