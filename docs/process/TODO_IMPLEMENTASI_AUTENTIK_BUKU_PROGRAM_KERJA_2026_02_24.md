# TODO Implementasi Autentik Buku Program Kerja (2026-02-24)

## Konteks
- Sumber autentik: `docs/referensi/excel/Buku Wajib Pokja I.xlsx` (sheet `Buku Rencana Program` / judul visual `BUKU PROGRAM KERJA`).
- Pada sesi 2026-02-24 user memberikan screenshot visual header tabel yang memenuhi syarat validasi (header utuh, garis sel terlihat, teks terbaca, nomor kolom terlihat).
- Implementasi existing domain `ProgramPrioritas` saat ini masih memakai jadwal triwulan (`jadwal_i` s.d. `jadwal_iv`), sementara format autentik memakai 12 kolom jadwal bulanan.

## Target Hasil
- Kontrak header autentik `Buku Program Kerja` terkunci sampai level grup subkolom.
- Gap kontrak autentik vs skema existing terdokumentasi jelas.
- Rencana patch minimal untuk migrasi dari jadwal 4 kolom ke 12 kolom siap dieksekusi.

## Bukti Visual dan Peta Header Terkunci
- Judul: `BUKU PROGRAM KERJA`.
- Header utama:
  - Kolom tunggal: `NO`, `PROGRAM`, `PRIORITAS PROGRAM`, `KEGIATAN`, `SASARAN TARGET`, `KET`.
  - Grup `JADWAL WAKTU` dengan 12 subkolom bulan (`1` s.d. `12`).
  - Grup `SUMBER DANA` dengan 4 subkolom: `Pus`, `APB`, `SWL`, `Ban`.
- Nomor kolom autentik pada screenshot:
  - `1` `NO`
  - `2` `PROGRAM`
  - `3` `PRIORITAS PROGRAM`
  - `4` `KEGIATAN`
  - `5` `SASARAN TARGET`
  - `6..17` `JADWAL WAKTU` bulan `1..12`
  - `18` `SUMBER DANA - Pus`
  - `19` `SUMBER DANA - APB`
  - `20` `SUMBER DANA - SWL`
  - `21` `SUMBER DANA - Ban`
  - `22` `KET`

## Gap Kontrak vs Implementasi Existing
- Struktur existing:
  - `jadwal_i`, `jadwal_ii`, `jadwal_iii`, `jadwal_iv` (4 flag jadwal).
  - `sumber_dana_pusat`, `sumber_dana_apbd`, `sumber_dana_swd`, `sumber_dana_bant`.
- Deviasi utama:
  - `JADWAL WAKTU` autentik = 12 kolom bulanan (belum terwakili penuh di schema).
  - Label visual sumber dana `APB/SWL/Ban` perlu diselaraskan terhadap field canonical existing `APBD/SWD/Bant`.

## Langkah Eksekusi
- [x] Verifikasi visual header `Buku Program Kerja` selesai.
- [x] Peta header final terkunci sampai grup subkolom.
- [x] Tetapkan matrix mapping kolom autentik -> field canonical:
  - jadwal bulan 1..12 -> `jadwal_bulan_1` s.d. `jadwal_bulan_12`.
  - label autentik `APB/SWL/Ban` dipetakan ke field canonical backend `sumber_dana_apbd/sumber_dana_swd/sumber_dana_bant`.
- [x] Susun rencana migrasi skema minimal:
  - opsi A dipilih: tambah 12 kolom boolean bulanan pada tabel `program_prioritas`.
  - opsi B (normalisasi tabel detail jadwal) ditunda untuk fase lanjutan.
- [x] Audit dampak lintas layer:
  - request + DTO + action + repository.
  - UI form/list/show + PDF report.
  - seeder/factory + test feature/policy/scope.
- [x] Definisikan fallback plan teknis untuk kompatibilitas data lama (`jadwal_i..iv`).

## Validasi
- [x] Bukti visual header valid tersedia.
- [x] Tidak ada ambigu jumlah kolom pada header autentik (22 kolom).
- [x] Keputusan model data jadwal bulanan disetujui.
- [x] Rencana test executable sebelum patch implementasi.

## Risiko
- Migrasi jadwal 4 -> 12 kolom berisiko memengaruhi data existing dan report PDF.
- Jika label sumber dana tidak dinormalisasi tegas, berisiko drift antara UI autentik dan kontrak backend.

## Keputusan
- [x] Screenshot user sesi 2026-02-24 dikunci sebagai bukti resmi kontrak header `Buku Program Kerja`.
- [x] Concern dinyatakan `siap sinkronisasi kontrak header`.
- [x] Implementasi jadwal bulanan 12 kolom dieksekusi dengan kompatibilitas data lama (`jadwal_i..iv`) tetap aktif.
