# TODO Buku Sekretaris TP PKK (Bertahap)

Urutan prioritas disusun dari paling mudah (kontrak jelas, konflik rendah) sampai yang lebih asumtif.

## Selesai
- [x] Tahap 1 - Buku Agenda Surat (`agenda-surat`) untuk desa dan kecamatan.
- [x] Tahap 2 - Buku Ekspedisi Surat sebagai report turunan `agenda-surat` (filter `jenis_surat=keluar`) tanpa tabel baru.
- [x] Tahap 3 - Validasi ulang kolom laporan terhadap template PDF sumber per lembar.

## Berikutnya (prioritas)
- [x] Tahap 4 - Penyesuaian format cetak jika ada perbedaan istilah/header antar level (medium risk, tetap reuse domain existing).
- [ ] Tahap 5 - Modul baru yang belum ada padanan domain (high assumption, hanya jika tidak bisa dipenuhi lewat reuse domain yang sudah ada).
  - [x] 4.9b report gabungan anggota TP PKK + kader dibuat dengan reuse domain existing (tanpa tabel baru).
  - [x] 4.11 buku tabungan/keuangan dibuat sebagai report turunan domain `bantuans` (kategori uang/keuangan).
  - [ ] Sinkronisasi checklist domain berdasarkan pedoman halaman 101-150.
    - Sumber: https://pubhtml5.com/zsnqq/vjcf/basic/101-150
    - [ ] 4.14.1a (domain baru, belum dipetakan ke modul existing).
    - [ ] 4.14.1b (domain baru, belum dipetakan ke modul existing).
    - [ ] 4.14.2a (domain baru, belum dipetakan ke modul existing).
    - [ ] 4.14.2b (domain baru, belum dipetakan ke modul existing).
    - [ ] 4.14.3a (domain baru, belum dipetakan ke modul existing).
    - [ ] 4.14.3b (domain baru, belum dipetakan ke modul existing).
    - [x] 4.14.4a `Warung PKK`.
    - [x] 4.14.4b `Taman Bacaan/Perpustakaan`.
    - [x] 4.14.4c `Koperasi`.
    - [x] 4.14.4d `Kejar Paket/KF/PAUD`.
    - [x] 4.14.4e `Posyandu`.
    - [x] 4.14.4f `Kelompok Simulasi dan Penyuluhan`.
    - [ ] 4.14.5a (belum dipetakan final ke modul existing).
    - [ ] 4.14.5b (belum dipetakan final ke modul existing).
    - [ ] 4.14.5c (belum dipetakan final ke modul existing).
    - [ ] 4.15 (domain baru, belum dipetakan ke modul existing).
    - [x] Normalisasi label level/area pada report kandidat existing (`bkl`, `bkr`, `simulasi-penyuluhan`, `program-prioritas`, `prestasi-lomba`).

## Catatan anti-konflik
- `agenda-surat` dipakai sebagai source of truth surat masuk/keluar.
- Buku ekspedisi tidak menambah tabel/domain baru untuk menghindari duplikasi data surat keluar.
