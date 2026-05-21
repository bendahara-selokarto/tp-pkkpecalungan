# Pedoman Konfigurasi Akses Fitur (Permission Matrix)
## Aplikasi TP-PKK Kabupaten Batang

Dokumen ini adalah acuan resmi non-teknis untuk menentukan siapa (peran apa) yang boleh mengakses fitur apa di dalam aplikasi. Dokumen ini harus menjadi **titik awal (entry point)** bagi pengambil kebijakan jika terjadi perubahan struktur organisasi atau penambahan buku administrasi baru sesuai Rakernas X.

---

### 1. Prinsip Utama Visibilitas (Update 21-05-2026)

Untuk menjaga fokus operasional dan mencegah kesalahan input lintas bidang, aplikasi PKK menerapkan **Strict Role-Based Visibility** dengan aturan sebagai berikut:

1.  **Ownership-Only Visibility**: Pengguna hanya diperbolehkan melihat menu yang secara fungsional menjadi tanggung jawabnya (memiliki hak akses *Read-Write*).
2.  **Shared Module Isolation**: Untuk modul yang digunakan bersama (seperti *Buku Kegiatan* atau *Program Kerja*), sistem menggunakan parameter isolasi dan metadata `sourceKey`. Hal ini memastikan bahwa tautan menu milik bidang lain (misal: milik Sekretaris) tidak akan pernah muncul di sidebar bidang lain (misal: Pokja), meskipun jenis modulnya sama.
3.  **No Unowned Modules**: Seluruh fitur wajib memiliki pemilik definitif. Grup audit "Belum Ada Pemilik" telah dihapus dari antarmuka pengguna untuk memastikan seluruh data terkelola secara resmi oleh bidang terkait.
4.  **Super Admin**: Memiliki visibilitas mutlak ke seluruh modul untuk keperluan audit sistem.

---

### 2. Matriks Kepemilikan Definitif (Baseline V1.0)

Gunakan tabel ini sebagai acuan pembagian tanggung jawab input data:

| Nama Modul | Pemilik Utama (RW) | Grup Sidebar |
| :--- | :--- | :--- |
| Data Warga, Keluarga, Catatan Keluarga | Sekretariat | Buku Wajib Sekretaris |
| Buku Daftar Hadir, Buku Tamu, Agenda Surat | Sekretariat | Buku Wajib Sekretaris |
| Buku Inventaris | Sekretariat & Pokja III | Buku Wajib / Buku Bantu |
| Buku Keuangan | Bendahara | Buku Wajib |
| PAAR, Simulasi, Literasi, BKR, BKL | Pokja I | Buku Bantu Pokja I |
| Koperasi, UP2K, Pelatihan Kader, Taman Bacaan | Pokja II | Buku Bantu Pokja II |
| Industri Rumah Tangga, HATINYA PKK | Pokja III | Buku Bantu Pokja III |
| Posyandu, Pilot Project | Pokja IV | Buku Bantu Pokja IV |

---

### 3. Prosedur Perubahan (Entry Point)
Jika terdapat permintaan perubahan dari stakeholder (misal: Ketua Pokja IV meminta fitur baru "Laporan Stunting"), ikuti langkah berikut:

1.  **Identifikasi Modul**: Tentukan nama modul baru tersebut.
2.  **Tentukan Pemilik**: Pilih peran mana yang akan menginput data tersebut (misal: `pokja-iv`).
3.  **Update Dokumen Ini**: Tambahkan baris baru pada tabel di atas.
4.  **Instruksi Teknis**: Serahkan dokumen ini kepada pengembang untuk disinkronkan ke dalam `RoleMenuVisibilityService.php` (backend) dan `printMenuRegistry.js` (frontend).

---

### 4. Log Perubahan Non-Teknis
| Tanggal | Deskripsi Perubahan | Disetujui Oleh | Status |
| :--- | :--- | :--- | :--- |
| 21-05-2026 | Peresmian **Strict Visibility** & Granular Module Mapping | Tim Pengembang | **Aktif (Standard)** |
| 20-05-2026 | Peresmian Dokumen Acuan Awal | Tim Pengembang | Superseded |
