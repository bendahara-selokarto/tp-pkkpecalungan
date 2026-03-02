# TODO Autentik Catatan TP PKK 4.17b
Tanggal: 2026-03-02 (normalisasi metadata; perlu verifikasi historis)  
Status: `done`

## Konteks
- Dokumen resmi yang dibaca melalui screenshot: `LAMPIRAN 4.17b`.
- Judul terlihat: `CATATAN DATA DAN KEGIATAN WARGA`.
- Header tabel kompleks dengan total `35` kolom.

## Target Hasil
- Struktur autentik 4.17b terdokumentasi lengkap sampai level merge header.
- Implementasi report aktif end-to-end pada flow `catatan-keluarga`.
- Terminology map + domain contract matrix tersinkron dengan status implementasi.

## Keputusan
- [x] Screenshot autentik 4.17b dipakai sebagai sumber verifikasi visual struktur header.
- [x] Peta final kolom + merge 35 kolom dikunci pada dokumen mapping.
- [x] Implementasi report 4.17b dijalankan report-only tanpa menu input baru.

## Langkah Eksekusi
- [x] Buat mapping: `docs/domain/CATATAN_TP_PKK_KECAMATAN_4_17B_MAPPING.md`.
- [x] Implementasi repository + use case + controller + route + view + UI trigger.
- [x] Tambah regression test 4.17b (header + akses scope + stale metadata).
- [x] Sinkronkan terminology map + domain contract matrix.
- [x] Catat deviasi jika ada gap kontrak sumber data.

## Validasi
- [x] Header 35 kolom tervalidasi visual.
- [x] Merge cell (`rowspan`/`colspan`) tercatat pada mapping.
- [x] Feature test report 4.17b targeted hijau (`php artisan test --filter=RekapCatatanDataKegiatanWargaReportPrintTest`).
- [x] Route report 4.17b terdaftar untuk scope desa/kecamatan.
- [x] Full test suite hijau setelah implementasi.

## Risiko
- Risiko nama desa/kelurahan tidak konsisten jika alamat sumber tidak baku.

## Fallback Plan
- [x] Jika pola alamat tidak mengandung desa/kelurahan, fallback ekstraksi pakai sumber teks rumah tangga yang tersedia.
- [x] Deviasi struktur sumber data dicatat pada `DOMAIN_DEVIATION_LOG.md`.
