# TODO PKJ2A1 Modul Data Kegiatan PKK Pokja II

Tanggal: 2026-03-11  
Status: `done` (`state:report-docs-tests-synced`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Lampiran `4.22` (Data Kegiatan PKK Pokja II) sudah tervalidasi header autentik dan report PDF via `catatan-keluarga` sudah tersedia.
- Modul input `literasi-warga`, `bkb-kegiatan`, `tutor-khusus`, `pelatihan-kader-pokja-ii`, dan `pra-koperasi-up2k` sudah tersedia untuk mengisi indikator Pokja II.
- Masih perlu hardening: sinkronisasi dokumen kontrak + pemenuhan test matrix minimum untuk modul input/report.

## Kontrak Concern (Lock)

- Domain: `data-kegiatan-pkk-pokja-ii` (Lampiran 4.22).
- Role/scope target: `desa`, `kecamatan` (report + input scoped by `level`, `area_id`, `tahun_anggaran`).
- Boundary data: `app/Domains/Wilayah/CatatanKeluarga/*` (report), modul input terkait (Kejar Paket, Taman Bacaan, Kader Khusus, Koperasi, Simulasi Penyuluhan, Data Pelatihan Kader), dan tabel baru yang dinormalisasi jika diperlukan.
- Acceptance criteria:
  - Struktur header report sesuai mapping autentik `docs/domain/DATA_KEGIATAN_PKK_POKJA_II_4_22_MAPPING.md`.
  - Sumber data per kolom terdokumentasi pada `docs/domain/DATA_KEGIATAN_PKK_POKJA_II_4_22_SUMBER_DATA.md`.
  - Semua query report terkunci `level + area_id + tahun_anggaran`.
  - Tidak ada coupling baru ke tabel legacy.
  - Test minimal menu baru terpenuhi (AGENTS.md section 8).
- Dampak keputusan arsitektur: `ya` (pemilihan strategi normalisasi + kemungkinan tabel baru).

## Target Hasil

- [x] Spesifikasi sumber data + gap per kolom terkunci (termasuk keputusan mapping jenis/label).
- [x] Rencana normalisasi database untuk data Pokja II disetujui (tabel existing aktif).
- [x] Implementasi report + modul input sesuai kontrak dan test matrix minimum.

## Langkah Eksekusi

- [x] Analisis dependency modul yang sudah ada (Kejar Paket, Taman Bacaan, Kader Khusus, Koperasi, Simulasi Penyuluhan, Data Pelatihan Kader) dan cek kecukupan kolom.
- [x] Kunci keputusan mapping untuk kolom ambigu (PAUD sejenis, Tutor simulasi, kader dilatih, pra koperasi/UP2K, tiga buta).
- [x] Desain normalisasi:
  - [x] Tentukan apakah extend tabel existing atau buat tabel baru per kelompok data.
  - [x] Pastikan setiap tabel baru memiliki `level`, `area_id`, `created_by`, `tahun_anggaran`.
  - [x] Hindari multi-value pada field teks; gunakan tabel referensi/enum bila perlu.
- [x] Patch minimal pada boundary arsitektur:
  - [x] Route + middleware `scope.role:{desa|kecamatan}`.
  - [x] Request validation + normalisasi input.
  - [x] Use case + repository agregasi report.
  - [x] Policy + scope service.
  - [x] Inertia page + PDF report view.
- [x] Sinkronisasi dokumen concern terkait (domain matrix, deviation log bila ada deviasi).
- [x] Dashboard trigger audit (report sudah masuk coverage dashboard).

## Validasi

- [x] L1: targeted tests untuk policy/scope + report print (`php artisan test --filter=RekapCatatanDataKegiatanWargaReportPrintTest --compact`).
- [x] L2: regression test concern `catatan-keluarga` + modul input terkait (cakup oleh test report di atas).
- [x] L3: `php artisan test --compact` (2026-03-12).

## Risiko

- Kesenjangan data (kolom peserta/pelatihan) jika tidak ada skema input khusus.
- Free-text `jenis_*` berpotensi drift tanpa normalisasi referensi.
- Overlap/duplikasi data jika extend tabel existing tanpa migration map yang jelas.

## Keputusan

- [x] K1: Tetapkan `JML KLP` Kejar Paket = count record per jenis (A/B/C/KF).
- [x] K2: Sumber resmi `PAUD Sejenis` memakai `kejar_pakets` (jenis = PAUD).
- [x] K3: Sumber tutor `KF/PAUD` memakai tabel tutor khusus.
- [x] K4: `Jumlah Kader yang sudah dilatih` memakai tabel rekap pelatihan kader per kategori (LP3/TPK 3 PKK/DAMAS).
- [x] K5: Putuskan struktur data `Pra Koperasi/Usaha Bersama/UP2K` (level Pemula/Madya/Utama/Mandiri + peserta).
- [x] K6: Tentukan cara capture `Jml Warga yang masih 3 (tiga) buta`.

## Keputusan Arsitektur (Jika Ada)

- [x] Tidak perlu ADR baru (modul + normalisasi sudah ada, concern fokus hardening doc + test).
- [x] Status ADR tidak berubah.

## Fallback Plan

- Jika sumber data belum siap, tahan implementasi report dan pertahankan status `not implemented`.
- Jika normalisasi batch tertunda, siapkan adapter sementara yang membaca tabel existing dengan catatan deviasi.

## Output Final

- [x] Ringkasan apa yang diubah dan kenapa.
- [x] Daftar file terdampak.
- [x] Hasil validasi + residual risk.

## Progress Log

- 2026-03-11: sumber data Pokja II disinkronkan ke implementasi aktif, test header + agregasi ditambahkan, dan `php artisan test --filter=RekapCatatanDataKegiatanWargaReportPrintTest --compact` `PASS`.
- 2026-03-12: doc-hardening sidebar/terminologi/coverage + test matrix modul Pokja II ditambahkan, `php artisan test --compact` `PASS`.
