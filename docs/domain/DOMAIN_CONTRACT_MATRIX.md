# Domain Contract Matrix (Lampiran 4.9-4.15)

Sumber canonical domain:
- https://pubhtml5.com/zsnqq/vjcf/basic/101-150

Aturan baca:
- Kolom `field canonical` berisi field inti domain. Untuk tabel persisten, invariant wajib: `level`, `area_id`, `created_by`.
- Kolom `label PDF saat ini` diambil dari judul render pada `resources/views/pdf/*.blade.php`.
- Kolom `catatan koherensi` menandai apakah label PDF sudah identik dengan label pedoman.

| Lampiran | Slug modul | Label pedoman | Field canonical (inti) | Label PDF saat ini | Sumber halaman pedoman | Catatan koherensi |
| --- | --- | --- | --- | --- | --- | --- |
| 4.9a | `anggota-tim-penggerak` | Buku Daftar Anggota Tim Penggerak PKK | `nama`, `jabatan`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `status_perkawinan`, `alamat`, `pendidikan`, `pekerjaan`, `keterangan` | `Buku Daftar Anggota Tim Penggerak PKK` | PubHTML5 101-150 (Lampiran 4.9a) | match |
| 4.9b | `kader-khusus` | Buku Daftar Kader Tim Penggerak PKK | `nama`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `status_perkawinan`, `alamat`, `pendidikan`, `jenis_kader_khusus`, `keterangan` | `BUKU DAFTAR KADER TIM PENGGERAK PKK` | PubHTML5 101-150 (Lampiran 4.9b) | match |
| 4.10 | `agenda-surat` | Buku Agenda Surat Masuk/Keluar | `jenis_surat`, `tanggal_terima`, `tanggal_surat`, `nomor_surat`, `asal_surat`, `dari`, `kepada`, `perihal`, `lampiran`, `diteruskan_kepada`, `tembusan`, `keterangan` | `BUKU AGENDA SURAT MASUK/KELUAR` | PubHTML5 101-150 (Lampiran 4.10) | match |
| 4.11 | `bantuans` (keuangan report) | Buku Keuangan | `name`, `category`, `description`, `source`, `amount`, `received_date` | `BUKU KEUANGAN` | PubHTML5 101-150 (Lampiran 4.11) | match (report turunan domain `bantuans`) |
| 4.12 | `inventaris` | Buku Inventaris | `name`, `asal_barang`, `tanggal_penerimaan`, `quantity`, `unit`, `tempat_penyimpanan`, `condition`, `description`, `keterangan` | `BUKU INVENTARIS` | PubHTML5 101-150 (Lampiran 4.12) | match |
| 4.13 | `activities` | Buku Kegiatan | `title`, `nama_petugas`, `jabatan_petugas`, `activity_date`, `tempat_kegiatan`, `description`, `uraian`, `status`, `tanda_tangan` | `BUKU KEGIATAN TP PKK` | PubHTML5 101-150 (Lampiran 4.13) | match |
| 4.14.1a | `data-warga` | Data Warga | `dasawisma`, `nama_kepala_keluarga`, `alamat`, `jumlah_warga_laki_laki`, `jumlah_warga_perempuan`, `keterangan` | `DATA WARGA` | PubHTML5 101-150 (Lampiran 4.14.1a) | match |
| 4.14.1b | `data-kegiatan-warga` | Data Kegiatan Warga | `kegiatan`, `aktivitas`, `keterangan` | `DATA KEGIATAN WARGA` | PubHTML5 101-150 (Lampiran 4.14.1b) | match |
| 4.14.2a | `data-keluarga` | Data Keluarga | `kategori_keluarga`, `jumlah_keluarga`, `keterangan` | `DATA KELUARGA` | PubHTML5 101-150 (Lampiran 4.14.2a) | match |
| 4.14.2b | `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | Data Pemanfaatan Tanah Pekarangan/HATINYA PKK | `kategori_pemanfaatan_lahan`, `komoditi`, `jumlah_komoditi` | `DATA PEMANFAATAN TANAH PEKARANGAN/HATINYA PKK` | PubHTML5 101-150 (Lampiran 4.14.2b) | match |
| 4.14.2c | `data-industri-rumah-tangga` | Data Industri Rumah Tangga | `kategori_jenis_industri`, `komoditi`, `jumlah_komoditi` | `DATA INDUSTRI RUMAH TANGGA` | PubHTML5 101-150 (Lampiran 4.14.2c) | match |
| 4.14.3 | `data-pelatihan-kader` | Data Pelatihan Kader | `nomor_registrasi`, `nama_lengkap_kader`, `tanggal_masuk_tp_pkk`, `jabatan_fungsi`, `nomor_urut_pelatihan`, `judul_pelatihan`, `jenis_kriteria_kaderisasi`, `tahun_penyelenggaraan`, `institusi_penyelenggara`, `status_sertifikat` | `DATA PELATIHAN KADER` | PubHTML5 101-150 (Lampiran 4.14.3) | match |
| 4.14.4a | `warung-pkk` | Data Aset (Sarana) Desa/Kelurahan | `nama_warung_pkk`, `nama_pengelola`, `komoditi`, `kategori`, `volume` | `Data aset (sarana) desa/kelurahan` | PubHTML5 101-150 (Lampiran 4.14.4a) | match |
| 4.14.4b | `taman-bacaan` | Data Isian Taman Bacaan/Perpustakaan | `nama_taman_bacaan`, `nama_pengelola`, `jumlah_buku_bacaan`, `jenis_buku`, `kategori`, `jumlah` | `DATA ISIAN TAMAN BACAAN/PERPUSTAKAAN` | PubHTML5 101-150 (Lampiran 4.14.4b) | match |
| 4.14.4c | `koperasi` | Data Isian Koperasi | `nama_koperasi`, `jenis_usaha`, `berbadan_hukum`, `belum_berbadan_hukum`, `jumlah_anggota_l`, `jumlah_anggota_p` | `Laporan Koperasi` | PubHTML5 101-150 (Lampiran 4.14.4c) | perlu normalisasi label PDF ke pedoman |
| 4.14.4d | `kejar-paket` | Data Isian Kejar Paket | `nama_kejar_paket`, `jenis_kejar_paket`, `jumlah_warga_belajar_l`, `jumlah_warga_belajar_p`, `jumlah_pengajar_l`, `jumlah_pengajar_p` | `Laporan Kejar Paket/KF/PAUD` | PubHTML5 101-150 (Lampiran 4.14.4d) | perlu normalisasi label PDF ke pedoman |
| 4.14.4e | `posyandu` | Data Isian Posyandu oleh TP PKK | `nama_posyandu`, `nama_pengelola`, `nama_sekretaris`, `jenis_posyandu`, `jumlah_kader`, `jenis_kegiatan`, `frekuensi_layanan`, `jumlah_pengunjung_l`, `jumlah_pengunjung_p`, `jumlah_petugas_l`, `jumlah_petugas_p` | `Laporan Posyandu` | PubHTML5 101-150 (Lampiran 4.14.4e) | perlu normalisasi label PDF ke pedoman |
| 4.14.4f | `simulasi-penyuluhan` | Data Isian Kelompok Simulasi dan Penyuluhan | `nama_kegiatan`, `jenis_simulasi_penyuluhan`, `jumlah_kelompok`, `jumlah_sosialisasi`, `jumlah_kader_l`, `jumlah_kader_p`, `keterangan` | `DATA ISIAN KELOMPOK SIMULASI DAN PENYULUHAN` | PubHTML5 101-150 (Lampiran 4.14.4f) | match |
| 4.15 | `catatan-keluarga` | Catatan Keluarga | Read-only rekap dari `data-warga` + `data-kegiatan-warga`: `nama_kepala_rumah_tangga`, `jumlah_anggota_rumah_tangga`, `kerja_bakti`, `rukun_kematian`, `kegiatan_keagamaan`, `jimpitan`, `arisan`, `lain_lain`, `keterangan` | `CATATAN KELUARGA` | PubHTML5 101-150 (Lampiran 4.15) | match (rekap, tanpa tabel baru) |

## Jejak Teknis (Acuan Verifikasi)

- Route modul utama: `routes/web.php`
- Migration kontrak field:
  - `database/migrations/2026_02_20_120000_create_anggota_tim_penggeraks_table.php`
  - `database/migrations/2026_02_20_210000_create_kader_khusus_table.php`
  - `database/migrations/2026_02_21_050000_create_agenda_surats_table.php`
  - `database/migrations/2026_02_16_180000_create_bantuans_table.php`
  - `database/migrations/2026_02_16_170000_create_inventaris_table.php`
  - `database/migrations/2026_02_11_211614_create_activities_table.php`
  - `database/migrations/2026_02_21_030000_extend_inventaris_and_activities_for_secretary_books.php`
  - `database/migrations/2026_02_21_060000_create_data_wargas_table.php`
  - `database/migrations/2026_02_21_070000_create_data_kegiatan_wargas_table.php`
  - `database/migrations/2026_02_21_080000_create_data_keluargas_table.php`
  - `database/migrations/2026_02_21_090000_create_data_pemanfaatan_tanah_pekarangan_hatinya_pkks_table.php`
  - `database/migrations/2026_02_21_100000_create_data_industri_rumah_tanggas_table.php`
  - `database/migrations/2026_02_21_110000_create_data_pelatihan_kaders_table.php`
  - `database/migrations/2026_02_21_022000_create_warung_pkks_table.php`
  - `database/migrations/2026_02_21_023000_create_taman_bacaans_table.php`
  - `database/migrations/2026_02_21_021000_create_koperasis_table.php`
  - `database/migrations/2026_02_21_024000_create_kejar_pakets_table.php`
  - `database/migrations/2026_02_21_025000_create_posyandus_table.php`
  - `database/migrations/2026_02_20_230000_create_simulasi_penyuluhans_table.php`
- Catatan keluarga rekap source:
  - `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php`

