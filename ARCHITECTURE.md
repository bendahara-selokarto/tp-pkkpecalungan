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

## Aturan Default

1. Controller harus tipis.
2. Alur aplikasi ditulis di `UseCases`/`Actions`, bukan di controller.
3. Query data harus lewat `Repositories`.
4. Dependency repository harus memakai interface (`*RepositoryInterface`), bukan concrete class.
5. Authorization policy harus delegasi ke service/domain source of truth (contoh activity: `ActivityScopeService`).
6. Dilarang memakai service locator `app()` di service/use case/action. Gunakan constructor injection.
7. Query lintas modul tidak boleh bypass repository.
8. Gunakan Bahasa Indonesia untuk istilah domain, dan English untuk istilah teknis pada code artifact (class, method, layer, contract).
9. Khusus test, nama function/method test wajib menggunakan Bahasa Indonesia agar deskripsi skenario domain konsisten.

## Layering

Urutan default:

`HTTP Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`

Authorization:

`Policy -> Domain Scope Service`

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

## Definition of Done

Sebuah perubahan dianggap selesai jika:

1. Mengikuti aturan default di dokumen ini.
2. Tidak menambah dependency ke concrete repository pada layer aplikasi.
3. Tidak menambah service locator `app()` di layer aplikasi.
4. `php artisan test` PASS.


