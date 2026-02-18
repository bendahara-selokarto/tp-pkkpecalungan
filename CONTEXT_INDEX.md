# Context Index

Dokumen ini adalah pintu masuk konteks proyek untuk developer dan AI agent.

## Baca Dulu
1. `ARCHITECTURE.md` (aturan layer backend + authorization)
2. `DATABASE_STANDARDS.md` (aturan schema, relasi, index, migration)
3. `UI_ARCHITECTURE.md` (stack UI, layout, dan konsistensi role-based navigation)
4. `README.md` (ringkasan proyek)

## Ringkasan Proyek (Kondisi Saat Ini)
- Framework: Laravel 12
- UI: hybrid Blade + Inertia Vue 3
- Domain utama: data berbasis wilayah (`kecamatan`, `desa`)
- Sumber wilayah canonical: tabel `areas`
- Pola backend aktif: `Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`
- Pola authorization aktif: `Policy -> Scope Service`

## Catatan Implementasi Penting
- Modul `activities` masih dominan Blade.
- Modul `bantuans`, `inventaris`, `anggota_pokja`, dan `super-admin/users` menggunakan Inertia Vue.
- Validasi manajemen user sudah mewajibkan konsistensi `role`, `scope`, dan `area_id` (dengan kecocokan `areas.level`).

## Aturan Integrasi Dokumen
- Perubahan backend architecture: update `ARCHITECTURE.md`.
- Perubahan schema database: update `DATABASE_STANDARDS.md`.
- Perubahan stack/layout UI: update `UI_ARCHITECTURE.md`.
- Perubahan konteks lintas dokumen: update file ini.

## Konvensi Bahasa
- Istilah domain bisnis: Bahasa Indonesia (contoh: `kecamatan`, `desa`, `bantuan`, `anggota_pokja`).
- Istilah teknis: English (contoh: `Controller`, `UseCase`, `Repository`, `Request`, `Policy`, `scope`, `level`).
- Kontrak teknis yang sudah berjalan tidak diubah tanpa migration/refactor terencana.
- Nama function/method pada test menggunakan Bahasa Indonesia.

## Catatan Legacy
Tabel `kecamatans`, `desas`, dan `user_assignments` masih ada untuk kompatibilitas. Fitur baru wajib memakai `areas`.


