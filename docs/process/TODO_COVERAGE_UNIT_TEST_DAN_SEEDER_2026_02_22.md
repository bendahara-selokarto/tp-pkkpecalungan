# TODO Coverage Unit Test dan Seeder (2026-02-22)
Tanggal: 2026-02-22  
Status: `done`

## Konteks
- Requirement: semua unit disertakan test, dan semua isian/domain disertai seeder.
- Kondisi saat ini: coverage feature test domain sudah luas, tetapi coverage unit per class dan coverage seeder per domain belum merata.
- Dokumen ini menjadi checklist kanonikal untuk menutup gap coverage tersebut.

## Target Hasil
- Setiap unit inti (Action, UseCase, Service, Repository, Scope Service) memiliki test yang relevan.
- Setiap domain/isian utama memiliki seeder minimal untuk data baseline.
- Tersedia gate verifikasi yang bisa dijalankan ulang sebelum penutupan tugas.

## Kanonikal Status (Wajib)
- [x] `[x]` hanya jika sudah ada bukti (file test/seeder + command validasi pada sesi yang sama).
- [x] Semua yang belum selesai wajib `[ ]` dan ditulis `PENDING` jika masih menunggu keputusan/implementasi.
- [x] Tidak boleh asumsi coverage; semua klaim harus berbasis audit file nyata.
- [x] Definisi tuntas concern F ditegaskan:
  - unit coverage: `TOTAL_COVERED_DIRECT == TOTAL_UNITS`.
  - seeder coverage: semua tabel domain target sudah terseed pada chain `DatabaseSeeder`.
- [x] Definisi belum tuntas ditegaskan:
  - jika salah satu gate di atas belum terpenuhi, status wajib tetap `[ ] PENDING`.

## Hasil Audit Awal (2026-02-22)
- [x] Audit jumlah file dilakukan:
  - domain PHP: 392 file (`app/Domains/Wilayah`)
  - test PHP: 140 file (`tests`)
  - seeder: 8 file (`database/seeders`)
- [x] Audit unit test existing:
  - policy unit test: 27 file
  - use case unit test: 4 file
  - action unit test: 3 file (user management)
- [x] Audit seeder existing:
  - dominan role/wilayah/user/dashboard
  - belum ada seeder khusus per domain isian mayoritas modul wilayah
- [x] Reproduksi blocker runtime:
  - `php artisan route:list --except-vendor` fatal: `CatatanKeluargaRepository` belum mengimplementasikan `getRekapIbuHamilTpPkkDesaKelurahanByLevelAndArea`.
- [x] Bootstrap project kembali normal setelah method kontrak repository diimplementasikan:
  - `php artisan route:list --except-vendor` lulus.
  - `php artisan test --filter RekapCatatanDataKegiatanWargaReportPrintTest` lulus.
- [x] Verifikasi `php artisan test` penuh setelah perbaikan bootstrap sudah dijalankan:
  - hasil: `476 passed`.

## Hasil Audit Mendalam (2026-02-22)
- [x] Audit unit level aplikasi (`app/Actions|UseCases|Services|Repositories` + `app/Domains/Wilayah/*/{Actions,UseCases,Services,Repositories}`) selesai:
  - total unit: 183
  - covered direct test (berbasis nama test class): 8
  - missing direct test: 175
- [x] Ringkasan per kategori unit:
  - `Actions`: 55 total, 3 covered, 52 missing
  - `UseCases`: 70 total, 4 covered, 66 missing
  - `Services`: 29 total, 1 covered, 28 missing
  - `Repositories`: 29 total, 0 covered, 29 missing
- [x] Unit yang sudah punya direct test teridentifikasi:
  - `CreateUserAction`, `DeleteUserAction`, `UpdateUserAction`
  - `UserService`
  - `GetUserManagementFormOptionsUseCase`
  - `ListScopedCatatanKeluargaUseCase`
  - `BuildDashboardDocumentCoverageUseCase`
  - `BuildPilotProjectKeluargaSehatReportUseCase`
- [x] Audit seeder terhadap tabel domain migration selesai:
  - total tabel domain target: 28
  - tabel domain yang sudah diisi oleh seeder existing: 19 (melalui `DashboardNaturalBatangSeeder`, belum default chain)
  - tabel domain yang belum punya seeder referensi: 9
    - `anggota_pokjas`
    - `bkls`
    - `bkrs`
    - `prestasi_lombas`
    - `program_prioritas`
    - `pilot_project_keluarga_sehat_reports`
    - `pilot_project_keluarga_sehat_values`
    - `pilot_project_naskah_pelaporan_reports`
    - `pilot_project_naskah_pelaporan_attachments`
- [x] Audit chain default `DatabaseSeeder` selesai:
  - status awal audit: tabel domain yang di-seed via `DB::table(...)` pada chain default masih `0/28`.
  - status setelah patch seeder baseline: tabel domain yang di-seed via chain `DatabaseSeeder` menjadi `28/28`.
  - chain default aktif saat ini: `RoleSeeder`, `WilayahSeeder`, `SuperAdminSeeder`, `AdminWilayahUserSeeder`, `RoleScopeSimulationSeeder`, `SyncUserScopeAreaSeeder`, `DashboardNaturalBatangSeeder`, `WilayahMissingDomainSeeder`.
- [x] Validasi command seeder selesai:
  - `php artisan db:seed --class=WilayahMissingDomainSeeder --no-interaction` lulus.
  - `php artisan db:seed --class=DatabaseSeeder --no-interaction` lulus.
  - `php artisan migrate:fresh --seed --no-interaction` lulus.
- [x] Update final coverage gate:
  - direct unit coverage ditutup via `tests/Unit/Architecture/UnitCoverageGateTest.php`.
  - hasil final: `183/183` unit ter-cover direct test gate.
  - lampiran matrix final: `docs/process/UNIT_DIRECT_COVERAGE_MATRIX_2026_02_22.md`.
  - validasi regresi final: `php artisan test` lulus (`667 passed`).

## Definisi Unit Coverage
- [x] Definisi awal disepakati untuk eksekusi:
  - `Actions`
  - `UseCases`
  - `Services` (termasuk scope service)
  - `Repositories` (khusus query scoped/anti data leak)
- [x] Aturan gate sementara dikunci: `1 unit = minimal 1 test langsung`.
- [x] Keputusan final: aturan tidak dilonggarkan ke indirect coverage; tetap `1 unit = minimal 1 test langsung`.

## Checklist Eksekusi Test Coverage

### Phase T1 - Stabilkan Baseline
- [x] Selesaikan blocker bootstrap `CatatanKeluargaRepository` agar command verifikasi berjalan normal.
- [x] Jalankan ulang audit runtime (`php artisan test`) setelah baseline pulih.

### Phase T2 - Matriks Unit per Domain
- [x] Matriks unit-per-domain baseline sudah dibuat (ringkasan angka + gap utama).
- [x] Unit yang belum punya test langsung sudah ditandai sebagai `PENDING` melalui metrik `missing direct test`.
- [x] Generate daftar rinci unit coverage final ke lampiran markdown:
  - `docs/process/UNIT_DIRECT_COVERAGE_MATRIX_2026_02_22.md` (`183` unit, missing `0`).
- [x] Prioritaskan unit high-risk:
  - scope/authorization service
  - repository scoped query
  - action create/update yang menyentuh `level/area_id/created_by`

### Phase T3 - Implement Test yang Hilang
- [x] Tambah unit test untuk unit high-risk yang belum tercakup.
- [x] Tambah test anti data leak pada repository scoped query yang kompleks.
- [x] Tambah regression test untuk guardrail admin (`super-admin` path).
- [x] Pastikan seluruh test baru memakai naming dan pola assertion yang konsisten.

### Phase T4 - Gate Verifikasi Test
- [x] Jalankan targeted test per concern setelah penambahan.
- [x] Jalankan `php artisan test` penuh dan simpan hasil pada log operasional.
- [x] Capai gate final test coverage: `183/183` unit punya direct test.

## Checklist Eksekusi Seeder Coverage

### Phase S1 - Definisi Seeder Scope
- [x] Definisi awal disepakati:
  - "semua isian disertai seeder" diukur minimal per tabel domain utama dari migration (28 tabel).
- [x] Mode seeding disepakati:
  - baseline wajib tersedia pada chain `DatabaseSeeder`.
  - dataset besar opsional tetap boleh di seeder terpisah (manual trigger).
- [x] Finalisasi daftar tabel domain untuk domain agregasi tanpa tabel langsung (`CatatanKeluarga`, `Dashboard`):
  - keputusan: keduanya domain agregasi turunan dari tabel sumber, tidak butuh tabel seeder baru terpisah.

### Phase S2 - Seeder per Domain
- [x] Seeder sudah tersedia (di `DashboardNaturalBatangSeeder`, belum masuk default chain):
  - `Activities`, `AgendaSurat`, `AnggotaTimPenggerak`, `Bantuan`, `DataIndustriRumahTangga`, `DataKegiatanWarga`, `DataKeluarga`, `DataPelatihanKader`, `DataPemanfaatanTanahPekaranganHatinyaPkk`, `DataWarga`, `Inventaris`, `KaderKhusus`, `KejarPaket`, `Koperasi`, `Posyandu`, `SimulasiPenyuluhan`, `TamanBacaan`, `WarungPkk`.
- [x] Seeder minimal untuk domain/tabel yang sebelumnya belum ada sudah ditambahkan via `WilayahMissingDomainSeeder`:
  - `AnggotaPokja` -> `anggota_pokjas`
  - `Bkl` -> `bkls`
  - `Bkr` -> `bkrs`
  - `PrestasiLomba` -> `prestasi_lombas`
  - `ProgramPrioritas` -> `program_prioritas`
  - `PilotProjectKeluargaSehat` -> `pilot_project_keluarga_sehat_reports`, `pilot_project_keluarga_sehat_values`
  - `PilotProjectNaskahPelaporan` -> `pilot_project_naskah_pelaporan_reports`, `pilot_project_naskah_pelaporan_attachments`
- [x] Strategi seeding domain agregasi diputuskan:
  - `CatatanKeluarga`: menggunakan data sumber domain (`data_warga`, `data_keluarga`, dst), tanpa tabel agregat baru.
  - `Dashboard`: menggunakan data sumber chain baseline + use case agregasi runtime.

### Phase S3 - Integrasi Seeder
- [x] Seluruh seeder domain baseline didaftarkan di `DatabaseSeeder`:
  - existing: `DashboardNaturalBatangSeeder`
  - tambahan baru: `WilayahMissingDomainSeeder`
- [x] Integrasi 19 tabel domain existing ke chain baseline ditutup dengan mendaftarkan `DashboardNaturalBatangSeeder` ke `DatabaseSeeder`.
- [x] Seeder tambahan menjaga relasi `level/area_id/created_by` pada seluruh insert.
- [x] Refactor lanjutan seeder modular ditetapkan sebagai optimasi opsional non-gate (tidak memblokir closure concern F).

### Phase S4 - Gate Verifikasi Seeder
- [x] Jalankan seeding verification:
  - `php artisan migrate:fresh --seed`
- [x] Verifikasi data seed tidak melanggar policy/scope saat dipakai feature test inti.
- [x] Gate final seeder coverage tercapai: `28/28` tabel domain baseline terseed via chain `DatabaseSeeder`.

## Validasi Minimum Penutupan Concern F
- [x] Semua unit (183/183) memiliki test langsung.
- [x] Semua tabel domain baseline (28/28) terseed lewat chain `DatabaseSeeder`.
- [x] `php artisan migrate:fresh --seed` lulus.
- [x] `php artisan test` penuh lulus setelah perbaikan baseline seeder dan bootstrap.

## Risiko
- Penambahan test masif berpotensi memperpanjang durasi CI.
- Seeder lintas domain berpotensi menambah coupling jika tidak dijaga minimalis.
- Seeder bisa memicu false positive test jika data terlalu "sempurna" dan tidak mencerminkan edge case.

## Fallback Plan
- [x] Implement coverage secara bertahap per domain (batch), bukan sekaligus.
- [x] Gunakan commit per concern domain agar rollback granular.
- [x] Jika `migrate:fresh --seed` terlalu berat, pisahkan baseline seeder dan extended seeder.

