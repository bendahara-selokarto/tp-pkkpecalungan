# TODO SFC26A1 Hardening Struktur Folder Maintainable

Tanggal: 2026-03-07  
Status: `done` (`state:structure-hardened`)
Related ADR: `-`

## Aturan Pakai
- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks
- Hasil audit manual menemukan tiga sumber kebingungan review:
  - boundary penempatan kode concern baru belum tertulis eksplisit (global `app/*` vs domain `app/Domains/Wilayah/*`),
  - artefak lokal/generated masih muncul pada area yang mudah terlihat reviewer (`root`, sample output),
  - belum ada strategi arsip TODO `done` yang baku untuk menjaga `docs/process` tetap navigable.
- Concern ini fokus pada hardening struktur dan dokumen operasional tanpa mengubah perilaku bisnis aplikasi.

## Kontrak Concern (Lock)
- Domain: maintainability struktur repository + dokumentasi operasional.
- Role/scope target: lintas role (tidak mengubah kontrak akses domain).
- Boundary data:
  - root repo (`.gitignore` + artefak root non-source),
  - dokumen proses/arsitektur (`AGENTS.md`, `README.md`, `docs/README.md`, `docs/process/*`),
  - artefak sample non-runtime (`public/chart-pdf-examples/output/*`).
- Acceptance criteria:
  - rule placement kode concern baru tertulis eksplisit dan konsisten lintas dokumen inti,
  - artefak lokal/generated tidak lagi mengotori root atau sample output tracked,
  - strategi arsip TODO `done` terdokumentasi dengan langkah operasional yang jelas.
- Dampak keputusan arsitektur: `tidak` (tidak mengubah boundary runtime aplikasi).

## Target Hasil
- [x] Rule placement kode concern baru terkunci (single-path yang tegas).
- [x] Artefak root/generated dibersihkan dari jalur review utama.
- [x] Strategi arsip TODO `done` aktif di dokumen process.

## Langkah Eksekusi
- [x] L0. Audit scoped dependency + side effect.
- [x] L1. Hardening dokumen rule placement kode concern baru.
- [x] L2. Cleanup artefak root/generated (diff kecil, tanpa ubah behavior runtime).
- [x] L3. Hardening strategi arsip TODO `done` pada docs/process.
- [x] L4. Sinkronisasi dokumen concern terkait (doc-hardening pass).

## Validasi
- [x] V1. Artefak root/sample generated tidak lagi ada di jalur fisik review utama, dan deletion marker tercatat pada git diff.
- [x] V2. Dokumen inti (`AGENTS.md`, `README.md`, `docs/README.md`) konsisten dengan policy baru.
- [x] V3. `git status --short` hanya memuat perubahan concern ini.

## Risiko
- Risiko 1: policy placement terlalu ketat dan menghambat concern cross-domain.
- Risiko 2: pemindahan artefak lokal bisa membuat path referensi lama putus bila tidak didokumentasikan.

## Keputusan
- [x] K1: concern baru domain wilayah harus default ke `app/Domains/Wilayah/<Concern>/<Layer>`.
- [x] K2: artefak lokal/generated wajib dipisah dari source tracked.

## Keputusan Arsitektur (Jika Ada)
- [x] ADR baru tidak diperlukan (tidak mengubah boundary arsitektur runtime).
- [x] Status concern disinkronkan via TODO + operational log.

## Fallback Plan
- Jika policy baru menimbulkan kebingungan implementasi, rollback hanya perubahan dokumen policy dan kembali ke baseline concern sebelum `SFC26A1`.
- Jika cleanup artefak mengganggu alur lokal, file lokal dapat dipulihkan ke jalur `_local` tanpa masuk version control.

## Output Final
- [x] Ringkasan apa yang diubah dan kenapa.
- [x] Daftar file terdampak.
- [x] Hasil validasi + residual risk.

## Progress Update 2026-03-07 (Closure)

- Hardening placement policy:
  - dibuat dokumen `docs/process/CODE_PLACEMENT_POLICY.md` sebagai kontrak placement concern baru,
  - disinkronkan ke `AGENTS.md`, `README.md`, `docs/README.md`, dan referensi `AI_SINGLE_PATH_ARCHITECTURE.md`.
- Cleanup artefak review:
  - artefak root `buku_*.pdf` dipindah ke `docs/referensi/_local/` (non-tracked) dan dihapus dari root tracked,
  - sample output generated `public/chart-pdf-examples/output/quickchart-example.pdf` dihapus dari tracked file,
  - `.gitignore` dihardening untuk memisahkan referensi lokal (`docs/referensi`) dan output generated sample.
- Strategi arsip TODO:
  - dibuat `docs/process/PROCESS_TODO_ARCHIVE_STRATEGY.md`,
  - dibuat `docs/process/archive/README.md` sebagai lokasi arsip concern done.
- Sinkronisasi registry:
  - concern `SFC26A1` ditambahkan pada registry SOT `TODO_TTM25R1_*`.

- Validasi:
  - `root_pdf_exists=no`, `quickchart_pdf_exists=no`,
  - `git diff --name-status` menandai penghapusan artefak target,
  - scoped grep menemukan referensi policy baru konsisten di dokumen inti.

- Residual risk:
  - concern existing yang masih berada di folder global `app/*` tetap dipertahankan sebagai kompatibilitas historis dan tidak direlokasi pada wave ini.
