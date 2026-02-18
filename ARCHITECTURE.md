# Default Architecture

Dokumen ini menetapkan arsitektur default proyek. Semua fitur baru wajib mengikuti pola ini.

## Konteks Dokumen
- Mulai dari `CONTEXT_INDEX.md` untuk peta dokumentasi.
- Standar database: `DATABASE_STANDARDS.md`.
- Standar UI: `UI_ARCHITECTURE.md`.
- Instruksi agent: `AGENTS.md`.

## Tujuan
- Konsisten antar modul
- Mudah dirawat
- Mudah diuji
- Menghindari logic drift antar layer

## Layering Wajib
Urutan backend:

`HTTP Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`

Urutan authorization:

`Policy -> Domain Scope Service`

## Aturan Default
1. Controller harus tipis (hanya orchestration request/response).
2. Alur aplikasi ditulis di `UseCases`/`Actions`, bukan di controller.
3. Query data domain harus lewat `Repositories`.
4. Dependency repository di layer aplikasi wajib memakai interface (`*RepositoryInterface`).
5. Policy wajib delegasi ke scope service sebagai source of truth authorization.
6. Dilarang memakai service locator `app()` di service/use case/action; gunakan constructor injection.
7. Query lintas modul tidak boleh bypass repository.
8. Istilah domain bisnis memakai Bahasa Indonesia; istilah teknis memakai English.
9. Nama function/method test menggunakan Bahasa Indonesia.

## Aturan Authorization Scope (Aktif)
1. Akses data domain wilayah ditentukan oleh kombinasi:
- scoped role pengguna (matrix `scope -> role` via `RoleScopeMatrix`)
- level area pada `users.area_id` (via `areas.level`)
- kecocokan `area_id` terhadap data target
2. Pada modul existing, kolom `users.scope` diperlakukan sebagai metadata yang harus konsisten, tetapi authorization tidak bergantung ke kolom ini saja.
3. Tujuan aturan ini adalah menjaga kompatibilitas transisi data lama (contoh kasus `scope` belum sinkron) tanpa melonggarkan batas area.
4. Untuk endpoint list/create, policy `viewAny/create` tetap harus memverifikasi kelayakan konteks area user.

## Aturan Manajemen User (Aktif)
1. `role`, `scope`, dan `area_id` wajib konsisten.
2. `area_id` harus mengacu ke `areas.id` dengan `areas.level` yang sama dengan `scope`.
3. Mapping role-scope aktif:
- `scope=desa` -> `desa-sekretaris`, `desa-bendahara`, `desa-pokja-i`, `desa-pokja-ii`, `desa-pokja-iii`, `desa-pokja-iv`
- `scope=kecamatan` -> `kecamatan-sekretaris`, `kecamatan-bendahara`, `kecamatan-pokja-i`, `kecamatan-pokja-ii`, `kecamatan-pokja-iii`, `kecamatan-pokja-iv`
- `super-admin` -> `scope=kecamatan`
- role legacy `admin-desa` dan `admin-kecamatan` masih diterima untuk backward compatibility
4. Validasi ini wajib dijaga di request **dan** action (defensive validation).

## Struktur Minimal Modul Baru
Untuk modul baru, minimal sediakan:
- `Controllers/`
- `UseCases/` atau `Actions/`
- `Repositories/*RepositoryInterface.php`
- `Repositories/*Repository.php`
- `Models/`
- `Requests/` (jika ada input HTTP)
- `Policies/` (jika butuh authorization)
- `tests/Feature` dan `tests/Unit` yang relevan

## Known Debt (Per 2026-02-18)
1. Beberapa controller non-domain administratif masih melakukan query model langsung untuk listing/reference data.
2. Enum domain masih tersebar dalam string literal lintas layer.

## Definition of Done
Sebuah perubahan dianggap selesai jika:
1. Mengikuti aturan default di dokumen ini.
2. Tidak menambah dependency ke concrete repository pada layer aplikasi.
3. Tidak menambah service locator `app()` di layer aplikasi.
4. `php artisan test` PASS.
