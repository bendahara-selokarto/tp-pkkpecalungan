# TODO UI Dashboard Chart Dinamis Akses 2026-02-23

Tanggal: 2026-02-23  
Status: `in-progress`

Catatan lanjutan visual minimalis:
- Eksekusi refactor UI minimalis direncanakan di `docs/process/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`.
- Acuan baseline visual: dashboard role `kecamatan-sekretaris` yang sudah diperbaiki pada sesi aktif.

## Konteks

- Backend dan UI dashboard sudah memakai payload role-aware `dashboardBlocks[]` sebagai jalur utama, dengan payload legacy (`dashboardStats/dashboardCharts`) masih dipertahankan sebagai fallback transisi.
- Kebutuhan produk: chart dashboard harus dinamis mengikuti hak akses role-scope, keterbacaan diutamakan, dan pengguna role bertingkat dapat filter `all`, `by level`, `by sub-level`.
- Guard akses backend sudah aktif; UI wajib menjadi refleksi data backend, bukan authority akses.

## Target Hasil

- `Dashboard.vue` merender chart berdasarkan `dashboardBlocks[]` (per blok hak akses), bukan asumsi tunggal activity/documents.
- Pengguna melihat hanya blok chart yang diizinkan role-nya.
- Setiap blok menampilkan sumber data secara eksplisit (`Sumber`, `Cakupan`, `Filter aktif`) untuk mencegah label ambigu.
- Filter dinamis (`mode`, `level`, `sub_level`) tersinkron dengan URL query agar reproducible dan shareable.
- Representasi activity dashboard tidak menampilkan metrik/visual berbasis status publikasi (`published`/`draft`).
- Khusus `section 1` pada scope `kecamatan`, chart activity ditampilkan dalam dua chart:
  - chart 1: `jumlah kegiatan per desa` tipe `pie` (filter bulan).
  - chart 2: `jumlah buku` terhadap `buku terisi` tipe `bar` (filter bulan).
  - Tambahan filter bulan: dropdown `section1_month` dengan opsi `all` + `1..12` untuk memfilter chart sesuai bulan terpilih.
- Khusus `desa-sekretaris`: level default dikunci ke `desa`, tanpa kontrol `sub_level`, dan filter utama berbasis group (`all`, `pokja-i`, `pokja-ii`, `pokja-iii`, `pokja-iv`) dengan query key `section2_group`.
- Khusus dashboard sekretaris:
  - `Section 1` menampilkan domain sekretaris.
  - `Section 2` menampilkan semua pokja pada level yang sama.
  - `Section 3` hanya untuk scope kecamatan: menampilkan semua pokja pada level setingkat di bawahnya (desa turunan).
  - `Section 2` dan `Section 3` memiliki filter group: `all|pokja-i|pokja-ii|pokja-iii|pokja-iv`.
  - Query key filter:
    - `section2_group` untuk section 2.
    - `section3_group` untuk section 3.
  - Add-on skenario kecamatan: saat `section 3` memilih `pokja-i`, tampilkan `section 4` untuk rincian sumber data per desa (referensi: `docs/process/TODO_SCENARIO_KECAMATAN_SECTION4_POKJA_I_2026_02_23.md`).

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
  - Filter yang ditampilkan hanya `By Group`: `all|pokja-i|pokja-ii|pokja-iii|pokja-iv` (query: `section2_group`).
- Aturan section sekretaris:
  - `section-1-sekretaris`: tanpa filter pokja.
    - untuk scope `kecamatan`, tersedia filter bulan `section1_month` (`all|1..12`) khusus chart `kegiatan per desa`.
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
  - pakai kontrol tunggal `section2_group` dengan opsi `all` + `pokja-i..iv`.
  - default query untuk role ini: `mode=by-level&level=desa&sub_level=all&section2_group=all&section3_group=all`.
- [x] `U8` Penyesuaian layout section sekretaris:
  - render section 1 (domain sekretaris) sebagai blok terpisah paling atas.
  - render section 2 (pokja level aktif) sebagai blok terpisah dengan filter `section2_group`.
  - untuk `kecamatan-sekretaris`, tambah section 3 (pokja level bawah/desa turunan) dengan filter `section3_group`.
  - kedua filter section 2 dan 3 tidak saling mengubah state satu sama lain.

## Validasi Wajib

- [ ] Feature test Inertia:
  - `dashboardBlocks` muncul untuk role valid.
  - metadata sumber (`sources`) ikut terkirim konsisten pada blok.
- [ ] Feature test query filter:
  - request `/dashboard?mode=by-level&level=desa` mengubah `sources.filter_context`.
  - request `/dashboard?mode=by-sub-level&sub_level=<x>` tidak merusak payload.
- [x] Feature test role `desa-sekretaris`:
  - panel filter hanya menampilkan `section2_group`.
  - default context tetap `level=desa` tanpa `sub_level`.
  - opsi `section2_group` terbatas pada `all|pokja-i|pokja-ii|pokja-iii|pokja-iv`.
- [x] Feature test role `kecamatan-sekretaris`:
  - section 1 tampil tanpa filter pokja.
  - section 2 (pokja level kecamatan) + section 3 (pokja level desa turunan) tampil bersamaan.
  - masing-masing section 2/3 memiliki filter `section2_group`/`section3_group` dengan opsi `all|pokja-i|pokja-ii|pokja-iii|pokja-iv`.
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
- [x] Pengecualian `desa-sekretaris`: kontrol filter bertingkat disederhanakan menjadi `section2_group` (`all` + `pokja-i..iv`) dengan level default tetap `desa`.
- [x] Struktur section sekretaris dikunci: section 1 domain sekretaris, section 2 pokja level aktif, section 3 khusus kecamatan untuk level bawah; filter memakai query `section2_group`/`section3_group`.
- [x] Kontrak query filter section dikunci: `section2_group` (section 2) dan `section3_group` (section 3).
- [x] KPI/chart status aktivitas (`published`/`draft`) tidak ditampilkan pada dashboard; fokus ringkasan activity: total + bulan ini + distribusi level.
- [x] Pada section 1 level kecamatan, chart activity dipecah menjadi dua chart: `jumlah kegiatan per desa` tipe `pie` dan `jumlah buku vs buku terisi` tipe `bar`, keduanya mengikuti filter bulan.

## Referensi Implementasi

- `app/Http/Controllers/DashboardController.php`
- `app/Domains/Wilayah/Dashboard/UseCases/BuildRoleAwareDashboardBlocksUseCase.php`
- `resources/js/Pages/Dashboard.vue`
- `docs/process/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`
