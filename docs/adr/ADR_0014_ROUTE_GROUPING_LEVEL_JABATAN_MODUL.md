# ADR 0014 Route Grouping Level Jabatan Modul

Tanggal: 2026-06-06
Status: `accepted`
Owner:
Related TODO: `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`
Supersedes: `-`
Superseded by: `-`

## Konteks

- `routes/web.php` saat ini sudah dipisahkan per level (`desa`, `kecamatan`), tetapi di dalam blok level masih campur antara sekretaris, pokja, report-only, dan monitoring.
- Kebutuhan eksekusi berikutnya adalah regroup route tanpa mengubah URI publik, nama route, middleware, atau controller binding.
- Tanpa kontrak eksplisit, refactor route berisiko menggeser owner modul secara tidak sengaja dan menyulitkan rollback.

## Opsi yang Dipertimbangkan
### Opsi A - Tetap Flat di Dalam Blok Level

- Ringkasan pendek: route tetap dikelompokkan hanya per level.
- Kelebihan: minim perubahan struktur.
- Konsekuensi: sulit membaca ownership per jabatan, sulit audit cluster, dan rawan overlap report/resource.

### Opsi B - Grouping Bertingkat `level -> jabatan -> modul`

- Ringkasan pendek: setiap blok level dipecah lagi menjadi cluster sekretaris/umum, pokja I-IV, dan lintas pokja/report-only.
- Kelebihan: struktur lebih terbaca, audit ownership lebih jelas, refactor bertahap lebih aman.
- Konsekuensi: butuh regroup manual yang hati-hati, terutama untuk route report-only dan route turunan.

## Keputusan

- Opsi terpilih: Opsi B.
- Alasan utama: struktur bertingkat memberi urutan baca yang konsisten dan meminimalkan salah taruh route saat refactor.
- Kontrak yang dikunci:
  - urutan baca route adalah `level -> jabatan -> modul`,
  - regroup hanya dilakukan di `routes/web.php`,
  - URI publik, nama route, middleware, dan controller binding tidak berubah,
  - route report-only tetap mengikuti owner modul asal,
  - route turunan `catatan-keluarga` dan `simulasi` tetap dipertahankan sebagai subcluster alami.

## Dampak

- Dampak positif: navigasi file lebih mudah, ownership route lebih jelas, dan refactor dapat dipecah per batch.
- Trade-off: penataan ulang butuh review manual pada route yang berpotensi overlap.
- Area terdampak (route/request/use case/repository/test/docs):
  - `routes/web.php`
  - `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`

## Validasi

- [ ] Targeted test concern.
- [ ] Regression test concern terkait.
- [ ] `php artisan test` (jika perubahan signifikan).

## Rollback/Fallback Plan

- Kembalikan grouping ke struktur flat per level jika regroup bertingkat menimbulkan konflik ownership atau overlap route.
- Bila ada route yang ambigu, pertahankan pada blok asal dan tandai sebagai pengecualian di TODO sebelum patch runtime.

## Referensi

- `AGENTS.md`
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/CODE_PLACEMENT_POLICY.md`
- `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`

## Status Log

- 2026-06-06: `proposed` -> `accepted` | regroup route bertingkat dipilih agar grouping level dan jabatan terbaca konsisten tanpa mengubah URI publik.
