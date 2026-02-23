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
- Khusus `desa-sekretaris`: level default dikunci ke `desa`, tanpa kontrol `sub_level`, dan filter utama berbasis `group` (`all`, `pokja-i`, `pokja-ii`, `pokja-iii`, `pokja-iv`).
- Khusus dashboard sekretaris:
  - `Section 1` menampilkan domain sekretaris.
  - `Section 2` menampilkan semua pokja pada level yang sama.
  - `Section 3` hanya untuk scope kecamatan: menampilkan semua pokja pada level setingkat di bawahnya (desa turunan).
  - `Section 2` dan `Section 3` memiliki filter `by_group`: `all|pokja-i|pokja-ii|pokja-iii|pokja-iv`.

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
- Pengecualian `desa-sekretaris`:
  - `mode` default `by-level`.
  - `level` default `desa` dan tidak ditampilkan sebagai kontrol.
  - `sub_level` tidak ditampilkan.
  - Filter yang ditampilkan hanya `By Group`: `all|pokja-i|pokja-ii|pokja-iii|pokja-iv`.
- Aturan section sekretaris:
  - `section-1-sekretaris`: tanpa filter pokja.
  - `section-2-pokja-level-aktif`: wajib filter `By Group`.
  - `section-3-pokja-level-bawah` (kecamatan saja): wajib filter `By Group`.

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
- [x] `U7` Penyesuaian UX `desa-sekretaris`:
  - sembunyikan kontrol `mode`, `level`, `sub_level`.
  - pakai kontrol tunggal `by_group` dengan opsi `all` + `pokja-i..iv`.
  - default query untuk role ini: `mode=by-level&level=desa&sub_level=all&by_group=all`.
- [x] `U8` Penyesuaian layout section sekretaris:
  - render section 1 (domain sekretaris) sebagai blok terpisah paling atas.
  - render section 2 (pokja level aktif) sebagai blok terpisah dengan filter `by_group`.
  - untuk `kecamatan-sekretaris`, tambah section 3 (pokja level bawah/desa turunan) dengan filter `by_group` terpisah.
  - kedua filter section 2 dan 3 tidak saling mengubah state satu sama lain.

## Validasi Wajib

- [ ] Feature test Inertia:
  - `dashboardBlocks` muncul untuk role valid.
  - metadata sumber (`sources`) ikut terkirim konsisten pada blok.
- [ ] Feature test query filter:
  - request `/dashboard?mode=by-level&level=desa` mengubah `sources.filter_context`.
  - request `/dashboard?mode=by-sub-level&sub_level=<x>` tidak merusak payload.
- [ ] Feature test role `desa-sekretaris`:
  - panel filter hanya menampilkan `by_group`.
  - default context tetap `level=desa` tanpa `sub_level`.
  - opsi `by_group` terbatas pada `all|pokja-i|pokja-ii|pokja-iii|pokja-iv`.
- [ ] Feature test role `kecamatan-sekretaris`:
  - section 1 tampil tanpa filter pokja.
  - section 2 (pokja level kecamatan) + section 3 (pokja level desa turunan) tampil bersamaan.
  - masing-masing section 2/3 memiliki filter `by_group` sendiri dengan opsi `all|pokja-i|pokja-ii|pokja-iii|pokja-iv`.
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
- [x] Pengecualian `desa-sekretaris`: kontrol filter bertingkat disederhanakan menjadi `by_group` (`all` + `pokja-i..iv`) dengan level default tetap `desa`.
- [x] Struktur section sekretaris dikunci: section 1 domain sekretaris, section 2 pokja level aktif, section 3 khusus kecamatan untuk level bawah; filter `by_group` hanya pada section 2/3.

## Referensi Implementasi

- `app/Http/Controllers/DashboardController.php`
- `app/Domains/Wilayah/Dashboard/UseCases/BuildRoleAwareDashboardBlocksUseCase.php`
- `resources/js/Pages/Dashboard.vue`
- `docs/process/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`
