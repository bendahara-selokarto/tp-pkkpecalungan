# TODO ASM26B1 Management Arsip Super Admin 2026-02-27

Tanggal: 2026-02-27  
Status: `done`

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
- Concern turunan aktif: `docs/process/archive/2026_02/TODO_ARS26B2_HARDENING_AKSES_ARSIP_GLOBAL_PRIBADI_2026_02_28.md`.
- Kontrak akses terkunci:
  - arsip unggahan `super-admin` bernilai `global` dan visible semua role,
  - arsip unggahan non `super-admin` bernilai private dan mutasi hanya owner,
  - jalur monitoring desa untuk arsip kecamatan mengikuti pola dual-scope concern `activities`.

## Target Hasil
- [x] Panel management arsip tersedia pada area `super-admin`.
- [x] Super-admin dapat tambah/ubah/hapus dokumen arsip global, termasuk lintas akun `super-admin`.
- [x] Halaman `Arsip` user mengikuti kontrak `ARS26B2` (global + private milik sendiri, monitoring read-only via jalur sekretaris kecamatan).
- [x] Download dokumen memiliki guard keamanan (path traversal, file existence, dan otorisasi).
- [x] Menu super-admin menampilkan entry `Management Arsip` sejajar concern administratif lain.

## Rencana Eksekusi
- [x] Tetapkan model dan skema metadata arsip (`arsip_documents`) beserta field status visibilitas (`is_global`).
- [x] Tambah repository interface + repository arsip management (query scoped by visibility).
- [x] Tambah use case/action:
  - [x] list arsip user (`/arsip`) dan monitoring (`desa-arsip`),
  - [x] list arsip management (super-admin),
  - [x] create/update/delete.
- [x] Tambah controller dan route:
  - [x] route publik `arsip` (read/download + owner mutation),
  - [x] route `super-admin/arsip/*` untuk management.
- [x] Tambah request validation upload dokumen + normalisasi payload metadata.
- [x] Tambah policy/authorization untuk operasi arsip, termasuk hardening mutasi arsip global lintas akun `super-admin`.
- [x] Update UI:
  - [x] halaman management arsip super-admin,
  - [x] update menu super-admin,
  - [x] penyesuaian halaman arsip publik agar konsumsi metadata arsip terkelola.
- [x] Keputusan: migrasi/seed baseline dokumen referensi awal dari `docs/referensi` tidak dieksekusi di concern ini dan dipertahankan sebagai opsi rollout terpisah.

## Validasi (Matrix Minimum)
- [x] Feature test jalur sukses management arsip oleh `super-admin`.
- [x] Feature test tolak akses management arsip oleh non `super-admin`.
- [x] Feature test tolak akses saat metadata role-area stale (untuk user non super-admin yang mencoba path management).
- [x] Unit test policy arsip (`view`, `create`, `update`, `delete`).
- [x] Unit/feature test repository/use case anti data leak (dokumen private tidak muncul untuk user non-owner pada jalur `/arsip`).
- [x] Jalankan `php artisan test --filter ArsipManagementTest`.
- [x] Jalankan `php artisan test --filter ArsipTest`.
- [x] Jalankan `php artisan test --filter ArsipDocumentPolicyTest`.
- [x] Jalankan `php artisan test`.

## Audit Dashboard Trigger
- [x] Audit dampak terhadap KPI/chart/progress dashboard.
- [x] Keputusan: management arsip adalah concern utilitas dokumen, bukan input domain KPI dashboard.
- [x] Justifikasi final `N/A` dashboard dikunci pada concern ini.

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

## Sinkronisasi 2026-03-02
- Gap otorisasi ditutup: `super-admin` kini dapat `update/delete` arsip `global` lintas akun `super-admin` pada jalur `/super-admin/arsip`.
- Jalur `/arsip` user tetap owner-only untuk mutasi private, sehingga boundary `management` vs `user-route` tidak drift.
- Sinkronisasi concern dilakukan ke registry SOT `TTM25R1` dengan status concern `C-ARSIP-MGMT` menjadi `done`.
