# Pedoman Domain Utama 101-150 (Roadmap Implementasi)

Sumber utama domain:
- https://pubhtml5.com/zsnqq/vjcf/basic/101-150

Aturan koherensi:
- Jika ada ketidaksesuaian istilah/label domain dengan dokumen lain, dokumen ini menjadi acuan domain utama.
- Aspek teknis implementasi (arsitektur, boundary repository, policy/scope, quality gate) tetap mengikuti `AGENTS.md`.

Urutan prioritas disusun dari paling mudah (kontrak jelas, konflik rendah) sampai yang lebih asumtif.

## Selesai
- [x] Tahap 1 - Buku Agenda Surat (`agenda-surat`) untuk desa dan kecamatan.
- [x] Tahap 2 - Buku Ekspedisi Surat sebagai report turunan `agenda-surat` (filter `jenis_surat=keluar`) tanpa tabel baru.
- [x] Tahap 3 - Validasi ulang kolom laporan terhadap template PDF sumber per lembar.
- [x] Tahap 4 - Penyesuaian format cetak jika ada perbedaan istilah/header antar level (medium risk, tetap reuse domain existing).
- [x] Tahap 5 - Refactor TODO sinkronisasi pedoman domain + kontrak domain baru.
- [x] Tahap 6 - Implementasi modul 4.14.1a `data-warga` (desa + kecamatan + report + policy + test matrix).
- [x] Tahap 7 - Implementasi modul 4.14.1b `data-kegiatan-warga` (desa + kecamatan + report + policy + test matrix).
- [x] Tahap 8 - Implementasi modul 4.14.2a `data-keluarga` (desa + kecamatan + report + policy + test matrix).

## Ringkasan Sinkronisasi Pedoman
- [x] Sumber canonical dipakai: https://pubhtml5.com/zsnqq/vjcf/basic/101-150
- [x] Fokus lampiran: 4.9a, 4.9b, 4.10, 4.11, 4.12, 4.13, 4.14.1a-4.14.4f, 4.15.

### Sudah Identik (Aplikasi vs Pedoman)
- [x] 4.9a Buku Daftar Anggota Tim Penggerak PKK -> `anggota-tim-penggerak`.
- [x] 4.9b Buku Daftar Kader Tim Penggerak PKK -> `kader-khusus` (domain teknis dipertahankan, label/PDF sudah identik pedoman).
- [x] 4.10 Buku Agenda Surat -> `agenda-surat`.
- [x] 4.11 Buku Keuangan -> `bantuans` (report arus masuk/keluar + saldo sudah dinormalisasi).
- [x] 4.12 Buku Inventaris -> `inventaris`.
- [x] 4.13 Buku Kegiatan -> domain `kegiatan` (route teknis: `activities`).
- [x] 4.14.4a Data Aset (Sarana) Desa/Kelurahan -> `warung-pkk` (domain teknis dipertahankan, label/PDF sudah identik pedoman).
- [x] 4.14.4b Data Isian Taman Bacaan/Perpustakaan -> `taman-bacaan`.
- [x] 4.14.4c Data Isian Koperasi -> `koperasi`.
- [x] 4.14.4d Data Isian Kejar Paket -> `kejar-paket`.
- [x] 4.14.4e Data Isian Posyandu oleh TP PKK -> `posyandu`.
- [x] 4.14.4f Data Isian Kelompok Simulasi dan Penyuluhan -> `simulasi-penyuluhan` (domain teknis dipertahankan, label/PDF sudah identik pedoman).

### List Baru (Kontrak Domain Disiapkan)
- [x] 4.14.1a Data Warga -> kontrak domain: `data-warga`.
- [x] 4.14.1b Data Kegiatan Warga -> kontrak domain: `data-kegiatan-warga`.
- [x] 4.14.2a Data Keluarga -> kontrak domain: `data-keluarga`.
- [x] 4.14.2b Data Pemanfaatan Tanah Pekarangan/HATINYA PKK -> kontrak domain: `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`.
- [x] 4.14.2c Data Industri Rumah Tangga -> kontrak domain: `data-industri-rumah-tangga`.
- [x] 4.14.3 Data Pelatihan Kader -> kontrak domain: `data-pelatihan-kader`.
- [x] 4.15 Catatan Keluarga -> kontrak domain: `catatan-keluarga` (rekap lintas lampiran terkait).
- [x] Verifikasi 4.14.5 pada baseline halaman 101-150: belum ditemukan pada sumber canonical saat ini.

### Roadmap Implementasi Modul Baru (Refactor TODO)
- [x] 4.14.1a Data Warga -> `data-warga` sudah terimplementasi end-to-end.
- [x] 4.14.1b Data Kegiatan Warga -> `data-kegiatan-warga` sudah terimplementasi end-to-end.
- [x] 4.14.2a Data Keluarga -> `data-keluarga` sudah terimplementasi end-to-end.
- [x] 4.14.2b Data Pemanfaatan Tanah Pekarangan/HATINYA PKK -> `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`.
- [x] 4.14.2c Data Industri Rumah Tangga -> `data-industri-rumah-tangga`.
- [x] 4.14.3 Data Pelatihan Kader -> `data-pelatihan-kader`.
- [x] 4.15 Catatan Keluarga -> `catatan-keluarga`.

## Akses Tulis Data (Scope Policy)
- `desa`: `desa-sekretaris`, `desa-bendahara`, `desa-pokja-i`, `desa-pokja-ii`, `desa-pokja-iii`, `desa-pokja-iv`, kompatibilitas `admin-desa`.
- `kecamatan`: `kecamatan-sekretaris`, `kecamatan-bendahara`, `kecamatan-pokja-i`, `kecamatan-pokja-ii`, `kecamatan-pokja-iii`, `kecamatan-pokja-iv`, kompatibilitas `admin-kecamatan`, `super-admin`.
- Guard backend tetap `scope.role:{desa|kecamatan}` + `Policy -> Scope Service`.

## Log Tahapan Pemrosesan
- `1944513` refactor(kader): align naming with pedoman 4.9b.
- `df80665` refactor(keuangan): normalize inflow outflow structure for pedoman 4.11.
- `051954d` refactor(warung-pkk): align naming with pedoman 4.14.4a.
- `4431aa6` refactor(simulasi-penyuluhan): align naming with pedoman 4.14.4f.

## Catatan Anti-Konflik
- `agenda-surat` dipakai sebagai source of truth surat masuk/keluar.
- Buku ekspedisi tidak menambah tabel/domain baru untuk menghindari duplikasi data surat keluar.
- Kontrak domain baru (4.14.1a-4.15) sudah dipetakan; implementasi masuk fase bertahap. Modul 4.14.1a (`data-warga`), 4.14.1b (`data-kegiatan-warga`), 4.14.2a (`data-keluarga`), 4.14.2b (`data-pemanfaatan-tanah-pekarangan-hatinya-pkk`), 4.14.2c (`data-industri-rumah-tangga`), 4.14.3 (`data-pelatihan-kader`), dan 4.15 (`catatan-keluarga`) sudah selesai.

## TODO Lanjutan - Backlog Tugas Nyata

Tujuan:
- Menjaga keamanan autentikasi/otorisasi tetap konsisten lintas modul.
- Menjaga koherensi istilah domain sesuai pedoman utama.
- Memastikan output generate PDF identik dengan dokumen baku.

Aturan eksekusi:
- Semua task harus menghasilkan artefak yang bisa direview.
- Semua task wajib mencantumkan bukti validasi (`route:list`, test targeted, `php artisan test` bila relevan).
- Jika ada deviasi dari pedoman, wajib dicatat di log deviasi.

### Sprint 1 - Baseline Kontrak dan Akses

- [x] `T1` Bangun `Domain Contract Matrix` lintas lampiran 4.9-4.15.
Output:
  - File baru `docs/domain/DOMAIN_CONTRACT_MATRIX.md`.
Kriteria selesai:
  - Setiap modul memiliki: `slug`, label pedoman, field canonical, label PDF, sumber halaman.

- [x] `T2` Bangun `Terminology Normalization Map`.
Output:
  - File baru `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`.
Kriteria selesai:
  - Semua perbedaan domain teknis vs label pedoman terdokumentasi.
  - Tidak ada label UI/PDF yang ambigu.

- [x] `T3` Bangun `Auth Coherence Matrix` untuk modul buku sekretaris.
Output:
  - File baru `docs/security/AUTH_COHERENCE_MATRIX.md`.
Kriteria selesai:
  - Memetakan `role -> scope -> area level -> akses modul (view/create/update/delete/print)`.
  - Mencakup role kompatibilitas (`admin-desa`, `admin-kecamatan`) dan `super-admin`.

- [x] `T4` Audit policy dan scope service untuk anti-bypass.
Output:
  - File baru `docs/security/POLICY_SCOPE_AUDIT_REPORT.md`.
Kriteria selesai:
  - Semua controller domain buku sekretaris diverifikasi memakai `authorize(...)`.
  - Tidak ada query akses lintas area tanpa guard `Policy -> Scope Service`.

### Sprint 2 - PDF Compliance dan Regression Guard

- [x] `T5` Buat `PDF Compliance Checklist` per modul.
Output:
  - File baru `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`.
Kriteria selesai:
  - Checklist mencakup: urutan kolom, header, format nilai, orientasi, footer metadata cetak.

- [x] `T6` Tambahkan test assertion header PDF untuk modul prioritas.
Output:
  - Update test feature report print pada modul prioritas.
Kriteria selesai:
  - Test gagal jika header kolom PDF berubah dari pedoman.
  - Minimal mencakup modul 4.14.1a-4.15.

- [x] `T7` Siapkan baseline fixture PDF untuk visual review.
Output:
  - Folder baru `tests/Fixtures/pdf-baseline/` + petunjuk penggunaan.
Kriteria selesai:
  - Setiap modul punya minimal 1 baseline fixture.
  - Ada prosedur compare manual/otomatis antar revisi.

- [x] `T8` Tambahkan regression checklist mismatch metadata akses.
Output:
  - File baru `docs/security/REGRESSION_CHECKLIST_AUTH_SCOPE.md`.
Kriteria selesai:
  - Memiliki skenario stale metadata: `scope=desa area=kecamatan` dan sebaliknya.
  - Ada mapping ke test yang menutup skenario tersebut.

### Sprint 3 - Change Gate dan Operasional Rilis

- [x] `T9` Terapkan `change gate` untuk perubahan kontrak domain.
Output:
  - File baru `docs/process/CHANGE_GATE_DOMAIN_CONTRACT.md`.
Kriteria selesai:
  - PR yang mengubah kontrak domain wajib update matrix + test terkait.

- [x] `T10` Terapkan release checklist khusus modul PDF.
Output:
  - File baru `docs/process/RELEASE_CHECKLIST_PDF.md`.
Kriteria selesai:
  - Checklist minimal: test scoped auth hijau, compliance checklist lulus, uji PDF sample per level.

- [x] `T11` Buat log deviasi pedoman domain.
Output:
  - File baru `docs/domain/DOMAIN_DEVIATION_LOG.md`.
Kriteria selesai:
  - Setiap deviasi memiliki: alasan teknis, dampak, rencana mitigasi, status.

### Task Operasional Berulang (Setiap Modul Baru/Perubahan Besar)

- [x] `R1` Jalankan `php artisan route:list --name=<slug-modul>` dan simpan ringkasan hasil. (baseline 2026-02-20, ulang setiap perubahan)
- [x] `R2` Jalankan test targeted modul terkait + policy/scope test. (baseline 2026-02-20, ulang setiap perubahan)
- [x] `R3` Jalankan `php artisan test` sebelum merge perubahan signifikan. (baseline 2026-02-20, ulang setiap perubahan)
- [x] `R4` Verifikasi PDF sample `desa` dan `kecamatan` terhadap pedoman utama. (baseline otomatis 2026-02-20, ulang setiap perubahan)

### Definition of Done TODO Lanjutan

- [x] Tidak ada mismatch label/header PDF terhadap pedoman domain utama. (baseline 2026-02-20)
- [x] Tidak ada drift akses antara `role`, `scope`, `area level`, dan policy. (baseline 2026-02-20)
- [x] Setiap perubahan domain memiliki jejak referensi pedoman dan bukti test validasi. (baseline 2026-02-20)
- [x] Semua artefak pada `docs/domain`, `docs/security`, `docs/pdf`, dan `docs/process` tersedia dan terisi. (baseline 2026-02-20)

## Backlog Berikutnya - Dashboard Coverage Dokumen

Latar belakang:
- Dashboard saat ini masih dominan berbasis domain `activities`, belum merepresentasikan cakupan dokumen 4.9a-4.15 secara utuh.

Rujukan rencana:
- `docs/process/DASHBOARD_CHART_ALIGNMENT_PLAN.md`

- [x] `D1` Bangun kontrak data dashboard lintas modul (4.9a-4.15) via UseCase + Repository.
- [x] `D2` Render chart coverage dokumen di `Dashboard.vue` (bukan hanya widget activity).
- [x] `D3` Tambahkan feature test scope dashboard coverage (desa, kecamatan, stale metadata).
- [x] `D4` Tambahkan unit test agregasi coverage per modul/lampiran.
- [x] `D5` (opsional) Tambahkan cache TTL pendek untuk query dashboard coverage besar.

## Lanjutan Pedoman Domain

- Analisis dan rencana implementasi untuk rentang halaman `202-211` tersedia di:
  - `docs/domain/PEDOMAN_DOMAIN_UTAMA_202_211.md`
