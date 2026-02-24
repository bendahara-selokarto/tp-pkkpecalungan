# TODO Implementasi Autentik Buku Kegiatan (2026-02-24)

## Konteks
- Sumber autentik: `docs/referensi/excel/Buku Wajib Pokja I.xlsx`, sheet `Buku Kegiatan`.
- Hasil baca text-layer XML sudah tersedia (struktur merge awal sudah teridentifikasi).
- Bukti visual header tabel sudah tersedia dari screenshot user sesi 2026-02-24.
- Status saat ini: `implemented` (copy UI + label report sinkron dengan kontrak autentik).

## Target Hasil
- Kontrak header final sheet `Buku Kegiatan` tervalidasi sampai merge cell.
- Mapping kolom autentik ke field aplikasi terdokumentasi.
- Rencana patch implementasi minimal lintas layer (`request -> use case -> repository -> inertia`) siap eksekusi.

## Langkah Eksekusi
- [x] Ambil bukti visual header tabel `Buku Kegiatan` (header utuh + garis sel + teks terbaca).
- [x] Finalisasi peta header:
  - `NO`
  - `NAMA`
  - `JABATAN`
  - `KEGIATAN` (`TANGGAL`, `TEMPAT`, `URAIAN`)
  - `TANDA TANGAN`
- [x] Tetapkan matrix mapping `kolom autentik -> field storage/report`.
- [x] Audit dampak implementasi:
  - route + middleware scope/role
  - request normalisasi/validasi
  - use case/repository query boundary
  - inertia payload/dashboard trigger (jika relevan)
- [x] Siapkan daftar test minimum (feature + policy/scope + anti data leak bila query scoped kompleks).
- [x] Sinkronkan dokumen concern jika ada perubahan kontrak canonical.

## Matrix Mapping Final
- `NO` -> nomor urut report (`index + 1`).
- `NAMA` -> `nama_petugas` (fallback kompatibilitas: `title`).
- `JABATAN` -> `jabatan_petugas`.
- `KEGIATAN.TANGGAL` -> `activity_date`.
- `KEGIATAN.TEMPAT` -> `tempat_kegiatan`.
- `KEGIATAN.URAIAN` -> `uraian` (fallback kompatibilitas: `description`).
- `TANDA TANGAN` -> `tanda_tangan` (fallback kompatibilitas: `nama_petugas`).

## Dampak Implementasi
- Route/middleware scope-role tetap memakai jalur existing:
  - `/{scope}/activities/*` dengan policy backend sebagai authority.
- Request + use case + repository:
  - Tidak ada perubahan kontrak input/storage; hanya normalisasi copy user-facing.
- Inertia payload:
  - Tidak ada perubahan shape payload; hanya label judul/index/create/edit/show.
- Dashboard trigger:
  - Tidak memerlukan perubahan coverage baru karena modul `activities` sudah masuk coverage dan matrix dashboard.

## Validasi
- [x] Bukti visual header valid tersimpan.
- [x] Tidak ada ambiguitas merge cell pada header.
- [x] Checklist dampak layer arsitektur lengkap.
- [x] Rencana test executable sebelum patch implementasi.

## Test Matrix Concern
- [x] Feature scoped CRUD:
  - `tests/Feature/DesaActivityTest.php`
  - `tests/Feature/KecamatanActivityTest.php`
  - `tests/Feature/KecamatanDesaActivityTest.php`
- [x] Feature report scoped:
  - `tests/Feature/ActivityPrintTest.php`
- [x] Kontrak baseline PDF:
  - `tests/Feature/PdfBaselineFixtureComplianceTest.php` (fixture `4.13-kegiatan.json`)

## Risiko
- Salah interpretasi header `KEGIATAN` multi-subkolom jika hanya mengandalkan text-layer.
- Drift istilah kolom antara referensi autentik dan label UI existing.

## Keputusan
- [x] Sheet `Buku Kegiatan` dipilih sebagai prioritas fase 1A.
- [x] Implementasi ditahan sampai verifikasi visual header selesai.
- [x] Setelah verifikasi visual dikunci, concern `Buku Kegiatan` diselesaikan tanpa perubahan kontrak storage.
