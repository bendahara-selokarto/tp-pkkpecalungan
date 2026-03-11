# Sumber Data Lampiran 4.22 - Data Kegiatan PKK (Pokja II)

## Tujuan

- Menyiapkan bahan implementasi report Lampiran `4.22` (Pokja II).
- Mengunci sumber data per kolom dan memastikan normalisasi tetap konsisten.

## Status Validasi

- Status sinkronisasi kontrak domain: **implemented** (report + input aktif).
- Status cek manual header autentik: **selesai, sesuai**.
- Status cek manual sumber data report: **selesai** (repository + view).
- Tanggal cek manual terakhir (header): `2026-03-11`.
- Tanggal cek manual terakhir (sumber data): `2026-03-11`.
- Bukti acuan visual: screenshot Lampiran `4.22` di `docs/referensi/_screenshots/rakernas-x-autentik/lampiran_4_22_data_kegiatan_pkk_pokja_ii.png`.
- Referensi cara pengisian: `docs/referensi/supporting/lampiran-4-22-cara-pengisian.pdf` (kolom 1-36).

## Jalur Eksekusi Report (Aktif)

- Route desa: `desa.catatan-keluarga.data-kegiatan-pkk-pokja-ii.report`
- Route kecamatan: `kecamatan.catatan-keluarga.data-kegiatan-pkk-pokja-ii.report`
- Controller print: `app/Domains/Wilayah/CatatanKeluarga/Controllers/CatatanKeluargaPrintController.php`
- Use case: `app/Domains/Wilayah/CatatanKeluarga/UseCases/ListScopedDataKegiatanPkkPokjaIiUseCase.php`
- Repository: `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php`
- View PDF: `resources/views/pdf/data_kegiatan_pkk_pokja_ii_report.blade.php`

## Sumber Data Per Kolom (Aktif)

| Kolom | Header | Sumber data | Catatan implementasi | Normalisasi |
| --- | --- | --- | --- | --- |
| 1 | `NO` | agregasi report | nomor urut hasil iterasi | - |
| 2 | `NAMA WILAYAH` | `areas` (area aktif user) | label wilayah diambil dari area scope | - |
| 3 | `JML WARGA YANG MASIH 3 (TIGA) BUTA` | `literasi_wargas.jumlah_tiga_buta` | 1 record per `level+area+tahun` | - |
| 4 | `PAKET A - JML KLP BELAJAR` | `kejar_pakets` (jenis = Paket A) | hitung jumlah record | `normalizeJenisKejarPaket` |
| 5 | `PAKET A - WARGA BELAJAR` | `kejar_pakets` | sum `jumlah_warga_belajar_l/p` | `normalizeJenisKejarPaket` |
| 6 | `PAKET B - JML KLP BELAJAR` | `kejar_pakets` (jenis = Paket B) | hitung jumlah record | `normalizeJenisKejarPaket` |
| 7 | `PAKET B - WARGA BELAJAR` | `kejar_pakets` | sum `jumlah_warga_belajar_l/p` | `normalizeJenisKejarPaket` |
| 8 | `PAKET C - JML KLP BELAJAR` | `kejar_pakets` (jenis = Paket C) | hitung jumlah record | `normalizeJenisKejarPaket` |
| 9 | `PAKET C - WARGA BELAJAR` | `kejar_pakets` | sum `jumlah_warga_belajar_l/p` | `normalizeJenisKejarPaket` |
| 10 | `KF - JML KLP BELAJAR` | `kejar_pakets` (jenis = KF) | hitung jumlah record | `normalizeJenisKejarPaket` |
| 11 | `KF - WARGA BELAJAR` | `kejar_pakets` | sum `jumlah_warga_belajar_l/p` | `normalizeJenisKejarPaket` |
| 12 | `PAUD SEJENIS` | `kejar_pakets` (jenis = PAUD) | hitung jumlah record | `normalizeJenisKejarPaket` |
| 13 | `JUMLAH TAMAN BACA PERPUSTAKAAN` | `taman_bacaans` | distinct count `nama_taman_bacaan` | - |
| 14 | `BKB - JML KLP` | `bkb_kegiatans.jumlah_kelompok` | sum `jumlah_kelompok` | - |
| 15 | `BKB - JML IBU PESERTA` | `bkb_kegiatans.jumlah_ibu_peserta` | sum `jumlah_ibu_peserta` | - |
| 16 | `BKB - JML APE (SET)` | `bkb_kegiatans.jumlah_ape_set` | sum `jumlah_ape_set` | - |
| 17 | `BKB - JML KLP SIMULASI` | `bkb_kegiatans.jumlah_kelompok_simulasi` | sum `jumlah_kelompok_simulasi` | - |
| 18 | `TUTOR - KF` | `tutor_khusus` (jenis = KF) | sum `jumlah_tutor` | normalizer `jenis_tutor` |
| 19 | `TUTOR - PAUD SEJENIS` | `tutor_khusus` (jenis = PAUD) | sum `jumlah_tutor` | normalizer `jenis_tutor` |
| 20 | `KADER KHUSUS - BKB` | `kader_khusus` | count `jenis_kader_khusus` mengandung BKB | keyword map `bkb` |
| 21 | `KADER KHUSUS - KOPERASI` | `kader_khusus` | count `jenis_kader_khusus` mengandung koperasi/usaha bersama/UP2K | keyword map `koperasi` |
| 22 | `KADER KHUSUS - KETERAMPILAN` | `kader_khusus` | count `jenis_kader_khusus` mengandung keterampilan/kerajinan | keyword map `keterampilan` |
| 23 | `JUMLAH KADER DILATIH - LP3` | `pelatihan_kader_pokja_ii` | sum `jumlah_kader` kategori `lp3` | normalizer `kategori_pelatihan` |
| 24 | `JUMLAH KADER DILATIH - TPK 3 PKK` | `pelatihan_kader_pokja_ii` | sum `jumlah_kader` kategori `tpk_3_pkk` | normalizer `kategori_pelatihan` |
| 25 | `JUMLAH KADER DILATIH - DAMAS PKK` | `pelatihan_kader_pokja_ii` | sum `jumlah_kader` kategori `damas_pkk` | normalizer `kategori_pelatihan` |
| 26 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - PEMULA - JML KLP` | `pra_koperasi_up2k` (tingkat = pemula) | sum `jumlah_kelompok` | normalizer `tingkat` |
| 27 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - PEMULA - PESERTA` | `pra_koperasi_up2k` | sum `jumlah_peserta` | normalizer `tingkat` |
| 28 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - MADYA - JML KLP` | `pra_koperasi_up2k` (tingkat = madya) | sum `jumlah_kelompok` | normalizer `tingkat` |
| 29 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - MADYA - PESERTA` | `pra_koperasi_up2k` | sum `jumlah_peserta` | normalizer `tingkat` |
| 30 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - UTAMA - JML KLP` | `pra_koperasi_up2k` (tingkat = utama) | sum `jumlah_kelompok` | normalizer `tingkat` |
| 31 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - UTAMA - PESERTA` | `pra_koperasi_up2k` | sum `jumlah_peserta` | normalizer `tingkat` |
| 32 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - MANDIRI - JML KLP` | `pra_koperasi_up2k` (tingkat = mandiri) | sum `jumlah_kelompok` | normalizer `tingkat` |
| 33 | `PENGEMBANGAN KEAHLIAN BERKOPERASI - MANDIRI - PESERTA` | `pra_koperasi_up2k` | sum `jumlah_peserta` | normalizer `tingkat` |
| 34 | `KOPERASI BERBADAN HUKUM - JML KLP` | `koperasis` (`berbadan_hukum` = true) | count koperasi berbadan hukum | - |
| 35 | `KOPERASI BERBADAN HUKUM - JML ANGGOTA` | `koperasis` | sum `jumlah_anggota_l + jumlah_anggota_p` | - |
| 36 | `KET.` | agregasi catatan | gabungan `keterangan` dari sumber terkait | normalizer `composeScalarFieldLabel` |

## Catatan Normalisasi

- Semua tabel sumber sudah memiliki `level`, `area_id`, `created_by`, dan `tahun_anggaran`.
- Normalizer aktif berada di `CatatanKeluargaRepository` (`normalizeJenisKejarPaket`, `containsAnyKeyword`, dan normalizer string untuk `kategori_pelatihan` + `tingkat`).

## Keputusan Terkunci

- `JML KLP` Kejar Paket = count record per jenis (A/B/C/KF).
- `PAUD Sejenis` mengambil `kejar_pakets` (jenis = PAUD).
- Tutor `KF/PAUD` memakai `tutor_khusus`.
- `Jumlah Kader yang sudah dilatih` memakai `pelatihan_kader_pokja_ii` (LP3/TPK 3 PKK/DAMAS PKK).
- `Pra Koperasi/Usaha Bersama/UP2K` memakai `pra_koperasi_up2k` (pemula/madya/utama/mandiri + peserta).
- `Jml Warga yang masih 3 (tiga) buta` memakai `literasi_wargas.jumlah_tiga_buta`.

## Status Sinkronisasi

- Dokumen ini sudah mencerminkan implementasi report aktif.
- Jika normalizer berubah, dokumen ini wajib diperbarui pada sesi yang sama.
