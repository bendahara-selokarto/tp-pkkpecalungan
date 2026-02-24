# Audit Normalisasi Database 2026-02-24

## Tujuan Audit
- Memastikan kontrak canonical wilayah tetap normal dan tidak drift.
- Memastikan domain aktif tidak menambah coupling ke artefak legacy.

## Ruang Audit
- Skema migrasi domain aktif (`database/migrations`).
- Query scoped pada repository/service dashboard.
- Validasi otomatis melalui test suite penuh.

## Bukti Audit
- Pencarian referensi legacy aktif:
  - `rg -n "kecamatans|desas|user_assignments" app database routes tests`
  - Hasil: tidak ditemukan coupling read/write aktif baru ke artefak legacy.
- Audit foreign key canonical:
  - `rg -n "foreignId\\('area_id'\\)|foreignId\\('created_by'\\)" database/migrations`
  - Hasil: tabel domain aktif memakai relasi `area_id -> areas` dan `created_by -> users`.
- Audit guard scope:
  - `rg -n -F "hasRoleForScope" app`
  - Hasil: repository/service utama tetap mengunci akses dengan role-scope.
- Audit query level:
  - `rg -n -F "where('level'" app/Domains/Wilayah app/Services`
  - Hasil: query domain menjaga filter `level` dan `area_id` sesuai scope.
- Validasi eksekusi:
  - `php artisan test` -> lulus penuh (773 tests).

## Temuan
- Tidak ditemukan kebutuhan migrasi struktural darurat pada concern ini.
- Integritas canonical `areas` sebagai sumber wilayah tetap terjaga.
- Hardening dashboard terbaru memecah concern repository per grup tanpa menambah coupling legacy.

## Keputusan
- Concern normalisasi ditutup untuk batch ini dengan status aman.
- Patch migrasi ditunda karena tidak ada pelanggaran kritis yang memerlukan perubahan skema.
- Review berikutnya dipicu jika ada domain/menu baru atau perubahan relasi wilayah.

## Fallback Plan
- Jika ditemukan anomali data level-area pada siklus berikutnya:
  1. Tambah migration patch minimal (constraint/index) per tabel terdampak.
  2. Jalankan backfill terukur.
  3. Validasi ulang `php artisan test` + smoke role/scope.
