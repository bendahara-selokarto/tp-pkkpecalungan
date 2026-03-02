# TODO ACP416C Autentik Catatan PKK RW 4.16c
Tanggal: 2026-03-02 (normalisasi metadata; perlu verifikasi historis)  
Status: `done`

## Konteks
- Dokumen resmi baru ditetapkan: `d:\pedoman\183.pdf`.
- Judul terbaca: `CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RW`.
- Lampiran terbaca: `LAMPIRAN 4.16c`.

## Target Hasil
- Struktur autentik 4.16c terdokumentasi lengkap sampai level merge header.
- Terminology map dan domain contract matrix memuat status 4.16c secara eksplisit.
- Sinkronisasi implementasi hanya dilakukan setelah peta header tervalidasi.

## Keputusan
- [x] `d:\pedoman\183.pdf` ditetapkan sebagai sumber autentik 4.16c.
- [x] Text-layer parser dipakai untuk identitas dokumen.
- [x] Status sementara ditetapkan `belum siap sinkronisasi` sampai verifikasi visual header tabel selesai.
- [x] Header tabel 4.16c difinalkan melalui verifikasi visual screenshot autentik hingga level merge (`rowspan`/`colspan`).

## Langkah Eksekusi
- [x] Baca awal text-layer dan catat token identitas dokumen.
- [x] Buat dokumen mapping awal: `docs/domain/CATATAN_PKK_RW_4_16C_MAPPING.md`.
- [x] Sinkronkan status awal ke terminology map dan contract matrix.
- [x] Lakukan verifikasi visual header tabel hingga level `rowspan/colspan`.
- [x] Konfirmasi peta final kolom + merge cell.
- [x] Sinkronkan implementasi report 4.16c pada flow `catatan-keluarga` (report-only, tanpa menu input baru).

## Validasi
- [x] Token identitas dokumen 4.16c terbaca.
- [x] Mapping awal 4.16c tersedia.
- [x] Peta header tabel lengkap tervalidasi visual.
- [x] Regression test report 4.16c disiapkan (header + akses scope + stale metadata).

## Risiko
- Risiko salah mapping jika implementasi dilakukan sebelum verifikasi visual header tabel.
- Risiko refactor mahal jika merge-header dikunci terlambat.

## Fallback Plan
- [x] Tahan sinkronisasi implementasi sampai peta header lengkap.
- [x] Gunakan dokumen autentik sebagai source of truth saat parser tidak memadai.

## Catatan Keputusan Final Fase Dokumen
- Status fase saat ini: **implemented (report-only via catatan-keluarga)**.
- Endpoint aktif:
  - `/desa/catatan-keluarga/catatan-pkk-rw/report/pdf`
  - `/kecamatan/catatan-keluarga/catatan-pkk-rw/report/pdf`
- Catatan implementasi:
  - Tidak ada menu/domain input baru.
  - Agregasi 4.16c mengikuti data `data_wargas` + `data_warga_anggotas` + indikator area-level lintas modul, selaras pola 4.16a/4.16b.
