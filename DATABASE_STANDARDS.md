# Database Architecture Review & Standard

## Tujuan
Dokumen ini menetapkan standar desain database berdasarkan kondisi kode saat ini.
Fokus: konsistensi model wilayah, integritas relasi, dan performa query.

## Ringkasan Arsitektur Saat Ini
- `areas` adalah source-of-truth wilayah (hierarki `kecamatan -> desa` via `parent_id`).
- `users` terhubung ke wilayah melalui `area_id` + `scope`.
- Modul domain (`activities`, `inventaris`, `bantuans`, `anggota_pokjas`) memakai pola: `level`, `area_id`, `created_by`.
- Tabel legacy `kecamatans`, `desas`, `user_assignments` masih ada untuk kompatibilitas.

## Standar Wajib

### 1) Sumber Data Wilayah
- Fitur baru wajib memakai `areas`.
- Dilarang menambah dependency baru ke tabel legacy.
- Kompatibilitas legacy harus bersifat transisional, bukan source-of-truth kedua.

### 2) Konvensi Penamaan
- Domain term: Bahasa Indonesia.
- Technical term: English.
- Nama kolom/model/DTO/request harus konsisten dalam satu modul.

### 3) Relasi dan FK
- Semua relasi wajib FK eksplisit.
- Gunakan aksi delete yang jelas (`cascadeOnDelete`, `nullOnDelete`, atau `restrict` sesuai kebutuhan).
- Gunakan `foreignId()` untuk kolom relasi.

### 4) Aturan Kolom Scope
Setiap tabel domain wilayah wajib memiliki:
- `level` (`desa|kecamatan`)
- `area_id` (FK ke `areas.id`)
- `created_by` (FK ke `users.id`)

Guard aplikasi wajib memastikan:
- data `level=desa` hanya dikaitkan ke `areas.level=desa`
- data `level=kecamatan` hanya dikaitkan ke `areas.level=kecamatan`

### 5) Standar Index
Minimal untuk tabel domain wilayah:
- `index(['level', 'area_id'])`
- index tanggal bisnis bila dipakai filter/sort (contoh `activity_date`, `received_date`)

Jika query melakukan filter+sort, gunakan index komposit mengikuti urutan query nyata di repository.

### 6) Enum dan Konstanta Domain
- Hindari duplikasi literal enum lintas file.
- Disarankan migrasi bertahap ke enum/konstanta terpusat untuk request, DTO, policy/service, seeder.

### 7) Migration Governance
- Satu migration satu tujuan jelas.
- Wajib punya rollback valid.
- Untuk perubahan berisiko, gunakan rollout bertahap (add/backfill -> switch read path -> cleanup).

### 8) Seeder Governance
- Seeder harus idempotent (`firstOrCreate`/`updateOrCreate`).
- Seeder kompatibilitas wajib ditandai jelas sebagai legacy/transitional.

## Status Kepatuhan Saat Ini (Per 2026-02-18)

### Sudah Selaras
1. Modul domain utama sudah memiliki `level`, `area_id`, `created_by`.
2. FK utama dan index dasar `level+area_id` sudah tersedia.
3. Validasi manajemen user sudah memaksa kecocokan `scope` dengan `areas.level`.

### Masih Perlu Peningkatan
1. Belum ada constraint database langsung untuk memaksa kecocokan `record.level` dengan `areas.level` lintas tabel.
2. Enum literal masih tersebar di migration/request/UI.
3. Target deprecasi tabel legacy belum diformalisasi dalam milestone.

## Checklist PR Database
- [ ] Schema baru mengikuti source-of-truth `areas`.
- [ ] Naming kolom konsisten dengan model, DTO, request, seeder.
- [ ] FK + aksi delete sudah tepat.
- [ ] Index mengikuti query utama repository/use case.
- [ ] Tidak menambah enum literal duplikatif tanpa alasan.
- [ ] Ada test minimal untuk integritas relasi dan scope data.
- [ ] Jika menyentuh legacy, ada catatan kompatibilitas dan rencana deprecasi.



