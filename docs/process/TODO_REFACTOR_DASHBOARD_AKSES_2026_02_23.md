# TODO DRA23R1 Refactor Dashboard Akses 2026-02-23

Tanggal: 2026-02-23  
Status: `done` (`historical`, diikuti fase minimalis dan koherensi kritis)

## Force Latest Marker

- Todo Code: `DRA23R1`
- Marker: `DASH-AKSES-HIST-2026-02-23-R2`
- Dokumen ini adalah kontrak implementasi awal dashboard role-aware.
- Untuk concern eksperimen UI terkini, acuan wajib:
  - `docs/process/TODO_KOHERENSI_KRITIS_DASHBOARD_SEKRETARIS_KECAMATAN_BASELINE_2026_02_25.md`
  - `docs/process/TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md`

Catatan lanjutan visual minimalis:
- Rencana fase lanjutan UI ada di `docs/process/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`.
- Baseline visual untuk fase minimalis mengacu ke dashboard role `kecamatan-sekretaris` versi aktif saat ini.

## Konteks

- Dashboard saat ini masih menyajikan blok statistik generik (`activity` + `documents`) tanpa pemisahan tegas per penanggung jawab organisasi (`Sekretaris TPK`, `Pokja I-IV`).
- Kontrak visibilitas menu per role sudah ada di backend (`RoleMenuVisibilityService`), tetapi kontrak visibilitas dashboard per role belum dipisah menjadi blok dashboard yang eksplisit.
- Kebutuhan produk sesi ini:
  - `pokja` hanya melihat dashboard sesuai pokja-nya,
  - `sekretaris desa` melihat dashboard sekretaris + dashboard tiap pokja,
  - `pokja kecamatan` melihat dashboard pokja berdasarkan desa dalam kecamatannya,
  - label dashboard tidak boleh ambigu; wajib menjelaskan sumber data.

## Target Hasil

- Dashboard berbasis hak akses backend (bukan filter UI semata) dengan blok data per grup tanggung jawab.
- Kontrak data dashboard memuat metadata sumber agar label KPI/chart menjelaskan asal data.
- Tidak ada data leak lintas area/scope dan tidak ada drift `role` vs `scope` vs `areas.level`.
- Tetap mengikuti arsitektur `Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`.
- Prioritas UX: keterbacaan informasi lebih penting daripada kepadatan/tampilan visual.

## Kunci Tujuan Monitor Data

Tujuan monitor data dikunci sebagai kontrak implementasi:

- Memantau `coverage input` per domain/lampiran: `buku_terisi`, `buku_belum_terisi`, `total_entri`.
- Memantau `progress operasional` per level wilayah (`desa`, `kecamatan`, dan sub-level desa untuk scope kecamatan).
- Menjaga `kepatuhan akses` monitoring: semua agregat wajib tunduk `role-scope-area` (anti data leak lintas wilayah).
- Menjamin `keterbacaan sumber data`: setiap KPI/chart wajib menampilkan modul sumber dan cakupan area.
- Menyediakan `monitoring dinamis bertingkat` untuk role berjenjang: mode `all`, `by level`, `by sub-level`.

## Kontrak Dashboard Akses (Role -> Blok Dashboard)

### Struktur Section Dashboard Sekretaris (Terkunci)

- Section 1: `Domain Sekretaris` (`sekretaris-tpk`) tanpa filter pokja.
  - Pada scope `kecamatan`, section 1 menampilkan dua chart activity:
    - `jumlah kegiatan per desa` tipe `pie` (filter bulan),
    - `jumlah buku` terhadap `buku terisi` tipe `bar` (filter bulan).
  - Filter bulan khusus section 1 dikunci menggunakan query `section1_month` dengan opsi `all|1..12`.
- Section 2: `Pokja Level Aktif` (agregat semua pokja pada level user) dengan filter group `all|pokja-i|pokja-ii|pokja-iii|pokja-iv` (query: `section2_group`).
- Section 3: khusus scope `kecamatan`, `Pokja Level Bawah` (agregat pokja pada desa turunan) dengan filter group `all|pokja-i|pokja-ii|pokja-iii|pokja-iv` (query: `section3_group`).
- Scenario khusus kecamatan: ketika `section 3` memilih `pokja-i`, tambahkan `section 4` berisi rincian sumber data per desa (`docs/process/TODO_SCENARIO_KECAMATAN_SECTION4_POKJA_I_2026_02_23.md`).

### Blok Dashboard Canonical

- `sekretaris-tpk`: ringkasan domain sekretaris (`anggota-tim-penggerak`, `kader-khusus`, `agenda-surat`, `buku-keuangan`, `inventaris`, `activities`, `anggota-pokja`, `prestasi-lomba`).
- `pokja-i`: ringkasan domain pokja I (`data-warga`, `data-kegiatan-warga`, `bkl`, `bkr`, `paar`).
- `pokja-ii`: ringkasan domain pokja II (`data-pelatihan-kader`, `taman-bacaan`, `koperasi`, `kejar-paket`).
- `pokja-iii`: ringkasan domain pokja III (`data-keluarga`, `data-industri-rumah-tangga`, `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, `warung-pkk`).
- `pokja-iv`: ringkasan domain pokja IV (`posyandu`, `simulasi-penyuluhan`, `catatan-keluarga`, `program-prioritas`, `pilot-project-naskah-pelaporan`, `pilot-project-keluarga-sehat`).

### Matrix Role

- `desa-pokja-i|ii|iii|iv`: hanya blok pokja masing-masing untuk area desa user.
- `kecamatan-pokja-i|ii|iii|iv`: hanya blok pokja masing-masing, agregasi per desa dalam kecamatan user (wajib ada breakdown per desa).
- `desa-sekretaris`: blok `sekretaris-tpk` (RW) + blok `pokja-i..iv` (RO dashboard-level).
- `kecamatan-sekretaris`: blok `sekretaris-tpk` (RW) + blok `pokja-i..iv` dengan breakdown per desa + indikator monitoring kecamatan.
- `admin-desa/admin-kecamatan` (legacy): fallback kompatibilitas sementara ke pola sekretaris + pokja penuh, sambil menunggu pembersihan role legacy.

## Kontrak Label Dashboard (Anti Ambigu)

Setiap KPI/chart wajib memuat metadata sumber di payload backend:

- `source_group`: `sekretaris-tpk|pokja-i|pokja-ii|pokja-iii|pokja-iv`.
- `source_scope`: `desa|kecamatan`.
- `source_area_type`: `area-sendiri|desa-turunan`.
- `source_modules`: daftar slug modul penyusun metrik.
- `source_note`: deskripsi singkat formula agregasi.

Aturan label UI:

- Judul blok: `Dashboard <Nama Group> - <Level Scope>`.
- Subjudul wajib: `Sumber: <ringkasan modul> | Cakupan: <area>`.
- Label KPI dilarang generik tunggal seperti `Total` tanpa konteks; minimal format `Total Entri <Group>`.
- Label chart harus menyebut dimensi (`per desa`, `per lampiran`, `per status`) agar origin metrik terbaca.

## Langkah Eksekusi (Checklist)

- [x] `D1` Tetapkan kontrak DTO dashboard baru (role-aware):
  - shape rekomendasi: `dashboardBlocks[]` berisi `group`, `mode`, `scope`, `stats`, `charts`, `sources`.
  - pertahankan payload lama sementara (`dashboardStats/dashboardCharts`) selama fase transisi.
- [x] `D2` Buat `DashboardVisibilityService` atau perluasan service existing untuk memetakan blok dashboard yang boleh diakses per role-scope.
- [x] `D3` Tambah use case baru `BuildRoleAwareDashboardUseCase`:
  - resolve effective scope via `UserAreaContextService`.
  - resolve blok visible via service visibilitas.
  - delegasi query ke repository boundary.
- [x] `D4` Pecah repository dashboard per grup agar query tidak fat:
  - minimal `DashboardGroupRepositoryInterface` + implementasi agregasi per group.
  - kecamatan pokja wajib punya query breakdown `by desa` anti data leak.
- [x] `D5` Refactor `DashboardController` agar hanya orchestration use case + mapping response Inertia.
- [x] `D6` Refactor `resources/js/Pages/Dashboard.vue`:
  - render dinamis per `dashboardBlocks`.
  - tampilkan subtitle sumber data konsisten di setiap blok.
  - fallback empty-state per blok jika user sah tetapi belum ada data.
  - sediakan kontrol tampilan data untuk role bertingkat: `all` (semua cakupan), `by level`, `by sub-level`.
  - pengecualian `desa-sekretaris`: default `level=desa`, tanpa `sub-level`, filter tampilan memakai `section2_group` (`all` + `pokja-i..iv`).
  - struktur section sekretaris:
    - section 1: domain sekretaris.
    - section 2: semua pokja level aktif + filter `section2_group`.
    - section 3 (khusus kecamatan): semua pokja level bawah (desa turunan) + filter `section3_group`.
  - desain kontrol wajib mengutamakan keterbacaan (label eksplisit, tanpa istilah ambigu).
  - rincian rencana UI: `docs/process/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`.
- [x] `D7` Hardening cache dashboard:
  - cache key minimal: `scope + area_id + role signature + block signature`.
  - invalidasi event-based + TTL pendek diterapkan untuk menjaga freshness.
- [x] `D8` Dokumentasi kontrak:
  - update `docs/domain/DOMAIN_CONTRACT_MATRIX.md` bagian dashboard representation.
  - update `docs/process/DASHBOARD_CHART_ALIGNMENT_PLAN.md` dengan status baru role-aware.

## Validasi Wajib

- [x] Feature test role valid (jalur sekretaris):
  - sekretaris desa melihat blok sekretaris + semua pokja.
  - sekretaris kecamatan memuat section bertingkat sesuai kontrak.
- [x] Feature test role valid (jalur pokja):
  - pokja desa hanya melihat blok pokja sendiri.
  - pokja kecamatan melihat blok pokja sendiri dengan breakdown desa.
- [x] Feature test role invalid dan scope mismatch (stale metadata) menghasilkan nol data/forbidden sesuai kontrak existing.
- [x] Unit test service visibilitas dashboard role-scope.
- [x] Unit test use case/repository untuk anti data leak lintas kecamatan/desa.
- [x] Snapshot/assertion label sumber di response Inertia agar UI tidak kembali ke label ambigu.
- [x] Jalankan `php artisan test` penuh sebelum merge implementasi.

## Risiko (Residual)

- Risiko query berat karena blok dashboard bertambah per role.
- Risiko drift antara matrix menu dan matrix dashboard jika dikelola terpisah.
- Risiko noise UI jika label sumber terlalu panjang.

## Mitigasi (Aktif)

- Reuse matrix role-group existing sebagai single source untuk menu dan dashboard.
- Gunakan cache pendek + grouping query per blok.
- Terapkan pola label dua tingkat (judul ringkas + subjudul sumber).

## Keputusan yang Dikunci pada Rencana Ini

- [x] Dashboard wajib berbasis role-scope backend, bukan filter frontend.
- [x] Pokja hanya melihat blok pokja terkait.
- [x] Sekretaris desa minimal melihat blok sekretaris + blok pokja I-IV.
- [x] Pokja kecamatan melihat dashboard pokja dengan breakdown per desa.
- [x] Label dashboard wajib menjelaskan sumber modul dan cakupan area.
- [x] Prioritas desain dashboard: keterbacaan informasi di atas aspek tampilan visual.
- [x] Untuk role bertingkat, filter dashboard bersifat dinamis: `all`, filter level, dan filter sub-level.
- [x] Pengecualian role `desa-sekretaris`: filter disederhanakan menjadi `section2_group` (`all` + `pokja-i..iv`) dengan level default tetap `desa`.
- [x] Struktur dashboard sekretaris dikunci menjadi section 1 (domain sekretaris), section 2 (pokja level aktif), dan section 3 khusus kecamatan (pokja level bawah), dengan filter `section2_group`/`section3_group` pada section 2/3.
- [x] Kontrak query filter section dikunci: `section2_group` (section 2) dan `section3_group` (section 3).
- [x] Untuk scope kecamatan, section 1 menampilkan dua chart terpisah: `jumlah kegiatan per desa` tipe `pie` dan `jumlah buku vs buku terisi` tipe `bar`, keduanya mengikuti filter bulan `section1_month`.

## Keputusan Lanjutan yang Perlu Konfirmasi Sebelum Implementasi

- [x] `kecamatan-sekretaris` dan role bertingkat lain memakai mode dinamis: dapat lihat `all`, filter `by level`, atau `by sub-level`.
- [x] Aturan multi-role dashboard mengikuti visibilitas bertingkat yang dapat difilter dinamis (`all/level/sub-level`).
- [x] Blok `monitoring` tetap dipisah dari blok `sekretaris-tpk` pada scope kecamatan agar konteks monitor lintas desa tetap tegas.
