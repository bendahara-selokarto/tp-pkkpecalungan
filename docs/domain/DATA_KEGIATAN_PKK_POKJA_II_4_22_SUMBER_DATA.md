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
| 3 | `JML WARGA YANG MASIH 3 (TIGA) BUTA` | belum ada field | butuh sumber resmi | tambah flag di `data_warga_anggotas` atau tabel khusus literasi |
| 4-5 | `PAKET A` (JML KLP, WARGA BELAJAR) | `kejar_pakets` (`jenis_kejar_paket` = Paket A) | definisi `JML KLP` | tambah `jumlah_kelompok` atau hitung jumlah record per jenis |
| 6-7 | `PAKET B` (JML KLP, WARGA BELAJAR) | `kejar_pakets` (`jenis_kejar_paket` = Paket B) | definisi `JML KLP` | sama seperti Paket A |
| 8-9 | `PAKET C` (JML KLP, WARGA BELAJAR) | `kejar_pakets` (`jenis_kejar_paket` = Paket C) | definisi `JML KLP` | sama seperti Paket A |
| 10-11 | `KF` (JML KLP, WARGA BELAJAR) | `kejar_pakets` (`jenis_kejar_paket` = KF) | butuh kamus jenis baku | tambah tabel referensi jenis kejar paket |
| 12 | `PAUD SEJENIS` | `kejar_pakets` (jenis = PAUD) atau `data_warga_anggotas.ikut_paud` | butuh keputusan resmi | pilih sumber tunggal + normalisasi jenis |
| 13 | `JUMLAH TAMAN BACA PERPUSTAKAAN` | `taman_bacaans` | definisi `jumlah` | gunakan count record scoped |
| 14-16 | `BKB` (JML KLP, JML IBU PESERTA, JML APE SET) | belum ada modul | butuh modul baru | buat tabel `bkb_kelompoks` dengan peserta + ape_set |
| 17 | `TUTOR - JML KLP SIMULASI` | `simulasi_penyuluhans` (`jumlah_kelompok`) | definisi jenis tutor | tambah klasifikasi `jenis_simulasi_penyuluhan` ter-normalisasi |
| 18-22 | `KADER KHUSUS` (KF/PAUD/BKB/KOPERASI/KETERAMPILAN) | `kader_khusus` (`jenis_kader_khusus`) | perlu mapping jenis | tambah kamus jenis kader khusus + normalizer |
| 23-25 | `JUMLAH KADER YANG SUDAH DILATIH` (LP3/TPK 3 PKK/DAMAS PKK) | `data_pelatihan_kaders` | belum ada jumlah peserta | tambah tabel pelatihan kader + jumlah peserta (L/P) |
| 26-33 | `PENGEMBANGAN KEAHLIAN BERKOPERASI` (Pemula/Madya/Utama/Mandiri, JML KLP + Peserta) | belum ada modul | butuh modul baru | buat tabel `pra_koperasi_usaha_bersama_up2k` dengan level + peserta |
| 34-35 | `KOPERASI BERBADAN HUKUM` (JML KLP, JML ANGGOTA) | `koperasis` (`berbadan_hukum`) | definisi `JML KLP` | count koperasi berbadan hukum + sum anggota |
| 36 | `KET.` | agregasi catatan | standar format | gunakan concat keterangan dari sumber terkait |

## Catatan Normalisasi

- Semua tabel baru wajib memiliki `level`, `area_id`, `created_by`, dan `tahun_anggaran`.
- Hindari free-text kategori tanpa kamus; gunakan tabel referensi/enum untuk `jenis_kejar_paket`, `jenis_kader_khusus`, `jenis_simulasi_penyuluhan`, dan `kategori_pelatihan`.
- Jika memakai data existing (Kejar Paket, Kader Khusus), lakukan normalizer yang konsisten dan terdokumentasi.

## Keputusan yang Butuh Konfirmasi

- Sumber resmi kolom `PAUD SEJENIS`.
- Definisi `JML KLP` pada Kejar Paket (count record vs field explicit).
- Mapping `Tutor - Jml KLP Simulasi` ke modul `Simulasi Penyuluhan`.
- Bentuk tabel baru untuk `Pelatihan Kader` dan `Pra Koperasi/UP2K`.
- Penentuan sumber kolom `JML WARGA YANG MASIH 3 (TIGA) BUTA`.

## Status Sinkronisasi

- Dokumen ini adalah rencana sumber data; implementasi belum dimulai.
- Sinkronisasi report dan modul input baru dilakukan setelah keputusan normalisasi dikunci.
