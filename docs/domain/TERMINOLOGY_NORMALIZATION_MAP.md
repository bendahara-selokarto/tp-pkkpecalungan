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
| 4.14.1a | `data-warga` | Data Warga | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
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
| 4.15 | `catatan-keluarga` | Catatan Keluarga | UI/PDF sudah mengarah ke label pedoman | Pertahankan | match |
| Ekstensi 202-211 | `pilot-project-keluarga-sehat` | Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana | Label canonical sudah aktif pada menu/index/PDF | Pertahankan label canonical, lanjutkan pemetaan indikator detail katalog | implemented (catalog tahap awal) |

## Daftar Alias Terlarang (Konteks Menu/Index/PDF)

- `Laporan Koperasi` -> gunakan `Data Isian Koperasi`
- `Laporan Kejar Paket/KF/PAUD` -> gunakan `Data Isian Kejar Paket/KF/PAUD`
- `Laporan Posyandu` -> gunakan `Data Isian Posyandu oleh TP PKK`
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
- Ekstensi pedoman:
  - `docs/domain/PEDOMAN_DOMAIN_UTAMA_202_211.md`
