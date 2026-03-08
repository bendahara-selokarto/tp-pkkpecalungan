# Mapping Lampiran 4.24 - Data Kegiatan PKK (Pokja IV)

## Sumber Autentik

- Text-layer: `docs/referensi/232.pdf` (cara pengisian Pokja IV, kolom 1-27).
- Verifikasi visual header tabel: screenshot aktual Lampiran 4.24 pada sesi validasi `2026-03-08`.

## Hasil Baca Header (Final)

- Judul: `DATA KEGIATAN PKK`
- Subjudul: `POKJA IV`
- Total kolom: `27`
- Struktur merge:
  - `NO` (`rowspan=4`)
  - `NAMA WILAYAH (...)` (`rowspan=4`)
  - `KESEHATAN` (`colspan=9`)
    - `JUMLAH KADER` (`colspan=5`)
      - `KADER KESEHATAN` (`rowspan=2`)
      - `KADER YANG ADA` (`colspan=4`) -> `GIZI`, `KESLING`, `PHBS`, `KB`
    - `POSYANDU` (`rowspan=3`)
    - `IMUNISASI / VAKSINASI BAYI/BALITA` (`rowspan=3`)
    - `PKG` (`rowspan=3`)
    - `TBC` (`rowspan=3`)
  - `KELESTARIAN LINGKUNGAN HIDUP` (`colspan=7`)
    - `JUMLAH RUMAH YANG MEMILIKI` (`colspan=3`) -> `JAMBAN (WC)`, `SPAL`, `TPS` (`rowspan=2` pada level leaf)
    - `JUMLAH MCK` (`rowspan=3`)
    - `JUMLAH KRT YANG MENGGUNAKAN AIR` (`colspan=3`) -> `PDAM`, `SUMUR`, `LAIN-LAIN` (`rowspan=2` pada level leaf)
  - `PERENCANAAN SEHAT` (`colspan=6`)
    - `JUMLAH PUS` (`rowspan=3`)
    - `JUMLAH WUS` (`rowspan=3`)
    - `JUMLAH AKSEPTOR KB` (`colspan=2`) -> `L`, `P` (`rowspan=2` pada level leaf)
    - `JML. KK YANG MEMILIKI TABUNGAN KELUARGA` (`rowspan=3`)
    - `JML. KK YANG MEMILIKI ASURANSI KESEHATAN` (`rowspan=3`)
  - `PROGRAM UNGGULAN GERAKAN KELUARGA SEHAT TANGGAP & TANGGUH BENCANA (GKSTTB)` (`colspan=3`) -> `KESEHATAN`, `KELESTARIAN LINGKUNGAN HIDUP`, `PERENCANAAN SEHAT` (masing-masing `rowspan=3`)

## Mapping Implementasi (Report-Only)
Target file:

- `resources/views/pdf/data_kegiatan_pkk_pokja_iv_report.blade.php`

Sumber data agregasi:

- `KaderKhusus` -> kolom 3-7 (kader kesehatan + klasifikasi gizi/kesling/phbs/kb).
- `Posyandu` -> kolom 8-9.
- `DataKegiatanWarga` -> kolom 10-11 (indikator PKG/TBC berbasis keyword operasional).
- `DataWarga`/`DataWargaAnggota` (via metric rekap) -> kolom 12-24.
- `ProgramPrioritas` -> kolom 25-27 (program unggulan berbasis keyword operasional).

Route report:

- `desa.catatan-keluarga.data-kegiatan-pkk-pokja-iv.report`
- `kecamatan.catatan-keluarga.data-kegiatan-pkk-pokja-iv.report`

Use case + repository:

- `app/Domains/Wilayah/CatatanKeluarga/UseCases/ListScopedDataKegiatanPkkPokjaIvUseCase.php`
- `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php` (`getDataKegiatanPkkPokjaIvByLevelAndArea`)

Dokumen sumber data rinci + status cek manual:

- `docs/domain/DATA_KEGIATAN_PKK_POKJA_IV_4_24_SUMBER_DATA.md`

## Catatan Deviasi

- Lihat `DV-015` pada `docs/domain/DOMAIN_DEVIATION_LOG.md`.
