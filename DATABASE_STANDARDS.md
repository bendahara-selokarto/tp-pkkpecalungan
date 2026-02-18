# Database Architecture Review & Standard

## Tujuan
Dokumen ini menetapkan standar desain database untuk proyek ini, berdasarkan kondisi kode saat ini.
Fokusnya: konsistensi model data wilayah, integritas relasi, dan performa query laporan.

## Ringkasan Arsitektur Saat Ini
- `areas` adalah struktur wilayah canonical baru (hierarki `kecamatan -> desa` via `parent_id`).
- `users` terhubung ke wilayah melalui `area_id` + `scope`.
- Modul domain (`activities`, `inventaris`, `bantuans`, `anggota_pokjas`) sudah memakai pola seragam: `level`, `area_id`, `created_by`.
- Tabel legacy `kecamatans`, `desas`, dan `user_assignments` masih ada dan tetap di-seed untuk kompatibilitas lama.

## Hasil Review (Prioritas)

### High
1. Kebijakan bahasa penamaan belum didefinisikan eksplisit lintas dokumen.
   - Domain term di proyek cenderung Bahasa Indonesia (`kecamatan`, `desa`, `bantuan`, `anggota_pokja`).
   - Technical term cenderung English (`Controller`, `UseCase`, `Repository`, `scope`, `level`).
   - Dampak: tanpa aturan tertulis, naming drift mudah terjadi saat modul baru ditambahkan.

2. Dual source-of-truth wilayah belum dipisah tegas.
   - `areas` dipakai modul aktif, tetapi `kecamatans/desas` masih ditulis oleh seeder (`database/seeders/WilayahSeeder.php`).
   - Dampak: risiko drift data antar skema baru dan legacy.

### Medium
1. Integritas semantik `level` dan `area_id` belum dipaksa di level database.
   - Contoh invalid yang masih mungkin: record `level = desa` tetapi `area_id` menunjuk area level kecamatan.

2. Index komposit belum sepenuhnya mengikuti pola query lintas modul.
   - Query sering memakai kombinasi `level + area_id + tanggal/id`.
   - Beberapa tabel sudah indexed, tetapi belum seragam untuk pola sorting/filter yang sama.

3. Enum literal tersebar di banyak migration/model.
   - Risiko typo dan inkonsistensi value antar modul meningkat seiring skala fitur.

## Standar Wajib (Mulai Sekarang)

### 1) Sumber Data Wilayah
- `areas` ditetapkan sebagai satu-satunya sumber data wilayah untuk fitur baru.
- Tabel `kecamatans/desas/user_assignments` statusnya `legacy`.
- Semua modul baru dilarang menambah dependency ke tabel legacy.
- Jika masih butuh compatibility, lakukan sinkronisasi satu arah: `areas -> legacy`, bukan dua arah.

### 2) Konvensi Penamaan
- Istilah domain bisnis: gunakan Bahasa Indonesia (contoh: `kecamatan`, `desa`, `bantuan`, `anggota_pokja`, `nama`, `jabatan`).
- Istilah teknis: gunakan English (contoh: `Controller`, `UseCase`, `Repository`, `Request`, `Policy`, `scope`, `level`).
- Nama kolom/model/DTO/request harus konsisten dalam satu modul; dilarang alias ganda untuk makna yang sama.
- Kontrak schema yang sudah berjalan (mis. `areas.name`) dipertahankan sampai ada migration/refactor terencana.

### 3) Relasi dan Foreign Key
- Semua FK wajib eksplisit dan memiliki aksi delete yang jelas:
  - `cascadeOnDelete()` untuk data turunan yang tidak boleh yatim.
  - `nullOnDelete()` hanya untuk relasi opsional yang memang boleh hilang referensinya.
- Kolom relasi wajib `unsignedBigInteger` melalui `foreignId()`.

### 4) Aturan Kolom Scope
- Setiap tabel data domain berbasis wilayah wajib memiliki:
  - `level` (`desa|kecamatan`)
  - `area_id` (FK ke `areas.id`)
  - `created_by` (FK ke `users.id`)
- Tambahkan guard aplikasi (policy/service/request) untuk memastikan:
  - `level=desa` hanya boleh memakai `areas.level=desa`.
  - `level=kecamatan` hanya boleh memakai `areas.level=kecamatan`.

### 5) Standar Index
- Minimal index default untuk tabel domain wilayah:
  - `index(['level', 'area_id'])`
  - index tanggal bisnis utama (mis. `activity_date`, `received_date`) jika dipakai filtering/sort laporan.
- Jika query utama melakukan filter + sort, gunakan index komposit sesuai urutan query.
- Evaluasi index harus mengacu query nyata repository/use case, bukan asumsi.

### 6) Enum dan Konstanta Domain
- Hindari hardcoded enum string berulang di banyak file.
- Definisikan konstanta/domain enum terpusat (PHP enum/constant class), dipakai lintas:
  - Request validation
  - DTO
  - Policy/Service
  - Seeder

### 7) Migration Governance
- Satu migration = satu tujuan jelas.
- Setiap perubahan schema wajib menyertakan:
  - Dampak backward compatibility
  - Strategi migrasi data (jika rename/split/merge kolom)
  - Rollback yang valid
- Untuk perubahan berisiko, lakukan rollout 2 tahap:
  - Tahap 1: add kolom/struktur baru + backfill
  - Tahap 2: switch read path
  - Tahap 3: drop struktur lama

### 8) Seeder Governance
- Seeder tidak boleh menciptakan konflik antar skema canonical vs legacy.
- Seeder compatibility wajib diberi label jelas (`legacy`, `temporary`, atau `deprecation target`).
- Gunakan idempotent operation (`firstOrCreate`/`updateOrCreate`) dan kunci unik yang stabil.

## Checklist PR Database
- [ ] Schema baru mengikuti source-of-truth `areas`.
- [ ] Naming kolom konsisten dengan model, DTO, request, seeder.
- [ ] FK dan aksi delete sudah tepat (`cascade`, `restrict`, atau `set null`).
- [ ] Index sesuai query repository/use case utama.
- [ ] Tidak ada enum literal duplikatif yang seharusnya jadi konstanta terpusat.
- [ ] Ada test minimal untuk integritas relasi dan scope data.
- [ ] Jika menyentuh legacy, ada catatan kompatibilitas dan rencana deprecasi.

## Rekomendasi Tindak Lanjut Cepat
1. Tetapkan dan terapkan aturan bahasa: domain = Bahasa Indonesia, technical term = English.
2. Tetapkan status resmi tabel legacy dan target deprecasinya.
3. Tambahkan validasi lintas tabel untuk kecocokan `level` terhadap `areas.level`.
4. Audit index di semua tabel domain berdasarkan query paling sering dipakai.



