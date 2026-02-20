# TP PKK Pecalungan - Human Guide

Dokumen ini adalah panduan manusia untuk memahami dan mengimplementasikan proyek.
Dokumen AI dan optimasi rate limiter ada di `AGENTS.md`.

## 1. Tujuan Proyek

Aplikasi ini dipakai untuk manajemen data PKK berbasis wilayah.
Target utama:
- Konsistensi arsitektur lintas modul.
- Pemisahan business flow dari layer HTTP.
- Authorization yang ketat per scope wilayah.
- Perawatan dan pengujian yang mudah.

## 2. Stack Aktif

- Backend: Laravel 12
- UI utama: Inertia + Vue 3
- UI khusus: Blade (non-interaktif, misal PDF)
- Build tool: Vite
- Styling: Tailwind CSS

## 3. Arsitektur Backend

Layer wajib:

`Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`

Pola authorization wajib:

`Policy -> Scope Service`

Aturan implementasi:
- Controller harus tipis (orchestration request/response).
- Business flow di `UseCase/Action`, bukan di controller.
- Query domain melalui repository.
- Dependency repository di layer aplikasi harus via interface.
- Hindari service locator `app()` di use case/action/service.

## 4. Domain Wilayah dan Source of Truth

- Source wilayah canonical: tabel `areas`.
- Hierarki aktif: `kecamatan -> desa` dengan `parent_id`.
- Tabel `kecamatans`, `desas`, `user_assignments` dianggap legacy/transitional.
- Fitur baru tidak boleh menambah dependency ke tabel legacy.

## 5. Authorization dan Scope

Scope aktif:
- `desa`
- `kecamatan`

Role scoped aktif:
- `desa-sekretaris`, `desa-bendahara`, `desa-pokja-i`, `desa-pokja-ii`, `desa-pokja-iii`, `desa-pokja-iv`
- `kecamatan-sekretaris`, `kecamatan-bendahara`, `kecamatan-pokja-i`, `kecamatan-pokja-ii`, `kecamatan-pokja-iii`, `kecamatan-pokja-iv`

Role kompatibilitas legacy:
- `admin-desa`
- `admin-kecamatan`

Aturan penting:
- Source of truth authorization: `Policy -> Scope Service`.
- Guard route domain: middleware `scope.role:{desa|kecamatan}`.
- `role`, `scope`, `area_id` harus konsisten.
- `area_id` harus mengacu ke `areas.id` dengan `areas.level` yang cocok dengan scope.

## 6. Standar Database

Untuk tabel domain wilayah, kolom berikut wajib ada:
- `level` (`desa|kecamatan`)
- `area_id` (FK ke `areas.id`)
- `created_by` (FK ke `users.id`)

Standar wajib:
- Relasi penting harus FK eksplisit.
- Aksi delete harus jelas (`cascadeOnDelete`, `nullOnDelete`, `restrict`).
- Index minimal tabel domain wilayah: `index(['level', 'area_id'])`.
- Gunakan index tambahan sesuai pola query repository (filter/sort nyata).
- Seeder harus idempotent (`firstOrCreate` atau `updateOrCreate`).

Status saat ini:
- Struktur utama sudah mengikuti pola scope wilayah.
- Constraint DB untuk memaksa kecocokan `record.level` dan `areas.level` lintas tabel belum penuh.
- Enum literal masih tersebar lintas migration/request/UI (target refactor bertahap).

## 7. Standar UI

Entrypoint utama:
- `resources/js/app.js`
- Pages: `resources/js/Pages/**/*.vue`

Layout default:
- Auth: `resources/js/admin-one/layouts/LayoutGuest.vue`
- App: `resources/js/Layouts/DashboardLayout.vue`

Aturan UI:
- Sidebar menyesuaikan `scope` dan role user.
- Akses riil tetap ditentukan backend (middleware + policy).
- Hindari menambah Blade untuk flow aplikasi utama kecuali kebutuhan khusus.
- Kode domain/menu (contoh: `S1`, `L44`, `PRG`, `MON`) boleh disertakan pada dokumen resmi sebagai kode kecil di kanan atas area header.

Standar input tanggal:
- UI form domain memakai `DD/MM/YYYY`.
- Validasi dan normalisasi tanggal dilakukan di `FormRequest`.
- Format canonical backend: `Y-m-d`.
- Input tanggal dari UI wajib `DD/MM/YYYY` (bukan `MM/DD/YYYY`).

## 8. Modul Inertia Aktif

Sudah berbasis Inertia Vue:
- `kegiatan` (route teknis saat ini: `activities`)
- `inventaris`
- `bantuans`
- `anggota_pokja`
- `anggota-tim-penggerak`
- `kader_khusus`
- `prestasi-lomba`
- `bkl`
- `bkr`
- `koperasi`
- `data-warga`
- `data-kegiatan-warga`
- `data-keluarga`
- `data-industri-rumah-tangga`
- `data-pelatihan-kader`
- `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`
- `warung-pkk`
- `taman-bacaan`
- `kejar-paket`
- `posyandu`
- `simulasi-penyuluhan`
- `program-prioritas`
- `super-admin/users`
- `profile`
- `auth/verify-email`
- `auth/confirm-password`

Blade dipertahankan untuk use case khusus:
- Contoh: template PDF kegiatan.

## 9. Konvensi Bahasa

- Istilah domain bisnis: Bahasa Indonesia.
- Istilah teknis: English.
- Nama function/method test: Bahasa Indonesia.
- Kontrak teknis yang sudah berjalan tidak diubah tanpa refactor/migration terencana.

## 10. Definition of Done

Perubahan dianggap selesai jika:
- Mengikuti layering dan authorization pattern di dokumen ini.
- Tidak menambah bypass repository untuk query domain baru.
- Tidak menambah dependency baru ke tabel legacy.
- Untuk flow user/scope, tidak ada drift antara role, `scope`, dan `areas.level`.
- Test lulus (`php artisan test`).

## 11. Known Debt dan Arah Refactor

Known debt aktif:
- Sebagian controller administratif masih query model langsung.
- Enum domain belum sepenuhnya terpusat.
- Enforcement DB level-area match lintas tabel belum menyeluruh.

Arah refactor prioritas:
1. Sentralisasi enum domain.
2. Eliminasi query langsung controller administratif.
3. Tambah constraint DB untuk validasi level-area.
4. Formalisasi milestone deprecasi legacy table.

## 12. Standar Output PDF

- Default orientasi PDF adalah `landscape` untuk seluruh flow report/print.
- Orientasi `portrait` hanya dipakai jika diminta eksplisit pada flow terkait.
- Implementasi PDF baru wajib menggunakan helper terpusat agar konsisten lintas domain.

## 13. Playbook Modul/Menu Baru

Gunakan urutan ini agar implementasi konsisten:
1. Tetapkan kontrak domain: nama modul, scope aktif (`desa`/`kecamatan`), role yang diizinkan, dan boundary data.
2. Definisikan route + middleware: group route wajib pakai `scope.role:{desa|kecamatan}`.
3. Buat `FormRequest` untuk validasi + normalisasi input (termasuk tanggal `DD/MM/YYYY` ke `Y-m-d`).
4. Implement `UseCase/Action` untuk business flow, bukan di controller.
5. Implement `Repository Interface + Repository` untuk query domain.
6. Implement policy berbasis `Scope Service`.
7. Tambahkan halaman Inertia (Index/Create/Edit/Show sesuai kebutuhan) dan pakai data yang sudah dipetakan backend.
8. Tutup dengan test matrix minimum (lihat bagian 14).

Aturan implementasi penting:
- Untuk nilai scope/level di PHP, gunakan enum domain (`ScopeLevel`) dan hindari literal berulang.
- Data domain wilayah baru harus tetap menyimpan `level`, `area_id`, `created_by`.
- Flow create/update harus menjaga konsistensi `area_id` terhadap `areas.level`.
- Data akses UI (`auth.user.scope`) harus dianggap derived/effective dari backend, bukan authority di frontend.

## 14. Test Matrix Minimum (Modul Baru)

Minimal test yang wajib ada:
1. Feature test jalur sukses untuk role/scope yang valid.
2. Feature test jalur tolak untuk role tidak valid.
3. Feature test jalur tolak untuk mismatch role vs level area (simulasi data stale/legacy).
4. Unit test policy/scope service untuk `view` dan `update`/`delete`.
5. Jika query scoped kompleks, test repository/use case untuk memastikan data luar scope tidak bocor.
6. Jalankan `php artisan test` sebelum finalisasi.
