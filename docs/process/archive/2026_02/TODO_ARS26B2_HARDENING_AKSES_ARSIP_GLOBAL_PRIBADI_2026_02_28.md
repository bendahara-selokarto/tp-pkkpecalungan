# TODO ARS26B2 Hardening Akses Arsip Global Pribadi 2026-02-28

Tanggal: 2026-02-28  
Status: `done`

## Konteks
- Kontrak akses arsip perlu dikunci agar tidak drift:
  - arsip yang diunggah `super-admin` bersifat global dan dapat dilihat semua role,
  - arsip yang diunggah user non `super-admin` dikelola oleh pengunggahnya sendiri,
  - `kecamatan-sekretaris` dapat memantau arsip user desa di wilayah kecamatannya.
- Mekanisme UI monitoring arsip kecamatan perlu disamakan dengan pola concern `activities` (dual-scope via toggle halaman, bukan menu sidebar langsung).

## Target Hasil
- [x] Jalur `/arsip` hanya menampilkan arsip global + arsip milik user login.
- [x] Mutasi arsip di jalur `/arsip` (`update/delete`) dipaksa owner-only.
- [x] Download arsip non-global tidak bisa dibypass oleh `super-admin` untuk arsip private user lain.
- [x] Toggle cakupan monitoring arsip di halaman `Arsip` hanya tampil untuk `kecamatan-sekretaris`.
- [x] Entry sidebar `Rekap Arsip Desa` disembunyikan agar pola UI selaras concern `activities`.
- [x] Monitoring backend `desa-arsip` tetap `read-only` melalui `moduleModes`.

## Langkah Eksekusi
- [x] Patch repository list arsip user untuk filter `is_global OR created_by = user_id`.
- [x] Tambah hard guard owner-only di controller jalur `/arsip` untuk `update/delete`.
- [x] Ubah use case download agar evaluasi akses memakai `ArsipDocumentPolicy` langsung (tanpa bypass `Gate::before`).
- [x] Sinkronkan UI `Arsip/Index` dan `DashboardLayout` terhadap kontrak dual-scope sekretaris kecamatan.
- [x] Tambah regression test untuk mencegah mutasi/unduh private arsip oleh `super-admin` pada jalur user.

## Validasi
- [x] `php artisan test tests/Feature/ArsipTest.php`
- [x] `php artisan test tests/Feature/KecamatanDesaArsipTest.php tests/Unit/Policies/ArsipDocumentPolicyTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/MenuVisibilityPayloadTest.php`
- [x] `php artisan test`

## Risiko
- Guard owner-only di jalur `/arsip` membuat mutasi lintas-user harus lewat concern management super-admin (`/super-admin/arsip`) untuk dokumen global.
- Jika ada endpoint baru terkait arsip yang memanggil gate global tanpa policy direct check, bypass `Gate::before` bisa muncul kembali.

## Keputusan
- [x] Kontrak akses arsip dikunci:
  - `global`: visible semua role,
  - `private`: owner manage-only,
  - `monitoring desa`: khusus jalur sekretaris kecamatan sesuai pola UI concern `activities`.
- [x] Pattern `P-020` (dual-scope kecamatan) direuse untuk concern arsip dan dicatat pada playbook.
