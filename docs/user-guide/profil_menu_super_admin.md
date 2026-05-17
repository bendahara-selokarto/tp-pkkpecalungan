# Profil Menu Pengguna: Peran Super Admin

Dokumen ini mencatat daftar menu dan hak akses untuk pengguna dengan peran **super-admin** dalam aplikasi TP-PKK Pecalungan.

## Informasi Dasar
- **Role**: `super-admin`
- **Level**: Sistem / Global
- **Fokus**: Manajemen Infrastruktur, Pengguna, dan Ijin Akses.

## Daftar Menu dan Hak Akses

### 1. Navigasi Utama (Halaman Manajemen Utama)
Super Admin diarahkan langsung ke halaman manajemen saat login:
| Menu | Deskripsi |
| :--- | :--- |
| **Manajemen User** | Pengelolaan akun seluruh kader (Desa & Kecamatan). |
| **Management Ijin Akses** | Pengaturan visibilitas modul dan fitur secara global. |
| **Management Arsip** | Pengawasan dan pengelolaan seluruh dokumen yang diunggah ke sistem. |

### 2. Menu Domain (Sidebar Supervisi)
Super Admin memiliki hak akses teknis ke seluruh grup modul guna keperluan troubleshooting atau supervisi:
- **Sekretaris PKK**: Hak Akses Read-Write.
- **Kelompok Pokja I s/d IV**: Hak Akses Read-Write.
- **Monitoring**: Hak Akses Read-Write.
- **Belum Ada Pemilik**: Hak Akses Read-Only.

### 3. Fitur Khusus
- **Rollback Ijin Akses**: Membatalkan perubahan ijin akses massal.
- **Audit Log**: (Jika diaktifkan) Memantau aktivitas teknis dalam sistem.

---
*Catatan: Peran Super Admin tidak menggunakan dashboard visual seperti kader desa/kecamatan, melainkan fokus pada tabel data manajemen.*

---
*Dicatat pada: 17 Mei 2026*
