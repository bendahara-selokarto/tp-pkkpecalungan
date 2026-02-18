# Aplikasi PKK

Aplikasi ini digunakan untuk pengelolaan data PKK dengan arsitektur yang terstruktur dan mudah dikembangkan.

## Tujuan
- Menjaga struktur kode tetap konsisten antar modul.
- Memisahkan alur bisnis dari layer HTTP.
- Mempermudah maintenance dan pengujian.

## Stack
- Backend: Laravel 12
- Frontend: Blade + Inertia + Vue 3
- Build tool: Vite
- Styling: Tailwind CSS

## Prinsip Arsitektur
- Controller tipis.
- Alur proses di `UseCases` atau `Actions`.
- Akses data melalui `Repositories`.
- Otorisasi melalui `Policies` + role/permission.

## Konteks Dokumentasi
- Indeks konteks: `CONTEXT_INDEX.md`
- Arsitektur backend: `ARCHITECTURE.md`
- Standar database: `DATABASE_STANDARDS.md`
- Arsitektur UI: `UI_ARCHITECTURE.md`
- Instruksi agen AI: `AGENTS.md`

## Catatan Database
- Sumber wilayah canonical: tabel `areas`.
- Tabel `kecamatans`, `desas`, dan `user_assignments` adalah legacy compatibility.
