# TODO ADR Mitigation 2026-06-01

## Deskripsi
Menyelesaikan inkoherensi antara ADR, Pedoman Akses, dan implementasi kode terkait visibilitas `inventaris` dan penamaan role.

## Status
- [x] Dokumentasi & Pelacakan
- [x] Perbaikan Visibilitas Modul `inventaris`
- [x] Standardisasi Penamaan Role (Hyphenation)
- [x] Migrasi Database
- [x] Verifikasi & Cleanup

## Rincian Tugas

### 1. Dokumentasi
- [x] Buat file TODO ini.

### 2. Visibilitas Modul `inventaris`
- [x] Edit `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`:
    - Hapus `'inventaris'` dari `pokja-i`.
    - Hapus `'inventaris'` dari `pokja-iv`.

### 3. Standardisasi Role
- [x] Edit `app/Support/RoleScopeMatrix.php`:
    - Ganti `admin_` menjadi `admin-` pada konstanta role administratif.
    - Sinkronisasi izin `inventaris` sesuai kepemilikan definitif (Sekretaris & Pokja III).

### 4. Migrasi
- [x] Buat migrasi untuk update tabel `roles`.
- [x] Jalankan migrasi.

### 5. Verifikasi
- [x] Jalankan test suite.
- [x] Cek visibilitas menu sidebar.
