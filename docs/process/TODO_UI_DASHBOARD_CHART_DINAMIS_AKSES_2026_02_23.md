# TODO UI Dashboard Chart Dinamis Akses 2026-02-23

Tanggal: 2026-02-23  
Status: `planned`

## Konteks

- Backend dashboard sudah menyiapkan payload role-aware `dashboardBlocks[]` + metadata sumber (`sources`), namun UI `resources/js/Pages/Dashboard.vue` masih dominan memakai payload legacy (`dashboardStats/dashboardCharts`).
- Kebutuhan produk: chart dashboard harus dinamis mengikuti hak akses role-scope, keterbacaan diutamakan, dan pengguna role bertingkat dapat filter `all`, `by level`, `by sub-level`.
- Guard akses backend sudah aktif; UI wajib menjadi refleksi data backend, bukan authority akses.

## Target Hasil

- `Dashboard.vue` merender chart berdasarkan `dashboardBlocks[]` (per blok hak akses), bukan asumsi tunggal activity/documents.
- Pengguna melihat hanya blok chart yang diizinkan role-nya.
- Setiap blok menampilkan sumber data secara eksplisit (`Sumber`, `Cakupan`, `Filter aktif`) untuk mencegah label ambigu.
- Filter dinamis (`mode`, `level`, `sub_level`) tersinkron dengan URL query agar reproducible dan shareable.

## Kontrak UI Dinamis

### Input Data Utama

- `dashboardBlocks[]` sebagai source utama rendering blok chart:
  - `key`, `kind`, `group`, `group_label`, `mode`, `title`, `stats`, `charts`, `sources`.
- Payload legacy (`dashboardStats/dashboardCharts`) dipertahankan hanya sebagai fallback transisi.

### Filter Interaktif

- `mode`:
  - `all`: tampil agregat penuh sesuai hak akses.
  - `by-level`: tampil agregat sesuai level (`desa`/`kecamatan`).
  - `by-sub-level`: tampil agregat dengan fokus sub-level (desa turunan pada scope kecamatan).
- `level`: `all|desa|kecamatan` (aktif saat `mode=by-level`).
- `sub_level`: `all|<area_id/desa_slug>` (aktif saat `mode=by-sub-level`).

### Aturan Keterbacaan

- Header blok wajib:
  - Judul: `title` backend.
  - Subjudul: `Sumber: <source_modules>` + `Cakupan: <source_area_type>`.
  - Badge mode: `RW` atau `RO`.
- Label KPI tidak boleh generik (`Total` saja). Wajib menyebut konteks blok.
- Empty state per blok wajib menyebut alasan konteks (`Belum ada data untuk filter aktif`).

## Langkah Eksekusi (Checklist)

- [x] `U1` Refactor props di `Dashboard.vue`:
  - gunakan `dashboardBlocks` sebagai primary computed source.
  - fallback ke payload lama bila `dashboardBlocks` kosong.
- [x] `U2` Tambah state filter UI:
  - inisialisasi dari query string (`mode`, `level`, `sub_level`).
  - lakukan sync ke URL saat filter berubah (Inertia visit query-only).
- [x] `U3` Bangun renderer blok dinamis:
  - loop per `dashboardBlocks`.
  - untuk `kind=documents`, render chart `coverage_per_module`.
  - untuk `kind=activity`, render chart activity existing.
- [x] `U4` Tambah panel metadata sumber:
  - render `source_group`, `source_modules`, `source_area_type`, `filter_context`.
  - format label human-readable tanpa menghilangkan token canonical.
- [x] `U5` Atur UX mobile/desktop:
  - filter panel collapse pada mobile.
  - chart tetap terbaca (tinggi chart minimum + list nilai ringkas).
- [x] `U6` Transisi aman:
  - pertahankan blok legacy sementara di belakang feature flag lokal sederhana (computed switch).
  - hapus fallback hanya setelah validasi end-to-end selesai.

## Validasi Wajib

- [ ] Feature test Inertia:
  - `dashboardBlocks` muncul untuk role valid.
  - metadata sumber (`sources`) ikut terkirim konsisten pada blok.
- [ ] Feature test query filter:
  - request `/dashboard?mode=by-level&level=desa` mengubah `sources.filter_context`.
  - request `/dashboard?mode=by-sub-level&sub_level=<x>` tidak merusak payload.
- [ ] UI smoke check manual:
  - role pokja hanya melihat blok pokja terkait.
  - sekretaris melihat blok sekretaris + blok pokja.
  - kecamatan bertingkat dapat ganti `all/by level/by sub-level`.
- [ ] Jalankan regression:
  - `php artisan test --filter=DashboardDocumentCoverageTest`
  - `php artisan test --filter=DashboardActivityChartTest`

## Risiko

- [ ] Risiko duplikasi informasi saat payload legacy dan `dashboardBlocks` tampil bersamaan.
- [ ] Risiko filter sub-level membingungkan jika label area tidak eksplisit.
- [ ] Risiko UI berat bila seluruh blok dirender tanpa lazy strategy pada role multi-blok.

## Mitigasi

- [ ] Render satu jalur utama saja (`dashboardBlocks`), legacy sebagai fallback tersembunyi.
- [ ] Gunakan label area eksplisit (`Desa X`, `Kecamatan Y`) pada opsi filter sub-level.
- [ ] Tambahkan virtual grouping sederhana: chart dirender saat blok expanded.

## Keputusan Terkunci di Rencana UI Ini

- [x] Keterbacaan diutamakan dibanding kepadatan tampilan.
- [x] UI dashboard berbasis hak akses backend (`dashboardBlocks`) sebagai source utama.
- [x] Filter dinamis wajib tersedia: `all`, `by-level`, `by-sub-level`.
- [x] Sumber data wajib terlihat per blok.

## Referensi Implementasi

- `app/Http/Controllers/DashboardController.php`
- `app/Domains/Wilayah/Dashboard/UseCases/BuildRoleAwareDashboardBlocksUseCase.php`
- `resources/js/Pages/Dashboard.vue`
- `docs/process/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`
