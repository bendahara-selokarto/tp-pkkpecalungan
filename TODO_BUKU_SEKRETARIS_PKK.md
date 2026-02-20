# TODO Buku Sekretaris TP PKK (Bertahap)

Urutan prioritas disusun dari paling mudah (kontrak jelas, konflik rendah) sampai yang lebih asumtif.

## Selesai
- [x] Tahap 1 - Buku Agenda Surat (`agenda-surat`) untuk desa dan kecamatan.
- [x] Tahap 2 - Buku Ekspedisi Surat sebagai report turunan `agenda-surat` (filter `jenis_surat=keluar`) tanpa tabel baru.

## Berikutnya (prioritas)
- [ ] Tahap 3 - Validasi ulang kolom laporan terhadap template PDF sumber per lembar (low risk, tanpa ubah domain).
- [ ] Tahap 4 - Penyesuaian format cetak jika ada perbedaan istilah/header antar level (medium risk, tetap reuse domain existing).
- [ ] Tahap 5 - Modul baru yang belum ada padanan domain (high assumption, hanya jika tidak bisa dipenuhi lewat reuse domain yang sudah ada).

## Catatan anti-konflik
- `agenda-surat` dipakai sebagai source of truth surat masuk/keluar.
- Buku ekspedisi tidak menambah tabel/domain baru untuk menghindari duplikasi data surat keluar.
