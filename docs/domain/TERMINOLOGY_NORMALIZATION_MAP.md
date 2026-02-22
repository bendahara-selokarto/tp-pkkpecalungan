# Terminology Normalization Map (Lampiran 4.9-4.15 + Ekstensi 202-211)

Sumber canonical:
- https://pubhtml5.com/zsnqq/vjcf/basic/101-150

Tujuan:
- Mengunci istilah canonical domain agar output UI/PDF koheren terhadap pedoman.
- Memisahkan istilah teknis (slug/table/model) dari istilah bisnis (label pedoman).

## Aturan Canonical

1. Konteks `slug/route/table/model` boleh memakai istilah teknis existing.
2. Konteks `menu sidebar`, `judul index module`, `judul PDF report` wajib memakai label pedoman canonical.
3. Konteks `aksi CRUD` (Tambah/Edit/Detail) boleh memakai label singkat entitas selama tidak mengubah label canonical pada menu dan PDF.
4. Jika ada konflik istilah, pedoman domain utama menjadi referensi final.

## Map Istilah Teknis vs Label Canonical

| Lampiran | Istilah teknis | Label canonical pedoman | Label saat ini (terdeteksi) | Normalisasi target | Status |
| --- | --- | --- | --- | --- | --- |
| 4.9a | `anggota-tim-penggerak` | Buku Daftar Anggota Tim Penggerak PKK | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.9b | `kader-khusus` | Buku Daftar Kader Tim Penggerak PKK | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.10 | `agenda-surat` | Buku Agenda Surat Masuk/Keluar | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.11 | `bantuans` | Buku Keuangan | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.12 | `inventaris` | Buku Inventaris | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.13 | `activities` | Buku Kegiatan | PDF judul utama sudah `BUKU KEGIATAN TP PKK` | Pertahankan | match |
| 4.14.1a | `data-warga` | Daftar Warga TP PKK | PDF sudah memakai judul autentik `Daftar Warga TP PKK`; menu/index masih mempertahankan label slug `Data Warga` | Pertahankan PDF autentik + evaluasi rename label menu/index di siklus UI terpisah agar tidak mengganggu kebiasaan operator | partial |
| 4.14.1b | `data-kegiatan-warga` | Data Kegiatan Warga | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.14.2a | `data-keluarga` | Data Keluarga | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.14.2b | `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | Data Pemanfaatan Tanah Pekarangan/HATINYA PKK | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.14.2c | `data-industri-rumah-tangga` | Data Industri Rumah Tangga | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.14.3 | `data-pelatihan-kader` | Data Pelatihan Kader | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.14.4a | `warung-pkk` | Data Aset (Sarana) Desa/Kelurahan | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.14.4b | `taman-bacaan` | Data Isian Taman Bacaan/Perpustakaan | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.14.4c | `koperasi` | Data Isian Koperasi | UI/PDF sudah memakai `Data Isian Koperasi` | Pertahankan | match |
| 4.14.4d | `kejar-paket` | Data Isian Kejar Paket/KF/PAUD | UI/PDF sudah memakai `Data Isian Kejar Paket/KF/PAUD` | Pertahankan | match |
| 4.14.4e | `posyandu` | Data Isian Posyandu oleh TP PKK | UI/PDF sudah memakai `Data Isian Posyandu oleh TP PKK` | Pertahankan | match |
| 4.14.4f | `simulasi-penyuluhan` | Data Isian Kelompok Simulasi dan Penyuluhan | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| 4.15 | `catatan-keluarga` | Catatan Keluarga | UI/PDF sudah mengarah ke label pedoman; struktur autentik 19 kolom diproyeksikan operasional ke report 10 kolom | Pertahankan label pedoman + kunci transformasi 19->10 di dokumen mapping domain | match (with projection) |
| 4.16a | `rekap-catatan-data-kegiatan-warga-dasawisma` | Rekapitulasi Catatan Data dan Kegiatan Warga Kelompok Dasa Wisma | Report PDF 29 kolom dengan merge-header autentik sudah tersedia pada flow `catatan-keluarga` (desa/kecamatan) | Pertahankan label autentik + kunci flow baca header tabel kompleks sebagai guardrail implementasi | implemented (report-only) |
| 4.16b | `rekap-catatan-data-kegiatan-warga-pkk-rt` | Rekapitulasi Catatan Data dan Kegiatan Warga Kelompok PKK RT | Report PDF 30 kolom dengan merge-header autentik sudah tersedia pada flow `catatan-keluarga` (desa/kecamatan) | Pertahankan label autentik + kunci flow baca header tabel kompleks sebagai guardrail implementasi | implemented (report-only) |
| 4.16c | `catatan-data-kegiatan-warga-pkk-rw` | Catatan Data dan Kegiatan Warga Kelompok PKK RW | Report PDF 32 kolom dengan merge-header autentik sudah tersedia pada flow `catatan-keluarga` (desa/kecamatan) | Pertahankan label autentik + jaga validasi merge-header 32 kolom sebagai kontrak | implemented (report-only) |
| 4.16d | `rekap-catatan-data-kegiatan-warga-rw` | Rekapitulasi Catatan Data dan Kegiatan Warga Kelompok PKK Desa/Kelurahan | Report PDF 33 kolom dengan merge-header autentik sudah tersedia pada flow `catatan-keluarga` (desa/kecamatan) | Pertahankan label implementasi saat ini, dan revisi jika token identitas final 4.16d berbeda | implemented (report-only, label final menunggu konfirmasi token identitas) |
| 4.17a | `catatan-data-kegiatan-warga-tp-pkk-desa-kelurahan` | Catatan Data dan Kegiatan Warga TP PKK Desa/Kelurahan | Report PDF 33 kolom dengan merge-header autentik sudah tersedia pada flow `catatan-keluarga` (desa/kecamatan) | Pertahankan label autentik + jaga validasi struktur kolom 1-33 untuk mencegah drift | implemented (report-only) |
| 4.17b | `catatan-data-kegiatan-warga-tp-pkk-kecamatan` | Catatan Data dan Kegiatan Warga TP PKK Kecamatan | Report PDF 35 kolom dengan merge-header autentik sudah tersedia pada flow `catatan-keluarga` (desa/kecamatan) | Pertahankan label autentik + jaga validasi struktur kolom 1-35 untuk mencegah drift | implemented (report-only) |
| 4.17c | `catatan-data-kegiatan-warga-tp-pkk-kabupaten-kota` | Catatan Data dan Kegiatan Warga TP PKK Kabupaten/Kota | Report PDF 36 kolom dengan merge-header autentik sudah tersedia pada flow `catatan-keluarga` (desa/kecamatan) | Pertahankan label autentik + jaga validasi struktur kolom 1-36 untuk mencegah drift | implemented (report-only) |
| 4.17d | `catatan-data-kegiatan-warga-tp-pkk-provinsi` | Catatan Data dan Kegiatan Warga TP PKK Provinsi | Report PDF 37 kolom dengan merge-header autentik sudah tersedia pada flow `catatan-keluarga` (desa/kecamatan) | Pertahankan label autentik + jaga validasi struktur kolom 1-37 untuk mencegah drift | implemented (report-only) |
| 4.18a | `rekap-ibu-hamil-melahirkan-dasawisma` | Rekapitulasi Data/Buku Catatan Ibu Hamil, Melahirkan, Nifas, Ibu Meninggal, Kelahiran Bayi, Bayi Meninggal dan Kematian Balita dalam Kelompok Dasawisma | Report PDF 17 kolom dengan merge-header autentik sudah tersedia pada flow `catatan-keluarga` (desa/kecamatan) | Pertahankan label autentik + jaga validasi struktur kolom 1-17 untuk mencegah drift | implemented (report-only) |
| 4.18b | `rekap-ibu-hamil-melahirkan-pkk-rt` | Rekapitulasi Data/Buku Catatan Ibu Hamil, Melahirkan, Nifas, Ibu Meninggal, Kelahiran Bayi, Bayi Meninggal dan Kematian Balita dalam Kelompok PKK RT | Report PDF 15 kolom dengan merge-header autentik sudah tersedia pada flow `catatan-keluarga` (desa/kecamatan) | Pertahankan label autentik + jaga validasi struktur kolom 1-15 untuk mencegah drift | implemented (report-only) |
| Ekstensi 202-211 | `pilot-project-keluarga-sehat` | Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana | Label canonical sudah aktif pada menu/index/PDF | Pertahankan label canonical, lanjutkan pemetaan indikator detail katalog | implemented (catalog tahap awal) |

## Daftar Alias Terlarang (Konteks Menu/Index/PDF)

- `Laporan Koperasi` -> gunakan `Data Isian Koperasi`
- `Laporan Kejar Paket/KF/PAUD` -> gunakan `Data Isian Kejar Paket/KF/PAUD`
- `Laporan Posyandu` -> gunakan `Data Isian Posyandu oleh TP PKK`
- `Data Warga` (sebagai judul modul/PDF 4.14.1a) -> gunakan `Daftar Warga TP PKK`
- `Koperasi Desa/Kecamatan` (sebagai judul modul) -> gunakan `Data Isian Koperasi Desa/Kecamatan`
- `Kejar Paket Desa/Kecamatan` (sebagai judul modul) -> gunakan `Data Isian Kejar Paket/KF/PAUD Desa/Kecamatan`
- `Posyandu Desa/Kecamatan` (sebagai judul modul) -> gunakan `Data Isian Posyandu oleh TP PKK Desa/Kecamatan`
- `Pilot Project` (label tunggal tanpa konteks) -> gunakan `Laporan Pilot Project Keluarga Sehat`
- `Laporan Pilot Project Bencana` -> gunakan `Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana`

## Jejak Verifikasi

- Sidebar/menu label:
  - `resources/js/Layouts/DashboardLayout.vue`
- Judul halaman modul:
  - `resources/js/Pages/Desa/Koperasi/Index.vue`
  - `resources/js/Pages/Kecamatan/Koperasi/Index.vue`
  - `resources/js/Pages/Desa/KejarPaket/Index.vue`
  - `resources/js/Pages/Kecamatan/KejarPaket/Index.vue`
  - `resources/js/Pages/Desa/Posyandu/Index.vue`
  - `resources/js/Pages/Kecamatan/Posyandu/Index.vue`
- Judul PDF:
  - `resources/views/pdf/koperasi_report.blade.php`
  - `resources/views/pdf/kejar_paket_report.blade.php`
  - `resources/views/pdf/posyandu_report.blade.php`
- Mapping Catatan Keluarga:
  - `docs/domain/CATATAN_KELUARGA_19_TO_10_MAPPING.md`
- Mapping Rekap Dasa Wisma:
  - `docs/domain/REKAP_DASA_WISMA_4_16A_MAPPING.md`
- Mapping Rekap PKK RT:
  - `docs/domain/REKAP_PKK_RT_4_16B_MAPPING.md`
- Mapping Catatan PKK RW:
  - `docs/domain/CATATAN_PKK_RW_4_16C_MAPPING.md`
- Mapping Lampiran 4.16d:
  - `docs/domain/LAMPIRAN_4_16D_MAPPING.md`
- Mapping Catatan TP PKK 4.17a:
  - `docs/domain/CATATAN_TP_PKK_DESA_KELURAHAN_4_17A_MAPPING.md`
- Mapping Catatan TP PKK 4.17b:
  - `docs/domain/CATATAN_TP_PKK_KECAMATAN_4_17B_MAPPING.md`
- Mapping Catatan TP PKK 4.17c:
  - `docs/domain/CATATAN_TP_PKK_KABUPATEN_KOTA_4_17C_MAPPING.md`
- Mapping Catatan TP PKK 4.17d:
  - `docs/domain/CATATAN_TP_PKK_PROVINSI_4_17D_MAPPING.md`
- Mapping Rekap Ibu Hamil 4.18a:
  - `docs/domain/REKAP_IBU_HAMIL_DASAWISMA_4_18A_MAPPING.md`
- Mapping Rekap Ibu Hamil 4.18b:
  - `docs/domain/REKAP_IBU_HAMIL_PKK_RT_4_18B_MAPPING.md`
- Ekstensi pedoman:
  - `docs/domain/PEDOMAN_DOMAIN_UTAMA_202_211.md`
