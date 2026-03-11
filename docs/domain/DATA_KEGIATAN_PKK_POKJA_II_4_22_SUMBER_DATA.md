# Sumber Data Lampiran 4.22 - Data Kegiatan PKK (Pokja II)

## Tujuan

- Menyiapkan bahan implementasi report Lampiran `4.22` (Pokja II).
- Mengunci rencana sumber data per kolom dan menandai gap normalisasi.

## Status Validasi

- Status sinkronisasi kontrak domain: **not implemented**.
- Status cek manual header autentik: **selesai, sesuai**.
- Status cek manual sumber data report: **belum**.
- Tanggal cek manual terakhir (header): `2026-03-11`.
- Bukti acuan visual: screenshot Lampiran `4.22` di `docs/referensi/_screenshots/rakernas-x-autentik/lampiran_4_22_data_kegiatan_pkk_pokja_ii.png`.
- Referensi cara pengisian: `docs/referensi/Cara Pengisian Lampiran 4.22.pdf` (kolom 1-36).

## Jalur Eksekusi Report (Planned)

- Route desa: `desa.catatan-keluarga.data-kegiatan-pkk-pokja-ii.report`
- Route kecamatan: `kecamatan.catatan-keluarga.data-kegiatan-pkk-pokja-ii.report`
- Controller print: `app/Domains/Wilayah/CatatanKeluarga/Controllers/CatatanKeluargaPrintController.php`
- Use case: `app/Domains/Wilayah/CatatanKeluarga/UseCases/ListScopedDataKegiatanPkkPokjaIiUseCase.php`
- Repository: `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php`
- View PDF: `resources/views/pdf/data_kegiatan_pkk_pokja_ii_report.blade.php`

## Sumber Data Per Kolom (Rencana)

| Kolom | Header | Kandidat sumber data | Gap/Keputusan | Normalisasi yang disarankan |
| --- | --- | --- | --- | --- |
| 1 | `NO` | hasil agregasi report | - | - |
| 2 | `NAMA WILAYAH` | `areas` (area aktif user) | - | - |
| 3 | `JML WARGA YANG MASIH 3 (TIGA) BUTA` | belum ada field | definisi sudah jelas, sumber belum ada | tambah flag di `data_warga_anggotas` atau tabel khusus literasi |
| 4 | `PAKET A - JML KLP BELAJAR` | `kejar_pakets` (`jenis_kejar_paket` = Paket A) | definisi `JML KLP` perlu dipastikan | tambah `jumlah_kelompok` atau hitung jumlah record per jenis |
| 5 | `PAKET A - WARGA BELAJAR` | `kejar_pakets` (`jenis_kejar_paket` = Paket A) | pastikan kolom warga belajar tersedia | tambah `jumlah_warga_belajar_l/p` sudah tersedia |
| 6 | `PAKET B - JML KLP BELAJAR` | `kejar_pakets` (`jenis_kejar_paket` = Paket B) | definisi `JML KLP` perlu dipastikan | sama seperti Paket A |
| 7 | `PAKET B - WARGA BELAJAR` | `kejar_pakets` (`jenis_kejar_paket` = Paket B) | - | gunakan `jumlah_warga_belajar_l/p` |
| 8 | `PAKET C - JML KLP BELAJAR` | `kejar_pakets` (`jenis_kejar_paket` = Paket C) | definisi `JML KLP` perlu dipastikan | sama seperti Paket A |
| 9 | `PAKET C - WARGA BELAJAR` | `kejar_pakets` (`jenis_kejar_paket` = Paket C) | - | gunakan `jumlah_warga_belajar_l/p` |
| 10 | `KF - JML KLP BELAJAR` | `kejar_pakets` (`jenis_kejar_paket` = KF) | butuh kamus jenis baku | tambah tabel referensi jenis kejar paket |
| 11 | `KF - WARGA BELAJAR` | `kejar_pakets` (`jenis_kejar_paket` = KF) | - | gunakan `jumlah_warga_belajar_l/p` |
| 12 | `PAUD SEJENIS` | `kejar_pakets` (jenis = PAUD) atau `data_warga_anggotas.ikut_paud` | butuh keputusan sumber resmi | pilih sumber tunggal + normalisasi jenis |
| 13 | `JUMLAH TAMAN BACA PERPUSTAKAAN` | `taman_bacaans` | definisi `jumlah` | gunakan count record scoped |
| 14 | `BKB - JML KLP` | belum ada modul | butuh modul baru | buat tabel `bkb_kelompoks` dengan jumlah kelompok |
| 15 | `BKB - JML IBU PESERTA` | belum ada modul | butuh modul baru | tambah kolom jumlah peserta |
| 16 | `BKB - JML APE (SET)` | belum ada modul | butuh modul baru | tambah kolom jumlah APE set |
| 17 | `TUTOR - JML KLP SIMULASI (BKB)` | `simulasi_penyuluhans` (`jumlah_kelompok`) | pastikan kategori simulasi BKB | tambah klasifikasi `jenis_simulasi_penyuluhan` ter-normalisasi |
| 18 | `TUTOR - KF` | belum ada field | butuh sumber resmi | extend `simulasi_penyuluhans` atau tabel tutor tersendiri |
| 19 | `TUTOR - PAUD SEJENIS` | belum ada field | butuh sumber resmi | extend `simulasi_penyuluhans` atau tabel tutor tersendiri |
| 20 | `KADER KHUSUS - BKB` | `kader_khusus` (`jenis_kader_khusus`) | perlu mapping jenis | tambah kamus jenis kader khusus + normalizer |
| 21 | `KADER KHUSUS - KOPERASI` | `kader_khusus` (`jenis_kader_khusus`) | perlu mapping jenis | tambah kamus jenis kader khusus + normalizer |
| 22 | `KADER KHUSUS - KETERAMPILAN` | `kader_khusus` (`jenis_kader_khusus`) | perlu mapping jenis | tambah kamus jenis kader khusus + normalizer |
| 23 | `JUMLAH KADER DILATIH - LP3` | `data_pelatihan_kaders` | belum ada jumlah peserta | tambah kolom jumlah peserta (L/P) + kategori pelatihan |
| 24 | `JUMLAH KADER DILATIH - TPK 3 PKK` | `data_pelatihan_kaders` | belum ada jumlah peserta | tambah kolom jumlah peserta (L/P) + kategori pelatihan |
| 25 | `JUMLAH KADER DILATIH - DAMAS PKK` | `data_pelatihan_kaders` | belum ada jumlah peserta | tambah kolom jumlah peserta (L/P) + kategori pelatihan |
| 26 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - PEMULA - JML KLP` | belum ada modul | butuh modul baru | buat tabel `pra_koperasi_usaha_bersama_up2k` dengan level + peserta |
| 27 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - PEMULA - PESERTA` | belum ada modul | butuh modul baru | buat tabel `pra_koperasi_usaha_bersama_up2k` dengan level + peserta |
| 28 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - MADYA - JML KLP` | belum ada modul | butuh modul baru | buat tabel `pra_koperasi_usaha_bersama_up2k` dengan level + peserta |
| 29 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - MADYA - PESERTA` | belum ada modul | butuh modul baru | buat tabel `pra_koperasi_usaha_bersama_up2k` dengan level + peserta |
| 30 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - UTAMA - JML KLP` | belum ada modul | butuh modul baru | buat tabel `pra_koperasi_usaha_bersama_up2k` dengan level + peserta |
| 31 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - UTAMA - PESERTA` | belum ada modul | butuh modul baru | buat tabel `pra_koperasi_usaha_bersama_up2k` dengan level + peserta |
| 32 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - MANDIRI - JML KLP` | belum ada modul | butuh modul baru | buat tabel `pra_koperasi_usaha_bersama_up2k` dengan level + peserta |
| 33 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - MANDIRI - PESERTA` | belum ada modul | butuh modul baru | buat tabel `pra_koperasi_usaha_bersama_up2k` dengan level + peserta |
| 34 | `KOPERASI BERBADAN HUKUM - JML KLP` | `koperasis` (`berbadan_hukum`) | definisi `JML KLP` | count koperasi berbadan hukum + sum anggota |
| 35 | `KOPERASI BERBADAN HUKUM - JML ANGGOTA` | `koperasis` (`berbadan_hukum`) | definisi `JML ANGGOTA` | sum anggota (L/P) dari koperasi berbadan hukum |
| 36 | `KET.` | agregasi catatan | standar format | gunakan concat keterangan dari sumber terkait |

## Catatan Normalisasi

- Semua tabel baru wajib memiliki `level`, `area_id`, `created_by`, dan `tahun_anggaran`.
- Hindari free-text kategori tanpa kamus; gunakan tabel referensi/enum untuk `jenis_kejar_paket`, `jenis_kader_khusus`, `jenis_simulasi_penyuluhan`, dan `kategori_pelatihan`.
- Jika memakai data existing (Kejar Paket, Kader Khusus), lakukan normalizer yang konsisten dan terdokumentasi.

## Keputusan yang Butuh Konfirmasi

- Sumber resmi kolom `PAUD SEJENIS`.
- Definisi `JML KLP` pada Kejar Paket (count record vs field explicit).
- Sumber data `Tutor KF` dan `Tutor PAUD sejenis`.
- Bentuk tabel baru untuk `Pelatihan Kader` dan `Pra Koperasi/UP2K`.
- Penentuan sumber kolom `JML WARGA YANG MASIH 3 (TIGA) BUTA`.

## Status Sinkronisasi

- Dokumen ini adalah rencana sumber data; implementasi belum dimulai.
- Sinkronisasi report dan modul input baru dilakukan setelah keputusan normalisasi dikunci.
