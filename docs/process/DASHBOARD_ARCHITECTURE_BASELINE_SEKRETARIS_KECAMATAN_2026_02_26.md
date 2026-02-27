# Dashboard Architecture Baseline Sekretaris Kecamatan (2026-02-26)

Status: `active-baseline`  
Tujuan: referensi implementasi dashboard role lain dengan pola yang sama seperti `kecamatan-sekretaris`.

## Kontrak Utama

- Route utama dashboard: `GET /dashboard` (`auth`, `verified`).
- Arsitektur wajib: `Controller -> UseCase/Service -> Repository Interface -> Repository -> Model`.
- Akses backend tetap authority:
  - scope efektif ditentukan dari kombinasi `role + area.level` (bukan frontend),
  - jika metadata stale (role/scope/area tidak sinkron), payload aman menjadi nol/kosong.
- `dashboardBlocks[]` adalah source utama rendering dinamis; payload legacy (`dashboardStats/dashboardCharts`) hanya fallback transisi.

## Alur End-to-End (Aktual)

1. `DashboardController` membaca query:
   - `mode`, `level`, `sub_level`,
   - `section1_month`,
   - `section2_group` (kompatibilitas, default `all`),
   - `section3_group` (kompatibilitas, default `all`).
2. `DashboardActivityChartService` membangun blok activity sesuai scope user.
3. `BuildDashboardDocumentCoverageUseCase` membangun coverage dokumen + cache berdasarkan scope/area/role/filter signature.
4. `BuildRoleAwareDashboardBlocksUseCase` menggabungkan data activity + documents menjadi `dashboardBlocks[]` role-aware.
5. Inertia mengirim `dashboardStats`, `dashboardCharts`, `dashboardBlocks` ke `Dashboard.vue`.

## Struktur Section Khusus Sekretaris Kecamatan

Precondition masuk mode section sekretaris:
- group `sekretaris-tpk` tersedia, dan
- minimal satu group pokja (`pokja-i..pokja-iv`) tersedia.

Section yang dihasilkan backend (skenario aktif saat ini):

1. `sekretaris-section-1` (Domain Sekretaris)
- Jenis blok: `activity`
- Context dipaksa: `mode=by-level`, `level=kecamatan`
- Sumber modul: `activities`
- Tidak merender section lanjutan (`section-2/3/4`).

## Kontrak Data Block

Setiap block di `dashboardBlocks[]` membawa:
- `key`, `kind`, `group`, `group_label`, `mode`, `title`,
- `stats`,
- `charts`,
- `sources`:
  - `source_group`,
  - `source_scope`,
  - `source_area_type`,
  - `source_modules`,
  - `source_note`,
  - `filter_context` (`mode`, `level`, `sub_level`, `section1_month`, `section2_group`, `section3_group`).

Untuk block dokumen, chart utama:
- `charts.coverage_per_module` (`labels`, `values`, `items`).

## Mapping Group -> Module (Sumber Visibilitas)

Kontrak mapping berasal dari `RoleMenuVisibilityService::GROUP_MODULES`:
- `sekretaris-tpk`
- `pokja-i`
- `pokja-ii`
- `pokja-iii`
- `pokja-iv`
- `monitoring` (khusus scope kecamatan)

Mode akses group per role berasal dari `ROLE_GROUP_MODES` dan di-resolve per scope.

## Guardrail Data & Keamanan

- Scope efektif dihitung backend via `UserAreaContextService`; frontend tidak bisa memaksa akses lintas area.
- Query coverage dokumen dibatasi repository by `level` + `area_id` (+ desa turunan jika scope kecamatan dan modul mengizinkan).
- Jika scope tidak valid, blok dinamis dikembalikan kosong.

## Cache Dashboard Dokumen

- Cache key membawa:
  - scope,
  - area_id,
  - cache version,
  - role signature,
  - filter signature (`mode|level|sub_level`),
  - block signature.
- Invalidasi cache bersifat event-based via observer model dashboard coverage.

## Catatan UI Saat Ini (Penting untuk Replikasi)

- `Dashboard.vue` menampilkan satu section untuk skenario sekretaris.
- Query `section2_group/section3_group` dipertahankan agar URL lama tetap kompatibel, tetapi tidak dipakai untuk merender section lanjutan.

## Perekaman Detail Frontend (Aktual)

### Sumber Render dan State Filter

- Sumber render utama: `dashboardBlocks[]` (`hasDynamicBlocks`).
- Untuk skenario sekretaris aktif:
  - section yang dirender hanya `sekretaris-section-1`.
  - filter pokja (`section2_group/section3_group`) tidak muncul di UI.
- Token query tetap dinormalisasi:
  - `mode`, `level`, `sub_level`,
  - `section1_month`,
  - `section2_group`, `section3_group` (kompatibilitas URL).
- Untuk sekretaris kecamatan, sinkronisasi query tetap mengunci:
  - `mode=by-level`,
  - `level=kecamatan`,
  - `sub_level=all`.

### Matrix Chart (Tipe, Orientasi, Warna)

1. Activity block (section utama sekretaris kecamatan):
- Chart A: `Jumlah Kegiatan per Desa`
  - type: `pie` (ApexCharts),
  - orientasi: radial/pie,
  - warna seri: `#06b6d4`, `#f97316`, `#ef4444`, `#22c55e`, `#a855f7`, `#eab308`, `#0f766e`, `#db2777`, `#1d4ed8`, `#65a30d`.
- Chart B: `Jumlah Buku vs Buku Terisi`
  - type: `bar` (ApexCharts),
  - orientasi: vertikal (`horizontal: false`),
  - warna seri: `#7e22ce` (Jumlah Buku), `#16a34a` (Buku Terisi).

2. Activity block (fallback non by-desa):
- Chart A: `Kegiatan Bulanan`
  - type: `bar` multi-series (ApexCharts),
  - orientasi: vertikal (`horizontal: false`),
  - warna seri: `#0ea5e9` (Jumlah Kegiatan), `#6366f1` (Akumulasi 6 Bulan).
- Chart B: `Distribusi Level`
  - type: `bar` (BarChart),
  - orientasi: vertikal,
  - warna dataset: `#06b6d4` (Desa), `#6366f1` (Kecamatan).

3. Document block (reusable lintas role; saat skenario sekretaris satu-section tidak aktif):
- Chart: `Cakupan Per Modul/Desa`
  - type: `bar` (BarChart),
  - orientasi: horizontal (`horizontal` prop aktif),
  - warna dataset: `#10b981`.

### Warna Item Status dan Badge

- Badge mode block:
  - `RO`: amber (`border-amber-300`, `bg-amber-50`, `text-amber-700`),
  - `RW`: emerald (`border-emerald-300`, `bg-emerald-50`, `text-emerald-700`).
- Warna KPI widget:
  - `Total Kegiatan`: `text-blue-500`,
  - `Kegiatan Bulan Ini`: `text-indigo-500`,
  - `Jumlah Buku`: `text-cyan-500`,
  - `Buku Terisi`: `text-emerald-500`,
  - `Buku Belum Terisi`: `text-rose-500`,
  - `Total Entri`: `text-violet-500`.

### Perilaku Item/Filter Terpilih

- Opsi filter terpilih mengikuti `v-model` bawaan komponen `select` (native browser style).
- Tidak ada style warna khusus tambahan untuk option terpilih di dropdown.
- Interaksi klik item chart tidak memiliki override warna state aktif; perilaku highlight mengikuti default library chart.

### Empty-State dan Keterbacaan

- Empty-state chart dokumen: `Belum ada data untuk pilihan ini. Coba pilih pokja lain atau tampilkan semua pokja.`
- Empty-state activity by-desa:
  - `Belum ada kegiatan desa pada bulan yang dipilih.`
  - `Belum ada buku terisi pada bulan yang dipilih.`
- Empty-state activity bulanan:
  - `Belum ada kegiatan terhitung untuk periode ini.`

### Tabel Informatif Blok Dashboard

- Setiap blok dashboard merender tabel informatif di bawah kartu KPI.
- Struktur kolom:
  - `Informasi`,
  - `Nilai`,
  - `Keterangan`.
- Baris profil blok minimal:
  - `Jenis Dashboard`,
  - `Mode Akses`,
  - `Cakupan Wilayah`,
  - `Filter Aktif`.
- Baris metrik mengambil data yang sama dengan kartu KPI agar tidak terjadi drift angka antara kartu dan tabel.
- Baris level ditampilkan eksplisit agar semua level terbaca:
  - blok `activity`: mengikuti `charts.level` (minimal `Desa` dan `Kecamatan`),
  - blok `documents`: agregasi level `desa` dan `kecamatan` dari item coverage per modul.

## Perekaman Behavior Sidebar (Aktual)

### State dan Persistensi

- State utama:
  - `sidebarOpen` untuk drawer mobile (`true/false`),
  - `sidebarCollapsed` untuk mode desktop ringkas.
- Persistensi collapse memakai `localStorage` key: `sidebar-collapsed`.
  - nilai `1` = collapsed,
  - nilai `0` = expanded.
- Jika akses storage gagal, UI tetap jalan dengan state in-memory (graceful fallback).

### Perilaku Mobile vs Desktop

- Mobile:
  - sidebar muncul sebagai drawer (`translate-x`),
  - ada overlay gelap; klik overlay menutup sidebar,
  - tombol hamburger membuka/menutup sidebar.
- Desktop:
  - sidebar selalu attach di kiri,
  - lebar expanded: `lg:w-64`,
  - lebar collapsed: `lg:w-20`,
  - area konten menyesuaikan padding kiri (`lg:pl-64` / `lg:pl-20`).

### Visibilitas Menu Berdasarkan Akses Backend

- Sidebar tidak menjadi authority akses; menu di-filter dari `auth.user.menuGroupModes`.
- Group yang tidak ada mode-nya (`read-only`/`read-write`) tidak dirender.
- Item menu internal dideduplicate per `href` agar tidak muncul ganda lintas group.
- Scope menu:
  - `desa` -> group scoped desa,
  - `kecamatan` -> group scoped kecamatan + group `monitoring`.

### Perilaku Group Menu

- State buka/tutup group disimpan per scope:
  - `desaGroupOpen`,
  - `kecamatanGroupOpen`.
- Default group terbuka jika:
  - group sedang aktif (route current berada di salah satu item), atau
  - mode group `read-write`.
- Jika sidebar collapsed dan user klik header group:
  - tidak expand accordion,
  - langsung redirect ke item pertama group (primary item).
- Jika sidebar expanded dan user klik header group:
  - toggle expand/collapse accordion.

### Penanda Aktif, Badge, dan Label Saat Collapsed

- Group aktif diberi style highlight hijau (`bg-emerald-600 text-white`).
- Item aktif di dalam group diberi style aktif lebih terang (`bg-emerald-500/80 text-white`).
- Badge mode `RO` tampil hanya saat sidebar expanded (`v-show="!sidebarCollapsed"`).
- Saat collapsed:
  - label group dipendekkan ke kode (`ST`, `P1`, `P2`, `P3`, `P4`, `MON`),
  - info user card disembunyikan.

## Checklist Implementasi ke Role Lain

- [ ] Tetapkan kontrak section role baru (section mana aktif, source level, query key filter).
- [ ] Tambahkan/ubah mapping role ke group-mode di `RoleMenuVisibilityService` tanpa bypass scope gate.
- [ ] Pastikan `BuildRoleAwareDashboardBlocksUseCase` membangun block per section sesuai kontrak role baru.
- [ ] Jika butuh breakdown turunan wilayah, gunakan repository boundary (jangan query ad-hoc di controller/Vue).
- [ ] Sinkronkan filter query URL dan `sources.filter_context`.
- [ ] Putuskan apakah UI role baru memakai semua section backend atau subset (seperti simplifikasi kecamatan-sekretaris saat ini).
- [ ] Tambahkan feature test jalur sukses + stale metadata + anti data leak.
- [ ] Tambahkan test sinkronisasi mapping menu-vs-dashboard jika ada slug/group baru.

## Validasi Minimum Replikasi

- `php artisan test --filter=DashboardDocumentCoverageTest`
- `php artisan test --filter=DashboardActivityChartTest`
- `php artisan test --filter=DashboardCoverageMenuSyncTest`
- Smoke test manual query:
  - `mode|level|sub_level`
  - `section1_month`
  - `section2_group` dan `section3_group` tetap fallback ke `all` (kompatibilitas URL).

## Referensi Implementasi

- `routes/web.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Services/DashboardActivityChartService.php`
- `app/Domains/Wilayah/Dashboard/UseCases/BuildDashboardDocumentCoverageUseCase.php`
- `app/Domains/Wilayah/Dashboard/UseCases/BuildRoleAwareDashboardBlocksUseCase.php`
- `app/Domains/Wilayah/Dashboard/Repositories/DashboardDocumentCoverageRepository.php`
- `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- `app/Domains/Wilayah/Services/UserAreaContextService.php`
- `resources/js/Pages/Dashboard.vue`
- `tests/Feature/DashboardDocumentCoverageTest.php`
- `tests/Feature/DashboardActivityChartTest.php`
- `tests/Unit/Dashboard/DashboardCoverageMenuSyncTest.php`
