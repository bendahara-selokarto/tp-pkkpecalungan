# Sumber Data Lampiran 4.24 - Data Kegiatan PKK (Pokja IV)

## Tujuan

- Menjelaskan sumber data operasional yang dipakai report Lampiran `4.24`.
- Menandai bahwa struktur header dan sumber data report sudah dicek manual dan dinyatakan sesuai dengan implementasi aktif.

## Status Validasi

- Status sinkronisasi kontrak domain: **implemented (report-only via `catatan-keluarga`)**.
- Status cek manual header autentik: **selesai, sesuai**.
- Status cek manual sumber data report: **selesai, sesuai**.
- Tanggal cek manual terakhir: `2026-03-08`.
- Bukti acuan visual: screenshot aktual Lampiran `4.24` yang dipakai pada sesi validasi `2026-03-08`.

## Jalur Eksekusi Report

- Route desa: `desa.catatan-keluarga.data-kegiatan-pkk-pokja-iv.report`
- Route kecamatan: `kecamatan.catatan-keluarga.data-kegiatan-pkk-pokja-iv.report`
- Controller print: `app/Domains/Wilayah/CatatanKeluarga/Controllers/CatatanKeluargaPrintController.php`
- Use case: `app/Domains/Wilayah/CatatanKeluarga/UseCases/ListScopedDataKegiatanPkkPokjaIvUseCase.php`
- Repository: `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php`
- Method agregasi utama: `getDataKegiatanPkkPokjaIvByLevelAndArea`
- View PDF: `resources/views/pdf/data_kegiatan_pkk_pokja_iv_report.blade.php`

## Sumber Data Per Kolom

| Kolom | Header | Sumber modul/input | Tabel/model | Field/input yang dipakai | Cara baca implementasi |
| --- | --- | --- | --- | --- | --- |
| 1 | `NO` | Bukan input user langsung | hasil agregasi report | `nomor_urut` | Diisi statis `1` untuk satu baris agregasi area aktif. |
| 2 | `NAMA WILAYAH` | Area aktif user | `areas` / `Area` | `name` area aktif | Diambil dari `area_id` user sesuai scope report. |
| 3 | `KADER KESEHATAN` | Modul `Kader Khusus` | `kader_khusus` / `KaderKhusus` | seluruh entri scoped | Dihitung dari jumlah seluruh entri kader khusus pada area aktif. |
| 4 | `GIZI` | Modul `Kader Khusus` | `kader_khusus` / `KaderKhusus` | `jenis_kader_khusus` | Hitung entri yang mengandung token `gizi`. |
| 5 | `KESLING` | Modul `Kader Khusus` | `kader_khusus` / `KaderKhusus` | `jenis_kader_khusus` | Hitung entri yang mengandung token `kesling` atau `lingkungan`. |
| 6 | `PHBS` | Modul `Kader Khusus` | `kader_khusus` / `KaderKhusus` | `jenis_kader_khusus` | Hitung entri yang mengandung token `phbs`. |
| 7 | `KB` | Modul `Kader Khusus` | `kader_khusus` / `KaderKhusus` | `jenis_kader_khusus` | Hitung entri yang mengandung token `kb` atau `keluarga berencana`. |
| 8 | `POSYANDU` | Modul `Posyandu` | `posyandus` / `Posyandu` | seluruh entri scoped | Dihitung dari jumlah entri posyandu pada area aktif. |
| 9 | `IMUNISASI / VAKSINASI BAYI/BALITA` | Modul `Posyandu` | `posyandus` / `Posyandu` | `jumlah_pengunjung_l`, `jumlah_pengunjung_p` | Dijumlahkan dari pengunjung laki-laki dan perempuan seluruh entri posyandu scoped. |
| 10 | `PKG` | Modul `Data Kegiatan Warga` | `data_kegiatan_wargas` / `DataKegiatanWarga` | `kegiatan`, `keterangan`, `aktivitas` | Hitung entri `aktivitas = true` yang mengandung token `pkg` atau `pemeriksaan kesehatan gratis`. |
| 11 | `TBC` | Modul `Data Kegiatan Warga` | `data_kegiatan_wargas` / `DataKegiatanWarga` | `kegiatan`, `keterangan`, `aktivitas` | Hitung entri `aktivitas = true` yang mengandung token `tbc` atau `tuberkulosis`. |
| 12 | `JAMBAN (WC)` | Modul `Catatan Keluarga` | `data_wargas` / `DataWarga` | metrik rumah tangga `memiliki_mck_septic` | Saat ini diproyeksikan dari metric rumah tangga yang sama dengan kolom `MCK`. |
| 13 | `SPAL` | Modul `Catatan Keluarga` | `data_wargas` / `DataWarga` | metric `memiliki_spal` | Bersumber dari agregasi metric rumah tangga scoped. |
| 14 | `TPS` | Modul `Catatan Keluarga` | `data_wargas` / `DataWarga` | metric `memiliki_tempat_sampah` | Bersumber dari agregasi metric rumah tangga scoped. |
| 15 | `JUMLAH MCK` | Modul `Catatan Keluarga` | `data_wargas` / `DataWarga` | metric `memiliki_mck_septic` | Bersumber dari agregasi metric rumah tangga scoped. |
| 16 | `PDAM` | Modul `Catatan Keluarga` | `data_wargas` / `DataWarga` | metric `pdam` | Bersumber dari agregasi metric rumah tangga scoped. |
| 17 | `SUMUR` | Modul `Catatan Keluarga` | `data_wargas` / `DataWarga` | metric `sumur` | Bersumber dari agregasi metric rumah tangga scoped. |
| 18 | `LAIN-LAIN` | Modul `Catatan Keluarga` | `data_wargas` / `DataWarga` | metric `dll` | Bersumber dari agregasi metric rumah tangga scoped. |
| 19 | `JUMLAH PUS` | Modul `Catatan Keluarga` | `data_warga_anggotas` / `DataWargaAnggota` | `umur_tahun`, `status_perkawinan` | Dihitung per keluarga yang memiliki anggota kandidat PUS; bukan per anggota. |
| 20 | `JUMLAH WUS` | Modul `Catatan Keluarga` | `data_warga_anggotas` / `DataWargaAnggota` | `jenis_kelamin`, `umur_tahun` | Hitung perempuan usia `15-49` tahun pada area aktif. |
| 21 | `L` | Modul `Catatan Keluarga` | `data_warga_anggotas` / `DataWargaAnggota` | `jenis_kelamin`, `akseptor_kb` | Hitung anggota laki-laki dengan `akseptor_kb = true`. |
| 22 | `P` | Modul `Catatan Keluarga` | `data_warga_anggotas` / `DataWargaAnggota` | `jenis_kelamin`, `akseptor_kb` | Hitung anggota perempuan dengan `akseptor_kb = true`. |
| 23 | `JML. KK YANG MEMILIKI TABUNGAN KELUARGA` | Modul `Catatan Keluarga` | `data_warga_anggotas` / `DataWargaAnggota` | `memiliki_tabungan` | Hitung keluarga yang memiliki minimal satu anggota dengan `memiliki_tabungan = true`. |
| 24 | `JML. KK YANG MEMILIKI ASURANSI KESEHATAN` | Modul `Catatan Keluarga` | `data_warga_anggotas` / `DataWargaAnggota` | `keterangan` | Hitung keluarga yang punya anggota dengan `keterangan` mengandung token `asuransi` atau `bpjs`. |
| 25 | `KESEHATAN` | Modul `Program Prioritas` | `program_prioritas` / `ProgramPrioritas` | `program`, `prioritas_program`, `kegiatan` | Flag `1/0`; bernilai `1` jika ada entri yang mengandung token `kesehatan`. |
| 26 | `KELESTARIAN LINGKUNGAN HIDUP` | Modul `Program Prioritas` | `program_prioritas` / `ProgramPrioritas` | `program`, `prioritas_program`, `kegiatan` | Flag `1/0`; bernilai `1` jika ada entri yang mengandung token `lingkungan`. |
| 27 | `PERENCANAAN SEHAT` | Modul `Program Prioritas` | `program_prioritas` / `ProgramPrioritas` | `program`, `prioritas_program`, `kegiatan` | Flag `1/0`; bernilai `1` jika ada entri yang mengandung token `perencanaan sehat` atau `perencanaan`. |

## Catatan Penting

- Lampiran `4.24` saat ini tidak memiliki form input tunggal khusus. Report dibentuk dari agregasi lintas modul yang sudah ada.
- Semua query report dibatasi oleh `level`, `area_id`, dan `tahun_anggaran` aktif sesuai scope user.
- Sebagian kolom masih bersifat proyeksi operasional berbasis keyword, bukan field dedicated per lampiran autentik.
- Deviasi sumber data resmi concern ini tetap dicatat pada `DV-015` di `docs/domain/DOMAIN_DEVIATION_LOG.md`.

## Hasil Cek Manual

- Header tabel pada view PDF dibandingkan manual dengan screenshot aktual Lampiran `4.24`.
- Jalur sumber data ditelusuri manual dari `route -> controller -> use case -> repository -> model`.
- Pemetaan sumber data per kolom dibandingkan manual dengan implementasi aktif repository `getDataKegiatanPkkPokjaIvByLevelAndArea`.
- Status akhir: **sesuai** untuk konteks implementasi aktif `report-only via catatan-keluarga`.
