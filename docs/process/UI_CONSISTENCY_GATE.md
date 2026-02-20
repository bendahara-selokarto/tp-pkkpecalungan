# UI Consistency Gate (Admin-One)

Tujuan:
- Menjaga implementasi UI feedback tetap konsisten dengan komponen `admin-one`.
- Mencegah regresi native dialog (`window.confirm`/`alert`) pada flow utama aplikasi.

## 1) Checklist Wajib Sebelum Merge

1. Daftar halaman terdampak dicantumkan pada deskripsi perubahan.
2. Bukti command audit dilampirkan (copy output ringkas).
3. Screenshot before/after untuk perubahan alert/konfirmasi disertakan.
4. `npm run build` lulus.
5. `php artisan test` lulus.

## 2) Command Audit Standar

Jalankan dari root project:

```bash
rg "flashSuccess|flashError" resources/js/Pages -l
rg "window\\.confirm|window\\.alert|\\balert\\(" resources/js -l
rg "border-emerald-200 bg-emerald-50|border-rose-200 bg-rose-50" resources/js/Pages -l
```

## 3) Kriteria Lulus

- `resources/js/Pages` tidak mengandung `flashSuccess|flashError` untuk modul dashboard.
- `resources/js` tidak mengandung native dialog (`window.confirm`, `window.alert`, `alert(`, `confirm(`) pada flow utama aplikasi.
- Alert feedback pada halaman dashboard menggunakan layout-level flash (`FlashMessageBar`) atau komponen reusable admin-one.

## 4) Pengecualian Saat Ini (Tercatat)

- Tidak ada pengecualian aktif.
