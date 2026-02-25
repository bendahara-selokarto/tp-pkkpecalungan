# TODO Audit Kepemilikan Modul dan Penempatan Role 2026-02-25

Tanggal: 2026-02-25  
Status: `planned`

## Konteks

- Diperlukan checklist menyeluruh untuk memetakan seluruh modul operasional yang ada di sistem.
- Checklist ini dipakai untuk audit: modul saat ini dimiliki role apa saja, level aksesnya (`read-write`/`read-only`), dan tindak lanjut jika ada salah penempatan role.
- Source of truth audit akses modul:
  - `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
  - `app/Http/Middleware/EnsureModuleVisibility.php`
  - `routes/web.php`

## Target Hasil

- Tersedia satu dokumen checklist operasional untuk audit penempatan role per modul.
- Setiap modul punya kolom aksi perbaikan role yang bisa diisi saat temuan audit muncul.
- Audit bisa dijalankan berulang tanpa ambigu (format tetap, kolom tetap).

## Definisi Kolom

- `Desa RW`: role yang saat ini punya hak tulis di scope desa.
- `Desa RO`: role yang saat ini hanya baca di scope desa.
- `Kecamatan RW`: role yang saat ini punya hak tulis di scope kecamatan.
- `Kecamatan RO`: role yang saat ini hanya baca di scope kecamatan.
- `Checklist Perbaikan Role`: centang jika ditemukan salah penempatan role.
- `Catatan Audit`: isi perubahan yang diminta (role yang harus ditambah/dicabut + alasan).

## Checklist Global Audit

- [ ] Validasi modul pada matrix sesuai modul route aktif di `routes/web.php`.
- [ ] Validasi mode akses tiap modul terhadap kontrak `RoleMenuVisibilityService`.
- [ ] Tandai modul yang salah penempatan role pada kolom `Checklist Perbaikan Role`.
- [ ] Isi `Catatan Audit` dengan usulan koreksi role yang eksplisit.
- [ ] Buat concern implementasi terpisah untuk setiap perubahan role yang disetujui.

## Matrix Modul x Role Saat Ini

| Modul | Grup | Desa RW | Desa RO | Kecamatan RW | Kecamatan RO | Checklist Perbaikan Role | Catatan Audit |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `activities` | pokja-i, pokja-ii, pokja-iii, pokja-iv, sekretaris-tpk | admin-desa, admin-kecamatan, desa-pokja-i, desa-pokja-ii, desa-pokja-iii, desa-pokja-iv, desa-sekretaris, kecamatan-pokja-i, kecamatan-pokja-ii, kecamatan-pokja-iii, kecamatan-pokja-iv, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-pokja-i, desa-pokja-ii, desa-pokja-iii, desa-pokja-iv, desa-sekretaris, kecamatan-pokja-i, kecamatan-pokja-ii, kecamatan-pokja-iii, kecamatan-pokja-iv, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `agenda-surat` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `anggota-pokja` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `anggota-tim-penggerak` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `anggota-tim-penggerak-kader` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `bantuans` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `bkl` | pokja-i | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `bkr` | pokja-i | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `buku-keuangan` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `catatan-keluarga` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `data-industri-rumah-tangga` | pokja-iii | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `data-kegiatan-warga` | pokja-i | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `data-keluarga` | pokja-iii | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `data-pelatihan-kader` | pokja-ii | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | pokja-iii | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `data-warga` | pokja-i | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `desa-activities` | monitoring | - | - | super-admin | admin-kecamatan, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `inventaris` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `kader-khusus` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `kejar-paket` | pokja-ii | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `koperasi` | pokja-ii | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `laporan-tahunan-pkk` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `paar` | pokja-i | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `pilot-project-keluarga-sehat` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `pilot-project-naskah-pelaporan` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `posyandu` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `prestasi-lomba` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `program-prioritas` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `simulasi-penyuluhan` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `taman-bacaan` | pokja-ii | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |
| `warung-pkk` | pokja-iii | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | [ ] Perlu koreksi | - |

## Modul Global (Non Scope Prefix `desa|kecamatan`)

| Modul | Route Utama | Role Saat Ini | Checklist Perbaikan Role | Catatan Audit |
| --- | --- | --- | --- | --- |
| `dashboard` | `/dashboard` | semua user login non super-admin (super-admin dialihkan ke `super-admin.users.index`) | [ ] Perlu koreksi | - |
| `profile` | `/profile` | semua user login | [ ] Perlu koreksi | - |
| `super-admin.users` | `/super-admin/users` | super-admin | [ ] Perlu koreksi | - |

## Risiko

- Matrix ini merepresentasikan kondisi saat ini; perubahan role selanjutnya harus memperbarui dokumen ini.
- Peran legacy (`admin-desa`, `admin-kecamatan`) masih muncul karena mode kompatibilitas.

## Keputusan

- [x] Checklist audit role-modul dikunci berbasis source of truth backend, bukan asumsi UI.
- [x] Kolom perbaikan role disiapkan agar bisa dipakai tim domain saat validasi penempatan akses.
