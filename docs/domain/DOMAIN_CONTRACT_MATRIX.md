# Domain Contract Matrix (Lampiran 4.9-4.15 + Ekstensi 202-211)

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
| 4.14.1a | `data-warga` | Daftar Warga TP PKK | Header rumah tangga: `dasawisma`, `nama_kepala_keluarga`; detail anggota autentik 1-20: `nomor_registrasi`, `nomor_ktp_kk`, `nama`, `jabatan`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `umur_tahun`, `status_perkawinan`, `status_dalam_keluarga`, `agama`, `alamat`, `desa_kel_sejenis`, `pendidikan`, `pekerjaan`, `akseptor_kb`, `aktif_posyandu`, `ikut_bkb`, `memiliki_tabungan`, `ikut_kelompok_belajar`, `jenis_kelompok_belajar`, `ikut_paud`, `ikut_koperasi`; field agregat legacy transisi: `jumlah_warga_laki_laki`, `jumlah_warga_perempuan`, `keterangan` | `DAFTAR WARGA TP PKK` | Dokumen autentik `d:\\pedoman\\153.pdf` (Lampiran 4.14.1a) | implemented (struktur detail + PDF portrait autentik, kompatibilitas summary tetap aktif) |
| 4.14.1b | `data-kegiatan-warga` | Data Kegiatan Warga | `kegiatan`, `aktivitas`, `keterangan` | `DATA KEGIATAN WARGA` | PubHTML5 101-150 (Lampiran 4.14.1b) | match |
| 4.14.2a | `data-keluarga` | Data Keluarga | `kategori_keluarga`, `jumlah_keluarga`, `keterangan` | `DATA KELUARGA` | PubHTML5 101-150 (Lampiran 4.14.2a) | match |
| 4.14.2b | `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | Data Pemanfaatan Tanah Pekarangan/HATINYA PKK | `kategori_pemanfaatan_lahan`, `komoditi`, `jumlah_komoditi` | `DATA PEMANFAATAN TANAH PEKARANGAN/HATINYA PKK` | PubHTML5 101-150 (Lampiran 4.14.2b) | match |
| 4.14.2c | `data-industri-rumah-tangga` | Data Industri Rumah Tangga | `kategori_jenis_industri`, `komoditi`, `jumlah_komoditi` | `DATA INDUSTRI RUMAH TANGGA` | PubHTML5 101-150 (Lampiran 4.14.2c) | match |
| 4.14.3 | `data-pelatihan-kader` | Data Pelatihan Kader | `nomor_registrasi`, `nama_lengkap_kader`, `tanggal_masuk_tp_pkk`, `jabatan_fungsi`, `nomor_urut_pelatihan`, `judul_pelatihan`, `jenis_kriteria_kaderisasi`, `tahun_penyelenggaraan`, `institusi_penyelenggara`, `status_sertifikat` | `DATA PELATIHAN KADER` | PubHTML5 101-150 (Lampiran 4.14.3) | match |
| 4.14.4a | `warung-pkk` | Data Aset (Sarana) Desa/Kelurahan | `nama_warung_pkk`, `nama_pengelola`, `komoditi`, `kategori`, `volume` | `Data aset (sarana) desa/kelurahan` | PubHTML5 101-150 (Lampiran 4.14.4a) | match |
| 4.14.4b | `taman-bacaan` | Data Isian Taman Bacaan/Perpustakaan | `nama_taman_bacaan`, `nama_pengelola`, `jumlah_buku_bacaan`, `jenis_buku`, `kategori`, `jumlah` | `DATA ISIAN TAMAN BACAAN/PERPUSTAKAAN` | PubHTML5 101-150 (Lampiran 4.14.4b) | match |
| 4.14.4c | `koperasi` | Data Isian Koperasi | `nama_koperasi`, `jenis_usaha`, `berbadan_hukum`, `belum_berbadan_hukum`, `jumlah_anggota_l`, `jumlah_anggota_p` | `DATA ISIAN KOPERASI` | PubHTML5 101-150 (Lampiran 4.14.4c) | match |
| 4.14.4d | `kejar-paket` | Data Isian Kejar Paket/KF/PAUD | `nama_kejar_paket`, `jenis_kejar_paket`, `jumlah_warga_belajar_l`, `jumlah_warga_belajar_p`, `jumlah_pengajar_l`, `jumlah_pengajar_p` | `DATA ISIAN KEJAR PAKET/KF/PAUD` | PubHTML5 101-150 (Lampiran 4.14.4d) | match |
| 4.14.4e | `posyandu` | Data Isian Posyandu oleh TP PKK | `nama_posyandu`, `nama_pengelola`, `nama_sekretaris`, `jenis_posyandu`, `jumlah_kader`, `jenis_kegiatan`, `frekuensi_layanan`, `jumlah_pengunjung_l`, `jumlah_pengunjung_p`, `jumlah_petugas_l`, `jumlah_petugas_p` | `DATA ISIAN POSYANDU OLEH TP PKK` | PubHTML5 101-150 (Lampiran 4.14.4e) | match |
| 4.14.4f | `simulasi-penyuluhan` | Data Isian Kelompok Simulasi dan Penyuluhan | `nama_kegiatan`, `jenis_simulasi_penyuluhan`, `jumlah_kelompok`, `jumlah_sosialisasi`, `jumlah_kader_l`, `jumlah_kader_p`, `keterangan` | `DATA ISIAN KELOMPOK SIMULASI DAN PENYULUHAN` | PubHTML5 101-150 (Lampiran 4.14.4f) | match |
| 4.15 | `catatan-keluarga` | Catatan Keluarga | Read-only rekap dari `data-warga` + `data-kegiatan-warga`: `nama_kepala_rumah_tangga`, `jumlah_anggota_rumah_tangga`, `kerja_bakti`, `rukun_kematian`, `kegiatan_keagamaan`, `jimpitan`, `arisan`, `lain_lain`, `keterangan`; referensi struktur autentik fisik 19 kolom: `docs/domain/CATATAN_KELUARGA_19_TO_10_MAPPING.md` | `CATATAN KELUARGA` | Dokumen autentik `d:\\pedoman\\177.pdf` (Lampiran 4.15) | match (operasional 10 kolom sebagai proyeksi dari layout autentik 19 kolom) |
| 4.16a | `rekap-catatan-data-kegiatan-warga-dasawisma` | Rekapitulasi Catatan Data dan Kegiatan Warga Kelompok Dasa Wisma | Report rekap 29 kolom dengan merge-header autentik; data diambil dari `data_wargas` + `data_warga_anggotas` + indikator area dari `data_kegiatan_wargas` (UP2K/kesehatan lingkungan) + keberadaan entri `data_pemanfaatan_tanah_pekarangan_hatinya_pkks` dan `data_industri_rumah_tanggas` | `REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK DASA WISMA` | Dokumen autentik `d:\\pedoman\\179.pdf` (Lampiran 4.16a) | implemented (report-only via `catatan-keluarga`) |
| 4.16b | `rekap-catatan-data-kegiatan-warga-pkk-rt` | Rekapitulasi Catatan Data dan Kegiatan Warga Kelompok PKK RT | Report rekap 30 kolom dengan merge-header autentik; agregasi per `dasawisma` dari `data_wargas` + `data_warga_anggotas` + indikator area dari `data_kegiatan_wargas`/modul terkait | `REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RT` | Dokumen autentik `d:\\pedoman\\181.pdf` (Lampiran 4.16b) | implemented (report-only via `catatan-keluarga`) |
| 4.16c | `catatan-data-kegiatan-warga-pkk-rw` | Catatan Data dan Kegiatan Warga Kelompok PKK RW | Report 32 kolom dengan merge-header autentik; agregasi per `nomor_rt` (ekstraksi dari data rumah tangga) dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul, referensi mapping: `docs/domain/CATATAN_PKK_RW_4_16C_MAPPING.md` | `CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RW` | Dokumen autentik `d:\\pedoman\\183.pdf` (Lampiran 4.16c) | implemented (report-only via `catatan-keluarga`) |
| 4.16d | `rekap-catatan-data-kegiatan-warga-rw` | Rekapitulasi Catatan Data dan Kegiatan Warga Kelompok PKK Desa/Kelurahan | Report 33 kolom dengan merge-header autentik; agregasi per `nomor_rw` (ekstraksi dari data rumah tangga) dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul; `jml_rt` dihitung dari RT unik terdeteksi, referensi mapping: `docs/domain/LAMPIRAN_4_16D_MAPPING.md` | `REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK DESA/KELURAHAN` | Dokumen autentik `d:\\pedoman\\185.pdf` (Lampiran 4.16d) | implemented (report-only via `catatan-keluarga`, judul canonical final menunggu konfirmasi token identitas) |
| 4.17a | `catatan-data-kegiatan-warga-tp-pkk-desa-kelurahan` | Catatan Data dan Kegiatan Warga TP PKK Desa/Kelurahan | Report 33 kolom dengan merge-header autentik; agregasi per `nama_dusun_lingkungan` dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul; `jml_rw` dan `jml_rt` dihitung dari nilai unik hasil ekstraksi alamat, referensi mapping: `docs/domain/CATATAN_TP_PKK_DESA_KELURAHAN_4_17A_MAPPING.md` | `CATATAN DATA DAN KEGIATAN WARGA TP PKK` | Screenshot dokumen autentik Lampiran 4.17a (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.17b | `catatan-data-kegiatan-warga-tp-pkk-kecamatan` | Catatan Data dan Kegiatan Warga TP PKK Kecamatan | Report 35 kolom dengan merge-header autentik; agregasi per `nama_desa_kelurahan` dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul; `jml_dusun_lingkungan`, `jml_rw`, dan `jml_rt` dihitung dari nilai unik hasil ekstraksi alamat, referensi mapping: `docs/domain/CATATAN_TP_PKK_KECAMATAN_4_17B_MAPPING.md` | `CATATAN DATA DAN KEGIATAN WARGA` | Screenshot dokumen autentik Lampiran 4.17b (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.17c | `catatan-data-kegiatan-warga-tp-pkk-kabupaten-kota` | Catatan Data dan Kegiatan Warga TP PKK Kabupaten/Kota | Report 36 kolom dengan merge-header autentik; agregasi per `nama_kecamatan` dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul; `jml_desa_kelurahan`, `jml_dusun_lingkungan`, `jml_rw`, dan `jml_rt` dihitung dari nilai unik hasil ekstraksi alamat, referensi mapping: `docs/domain/CATATAN_TP_PKK_KABUPATEN_KOTA_4_17C_MAPPING.md` | `CATATAN DATA DAN KEGIATAN WARGA` | Screenshot dokumen autentik Lampiran 4.17c (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.17d | `catatan-data-kegiatan-warga-tp-pkk-provinsi` | Catatan Data dan Kegiatan Warga TP PKK Provinsi | Report 37 kolom dengan merge-header autentik; agregasi per `nama_kab_kota` dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul; `jml_kecamatan`, `jml_desa_kelurahan`, `jml_dusun_lingkungan`, `jml_rw`, dan `jml_rt` dihitung dari nilai unik hasil ekstraksi alamat, referensi mapping: `docs/domain/CATATAN_TP_PKK_PROVINSI_4_17D_MAPPING.md` | `CATATAN DATA DAN KEGIATAN WARGA` | Screenshot dokumen autentik Lampiran 4.17d (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.18a | `rekap-ibu-hamil-melahirkan-dasawisma` | Rekapitulasi Data/Buku Catatan Ibu Hamil, Melahirkan, Nifas, Ibu Meninggal, Kelahiran Bayi, Bayi Meninggal dan Kematian Balita dalam Kelompok Dasawisma | Report 17 kolom dengan merge-header autentik; basis data operasional dari `data_wargas` + `data_warga_anggotas`; metadata wilayah diturunkan dari hasil ekstraksi alamat dan area user; referensi mapping: `docs/domain/REKAP_IBU_HAMIL_DASAWISMA_4_18A_MAPPING.md` | `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA DALAM KELOMPOK DASAWISMA` | Screenshot dokumen autentik Lampiran 4.18a (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.18b | `rekap-ibu-hamil-melahirkan-pkk-rt` | Rekapitulasi Data/Buku Catatan Ibu Hamil, Melahirkan, Nifas, Ibu Meninggal, Kelahiran Bayi, Bayi Meninggal dan Kematian Balita dalam Kelompok PKK RT | Report 15 kolom dengan merge-header autentik; agregasi per `nama_kelompok_dasa_wisma` dari dataset 4.18a; metadata wilayah diturunkan dari hasil agregasi + area user; referensi mapping: `docs/domain/REKAP_IBU_HAMIL_PKK_RT_4_18B_MAPPING.md` | `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA DALAM KELOMPOK PKK RT` | Screenshot dokumen autentik Lampiran 4.18b (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.18c | `rekap-ibu-hamil-melahirkan-pkk-rw` | Rekapitulasi Data/Buku Catatan Ibu Hamil, Melahirkan, Nifas, Ibu Meninggal, Kelahiran Bayi, Bayi Meninggal dan Kematian Balita dalam Kelompok PKK RW | Report 16 kolom berdasarkan kontrak cara pengisian autentik; agregasi per `nomor_rt` dari dataset 4.18a; kolom `4-15` merepresentasikan penjumlahan indikator tingkat PKK RT; referensi mapping: `docs/domain/REKAP_IBU_HAMIL_PKK_RW_4_18C_MAPPING.md` | `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA DALAM KELOMPOK PKK RW` | Screenshot dokumen autentik Lampiran 4.18c (cara pengisian, sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| Ekstensi 202-211 | `pilot-project-keluarga-sehat` | Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana (Pokja IV) | Header laporan: `judul_laporan`, `dasar_hukum`, `pendahuluan`, `maksud_tujuan`, `pelaksanaan`, `dokumentasi`, `penutup`; nilai indikator periodik: `section`, `cluster_code`, `indicator_code`, `year`, `semester`, `value`, `evaluation_note`, `sort_order` | `LAPORAN PELAKSANAAN PILOT PROJECT GERAKAN KELUARGA SEHAT TANGGAP DAN TANGGUH BENCANA` | PubHTML5 201-241 (Halaman 202-211) | implemented (catalog tahap awal) |

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
  - `docs/domain/CATATAN_KELUARGA_19_TO_10_MAPPING.md`
- Rekap Dasa Wisma autentik:
  - `docs/domain/REKAP_DASA_WISMA_4_16A_MAPPING.md`
- Rekap PKK RT autentik:
  - `docs/domain/REKAP_PKK_RT_4_16B_MAPPING.md`
- Catatan PKK RW autentik:
  - `docs/domain/CATATAN_PKK_RW_4_16C_MAPPING.md`
  - `resources/views/pdf/catatan_data_kegiatan_warga_pkk_rw_report.blade.php`
- Lampiran 4.16d autentik:
  - `docs/domain/LAMPIRAN_4_16D_MAPPING.md`
  - `resources/views/pdf/rekap_catatan_data_kegiatan_warga_rw_report.blade.php`
- Catatan TP PKK Desa/Kelurahan autentik:
  - `docs/domain/CATATAN_TP_PKK_DESA_KELURAHAN_4_17A_MAPPING.md`
  - `resources/views/pdf/catatan_data_kegiatan_warga_tp_pkk_desa_kelurahan_report.blade.php`
- Catatan TP PKK Kecamatan autentik:
  - `docs/domain/CATATAN_TP_PKK_KECAMATAN_4_17B_MAPPING.md`
  - `resources/views/pdf/catatan_data_kegiatan_warga_tp_pkk_kecamatan_report.blade.php`
- Catatan TP PKK Kabupaten/Kota autentik:
  - `docs/domain/CATATAN_TP_PKK_KABUPATEN_KOTA_4_17C_MAPPING.md`
  - `resources/views/pdf/catatan_data_kegiatan_warga_tp_pkk_kabupaten_kota_report.blade.php`
- Catatan TP PKK Provinsi autentik:
  - `docs/domain/CATATAN_TP_PKK_PROVINSI_4_17D_MAPPING.md`
  - `resources/views/pdf/catatan_data_kegiatan_warga_tp_pkk_provinsi_report.blade.php`
- Rekap Ibu Hamil Dasawisma autentik:
  - `docs/domain/REKAP_IBU_HAMIL_DASAWISMA_4_18A_MAPPING.md`
  - `resources/views/pdf/rekap_ibu_hamil_melahirkan_dasawisma_report.blade.php`
- Rekap Ibu Hamil PKK RT autentik:
  - `docs/domain/REKAP_IBU_HAMIL_PKK_RT_4_18B_MAPPING.md`
  - `resources/views/pdf/rekap_ibu_hamil_melahirkan_pkk_rt_report.blade.php`
- Rekap Ibu Hamil PKK RW autentik:
  - `docs/domain/REKAP_IBU_HAMIL_PKK_RW_4_18C_MAPPING.md`
  - `resources/views/pdf/rekap_ibu_hamil_melahirkan_pkk_rw_report.blade.php`
- Ekstensi pilot project source:
  - `docs/domain/PEDOMAN_DOMAIN_UTAMA_202_211.md`

## Mapping Sidebar by Domain (Sekretaris TPK + Pokja I-IV)

Tujuan:
- Menyatukan navigasi domain dari struktur lampiran pedoman menjadi struktur kerja organisasi `Sekretaris TPK` dan `Pokja I-IV`.

Mapping grup sidebar:

| Grup Sidebar | Slug Modul |
| --- | --- |
| Sekretaris TPK | `anggota-tim-penggerak`, `kader-khusus`, `agenda-surat`, `bantuans`, `inventaris`, `activities`, `anggota-pokja`, `prestasi-lomba` |
| Pokja I | `data-warga`, `data-kegiatan-warga`, `bkl`, `bkr` |
| Pokja II | `data-pelatihan-kader`, `taman-bacaan`, `koperasi`, `kejar-paket` |
| Pokja III | `data-keluarga`, `data-industri-rumah-tangga`, `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, `warung-pkk` |
| Pokja IV | `posyandu`, `simulasi-penyuluhan`, `catatan-keluarga`, `program-prioritas`, `pilot-project-naskah-pelaporan`, `pilot-project-keluarga-sehat` |

Implementasi aktif:
- `resources/js/Layouts/DashboardLayout.vue`
