# TODO Autentik Data Umum PKK 4.20a
Tanggal: 2026-03-02 (normalisasi metadata; perlu verifikasi historis)  
Status: `done`

## Konteks
- Dokumen autentik yang dibaca: `docs/referensi/213.pdf` (Lampiran `4.20a`) dan screenshot header tabel dari user pada sesi validasi 2026-02-22.
- Lampiran `4.20a` memuat format `DATA UMUM PKK` dengan header bertingkat dan penggabungan sel (`rowspan`/`colspan`) hingga 20 kolom.
- Flow wajib yang dipakai: `text-layer -> verifikasi visual manual -> laporkan/konfirmasi -> sinkronkan`.

## Target Hasil
- Struktur autentik 4.20a terdokumentasi sampai peta merge header (`rowspan`/`colspan`) 20 kolom.
- Implementasi report 4.20a aktif end-to-end pada flow `catatan-keluarga` untuk scope `desa` dan `kecamatan`.
- Terminology map + domain contract matrix + deviasi domain tersinkron.

## Keputusan
- [x] Ekstraksi text-layer dari `213.pdf` dijalankan terlebih dahulu untuk deteksi token identitas dokumen.
- [x] Karena text-layer tidak cukup terbaca untuk rekonstruksi header tabel, verifikasi visual screenshot dipakai sebagai sumber final peta merge header.
- [x] Implementasi 4.20a dijalankan sebagai report-only tanpa menu input baru.
- [x] Dashboard trigger dinilai tidak relevan untuk KPI/input coverage karena 4.20a merupakan agregasi turunan dari data existing (`data_wargas`, `anggota_tim_penggeraks`, `anggota_pokjas`, `kader_khusus`) tanpa sumber input baru.

## Langkah Eksekusi
- [x] Buat mapping: `docs/domain/DATA_UMUM_PKK_4_20A_MAPPING.md`.
- [x] Implementasi repository + use case + controller + route + view + UI trigger 4.20a.
- [x] Tambah regression test 4.20a (header + agregasi + akses scope + stale metadata).
- [x] Sinkronkan terminology map + domain contract matrix.
- [x] Sinkronkan deviasi domain untuk keterbatasan sumber data `tenaga sekretariat`.

## Validasi
- [x] Text-layer parser dijalankan pada `docs/referensi/213.pdf`.
- [x] Header 20 kolom tervalidasi visual dengan merge cell (`rowspan`/`colspan`).
- [x] Feature test concern rekap catatan keluarga mencakup route/cetak 4.20a pada scope `desa` dan `kecamatan`.
- [x] Route report 4.20a terdaftar untuk scope `desa` dan `kecamatan`.

## Risiko
- Risiko klasifikasi `tenaga sekretariat (honorer/bantuan)` masih bergantung inferensi teks `jabatan` karena belum ada field dedicated.
- Risiko konsistensi grup `nama dusun/lingkungan` bergantung pola alamat sumber pada tabel anggota/kader.

## Fallback Plan
- [x] Jika pola `jabatan` tidak memuat token `honorer`/`bantuan`, nilai kolom tenaga sekretariat dipertahankan `0` dan didokumentasikan sebagai keterbatasan kontrak operasional.
- [x] Jika token dusun/lingkungan tidak terbaca pada alamat anggota/kader, data dikelompokkan ke label `SEBUTAN LAIN` agar report tetap stabil.
