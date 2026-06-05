# ADR 0013 Standarisasi Kode Wilayah Pecalungan

Tanggal: 2026-06-05  
Status: `accepted`  
Owner: `manto`  
Related TODO: `docs/process/TODO_WIL26A1_STANDARISASI_KODE_WILAYAH_PEcalungan_2026_06_05.md`  
Supersedes: `-`  
Superseded by: `-`

## Konteks

- Repository ini memakai `areas` sebagai source of truth wilayah.
- Pecalungan secara operasional diperlakukan sebagai 1 kecamatan dengan 10 desa canonical.
- User sudah menetapkan kode administratif desa yang stabil dan umum dipakai di dunia nyata.
- Tanpa kode wilayah canonical, nama area saja berisiko drift pada seed, dashboard, PDF, dan integrasi data.

## Opsi yang Dipertimbangkan
### Opsi A - Mengandalkan `name` + `level` saja

- Ringkasan pendek: wilayah diidentifikasi hanya dengan nama tampilan dan level.
- Kelebihan: tidak perlu perubahan schema.
- Konsekuensi: rawan perubahan penulisan, sulit menjadi referensi stabil lintas sistem, dan kurang cocok untuk standarisasi jangka panjang.

### Opsi B - Menambahkan `areas.code` sebagai identitas stabil

- Ringkasan pendek: setiap area memiliki kode unik dan immutable yang dipakai sebagai identitas administratif canonical.
- Kelebihan: stabil, mudah diseed, mudah diaudit, dan lebih aman untuk integrasi serta sinkronisasi lintas dokumen.
- Konsekuensi: perlu migrasi schema, penyesuaian seeder, dan test pengunci canonical.

## Keputusan

- Opsi terpilih: Opsi B.
- Alasan utama: repository membutuhkan identitas wilayah yang stabil dan tidak bergantung pada label tampilan.
- Kontrak yang dikunci:
  - `areas.code` adalah identitas wilayah stabil.
  - Pecalungan adalah 1 kecamatan canonical.
  - 10 desa canonical Pecalungan memiliki kode resmi:
    - `2001` Pecalungan
    - `2002` Bandung
    - `2003` Gombong
    - `2004` Randu
    - `2005` Siguci
    - `2006` Pretek
    - `2007` Selokarto
    - `2008` Gemuh
    - `2009` Gumawang
    - `2010` Keniten

## Dampak

- Dampak positif:
  - identitas wilayah menjadi stabil dan mudah diaudit,
  - seeder dan test dapat mengunci canonical wilayah,
  - dashboard, PDF, dan referensi domain bisa merujuk ke kode yang konsisten.
- Trade-off:
  - perlu migrasi schema dan backfill data existing,
  - perlu memastikan semua referensi internal tetap menggunakan `area_id` sebagai FK utama.
- Area terdampak (route/request/use case/repository/test/docs):
  - docs
  - database/migrations
  - database/seeders
  - tests
  - bila diperlukan, layer repository yang membaca metadata wilayah

## Validasi

- [ ] Targeted test concern.
- [ ] Regression test concern terkait.
- [ ] `php artisan test` (jika perubahan signifikan).

## Rollback/Fallback Plan

- Jika migrasi kode wilayah bermasalah, rollback migrasi dan pertahankan `name`, `level`, `parent_id` sebagai baseline sementara.
- Seeder harus tetap idempotent agar bisa dipakai untuk pemulihan data lokal.

## Referensi

- `README.md`
- `AGENTS.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/process/NORMALISASI_DATABASE_FORMAL_AUDIT_BASELINE_2026_03_10.md`
- `database/seeders/WilayahSeeder.php`
- `database/migrations/2026_02_11_172305_create_areas_table.php`

## Status Log

- 2026-06-05: `proposed` -> `accepted` | kode wilayah sudah diset user dan perlu dikunci sebagai standar canonical repository.
