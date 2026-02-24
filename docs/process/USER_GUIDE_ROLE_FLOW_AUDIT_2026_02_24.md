# User Guide Role Flow Audit (2026-02-24)

## Tujuan Audit
- Memastikan topik user guide selaras dengan flow sistem yang benar-benar aktif pada route dan visibilitas backend.
- Menjadi sumber rujukan untuk penulisan panduan per peran (`docs/user-guide/`).

## Sumber Audit
- `routes/web.php`
- `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- `app/Http/Middleware/EnsureModuleVisibility.php`
- `resources/js/Layouts/DashboardLayout.vue`

## Ringkasan Struktur Akses

### Scope dan Prefix Route
- Scope `desa`: prefix `/desa/*`, middleware `scope.role:desa` + `module.visibility`.
- Scope `kecamatan`: prefix `/kecamatan/*`, middleware `scope.role:kecamatan` + `module.visibility`.
- `super-admin`: prefix `/super-admin/*`, fokus utama pada manajemen user.

### Grup Menu Domain
- `Sekretaris TPK`: administrasi sekretariat dan operasional umum.
- `Pokja I` sampai `Pokja IV`: modul isian per bidang.
- `Monitoring Kecamatan`: khusus scope kecamatan (`/kecamatan/desa-activities`).
- `Referensi`: tautan pedoman, mode baca.

## Matrix Role ke Mode Akses (Hasil Audit)

| Role | Scope | Sekretaris TPK | Pokja I-IV | Monitoring Kecamatan |
| --- | --- | --- | --- | --- |
| `desa-sekretaris` | desa | read-write | read-only | tidak ada |
| `kecamatan-sekretaris` | kecamatan | read-write | read-only | read-only |
| `desa-pokja-i..iv` | desa | tidak ada | read-write (sesuai pokja) | tidak ada |
| `kecamatan-pokja-i..iv` | kecamatan | tidak ada | read-write (sesuai pokja) | tidak ada |
| `super-admin` | lintas | khusus halaman manajemen user | n/a | n/a |

Catatan:
- Role legacy `admin-*` masih ada di compatibility layer, tetapi bukan model akses utama user guide.

## Aturan Enforcement yang Mempengaruhi User Guide
- Akses modul ditentukan backend; menu UI hanya menampilkan yang diizinkan.
- Jika modul berstatus `read-only`, aksi mutasi ditolak backend:
  - request non-GET/HEAD/OPTIONS ditolak,
  - route `create`/`edit` ditolak.
- Pada UI, modul read-only diberi badge `RO` dan aksi mutasi disembunyikan.

## Implikasi untuk Struktur User Guide
- Panduan wajib dipisah per peran, bukan per tabel database.
- Untuk sekretaris, jelaskan dua mode kerja:
  - mengelola modul sekretaris (bisa ubah data),
  - memantau modul pokja (lihat data, tanpa ubah).
- Untuk pokja, fokus pada alur input, ubah, dan cetak laporan di modul pokja masing-masing.
- Untuk kecamatan sekretaris, tambahkan alur monitoring kegiatan desa.
- Untuk super-admin, fokus pada kelola user dan batasan role.

## Keputusan Audit
- Struktur user guide pada TODO `U4` dinyatakan valid dan dapat dipakai sebagai skeleton final tahap awal.
- Penulisan konten fase berikutnya harus mengikuti matrix akses di atas agar tidak terjadi mismatch panduan vs sistem.

