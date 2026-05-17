# TODO PDF26F1 Footer PDF Dinamis (Database Driven)

Tanggal: 2026-05-18  
Status: `done`  
Related ADR: `-`

## Konteks

- Setelah standarisasi header PDF (TODO PDF26H1), langkah selanjutnya adalah menormalisasi footer PDF agar data tidak lagi hardcoded atau hanya berbasis logic ad-hoc di Factory.
- Data footer (khususnya nama ketua dan jabatan) harus diambil dari database (`areas` table) agar fleksibel terhadap perubahan pejabat wilayah.

## Kontrak Concern (Lock)

- Domain: Output PDF Reports
- Role/scope target: Semua role (Desa & Kecamatan)
- Boundary data: `areas` table (chairperson_name, chairperson_role), `auth()->user()` metadata.
- Acceptance criteria:
  - Migrasi penambahan field di `areas` berhasil dijalankan.
  - `PdfViewFactory` mengambil data `footerChairpersonName` dan `footerChairpersonRole` dari tabel `areas`.
  - Tersedia fallback yang aman jika data di database kosong.
  - Seluruh laporan PDF yang menggunakan `_report_footer.blade.php` terupdate otomatis.
- Dampak keputusan arsitektur: `ya` (perubahan sumber data metadata footer)

## Target Hasil

- [x] Migrasi `2026_05_17_000000_add_chairperson_name_to_areas_table.php` dijalankan.
- [x] Penambahan field `chairperson_role` (optional/sebagai perluasan) atau standarisasi logic role berbasis database.
- [x] `PdfViewFactory` dimodifikasi untuk otomatisasi data footer dari database.
- [x] Unit test/Feature test memastikan footer berubah saat data area diubah.
- [x] Implementasi UI "Manajemen Wilayah" untuk Super Admin agar dapat menginput data ketua.

## Langkah Eksekusi

- [x] Menjalankan migrasi `php artisan migrate`.
- [x] Menjalankan seeder `AreaChairpersonSeeder`.
- [x] (Opsional) Menambahkan migrasi untuk `chairperson_role` jika diperlukan untuk kedinamisan penuh.
- [x] Refactor `PdfViewFactory::appendStandardMetadata` untuk lookup ke database.
- [x] Verifikasi visual/test pada modul pilot (Agenda Surat).
- [x] Membuat `AreaManagementController` dan UseCases terkait.
- [x] Membuat halaman Inertia `SuperAdmin/Areas/Index` dan `SuperAdmin/Areas/Edit`.
- [x] Menambahkan menu "Manajemen Wilayah" di Sidebar Super Admin.

## Validasi

- [x] L1: `php artisan test --filter=PdfViewFactoryTest`
- [x] L2: `php artisan test --filter=AreaManagementTest` (Feature test UI)
- [x] L3: Verifikasi manual/test render pada modul yang sudah menggunakan footer standar.

## Risiko

- Risiko 1: Data `chairperson_name` kosong di database menyebabkan footer tampil kosong atau `..........`.
- Risiko 2: N+1 query jika lookup area tidak di-eager load (Factory biasanya menangani per request, jadi aman).

## Keputusan

- [x] K1: Gunakan `areas.chairperson_name` sebagai source of truth utama untuk nama ketua.
- [x] K2: Standarisasi label role tetap di logic factory jika belum ada field dedicated, atau tambahkan field `chairperson_role` di `areas`.
- [x] K3: Modul Manajemen Wilayah hanya dapat diakses oleh Super Admin.

## Fallback Plan

- Jika lookup database gagal, gunakan placeholder `..........................` atau default config.
