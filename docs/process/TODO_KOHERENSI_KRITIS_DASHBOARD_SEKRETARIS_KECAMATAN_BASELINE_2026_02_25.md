# TODO DKB25R1 Koherensi Kritis Dashboard Baseline Sekretaris Kecamatan 2026-02-25

Tanggal: 2026-02-25  
Status: `done` (`experimental-ui-only`, non-final, rolling)

## Force Latest Marker

- Todo Code: `DKB25R1`
- Marker: `DASH-COHERENCE-EXP-2026-02-25-R1`
- Jika ada analisa yang memakai versi TODO ini sebelum marker ini ditambahkan, analisa tersebut dianggap usang.
- Wajib gunakan isi terbaru dokumen ini sebagai acuan concern koherensi UI dashboard eksperimen.
- Concern turunan chart/filter lintas role pasca Apex dipisahkan ke acuan tunggal:
  - `docs/process/TODO_DCF25R1_KOHERENSI_CHART_FILTER_LINTAS_ROLE_2026_02_25.md`

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
- Fase saat ini dibatasi pada eksperimen UI (presentasi) dan belum menjadi keputusan final.

## Target Hasil

- Semua dashboard non `super-admin` koheren terhadap baseline `kecamatan-sekretaris` pada aspek visual kritis.
- Perbedaan antar role hanya pada data dan hak akses backend, bukan pola UI utama.
- Metadata teknis tidak mendominasi area konten pada role mana pun.
- Perubahan ini tidak mengubah kontrak backend; akses dan anti data leak tetap mengikuti implementasi yang sudah ada.

## Definisi Koherensi Kritis (Locked)

- [x] Struktur utama konsisten:
  - header dashboard,
  - panel filter,
  - kartu KPI,
  - area chart,
  - empty-state.
- [x] Visibilitas metadata konsisten lintas role (aturan tampil/sembunyi sama untuk concern yang setara).
- [x] Tidak ada branch UI khusus role yang mengubah model section tanpa justifikasi kontrak backend.
- [x] Istilah user-facing natural, konsisten, dan tanpa token teknis internal.
- [x] URL filter tetap stabil (`mode`, `level`, `sub_level`, `section1_month`, `section2_group`, `section3_group`).

## Scope Role

- `desa-sekretaris`
- `kecamatan-sekretaris`
- `desa-pokja-i|ii|iii|iv`
- `kecamatan-pokja-i|ii|iii|iv`
- `admin-desa` (kompatibilitas)
- `admin-kecamatan` (kompatibilitas)

## Scope Eksekusi

- In scope:
  - penyesuaian presentasi UI dashboard (`resources/js`),
  - copywriting user-facing,
  - sinkronisasi dokumen TODO concern UI dashboard.
- Out of scope:
  - perubahan policy/middleware/repository/query backend,
  - perubahan test matrix E2E backend sebagai syarat fase eksperimen UI.

## Langkah Eksekusi (Checklist)

- [x] `K1` Contract lock baseline UI:
  - tetapkan elemen wajib/opsional dashboard yang harus identik lintas role,
  - tetapkan deviasi yang diizinkan beserta justifikasi.
- [x] `K2` Refactor koherensi renderer dashboard:
  - audit dan eliminasi branch frontend yang membuat perilaku role-specific tidak setara,
  - ubah ke aturan berbasis kontrak section/block yang datang dari backend.
- [x] `K3` Standardisasi presentasi metadata:
  - definisikan mode tampilan metadata (ringkas/default),
  - terapkan rule tunggal lintas role untuk `Sumber`, `Cakupan`, dan status mode akses.
- [x] `K4` Copywriting hardening dashboard:
  - normalisasi label filter, helper, empty-state, dan deskripsi section ke bahasa natural user,
  - hilangkan istilah teknis internal pada teks UI.
- [x] `K5` Validasi regresi UI dashboard:
  - verifikasi parity tampilan lintas role pada data yang sama (tanpa ubah kontrak backend),
  - verifikasi interaksi filter URL tetap stabil pada UI concern aktif.
- [x] `K6` Doc-hardening lintas concern dashboard:
  - sinkronkan status dan keputusan pada TODO dashboard yang berelasi,
  - tandai dokumen lama sebagai `superseded` bila ada keputusan yang diganti.

## File Target (Rencana)

- `resources/js/Pages/Dashboard.vue`
- `docs/process/TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md`
- `docs/process/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
- `docs/process/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md` (jika ada pattern baru yang dikunci)

## Validasi Wajib

- [x] `npm run build`
- [x] Smoke check parity desktop + mobile (berbasis regression dashboard + audit renderer UI).
- [x] Smoke check stabilitas filter URL (`mode`, `level`, `sub_level`, `section1_month`, `section2_group`, `section3_group`) pada concern aktif (berbasis regression test + audit query binding UI).

## Risiko

- Drift UI antar role jika refactor hanya menutup sebagian branch kondisi.
- Regresi filter URL section sekretaris saat sinkronisasi rule lintas role.
- Noise metadata kembali muncul karena aturan visibilitas tidak dipusatkan.

## Mitigasi

- Refactor bertahap per concern (`K1` s.d. `K6`) dan validasi di tiap langkah.
- Pertahankan kontrak backend sebagai source of truth; frontend hanya renderer.
- Kunci aturan visibilitas metadata dalam satu rule eksplisit yang dapat diuji.

## Keputusan (To Lock)

- [x] Baseline visual sementara tetap `kecamatan-sekretaris` versi aktif.
- [x] Keputusan pada fase ini bersifat eksperimental UI dan dapat berubah pada iterasi berikutnya.
- [x] Perubahan backend/E2E baru boleh dibuka jika ada concern terpisah setelah eksperimen UI stabil.

