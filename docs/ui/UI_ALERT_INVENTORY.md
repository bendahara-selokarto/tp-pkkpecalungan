# UI Alert Inventory

Tanggal audit:
- 2026-02-20

Scope:
- `resources/js/Pages`
- `resources/js/Layouts`
- `resources/js/admin-one`

## 1) Baseline Awal

Perintah:
- `rg "flashSuccess|flashError" resources/js/Pages -l`
- `rg "window\\.confirm" resources/js/Pages -l`
- `rg "window\\.alert|alert\\(" resources/js -l`
- `rg "border-emerald-200 bg-emerald-50|border-rose-200 bg-rose-50" resources/js/Pages -l`

Hasil baseline:
- `FLASH_FILES`: 47 file.
- `CONFIRM_FILES`: 47 file.
- `ALERT_FILES`: 1 file (`resources/js/admin-one/stores/main.js`).
- `INLINE_FLASH_BLOCK_FILES`: 49 file.

## 2) Eksekusi Batch 1 (U2-U4)

Perubahan yang sudah dieksekusi:
- Tambah komponen global flash berbasis admin-one:
  - `resources/js/admin-one/components/FlashMessageBar.vue`
- Integrasi global flash pada layout utama:
  - `resources/js/Layouts/DashboardLayout.vue`
- Migrasi halaman prioritas tinggi dari alert inline ke layout-level flash:
  - `resources/js/Pages/SuperAdmin/Users/Index.vue`
  - `resources/js/Pages/Desa/Activities/Index.vue`
  - `resources/js/Pages/Kecamatan/Activities/Index.vue`
  - `resources/js/Pages/Desa/AgendaSurat/Index.vue`
  - `resources/js/Pages/Kecamatan/AgendaSurat/Index.vue`
  - `resources/js/Pages/Desa/Inventaris/Index.vue`
  - `resources/js/Pages/Kecamatan/Inventaris/Index.vue`
  - `resources/js/Pages/Desa/Bantuan/Index.vue`
  - `resources/js/Pages/Kecamatan/Bantuan/Index.vue`

## 3) Status Setelah Batch 1

Hasil audit ulang:
- `FLASH_FILES_AFTER`: 38 file.
- `CONFIRM_FILES_AFTER`: 47 file.
- `ALERT_FILES_AFTER`: 1 file (`resources/js/admin-one/stores/main.js`).
- `INLINE_FLASH_BLOCK_FILES_AFTER`: 40 file.

Delta:
- `FLASH_FILES`: turun `47 -> 38` (`-9`).
- `INLINE_FLASH_BLOCK_FILES`: turun `49 -> 40` (`-9`).
- `CONFIRM_FILES`: belum turun (belum masuk scope migrasi modal konfirmasi).

## 4) Sisa Inventaris (Ringkas)

Kelompok file yang masih memakai flash inline:
- Seluruh `Index.vue` modul desa/kecamatan selain 9 halaman prioritas yang sudah dimigrasi.
- Halaman non-module:
  - `resources/js/Pages/Auth/Login.vue`
  - `resources/js/Pages/Profile/Edit.vue`

Kelompok file yang masih memakai native confirm:
- 47 halaman `Index.vue` modul desa/kecamatan + `super-admin/users`.

Native alert tersisa:
- `resources/js/admin-one/stores/main.js` (2 pemanggilan `alert(error.message)`).

## 5) Rekomendasi Batch Berikutnya

Prioritas batch 2:
1. Migrasi 38 halaman flash inline tersisa ke global flash.
2. Ganti native `window.confirm` ke modal konfirmasi reusable (`CardBoxModal` wrapper).
3. Refactor `alert(error.message)` di `resources/js/admin-one/stores/main.js` ke notifikasi UI.

## 6) Eksekusi Batch 2 (U5-U7)

Perubahan yang sudah dieksekusi:
- Tambah komponen konfirmasi reusable berbasis admin-one:
  - `resources/js/admin-one/components/ConfirmActionModal.vue`
- Upgrade komponen modal dasar agar label tombol batal bisa dikustom:
  - `resources/js/admin-one/components/CardBoxModal.vue`
- Migrasi seluruh halaman `Index.vue` modul desa/kecamatan/super-admin dari native `window.confirm` ke `ConfirmActionModal`:
  - total `47` halaman.
- Migrasi sisa halaman yang masih memakai `flashSuccess` inline ke global flash pada layout:
  - total `38` halaman.
- Hapus native alert di store:
  - `resources/js/admin-one/stores/main.js` (`alert(error.message)` -> `console.error(error.message)`).

## 7) Status Setelah Batch 2

Perintah audit:
- `rg "flashSuccess|flashError" resources/js/Pages -l`
- `rg "window\\.confirm" resources/js/Pages -l`
- `rg "window\\.alert|alert\\(" resources/js -l`
- `rg "border-emerald-200 bg-emerald-50|border-rose-200 bg-rose-50" resources/js/Pages -l`

Hasil akhir:
- `FLASH_FILES_FINAL`: `0` file.
- `CONFIRM_FILES_FINAL`: `0` file.
- `ALERT_FILES_FINAL`: `0` file.
- `INLINE_FLASH_BLOCK_FILES_FINAL`: `2` file.

Catatan sisa 2 file:
- `resources/js/Pages/Auth/Login.vue` (status login sukses, di luar dashboard/module index).
- `resources/js/Pages/Profile/Edit.vue` (blok konfirmasi hapus akun, flow profil).

Kesimpulan batch:
- Target utama audit alert dashboard/admin-one tercapai untuk seluruh halaman modul.
- Native dialog pada flow utama (`window.confirm`/`alert`) sudah dieliminasi.
