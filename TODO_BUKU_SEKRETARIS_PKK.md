# TODO Buku Sekretaris TP PKK (Bertahap)

Urutan prioritas disusun dari paling mudah (kontrak jelas, konflik rendah) sampai yang lebih asumtif.

## Selesai
- [x] Tahap 1 - Buku Agenda Surat (`agenda-surat`) untuk desa dan kecamatan.
- [x] Tahap 2 - Buku Ekspedisi Surat sebagai report turunan `agenda-surat` (filter `jenis_surat=keluar`) tanpa tabel baru.
- [x] Tahap 3 - Validasi ulang kolom laporan terhadap template PDF sumber per lembar.
- [x] Tahap 4 - Penyesuaian format cetak jika ada perbedaan istilah/header antar level (medium risk, tetap reuse domain existing).
- [x] Tahap 5 - Refactor TODO sinkronisasi pedoman domain + kontrak domain baru.
- [x] Tahap 6 - Implementasi modul 4.14.1a `data-warga` (desa + kecamatan + report + policy + test matrix).
- [x] Tahap 7 - Implementasi modul 4.14.1b `data-kegiatan-warga` (desa + kecamatan + report + policy + test matrix).
- [x] Tahap 8 - Implementasi modul 4.14.2a `data-keluarga` (desa + kecamatan + report + policy + test matrix).

## Ringkasan Sinkronisasi Pedoman
- [x] Sumber canonical dipakai: https://pubhtml5.com/zsnqq/vjcf/basic/101-150
- [x] Fokus lampiran: 4.9a, 4.9b, 4.10, 4.11, 4.12, 4.13, 4.14.1a-4.14.4f, 4.15.

### Sudah Identik (Aplikasi vs Pedoman)
- [x] 4.9a Buku Daftar Anggota Tim Penggerak PKK -> `anggota-tim-penggerak`.
- [x] 4.9b Buku Daftar Kader Tim Penggerak PKK -> `kader-khusus` (domain teknis dipertahankan, label/PDF sudah identik pedoman).
- [x] 4.10 Buku Agenda Surat -> `agenda-surat`.
- [x] 4.11 Buku Keuangan -> `bantuans` (report arus masuk/keluar + saldo sudah dinormalisasi).
- [x] 4.12 Buku Inventaris -> `inventaris`.
- [x] 4.13 Buku Kegiatan -> domain `kegiatan` (route teknis: `activities`).
- [x] 4.14.4a Data Aset (Sarana) Desa/Kelurahan -> `warung-pkk` (domain teknis dipertahankan, label/PDF sudah identik pedoman).
- [x] 4.14.4b Data Isian Taman Bacaan/Perpustakaan -> `taman-bacaan`.
- [x] 4.14.4c Data Isian Koperasi -> `koperasi`.
- [x] 4.14.4d Data Isian Kejar Paket -> `kejar-paket`.
- [x] 4.14.4e Data Isian Posyandu oleh TP PKK -> `posyandu`.
- [x] 4.14.4f Data Isian Kelompok Simulasi dan Penyuluhan -> `simulasi-penyuluhan` (domain teknis dipertahankan, label/PDF sudah identik pedoman).

### List Baru (Kontrak Domain Disiapkan)
- [x] 4.14.1a Data Warga -> kontrak domain: `data-warga`.
- [x] 4.14.1b Data Kegiatan Warga -> kontrak domain: `data-kegiatan-warga`.
- [x] 4.14.2a Data Keluarga -> kontrak domain: `data-keluarga`.
- [x] 4.14.2b Data Pemanfaatan Tanah Pekarangan/HATINYA PKK -> kontrak domain: `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`.
- [x] 4.14.2c Data Industri Rumah Tangga -> kontrak domain: `data-industri-rumah-tangga`.
- [x] 4.14.3 Data Pelatihan Kader -> kontrak domain: `data-pelatihan-kader`.
- [x] 4.15 Catatan Keluarga -> kontrak domain: `catatan-keluarga` (rekap lintas lampiran terkait).
- [x] Verifikasi 4.14.5 pada baseline halaman 101-150: belum ditemukan pada sumber canonical saat ini.

### Roadmap Implementasi Modul Baru (Refactor TODO)
- [x] 4.14.1a Data Warga -> `data-warga` sudah terimplementasi end-to-end.
- [x] 4.14.1b Data Kegiatan Warga -> `data-kegiatan-warga` sudah terimplementasi end-to-end.
- [x] 4.14.2a Data Keluarga -> `data-keluarga` sudah terimplementasi end-to-end.
- [x] 4.14.2b Data Pemanfaatan Tanah Pekarangan/HATINYA PKK -> `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`.
- [x] 4.14.2c Data Industri Rumah Tangga -> `data-industri-rumah-tangga`.
- [ ] 4.14.3 Data Pelatihan Kader -> `data-pelatihan-kader`.
- [ ] 4.15 Catatan Keluarga -> `catatan-keluarga`.

## Akses Tulis Data (Scope Policy)
- `desa`: `desa-sekretaris`, `desa-bendahara`, `desa-pokja-i`, `desa-pokja-ii`, `desa-pokja-iii`, `desa-pokja-iv`, kompatibilitas `admin-desa`.
- `kecamatan`: `kecamatan-sekretaris`, `kecamatan-bendahara`, `kecamatan-pokja-i`, `kecamatan-pokja-ii`, `kecamatan-pokja-iii`, `kecamatan-pokja-iv`, kompatibilitas `admin-kecamatan`, `super-admin`.
- Guard backend tetap `scope.role:{desa|kecamatan}` + `Policy -> Scope Service`.

## Log Tahapan Pemrosesan
- `1944513` refactor(kader): align naming with pedoman 4.9b.
- `df80665` refactor(keuangan): normalize inflow outflow structure for pedoman 4.11.
- `051954d` refactor(warung-pkk): align naming with pedoman 4.14.4a.
- `4431aa6` refactor(simulasi-penyuluhan): align naming with pedoman 4.14.4f.

## Catatan Anti-Konflik
- `agenda-surat` dipakai sebagai source of truth surat masuk/keluar.
- Buku ekspedisi tidak menambah tabel/domain baru untuk menghindari duplikasi data surat keluar.
- Kontrak domain baru (4.14.1a-4.15) sudah dipetakan; implementasi masuk fase bertahap. Modul 4.14.1a (`data-warga`), 4.14.1b (`data-kegiatan-warga`), 4.14.2a (`data-keluarga`), 4.14.2b (`data-pemanfaatan-tanah-pekarangan-hatinya-pkk`), dan 4.14.2c (`data-industri-rumah-tangga`) sudah selesai.
