# UI Architecture

Dokumen ini menjelaskan stack UI aktif dan pola implementasi yang dipakai saat ini.

## Stack Aktif
- Server-side: Laravel 12
- Hybrid rendering:
  - Blade (masih dipakai untuk modul activities)
  - Inertia + Vue 3 (super-admin users + modul inventaris/bantuan/anggota pokja)
- Build tool: Vite
- Utility CSS: Tailwind CSS
- Interaktivitas Blade: Alpine.js

## Entrypoint UI
- Frontend entry: `resources/js/app.js`
- Inertia pages: `resources/js/Pages/**/*.vue`
- Default layout Inertia:
  - Auth page: `resources/js/admin-one/layouts/LayoutGuest.vue`
  - Non-auth page: `resources/js/Layouts/DashboardLayout.vue`

## Layout Aktif
- Blade app layout: `resources/views/layouts/app.blade.php`
- Vue app layout: `resources/js/Layouts/DashboardLayout.vue`

Keduanya menjaga pola sidebar serupa:
- mobile: drawer (`sidebarOpen`)
- desktop: collapse (`sidebarCollapsed`)
- state collapse tersimpan di `localStorage` key `sidebar-collapsed`

## Navigasi Berbasis Role
- Sidebar menampilkan menu berdasarkan role (`super-admin`, `admin-kecamatan`, `admin-desa`).
- Guard akses utama tetap di backend (middleware role + policy), UI hanya sebagai layer presentasi.

## Form Super Admin User Management
Pada `resources/js/Pages/SuperAdmin/Users/Create.vue` dan `resources/js/Pages/SuperAdmin/Users/Edit.vue`:
- pilihan role difilter berdasarkan `scope`
- pilihan area difilter berdasarkan `areas.level == scope`
- nilai role/area di-reset otomatis jika tidak kompatibel dengan scope aktif

Ini menjaga sinkronisasi payload form dengan validasi backend.

## Catatan Konsistensi
- Saat menambah halaman Vue baru, gunakan `DashboardLayout` secara default.
- Jika menambah halaman Blade, ikuti pola role-based navigation pada layout Blade.
- Hindari menambah layout ketiga tanpa alasan kuat.
- Istilah domain di UI gunakan Bahasa Indonesia.
- Istilah teknis UI gunakan English (contoh: `Dashboard`, `Sidebar`, `Layout`, `Auth`).

