# TODO RFD26A1 Standarisasi Struktur Dokumen Referensi Domain

Tanggal: 2026-03-10  
Status: `done` (`state:taxonomy-naming-manifest-pilot-rename-closed`)
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

- [x] Struktur target `docs/referensi` terkunci minimal pada zonasi:
  - top-level hanya dokumen kontrol,
  - `canonical/` untuk sumber referensi utama,
  - `supporting/` untuk workbook/docx/bahan pendamping,
  - `evidence/screenshots/` untuk bukti visual,
  - `_local/` tetap untuk artefak non-tracked.
- [x] Konvensi nama file baru terkunci:
  - huruf kecil,
  - tanpa spasi,
  - berbasis `doc-key` yang manusiawi,
  - evidence screenshot memakai pola turunan dari `doc-key`.
- [x] Inventaris aktif dan migration map path referensi tersedia untuk seluruh file yang saat ini dipakai `docs/domain/**`.
- [x] Tersusun urutan rollout aman: audit -> taxonomy -> manifest -> pilot rename -> wave rollout.

## Langkah Eksekusi

- [x] Audit scoped inventaris `docs/referensi` dan pola referensi silang di `docs/domain/**`.
- [x] Tetapkan taxonomy folder final dan guard top-level `docs/referensi/`.
- [x] Tetapkan naming convention canonical untuk:
  - dokumen referensi utama,
  - dokumen pendamping,
  - screenshot evidence,
  - artefak lokal non-tracked.
- [x] Susun manifest migrasi `old-path -> new-path` beserta kategori setiap dokumen.
- [x] Jalankan pilot rename concern kecil pada subset referensi yang dampaknya rendah.
- [x] Sinkronkan seluruh referensi path pada `docs/domain/**`, `docs/process/**`, dan README terkait setelah pilot tervalidasi (scope pilot).
- [x] Hardening akhir:
  - `docs/referensi/README.md`,
  - indeks/katalog referensi,
  - guard governance path bila diperlukan.

## Validasi

- [x] L1: audit inventory folder + audit scoped referensi path markdown.
- [x] L2: regression audit referensi markdown setelah pilot rename (`audit_markdown_paths.ps1` + scoped grep).
- [x] L3: `php artisan test` tidak diperlukan (doc-only, tidak menyentuh runtime fixture).

## Risiko

- Risiko 1: rename fisik memutus referensi pada dokumen domain yang saat ini mengarah ke path lama.
- Risiko 2: taxonomy terlalu cepat dipaksakan tanpa manifest sehingga folder baru hanya memindahkan kekacauan ke lokasi lain.
- Risiko 3: pencampuran canonical vs supporting vs evidence tetap terjadi jika README dan guard operasional tidak ikut diubah.

## Keputusan

- [x] K1: concern ini dimulai sebagai planning + inventory concern, bukan rename massal langsung.
- [x] K2: migration map `old-path -> new-path` wajib selesai sebelum ada perpindahan path referensi canonical.
- [x] K3: `_local/` dipertahankan sebagai zona non-tracked dan tidak dicampur dengan artefak canonical lintas tim.

## Keputusan Arsitektur (Jika Ada)

- [x] Tidak perlu ADR baru (concern doc-only, tanpa perubahan boundary runtime).
- [x] Status ADR tidak berubah.

## Fallback Plan

- Jika pilot rename memutus referensi:
  - rollback batch rename concern tersebut,
  - pulihkan path lama,
  - perbaiki manifest migrasi lebih dulu,
  - ulangi pada batch yang lebih kecil dengan audit referensi scoped.

## Output Final

- [x] Ringkasan taxonomy target + naming convention yang dikunci.
- [x] Daftar file referensi yang masuk batch pilot dan mapping path lamanya.
- [x] Hasil audit referensi markdown + residual risk rename bertahap.

## Progress Log

- 2026-03-11: taxonomy + naming convention dikunci, `MIGRATION_MANIFEST.md` dibuat, pilot rename `Cara Pengisian Lampiran 4.22.pdf` -> `docs/referensi/supporting/lampiran-4-22-cara-pengisian.pdf`, referensi path pilot disinkronkan, `docs/referensi/README.md` dihardening, `audit_markdown_paths.ps1` `PASS`.
