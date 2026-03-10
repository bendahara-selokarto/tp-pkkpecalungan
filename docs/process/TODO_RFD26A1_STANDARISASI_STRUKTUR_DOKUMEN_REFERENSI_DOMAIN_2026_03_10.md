# TODO RFD26A1 Standarisasi Struktur Dokumen Referensi Domain

Tanggal: 2026-03-10  
Status: `planned`
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Folder `docs/referensi` saat ini berisi campuran artefak domain: PDF numerik mentah (`176.pdf`, `207.pdf`, dst), nama deskriptif bercampur spasi (`Rakernas X.pdf`, `cara pengisian 4.19a.pdf`), workbook (`.xlsx`), screenshot evidence, dan artefak lokal.
- Taksonomi yang sudah ada hanya parsial:
  - `_local/` untuk artefak non-tracked,
  - `_screenshots/` untuk sebagian bukti visual,
  - `excel/` untuk sebagian workbook,
  - tetapi banyak file kerja masih menumpuk di root `docs/referensi/`.
- Audit singkat baseline menemukan `12` PDF, `4` XLSX, `8` PNG, `1` DOCX, dan `2` README/marker.
- Drift utama concern ini:
  - penamaan file tidak konsisten (numeric-only, title-case dengan spasi, lowercase dengan spasi, folder campuran),
  - pemisahan antara dokumen canonical, bahan kerja pendamping, dan evidence visual belum tegas,
  - banyak dokumen `docs/domain/**` masih mereferensikan path lama sehingga rename massal berisiko memutus kontrak dokumentasi.

## Kontrak Concern (Lock)

- Domain: governance dan standardisasi dokumen referensi domain.
- Role/scope target: lintas tim internal; tidak terkait role runtime aplikasi.
- Boundary data: `docs/referensi/**`, dokumen `docs/domain/**` dan `docs/process/**` yang mereferensikan path referensi, serta README/index concern terkait.
- Acceptance criteria:
  - taxonomy target untuk dokumen referensi terkunci dan terdokumentasi,
  - naming convention canonical untuk file referensi dan screenshot evidence terdokumentasi,
  - tersedia manifest migrasi `old-path -> new-path` sebelum rename fisik dimulai,
  - rollout plan bertahap + fallback plan jelas agar referensi lama tidak putus mendadak.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [ ] Struktur target `docs/referensi` terkunci minimal pada zonasi:
  - top-level hanya dokumen kontrol,
  - `canonical/` untuk sumber referensi utama,
  - `supporting/` untuk workbook/docx/bahan pendamping,
  - `evidence/screenshots/` untuk bukti visual,
  - `_local/` tetap untuk artefak non-tracked.
- [ ] Konvensi nama file baru terkunci:
  - huruf kecil,
  - tanpa spasi,
  - berbasis `doc-key` yang manusiawi,
  - evidence screenshot memakai pola turunan dari `doc-key`.
- [ ] Inventaris aktif dan migration map path referensi tersedia untuk seluruh file yang saat ini dipakai `docs/domain/**`.
- [ ] Tersusun urutan rollout aman: audit -> taxonomy -> manifest -> pilot rename -> wave rollout.

## Langkah Eksekusi

- [x] Audit scoped inventaris `docs/referensi` dan pola referensi silang di `docs/domain/**`.
- [ ] Tetapkan taxonomy folder final dan guard top-level `docs/referensi/`.
- [ ] Tetapkan naming convention canonical untuk:
  - dokumen referensi utama,
  - dokumen pendamping,
  - screenshot evidence,
  - artefak lokal non-tracked.
- [ ] Susun manifest migrasi `old-path -> new-path` beserta kategori setiap dokumen.
- [ ] Jalankan pilot rename concern kecil pada subset referensi yang dampaknya rendah.
- [ ] Sinkronkan seluruh referensi path pada `docs/domain/**`, `docs/process/**`, dan README terkait setelah pilot tervalidasi.
- [ ] Hardening akhir:
  - `docs/referensi/README.md`,
  - indeks/katalog referensi,
  - guard governance path bila diperlukan.

## Validasi

- [x] L1: audit inventory folder + audit scoped referensi path markdown.
- [ ] L2: regression audit referensi markdown setelah pilot rename (`audit_markdown_paths.ps1` + scoped grep).
- [ ] L3: `php artisan test` jika concern meluas ke path runtime/test fixture yang dibaca aplikasi.

## Risiko

- Risiko 1: rename fisik memutus referensi pada dokumen domain yang saat ini mengarah ke path lama.
- Risiko 2: taxonomy terlalu cepat dipaksakan tanpa manifest sehingga folder baru hanya memindahkan kekacauan ke lokasi lain.
- Risiko 3: pencampuran canonical vs supporting vs evidence tetap terjadi jika README dan guard operasional tidak ikut diubah.

## Keputusan

- [x] K1: concern ini dimulai sebagai planning + inventory concern, bukan rename massal langsung.
- [x] K2: migration map `old-path -> new-path` wajib selesai sebelum ada perpindahan path referensi canonical.
- [x] K3: `_local/` dipertahankan sebagai zona non-tracked dan tidak dicampur dengan artefak canonical lintas tim.

## Keputusan Arsitektur (Jika Ada)

- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jika pilot rename memutus referensi:
  - rollback batch rename concern tersebut,
  - pulihkan path lama,
  - perbaiki manifest migrasi lebih dulu,
  - ulangi pada batch yang lebih kecil dengan audit referensi scoped.

## Output Final

- [ ] Ringkasan taxonomy target + naming convention yang dikunci.
- [ ] Daftar file referensi yang masuk batch pilot dan mapping path lamanya.
- [ ] Hasil audit referensi markdown + residual risk rename bertahap.
