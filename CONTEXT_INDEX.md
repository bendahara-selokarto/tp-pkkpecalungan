# Context Index

Dokumen ini adalah pintu masuk konteks proyek untuk developer dan AI agent.

## Baca Dulu
1. `ARCHITECTURE.md` (aturan layer backend)
2. `DATABASE_STANDARDS.md` (aturan schema, relasi, index, migration)
3. `UI_ARCHITECTURE.md` (stack UI, layout, dan konsistensi sidebar)

## Ringkasan Proyek
- Framework: Laravel 12
- UI: Blade + Inertia Vue 3
- Scope domain utama: data berbasis wilayah (`kecamatan` dan `desa`)
- Sumber wilayah canonical: tabel `areas`

## Aturan Integrasi Dokumen
- Perubahan backend architecture: update `ARCHITECTURE.md`.
- Perubahan schema database: update `DATABASE_STANDARDS.md`.
- Perubahan stack/layout UI: update `UI_ARCHITECTURE.md`.
- Perubahan konteks lintas dokumen: update file ini.

## Catatan Legacy
Tabel `kecamatans`, `desas`, dan `user_assignments` masih ada untuk kompatibilitas. Fitur baru harus memakai `areas`.
