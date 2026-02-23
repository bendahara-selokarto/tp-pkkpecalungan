# Dashboard Aggregate Risk Test 2026-02-23

Tanggal: 2026-02-23  
Status: `baseline-tested`

## Ruang Lingkup

- Uji baseline dashboard saat ini sebelum refactor role-aware aggregate.
- Fokus risiko:
  - kebocoran scope/area,
  - drift role-scope terhadap blok agregat,
  - performa query agregat,
  - konsistensi cache.

## Eksekusi Uji Otomatis

Perintah yang dijalankan:

- `php artisan test --filter=DashboardActivityChartTest`
- `php artisan test --filter=DashboardDocumentCoverageTest`
- `php artisan test --filter=BuildDashboardDocumentCoverageUseCaseTest`
- `php artisan test --filter=MenuVisibilityPayloadTest`
- `php artisan test --filter=ModuleVisibilityMiddlewareTest`
- `php artisan test --filter=RoleMenuVisibilityServiceTest`

Hasil:

- Semua test lulus (`19 tests`, `250 assertions` agregat dari command di atas).
- Baseline saat ini menunjukkan guard scope/role utama sudah bekerja untuk skenario yang diuji.

## Temuan Risiko Arsitektur (Ordered by Severity)

### High

1. Cache key belum siap untuk mode dashboard dinamis bertingkat (`all/by level/by sub-level`).
- Bukti: cache key hanya `scope + area_id`.
- Referensi: `app/Domains/Wilayah/Dashboard/UseCases/BuildDashboardDocumentCoverageUseCase.php:28`.
- Dampak: saat filter dinamis diperkenalkan, payload antar mode bisa saling tertukar (cache collision).
- Rekomendasi: tambahkan `role-signature + filter-signature + block-signature` ke key.

2. Kontrak response dashboard belum memisahkan blok role-aware dan belum membawa metadata sumber metrik.
- Bukti: controller masih merge `activity` + `documents` ke payload generik.
- Referensi: `app/Http/Controllers/DashboardController.php:28`, `app/Http/Controllers/DashboardController.php:36`.
- Dampak: label mudah ambigu, trace sumber metrik sulit, dan rawan salah interpretasi saat blok bertambah.
- Rekomendasi: pindah ke `dashboardBlocks[]` dengan `sources` eksplisit per blok.

### Medium

3. Risiko amplifikasi query saat refactor aggregate per group/per filter.
- Bukti: agregasi dokumen iteratif per definisi modul dan menjalankan `countModelByScope` per model.
- Referensi: `app/Domains/Wilayah/Dashboard/Repositories/DashboardDocumentCoverageRepository.php:60`, `app/Domains/Wilayah/Dashboard/Repositories/DashboardDocumentCoverageRepository.php:208`.
- Dampak: beban query meningkat linear terhadap jumlah blok/filter, rentan melambat pada data besar.
- Rekomendasi: pre-aggregation/materialized summary atau batching query per group.

4. Invalidasi cache eksplisit dashboard dokumen perlu event-based agar tidak bergantung TTL semata.
- Status: mitigated (observer model + cache version bump sudah diterapkan).
- Referensi implementasi: `app/Domains/Wilayah/Dashboard/Observers/InvalidateDashboardDocumentCacheObserver.php`, `app/Domains/Wilayah/Dashboard/Services/DashboardDocumentCacheVersionService.php`, `app/Domains/Wilayah/Dashboard/UseCases/BuildDashboardDocumentCoverageUseCase.php`.

5. Potensi drift definisi group-module antara visibilitas menu dan agregasi dashboard.
- Bukti: mapping group module ada di service menu, sedangkan dashboard coverage punya daftar modul sendiri.
- Referensi: `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php:16`, `app/Domains/Wilayah/Dashboard/Repositories/DashboardDocumentCoverageRepository.php:270`.
- Dampak: menu menampilkan group tertentu, tetapi angka dashboard tidak sinkron dengan group yang sama.
- Rekomendasi: satukan mapping ke satu source contract yang dipakai menu + dashboard.

### Low

6. Ambiguitas semantik metrik `catatan-keluarga` pada coverage karena masih memakai model yang sama dengan `data-warga`.
- Bukti: slug `catatan-keluarga` menggunakan `DataWarga::class`.
- Referensi: `app/Domains/Wilayah/Dashboard/Repositories/DashboardDocumentCoverageRepository.php:420`.
- Dampak: angka terlihat benar secara teknis, namun bisa disalahartikan sebagai source terpisah.
- Rekomendasi: tampilkan `source_note` eksplisit di UI bahwa ini proyeksi coverage, bukan tabel fisik terpisah.

## Gate Sebelum Implementasi Refactor

- [x] Update cache key dashboard agar memasukkan signature role + filter.
- [x] Migrasi payload ke `dashboardBlocks[]` + metadata source.
- [x] Tambah test anti-collision cache untuk mode `all/by level/by sub-level`.
- [x] Tambah test sinkronisasi mapping menu-vs-dashboard (single source mapping).
- [x] Tambah benchmark query minimum untuk skenario kecamatan dengan banyak desa.

## Re-Test 2 (2026-02-23, setelah penguncian tujuan monitor data)

Perintah re-test:

- `php artisan test --filter=DashboardActivityChartTest`
- `php artisan test --filter=DashboardDocumentCoverageTest`
- `php artisan test --filter=BuildDashboardDocumentCoverageUseCaseTest`
- `php artisan test --filter=MenuVisibilityPayloadTest`
- `php artisan test --filter=ModuleVisibilityMiddlewareTest`
- `php artisan test --filter=RoleMenuVisibilityServiceTest`

Hasil:

- Semua test tetap lulus (`19 tests`, `250 assertions`).
- Risiko high #1 dinyatakan ditutup:
  - cache key kini memuat `role-signature + filter-signature + block-signature` (`BuildDashboardDocumentCoverageUseCase` v2 key).
- Risiko high #2 masih aktif:
  - kontrak payload dashboard masih generik (`dashboardStats/dashboardCharts`) dan belum role-aware block + metadata source.

## Re-Test 3 (2026-02-23, setelah mitigasi payload role-aware block)

Perubahan implementasi:

- Tambah payload baru `dashboardBlocks[]` pada response dashboard (backward compatible, payload lama tetap ada).
- Tiap block kini memuat metadata sumber (`source_group`, `source_scope`, `source_area_type`, `source_modules`, `source_note`, `filter_context`).
- Block dibangun sesuai visibilitas role-group melalui use case role-aware.

Perintah re-test:

- `php artisan test --filter=DashboardDocumentCoverageTest`
- `php artisan test --filter=BuildDashboardDocumentCoverageUseCaseTest`
- `php artisan test --filter=DashboardActivityChartTest`
- `php artisan test --filter=RoleMenuVisibilityServiceTest`

Hasil:

- Semua test lulus (`13 tests`, `201 assertions`).
- Risiko high #2 dinyatakan ditutup:
  - kontrak payload tidak lagi hanya `dashboardStats/dashboardCharts`; sudah ada `dashboardBlocks[]` + metadata sumber.

Catatan residual:

- Rendering UI utama masih memakai payload lama; adopsi penuh `dashboardBlocks[]` di frontend dijadwalkan pada fase refactor berikutnya untuk menutup gap keterbacaan end-user.

## Re-Test 4 (2026-02-23, setelah mitigasi invalidasi cache event-based)

Perubahan implementasi:

- Tambah observer `InvalidateDashboardDocumentCacheObserver` untuk model coverage dashboard.
- Tambah service `DashboardDocumentCacheVersionService` dan inject version ke cache key (`v3`).
- Mutasi data domain kini memicu bump version sehingga cache lama tidak dipakai.

Perintah re-test:

- `php artisan test --filter=BuildDashboardDocumentCoverageUseCaseTest`
- `php artisan test --filter=DashboardDocumentCoverageTest`
- `php artisan test --filter=DashboardActivityChartTest`
- `php artisan test --filter=RoleMenuVisibilityServiceTest`

Hasil:

- Semua test lulus (`13 tests`, `201 assertions`).
- Risiko medium #4 dinyatakan ditutup:
  - cache dashboard terinvalidasi otomatis saat data berubah, tidak lagi bergantung pada `Cache::flush` manual.

## Re-Test 5 (2026-02-23, setelah mitigasi sinkronisasi mapping menu-vs-dashboard)

Perubahan implementasi:

- Tambah kontrak repository `trackedModuleSlugs()` untuk mengekspos slug coverage dashboard.
- Tambah guard test `DashboardCoverageMenuSyncTest` agar setiap slug menu wajib:
  - masuk coverage dashboard, atau
  - dicatat eksplisit sebagai non-coverage yang disengaja.

Perintah re-test:

- `php artisan test --filter=DashboardCoverageMenuSyncTest`
- `php artisan test --filter=DashboardDocumentCoverageTest`
- `php artisan test --filter=BuildDashboardDocumentCoverageUseCaseTest`

Hasil:

- Semua test lulus (`8 tests`, `97 assertions`).
- Risiko medium #5 dinyatakan ditutup:
  - drift mapping menu-vs-dashboard kini dijaga oleh test sinkronisasi eksplisit.

## Re-Test 6 (2026-02-23, setelah mitigasi benchmark query agregat)

Perubahan implementasi:

- Tambah benchmark query guard `DashboardDocumentCoverageQueryBenchmarkTest` untuk membandingkan:
  - kecamatan dengan desa sedikit,
  - kecamatan dengan desa banyak.
- Guard benchmark menahan regresi performa query melalui:
  - batas query absolut,
  - batas pertambahan query relatif antar skenario.

Perintah re-test:

- `php artisan test --filter=DashboardDocumentCoverageQueryBenchmarkTest`
- `php artisan test --filter=BuildDashboardDocumentCoverageUseCaseTest`
- `php artisan test --filter=DashboardDocumentCoverageTest`

Hasil:

- Semua test lulus (`7 tests`, `99 assertions`).
- Risiko medium #3 dinyatakan ditutup:
  - baseline query agregat kecamatan banyak desa kini dijaga oleh benchmark test otomatis.
