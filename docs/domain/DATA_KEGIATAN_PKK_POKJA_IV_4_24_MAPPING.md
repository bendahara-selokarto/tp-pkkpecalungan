# Mapping Lampiran 4.24 - Data Kegiatan PKK (Pokja IV)

## Sumber Autentik
- Text-layer: `docs/referensi/232.pdf` (cara pengisian Pokja IV, kolom 1-27).
- Verifikasi visual header tabel: screenshot lampiran 4.24 pada sesi implementasi ini.

## Hasil Baca Header (Final)
- Judul: `DATA KEGIATAN PKK`
- Subjudul: `POKJA IV`
- Total kolom: `27`
- Struktur merge:
  - `NO` (`rowspan=3`)
  - `NAMA WILAYAH (...)` (`rowspan=3`)
  - `JUMLAH KADER` (`colspan=5`)
    - `KADER KESEHATAN` (`rowspan=2`)
    - `KADER YANG ADA` (`colspan=4`) -> `GIZI`, `KESLING`, `PHBS`, `KB`
  - `POSYANDU` (`rowspan=3`)
  - `IMUNISASI / VAKSINASI BAYI/BALITA` (`rowspan=3`)
  - `PKG` (`rowspan=3`)
  - `TBC` (`rowspan=3`)
  - `KELESTARIAN LINGKUNGAN HIDUP` (`colspan=7`)
    - `JUMLAH RUMAH YANG MEMILIKI` (`colspan=3`) -> `JAMBAN (WC)`, `SPAL`, `TPS`
    - `JUMLAH MCK` (`rowspan=2`)
    - `JUMLAH KRT YANG MENGGUNAKAN AIR` (`colspan=3`) -> `PDAM`, `SUMUR`, `LAIN-LAIN`
  - `PERENCANAAN SEHAT` (`colspan=6`)
    - `JUMLAH PUS` (`rowspan=2`)
    - `JUMLAH WUS` (`rowspan=2`)
    - `JUMLAH AKSEPTOR KB` (`colspan=2`) -> `L`, `P`
    - `JML. KK YANG MEMILIKI TABUNGAN KELUARGA` (`rowspan=2`)
    - `JML. KK YANG MEMILIKI ASURANSI KESEHATAN` (`rowspan=2`)
  - `PROGRAM UNGGULAN` (`colspan=3`) -> `KESEHATAN`, `KELESTARIAN LINGKUNGAN HIDUP`, `PERENCANAAN SEHAT`

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

## Catatan Deviasi
- Lihat `DV-015` pada `docs/domain/DOMAIN_DEVIATION_LOG.md`.
