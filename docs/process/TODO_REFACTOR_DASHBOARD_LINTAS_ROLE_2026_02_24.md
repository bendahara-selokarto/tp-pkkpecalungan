# TODO DRL24R1 Refactor Dashboard Lintas Role 2026-02-24

Tanggal: 2026-02-24  
Status: `done` (`superseded` untuk koherensi kritis oleh `docs/process/TODO_KOHERENSI_KRITIS_DASHBOARD_SEKRETARIS_KECAMATAN_BASELINE_2026_02_25.md`)

## Force Latest Marker

- Todo Code: `DRL24R1`
- Marker: `DASH-LINTAS-HIST-2026-02-24-R2`
- Dokumen ini historis (`superseded`) dan tidak menjadi acuan final untuk concern eksperimen UI yang masih berjalan.
- Acuan aktif wajib:
  - `docs/process/TODO_KOHERENSI_KRITIS_DASHBOARD_SEKRETARIS_KECAMATAN_BASELINE_2026_02_25.md`
  - `docs/process/TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md`
- Khusus penataan menu/sidebar, acuan tunggal tetap `docs/process/TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md`.

## Konteks

- Refactor minimalis pada dashboard `kecamatan-sekretaris` sudah menjadi baseline visual terbaru.
- Dashboard role lain masih berpotensi belum seragam dari sisi:
  - hirarki visual,
  - copywriting label,
  - kepadatan informasi,
  - konsistensi filter.
- Kontrak backend `dashboardBlocks[]`, akses role-scope, dan anti data leak harus tetap dipertahankan.

## Target Hasil

- Semua dashboard non `super-admin` mengikuti standar minimalis yang konsisten.
- Label user-facing konsisten menggunakan istilah natural (`Kegiatan`, bukan `Aktivitas` pada konteks dashboard).
- Metadata teknis tidak mendominasi area konten.
- Tidak ada perubahan perilaku akses data yang tidak diminta.

## Baseline UI Yang Dikunci

- [x] Header ringkas: `Dashboard` (header), `Mode`, `Profil`, `Keluar`.
- [x] Menu `Dashboard` tampil di header; desktop sisi kiri header kosong.
- [x] Sidebar fokus domain; item non-esensial sudah disederhanakan.
- [x] Sidebar `Akun` hanya memuat `Profil`; `Keluar` hanya di header.
- [x] Sidebar default terbuka pada grup menu yang berhubungan dengan akun aktif.
- [x] Kartu user sidebar mengikuti warna dasar aplikasi (cyan).
- [x] Kartu user tidak menampilkan email.
- [x] Dashboard `kecamatan-sekretaris` menjadi acuan utama untuk minimalisasi.
- [x] Panel metadata non-esensial (mis. sumber/cakupan panjang, banner pengembangan) tidak ditampilkan.
- [x] Istilah user-facing memakai `Kegiatan` (bukan `Aktivitas`) pada konteks dashboard.

## Matrix Scope Refactor

- `desa-sekretaris`
- `kecamatan-sekretaris`
- `desa-pokja-i|ii|iii|iv`
- `kecamatan-pokja-i|ii|iii|iv`
- `admin-desa` (jalur kompatibilitas)
- `admin-kecamatan` (jalur kompatibilitas)

## Langkah Eksekusi (Checklist)

- [x] `R1` Audit UI lintas role:
  - capture per role: blok tampil, label KPI, chart tampil, empty-state.
  - catat gap terhadap baseline `kecamatan-sekretaris`.
- [x] `R2` Standardisasi copywriting dashboard lintas role:
  - label KPI, judul chart, helper text, empty-state.
  - hindari istilah teknis internal pada teks user-facing.
- [x] `R3` Standardisasi layout blok:
  - jarak antar section,
  - jarak header ke konten dashboard,
  - tinggi chart,
  - jumlah kartu KPI per baris (desktop/mobile).
- [x] `R4` Standardisasi metadata presentasi:
  - tampilkan hanya metadata penting,
  - ringkas detail filter agar tidak menutupi konten utama.
- [x] `R5` Pecah concern komponen frontend (tanpa ubah kontrak backend):
  - ekstrak builder/renderer blok dashboard ke unit yang lebih kecil untuk mengurangi risiko regressi.
- [x] `R6` Audit fallback legacy:
  - verifikasi jalur fallback `dashboardStats/dashboardCharts` masih aman untuk transisi,
  - pastikan fallback tidak tampil saat `dashboardBlocks[]` valid.
- [x] `R7` Hardening akses:
  - validasi role hanya melihat blok yang diizinkan,
  - pastikan filter URL tidak membuka data lintas scope.
- [x] `R8` Doc-hardening:
  - sinkronkan status dengan:
    - `docs/process/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
    - `docs/process/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`
    - `docs/process/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`

## Pembagian Concern (Commit Plan)

- [x] `C1`: standardisasi copywriting lintas role.
- [x] `C2`: standardisasi layout dan spacing dashboard lintas role.
- [x] `C3`: refactor komponen frontend dashboard (tanpa ubah kontrak backend).
- [x] `C4`: hardening fallback + regression test dashboard.
- [x] `C5`: doc-hardening lintas TODO dashboard.

## File Target (Rencana)

- `resources/js/Pages/Dashboard.vue`
- `resources/js/Layouts/DashboardLayout.vue`
- `resources/js/admin-one/components/*` (hanya bila perlu ekstraksi komponen dashboard)
- `tests/Feature/DashboardDocumentCoverageTest.php`
- `tests/Feature/DashboardActivityChartTest.php`
- `tests/Unit/Dashboard/DashboardCoverageMenuSyncTest.php`
- `docs/process/TODO_REFACTOR_DASHBOARD_MINIMALIS_2026_02_24.md`
- `docs/process/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`
- `docs/process/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`

## Validasi Wajib

- [x] `npm run build`
- [x] `php artisan test --filter=DashboardDocumentCoverageTest`
- [x] `php artisan test --filter=DashboardActivityChartTest`
- [x] `php artisan test --filter=DashboardCoverageMenuSyncTest`
- [x] Smoke test manual lintas role pada matrix scope refactor.

## Risiko (Residual)

- Drift tampilan antar role saat baseline diterapkan bertahap.
- Regresi filter URL per section pada role sekretaris.
- UI regressi tak terduga pada mobile ketika blok dipadatkan.

## Mitigasi (Aktif)

- Eksekusi bertahap per concern (`C1` s.d. `C5`) dengan validasi di tiap concern.
- Pertahankan kontrak backend dan query key existing.
- Jalankan smoke test desktop + mobile pada setiap concern selesai.

## Keputusan

- [x] Baseline minimalis utama: dashboard `kecamatan-sekretaris` versi aktif.
- [x] Refactor fokus pada lapisan presentasi; kontrak data backend tetap.
- [x] Laporan hasil tiap concern wajib mencantumkan perubahan, validasi, dan dampaknya.
