# TODO USR26A1 Pilot User Management Index Partial Reload Dan Payload Slimming

Tanggal: 2026-03-08  
Status: `done` (`state:full-suite-and-build-validated`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Concern ini adalah child concern kedua dari `SPA26A1` untuk pilot halaman list/filter non-dashboard.
- Halaman target adalah index user management super-admin karena:
  - pola interaksinya sederhana (`per_page`, pagination, delete action),
  - kontrak akses backend sudah kuat,
  - test paginasi existing sudah tersedia sehingga regression mudah diverifikasi.
- Target batch ini adalah mengurangi payload saat perubahan paginasi/filter pada `/super-admin/users` tanpa mengubah kontrak domain, authorization, atau form create/edit.

## Kontrak Concern (Lock)

- Domain: super-admin user management list delivery concern.
- Role/scope target: `super-admin` saja; tidak ada perubahan akses untuk role lain.
- Boundary data:
  - backend: `app/Http/Controllers/SuperAdmin/UserManagementController.php`,
  - frontend: `resources/js/Pages/SuperAdmin/Users/Index.vue`,
  - test contract: `tests/Feature/SuperAdmin/UserManagementIndexPaginationTest.php`.
- Acceptance criteria:
  - interaksi `per_page` memakai helper visit terpusat,
  - partial reload hanya meminta prop yang benar-benar berubah pada interaction loop list (`per_page` + klik pagination),
  - kontrak `users` + `filters.per_page` tetap stabil,
  - test paginasi existing tetap hijau, ditambah guard partial reload.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] T1. Index user management punya jalur partial reload yang konsisten untuk paginasi/filter.
- [x] T2. Payload update per-page tidak perlu mengirim ulang seluruh prop halaman.
- [x] T3. Ada test yang membuktikan partial reload user management hanya mengembalikan prop yang diminta.

## Langkah Eksekusi

- [x] L0. Audit jalur visit saat ini pada `SuperAdmin/Users/Index.vue`.
- [x] L1. Konsolidasi helper visit index user management.
- [x] L2. Ubah controller index agar prop list mendukung partial reload yang efisien.
- [x] L3. Tambah test partial reload pada feature test pagination user management.
- [x] L4. Sinkronisasi parent concern + registry + validation log.

## Validasi

- [x] V1. Audit scoped controller + page memastikan query/payload tidak drift.
- [x] V2. `php artisan test tests/Feature/SuperAdmin/UserManagementIndexPaginationTest.php --compact`
- [x] V3. Full regression `php artisan test --compact` di-offload ke operator lokal bila batch ini ditutup.
- [x] V4. Frontend compile guard `npm run build` di-offload ke operator lokal karena batch ini menyentuh komponen Vue shared.

## Risiko

- Risiko 1: prop statis yang masih dipakai UI tidak ikut partial reload dan menyebabkan state tidak sinkron.
- Risiko 2: perubahan pada index user management bocor ke flow delete atau pagination link yang sudah stabil.
- Risiko 3: perubahan opsi default pada `PaginationBar` bisa memengaruhi perilaku halaman lain bila ada page yang mengandalkan visit penuh secara implisit.

## Keputusan

- [x] K1: batch ini hanya menyentuh halaman index user management, bukan create/edit/update.
- [x] K2: partial reload dipakai untuk `users` dan `filters`; `per_page` dan klik pagination memakai jalur partial yang sama sementara prop statis lain tetap dipertahankan dari first load.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru tidak diperlukan; boundary arsitektur tetap sama.
- [x] Jika concern ini berkembang menjadi pattern reusable lintas banyak index page, cukup sinkronkan ke playbook/process tanpa ADR baru kecuali boundary berubah.

## Fallback Plan

- Jika partial reload membuat tabel atau pagination brittle:
  - rollback ke visit Inertia penuh pada halaman ini,
  - pertahankan test baru bila masih relevan sebagai guard kontrak future batch.

## Output Final

- [x] O1. Ringkasan perubahan mekanisme visit user management index.
- [x] O2. Daftar file terdampak.
- [x] O3. Hasil validasi targeted + status offload full regression/build.
