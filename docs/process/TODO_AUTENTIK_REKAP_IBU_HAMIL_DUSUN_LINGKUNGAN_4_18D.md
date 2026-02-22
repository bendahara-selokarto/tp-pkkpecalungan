# TODO Autentik Rekap Ibu Hamil Dusun/Lingkungan 4.18d

## Konteks
- Dokumen resmi yang dibaca melalui screenshot: `LAMPIRAN 4.18d`.
- Judul dokumen menunjukkan buku catatan tingkat `PKK Dusun/Lingkungan`.
- Header tabel kompleks dengan total `17` kolom.

## Target Hasil
- Struktur autentik 4.18d terdokumentasi sampai level merge header.
- Implementasi report 4.18d aktif end-to-end pada flow `catatan-keluarga`.
- Terminology map + domain contract matrix + deviation log tersinkron.

## Keputusan
- [x] Screenshot autentik 4.18d dipakai sebagai sumber verifikasi visual struktur header.
- [x] Peta final kolom + merge 17 kolom dikunci pada dokumen mapping.
- [x] Implementasi report 4.18d dijalankan report-only tanpa menu input baru.

## Langkah Eksekusi
- [x] Buat mapping: `docs/domain/REKAP_IBU_HAMIL_DUSUN_LINGKUNGAN_4_18D_MAPPING.md`.
- [x] Implementasi repository + use case + controller + route + view + UI trigger.
- [x] Tambah regression test 4.18d (header + akses scope + stale metadata).
- [x] Sinkronkan terminology map + domain contract matrix.
- [x] Sinkronkan deviasi indikator maternal 4.18 ke cakupan 4.18d.

## Validasi
- [x] Header 17 kolom tervalidasi visual.
- [x] Merge cell (`rowspan`/`colspan`) tercatat pada mapping.
- [x] Feature test report 4.18d hijau.
- [x] Route report 4.18d terdaftar untuk scope desa/kecamatan.
- [x] Validasi targeted test concern rekap catatan keluarga hijau.

## Risiko
- Risiko indikator maternal/kelahiran/kematian masih bergantung pada inferensi `keterangan` karena belum ada field dedicated.

## Fallback Plan
- [x] Jika token maternal/kematian tidak cukup, nilai indikator tetap diturunkan `0` dan dicatat sebagai keterbatasan kontrak operasional.
- [x] Keterbatasan kontrak diselaraskan pada `docs/domain/DOMAIN_DEVIATION_LOG.md`.
