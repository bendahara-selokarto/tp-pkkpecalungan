# TODO Buku Sekretaris TP PKK (Bertahap)

Urutan prioritas disusun dari paling mudah (kontrak jelas, konflik rendah) sampai yang lebih asumtif.

## Selesai
- [x] Tahap 1 - Buku Agenda Surat (`agenda-surat`) untuk desa dan kecamatan.
- [x] Tahap 2 - Buku Ekspedisi Surat sebagai report turunan `agenda-surat` (filter `jenis_surat=keluar`) tanpa tabel baru.
- [x] Tahap 3 - Validasi ulang kolom laporan terhadap template PDF sumber per lembar.

## Berikutnya (prioritas)
- [x] Tahap 4 - Penyesuaian format cetak jika ada perbedaan istilah/header antar level (medium risk, tetap reuse domain existing).
- [ ] Tahap 5 - Modul baru yang belum ada padanan domain (high assumption, hanya jika tidak bisa dipenuhi lewat reuse domain yang sudah ada).
  - [ ] Refactor TODO sinkronisasi pedoman domain berdasarkan sumber canonical:
    - Sumber: https://pubhtml5.com/zsnqq/vjcf/basic/101-150
    - Fokus lampiran: 4.9a, 4.9b, 4.10, 4.11, 4.12, 4.13, 4.14.1a-4.14.4f, 4.15.
  - [ ] Sudah identik (aplikasi vs pedoman):
    - [x] 4.9a Buku Daftar Anggota Tim Penggerak PKK -> `anggota-tim-penggerak`.
    - [x] 4.10 Buku Agenda Surat -> `agenda-surat`.
    - [x] 4.12 Buku Inventaris -> `inventaris`.
    - [x] 4.13 Buku Kegiatan -> domain `kegiatan` (route teknis saat ini: `activities`).
    - [x] 4.14.4c Data Isian Koperasi -> `koperasi`.
    - [x] 4.14.4d Data Isian Kejar Paket -> `kejar-paket`.
    - [x] 4.14.4e Data Isian Posyandu oleh TP PKK -> `posyandu`.
    - [x] 4.14.4b Data Isian Taman Bacaan/Perpustakaan -> `taman-bacaan` (label domain sudah dinormalisasi).
  - [ ] TODO Ubah (sudah ada implementasi, tetapi belum identik pedoman):
    - [ ] 4.9b Buku Daftar Kader Tim Penggerak PKK: saat ini ditandai sebagai report gabungan anggota+kader; perlu diselaraskan menjadi buku kader sesuai lampiran.
    - [ ] 4.11 Buku Keuangan: saat ini report `bantuans` hanya menutup arus pemasukan; perlu sinkronisasi struktur buku keuangan pedoman.
    - [ ] 4.14.4a Data Aset (Sarana) Desa/Kelurahan: domain saat ini `warung-pkk`, perlu penamaan domain/label yang identik dengan lampiran.
    - [ ] 4.14.4f Data Isian Kelompok Simulasi dan Penyuluhan: domain saat ini `simulasi-penyuluhan`, perlu normalisasi istilah agar identik lampiran.
  - [ ] List Baru (belum ada modul/domain khusus):
    - [ ] 4.14.1a Data Warga.
    - [ ] 4.14.1b Data Kegiatan Warga.
    - [ ] 4.14.2a Data Keluarga.
    - [ ] 4.14.2b Data Pemanfaatan Tanah Pekarangan/HATINYA PKK.
    - [ ] 4.14.2c Data Industri Rumah Tangga.
    - [ ] 4.14.3 Data Pelatihan Kader.
    - [ ] 4.15 Catatan Keluarga (rekap dari 4.14.1a + 4.14.1b dan lampiran lain yang dirujuk pedoman).
    - [ ] Verifikasi apakah lampiran 4.14.5 ada pada sumber lanjutan pedoman; belum ditemukan pada baseline halaman 101-150.
  - [x] Normalisasi label level/area pada report kandidat existing (`bkl`, `bkr`, `simulasi-penyuluhan`, `program-prioritas`, `prestasi-lomba`).

## Catatan anti-konflik
- `agenda-surat` dipakai sebagai source of truth surat masuk/keluar.
- Buku ekspedisi tidak menambah tabel/domain baru untuk menghindari duplikasi data surat keluar.
