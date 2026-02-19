# Context Index

Dokumen ini adalah pintu masuk konteks proyek untuk developer dan AI agent.

## Baca Dulu
1. `ARCHITECTURE.md` (aturan layer backend + authorization)
2. `DATABASE_STANDARDS.md` (aturan schema, relasi, index, migration)
3. `UI_ARCHITECTURE.md` (stack UI, layout, dan konsistensi scope-based navigation)
4. `README.md` (ringkasan proyek)
5. `CODEX_EXECUTION_PROTOCOL.md` (kontrak eksekusi agent + efisiensi konteks/token)

## Ringkasan Proyek (Kondisi Saat Ini)
- Framework: Laravel 12
- UI: Inertia + Vue 3 sebagai default, Blade tersisa untuk kebutuhan non-interaktif (contoh: template PDF)
- Domain utama: data berbasis wilayah (`kecamatan`, `desa`)
- Sumber wilayah canonical: tabel `areas`
- Pola backend aktif: `Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`
- Pola authorization aktif: `Policy -> Scope Service`
- Matrix role-scope aktif: role scoped `desa-*` dan `kecamatan-*` + legacy compatibility role

## Catatan Implementasi Penting
- Modul `activities`, `bantuans`, `inventaris`, `anggota_pokja`, dan `super-admin/users` sudah menggunakan Inertia Vue.
- Halaman `profile`, `auth/verify-email`, dan `auth/confirm-password` juga sudah menggunakan Inertia Vue.
- Validasi manajemen user sudah mewajibkan konsistensi `role`, `scope`, dan `area_id` (dengan kecocokan `areas.level`).
- Guard route domain wilayah memakai middleware `scope.role:{desa|kecamatan}`.

## Aturan Integrasi Dokumen
- Perubahan backend architecture: update `ARCHITECTURE.md`.
- Perubahan schema database: update `DATABASE_STANDARDS.md`.
- Perubahan stack/layout UI: update `UI_ARCHITECTURE.md`.
- Perubahan pola eksekusi agent: update `CODEX_EXECUTION_PROTOCOL.md`.
- Perubahan konteks lintas dokumen: update file ini.

## Konvensi Bahasa
- Istilah domain bisnis: Bahasa Indonesia (contoh: `kecamatan`, `desa`, `bantuan`, `anggota_pokja`).
- Istilah teknis: English (contoh: `Controller`, `UseCase`, `Repository`, `Request`, `Policy`, `scope`, `level`).
- Kontrak teknis yang sudah berjalan tidak diubah tanpa migration/refactor terencana.
- Nama function/method pada test menggunakan Bahasa Indonesia.

## Catatan Legacy
Tabel `kecamatans`, `desas`, dan `user_assignments` masih ada untuk kompatibilitas. Fitur baru wajib memakai `areas`.

