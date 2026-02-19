# Aplikasi PKK

Aplikasi ini digunakan untuk pengelolaan data PKK dengan arsitektur terstruktur dan konsisten lintas modul.

## Tujuan
- Menjaga konsistensi struktur kode antar modul.
- Memisahkan alur bisnis dari layer HTTP.
- Mempermudah maintenance dan pengujian.

## Stack
- Backend: Laravel 12
- Frontend: Inertia + Vue 3 (default), Blade untuk kebutuhan khusus seperti template PDF
- Build tool: Vite
- Styling: Tailwind CSS

## Prinsip Arsitektur
- Controller tipis.
- Alur bisnis ada di `UseCases`/`Actions`.
- Akses data domain melalui `Repositories`.
- Otorisasi melalui `Policies` yang mendelegasikan ke `Scope Service`.
- Akses data wilayah mengikuti scoped role + konteks area user.

## Ringkas Authorization Scope
- Scope domain aktif: `desa` dan `kecamatan`.
- Role scoped aktif:
  - `desa-sekretaris`, `desa-bendahara`, `desa-pokja-i`, `desa-pokja-ii`, `desa-pokja-iii`, `desa-pokja-iv`
  - `kecamatan-sekretaris`, `kecamatan-bendahara`, `kecamatan-pokja-i`, `kecamatan-pokja-ii`, `kecamatan-pokja-iii`, `kecamatan-pokja-iv`
- Compatibility role legacy masih didukung: `admin-desa`, `admin-kecamatan`.
- Guard route domain memakai middleware `scope.role:{desa|kecamatan}`.

## Status Implementasi UI
- `activities`, `inventaris`, `bantuans`, `anggota_pokja`, `super-admin/users`: Inertia Vue
- `profile`, `auth/verify-email`, `auth/confirm-password`: Inertia Vue
- Blade dipertahankan untuk use case khusus (contoh: `pdf/activity`)

## Konteks Dokumentasi
- Indeks konteks: `CONTEXT_INDEX.md`
- Arsitektur backend: `ARCHITECTURE.md`
- Standar database: `DATABASE_STANDARDS.md`
- Arsitektur UI: `UI_ARCHITECTURE.md`
- Protokol eksekusi agent: `CODEX_EXECUTION_PROTOCOL.md`
- Instruksi agen AI: `AGENTS.md`

## Konvensi Bahasa
- Istilah domain bisnis: Bahasa Indonesia.
- Istilah teknis implementasi: English.
- Kontrak teknis yang sudah dipakai di schema/API dipertahankan sampai ada refactor terencana.
- Nama function/method test ditulis dalam Bahasa Indonesia.

## Catatan Database
- Sumber wilayah canonical: tabel `areas`.
- Tabel `kecamatans`, `desas`, dan `user_assignments` adalah legacy compatibility.

