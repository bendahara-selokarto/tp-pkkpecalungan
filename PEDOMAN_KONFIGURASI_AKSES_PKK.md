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

| Bidang | Kategori Buku | Modul / Fitur |
| :--- | :--- | :--- |
| **Sekretariat** | [Buku Wajib] | Daftar Anggota TP-PKK, Buku Notulen, Buku Kegiatan, Buku Inventaris, Agenda Surat |
| | [Buku Penunjang] | Buku Program Kerja, Buku Data Umum (Warga/Keluarga) |
| | [Buku Bantu] | Daftar Hadir, Buku Prestasi, Buku Tamu, Buku Konsultasi, Buku Ekspedisi, Buku Bantuan, Buku Grafik, Buku Agenda SK |
| **Bendahara** | [Buku Wajib] | Buku Keuangan |
| **Pokja I** | [Buku Bantu] | PAAR, Simulasi, Literasi, BKR, BKL |
| **Pokja II** | [Buku Bantu] | Koperasi, UP2K, Pelatihan Kader, Taman Bacaan |
| **Pokja III** | [Buku Bantu] | Industri Rumah Tangga, HATINYA PKK, Inventaris |
| **Pokja IV** | [Buku Bantu] | Posyandu, Pilot Project |

---

### 3. Prosedur Perubahan (Entry Point)
Jika terdapat permintaan perubahan dari stakeholder (misal: Ketua Pokja IV meminta fitur baru "Laporan Stunting"), ikuti langkah berikut:

1.  **Identifikasi Modul**: Tentukan nama modul baru tersebut.
2.  **Tentukan Pemilik**: Pilih peran mana yang akan menginput data tersebut (misal: `pokja-iv`).
3.  **Update Dokumen Ini**: Tambahkan baris baru pada tabel di atas.
4.  **Instruksi Teknis**: Serahkan dokumen ini kepada pengembang untuk disinkronkan ke dalam `RoleMenuVisibilityService.php` (backend) dan `printMenuRegistry.js` (frontend).

---

### 4. Kontrak Menu Spesifik per Level (Mirror Scope Architecture)

Aplikasi PKK membedakan kontrak menu antara level Kecamatan dan Desa untuk menjaga stabilitas birokrasi di tingkat Kecamatan sambil memungkinkan fleksibilitas pendataan di tingkat Desa.

#### A. Level Kecamatan (Fixed Baseline V1.0)
Menu level Kecamatan dinyatakan **FIXED** dan tidak menerima modul tambahan di luar fungsi koordinasi. Seluruh Pokja di Kecamatan hanya mengelola modul yang bersifat agregat atau administratif umum.
- **Modul Utama**: `Program Prioritas`, `Buku Kegiatan`, `Data Kegiatan Pokja (Agregat)`.
- **Modul Bantu**: `Anggota TP-PKK`, `Kader Khusus`, `Inventaris`, `Prestasi Lomba`, `Bantuan`.

#### B. Level Desa (Extended Operational)
Menu level Desa bersifat **EXTENDED** untuk mengakomodasi titik masuk data (entry point) yang dibutuhkan untuk laporan Buku Data Kegiatan (Lampiran 4.21 - 4.24).
- **Pokja I (Desa)**: + `Literasi Warga`.
- **Pokja II (Desa)**: + `Koperasi`, `Kejar Paket`, `Taman Bacaan`.
- **Pokja IV (Desa)**: + `BKB Kegiatan`.
- **Umum (Desa)**: + `Anggota Pokja` (untuk setiap Pokja).

---

### 5. Flow Perbaikan & Sinkronisasi (E2E)

Setiap kali terjadi perubahan hak akses atau struktur menu, wajib mengikuti alur sinkronisasi berikut untuk mencegah error 403 atau menu tidak tampil:

1.  **Level Kebijakan (`PEDOMAN_KONFIGURASI_AKSES_PKK.md`)**:
    *   Tentukan kategori (Wajib/Penunjang/Bantu) dan Pemilik Utama.
2.  **Level Backend (`RoleMenuVisibilityService.php`)**:
    *   **Mapping Grup**: Daftarkan modul ke konstanta `GROUP_MODULES`.
    *   **Mapping Scope**: Daftarkan grup ke konstanta `GROUPS_BY_SCOPE` (Sering terlewat: Pastikan grup ada di scope `desa` DAN `kecamatan`).
    *   **Mapping Role**: Tentukan mode akses di konstanta `ROLE_GROUP_MODES`.
3.  **Level Frontend Registry (`printMenuRegistry.js`)**:
    *   Sesuaikan `rawGroups` dengan struktur kategori yang baru.
    *   Wajib sertakan metadata `sourceKey` yang nilainya sama dengan kunci grup di backend.
    *   Wajib sertakan parameter `?book_group=...` pada properti `href` untuk isolasi data.
4.  **Level Verifikasi**:
    *   Pastikan tidak ada duplikasi modul antar grup untuk satu peran yang sama (mencegah konflik filter).

---

### 6. Log Perubahan Non-Teknis
| Tanggal | Deskripsi Perubahan | Disetujui Oleh | Status |
| :--- | :--- | :--- | :--- |
| 11-06-2026 | Isolasi modul tambahan (`koperasi`, `kejar-paket`, `taman-bacaan`, `kader-khusus`, `literasi-warga`, `bkb-kegiatan`) khusus level **Desa** untuk akurasi Buku Data Kegiatan Pokja I-IV. Menu level Kecamatan tetap mengikuti baseline fixed V1.0. | Tim Pengembang | **Aktif (Standard)** |
| 21-05-2026 | Peresmian **Strict Visibility**, Granular Mapping, & Flow E2E | Tim Pengembang | Superseded |
| 20-05-2026 | Peresmian Dokumen Acuan Awal | Tim Pengembang | Superseded |
