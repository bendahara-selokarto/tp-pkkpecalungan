# UI Architecture

Dokumen ini menjelaskan stack UI aktif dan pola implementasi yang dipakai saat ini.

## Stack Aktif
- Server-side: Laravel 12
- Rendering utama:
  - Inertia + Vue 3 untuk halaman aplikasi (auth, profile, activities, inventaris, bantuan, anggota pokja, super-admin users)
  - Blade untuk kebutuhan non-interaktif/khusus (contoh: template PDF)
- Build tool: Vite
- Utility CSS: Tailwind CSS

## Entrypoint UI
- Frontend entry: `resources/js/app.js`
- Inertia pages: `resources/js/Pages/**/*.vue`
- Default layout Inertia:
  - Auth page: `resources/js/admin-one/layouts/LayoutGuest.vue`
  - Non-auth page: `resources/js/Layouts/DashboardLayout.vue`

## Layout Aktif
- Vue app layout: `resources/js/Layouts/DashboardLayout.vue`

Layout Vue menjaga pola sidebar:
- mobile: drawer (`sidebarOpen`)
- desktop: collapse (`sidebarCollapsed`)
- state collapse tersimpan di `localStorage` key `sidebar-collapsed`

## Navigasi Berbasis Scope dan Role
- Sidebar menampilkan menu domain berdasarkan `scope` user (`desa`/`kecamatan`) dan super admin tetap berbasis role `super-admin`.
- Guard akses utama tetap di backend (middleware scope-role + policy), UI hanya sebagai layer presentasi.

## Form Super Admin User Management
Pada `resources/js/Pages/SuperAdmin/Users/Create.vue` dan `resources/js/Pages/SuperAdmin/Users/Edit.vue`:
- pilihan role difilter berdasarkan `scope`
- role scope `desa`: `sekretaris`, `bendahara`, `pokja I`, `pokja II`, `pokja III`, `pokja IV`
- role scope `kecamatan`: `sekretaris`, `bendahara`, `pokja I`, `pokja II`, `pokja III`, `pokja IV`
- pilihan area difilter berdasarkan `areas.level == scope`
- nilai role/area di-reset otomatis jika tidak kompatibel dengan scope aktif

Ini menjaga sinkronisasi payload form dengan validasi backend.

## Standar Format Tanggal (Aktif)
- Semua input tanggal pada form aplikasi wajib menggunakan format `DD/MM/YYYY`.
- Field tanggal di UI menggunakan input teks terkontrol (bukan native `type="date"`) agar format konsisten lintas browser/locale.
- Contoh field domain yang mengikuti aturan ini:
  - `activity_date`
  - `received_date`
  - `tanggal_lahir`
- Untuk halaman edit, nilai prefill tanggal dari backend juga harus dikirim dalam format `DD/MM/YYYY`.

## Catatan Konsistensi
- Saat menambah halaman Vue baru, gunakan `DashboardLayout` secara default.
- Hindari menambah halaman Blade untuk flow aplikasi utama kecuali ada kebutuhan khusus (contoh: dokumen cetak/PDF).
- Hindari menambah layout ketiga tanpa alasan kuat.
- Istilah domain di UI gunakan Bahasa Indonesia.
- Istilah teknis UI gunakan English (contoh: `Dashboard`, `Sidebar`, `Layout`, `Auth`).
