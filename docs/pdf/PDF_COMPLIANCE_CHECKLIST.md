# PDF Compliance Checklist (Lampiran 4.9-4.24)

Tujuan:
- Menjadi checklist baku verifikasi output PDF agar identik dengan pedoman domain utama.
- Menjadi baseline regression untuk perubahan header, urutan kolom, format nilai, orientasi, dan metadata cetak.

Sumber acuan:
- Pedoman domain utama: https://pubhtml5.com/zsnqq/vjcf/basic/101-150
- Validasi awal format: `docs/pdf/VALIDASI_FORMAT_BUKU_SEKRETARIS_PDF.md`
- Kontrak domain: `docs/domain/DOMAIN_CONTRACT_MATRIX.md`

## A. Checklist Global (Wajib untuk semua modul)

| Item | Kriteria lulus |
| --- | --- |
| Judul report | Harus sama dengan label pedoman pada lampiran terkait (huruf besar/kecil boleh berbeda, makna tidak boleh berubah). |
| Header tabel | Semua kolom wajib ada; urutan kolom harus sesuai pedoman. |
| Urutan data | Nomor urut dan urutan baris stabil sesuai aturan modul (umumnya sort by `id`/tanggal). |
| Format nilai | Tanggal, angka, boolean, dan teks kosong harus konsisten (`-`/`Ya-Tidak`/format tanggal). |
| Orientasi PDF | Default `landscape` (sesuai `PdfViewFactory`); `portrait` hanya jika diminta eksplisit. |
| Footer metadata cetak | Wajib ada informasi minimal: area, dicetak oleh, dicetak pada (lokasi boleh header/footer selama konsisten). |
| Scope output | Sample PDF `desa` dan `kecamatan` harus menampilkan data sesuai scope area user. |

## B. Checklist Per Modul

Keterangan status:
- `pending`: belum divalidasi pada siklus ini.
- `pass`: sudah diverifikasi sesuai pedoman.
- `fail`: ada mismatch yang harus diperbaiki.

Siklus validasi terbaru:
- `2026-02-21`: validasi otomatis lulus via:
  - `php artisan test --filter=PdfBaselineFixtureComplianceTest`
  - `php artisan test --filter=header_kolom_pdf`
  - `php artisan test --filter=ReportPrintTest`
- `2026-02-22`: sinkronisasi autentik 4.20b selesai, validasi header + route print ter-cover pada test feature `RekapCatatanDataKegiatanWargaReportPrintTest`.
- `2026-02-22`: sinkronisasi autentik 4.23-4.24 selesai, validasi header + agregasi + route print ter-cover pada test feature `RekapCatatanDataKegiatanWargaReportPrintTest`.
- `2026-02-21`: dokumen autentik 4.14.1a (`d:\\pedoman\\153.pdf`) mengubah baseline acuan menjadi format detail anggota kolom 1-20; status modul 4.14.1a ditandai `fail` sampai penyesuaian selesai.
- `2026-02-21`: penyesuaian 4.14.1a selesai, PDF `data-warga` sudah memakai judul autentik `DAFTAR WARGA TP PKK`, header rumah tangga, kolom detail anggota, dan orientasi `portrait` eksplisit.
- `2026-02-22`: sinkronisasi autentik 4.9a-4.14.4b (batch sekretaris/pokja) selesai untuk layer PDF + fixture; modul yang belum punya field penuh tetap ditandai `partial` pada kontrak domain.

| Lampiran | Modul | File PDF | Acuan urutan kolom/header | Format nilai kritikal | Orientasi | Footer metadata cetak | Status |
| --- | --- | --- | --- | --- | --- | --- | --- |
| 4.9a | `anggota-tim-penggerak` | `resources/views/pdf/anggota_tim_penggerak_report.blade.php` | Header autentik 11 kolom + baris nomor kolom (`1-11`) | Tanggal lahir/umur valid; field kosong jadi `-` | `landscape` | area + printedBy + printedAt | `pass` |
| 4.9b | `kader-khusus` | `resources/views/pdf/kader_khusus_report.blade.php` | Header autentik 13 kolom dengan group `KEDUDUKAN/FUNGSI` + baris nomor (`1-13`) | Kolom yang belum tersedia di domain tampil mode kompatibilitas (`-`) | `landscape` | area + printedBy + printedAt | `pass` |
| 4.10 | `agenda-surat` | `resources/views/pdf/agenda_surat_report.blade.php` | Group `SURAT MASUK` (8 kolom) + `SURAT KELUAR` (7 kolom), subheader `TANGGAL`, baris nomor (`1-15`) | Tanggal surat/terima konsisten; nomor surat tampil | `landscape` | area + printedBy + printedAt | `pass` |
| 4.11 | `buku-keuangan` | `resources/views/pdf/buku_keuangan_report.blade.php` | Layout autentik dua blok 12 kolom (`1-12`) model buku tabungan | Nominal masuk/keluar dan total konsisten terhadap sumber `entries` | `landscape` | area + printedBy + printedAt | `pass` |
| 4.12 | `inventaris` | `resources/views/pdf/inventaris_report.blade.php` | Header autentik 8 kolom + baris nomor (`1-8`) | Angka jumlah valid; kondisi sesuai enum | `landscape` | area + printedBy + printedAt | `pass` |
| 4.13 | `activities` | `resources/views/pdf/activity.blade.php` | Group header `KEGIATAN` (Tanggal/Tempat/Uraian) + baris nomor (`1-7`) | Tanggal kegiatan valid; uraian/tanda tangan tidak kosong | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.1a | `data-warga` | `resources/views/pdf/data_warga_report.blade.php` | Judul autentik `DAFTAR WARGA TP PKK`; header rumah tangga `Dasa Wisma` + `Nama Kepala Rumah Tangga`; kolom detail anggota 1-20 (`No. Registrasi` s.d. `Ikut dalam Kegiatan Koperasi`) | Validasi detail identitas anggota, status partisipasi, dan konsistensi umur/tanggal | `portrait` (eksplisit untuk lampiran autentik 4.14.1a) | area + printedBy + printedAt | `pass` |
| 4.14.1b | `data-kegiatan-warga` | `resources/views/pdf/data_kegiatan_warga_report.blade.php` | 7 baris kegiatan baku autentik + kolom `Aktivitas (Y/T)` + `Keterangan` | Aktivitas tampil `Y/T` sesuai data kegiatan | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.2a | `data-keluarga` | `resources/views/pdf/data_keluarga_report.blade.php` | Mode operasional summary (`No/Kategori/Jumlah/Keterangan`) | Nilai jumlah numerik non-negatif | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.2b | `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | `resources/views/pdf/data_pemanfaatan_tanah_pekarangan_hatinya_pkk_report.blade.php` | Header autentik ringkas `No/Kategori/Komoditi/Jumlah` + baris nomor (`1-4`) | Nilai jumlah konsisten satuan | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.2c | `data-industri-rumah-tangga` | `resources/views/pdf/data_industri_rumah_tangga_report.blade.php` | No, Kategori Industri, Komoditi, Jumlah | Nilai jumlah konsisten satuan | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.3 | `data-pelatihan-kader` | `resources/views/pdf/data_pelatihan_kader_report.blade.php` | No Reg, Nama, Tgl/Th Masuk, Jabatan, No Urut, Judul, Kriteria, Tahun, Institusi, Sertifikat | Tahun numerik; status sertifikat valid | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4a | `warung-pkk` | `resources/views/pdf/warung_pkk_report.blade.php` | No, Nama Warung, Pengelola, Komoditi, Kategori, Volume | Volume/kategori tidak kosong | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4b | `taman-bacaan` | `resources/views/pdf/taman_bacaan_report.blade.php` | Layout formulir autentik (identitas taman bacaan) + tabel 4 kolom (`No/Jenis Buku/Katagori/Jumlah`) | Pengelompokan per taman bacaan konsisten; total jumlah tampil | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4c | `koperasi` | `resources/views/pdf/koperasi_report.blade.php` | Header merge autentik: No, Nama Koperasi, Jenis Usaha, group `Status Hukum` (Berbadan Hukum/Blm. Berbadan Hukum), group `Jumlah Anggota` (L/P) | Boolean tampil `Ya/-`; jumlah anggota numerik | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4d | `kejar-paket` | `resources/views/pdf/kejar_paket_report.blade.php` | Header merge autentik operasional: No, Nama, Jenis Kejar Paket/KF/PAUD, group `Jumlah Warga Belajar/Siswa` (L/P), group `Jumlah Pengajar` (L/P) | Nilai L/P numerik | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4e | `posyandu` | `resources/views/pdf/posyandu_report.blade.php` | Identitas Posyandu di blok metadata; tabel 8 kolom: No, Jenis Kegiatan/Layanan, Frekuensi Layanan, group `Jumlah` (`Pengunjung` L/P + `Petugas/Paramedis` L/P), Keterangan | Seluruh angka kuantitatif numerik + `keterangan` opsional | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4f | `simulasi-penyuluhan` | `resources/views/pdf/simulasi_penyuluhan_report.blade.php` | Header merge autentik: No, Nama Kegiatan, Jenis Simulasi/Penyuluhan, group `Jumlah` (Kelompok/Sosialisasi), group `Jumlah Kader` (L/P) | Nilai jumlah numerik | `landscape` | area + printedBy + printedAt | `pass` |
| 4.15 | `catatan-keluarga` | `resources/views/pdf/catatan_keluarga_report.blade.php` | No, Nama KRT, Jml ART, Kerja Bakti, Rukun Kematian, Keagamaan, Jimpitan, Arisan, Lain-lain, Ket. | Nilai kegiatan tampil `Ya/Tidak` | `landscape` | area + printedBy + printedAt | `pass` |
| 4.20a | `data-umum-pkk` | `resources/views/pdf/data_umum_pkk_report.blade.php` | Header merge 20 kolom (`NO` s.d. `KETERANGAN`) sesuai mapping autentik 4.20a | Nilai agregasi numerik per dusun/lingkungan; fallback teks `SEBUTAN LAIN` untuk sumber label kosong | `landscape` | level + printedBy + printedAt | `pass` |
| 4.20b | `data-umum-pkk-kecamatan` | `resources/views/pdf/data_umum_pkk_kecamatan_report.blade.php` | Header merge 21 kolom (`NO` s.d. `KETERANGAN`) sesuai mapping autentik 4.20b | Nilai agregasi numerik per desa/kelurahan; fallback teks `SEBUTAN LAIN` untuk sumber label kosong | `landscape` | level + printedBy + printedAt | `pass` |
| 4.23 | `data-kegiatan-pkk-pokja-iii` | `resources/views/pdf/data_kegiatan_pkk_pokja_iii_report.blade.php` | Header merge 20 kolom (`NO` s.d. `KETERANGAN`) sesuai mapping autentik 4.23 | Agregasi lintas modul Pokja III (kader pangan/sandang/tata laksana, pemanfaatan pekarangan, industri, indikator rumah) dengan fallback nilai `0` untuk data yang belum dedicated | `landscape` | level + printedBy + printedAt | `pass` |
| 4.24 | `data-kegiatan-pkk-pokja-iv` | `resources/views/pdf/data_kegiatan_pkk_pokja_iv_report.blade.php` | Header merge 27 kolom (`NO` s.d. `PERENCANAAN SEHAT`) sesuai mapping autentik 4.24 | Agregasi lintas modul Pokja IV (kader kesehatan, posyandu, indikator lingkungan, perencanaan sehat, program unggulan) dengan inferensi keyword terkontrol | `landscape` | level + printedBy + printedAt | `pass` |

## C. Prosedur Eksekusi Checklist

1. Generate sample PDF `desa` dan `kecamatan` untuk modul yang diuji.
2. Cocokkan judul + header + urutan kolom dengan pedoman.
3. Cocokkan format nilai (tanggal/angka/boolean/empty).
4. Verifikasi orientasi output adalah `landscape` (default).
5. Verifikasi metadata cetak (area, printedBy, printedAt) tersedia.
6. Catat hasil `pass/fail` dan deviasi ke `docs/domain/DOMAIN_DEVIATION_LOG.md` jika ada.

## D. Audit Trail Sumber Data PDF

Tujuan:
- Menjaga agar semua laporan PDF bertabel mengambil data dari tabel database yang tepat sesuai boundary domain.
- Menyediakan jejak audit yang bisa diulang pada siklus berikutnya.

### Riwayat Audit

| Tanggal | Cakupan | Metode | Ringkasan hasil | Status |
| --- | --- | --- | --- | --- |
| 2026-02-22 | Semua endpoint print/report PDF (`desa` + `kecamatan`) | Route scan -> mapping `PrintController -> UseCase -> Repository -> Model -> Table` -> verifikasi tabel eksis | 90 route print/report teraudit, 46 view PDF teraudit, tidak ada mismatch tabel sumber data, tidak ditemukan query domain langsung di print controller | `pass` |

### Ringkasan Baseline Audit 2026-02-22

- Total route print/report yang diaudit: `90`.
- Total view PDF unik dari print controller: `46`.
- Seluruh tabel sumber data report yang dipakai repository terverifikasi `OK` (schema exists).
- Tidak ditemukan penggunaan tabel legacy compatibility (`kecamatans`, `desas`, `user_assignments`) pada jalur print PDF.

Catatan penting:
- Domain `catatan-keluarga` memakai model marker policy (`CatatanKeluarga`) untuk otorisasi, namun sumber report berasal dari tabel operasional lintas modul.
- Report `ekspedisi` berasal dari tabel `agenda_surats` dengan filter `jenis_surat = keluar` (bukan tabel ekspedisi terpisah).

### Baseline Mapping Domain -> Tabel Sumber

| Domain report | View PDF | Tabel sumber utama |
| --- | --- | --- |
| Activity | `pdf.activity` | `activities` |
| Agenda Surat + Ekspedisi | `pdf.agenda_surat_report`, `pdf.ekspedisi_surat_report` | `agenda_surats` |
| Anggota Pokja | `pdf.anggota_pokja_report` | `anggota_pokjas` |
| Anggota Tim Penggerak | `pdf.anggota_tim_penggerak_report` | `anggota_tim_penggeraks` |
| Anggota + Kader gabungan | `pdf.anggota_dan_kader_report` | `anggota_tim_penggeraks`, `kader_khusus` |
| Bantuan | `pdf.bantuan_report` | `bantuans` |
| Buku Keuangan | `pdf.buku_keuangan_report` | `buku_keuangans` |
| BKL | `pdf.bkl_report` | `bkls` |
| BKR | `pdf.bkr_report` | `bkrs` |
| Inventaris | `pdf.inventaris_report` | `inventaris` |
| Kader Khusus | `pdf.kader_khusus_report` | `kader_khusus` |
| Kejar Paket | `pdf.kejar_paket_report` | `kejar_pakets` |
| Koperasi | `pdf.koperasi_report` | `koperasis` |
| Posyandu | `pdf.posyandu_report` | `posyandus` |
| Prestasi Lomba | `pdf.prestasi_lomba_report` | `prestasi_lombas` |
| Program Prioritas | `pdf.program_prioritas_report` | `program_prioritas` |
| Simulasi Penyuluhan | `pdf.simulasi_penyuluhan_report` | `simulasi_penyuluhans` |
| Taman Bacaan | `pdf.taman_bacaan_report` | `taman_bacaans` |
| Warung PKK | `pdf.warung_pkk_report` | `warung_pkks` |
| Data Warga | `pdf.data_warga_report` | `data_wargas`, `data_warga_anggotas` |
| Data Kegiatan Warga | `pdf.data_kegiatan_warga_report` | `data_kegiatan_wargas` |
| Data Keluarga | `pdf.data_keluarga_report` | `data_keluargas` |
| Data Industri Rumah Tangga | `pdf.data_industri_rumah_tangga_report` | `data_industri_rumah_tanggas` |
| Data Pelatihan Kader | `pdf.data_pelatihan_kader_report` | `data_pelatihan_kaders` |
| Data Pemanfaatan Tanah Pekarangan/Hatinya PKK | `pdf.data_pemanfaatan_tanah_pekarangan_hatinya_pkk_report` | `data_pemanfaatan_tanah_pekarangan_hatinya_pkks` |
| Pilot Project Keluarga Sehat | `pdf.pilot_project_keluarga_sehat_report` | `pilot_project_keluarga_sehat_reports`, `pilot_project_keluarga_sehat_values` |
| Pilot Project Naskah Pelaporan | `pdf.pilot_project_naskah_pelaporan_report` | `pilot_project_naskah_pelaporan_reports`, `pilot_project_naskah_pelaporan_attachments` |
| Catatan Keluarga + seluruh turunan 4.15-4.24 | `pdf.catatan_keluarga_report` + seluruh view `catatan_*`, `rekap_*`, `data_umum_*`, `data_kegiatan_pkk_pokja_*` | `data_wargas`, `data_warga_anggotas`, `data_kegiatan_wargas`, `data_pemanfaatan_tanah_pekarangan_hatinya_pkks`, `data_industri_rumah_tanggas`, `anggota_tim_penggeraks`, `anggota_pokjas`, `kader_khusus`, `posyandus`, `program_prioritas`, `areas` |

### Template Audit Berikutnya (Wajib Isi)

Isi baris baru pada tabel `Riwayat Audit` dengan format:
- Tanggal audit (`YYYY-MM-DD`)
- Cakupan route/fitur
- Metode verifikasi
- Ringkasan hasil (angka route/view + mismatch/temuan)
- Status (`pass`/`fail`)

Checklist run minimum sebelum mengisi riwayat:
1. `php artisan route:list --json` lalu filter route `report/pdf` dan `print`.
2. Mapping `loadView('pdf.*')` dari seluruh `*PrintController.php`.
3. Verifikasi chain `UseCase -> Repository`.
4. Verifikasi query repository (`::query()`) dan tabel model (`getTable()`).
5. Validasi tabel eksis (`Schema::hasTable`).
6. Catat mismatch jika ada ke `docs/domain/DOMAIN_DEVIATION_LOG.md`.


