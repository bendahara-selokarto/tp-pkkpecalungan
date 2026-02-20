# UI Admin-One Audit & Optimization Plan

Tujuan:
- Menstandarkan implementasi UI agar konsisten dengan komponen `admin-one`.
- Menghilangkan alert inline yang tidak memakai template `admin-one`.
- Menurunkan duplikasi style dan memperkecil drift visual lintas halaman.

Status eksekusi awal (2026-02-20):
- `U1` selesai (inventory awal + delta batch 1): `docs/ui/UI_ALERT_INVENTORY.md`.
- `U2` selesai (kontrak komponen feedback): `FlashMessageBar` berbasis `NotificationBar`.
- `U3` selesai (integrasi global layout): `DashboardLayout` sudah merender flash global.
- `U4` parsial (migrasi prioritas): 9 halaman utama sudah dipindahkan dari inline alert ke global flash.
- `U5` selesai (migrasi penuh flash inline): tersisa `0` file `flashSuccess|flashError` di `resources/js/Pages`.
- `U6` selesai (standardisasi konfirmasi): `window.confirm` di halaman modul menjadi `ConfirmActionModal`.
- `U7` selesai (regression guard): command audit menghasilkan `0` untuk pattern utama.
- `U8` selesai (change gate): checklist operasional ditulis di `docs/process/UI_CONSISTENCY_GATE.md`.

## 1) Temuan Baseline (Audit Cepat)

Temuan awal pada codebase saat ini:
- Pattern `flashSuccess|flashError` ditemukan luas di halaman `resources/js/Pages/*` (indikasi alert dibuat manual per halaman).
- Pemakaian `NotificationBar` pada folder `resources/js/Pages` belum ada.
- Masih ada penggunaan native browser dialog (`alert/confirm`) pada sebagian flow aksi.

Implikasi:
- UI feedback tidak konsisten antar halaman.
- Sulit menjaga quality visual karena style alert tersebar dan duplikatif.
- UX kurang terkontrol (native dialog vs komponen UI).

## 2) Scope Audit

Audit difokuskan pada:
1. `Flash/Alert feedback`: success, error, warning, info.
2. `Action confirmation`: hapus/update kritikal yang masih memakai native confirm.
3. `Layout consistency`: keseragaman pemakaian komponen `admin-one` untuk feedback global.
4. `Style duplication`: blok style alert hardcoded yang seharusnya diganti komponen reusable.

Out of scope tahap ini:
- Redesain tema besar.
- Perubahan domain backend.

## 3) Prinsip Standarisasi

Aturan target:
- Semua feedback pesan memakai komponen berbasis `admin-one` (`NotificationBar` atau wrapper-nya).
- Tidak ada `window.alert` di flow aplikasi.
- `window.confirm` diganti bertahap ke modal konfirmasi berbasis komponen `admin-one`.
- Flash message diprioritaskan tampil dari level layout agar halaman tidak mengulang blok yang sama.

## 4) Backlog Eksekusi (By Concern)

### U1 - Inventory & Mapping
- Bangun daftar halaman yang masih memakai flash inline dan native dialog.
- Kelompokkan per modul: `desa`, `kecamatan`, `super-admin`.
- Output: `docs/ui/UI_ALERT_INVENTORY.md`.

Acceptance:
- Semua halaman terdampak terdaftar (path + jenis masalah + prioritas).

### U2 - UI Contract Komponen Feedback
- Buat kontrak komponen `FlashMessageBar` di atas `NotificationBar`.
- Definisikan mapping level pesan:
  - `success -> success`
  - `error -> danger`
  - `warning -> warning`
  - `info -> info`
- Tentukan fallback message bila payload kosong.

Acceptance:
- Ada satu sumber kebenaran untuk rendering flash message.

### U3 - Integrasi Global Flash di Layout
- Integrasikan `FlashMessageBar` ke `resources/js/Layouts/DashboardLayout.vue`.
- Konsumsi `page.props.flash` sekali di layout, bukan di tiap halaman.

Acceptance:
- Halaman yang sudah migrasi tidak perlu lagi blok alert manual.

### U4 - Migrasi Halaman Prioritas Tinggi
- Migrasi modul dengan trafik tinggi dulu:
  - `super-admin/users`
  - `desa/activities`, `kecamatan/activities`
  - `agenda-surat`, `inventaris`, `bantuan`
- Hapus alert inline di halaman tersebut.

Acceptance:
- Alert tampil konsisten dengan template `admin-one`.

### U5 - Migrasi Bertahap Semua Modul
- Migrasi sisa halaman index/create/edit yang masih memakai flash inline.
- Lakukan per batch kecil agar aman direview.

Acceptance:
- Tidak ada alert inline hardcoded tersisa pada folder `resources/js/Pages`.

### U6 - Standardisasi Konfirmasi Aksi
- Buat komponen `ConfirmActionModal` berbasis `CardBoxModal` (admin-one).
- Ganti `window.confirm` pada aksi destructive.

Acceptance:
- Native confirm tidak dipakai lagi di flow utama.

### U7 - Regression Guard UI
- Tambah checklist linting/audit berbasis command:
  - `rg "flashSuccess|flashError" resources/js/Pages -n`
  - `rg "window\\.alert|window\\.confirm|alert\\(|confirm\\(" resources/js -n`
  - `rg "border-emerald-200 bg-emerald-50|border-rose-200 bg-rose-50" resources/js/Pages -n`
- Dokumentasikan output baseline dan target nol temuan untuk pattern terlarang.

Acceptance:
- Ada bukti audit yang repeatable sebelum merge.

### U8 - Documentation & Change Gate
- Tambahkan gate UI consistency ke dokumen proses.
- Wajibkan PR UI menyertakan:
  - daftar halaman terdampak,
  - bukti command audit,
  - screenshot before/after untuk feedback alert.

Acceptance:
- Standar tidak mudah regresi pada iterasi berikutnya.

## 5) Rencana Validasi

Validasi minimum tiap batch:
1. `npm run build` lulus.
2. `php artisan test` tetap hijau (anti side effect).
3. Smoke test manual:
   - create/update/delete sukses -> muncul success bar.
   - error validasi/server -> muncul danger bar.
   - aksi hapus -> modal konfirmasi tampil.

## 6) Definition of Done

Rencana dianggap selesai saat:
- Alert feedback lintas halaman konsisten dengan komponen `admin-one`.
- Tidak ada native `alert/confirm` pada flow utama.
- Tidak ada blok alert inline hardcoded di `resources/js/Pages`.
- Checklist audit UI terdokumentasi dan dijalankan pada proses merge.
