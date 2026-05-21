# TODO BKADM1X Eksekusi Implementasi Kategori Buku Administrasi

Tanggal: 2026-05-21  
Status: `in-progress`
Related ADR: `docs/adr/ADR_0011_COMMON_BOOK_VISIBILITY_PER_ROLE_GROUP.md`

## Konteks

- Implementasi klasifikasi buku administrasi standar sesuai `TODO_BKADM1`.
- Pengelompokan kategori menjadi `Buku Wajib`, `Buku Pembantu`, dan `Penunjang Buku Wajib` (khusus Sekretaris).
- `Buku Inventaris` menjadi `Buku Wajib` Sekretaris dan bukan lagi buku pembantu bersama.
- `Buku Pembantu Bersama` dibatasi hanya: `Buku Prestasi`, `Buku Bantuan`, dan `Buku Kader Khusus`.

## Kontrak Concern (Lock)

- Domain: Grouping menu dan visibilitas modul berbasis jabatan.
- Role/scope target: `sekretaris-tpk`, `bendahara-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, `pokja-iv` (scope `desa` & `kecamatan`).
- Boundary data: `RoleMenuVisibilityService` (backend) dan `printMenuRegistry.js` (frontend).
- Acceptance criteria:
  - Sidebar menampilkan kategori `Buku Wajib`, `Buku Pembantu`, dan `Penunjang Buku Wajib` sesuai role.
  - Modul `inventaris` masuk kategori wajib bagi Sekretaris.
  - Modul `prestasi-lomba`, `bantuans`, dan `kader-khusus` menjadi pembantu bersama.
  - Gap modul (tanpa slug) tidak ditampilkan atau diberi penanda.
- Dampak keputusan arsitektur: `ya` (perubahan kontrak visibilitas dan grouping).

## Target Hasil

- [x] Audit baseline slug modul aktual.
- [x] Refactor `RoleMenuVisibilityService.php` (backend groups & categories).
- [x] Refactor `printMenuRegistry.js` (frontend labels & grouping).
- [x] Sinkronisasi `bookGroupContextByMenuGroup` di frontend.
- [x] Test visibilitas per role lulus.

## Langkah Eksekusi

- [x] P0. Audit baseline slug modul (dilakukan pada tahap research).
- [x] P1. Update `RoleMenuVisibilityService.php`:
    - Sesuaikan `GROUP_MODULES` dengan matrix `BKADM1`.
    - Pastikan `inventaris` ada di `sekretaris-wajib`.
    - Pastikan `common-pembantu` hanya berisi 3 modul.
    - Tambahkan modul spesifik ke grup Pokja sesuai matrix.
- [x] P2. Update `printMenuRegistry.js`:
    - Gunakan label `Buku Bantu` (BB) sesuai pedoman terbaru.
    - Sesuaikan `rawGroups` items dengan matrix.
    - Pastikan `inventaris` ada di `Buku Wajib` untuk Sekretaris.
    - Sinkronkan `bookGroupContextByMenuGroup` dengan kunci grup baru jika ada perubahan.
- [x] P3. Validasi dengan test:
    - `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
    - `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`

## Validasi

- [x] L1: targeted test `RoleMenuVisibilityServiceTest.php`.
- [x] L2: `php artisan test --compact`.
- [x] L3: Verifikasi manual sidebar UI untuk role Sekretaris dan Pokja.

## Risiko

- Risiko 1: Perubahan nama group di backend tanpa update di frontend akan menyebabkan menu hilang.
- Risiko 2: Modul yang di-reuse lintas role (seperti `inventaris`) harus dipastikan isolasi datanya tetap bekerja via `book_group`.

## Keputusan

- [x] K1: Mengikuti matrix target `BKADM1` sebagai source of truth.
- [x] K2: `Buku Inventaris` adalah buku wajib Sekretaris.
- [x] K3: `Buku Pembantu Bersama` hanya 3 modul utama.

## Fallback Plan

- Jika visibilitas rusak, kembalikan `RoleMenuVisibilityService.php` dan `printMenuRegistry.js` ke state sebelumnya.

## Output Final

- [ ] Ringkasan perubahan kategori.
- [ ] Daftar file terdampak.
- [ ] Hasil validasi.
