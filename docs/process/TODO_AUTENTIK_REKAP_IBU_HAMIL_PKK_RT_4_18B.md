# TODO Autentik Rekap Ibu Hamil PKK RT 4.18b

## Konteks
- Dokumen resmi yang dibaca melalui screenshot: `LAMPIRAN 4.18b`.
- Judul terlihat: `REKAPITULASI DATA/BUKU CATATAN IBU HAMIL, MELAHIRKAN, NIFAS, IBU MENINGGAL, KELAHIRAN BAYI, BAYI MENINGGAL DAN KEMATIAN BALITA DALAM KELOMPOK PKK RT`.
- Header tabel kompleks dengan total `15` kolom.

## Target Hasil
- Struktur autentik 4.18b terdokumentasi sampai level merge header.
- Implementasi report 4.18b aktif end-to-end pada flow `catatan-keluarga`.
- Terminology map + domain contract matrix + deviation log tersinkron dengan status implementasi.

## Keputusan
- [x] Screenshot autentik 4.18b dipakai sebagai sumber verifikasi visual struktur header.
- [x] Peta final kolom + merge 15 kolom dikunci pada dokumen mapping.
- [x] Implementasi report 4.18b dijalankan report-only tanpa menu input baru.

## Langkah Eksekusi
- [x] Buat mapping: `docs/domain/REKAP_IBU_HAMIL_PKK_RT_4_18B_MAPPING.md`.
- [x] Implementasi repository + use case + controller + route + view + UI trigger.
- [x] Tambah regression test 4.18b (header + akses scope + stale metadata).
- [x] Sinkronkan terminology map + domain contract matrix.
- [x] Catat deviasi kontrak sumber data di `docs/domain/DOMAIN_DEVIATION_LOG.md`.

## Validasi
- [x] Header 15 kolom tervalidasi visual.
- [x] Merge cell (`rowspan`/`colspan`) tercatat pada mapping.
- [x] Feature test report 4.18b hijau.
- [x] Route report 4.18b terdaftar untuk scope desa/kecamatan.
- [x] Validasi targeted test concern rekap catatan keluarga hijau.

## Risiko
- Risiko interpretasi indikator ibu/bayi/balita masih bergantung pada keyword `keterangan` karena belum ada field domain dedicated.

## Fallback Plan
- [x] Jika data tidak memuat token maternal/kematian yang cukup, nilai indikator diturunkan sebagai `0` dan tetap dilaporkan sebagai keterbatasan data operasional.
- [x] Deviasi kontrak sumber data dicatat pada `DOMAIN_DEVIATION_LOG.md`.
