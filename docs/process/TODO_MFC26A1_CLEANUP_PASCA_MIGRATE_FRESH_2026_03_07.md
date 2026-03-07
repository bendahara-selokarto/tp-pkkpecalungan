# TODO MFC26A1 Cleanup Pasca Migrate Fresh

Tanggal: 2026-03-07  
Status: `done` (`state:closed-validated`)
Related ADR: `-`

## Aturan Pakai
- `migrate:fresh` pada concern ini hanya untuk environment development (pre-release).
- Semua data lokal akan ter-reset saat eksekusi `php artisan migrate:fresh`.
- Eksekusi concern ini wajib bertahap per wave, tidak big-bang.

## Konteks
- Project masih memiliki beberapa jejak transisi: migration bertingkat (`create` lalu `add_*`), compatibility role legacy (`admin-*`), dan fallback payload/transisi dashboard.
- Karena status pre-release mengizinkan reset data dev, ini momentum paling aman untuk merapikan debt transisional sebelum concern bertambah luas.
- Target concern ini adalah menyederhanakan fondasi data + akses agar refactor berikutnya lebih stabil dan lebih murah diuji.

## Kontrak Concern (Lock)
- Domain:
  - simplifikasi migration baseline,
  - cleanup compatibility layer yang sudah tidak dibutuhkan,
  - pengurangan fallback legacy yang berpotensi drift.
- Scope target:
  - migration + seeder + route/request/use-case/repository + access/visibility contract + dashboard contract + docs canonical.
- Boundary data:
  - `database/migrations`, `database/seeders`,
  - `routes/web.php`, request/use case/repository concern terdampak,
  - `RoleMenuVisibilityService`, `RoleScopeMatrix`, middleware visibilitas, policy/scope service,
  - dashboard payload contract (`dashboardBlocks` vs payload fallback),
  - docs process/domain terkait concern cleanup.
- Acceptance criteria:
  - baseline migration lebih ringkas tanpa mengubah perilaku domain aktif,
  - tidak ada penambahan coupling ke artefak legacy non-canonical,
  - logika bisnis aktif tetap terkunci (tidak ada behavior drift fungsional),
  - akses role/scope tetap koheren pasca cleanup,
  - test matrix utama tetap hijau setelah `migrate:fresh`.
- Dampak keputusan arsitektur: `ya`.

## Kunci Logika Bisnis Aktif (Wajib Dipertahankan)
- [x] `areas` tetap single source of truth wilayah.
- [x] Data domain wilayah tetap konsisten pada `level`, `area_id`, `created_by`.
- [x] Koherensi `role` vs `scope` vs `area_id` tetap valid.
- [x] Authority akses tetap backend-first (`Policy -> Scope Service -> middleware`), bukan frontend.
- [x] Kontrak akses `menuGroupModes` / `moduleModes` tidak drift terhadap enforcement runtime.

## Target Cleanup Prioritas
- [x] C1. Migration Squash (high impact)
  - gabungkan migration `create + add_*` untuk tabel yang sama menjadi baseline final tunggal.
- [x] C2. Legacy Role Compatibility (medium-high impact)
  - evaluasi dan kurangi ketergantungan `admin-desa` / `admin-kecamatan` bila migrasi role sudah final.
- [x] C3. Legacy/Fallback Payload (medium impact)
  - evaluasi penghapusan fallback payload dashboard yang sudah tidak dipakai jalur utama.
- [x] C4. Alias/Transisi Historis (medium impact)
  - review alias route/istilah transisi; pertahankan hanya yang benar-benar dibutuhkan.
- [x] C5. Dead Code/Dead Config Removal (medium impact)
  - hapus artefak yang terbukti tidak dipakai runtime + tidak dibutuhkan test/rollback.

## Kandidat Teknis (Baseline Audit)

### A. Kandidat Squash Migration
- [x] `pilot_project_naskah_pelaporan_reports`
  - `create_*` + `add_penutup_*` + `add_head_surat_fields_*`
- [x] `program_prioritas`
  - `create_*` + `add_jadwal_bulanan_*`
- [x] `agenda_surats`
  - `create_*` + `add_data_dukung_path_*`
- [x] modul lain yang masih pola `create` lalu patch struktural terpisah untuk tabel sama.

### B. Kandidat Cleanup Compatibility Role
- [x] audit pemakaian aktif `admin-desa` / `admin-kecamatan` pada:
  - resolver visibilitas role,
  - matrix akses/read-only matrix super-admin,
  - seeder migrasi role legacy.
- [x] putuskan retain/deprecate dengan owner sebelum patch.

### C. Kandidat Cleanup Fallback Legacy
- [x] audit payload dashboard fallback (`dashboardStats/dashboardCharts`) vs jalur utama (`dashboardBlocks`).
- [x] tentukan apakah fallback dihapus penuh atau dipertahankan dengan batas waktu.

### D. Kandidat Cleanup Route/Request/Repository
- [x] audit route alias transisi yang masih dipakai vs yang sudah dead.
- [x] audit normalisasi request/mapper legacy yang sudah tidak diperlukan.
- [x] audit repository branch/fallback khusus transisi yang bisa dipangkas.

### E. Evidence Gate (Wajib sebelum Hapus)
- [x] setiap kandidat hapus harus punya bukti:
  - hasil pencarian penggunaan (`rg`) = tidak dipakai jalur aktif,
  - tidak menjadi dependency test utama,
  - ada fallback/rollback jelas jika asumsi meleset.

## Rencana Eksekusi Bertahap

### Wave 0 - Preflight dan Freeze
- [x] freeze daftar cleanup yang disetujui owner.
- [x] pastikan tidak ada concern lain yang memodifikasi migration concern sama.
- [x] lock test matrix wajib sebelum patch.

### Wave 1 - Migration Baseline Simplification
- [x] refactor migration kandidat C1 (satu tabel per batch kecil).
- [x] jalankan:
  - `php artisan migrate:fresh --seed`
  - targeted feature test domain terdampak.
- [x] verifikasi struktur tabel final sama dengan kontrak domain aktif.

### Wave 2 - Role Compatibility Cleanup
- [x] tetapkan keputusan compatibility role legacy berdasarkan audit penggunaan aktif.
- [x] verifikasi gate akses:
  - `RoleMenuVisibilityServiceTest`
  - `RoleMenuVisibilityGlobalContractTest`
  - `ModuleVisibilityMiddlewareTest`
  - `MenuVisibilityPayloadTest`

### Wave 3 - Legacy/Fallback Payload Cleanup
- [x] tetapkan keputusan fallback dashboard berdasarkan audit penggunaan aktif.
- [x] verifikasi dashboard/menu sinkron:
  - `DashboardCoverageMenuSyncTest`
  - `DashboardLayoutMenuContractTest`

### Wave 4 - Doc Hardening dan Closing
- [x] sinkronkan dokumen canonical yang terdampak:
  - `DOMAIN_CONTRACT_MATRIX`,
  - `DOMAIN_DEVIATION_LOG`,
  - `OPERATIONAL_VALIDATION_LOG`,
  - TODO concern terkait.
- [x] catat keputusan retain/deprecate + alasan auditability.

## Validasi
- [x] V1. `php artisan migrate:fresh --seed` (wajib per wave migration).
- [x] V2. targeted tests sesuai wave.
- [x] V3. `php artisan test` setelah wave final.
- [x] V4. `npm run build` untuk memastikan payload/UI contract tetap stabil.
- [x] V5. scoped grep audit pasca-cleanup:
  - artefak legacy target benar-benar hilang,
  - tidak ada referensi putus.
- [x] V6. uji ulang TODO minimal 3 putaran sebelum eksekusi wave-1.

## Risiko
- Risiko 1: perubahan migration baseline menyebabkan drift skema jika ada dependensi tersembunyi.
- Risiko 2: penghapusan compatibility role memutus akses user yang belum termigrasi.
- Risiko 3: fallback dashboard dihapus terlalu cepat sementara konsumsi UI belum 100% lock.
- Risiko 4: cleanup lintas concern tanpa freeze owner memicu rework.

## Keputusan
- [x] K1: concern dieksekusi per wave dengan gate hijau per wave.
- [x] K2: tidak ada penghapusan compatibility tanpa bukti usage audit.
- [x] K3: setiap cleanup yang mengubah kontrak wajib disinkronkan ke dokumen canonical pada sesi yang sama.

## Keputusan Arsitektur (Jika Ada)
- [x] ADR baru tidak diperlukan karena perubahan tidak mengubah boundary arsitektur utama dan tidak mengubah enforcement authorization backend.
- [x] Status concern tetap disinkronkan melalui TODO + log validasi operasional.

## Fallback Plan
- rollback per wave via revert commit concern wave terkait.
- jika gagal di wave migration, pulihkan baseline migration terakhir yang lulus `migrate:fresh --seed`.
- jika gagal di wave role compatibility, aktifkan sementara mapping compatibility lama sambil perbaiki data role.

## Output Final
- [x] Ringkasan cleanup yang diterapkan per wave.
- [x] Daftar file terdampak (migration/seeder/service/ui/docs/tests).
- [x] Bukti validasi + residual risk + keputusan owner final (`go/hold`).

## Siklus Uji Ulang TODO (Minimal 3x)

### Putaran 1 - Coverage Keseluruhan Project
- [x] Cek bahwa rencana mencakup jalur `database`, `backend contract`, `route/request/repository`, `frontend/dashboard`, `tests`, dan `docs`.
- Hasil: `PASS` (scope TODO diperluas ke route/request/repository + policy/scope service).

### Putaran 2 - Lock Logika Bisnis Aktif
- [x] Cek bahwa TODO memiliki guard eksplisit untuk invariants bisnis aktif (areas canonical, role-scope-area coherence, backend authority).
- Hasil: `PASS` (ditambahkan section `Kunci Logika Bisnis Aktif` sebagai gate wajib).

### Putaran 3 - Removal Harus Evidence-Based
- [x] Cek bahwa TODO melarang penghapusan asumtif tanpa bukti penggunaan.
- Hasil: `PASS` (ditambahkan `Evidence Gate`, `V5`, dan gate dead code/config removal).

## Progress Update 2026-03-07 (Closing Concern `MFC26A1`)

- Ringkasan eksekusi:
  - concern ditutup `done` melalui eksekusi wave 1-4 dengan keputusan retain/deprecate berbasis evidence;
  - `php artisan migrate:fresh --seed` dijalankan dan seluruh data lokal development ter-reset.
- Wave 1 (migration squash):
  - kolom `data_dukung_path` digabung ke migration create `agenda_surats`;
  - kolom `jadwal_bulan_1..12` digabung ke migration create `program_prioritas`;
  - kolom `surat_*` + `penutup` digabung ke migration create `pilot_project_naskah_pelaporan_reports`;
  - migration transisi berikut dihapus:
    - `2026_02_28_000000_add_data_dukung_path_to_agenda_surats_table.php`,
    - `2026_02_24_180000_add_jadwal_bulanan_columns_to_program_prioritas_table.php`,
    - `2026_02_22_132000_add_penutup_to_pilot_project_naskah_pelaporan_reports_table.php`,
    - `2026_02_22_133000_add_head_surat_fields_to_pilot_project_naskah_pelaporan_reports_table.php`.
- Wave 2 (legacy role compatibility):
  - audit usage `admin-desa/admin-kecamatan` menunjukkan referensi masih aktif (`887` referensi; `4` file `app`, `2` file `resources/js`, `2` file `database/seeders`, `155` file `tests`);
  - keputusan owner concern: compatibility legacy **dipertahankan sementara** (tidak dipangkas pada wave ini).
- Wave 3 (fallback dashboard):
  - audit usage menunjukkan fallback masih aktif (`dashboardStats`: `4` file, `dashboardCharts`: `4` file, `dashboardBlocks`: `3` file);
  - keputusan owner concern: fallback `dashboardStats/dashboardCharts` **dipertahankan sementara** (belum deprecated).
- Wave 4 (doc hardening):
  - `docs/domain/DOMAIN_CONTRACT_MATRIX.md` disinkronkan agar referensi migration sesuai hasil squash.
- Validasi yang dijalankan:
  - `php artisan migrate:fresh --seed` -> `PASS`;
  - targeted suite (12 file concern migration + visibility/dashboard) -> `PASS` (`80` tests, `615` assertions);
  - `php artisan test --compact` -> `PASS` (`1057` tests, `7110` assertions);
  - `npm run build` -> `PASS`.
- Keputusan final concern:
  - `go` untuk menutup concern cleanup;
  - residual risk yang tersisa: compatibility role legacy + fallback dashboard masih aktif dan perlu concern terpisah saat owner siap deprecate.
