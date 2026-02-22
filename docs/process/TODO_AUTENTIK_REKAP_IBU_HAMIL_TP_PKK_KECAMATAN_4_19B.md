# TODO Autentik Rekap Ibu Hamil TP PKK Kecamatan 4.19b

## Konteks
- Dokumen autentik yang dibaca: `docs/referensi/207.pdf` (Lampiran `4.19b`) dan screenshot header tabel dari user pada sesi validasi 2026-02-22.
- Lampiran `4.19b` memuat rekap tingkat `TP PKK Kecamatan` dengan header bertingkat dan penggabungan sel kompleks.
- Flow wajib: `text-layer -> verifikasi visual manual -> laporkan/konfirmasi -> sinkronkan`.

## Target Hasil
- Struktur autentik 4.19b terdokumentasi sampai level merge header (`rowspan`/`colspan`).
- Implementasi report 4.19b aktif end-to-end pada flow `catatan-keluarga` untuk scope `desa` dan `kecamatan`.
- Terminology map + domain contract matrix + deviasi domain tersinkron.

## Keputusan
- [x] Ekstraksi text-layer dari `207.pdf` dipakai untuk token identitas dokumen.
- [x] Verifikasi visual screenshot dipakai sebagai sumber final peta merge header 19 kolom.
- [x] Implementasi 4.19b dijalankan sebagai report-only tanpa menu input baru.
- [x] Dashboard trigger dinilai tidak relevan untuk KPI/input coverage karena 4.19b merupakan agregasi turunan dari data existing (`data_warga*`) tanpa sumber input baru.

## Langkah Eksekusi
- [x] Buat mapping: `docs/domain/REKAP_IBU_HAMIL_TP_PKK_KECAMATAN_4_19B_MAPPING.md`.
- [x] Implementasi repository + use case + controller + route + view + UI trigger.
- [x] Tambah regression test 4.19b (header + agregasi + akses scope + stale metadata).
- [x] Sinkronkan terminology map + domain contract matrix.
- [x] Sinkronkan deviasi indikator maternal ke cakupan 4.19b.

## Validasi
- [x] Token identitas dokumen 4.19b terbaca dari text-layer parser.
- [x] Header 19 kolom tervalidasi visual dengan merge cell (`rowspan`/`colspan`).
- [x] Feature test concern rekap catatan keluarga hijau untuk coverage 4.19b.
- [x] Route report 4.19b terdaftar untuk scope desa/kecamatan.

## Risiko
- Risiko indikator maternal/kelahiran/kematian masih bergantung pada inferensi `keterangan` karena belum ada field dedicated.
- Risiko kualitas agregasi desa/kelurahan bergantung pada konsistensi token wilayah pada alamat sumber.

## Fallback Plan
- [x] Jika token indikator maternal/kematian tidak cukup, nilai indikator tetap diturunkan `0` dan didokumentasikan sebagai keterbatasan kontrak operasional.
- [x] Jika token desa/kelurahan tidak terbaca, fallback label `-` dipertahankan agar report tetap dapat dihasilkan tanpa melanggar scope.
