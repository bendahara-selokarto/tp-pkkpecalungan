# TODO ASM26B1 Management Arsip Super Admin 2026-02-27

Tanggal: 2026-02-27  
Status: `done` (`implementation-done`, `validation-passed`, `doc-hardening`)

## Konteks
- Baseline saat ini: menu `Arsip` sudah tersedia untuk daftar/unduh dokumen statis.
- Arah baru: management arsip global dipusatkan ke `super-admin`, sementara arsip non-global tetap dikelola pemilik dokumen.
- Concern ini beririsan dengan akses role, struktur route `super-admin`, dan representasi menu super-admin.

## Kontrak Concern (Lock)
- Domain: `Arsip Management`.
- Role authority:
  - `super-admin`: `create`, `update`, `delete` dokumen arsip global.
  - non `super-admin`: dapat `create` arsip pribadi, `update/delete` hanya untuk dokumen milik sendiri.
  - `kecamatan-sekretaris`: dapat `view/download` arsip user desa dalam parent kecamatan sendiri (monitoring read-only).
- Boundary data:
  - Metadata arsip dikelola melalui boundary repository.
  - File arsip disimpan pada storage aplikasi (bukan membaca bebas dari filesystem arbitrary path).
- Arsitektur wajib: `Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`.
- Enforcement akses wajib backend: middleware `role:super-admin` + policy/authorization gate untuk operasi management.

## Update Kontrak 2026-02-28
- Concern turunan aktif: `docs/process/TODO_ARS26B2_HARDENING_AKSES_ARSIP_GLOBAL_PRIBADI_2026_02_28.md`.
- Kontrak akses terkunci:
  - arsip unggahan `super-admin` bernilai `global` dan visible semua role,
  - arsip unggahan non `super-admin` bernilai private dan mutasi hanya owner,
  - jalur monitoring desa untuk arsip kecamatan mengikuti pola dual-scope concern `activities`.

## Target Hasil
- [x] Panel management arsip tersedia pada area `super-admin`.
- [x] Super-admin dapat tambah/ubah/hapus dokumen arsip global.
- [x] Halaman `Arsip` user umum menampilkan arsip global + arsip milik user login.
- [x] Download dokumen memiliki guard keamanan (path traversal, file existence, dan otorisasi).
- [x] Menu super-admin menampilkan entry `Management Arsip` sejajar concern administratif lain.

## Rencana Eksekusi
- [x] Tetapkan model dan skema metadata arsip (`arsip_documents`) dengan visibilitas `is_global`.
- [x] Tambah repository interface + repository arsip management (query scoped by visibility).
- [x] Tambah use case/action:
  - [x] list arsip user (`global + own`),
  - [x] list arsip management (super-admin),
  - [x] create/update/delete dokumen arsip.
- [x] Tambah controller dan route:
  - [x] route publik `arsip` (read/download),
  - [x] route `super-admin/arsip/*` untuk management.
- [x] Tambah request validation upload dokumen + normalisasi payload metadata.
- [x] Tambah policy/authorization untuk operasi arsip.
- [x] Update UI:
  - [x] halaman management arsip super-admin,
  - [x] update menu super-admin,
  - [x] penyesuaian halaman arsip publik agar konsumsi metadata arsip terkelola.
- [x] Migrasi/seed baseline dokumen referensi awal ditetapkan `N/A` pada concern ini (opsional, tidak dieksekusi).

## Validasi (Matrix Minimum)
- [x] Feature test jalur sukses management arsip oleh `super-admin`.
- [x] Feature test tolak akses management arsip oleh non `super-admin`.
- [x] Feature test tolak akses saat metadata role-area stale (untuk user non super-admin yang mencoba path management).
- [x] Unit test policy arsip (`view`, `create`, `update`, `delete`).
- [x] Unit/feature test repository/use case anti data leak (dokumen non publik tidak muncul di list publik).
- [x] Jalankan `php artisan test`.

Bukti validasi 2026-03-02:
- `php artisan test tests/Feature/ArsipTest.php tests/Feature/KecamatanDesaArsipTest.php tests/Feature/SuperAdmin/ArsipManagementTest.php tests/Unit/Policies/ArsipDocumentPolicyTest.php --stop-on-failure` -> `PASS` (`18` tests, `83` assertions).
- `php artisan test` -> `PASS` (`1002` tests, `6317` assertions).

## Audit Dashboard Trigger
- [x] Audit dampak terhadap KPI/chart/progress dashboard.
- [x] Keputusan akhir: management arsip adalah concern utilitas dokumen statis, bukan input domain KPI dashboard.
- [x] Justifikasi `N/A` dashboard dikunci: arsip tidak menambah data KPI proses domain, hanya menyediakan repositori dokumen referensi.

## Risiko
- Transisi dari arsip statis ke arsip terkelola berisiko mismatch daftar dokumen bila migrasi metadata tidak lengkap.
- Upload file tanpa guard MIME/size dapat memicu risiko keamanan dan storage abuse.
- Jika policy tidak konsisten, non super-admin berpotensi mengakses dokumen non publik.

## Fallback Plan
- Pertahankan route publik `arsip` tetap read-only selama fase transisi.
- Jika panel management belum stabil, rollback terbatas ke mode daftar statis sambil mempertahankan guard download.
- Re-sync metadata arsip dari sumber referensi canonical dengan command sekali jalan (idempotent).

## Keputusan
- Dokumen ini ditetapkan sebagai acuan rencana concern `management arsip`.
- TODO baseline sebelumnya (`TODO_ARS26A1_MENU_ARSIP_DOKUMEN_STATIS_2026_02_27.md`) diperlakukan sebagai baseline historis implementasi fase awal menu arsip statis.
- Concern ini ditutup `done`; hardening akses global/pribadi dimatrikkan pada child concern `TODO_ARS26B2_HARDENING_AKSES_ARSIP_GLOBAL_PRIBADI_2026_02_28.md` (`done`).
