# TODO ACT417A Autentik Catatan TP PKK 4.17a
Tanggal: 2026-03-02 (normalisasi metadata; perlu verifikasi historis)  
Status: `done`

## Konteks
- Dokumen resmi yang dibaca melalui screenshot: `LAMPIRAN 4.17a`.
- Judul terlihat: `CATATAN DATA DAN KEGIATAN WARGA TP PKK`.
- Header tabel kompleks dengan total `33` kolom.

## Target Hasil
- Struktur autentik 4.17a terdokumentasi lengkap sampai level merge header.
- Implementasi report aktif end-to-end pada flow `catatan-keluarga`.
- Terminology map + domain contract matrix tersinkron dengan status implementasi.

## Keputusan
- [x] Screenshot autentik 4.17a dipakai sebagai sumber verifikasi visual struktur header.
- [x] Peta final kolom + merge 33 kolom dikunci pada dokumen mapping.
- [x] Implementasi report 4.17a dijalankan report-only tanpa menu input baru.

## Langkah Eksekusi
- [x] Buat mapping: `docs/domain/CATATAN_TP_PKK_DESA_KELURAHAN_4_17A_MAPPING.md`.
- [x] Implementasi repository + use case + controller + route + view + UI trigger.
- [x] Tambah regression test 4.17a (header + akses scope + stale metadata).
- [x] Sinkronkan terminology map + domain contract matrix.
- [x] Catat deviasi jika ada gap kontrak sumber data.

## Validasi
- [x] Header 33 kolom tervalidasi visual.
- [x] Merge cell (`rowspan`/`colspan`) tercatat pada mapping.
- [x] Feature test report 4.17a hijau.
- [x] Route report 4.17a terdaftar untuk scope desa/kecamatan.
- [x] Full test suite hijau setelah implementasi.

## Risiko
- Risiko nama dusun/lingkungan tidak konsisten jika alamat sumber tidak baku.

## Fallback Plan
- [x] Jika pola alamat tidak mengandung dusun/lingkungan, fallback gunakan label `dasawisma`.
- [x] Deviasi struktur sumber data dicatat pada `DOMAIN_DEVIATION_LOG.md`.
