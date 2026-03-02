# TODO Autentik Data Kegiatan PKK 4.23-4.24
Tanggal: 2026-03-02 (normalisasi metadata; perlu verifikasi historis)  
Status: `done`

## Konteks
- User menyediakan lampiran autentik:
  - `docs/referensi/229-230.pdf` (cara pengisian Pokja III)
  - `docs/referensi/232.pdf` (cara pengisian Pokja IV)
- Verifikasi visual struktur header tabel dilakukan dari screenshot lampiran 4.23 dan 4.24 pada sesi ini.

## Target Hasil
- Endpoint report PDF baru untuk:
  - Lampiran 4.23 (`Data Kegiatan PKK - Pokja III`)
  - Lampiran 4.24 (`Data Kegiatan PKK - Pokja IV`)
- Tombol cetak tersedia di halaman `Catatan Keluarga` desa/kecamatan.
- Kontrak domain + checklist docs tersinkron.

## Langkah Eksekusi
- [x] Validasi dokumen autentik via text-layer (`pdf-parse text`) untuk token identitas.
- [x] Validasi visual header tabel (jumlah kolom + merge header) dari screenshot autentik.
- [x] Tambah kontrak repository + use case scoped untuk 4.23 dan 4.24.
- [x] Tambah stream method pada `CatatanKeluargaPrintController`.
- [x] Tambah route report `desa`/`kecamatan` untuk 4.23 dan 4.24.
- [x] Tambah view PDF autentik:
  - `resources/views/pdf/data_kegiatan_pkk_pokja_iii_report.blade.php`
  - `resources/views/pdf/data_kegiatan_pkk_pokja_iv_report.blade.php`
- [x] Tambah tombol cetak di:
  - `resources/js/Pages/Desa/CatatanKeluarga/Index.vue`
  - `resources/js/Pages/Kecamatan/CatatanKeluarga/Index.vue`
- [x] Tambah/ubah test feature header + akses route + stale-metadata + agregasi repository.
- [x] Sinkronisasi dokumen domain/checklist/deviasi.

## Validasi
- [x] `php artisan route:list --name=catatan-keluarga.data-kegiatan-pkk-pokja`
  - hasil: 4 route report (`desa` + `kecamatan` untuk 4.23 dan 4.24) terdaftar.
- [x] `php artisan test --filter=RekapCatatanDataKegiatanWargaReportPrintTest`
  - hasil: 27 test pass.
- [x] `php artisan test --filter=scope_metadata_tidak_sinkron`
  - hasil: 28 test pass.
- [x] `php artisan test`
  - hasil: 682 test pass.

## Risiko
- Sumber data operasional belum memiliki seluruh field dedicated autentik 4.23/4.24 per kolom.
- Sejumlah kolom diisi melalui inferensi keyword lintas modul (risiko undercount/overcount).

## Keputusan
- Dipakai strategi `report-only` berbasis agregasi lintas modul yang tersedia.
- Keterbatasan data dicatat eksplisit di `DOMAIN_DEVIATION_LOG` (DV-014, DV-015).
