# TODO Autentik Rekap Ibu Hamil PKK RW 4.18c
Tanggal: 2026-03-02 (normalisasi metadata; perlu verifikasi historis)  
Status: `done`

## Konteks
- Dokumen resmi yang dibaca melalui screenshot: `LAMPIRAN 4.18c`.
- Halaman yang diterima berisi `cara pengisian` dengan kontrak kolom untuk rekap tingkat PKK RW.
- Kontrak menyebut kolom `1-16`, dengan kolom `4-15` berasal dari penjumlahan buku catatan tingkat PKK RT.

## Target Hasil
- Struktur autentik 4.18c terdokumentasi pada level kontrak kolom dan metadata.
- Implementasi report 4.18c aktif end-to-end pada flow `catatan-keluarga`.
- Terminology map + domain contract matrix + deviation log tersinkron.

## Keputusan
- [x] Screenshot autentik 4.18c dipakai sebagai sumber verifikasi kontrak kolom.
- [x] Peta final kolom `1-16` dikunci pada dokumen mapping.
- [x] Implementasi report 4.18c dijalankan report-only tanpa menu input baru.

## Langkah Eksekusi
- [x] Buat mapping: `docs/domain/REKAP_IBU_HAMIL_PKK_RW_4_18C_MAPPING.md`.
- [x] Implementasi repository + use case + controller + route + view + UI trigger.
- [x] Tambah regression test 4.18c (header + akses scope + stale metadata).
- [x] Sinkronkan terminology map + domain contract matrix.
- [x] Sinkronkan deviasi indikator maternal 4.18 ke cakupan 4.18c.

## Validasi
- [x] Kontrak kolom 16 dari halaman cara pengisian tervalidasi.
- [x] Feature test report 4.18c hijau.
- [x] Route report 4.18c terdaftar untuk scope desa/kecamatan.
- [x] Validasi targeted test concern rekap catatan keluarga hijau.

## Risiko
- Risiko indikator maternal/kelahiran/kematian masih bergantung pada inferensi `keterangan` karena belum ada field dedicated.

## Fallback Plan
- [x] Jika token maternal/kematian tidak cukup, nilai indikator tetap diturunkan `0` dan dicatat sebagai keterbatasan kontrak operasional.
- [x] Keterbatasan kontrak diselaraskan pada `docs/domain/DOMAIN_DEVIATION_LOG.md`.
