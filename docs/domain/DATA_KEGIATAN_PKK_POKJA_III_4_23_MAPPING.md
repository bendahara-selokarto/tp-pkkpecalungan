# Mapping Lampiran 4.23 - Data Kegiatan PKK (Pokja III)

## Sumber Autentik
- Text-layer: `docs/referensi/229-230.pdf` (cara pengisian Pokja III, kolom 1-20).
- Verifikasi visual header tabel: screenshot lampiran 4.23 pada sesi implementasi ini.

## Hasil Baca Header (Final)
- Judul: `DATA KEGIATAN PKK`
- Subjudul: `POKJA III`
- Total kolom: `20`
- Struktur merge:
  - `NO` (`rowspan=3`)
  - `NAMA WILAYAH (...)` (`rowspan=3`)
  - `JUMLAH KADER` (`colspan=3`)
    - `PANGAN`, `SANDANG`, `TATA LAKSANA RUMAH TANGGA` (`rowspan=2`)
  - `PANGAN` (`colspan=9`)
    - `MAKANAN POKOK` (`colspan=2`) -> `BERAS`, `NON BERAS`
    - `PEMANFAATAN PEKARANGAN/HATINYA PKK` (`colspan=7`) ->
      `PETERNAKAN`, `PERIKANAN`, `WARUNG HIDUP`, `LUMBUNG HIDUP`, `TOGA`, `TANAMAN KERAS`, `TANAMAN LAINNYA`
  - `JUMLAH INDUSTRI` (`colspan=3`) -> `PANGAN`, `SANDANG`, `JASA`
  - `JUMLAH` (`colspan=2`) -> `SEHAT LAYAK HUNI`, `TIDAK SEHAT DAN TIDAK LAYAK HUNI`
  - `KETERANGAN` (`rowspan=3`)

## Mapping Implementasi (Report-Only)
Target file:
- `resources/views/pdf/data_kegiatan_pkk_pokja_iii_report.blade.php`

Sumber data agregasi:
- `AnggotaPokja` -> kolom 3-5 (kader pangan/sandang/tata laksana).
- `DataPemanfaatanTanahPekaranganHatinyaPkk` -> kolom 8-14.
- `DataIndustriRumahTangga` -> kolom 15-17.
- `DataWarga`/`DataWargaAnggota` (via metric rekap) -> kolom 6-7, 18-19 (sesuai ketersediaan data operasional).

Route report:
- `desa.catatan-keluarga.data-kegiatan-pkk-pokja-iii.report`
- `kecamatan.catatan-keluarga.data-kegiatan-pkk-pokja-iii.report`

Use case + repository:
- `app/Domains/Wilayah/CatatanKeluarga/UseCases/ListScopedDataKegiatanPkkPokjaIiiUseCase.php`
- `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php` (`getDataKegiatanPkkPokjaIiiByLevelAndArea`)

## Catatan Deviasi
- Lihat `DV-014` pada `docs/domain/DOMAIN_DEVIATION_LOG.md`.
