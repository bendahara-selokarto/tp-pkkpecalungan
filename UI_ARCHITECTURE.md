# UI Architecture

Dokumen ini menjelaskan stack UI yang aktif dan template yang dipakai saat ini.

## Stack Aktif

- Server-side: Laravel 12
- Hybrid rendering:
  - Blade (legacy / halaman yang belum dimigrasi)
  - Inertia + Vue 3 (halaman baru/refactor)
- Build tool: Vite
- Utility CSS: Tailwind CSS
- Interaktivitas Blade: Alpine.js

## Entrypoint UI

- Frontend entry: `resources/js/app.js`
- Inertia pages: `resources/js/Pages/**/*.vue`
- Default layout Inertia:
  - Auth page: `resources/js/admin-one/layouts/LayoutGuest.vue`
  - Non-auth page: `resources/js/Layouts/DashboardLayout.vue`

## Layout Yang Dipakai

- Blade app layout: `resources/views/layouts/app.blade.php`
- Vue app layout: `resources/js/Layouts/DashboardLayout.vue`

Keduanya memakai pola sidebar yang sama:

- mobile: drawer sidebar (`sidebarOpen`)
- desktop: collapsible sidebar (`sidebarCollapsed`)
- state collapse disimpan di `localStorage` key `sidebar-collapsed`

## Sidebar Desktop

Tombol desktop collapse/minimize tersedia di header layout:

- Blade: `resources/views/layouts/app.blade.php`
- Vue: `resources/js/Layouts/DashboardLayout.vue`

Label tombol dibuat eksplisit:

- `Minimize` saat sidebar lebar
- `Expand` saat sidebar dalam mode collapse

Pada template `admin-one` (`resources/js/admin-one/layouts/LayoutAuthenticated.vue`), tombol desktop juga tersedia untuk `Collapse/Expand sidebar` dengan state yang disimpan di `localStorage` key `admin-one-sidebar-collapsed`.

## Catatan Konsistensi

- Saat menambah halaman Vue baru, gunakan `DashboardLayout` secara default.
- Jika menambah halaman Blade, pastikan pola navigasi/role mengikuti layout app Blade.
- Hindari membuat layout ketiga tanpa alasan kuat; sinkronkan behavior sidebar agar konsisten.
- Gunakan Bahasa Indonesia untuk label domain bisnis di UI.
- Gunakan English untuk istilah teknis UI (contoh: `Dashboard`, `Sidebar`, `Layout`, `Auth`).

