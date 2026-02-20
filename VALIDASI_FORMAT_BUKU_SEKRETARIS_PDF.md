# Validasi Format Buku Sekretaris TP PKK

Sumber validasi:
- `d:\Buku kegiatan sekretaris (1).pdf` (66 halaman)

## Ringkasan hasil tahap 3

1. `LAMPIRAN 4.9a` (hal. 1-2) - **Buku Daftar Anggota Tim Penggerak PKK**
- Status: `match` dengan modul `anggota-tim-penggerak`.
- File report: `resources/views/pdf/anggota_tim_penggerak_report.blade.php`.

2. `LAMPIRAN 4.10` (hal. 5-6) - **Buku Agenda Surat Masuk/Keluar**
- Status: `disesuaikan` agar struktur tabel mengikuti format masuk/keluar.
- Perubahan:
  - Judul menjadi `BUKU AGENDA SURAT MASUK/KELUAR`.
  - Header tabel dipecah menjadi grup `SURAT MASUK` dan `SURAT KELUAR`.
  - Nomor urut dipisah untuk surat masuk dan surat keluar.
- File report: `resources/views/pdf/agenda_surat_report.blade.php`.

3. `LAMPIRAN 4.12` (hal. 9-10) - **Buku Inventaris**
- Status: `match` untuk kolom utama.
- Penyesuaian: normalisasi judul dari "Laporan" menjadi `BUKU INVENTARIS`.
- File report: `resources/views/pdf/inventaris_report.blade.php`.

4. `LAMPIRAN 4.13` (hal. 11-12) - **Buku Kegiatan**
- Status: `match` dengan struktur kolom referensi.
- File report: `resources/views/pdf/activity.blade.php`.

5. `LAMPIRAN 4.14.4f` (hal. 35-36) - **Kelompok Simulasi dan Penyuluhan**
- Status: `match` untuk kolom inti.
- File report: `resources/views/pdf/simulasi_penyuluhan_report.blade.php`.

## Update tahap 4 (istilah/header antar level)

- Status: `selesai` untuk report sekretaris utama.
- Penyesuaian:
  - Suffix level pada judul report diubah menjadi label canonical:
    - `DESA/KELURAHAN` untuk scope desa.
    - `KECAMATAN` untuk scope kecamatan.
  - Label metadata area di report menyesuaikan scope:
    - `Desa/Kelurahan: ...` atau `Kecamatan: ...`.
- File terdampak:
  - `resources/views/pdf/anggota_tim_penggerak_report.blade.php`
  - `resources/views/pdf/agenda_surat_report.blade.php`
  - `resources/views/pdf/ekspedisi_surat_report.blade.php`
  - `resources/views/pdf/inventaris_report.blade.php`
  - `resources/views/pdf/activity.blade.php`

## Item yang belum tercover penuh (lebih asumtif)

1. `LAMPIRAN 4.11` (hal. 7-8) - Buku Tabungan/Keuangan.
- Belum ada modul domain khusus buku keuangan.

2. `LAMPIRAN 4.9b` (hal. 3-4) - Daftar anggota TP PKK dan kader (format gabungan).
- Sudah ada modul terkait anggota/kader, namun belum ada satu report gabungan persis format lampiran.

3. `LAMPIRAN 4.14.4a` s.d. `4.14.4e` (hal. 25-34) dan lampiran lanjutan lain.
- Butuh pemetaan domain tambahan agar tidak terjadi duplikasi dengan modul existing.
