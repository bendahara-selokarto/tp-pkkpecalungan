# TODO NDB26A1 Normalisasi Database
Tanggal: 2026-02-24  
Status: `done`

## Konteks
- Diperlukan concern khusus untuk menjaga normalisasi database agar konsisten lintas domain.
- Arsitektur canonical project menetapkan:
  - `areas` sebagai single source of truth wilayah.
  - Data domain wilayah wajib konsisten pada `level`, `area_id`, `created_by`.
  - Dilarang menambah coupling baru ke artefak legacy (`kecamatans`, `desas`, `user_assignments`).
- Status aplikasi masih pre-release, sehingga refactor skema terkontrol dan reset data development tetap memungkinkan.

## Target Hasil
- Tidak ada duplikasi atribut domain yang seharusnya direlasikan via foreign key.
- Kontrak `role/scope/area` dan `areas.level` tetap konsisten pada level skema dan data.
- Query domain tetap melewati repository boundary tanpa kebocoran data lintas scope.
- Tersedia guardrail validasi otomatis (test + checklist migrasi) untuk mencegah regresi normalisasi.

## Ruang Lingkup Concern
- Skema tabel domain wilayah aktif (BKL/BKR, dashboard aggregate source, dan domain turunan yang memakai `level` + `area_id`).
- Foreign key, unique index, dan integritas referensial lintas tabel utama.
- Backfill/cleanup data yang melanggar normal form atau kontrak canonical wilayah.
- Dokumentasi kontrak dan fallback plan saat migrasi perubahan struktur.

## Langkah Eksekusi
- [x] Audit cepat skema aktif:
  - cek tabel dengan potensi duplikasi atribut wilayah (nama desa/kecamatan tersimpan bersamaan dengan `area_id`).
  - cek kolom wajib domain wilayah (`level`, `area_id`, `created_by`) dan kesesuaiannya terhadap `areas.level`.
- [x] Identifikasi pelanggaran normalisasi per tabel:
  - atribut turunan/transitif yang seharusnya diturunkan dari relasi.
  - atribut multi-nilai dalam satu kolom.
  - dependensi parsial/inkonsisten pada kunci data.
- [x] Definisikan kontrak normalisasi target per concern:
  - field yang dipertahankan.
  - field yang dipindah ke relasi.
  - field legacy yang ditandai deprecated.
- [x] Susun patch migrasi minimal:
  - tambah/ketatkan foreign key dan index.
  - tambah constraint unik yang relevan.
  - siapkan skrip backfill untuk data eksisting.
- [x] Audit dampak implementasi:
  - request/use case/repository/policy.
  - seeder/factory.
  - report/PDF dan payload Inertia yang membaca field terdampak.
- [x] Definisikan fallback plan:
  - rollback migration.
  - fallback query sementara jika backfill belum lengkap.
  - langkah recovery saat ditemukan data anomali.
- [x] Validasi akhir:
  - jalankan `php artisan test`.
  - jika perubahan skema signifikan, jalankan `php artisan migrate:fresh --seed` pada environment development.

## Validasi
- [x] Tidak ada tabel domain wilayah aktif yang menyimpan duplikasi atribut wilayah tanpa alasan bisnis eksplisit.
- [x] Semua record domain wilayah lulus konsistensi `level` vs `areas.level`.
- [x] Tidak ada query lintas scope yang leak data karena relasi/constraint longgar.
- [x] Test feature + policy/scope service untuk concern terdampak tetap hijau.
- [x] Dokumen proses/kontrak yang terdampak sudah sinkron (tanpa drift istilah canonical).

## Artefak Audit
- `docs/process/NORMALISASI_DATABASE_AUDIT_2026_02_24.md`

## Risiko
- Pengetatan constraint dapat menggagalkan migrasi jika data historis belum bersih.
- Perubahan relasi dapat memengaruhi report/PDF yang masih mengakses field lama.
- Query dashboard aggregate berisiko berubah hasil bila data duplikat dibersihkan tanpa baseline pembanding.

## Keputusan
- [x] Concern normalisasi database ditetapkan sebagai concern terpisah agar bisa dieksekusi bertahap per domain.
- [x] Strategi eksekusi memakai patch minimal + backfill terukur, bukan rewrite besar.
- [x] Implementasi wajib menjaga authority akses di backend (`Policy -> Scope Service`) tanpa memindahkan otorisasi ke frontend.
