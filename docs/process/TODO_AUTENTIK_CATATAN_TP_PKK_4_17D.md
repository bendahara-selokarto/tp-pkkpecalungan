# TODO Autentik Catatan TP PKK 4.17d

## Konteks
- Dokumen resmi yang dibaca melalui screenshot: `LAMPIRAN 4.17d`.
- Judul terlihat: `CATATAN DATA DAN KEGIATAN WARGA`.
- Header tabel kompleks dengan total `37` kolom.

## Target Hasil
- Struktur autentik 4.17d terdokumentasi lengkap sampai level merge header.
- Implementasi report aktif end-to-end pada flow `catatan-keluarga`.
- Terminology map + domain contract matrix tersinkron dengan status implementasi.

## Keputusan
- [x] Screenshot autentik 4.17d dipakai sebagai sumber verifikasi visual struktur header.
- [x] Peta final kolom + merge 37 kolom dikunci pada dokumen mapping.
- [x] Implementasi report 4.17d dijalankan report-only tanpa menu input baru.

## Langkah Eksekusi
- [x] Buat mapping: `docs/domain/CATATAN_TP_PKK_PROVINSI_4_17D_MAPPING.md`.
- [x] Implementasi repository + use case + controller + route + view + UI trigger.
- [x] Tambah regression test 4.17d (header + akses scope + stale metadata).
- [x] Sinkronkan terminology map + domain contract matrix.
- [x] Catat deviasi jika ada gap kontrak sumber data.

## Validasi
- [x] Header 37 kolom tervalidasi visual.
- [x] Merge cell (`rowspan`/`colspan`) tercatat pada mapping.
- [x] Feature test report 4.17d targeted hijau (`php artisan test --filter=RekapCatatanDataKegiatanWargaReportPrintTest`).
- [x] Route report 4.17d terdaftar untuk scope desa/kecamatan.
- [x] Full test suite hijau setelah implementasi.

## Risiko
- Risiko nama kabupaten/kota tidak konsisten jika alamat sumber tidak baku.

## Fallback Plan
- [x] Jika pola alamat tidak mengandung kabupaten/kota, fallback tetap `-` untuk menjaga report tetap bisa dirender.
- [x] Deviasi struktur sumber data dicatat pada `DOMAIN_DEVIATION_LOG.md`.
