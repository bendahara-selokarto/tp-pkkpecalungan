# TODO Implementasi Role Ownership Non RW/RO 2026-02-25

Tanggal: 2026-02-25  
Status: `done`

## Konteks

- Hasil koreksi domain menyatakan beberapa modul tidak memiliki owner `RW/RO`.
- Implementasi backend saat ini masih memberi mode `RW/RO` ke role pokja/sekretaris pada modul terkait.

## Target Hasil

- Modul non-ownership tidak lagi memakai matrix `RW/RO` yang menimbulkan ambiguity akses.
- Boundary akses backend tetap eksplisit dan tidak melemah.

## Langkah Eksekusi

- [x] Tetapkan keputusan final owner akses untuk modul:
  - [x] `catatan-keluarga` -> `retain` (akses existing dipertahankan sementara).
  - [x] `program-prioritas` -> `retain` (akses existing dipertahankan sementara).
  - [x] `pilot-project-keluarga-sehat` -> `retain` (akses existing dipertahankan sementara).
  - [x] `pilot-project-naskah-pelaporan` -> `retain` (akses existing dipertahankan sementara).
  - [x] `desa-activities` -> `retain` sebagai modul monitoring kecamatan.
- [x] Sesuaikan `RoleMenuVisibilityService` sesuai keputusan owner final per modul. (No-op: keputusan final batch ini tidak mengubah runtime modul non-RW/RO).
- [x] Audit `EnsureModuleVisibility` untuk memastikan `read-only`/`read-write` tetap konsisten pada modul yang masih aktif.
- [x] Tambah test anti data leak + anti write-intent untuk role yang dicabut. (No-op: tidak ada role dicabut pada batch ini; guard diuji lewat `DashboardDocumentCoverageTest` + policy test terkait).

## Validasi

- [x] `php artisan test --filter=module.visibility|DashboardDocumentCoverageTest` (dijalankan via `DashboardDocumentCoverageTest`).
- [x] `php artisan test --filter=CatatanKeluargaPolicyTest|PilotProjectKeluargaSehatPolicyTest` (dijalankan terpisah per test).
- [x] `php artisan test` penuh

Catatan validasi:
- `php artisan test` penuh lulus pada eksekusi 2026-02-25 (suite hijau end-to-end).

## Risiko

- Risiko blok akses user valid jika owner final belum disepakati lintas tim domain.
- Risiko drift dashboard block jika modul non-ownership dipindah group tanpa sinkronisasi representasi dashboard.

## Keputusan

- [x] Concern ini dibuat untuk menutup delta audit modul non-ownership 2026-02-25.
- [x] Implementasi runtime dikunci `retain sementara` untuk modul non-RW/RO pada sesi ini agar tidak terjadi drift akses mendadak.
