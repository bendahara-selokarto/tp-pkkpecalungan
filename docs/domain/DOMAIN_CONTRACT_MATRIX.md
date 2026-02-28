# Domain Contract Matrix (Lampiran 4.9-4.24 + Ekstensi 202-211 + Ekstensi Lokal)

Sumber canonical domain:
- `docs/referensi/Rakernas X.pdf`
- `docs/domain/dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md` (status ketersediaan buku, autentikasi buku, dan penanggung jawab buku)

Status interpretasi:
- Dokumen ini adalah interpretasi teknis-operasional dari Rakernas X untuk kebutuhan implementasi aplikasi.
- Saat terjadi konflik interpretasi, sumber primer yang wajib diikuti adalah:
  - `PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md`
  - `docs/referensi/Rakernas X.pdf`

Aturan baca:
- Kolom `field canonical` berisi field inti domain. Untuk tabel persisten, invariant wajib: `level`, `area_id`, `created_by`.
- Kolom `label PDF saat ini` diambil dari judul render pada `resources/views/pdf/*.blade.php`.
- Kolom `catatan koherensi` menandai apakah label PDF sudah identik dengan label pedoman.

| Lampiran | Slug modul | Label pedoman | Field canonical (inti) | Label PDF saat ini | Sumber halaman pedoman | Catatan koherensi |
| --- | --- | --- | --- | --- | --- | --- |
| 4.9a | `anggota-tim-penggerak` | Buku Daftar Anggota Tim Penggerak PKK | `nama`, `jabatan`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `status_perkawinan`, `alamat`, `pendidikan`, `pekerjaan`, `keterangan` | `BUKU DAFTAR ANGGOTA TIM PENGGERAK PKK` | Rakernas X (Lampiran 4.9a) | match (header nomor kolom 1-11 sinkron autentik) |
| 4.9b | `kader-khusus` | Buku Kader Khusus | Field inti: `nama`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `status_perkawinan`, `alamat`, `pendidikan`, `jenis_kader_khusus`, `keterangan`; proyeksi report autentik: `jenis_kelamin -> (L/P)`, `status_perkawinan -> (NIKAH/BLM NIKAH)` | `BUKU KADER KHUSUS` | Referensi terbaru `docs/referensi/excel/BUKU BANTU.xlsx` (sheet `Buku Kader Khusus`) + Rakernas X (Lampiran 4.9b) | implemented (menggunakan referensi terakhir; schema existing tetap normal) |
| 4.10 | `agenda-surat` | Buku Agenda Surat Masuk/Keluar | `jenis_surat`, `tanggal_terima`, `tanggal_surat`, `nomor_surat`, `asal_surat`, `dari`, `kepada`, `perihal`, `lampiran`, `diteruskan_kepada`, `tembusan`, `keterangan`, `data_dukung_path` (opsional, upload berkas operasional) | `BUKU AGENDA SURAT MASUK/KELUAR` | Rakernas X (Lampiran 4.10) | match (merge header + nomor kolom 1-15 sinkron autentik; data dukung upload adalah ekstensi operasional non-drift pedoman) |
| Ekstensi Lokal 2026 | `buku-notulen-rapat` | Buku Notulen Rapat | `entry_date`, `title`, `person_name`, `institution`, `description` | - | Rakernas X (kelompok buku sekretaris) | implemented (`unverified`): baseline header internal terkunci via test; menunggu sumber autentik primer + bukti visual final |
| Ekstensi Lokal 2026 | `buku-daftar-hadir` | Buku Daftar Hadir | `attendance_date`, `activity_id`, `attendee_name`, `institution`, `description` | - | Rakernas X (kelompok buku sekretaris) | implemented (`unverified`): baseline header internal terkunci via test; menunggu sumber autentik primer + bukti visual final |
| Ekstensi Lokal 2026 | `buku-tamu` | Buku Tamu | `visit_date`, `guest_name`, `purpose`, `institution`, `description` | - | Rakernas X (kelompok buku sekretaris) | implemented (`unverified`): baseline header internal terkunci via test; menunggu sumber autentik primer + bukti visual final |
| 4.11 | `buku-keuangan` | Buku Tabungan | `transaction_date`, `source`, `description`, `reference_number`, `entry_type`, `amount` | `BUKU TABUNGAN` | Rakernas X (Lampiran 4.11) | implemented (layout autentik 12 kolom sinkron; domain transaksi keuangan terpisah dari bantuan) |
| Ekstensi Lokal 2026 | `bantuan` | Buku Bantuan | Kontrak input/report autentik: `tanggal`, `asal_bantuan`, `jenis_bantuan`, `jumlah`, `lokasi_penerima`, `keterangan`; storage kompatibilitas: `received_date`, `source`, `category`, `amount`, `name`, `description` | `BUKU BANTUAN {LEVEL}` | Dokumen autentik `docs/referensi/excel/BUKU BANTU.xlsx` (sheet `Buku Bantuan`) | implemented (header merge sinkron autentik + normalisasi request/repository tanpa drift schema) |
| Ekstensi Lokal 2026 | `prestasi-lomba` | Buku Prestasi | `tahun`, `jenis_lomba`, `lokasi`, `prestasi_kecamatan`, `prestasi_kabupaten`, `prestasi_provinsi`, `prestasi_nasional`, `keterangan` | `BUKU PRESTASI {LEVEL}` | Dokumen autentik `docs/referensi/excel/BUKU BANTU.xlsx` (sheet `Buku Prestasi`) | implemented (header grup prestasi 4 tingkat sinkron autentik; field boolean capaian tetap normal) |
| Ekstensi Lokal 2026 | `paar` | Data Pola Asuh Anak dan Remaja (PAAR) | `indikator`, `jumlah`, `keterangan` | `BUKU PAAR` | Dokumen autentik `docs/referensi/excel/BUKU BANTU.xlsx` + screenshot PAAR sesi 2026-02-24 (referensi terakhir) | accepted deviation (koreksi domain operasional `Pemetaan Modul.xlsx` 2026-02-25; indikator fixed list 6 item dan scope `desa|kecamatan` tetap) |
| 4.12 | `inventaris` | Buku Inventaris | `name`, `asal_barang`, `tanggal_penerimaan`, `quantity`, `unit`, `tempat_penyimpanan`, `condition`, `description`, `keterangan` | `BUKU INVENTARIS` | Rakernas X (Lampiran 4.12) | match (header + nomor kolom 1-8 sinkron autentik) |
| 4.13 | `activities` | Buku Kegiatan | `title`, `nama_petugas`, `jabatan_petugas`, `activity_date`, `tempat_kegiatan`, `description`, `uraian`, `status`, `tanda_tangan`, `image_path` (opsional), `document_path` (opsional) | `BUKU KEGIATAN` | Rakernas X (Lampiran 4.13) | match (group header `KEGIATAN` + nomor kolom 1-7 sinkron autentik; lampiran upload bersifat ekstensi operasional) |
| 4.14.1a | `data-warga` | Daftar Warga TP PKK | Header rumah tangga: `dasawisma`, `nama_kepala_keluarga`; detail anggota autentik 1-20: `nomor_registrasi`, `nomor_ktp_kk`, `nama`, `jabatan`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `umur_tahun`, `status_perkawinan`, `status_dalam_keluarga`, `agama`, `alamat`, `desa_kel_sejenis`, `pendidikan`, `pekerjaan`, `akseptor_kb`, `aktif_posyandu`, `ikut_bkb`, `memiliki_tabungan`, `ikut_kelompok_belajar`, `jenis_kelompok_belajar`, `ikut_paud`, `ikut_koperasi`; field agregat legacy transisi: `jumlah_warga_laki_laki`, `jumlah_warga_perempuan`, `keterangan` | `DAFTAR WARGA TP PKK` | Dokumen autentik `d:\\pedoman\\153.pdf` (Lampiran 4.14.1a) | implemented (struktur detail + PDF portrait autentik, kompatibilitas summary tetap aktif) |
| 4.14.1b | `data-kegiatan-warga` | Kegiatan Warga | `kegiatan`, `aktivitas`, `keterangan` | `KEGIATAN WARGA` | Rakernas X (Lampiran 4.14.1b) | match (7 baris kegiatan baku sinkron autentik) |
| 4.14.2a | `data-keluarga` | Data Keluarga | `kategori_keluarga`, `jumlah_keluarga`, `keterangan` | `DATA KELUARGA` | Rakernas X (Lampiran 4.14.2a) | partial match (operasional summary; full form autentik ditunda refactor domain) |
| 4.14.2b | `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | Pemanfaatan Tanah Pekarangan/AKU HATINYA PKK | `kategori_pemanfaatan_lahan`, `komoditi`, `jumlah_komoditi` | `BUKU HATINYA PKK` | Rakernas X (Lampiran 4.14.2b) | accepted deviation (koreksi domain operasional `Pemetaan Modul.xlsx` 2026-02-25; kontrak field/query tetap) |
| 4.14.2c | `data-industri-rumah-tangga` | Data Industri Rumah Tangga | `kategori_jenis_industri`, `komoditi`, `jumlah_komoditi` | `BUKU INDUSTRI RUMAH TANGGA` | Rakernas X (Lampiran 4.14.2c) | accepted deviation (koreksi domain operasional `Pemetaan Modul.xlsx` 2026-02-25; kontrak field/query tetap) |
| 4.14.3 | `data-pelatihan-kader` | Data Pelatihan Kader | `nomor_registrasi`, `nama_lengkap_kader`, `tanggal_masuk_tp_pkk`, `jabatan_fungsi`, `nomor_urut_pelatihan`, `judul_pelatihan`, `jenis_kriteria_kaderisasi`, `tahun_penyelenggaraan`, `institusi_penyelenggara`, `status_sertifikat` | `DATA PELATIHAN KADER` | Rakernas X (Lampiran 4.14.3) | match |
| 4.14.4a | `warung-pkk` | Data Aset (Sarana) Desa/Kelurahan | `nama_warung_pkk`, `nama_pengelola`, `komoditi`, `kategori`, `volume` | `Data aset (sarana) desa/kelurahan` | Rakernas X (Lampiran 4.14.4a) | match |
| 4.14.4b | `taman-bacaan` | Data Isian Taman Bacaan/Perpustakaan | Identitas form: `nama_taman_bacaan`, `nama_pengelola`, `jumlah_buku_bacaan`; matriks jenis buku: `jenis_buku`, `kategori`, `jumlah` | `B. TAMAN BACAAN` | Rakernas X (Lampiran 4.14.4b) | match (layout formulir + tabel 4 kolom sinkron autentik) |
| 4.14.4c | `koperasi` | Data Isian Koperasi | `nama_koperasi`, `jenis_usaha`, `berbadan_hukum`, `belum_berbadan_hukum`, `jumlah_anggota_l`, `jumlah_anggota_p` | `DATA ISIAN KOPERASI` | Rakernas X (Lampiran 4.14.4c) | match |
| 4.14.4d | `kejar-paket` | Data Isian Kejar Paket/KF/PAUD | `nama_kejar_paket`, `jenis_kejar_paket`, `jumlah_warga_belajar_l`, `jumlah_warga_belajar_p`, `jumlah_pengajar_l`, `jumlah_pengajar_p` | `DATA ISIAN KEJAR PAKET/KF/PAUD` | Rakernas X (Lampiran 4.14.4d) | match |
| 4.14.4e | `posyandu` | Data Isian Posyandu oleh TP PKK | Identitas posyandu: `nama_posyandu`, `nama_pengelola`, `nama_sekretaris`, `jenis_posyandu`, `jumlah_kader`; detail kegiatan: `jenis_kegiatan`, `frekuensi_layanan`, `jumlah_pengunjung_l`, `jumlah_pengunjung_p`, `jumlah_petugas_l`, `jumlah_petugas_p`, `keterangan` | `DATA ISIAN POSYANDU OLEH TP PKK` | Rakernas X (Lampiran 4.14.4e) | match |
| 4.14.4f | `simulasi-penyuluhan` | Kelompok Simulasi dan Penyuluhan | Header autentik tabel: `nama_kegiatan`, `jenis_simulasi_penyuluhan`, `jumlah_kelompok`, `jumlah_sosialisasi`, `jumlah_kader_l`, `jumlah_kader_p`; field kompatibilitas internal: `keterangan` | `KELOMPOK SIMULASI DAN PENYULUHAN` | Rakernas X (Lampiran 4.14.4f) | match |
| 4.15 | `catatan-keluarga` | Catatan Keluarga | Read-only rekap dari `data-warga` + `data-kegiatan-warga`: `nama_kepala_rumah_tangga`, `jumlah_anggota_rumah_tangga`, `kerja_bakti`, `rukun_kematian`, `kegiatan_keagamaan`, `jimpitan`, `arisan`, `lain_lain`, `keterangan`; referensi struktur autentik fisik 19 kolom: `docs/domain/CATATAN_KELUARGA_19_TO_10_MAPPING.md` | `CATATAN KELUARGA` | Dokumen autentik `d:\\pedoman\\177.pdf` (Lampiran 4.15) | match (operasional 10 kolom sebagai proyeksi dari layout autentik 19 kolom) |
| 4.16a | `rekap-catatan-data-kegiatan-warga-dasawisma` | Rekapitulasi Catatan Data dan Kegiatan Warga Kelompok Dasa Wisma | Report rekap 29 kolom dengan merge-header autentik; data diambil dari `data_wargas` + `data_warga_anggotas` + indikator area dari `data_kegiatan_wargas` (UP2K/kesehatan lingkungan) + keberadaan entri `data_pemanfaatan_tanah_pekarangan_hatinya_pkks` dan `data_industri_rumah_tanggas` | `REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK DASA WISMA` | Dokumen autentik `d:\\pedoman\\179.pdf` (Lampiran 4.16a) | implemented (report-only via `catatan-keluarga`) |
| 4.16b | `rekap-catatan-data-kegiatan-warga-pkk-rt` | Rekapitulasi Catatan Data dan Kegiatan Warga Kelompok PKK RT | Report rekap 30 kolom dengan merge-header autentik; agregasi per `dasawisma` dari `data_wargas` + `data_warga_anggotas` + indikator area dari `data_kegiatan_wargas`/modul terkait | `REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RT` | Dokumen autentik `d:\\pedoman\\181.pdf` (Lampiran 4.16b) | implemented (report-only via `catatan-keluarga`) |
| 4.16c | `catatan-data-kegiatan-warga-pkk-rw` | Catatan Data dan Kegiatan Warga Kelompok PKK RW | Report 32 kolom dengan merge-header autentik; agregasi per `nomor_rt` (ekstraksi dari data rumah tangga) dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul, referensi mapping: `docs/domain/CATATAN_PKK_RW_4_16C_MAPPING.md` | `CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RW` | Dokumen autentik `d:\\pedoman\\183.pdf` (Lampiran 4.16c) | implemented (report-only via `catatan-keluarga`) |
| 4.16d | `rekap-catatan-data-kegiatan-warga-rw` | Catatan Data dan Kegiatan Warga Kelompok PKK Dusun/Lingkungan | Report 33 kolom dengan merge-header autentik; agregasi per `nomor_rw` (ekstraksi dari data rumah tangga) dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul; `jml_rt` dihitung dari RT unik terdeteksi, referensi mapping: `docs/domain/LAMPIRAN_4_16D_MAPPING.md` | `CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK DUSUN/LINGKUNGAN` | Dokumen autentik `d:\\pedoman\\185.pdf` (Lampiran 4.16d) + screenshot halaman penuh (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.17a | `catatan-data-kegiatan-warga-tp-pkk-desa-kelurahan` | Catatan Data dan Kegiatan Warga TP PKK Desa/Kelurahan | Report 33 kolom dengan merge-header autentik; agregasi per `nama_dusun_lingkungan` dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul; `jml_rw` dan `jml_rt` dihitung dari nilai unik hasil ekstraksi alamat, referensi mapping: `docs/domain/CATATAN_TP_PKK_DESA_KELURAHAN_4_17A_MAPPING.md` | `CATATAN DATA DAN KEGIATAN WARGA TP PKK` | Screenshot dokumen autentik Lampiran 4.17a (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.17b | `catatan-data-kegiatan-warga-tp-pkk-kecamatan` | Catatan Data dan Kegiatan Warga TP PKK Kecamatan | Report 35 kolom dengan merge-header autentik; agregasi per `nama_desa_kelurahan` dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul; `jml_dusun_lingkungan`, `jml_rw`, dan `jml_rt` dihitung dari nilai unik hasil ekstraksi alamat, referensi mapping: `docs/domain/CATATAN_TP_PKK_KECAMATAN_4_17B_MAPPING.md` | `CATATAN DATA DAN KEGIATAN WARGA` | Screenshot dokumen autentik Lampiran 4.17b (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.17c | `catatan-data-kegiatan-warga-tp-pkk-kabupaten-kota` | Catatan Data dan Kegiatan Warga TP PKK Kabupaten/Kota | Report 36 kolom dengan merge-header autentik; agregasi per `nama_kecamatan` dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul; `jml_desa_kelurahan`, `jml_dusun_lingkungan`, `jml_rw`, dan `jml_rt` dihitung dari nilai unik hasil ekstraksi alamat, referensi mapping: `docs/domain/CATATAN_TP_PKK_KABUPATEN_KOTA_4_17C_MAPPING.md` | `CATATAN DATA DAN KEGIATAN WARGA` | Screenshot dokumen autentik Lampiran 4.17c (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.17d | `catatan-data-kegiatan-warga-tp-pkk-provinsi` | Catatan Data dan Kegiatan Warga TP PKK Provinsi | Report 37 kolom dengan merge-header autentik; agregasi per `nama_kab_kota` dari `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul; `jml_kecamatan`, `jml_desa_kelurahan`, `jml_dusun_lingkungan`, `jml_rw`, dan `jml_rt` dihitung dari nilai unik hasil ekstraksi alamat, referensi mapping: `docs/domain/CATATAN_TP_PKK_PROVINSI_4_17D_MAPPING.md` | `CATATAN DATA DAN KEGIATAN WARGA` | Screenshot dokumen autentik Lampiran 4.17d (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.18a | `rekap-ibu-hamil-dasawisma` | Rekapitulasi Data/Buku Catatan Ibu Hamil, Melahirkan, Nifas, Ibu Meninggal, Kelahiran Bayi, Bayi Meninggal dan Kematian Balita dalam Kelompok Dasawisma | Report 17 kolom dengan merge-header autentik; basis data operasional dari `data_wargas` + `data_warga_anggotas`; metadata wilayah diturunkan dari hasil ekstraksi alamat dan area user; referensi mapping: `docs/domain/REKAP_IBU_HAMIL_DASAWISMA_4_18A_MAPPING.md` | `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA DALAM KELOMPOK DASAWISMA` | Screenshot dokumen autentik Lampiran 4.18a (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.18b | `rekap-ibu-hamil-pkk-rt` | Rekapitulasi Data/Buku Catatan Ibu Hamil, Melahirkan, Nifas, Ibu Meninggal, Kelahiran Bayi, Bayi Meninggal dan Kematian Balita dalam Kelompok PKK RT | Report 15 kolom dengan merge-header autentik; agregasi per `nama_kelompok_dasa_wisma` dari dataset 4.18a; metadata wilayah diturunkan dari hasil agregasi + area user; referensi mapping: `docs/domain/REKAP_IBU_HAMIL_PKK_RT_4_18B_MAPPING.md` | `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA DALAM KELOMPOK PKK RT` | Screenshot dokumen autentik Lampiran 4.18b (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.18c | `rekap-ibu-hamil-pkk-rw` | Rekapitulasi Data/Buku Catatan Ibu Hamil, Melahirkan, Nifas, Ibu Meninggal, Kelahiran Bayi, Bayi Meninggal dan Kematian Balita dalam Kelompok PKK RW | Report 16 kolom berdasarkan kontrak cara pengisian autentik; agregasi per `nomor_rt` dari dataset 4.18a; kolom `4-15` merepresentasikan penjumlahan indikator tingkat PKK RT; referensi mapping: `docs/domain/REKAP_IBU_HAMIL_PKK_RW_4_18C_MAPPING.md` | `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA DALAM KELOMPOK PKK RW` | Screenshot dokumen autentik Lampiran 4.18c (cara pengisian, sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.18d | `rekap-ibu-hamil-pkk-dusun-lingkungan` | Buku Catatan Ibu Hamil, Kelahiran, Kematian Bayi, Kematian Balita dan Kematian Ibu Hamil, Melahirkan dan Nifas dalam Kelompok PKK Dusun/Lingkungan | Report 17 kolom dengan merge-header autentik; agregasi per `nomor_rw` dari dataset 4.18a; kolom `3` merepresentasikan jumlah RT unik pada RW, kolom `4` merepresentasikan penjumlahan jumlah dasawisma per RT (sesuai cara pengisian), kolom `5-16` merupakan penjumlahan indikator maternal/kelahiran/kematian; referensi mapping: `docs/domain/REKAP_IBU_HAMIL_DUSUN_LINGKUNGAN_4_18D_MAPPING.md` | `BUKU CATATAN IBU HAMIL, KELAHIRAN, KEMATIAN BAYI, KEMATIAN BALITA DAN KEMATIAN IBU HAMIL, MELAHIRKAN DAN NIFAS DALAM KELOMPOK PKK DUSUN/LINGKUNGAN` | Screenshot dokumen autentik Lampiran 4.18d (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.19b | `rekap-ibu-hamil-tp-pkk-kecamatan` | Rekapitulasi Data/Buku Catatan Ibu Hamil, Melahirkan, Nifas, Ibu Meninggal, Kelahiran Bayi, Bayi Meninggal dan Kematian Balita pada Tingkat TP PKK Kecamatan | Report 19 kolom dengan merge-header autentik; agregasi per `nama_desa_kelurahan` dari dataset turunan 4.18d (via agregasi 4.19a tingkat desa/kelurahan); kolom `3` merepresentasikan jumlah dusun/lingkungan unik, kolom `4-18` merepresentasikan penjumlahan indikator maternal/kelahiran/kematian; referensi mapping: `docs/domain/REKAP_IBU_HAMIL_TP_PKK_KECAMATAN_4_19B_MAPPING.md` | `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA PADA TINGKAT TP PKK KECAMATAN` | Dokumen autentik `docs/referensi/207.pdf` + screenshot Lampiran 4.19b (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.20a | `data-umum-pkk` | Data Umum PKK | Report 20 kolom dengan merge-header autentik; agregasi per `nama_dusun_lingkungan_atau_sebutan_lain`; kolom `3-9` dari `data_wargas`, kolom `10-15` dari `anggota_tim_penggeraks` + `anggota_pokjas` + `kader_khusus`, kolom `16-19` dari inferensi token `jabatan` (`honorer`/`bantuan`) pada `anggota_tim_penggeraks`; referensi mapping: `docs/domain/DATA_UMUM_PKK_4_20A_MAPPING.md` | `DATA UMUM PKK` | Dokumen autentik `docs/referensi/213.pdf` + screenshot Lampiran 4.20a (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.20b | `data-umum-pkk-kecamatan` | Data Umum PKK | Report 21 kolom dengan merge-header autentik; agregasi per `nama_desa_kelurahan`; kolom `3-10` dari `data_wargas`, kolom `11-16` dari `anggota_tim_penggeraks` + `anggota_pokjas` + `kader_khusus`, kolom `17-20` dari inferensi token `jabatan` (`honorer`/`bantuan`) pada `anggota_tim_penggeraks`; referensi mapping: `docs/domain/DATA_UMUM_PKK_4_20B_MAPPING.md` | `DATA UMUM PKK` | Dokumen autentik `docs/referensi/215.pdf` + screenshot Lampiran 4.20b (sesi validasi 2026-02-22) | implemented (report-only via `catatan-keluarga`) |
| 4.23 | `data-kegiatan-pkk-pokja-iii` | Data Kegiatan PKK (Pokja III) | Report 20 kolom dengan merge-header autentik; agregasi operasional lintas modul (`anggota_pokjas`, `data_pemanfaatan_tanah_pekarangan_hatinya_pkks`, `data_industri_rumah_tanggas`, `data_wargas`, `data_warga_anggotas`); referensi mapping: `docs/domain/DATA_KEGIATAN_PKK_POKJA_III_4_23_MAPPING.md` | `DATA KEGIATAN PKK` | Dokumen autentik `docs/referensi/229-230.pdf` + screenshot Lampiran 4.23 (sesi validasi 2026-02-23) | implemented (report-only via `catatan-keluarga`) |
| 4.24 | `data-kegiatan-pkk-pokja-iv` | Data Kegiatan PKK (Pokja IV) | Report 27 kolom dengan merge-header autentik; agregasi operasional lintas modul (`kader_khusus`, `posyandus`, `data_kegiatan_wargas`, `data_wargas`, `data_warga_anggotas`, `program_prioritas`); referensi mapping: `docs/domain/DATA_KEGIATAN_PKK_POKJA_IV_4_24_MAPPING.md` | `DATA KEGIATAN PKK` | Dokumen autentik `docs/referensi/232.pdf` + screenshot Lampiran 4.24 (sesi validasi 2026-02-23) | implemented (report-only via `catatan-keluarga`) |
| Ekstensi 202-211 | `pilot-project-keluarga-sehat` | Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana (Pokja IV) | Header laporan: `judul_laporan`, `dasar_hukum`, `pendahuluan`, `maksud_tujuan`, `pelaksanaan`, `dokumentasi`, `penutup`; nilai indikator periodik: `section`, `cluster_code`, `indicator_code`, `year`, `semester`, `value`, `evaluation_note`, `sort_order` | `LAPORAN PELAKSANAAN PILOT PROJECT GERAKAN KELUARGA SEHAT TANGGAP DAN TANGGUH BENCANA` | Rakernas X (Halaman 202-211) | implemented (catalog tahap awal) |
| Ekstensi Lokal 2025 | `laporan-tahunan-pkk` | Laporan Tahunan Tim Penggerak PKK Kecamatan | Kontrak data baseline terdiri dari metadata laporan tahunan, item kegiatan per bidang (`sekretariat`, `pokja-i` s.d `pokja-iv`), dan narasi evaluasi/penutup; struktur visual diambil dari template dokumen contoh, sedangkan isi runtime wajib diambil dari database aplikasi dan boleh agregasi lintas tabel dalam boundary repository scoped; bila data tidak ditemukan, sistem memakai form isian pelengkap terpersistensi dengan guardrail scope; kontrak output wajib `single-file .docx` dengan urutan konten identik dokumen contoh; tabel OOXML diperlakukan sebagai layout naskah tanpa border; referensi mapping: `docs/domain/LAPORAN_TAHUNAN_PKK_2025_MAPPING.md` | `LAPORAN TAHUNAN TIM PENGGERAK PKK` | Dokumen contoh lokal `docs/referensi/LAPORAN TAHUNAN PKK th 2025.docx` | implemented (menu sekretaris + CRUD + docx generator; dashboard coverage dikecualikan eksplisit karena ekstensi lokal) |

## Jejak Teknis (Acuan Verifikasi)

- Route modul utama: `routes/web.php`
- Migration kontrak field:
  - `database/migrations/2026_02_20_120000_create_anggota_tim_penggeraks_table.php`
  - `database/migrations/2026_02_20_210000_create_kader_khusus_table.php`
  - `database/migrations/2026_02_21_050000_create_agenda_surats_table.php`
  - `database/migrations/2026_02_28_000000_add_data_dukung_path_to_agenda_surats_table.php`
  - `database/migrations/2026_02_27_120000_create_buku_notulen_rapats_table.php`
  - `database/migrations/2026_02_27_130000_create_buku_daftar_hadirs_table.php`
  - `database/migrations/2026_02_27_140000_create_buku_tamus_table.php`
  - `database/migrations/2026_02_16_180000_create_bantuans_table.php`
  - `database/migrations/2026_02_23_070000_create_buku_keuangans_table.php`
  - `database/migrations/2026_02_16_170000_create_inventaris_table.php`
  - `database/migrations/2026_02_11_211614_create_activities_table.php`
  - `database/migrations/2026_02_21_030000_extend_inventaris_and_activities_for_secretary_books.php`
  - `database/migrations/2026_02_27_120000_add_upload_fields_to_activities_table.php`
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
  - `database/migrations/2026_02_24_230000_create_paars_table.php`
  - `database/migrations/2026_02_20_200000_create_program_prioritas_table.php`
  - `database/migrations/2026_02_24_180000_add_jadwal_bulanan_columns_to_program_prioritas_table.php`
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
- Rekap Ibu Hamil Dusun/Lingkungan autentik:
  - `docs/domain/REKAP_IBU_HAMIL_DUSUN_LINGKUNGAN_4_18D_MAPPING.md`
  - `resources/views/pdf/rekap_ibu_hamil_melahirkan_dusun_lingkungan_report.blade.php`
- Rekap Ibu Hamil TP PKK Kecamatan autentik:
  - `docs/domain/REKAP_IBU_HAMIL_TP_PKK_KECAMATAN_4_19B_MAPPING.md`
  - `resources/views/pdf/rekap_ibu_hamil_melahirkan_tp_pkk_kecamatan_report.blade.php`
- Data Umum PKK autentik:
  - `docs/domain/DATA_UMUM_PKK_4_20A_MAPPING.md`
  - `resources/views/pdf/data_umum_pkk_report.blade.php`
- Data Umum PKK tingkat kecamatan autentik:
  - `docs/domain/DATA_UMUM_PKK_4_20B_MAPPING.md`
  - `resources/views/pdf/data_umum_pkk_kecamatan_report.blade.php`
- Data Kegiatan PKK Pokja III autentik:
  - `docs/domain/DATA_KEGIATAN_PKK_POKJA_III_4_23_MAPPING.md`
  - `resources/views/pdf/data_kegiatan_pkk_pokja_iii_report.blade.php`
- Data Kegiatan PKK Pokja IV autentik:
  - `docs/domain/DATA_KEGIATAN_PKK_POKJA_IV_4_24_MAPPING.md`
  - `resources/views/pdf/data_kegiatan_pkk_pokja_iv_report.blade.php`
- Laporan Tahunan PKK (ekstensi lokal):
  - `docs/domain/LAPORAN_TAHUNAN_PKK_2025_MAPPING.md`
- Baseline autentik sekretaris inti (ekstensi lokal):
  - `docs/domain/BUKU_SEKRETARIS_INTI_AUTH_MAPPING.md`
- Ekstensi pilot project source:
  - `docs/domain/PEDOMAN_DOMAIN_UTAMA_202_211.md`

## Mapping Sidebar by Domain (Sekretaris TPK + Pokja I-IV)

Tujuan:
- Menyatukan navigasi domain dari struktur lampiran pedoman menjadi struktur kerja organisasi `Sekretaris TPK` dan `Pokja I-IV`.

Mapping grup sidebar:

| Grup Sidebar | Slug Modul |
| --- | --- |
| Sekretaris TPK | `anggota-tim-penggerak`, `kader-khusus`, `agenda-surat`, `buku-daftar-hadir`, `buku-tamu`, `buku-notulen-rapat`, `buku-keuangan`, `inventaris`, `activities`, `program-prioritas`, `anggota-pokja`, `prestasi-lomba`, `laporan-tahunan-pkk` |
| Pokja I | `data-warga`, `data-kegiatan-warga`, `bkl`, `bkr`, `paar` |
| Pokja II | `data-pelatihan-kader`, `taman-bacaan`, `koperasi`, `kejar-paket` |
| Pokja III | `data-keluarga`, `data-industri-rumah-tangga`, `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, `warung-pkk` |
| Pokja IV | `posyandu`, `simulasi-penyuluhan`, `catatan-keluarga`, `pilot-project-naskah-pelaporan`, `pilot-project-keluarga-sehat` |

Implementasi aktif:
- `resources/js/Layouts/DashboardLayout.vue`

## Role Responsibility Matrix + Access Mode

Kontrak mode:
- `read-write`: dapat baca + mutasi (`create/store/edit/update/destroy`).
- `read-only`: hanya baca (`index/show/report/print`), mutasi ditolak backend.

| Role | Scope | Sekretaris TPK | Pokja I | Pokja II | Pokja III | Pokja IV | Monitoring Kecamatan | Referensi |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `desa-sekretaris` | `desa` | `read-write` | `read-only` | `read-only` | `read-only` | `read-only` | `-` | `read-only` |
| `kecamatan-sekretaris` | `kecamatan` | `read-write` | `read-only` | `read-only` | `read-only` | `read-only` | `read-only` | `read-only` |
| `desa-pokja-i` | `desa` | `-` | `read-write` | `-` | `-` | `-` | `-` | `read-only` |
| `desa-pokja-ii` | `desa` | `-` | `-` | `read-write` | `-` | `-` | `-` | `read-only` |
| `desa-pokja-iii` | `desa` | `-` | `-` | `-` | `read-write` | `-` | `-` | `read-only` |
| `desa-pokja-iv` | `desa` | `-` | `-` | `-` | `-` | `read-write` | `-` | `read-only` |
| `kecamatan-pokja-i` | `kecamatan` | `-` | `read-write` | `-` | `-` | `-` | `-` | `read-only` |
| `kecamatan-pokja-ii` | `kecamatan` | `-` | `-` | `read-write` | `-` | `-` | `-` | `read-only` |
| `kecamatan-pokja-iii` | `kecamatan` | `-` | `-` | `-` | `read-write` | `-` | `-` | `read-only` |
| `kecamatan-pokja-iv` | `kecamatan` | `-` | `-` | `-` | `-` | `read-write` | `-` | `read-only` |

Catatan:
- `super-admin` bypass policy dan tidak dibatasi matrix ini.
- Role legacy (`admin-*`) dipertahankan sementara untuk kompatibilitas sampai migrasi role legacy selesai.

## Dashboard Representation Contract (Role-Aware)

Status kontrak:
- Aktif per 2026-02-23 untuk dashboard berbasis hak akses backend.

Payload utama:
- `dashboardBlocks[]` menjadi source utama representasi dashboard.
- Payload legacy (`dashboardStats`/`dashboardCharts`) hanya fallback transisi.

Struktur section sekretaris:
- Section 1: domain sekretaris (tanpa filter pokja).
- Section 2: pokja level aktif, filter query `section2_group` dengan opsi `all|pokja-i|pokja-ii|pokja-iii|pokja-iv`.
- Section 3: khusus scope kecamatan, pokja level bawah (desa turunan), filter query `section3_group` dengan opsi `all|pokja-i|pokja-ii|pokja-iii|pokja-iv`.
- Section 4 (skenario khusus): hanya untuk kecamatan saat `section3_group=pokja-i`, menampilkan rincian sumber data pokja I per desa.

Aturan role khusus:
- `desa-sekretaris`: default `level=desa`, tanpa kontrol `sub_level`, filter yang tampil hanya `section2_group`.
- `kecamatan-sekretaris`: tetap dapat mode bertingkat (`all|by-level|by-sub-level`) dengan filter section 2 dan section 3 yang independen.

Kontrak metadata sumber (anti label ambigu):
- `source_group`: `sekretaris-tpk|pokja-i|pokja-ii|pokja-iii|pokja-iv`.
- `source_scope`: `desa|kecamatan`.
- `source_area_type`: `area-sendiri|desa-turunan`.
- `source_modules`: daftar slug modul penyusun metrik.
- `filter_context`: wajib memuat token query aktif termasuk `section2_group` dan/atau `section3_group` sesuai section.
