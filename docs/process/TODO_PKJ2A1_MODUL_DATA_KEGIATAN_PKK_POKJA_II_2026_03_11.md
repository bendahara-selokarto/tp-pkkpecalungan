# TODO PKJ2A1 Modul Data Kegiatan PKK Pokja II

Tanggal: 2026-03-11  
Status: `planned`
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Lampiran `4.22` (Data Kegiatan PKK Pokja II) sudah tervalidasi header autentik, tetapi belum ada modul/report khusus.
- Banyak kolom memerlukan data yang belum tersedia di skema saat ini (contoh: BKB, peserta pelatihan, pra koperasi/UP2K).
- Perlu strategi normalisasi data agar struktur input dan report tidak memakai field gabungan/teks bebas yang sulit diaudit.

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

- [ ] Spesifikasi sumber data + gap per kolom terkunci (termasuk keputusan mapping jenis/label).
- [ ] Rencana normalisasi database untuk data Pokja II disetujui (tabel baru/extend tabel existing).
- [ ] Implementasi report + modul input sesuai kontrak dan test matrix minimum.

## Langkah Eksekusi

- [ ] Analisis dependency modul yang sudah ada (Kejar Paket, Taman Bacaan, Kader Khusus, Koperasi, Simulasi Penyuluhan, Data Pelatihan Kader) dan cek kecukupan kolom.
- [ ] Kunci keputusan mapping untuk kolom ambigu (PAUD sejenis, Tutor simulasi, kader dilatih, pra koperasi/UP2K, tiga buta).
- [ ] Desain normalisasi:
  - [ ] Tentukan apakah extend tabel existing atau buat tabel baru per kelompok data.
  - [ ] Pastikan setiap tabel baru memiliki `level`, `area_id`, `created_by`, `tahun_anggaran`.
  - [ ] Hindari multi-value pada field teks; gunakan tabel referensi/enum bila perlu.
- [ ] Patch minimal pada boundary arsitektur:
  - [ ] Route + middleware `scope.role:{desa|kecamatan}`.
  - [ ] Request validation + normalisasi input.
  - [ ] Use case + repository agregasi report.
  - [ ] Policy + scope service.
  - [ ] Inertia page + PDF report view.
- [ ] Sinkronisasi dokumen concern terkait (domain matrix, deviation log bila ada deviasi).
- [ ] Dashboard trigger audit (apakah report perlu masuk coverage dashboard).

## Validasi

- [ ] L1: targeted tests untuk policy/scope + report print.
- [ ] L2: regression test concern `catatan-keluarga` + modul input terkait.
- [ ] L3: `php artisan test --compact` (karena modul baru + repository/authorization).

## Risiko

- Kesenjangan data (kolom peserta/pelatihan) jika tidak ada skema input khusus.
- Free-text `jenis_*` berpotensi drift tanpa normalisasi referensi.
- Overlap/duplikasi data jika extend tabel existing tanpa migration map yang jelas.

## Keputusan

- [x] K1: Tetapkan `JML KLP` Kejar Paket = count record per jenis (A/B/C/KF).
- [ ] K2: Tentukan sumber resmi untuk kolom `PAUD Sejenis`.
- [x] K3: Sumber tutor `KF/PAUD` memakai tabel tutor khusus.
- [x] K4: `Jumlah Kader yang sudah dilatih` memakai tabel rekap pelatihan kader per kategori (LP3/TPK 3 PKK/DAMAS).
- [ ] K5: Putuskan struktur data `Pra Koperasi/Usaha Bersama/UP2K` (level Pemula/Madya/Utama/Mandiri + peserta).
- [ ] K6: Tentukan cara capture `Jml Warga yang masih 3 (tiga) buta`.

## Keputusan Arsitektur (Jika Ada)

- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md` jika strategi normalisasi dipilih.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jika sumber data belum siap, tahan implementasi report dan pertahankan status `not implemented`.
- Jika normalisasi batch tertunda, siapkan adapter sementara yang membaca tabel existing dengan catatan deviasi.

## Output Final

- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak.
- [ ] Hasil validasi + residual risk.
