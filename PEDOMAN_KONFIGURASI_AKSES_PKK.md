# Pedoman Konfigurasi Akses Fitur (Permission Matrix)
## Aplikasi TP-PKK Kabupaten Batang

Dokumen ini adalah acuan resmi non-teknis untuk menentukan siapa (peran apa) yang boleh mengakses fitur apa di dalam aplikasi. Dokumen ini harus menjadi **titik awal (entry point)** bagi pengambil kebijakan jika terjadi perubahan struktur organisasi atau penambahan buku administrasi baru sesuai Rakernas X.

---

### 1. Prinsip Dasar Akses
Untuk menjaga integritas data namun tetap mendukung kolaborasi, aplikasi menggunakan prinsip:
1.  **Visibilitas Terbuka (View Only)**: Semua pengurus di tingkat yang sama (misal: semua pengurus Desa A) dapat melihat data pengurus lain untuk koordinasi.
2.  **Kontrol Eksekusi (Create/Edit/Delete)**: Hanya pengurus yang bertanggung jawab langsung pada bidangnya yang dapat menambah, mengubah, atau menghapus data.
3.  **Super Admin**: Hanya satu peran yang memiliki akses mutlak untuk pengelolaan sistem dan pengguna.

---

### 2. Matriks Tanggung Jawab Fitur
Gunakan tabel ini untuk mendiskusikan penyesuaian fitur. Jika ada buku baru, tentukan siapa "Pemilik (O)" dan siapa "Pengawas (V)".

| Bidang / Kelompok Jabatan | Contoh Fitur / Modul | Hak Akses (Operasional) |
| :--- | :--- | :--- |
| **Sekretariat** | Agenda Surat, Anggota TP-PKK, Laporan Tahunan, Inventaris, Data Warga | Full (Tambah/Edit/Hapus/Cetak) |
| **Bendahara** | Buku Keuangan, Laporan Keuangan | Full (Tambah/Edit/Hapus/Cetak) |
| **Pokja I** | Paar, Simulasi Penyuluhan, Literasi Warga, BKL, BKR | Full (Tambah/Edit/Hapus/Cetak) |
| **Pokja II** | Koperasi, UP2K, Taman Bacaan, Kejar Paket, Pelatihan Kader | Full (Tambah/Edit/Hapus/Cetak) |
| **Pokja III** | Industri Rumah Tangga, Pemanfaatan Tanah Pekarangan | Full (Tambah/Edit/Hapus/Cetak) |
| **Pokja IV** | Posyandu, BKB, Perencanaan Sehat, Pilot Project | Full (Tambah/Edit/Hapus/Cetak) |

*Catatan: Seluruh bidang di atas dapat saling **melihat (View)** data satu sama lain di tingkat wilayah yang sama.*

---

### 3. Prosedur Perubahan (Entry Point)
Jika terdapat permintaan perubahan dari stakeholder (misal: Ketua Pokja IV meminta fitur baru "Laporan Stunting"), ikuti langkah berikut:

1.  **Identifikasi Modul**: Tentukan nama modul baru tersebut.
2.  **Tentukan Pemilik**: Pilih peran mana yang akan menginput data tersebut (misal: `pokja-iv`).
3.  **Tentukan Level Wilayah**: Apakah fitur ini ada di tingkat Desa, Kecamatan, atau keduanya?
4.  **Update Dokumen Ini**: Tambahkan baris baru pada tabel di atas.
5.  **Instruksi Teknis**: Serahkan dokumen ini kepada pengembang untuk disinkronkan ke dalam `RoleScopeMatrix.php`.

---

### 4. Daftar Modul Saat Ini (Acuan Diskusi)
Berikut adalah daftar modul yang sudah aktif dan bisa dipindahkan hak aksesnya jika diperlukan:

*   **Administrasi Umum**: `arsip_document`, `activities`, `agenda_surat`, `anggota_pokja`, `inventaris`, `bantuan`, `kader_khusus`, `prestasi_lomba`, `program_prioritas`, `anggota_tim_penggerak`, `buku_daftar_hadir`, `buku_notulen_rapat`, `buku_tamu`, `laporan_tahunan_pkk`.
*   **Data Kependudukan**: `data_warga`, `data_kegiatan_warga`, `data_keluarga`, `catatan_keluarga`.
*   **Keuangan**: `buku_keuangan`.
*   **Pendidikan & Ekonomi (Pokja I & II)**: `simulasi_penyuluhan`, `bkr`, `paar`, `bkl`, `literasi_warga`, `pra_koperasi_up2k`, `koperasi`, `kejar_paket`, `taman_bacaan`, `pelatihan_kader_pokja_ii`, `warung_pkk`, `tutor_khusus`, `data_pelatihan_kader`.
*   **Pangan & Sandang (Pokja III)**: `data_pemanfaatan_tanah_pekarangan_hatinya_pkk`, `data_industri_rumah_tangga`.
*   **Kesehatan & Lingkungan (Pokja IV)**: `posyandu`, `bkb_kegiatan`, `pilot_project_naskah_pelaporan`, `pilot_project_keluarga_sehat`.

---

### 5. Log Perubahan Non-Teknis
| Tanggal | Deskripsi Perubahan | Disetujui Oleh | Status |
| :--- | :--- | :--- | :--- |
| 20-05-2026 | Peresmian Dokumen Acuan & Sentralisasi Izin Akses | Tim Pengembang | Aktif |
