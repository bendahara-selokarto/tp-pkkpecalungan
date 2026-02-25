# TODO Koherensi Kritis Dashboard Baseline Sekretaris Kecamatan 2026-02-25

Tanggal: 2026-02-25  
Status: `planned`

## Konteks

- Audit koherensi dashboard lintas role menemukan gap terhadap baseline `kecamatan-sekretaris`.
- Gap kritis:
  - baseline minimalis belum konsisten diterapkan ke seluruh role non `super-admin`,
  - ada cabang render khusus `kecamatan-sekretaris` yang memotong section 2/3/4 hanya di frontend,
  - copy user-facing masih bercampur istilah teknis/internal.
- Concern ini meneruskan hasil:
  - `docs/process/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
  - `docs/process/TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md`
  - `docs/process/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`

## Target Hasil

- Semua dashboard non `super-admin` koheren terhadap baseline `kecamatan-sekretaris` pada aspek visual kritis.
- Perbedaan antar role hanya pada data dan hak akses backend, bukan pola UI utama.
- Metadata teknis tidak mendominasi area konten pada role mana pun.
- Kontrak role/scope/area dan anti data leak tetap terjaga.

## Definisi Koherensi Kritis (Locked)

- [ ] Struktur utama konsisten:
  - header dashboard,
  - panel filter,
  - kartu KPI,
  - area chart,
  - empty-state.
- [ ] Visibilitas metadata konsisten lintas role (aturan tampil/sembunyi sama untuk concern yang setara).
- [ ] Tidak ada branch UI khusus role yang mengubah model section tanpa justifikasi kontrak backend.
- [ ] Istilah user-facing natural, konsisten, dan tanpa token teknis internal.
- [ ] URL filter tetap stabil (`mode`, `level`, `sub_level`, `section1_month`, `section2_group`, `section3_group`).

## Scope Role

- `desa-sekretaris`
- `kecamatan-sekretaris`
- `desa-pokja-i|ii|iii|iv`
- `kecamatan-pokja-i|ii|iii|iv`
- `admin-desa` (kompatibilitas)
- `admin-kecamatan` (kompatibilitas)

## Langkah Eksekusi (Checklist)

- [ ] `K1` Contract lock baseline UI:
  - tetapkan elemen wajib/opsional dashboard yang harus identik lintas role,
  - tetapkan deviasi yang diizinkan beserta justifikasi.
- [ ] `K2` Refactor koherensi renderer dashboard:
  - audit dan eliminasi branch frontend yang membuat perilaku role-specific tidak setara,
  - ubah ke aturan berbasis kontrak section/block yang datang dari backend.
- [ ] `K3` Standardisasi presentasi metadata:
  - definisikan mode tampilan metadata (ringkas/default),
  - terapkan rule tunggal lintas role untuk `Sumber`, `Cakupan`, dan status mode akses.
- [ ] `K4` Copywriting hardening dashboard:
  - normalisasi label filter, helper, empty-state, dan deskripsi section ke bahasa natural user,
  - hilangkan istilah teknis internal pada teks UI.
- [ ] `K5` Hardening test matrix dashboard:
  - tambah/rapikan test untuk memastikan parity perilaku lintas role pada payload yang dirender,
  - jaga regression anti data leak dan stabilitas query filter.
- [ ] `K6` Doc-hardening lintas concern dashboard:
  - sinkronkan status dan keputusan pada TODO dashboard yang berelasi,
  - tandai dokumen lama sebagai `superseded` bila ada keputusan yang diganti.

## File Target (Rencana)

- `resources/js/Pages/Dashboard.vue`
- `app/Http/Controllers/DashboardController.php` (jika perlu normalisasi context payload)
- `app/Domains/Wilayah/Dashboard/UseCases/BuildRoleAwareDashboardBlocksUseCase.php`
- `tests/Feature/DashboardDocumentCoverageTest.php`
- `tests/Feature/DashboardActivityChartTest.php`
- `docs/process/TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md`
- `docs/process/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
- `docs/process/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md` (jika ada pattern baru yang dikunci)

## Validasi Wajib

- [ ] `npm run build`
- [ ] `php artisan test --filter=DashboardDocumentCoverageTest`
- [ ] `php artisan test --filter=DashboardActivityChartTest`
- [ ] `php artisan test --filter=DashboardCoverageMenuSyncTest`
- [ ] `php artisan test` (full suite sebelum final close concern)
- [ ] Smoke test manual desktop + mobile untuk seluruh matrix role scope.

## Risiko

- Drift UI antar role jika refactor hanya menutup sebagian branch kondisi.
- Regresi filter URL section sekretaris saat sinkronisasi rule lintas role.
- Noise metadata kembali muncul karena aturan visibilitas tidak dipusatkan.

## Mitigasi

- Refactor bertahap per concern (`K1` s.d. `K6`) dan validasi di tiap langkah.
- Pertahankan kontrak backend sebagai source of truth; frontend hanya renderer.
- Kunci aturan visibilitas metadata dalam satu rule eksplisit yang dapat diuji.

## Keputusan (To Lock)

- [ ] Baseline visual final tetap `kecamatan-sekretaris` versi aktif.
- [ ] Cabang render role-specific di frontend hanya boleh ada jika dibuktikan oleh kontrak backend.
- [ ] Parity koherensi lintas role menjadi gate wajib sebelum concern dashboard ditutup.

