# TODO Implementasi Autentik Buku Bantuan (2026-02-24)

## Konteks
- Sumber autentik: `docs/referensi/excel/BUKU BANTU.xlsx`, sheet `Buku Bantuan`.
- Hasil baca text-layer XML sudah tersedia (header dan merge awal terdeteksi).
- Status saat ini: `siap sinkronisasi kontrak header` untuk sheet `Buku Bantuan`; implementasi masih ditahan sampai matrix mapping field selesai.

## Target Hasil
- Kontrak header final sheet `Buku Bantuan` tervalidasi sampai merge cell.
- Mapping kolom autentik ke field aplikasi terdokumentasi dan siap dipakai implementasi.
- Tersedia rencana patch minimal beserta validasi test.

## Langkah Eksekusi
- [x] Ambil screenshot valid area header tabel `Buku Bantuan`.
- [x] Finalisasi peta header:
  - `NO`
  - `TANGGAL`
  - `ASAL BANTUAN`
  - `JENIS BANTUAN` (`UANG`, `BARANG`)
  - `JUMLAH`
  - `LOKASI PENERIMA (SASARAN)`
  - `KETERANGAN`
- [ ] Susun matrix mapping `kolom autentik -> field input/storage/report`.
- [ ] Audit dampak implementasi ke layer:
  - route/request
  - use case/repository
  - policy/scope service
  - inertia page mapping
- [ ] Definisikan test matrix minimum untuk akses dan scoped data integrity.
- [ ] Lakukan doc-hardening jika ditemukan drift istilah/kontrak.

## Bukti Visual dan Peta Header Terkunci
- Bukti visual: screenshot user pada sesi 2026-02-24 untuk sheet `Buku Bantuan` (header utuh, garis sel terlihat, nomor kolom 1-8 terlihat, teks header terbaca).
- Merge range header tervalidasi:
  - `B3:I3` -> judul `BUKU BANTUAN`
  - `B5:B6` -> `NO`
  - `C5:C6` -> `TANGGAL`
  - `E5:F5` -> `JENIS BANTUAN`
  - `G5:G6` -> `JUMLAH`
  - `I5:I6` -> `KETERANGAN`
- Struktur header final:
  - Kolom 1: `NO`
  - Kolom 2: `TANGGAL`
  - Kolom 3: `ASAL BANTUAN` (dua baris label: `ASAL` / `BANTUAN`)
  - Kolom 4-5: `JENIS BANTUAN` -> `UANG`, `BARANG`
  - Kolom 6: `JUMLAH`
  - Kolom 7: `LOKASI PENERIMA (SASARAN)` (dua baris label)
  - Kolom 8: `KETERANGAN`

## Validasi
- [x] Bukti visual header valid tersedia.
- [x] Peta merge header final tidak ambigu.
- [ ] Mapping field disetujui untuk implementasi.
- [ ] Rencana test terdefinisi dan dapat dijalankan.

## Risiko
- `JENIS BANTUAN` ber-subkolom berpotensi salah map jika tidak dikunci visual.
- Perbedaan istilah lokal antar modul dapat memicu drift kontrak query/report.

## Keputusan
- [x] Sheet `Buku Bantuan` dipilih sebagai prioritas fase 1B.
- [x] Peta header visual `Buku Bantuan` tervalidasi dan dikunci sebagai kontrak.
- [x] Implementasi ditahan sampai matrix mapping field dan rencana test selesai.
