# TODO PDF Chart Academic Style SVG Implementation

Status: `completed`  
Tanggal: 2026-05-24  
Tujuan: Mengganti render grafik PDF berbasis CSS dengan generator SVG murni untuk mencapai estetika "akademik manual" dengan keterbacaan tinggi.

## Latar Belakang
Laporan "Buku Grafik" membutuhkan visualisasi yang formal, tegas, dan tajam saat dicetak. Grafik berbasis CSS memiliki keterbatasan dalam hal presisi grid, tick marks, dan kualitas vektor saat di-zoom. Implementasi ini menggunakan SVG (Scalable Vector Graphics) yang di-generate langsung dari sisi server (PHP).

## Target Implementasi Pertama: Buku Grafik (Data Umum PKK)
Visualisasi dari Lampiran 4.20a/4.20b yang mencakup:
- Jumlah Kelompok (PKK RW, PKK RT, Dasa Wisma).
- Jumlah Rumah Tangga (KRT, KK).
- Jumlah Jiwa (Laki-laki, Perempuan).
- Jumlah Kader (TP PKK, Umum, Khusus).
- Tenaga Sekretariat (Honorer, Bantuan).

## Spesifikasi Visual (Gaya Akademik Manual)
- **Latar Belakang**: Putih polos (`#ffffff`).
- **Sumbu X & Y**: Hitam pekat, ketebalan 2px, lengkap dengan tick marks.
- **Grid Y**: Garis horizontal abu-abu muda (`#eeeeee`), ketebalan 1px.
- **Balok (Bars)**:
  - Warna solid gelap (skala abu-abu kontras tinggi).
  - Border hitam 1px yang tegas.
  - Jarak antar balok proporsional.
- **Teks & Angka**: Font standar (Arial/Helvetica), ukuran besar, posisi rapi.
- **Format**: Vector SVG murni (bukan raster image).

## Rencana Aksi

### 1. Fondasi Service
- [x] Register dokumen proses.
- [x] Buat `app/Support/Pdf/AcademicChartPdfService.php`.
- [x] Implementasi logika hitung skala (max value & ticks).
- [x] Implementasi template SVG (axes, grids, bars, labels).

### 2. Integrasi Backend
- [x] Update `app/Http/Controllers/DashboardController.php`.
- [x] Inject `AcademicChartPdfService`.
- [x] Transform `dataUmumCharts` menjadi string SVG.

### 3. Pembaruan View PDF
- [x] Update `resources/views/pdf/dashboard_chart_report.blade.php`.
- [x] Hapus logika CSS/HTML bar lama.
- [x] Render variabel SVG secara langsung.

### 4. Validasi & Standarisasi
- [x] Uji ekspor PDF dengan berbagai skala data (kecil vs besar).
- [x] Verifikasi ketajaman vektor pada zoom 400%.
- [x] Pastikan sinkronisasi label dengan kolom Data Umum PKK.

## Referensi Terkait
- `docs/domain/DATA_UMUM_PKK_4_20A_MAPPING.md`
- `docs/domain/DATA_UMUM_PKK_4_20B_MAPPING.md`
- `app/Domains/Wilayah/Dashboard/UseCases/BuildDataUmumChartPayloadUseCase.php`

## Temuan & Perbaikan Sistemik (Post-Implementation)

Selama proses implementasi, ditemukan masalah pada `CatatanKeluargaRepository` di mana query rekapitulasi level `desa` oleh user level `kecamatan` tidak secara otomatis melakukan agregasi ke seluruh desa di bawahnya. 

### Perbaikan:
1. **Repository Hierarchy Aware**: Memperbarui `scopedModelQuery` di `CatatanKeluargaRepository` agar mendeteksi jika user level Kecamatan sedang mengakses data level Desa, maka query akan otomatis menggunakan `whereIn` pada seluruh `area_id` desa di bawah kecamatan tersebut.
2. **Impact**: Perbaikan ini berdampak positif tidak hanya pada Buku Grafik, tetapi pada seluruh akurasi laporan rekapitulasi (4.15 s.d 4.24) di modul Catatan Keluarga untuk user level Kecamatan.
3. **Data Demo**: Dibuat `BukuGrafikDemoSeeder` (terdaftar di `.gitignore`) untuk memverifikasi agregasi multi-desa ini secara empiris.
