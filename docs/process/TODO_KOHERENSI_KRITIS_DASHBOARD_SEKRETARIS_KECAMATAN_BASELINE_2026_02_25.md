# TODO Koherensi Kritis Dashboard Baseline Sekretaris Kecamatan 2026-02-25

Tanggal: 2026-02-25  
Status: `planned` (`experimental-ui-only`, non-final)

## Force Latest Marker

- Marker: `DASH-COHERENCE-EXP-2026-02-25-R1`
- Jika ada analisa yang memakai versi TODO ini sebelum marker ini ditambahkan, analisa tersebut dianggap usang.
- Wajib gunakan isi terbaru dokumen ini sebagai acuan concern koherensi UI dashboard eksperimen.

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

## Scope Eksekusi

- In scope:
  - penyesuaian presentasi UI dashboard (`resources/js`),
  - copywriting user-facing,
  - sinkronisasi dokumen TODO concern UI dashboard.
- Out of scope:
  - perubahan policy/middleware/repository/query backend,
  - perubahan test matrix E2E backend sebagai syarat fase eksperimen UI.

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
- [ ] `K5` Validasi regresi UI dashboard:
  - verifikasi parity tampilan lintas role pada data yang sama (tanpa ubah kontrak backend),
  - verifikasi interaksi filter URL tetap stabil pada UI concern aktif.
- [ ] `K6` Doc-hardening lintas concern dashboard:
  - sinkronkan status dan keputusan pada TODO dashboard yang berelasi,
  - tandai dokumen lama sebagai `superseded` bila ada keputusan yang diganti.

## File Target (Rencana)

- `resources/js/Pages/Dashboard.vue`
- `docs/process/TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md`
- `docs/process/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
- `docs/process/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md` (jika ada pattern baru yang dikunci)

## Validasi Wajib

- [ ] `npm run build`
- [ ] Smoke test manual desktop + mobile untuk seluruh matrix role scope.
- [ ] Smoke test manual filter URL (`mode`, `level`, `sub_level`, `section1_month`, `section2_group`, `section3_group`) pada role concern aktif.

## Risiko

- Drift UI antar role jika refactor hanya menutup sebagian branch kondisi.
- Regresi filter URL section sekretaris saat sinkronisasi rule lintas role.
- Noise metadata kembali muncul karena aturan visibilitas tidak dipusatkan.

## Mitigasi

- Refactor bertahap per concern (`K1` s.d. `K6`) dan validasi di tiap langkah.
- Pertahankan kontrak backend sebagai source of truth; frontend hanya renderer.
- Kunci aturan visibilitas metadata dalam satu rule eksplisit yang dapat diuji.

## Keputusan (To Lock)

- [ ] Baseline visual sementara tetap `kecamatan-sekretaris` versi aktif.
- [ ] Keputusan pada fase ini bersifat eksperimental UI dan dapat berubah pada iterasi berikutnya.
- [ ] Perubahan backend/E2E baru boleh dibuka jika ada concern terpisah setelah eksperimen UI stabil.
