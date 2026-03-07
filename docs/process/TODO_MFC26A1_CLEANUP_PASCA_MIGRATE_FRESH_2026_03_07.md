# TODO MFC26A1 Cleanup Pasca Migrate Fresh

Tanggal: 2026-03-07  
Status: `planned`
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
- [ ] `areas` tetap single source of truth wilayah.
- [ ] Data domain wilayah tetap konsisten pada `level`, `area_id`, `created_by`.
- [ ] Koherensi `role` vs `scope` vs `area_id` tetap valid.
- [ ] Authority akses tetap backend-first (`Policy -> Scope Service -> middleware`), bukan frontend.
- [ ] Kontrak akses `menuGroupModes` / `moduleModes` tidak drift terhadap enforcement runtime.

## Target Cleanup Prioritas
- [ ] C1. Migration Squash (high impact)
  - gabungkan migration `create + add_*` untuk tabel yang sama menjadi baseline final tunggal.
- [ ] C2. Legacy Role Compatibility (medium-high impact)
  - evaluasi dan kurangi ketergantungan `admin-desa` / `admin-kecamatan` bila migrasi role sudah final.
- [ ] C3. Legacy/Fallback Payload (medium impact)
  - evaluasi penghapusan fallback payload dashboard yang sudah tidak dipakai jalur utama.
- [ ] C4. Alias/Transisi Historis (medium impact)
  - review alias route/istilah transisi; pertahankan hanya yang benar-benar dibutuhkan.
- [ ] C5. Dead Code/Dead Config Removal (medium impact)
  - hapus artefak yang terbukti tidak dipakai runtime + tidak dibutuhkan test/rollback.

## Kandidat Teknis (Baseline Audit)

### A. Kandidat Squash Migration
- [ ] `pilot_project_naskah_pelaporan_reports`
  - `create_*` + `add_penutup_*` + `add_head_surat_fields_*`
- [ ] `program_prioritas`
  - `create_*` + `add_jadwal_bulanan_*`
- [ ] `agenda_surats`
  - `create_*` + `add_data_dukung_path_*`
- [ ] modul lain yang masih pola `create` lalu patch struktural terpisah untuk tabel sama.

### B. Kandidat Cleanup Compatibility Role
- [ ] audit pemakaian aktif `admin-desa` / `admin-kecamatan` pada:
  - resolver visibilitas role,
  - matrix akses/read-only matrix super-admin,
  - seeder migrasi role legacy.
- [ ] putuskan retain/deprecate dengan owner sebelum patch.

### C. Kandidat Cleanup Fallback Legacy
- [ ] audit payload dashboard fallback (`dashboardStats/dashboardCharts`) vs jalur utama (`dashboardBlocks`).
- [ ] tentukan apakah fallback dihapus penuh atau dipertahankan dengan batas waktu.

### D. Kandidat Cleanup Route/Request/Repository
- [ ] audit route alias transisi yang masih dipakai vs yang sudah dead.
- [ ] audit normalisasi request/mapper legacy yang sudah tidak diperlukan.
- [ ] audit repository branch/fallback khusus transisi yang bisa dipangkas.

### E. Evidence Gate (Wajib sebelum Hapus)
- [ ] setiap kandidat hapus harus punya bukti:
  - hasil pencarian penggunaan (`rg`) = tidak dipakai jalur aktif,
  - tidak menjadi dependency test utama,
  - ada fallback/rollback jelas jika asumsi meleset.

## Rencana Eksekusi Bertahap

### Wave 0 - Preflight dan Freeze
- [ ] freeze daftar cleanup yang disetujui owner.
- [ ] pastikan tidak ada concern lain yang memodifikasi migration concern sama.
- [ ] lock test matrix wajib sebelum patch.

### Wave 1 - Migration Baseline Simplification
- [ ] refactor migration kandidat C1 (satu tabel per batch kecil).
- [ ] jalankan:
  - `php artisan migrate:fresh --seed`
  - targeted feature test domain terdampak.
- [ ] verifikasi struktur tabel final sama dengan kontrak domain aktif.

### Wave 2 - Role Compatibility Cleanup
- [ ] kurangi role legacy compatibility sesuai keputusan owner.
- [ ] verifikasi gate akses:
  - `RoleMenuVisibilityServiceTest`
  - `RoleMenuVisibilityGlobalContractTest`
  - `ModuleVisibilityMiddlewareTest`
  - `MenuVisibilityPayloadTest`

### Wave 3 - Legacy/Fallback Payload Cleanup
- [ ] bersihkan fallback yang diputuskan deprecated.
- [ ] verifikasi dashboard/menu sinkron:
  - `DashboardCoverageMenuSyncTest`
  - `DashboardLayoutMenuContractTest`

### Wave 4 - Doc Hardening dan Closing
- [ ] sinkronkan dokumen canonical yang terdampak:
  - `DOMAIN_CONTRACT_MATRIX`,
  - `DOMAIN_DEVIATION_LOG`,
  - `OPERATIONAL_VALIDATION_LOG`,
  - TODO concern terkait.
- [ ] catat keputusan retain/deprecate + alasan auditability.

## Validasi
- [ ] V1. `php artisan migrate:fresh --seed` (wajib per wave migration).
- [ ] V2. targeted tests sesuai wave.
- [ ] V3. `php artisan test` setelah wave final.
- [ ] V4. `npm run build` untuk memastikan payload/UI contract tetap stabil.
- [ ] V5. scoped grep audit pasca-cleanup:
  - artefak legacy target benar-benar hilang,
  - tidak ada referensi putus.
- [ ] V6. uji ulang TODO minimal 3 putaran sebelum eksekusi wave-1.

## Risiko
- Risiko 1: perubahan migration baseline menyebabkan drift skema jika ada dependensi tersembunyi.
- Risiko 2: penghapusan compatibility role memutus akses user yang belum termigrasi.
- Risiko 3: fallback dashboard dihapus terlalu cepat sementara konsumsi UI belum 100% lock.
- Risiko 4: cleanup lintas concern tanpa freeze owner memicu rework.

## Keputusan
- [ ] K1: concern dieksekusi per wave dengan gate hijau per wave.
- [ ] K2: tidak ada penghapusan compatibility tanpa bukti usage audit.
- [ ] K3: setiap cleanup yang mengubah kontrak wajib disinkronkan ke dokumen canonical pada sesi yang sama.

## Keputusan Arsitektur (Jika Ada)
- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan
- rollback per wave via revert commit concern wave terkait.
- jika gagal di wave migration, pulihkan baseline migration terakhir yang lulus `migrate:fresh --seed`.
- jika gagal di wave role compatibility, aktifkan sementara mapping compatibility lama sambil perbaiki data role.

## Output Final
- [ ] Ringkasan cleanup yang diterapkan per wave.
- [ ] Daftar file terdampak (migration/seeder/service/ui/docs/tests).
- [ ] Bukti validasi + residual risk + keputusan owner final (`go/hold`).

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
