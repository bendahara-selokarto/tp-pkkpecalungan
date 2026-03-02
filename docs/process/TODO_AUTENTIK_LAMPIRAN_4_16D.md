# TODO ALA416D Autentik Lampiran 4.16d
Tanggal: 2026-03-02 (normalisasi metadata; perlu verifikasi historis)  
Status: `done`

## Konteks
- Dokumen resmi baru ditetapkan: `d:\pedoman\185.pdf`.
- User mengirim screenshot header tabel Lampiran 4.16d dengan struktur merge kompleks.
- Header menunjukkan total `33` kolom dan basis agregasi baris per `NOMOR RW`.

## Target Hasil
- Struktur autentik 4.16d terdokumentasi lengkap sampai level merge header.
- Terminology map dan domain contract matrix memuat status 4.16d secara eksplisit.
- Implementasi report 4.16d aktif end-to-end pada flow `catatan-keluarga`.

## Keputusan
- [x] `d:\pedoman\185.pdf` ditetapkan sebagai sumber autentik 4.16d.
- [x] Verifikasi visual header tabel 4.16d dilakukan dari screenshot autentik.
- [x] Peta final kolom + merge cell 33 kolom dikunci pada dokumen mapping.
- [x] Status fase ditetapkan `implemented (report-only)` dengan catatan deviasi judul sementara.

## Langkah Eksekusi
- [x] Buat dokumen mapping: `docs/domain/LAMPIRAN_4_16D_MAPPING.md`.
- [x] Sinkronkan status 4.16d ke terminology map dan domain contract matrix.
- [x] Sinkronkan implementasi report 4.16d (repository/use case/controller/view/route/UI).
- [x] Tambahkan regression test 4.16d (header + akses scope + stale metadata).
- [x] Konfirmasi judul canonical final 4.16d dari token identitas halaman penuh (deviasi ditutup pada sesi validasi 2026-02-22).

## Validasi
- [x] Peta header tabel 33 kolom tervalidasi visual.
- [x] Merge cell (`rowspan`/`colspan`) tercatat pada peta struktur.
- [x] Referensi mapping 4.16d sudah terhubung ke dokumen kontrak domain.
- [x] Test report 4.16d (feature regression) hijau.

## Risiko
- Risiko refactor mahal jika implementasi dilakukan sebelum kontrak 33 kolom dikunci.

## Fallback Plan
- [x] Gunakan hasil verifikasi visual sebagai acuan kontrak header sementara.
- [x] Setelah token identitas final tersedia, sinkronkan judul canonical ke implementasi dan dokumen kontrak.

## Catatan Keputusan Final
- Keputusan implementasi saat ini: Lampiran 4.16d **sudah diaktifkan sebagai report PDF** (tanpa menu domain input baru) melalui flow `catatan-keluarga`.
- Endpoint aktif:
  - `/desa/catatan-keluarga/rekap-rw/report/pdf`
  - `/kecamatan/catatan-keluarga/rekap-rw/report/pdf`
- Dashboard trigger audit:
  - Tidak ada menu/domain input baru, sehingga KPI coverage dashboard tidak berubah.
