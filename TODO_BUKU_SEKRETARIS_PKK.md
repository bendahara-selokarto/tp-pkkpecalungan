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
  - [ ] 4.14.4a s.d. 4.14.4e dan lampiran lanjutan (pengerjaan bertahap).
    - [x] Verifikasi pemetaan final lampiran 25-34 dari PDF sumber:
      - 4.14.4a `Warung PKK`
      - 4.14.4b `Taman Bacaan/Perpustakaan`
      - 4.14.4c `Koperasi`
      - 4.14.4d `Kejar Paket/KF/PAUD`
      - 4.14.4e `Posyandu`
    - [x] Normalisasi label level/area pada report kandidat existing (`bkl`, `bkr`, `simulasi-penyuluhan`, `program-prioritas`, `prestasi-lomba`).
    - [x] Implementasi domain + report `4.14.4a Warung PKK`.
    - [x] Implementasi domain + report `4.14.4b Taman Bacaan`.
    - [x] Implementasi domain + report `4.14.4c Koperasi`.
    - [ ] Implementasi domain + report `4.14.4d Kejar Paket/KF/PAUD`.
    - [ ] Implementasi domain + report `4.14.4e Posyandu`.

## Catatan anti-konflik
- `agenda-surat` dipakai sebagai source of truth surat masuk/keluar.
- Buku ekspedisi tidak menambah tabel/domain baru untuk menghindari duplikasi data surat keluar.
