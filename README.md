# TP PKK Pecalungan - Human Guide

Dokumen ini adalah panduan manusia untuk memahami dan mengimplementasikan proyek.
Dokumen AI dan optimasi rate limiter ada di `AGENTS.md`.
Indeks dokumentasi ada di `docs/README.md`.

## 0. Referensi Domain Utama

- Pedoman domain utama (canonical aktif): `docs/referensi/Rakernas X.pdf`
- Ringkasan sinkronisasi lokal: `PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md`
- Jika ada ketidakkoherenan istilah/label domain, utamakan pedoman domain utama di atas.
- Aturan teknis implementasi tetap mengacu ke `AGENTS.md`.

## 0.1 Arsitektur Dokumentasi Eksekusi

- Rencana eksekusi lintas-file: `docs/process/TODO_*.md` (format `TODO <KODE_UNIK> ...`).
- Keputusan arsitektur lintas concern: `docs/adr/ADR_*.md`.
- TODO adalah jalur eksekusi, ADR adalah jejak keputusan + trade-off.

Generate TODO concern baru (standar):
```powershell
powershell -File scripts/generate_todo.ps1 -Code PGM26A1 -Title "Mitigasi Gap Pagination" -Date 2026-03-02 -RelatedAdr -
```

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
- `catatan-keluarga`
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

## 15. Runtime Evidence UI/UX (Playwright + Axe)

Baseline audit runtime UI/UX tersedia melalui Playwright (smoke) dan Axe (aksesibilitas).

Prasyarat:
- Dependensi terpasang:
  - `npm install`
  - `npm run test:e2e:install`
- Base URL aplikasi:
  - default: `http://127.0.0.1:8000`
  - override: set `E2E_BASE_URL`
- Opsional auto-start web server dari Playwright:
  - set `E2E_WEB_SERVER_COMMAND`, contoh:
    - `php artisan serve --host=127.0.0.1 --port=8000`

Kredensial login (opsional untuk smoke terautentikasi):
- `E2E_DESA_EMAIL`
- `E2E_DESA_PASSWORD`
- `E2E_KECAMATAN_EMAIL`
- `E2E_KECAMATAN_PASSWORD`
- `E2E_SUPERADMIN_EMAIL`
- `E2E_SUPERADMIN_PASSWORD`
- `E2E_REQUIRE_AUTH=1` untuk memaksa lane auth tidak boleh `skip`.
- `E2E_REQUIRE_AUTH_A11Y=1` untuk memaksa lane a11y terautentikasi (default env lokal tetap `0`; gate CI runtime-evidence menetapkan `1`).
- `E2E_A11Y_EXCLUDE_NPROGRESS=0` untuk ikut mengaudit elemen progress bar runtime (`#nprogress`).
- `E2E_A11Y_DISABLE_COLOR_CONTRAST=0` untuk mengaktifkan rule `color-contrast` pada scan Axe.

Provisioning akun deterministik untuk CI:
- Jalankan seeder `Database\\Seeders\\E2ERuntimeUserSeeder`.
- Nilai default seeder:
  - desa: `e2e.desa@pkk.local / password123`
  - kecamatan: `e2e.kecamatan@pkk.local / password123`
  - super-admin: `e2e.superadmin@pkk.local / password123`

Fallback kompatibilitas lama:
- `E2E_EMAIL` + `E2E_PASSWORD` tetap dibaca untuk role `super-admin` jika env khusus super-admin belum diisi.

Perintah:
- `npm run test:e2e`
- `npm run test:e2e:smoke`
- `npm run test:e2e:a11y`
- `npm run test:e2e:visual`
- `npm run test:e2e:perf`
- `npm run test:e2e:perf:summary`
- `npm run test:e2e:perf:trend`
- Audit a11y mendalam (PowerShell):
  - `$env:E2E_A11Y_EXCLUDE_NPROGRESS='0'; $env:E2E_A11Y_DISABLE_COLOR_CONTRAST='0'; npm run test:e2e:a11y`

Catatan:
- Test login page (`@smoke`, `@a11y`) selalu jalan tanpa kredensial.
- Test smoke/a11y terautentikasi per role otomatis `skip` jika pasangan kredensial role terkait belum diisi.
- CI runtime evidence menjalankan lane tambahan `a11y deep audit` (non-blocking) untuk mendeteksi potensi blind spot.
- Lane `@smoke` kini mencakup CRUD prioritas (`activities`, `agenda-surat`, `arsip`) pada `chromium-desktop`; project mobile untuk lane CRUD di-`skip` by design agar baseline tetap stabil.
- Lane `@visual` menyediakan baseline visual untuk `login`, `dashboard`, dan `super-admin/users` lintas project Playwright; dijalankan sebagai candidate gate non-blocking.
- Lane `@perf` menyediakan baseline performance budget (navigation timing + FCP) untuk `login`, `dashboard`, dan `super-admin/users` pada desktop; dijalankan sebagai candidate gate non-blocking.
- Ringkasan evidence performa disimpan ke `reports/ui-runtime/perf/{summary.json,summary.md,history/perf-history.jsonl}` untuk audit lintas run.
- Evaluasi tren performa menyimpan hasil ke `reports/ui-runtime/perf/{trend-evaluation.json,trend-evaluation.md}` dan mem-flag degradasi jika 3 run terakhir memburuk beruntun (>=15%).
- CI menyimpan history perf lintas run via cache; trend gate mode strict (`PERF_TREND_ENFORCE=1`) dieksekusi untuk `main` agar degradasi beruntun dapat memblokir merge ke baseline utama.
