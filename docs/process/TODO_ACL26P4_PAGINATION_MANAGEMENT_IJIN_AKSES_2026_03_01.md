# TODO ACL26P4 Pagination Management Ijin Akses 2026-03-01

Tanggal: 2026-03-01  
Status: `done`  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`  
Parent Concern: `docs/process/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`

## Konteks
- Matrix `Management Ijin Akses` terus bertambah baris seiring rollout modul pilot.
- Halaman sebelumnya menampilkan semua baris sekaligus sehingga observasi matrix sulit saat data besar.
- Diperlukan kontrak pagination backend + UI yang konsisten dengan pola pagination concern lain.

## Target Hasil
- [x] Endpoint index management ijin akses menerima query `page` dan `per_page` dengan whitelist aman.
- [x] Use case mengembalikan payload pagination (`page`, `per_page`, `total`, `last_page`, `from`, `to`).
- [x] UI memiliki kontrol `Baris per Halaman`, tombol `Sebelumnya/Berikutnya`, dan indikator halaman.
- [x] Filter `scope/role/mode` tetap berjalan, dengan reset halaman ke page 1 saat filter/per-page berubah.
- [x] Feature test concern akses-control memverifikasi metadata pagination dan guard out-of-range page.

## Flow Branch dan Commit (Maks 3)
1. Branch concern:
- [x] `feature/acl26p4-pagination-management-ijin-akses`

2. Commit 1 (backend contract pagination):
- [x] Validasi query `page/per_page` di controller.
- [x] Normalisasi `page/per_page` di use case + slicing row matrix.
- [x] Clamp page out-of-range agar metadata pagination konsisten.

3. Commit 2 (UI + test + doc):
- [x] Integrasi kontrol pagination pada halaman `SuperAdmin/AccessControl/Index`.
- [x] Update feature test read-only untuk kontrak pagination.
- [x] Sinkronisasi TODO concern parent + registry.

## Implementasi
- Backend:
  - `AccessControlManagementController@index` validasi `page/per_page` pakai whitelist concern.
  - `ListAccessControlMatrixUseCase` menambah konstanta `PER_PAGE_OPTIONS`, payload `pagination`, payload `perPageOptions`, dan clamp page.
- UI:
  - `resources/js/Pages/SuperAdmin/AccessControl/Index.vue` menambah:
    - state filter `page/per_page`,
    - query builder pagination,
    - selector baris per halaman,
    - tombol navigasi halaman.
- Test:
  - `AccessControlManagementReadOnlyTest` menambah assertion kontrak pagination (`filters`, `pagination`, `perPageOptions`) dan skenario page out-of-range.

## Validasi
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php`
- [x] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`
- [x] `php artisan test`

## Risiko Residual
- Urutan row matrix masih mengikuti urutan resolver runtime; perubahan urutan resolver dapat mempengaruhi distribusi data per halaman.
- Perubahan daftar role/scoped module dapat mengubah `last_page`, sehingga smoke test manual UI tetap diperlukan saat rollout modul baru.

## Keputusan
- [x] Pagination matrix menggunakan payload kustom concern (bukan Laravel paginator link array) karena sumber data berasal dari komposisi resolver, bukan query tunggal.
- [x] Authority akses tetap backend; UI pagination hanya mengatur representasi subset data yang sudah scoped.

## Fallback Plan
- Kembalikan `per_page` default ke 25 untuk seluruh request jika anomali ditemukan.
- Nonaktifkan kontrol pagination UI secara sementara (tetap aman karena backend default page 1 + per_page 25).
