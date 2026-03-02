# TODO Rencana Perbaikan Menyeluruh (Scan 2026-02-22)
Tanggal: 2026-02-22  
Status: `done`

## Konteks
- Hasil scan mendalam menemukan 1 blocker kritis pada kontrak repository `CatatanKeluarga` yang memutus bootstrap aplikasi.
- Ditemukan concern otorisasi administratif: `super-admin` masih muncul di jalur role assignable user management.
- Ditemukan debt konsistensi tanggal: trait normalisasi tanggal tersedia tetapi belum terpakai pada request.
- Scope kerja saat ini: menyusun rencana perbaikan lintas concern tanpa mengubah file existing yang belum disepakati.

## Target Hasil
- Aplikasi kembali bisa bootstrap normal (`route:list` dan test terkait tidak fatal).
- Guardrail administratif selaras kontrak: tidak ada mutasi role `super-admin` dari jalur manajemen user biasa.
- Konsistensi kontrak tanggal lebih jelas (canonical vs exception) dan terdokumentasi.
- Seluruh perubahan tervalidasi dengan test dan checklist operasional.

## Kanonikal Status Eksekusi (Wajib)
- [x] Aturan status checklist ditegaskan:
  - `[x]` hanya boleh dipakai jika sudah ada bukti validasi (command/test/log) pada sesi yang sama.
  - `[ ]` wajib dipakai untuk semua item yang belum selesai, belum tervalidasi, atau masih butuh keputusan.
- [x] Larangan asumsi ditegaskan:
  - jika ada data/keputusan yang belum pasti, jangan disimpulkan sendiri.
  - catat eksplisit sebagai `PENDING` dengan blocker atau kebutuhan konfirmasi.
- [x] Definisi selesai ditegaskan:
  - implementasi selesai + validasi lulus + tidak ada blocker terbuka pada concern terkait.
- [x] Definisi belum selesai ditegaskan:
  - salah satu dari implementasi/validasi/keputusan masih belum terpenuhi => status tetap `[ ]`.

## Keputusan Kerja Saat Ini
- [x] Fokus prioritas pertama pada blocker bootstrap sebelum concern lain.
- [x] Perubahan existing di luar scope user tidak disentuh selama fase scan.
- [x] Konfirmasi akhir kontrak role `super-admin` dikunci:
  - `super-admin` tetap ada di compatibility matrix akses.
  - `super-admin` dikeluarkan dari assignable role manajemen user.
- [x] Konfirmasi akhir strategi tanggal dikunci:
  - canonical request tetap explicit strict `date_format:Y-m-d`.
  - trait `ParsesUiDate` dinyatakan deprecated (tidak dipakai request aktif).

## Tahapan Per Concern

### Concern A - Blocker Bootstrap CatatanKeluarga
- [x] Identifikasi mismatch interface vs implementasi:
  - `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepositoryInterface.php`
  - `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php`
- [x] Reproduksi error fatal dengan:
  - `php artisan route:list --except-vendor`
  - `php artisan test --filter RekapCatatanDataKegiatanWargaReportPrintTest`
- [x] Implement method yang hilang pada `CatatanKeluargaRepository` sesuai kontrak interface.
- [x] Pastikan use case/controller yang terkait route 4.17A/rekap ibu hamil tetap konsisten.
- [x] Tambah/adjust test untuk method baru (unit/feature yang relevan) melalui regresi report print yang sudah ada.
- [x] Validasi ulang:
  - `php artisan route:list --except-vendor`
  - `php artisan test --filter RekapCatatanDataKegiatanWargaReportPrintTest`
- [x] Blocker concern A ditutup: bootstrap tidak lagi fatal setelah method kontrak diimplementasikan.

### Concern B - Guardrail Super Admin Path
- [x] Audit matrix role-scope dan flow admin path selesai.
- [x] Konfirmasi bahwa `super-admin` masih ada pada jalur assignable melalui:
  - `app/Support/RoleScopeMatrix.php`
  - `app/UseCases/User/GetUserManagementFormOptionsUseCase.php`
  - `resources/js/Pages/SuperAdmin/Users/Create.vue`
- [x] Putuskan aturan final:
  - opsi A: `super-admin` tetap di matrix akses, tapi dikecualikan dari assignable role.
  - opsi B: `super-admin` dikeluarkan dari matrix scoped role dan ditangani terpisah.
- [x] Patch backend validasi agar request create/update user menolak assign role `super-admin` pada jalur administratif biasa.
- [x] Patch UI agar role `super-admin` tidak muncul di dropdown create/edit user.
- [x] Sesuaikan test yang saat ini masih menganggap `super-admin` assignable.
- [x] Validasi regresi:
  - `php artisan test --filter "UserProtectionTest|GetUserManagementFormOptionsUseCaseTest|SuperAdminAuthorizationTest"`
- [x] Blocker concern B ditutup: kontrak final role `super-admin` sudah dikunci dan tervalidasi.

### Concern C - Konsistensi Input/Normalisasi Tanggal
- [x] Audit menemukan trait `ParsesUiDate` sudah ada namun belum dipakai di request.
- [x] Audit frontend menemukan pola campuran `type="date"` dan field text tanggal domain tertentu.
- [x] Tegaskan batasan canonical vs exception berdasarkan dokumen `TODO_STANDARDISASI_INPUT_TANGGAL.md`.
- [x] Terapkan salah satu strategi secara konsisten:
  - gunakan trait `ParsesUiDate` pada request yang menerima input UI campuran, atau
  - hapus/deprecate trait dan pertahankan validasi strict per-request.
- [x] Tambah test unit/feature untuk mencegah drift format tanggal.
- [x] Sinkronkan dokumentasi jika ada perubahan keputusan teknis.
- [x] Blocker concern C ditutup: strategi normalisasi tanggal final dan tervalidasi.

### Concern D - Validasi Integrasi dan Quality Gate
- [x] Targeted test awal sudah dijalankan dan lulus:
  - `DashboardDocumentCoverageTest`
  - `DesaProgramPrioritasTest`
  - `UserProtectionTest`
- [x] Setelah semua patch concern A-C selesai, jalankan `php artisan test` penuh.
- [x] Verifikasi tambahan saat concern A sudah pulih:
  - `php artisan test` penuh lulus (`476 passed`).
- [x] Verifikasi final setelah patch concern B/C/F:
  - `php artisan test` penuh lulus (`667 passed`).
- [x] Verifikasi tidak ada drift authorization (`role` vs `scope` vs `areas.level`).
- [x] Verifikasi route utama scoped `desa/kecamatan/super-admin` tetap berjalan.

### Concern E - Dokumentasi dan Jejak Keputusan
- [x] TODO lintas concern dibuat di `docs/process/` (dokumen ini).
- [x] Catat hasil implementasi per concern ke log operasional:
  - `docs/process/OPERATIONAL_VALIDATION_LOG.md`
- [x] Jika ada perubahan pola eksekusi, update:
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
- [x] Jika ada perubahan kontrak teknis canonical, sinkronkan ke `AGENTS.md`.
  - Status: tidak perlu perubahan isi `AGENTS.md` karena guardrail `super-admin` sudah tercantum dan tetap konsisten.

### Concern F - Coverage Unit Test dan Seeder
- [x] Requirement baru ditetapkan: semua unit wajib punya test, semua isian/domain wajib punya seeder.
- [x] Rencana detail dipisah ke dokumen khusus:
  - `docs/process/TODO_COVERAGE_UNIT_TEST_DAN_SEEDER_2026_02_22.md`
- [x] Audit mendalam coverage sudah dicatat di dokumen khusus:
  - total unit terdeteksi: 183
  - unit dengan direct test: 8
  - tabel domain target seeding baseline: 28
  - tabel domain yang terseed lewat chain default `DatabaseSeeder`: 28 (setelah integrasi `DashboardNaturalBatangSeeder` + `WilayahMissingDomainSeeder`)
- [x] Eksekusi concern F mengikuti checklist detail pada dokumen khusus sampai seluruh gate coverage berstatus `[x]`.

## Validasi Minimum Sebelum Penutupan
- [x] Tidak ada fatal bootstrap saat `php artisan route:list --except-vendor`.
- [x] Tidak ada mismatch interface/implementasi pada repository utama.
- [x] Jalur admin tidak bisa membuat/mengubah user menjadi `super-admin` tanpa path khusus.
- [x] Test regresi concern A-C lulus.
- [x] Coverage concern F (unit test + seeder) lulus sesuai dokumen detail.
- [x] `php artisan test` penuh lulus.

## Rule Penutupan Tugas
- [x] Tugas dinyatakan selesai hanya jika seluruh item pada "Validasi Minimum Sebelum Penutupan" berstatus `[x]`.
- [x] Jika ada satu saja item validasi belum terpenuhi, status laporan akhir wajib `PENDING` dan mencantumkan blocker.

## Risiko
- Perubahan concern A berisiko mengubah output laporan catatan keluarga bila mapping tidak presisi.
- Perubahan concern B berisiko mematahkan test lama dan asumsi seeding role.
- Perubahan concern C berisiko memicu drift format tanggal antar modul bila tidak ditutup dengan test.

## Fallback Plan
- [x] Terapkan patch per concern secara terpisah dan commit terisolasi.
- [x] Jika regresi muncul, rollback per commit concern (bukan rollback massal).
- [x] Prioritaskan menjaga akses backend/policy tetap aman walau fitur non-kritis harus ditunda.

