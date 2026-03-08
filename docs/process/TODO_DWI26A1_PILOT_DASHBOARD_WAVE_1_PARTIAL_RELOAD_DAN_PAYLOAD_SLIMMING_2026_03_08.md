# TODO DWI26A1 Pilot Dashboard Wave 1 Partial Reload Dan Payload Slimming

Tanggal: 2026-03-08  
Status: `planned`
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Concern ini adalah child concern dari `SPA26A1` untuk mengeksekusi `Wave 1` pada halaman dashboard sebagai pilot pertama.
- Baseline dashboard saat ini:
  - route tunggal `GET /dashboard` mengirim `dashboardStats`, `dashboardCharts`, `dashboardBlocks`, dan `dashboardContext`,
  - frontend masih melakukan beberapa `router.get('/dashboard', ...)` pada handler filter dan watcher sinkronisasi query,
  - kontrak dashboard yang ada sudah dijaga ketat oleh `DashboardActivityChartTest` dan `DashboardDocumentCoverageTest`.
- Target pilot ini bukan mengubah representasi domain dashboard, tetapi merapikan mekanisme visit/filter agar:
  - kunjungan filter tidak mengirim ulang prop yang tidak perlu,
  - query sync tidak memicu visit berulang yang bisa dihindari,
  - payload dashboard lebih tipis pada interaction loop tanpa memecah arsitektur Inertia.

## Kontrak Concern (Lock)

- Domain: dashboard representation + runtime delivery concern.
- Role/scope target: seluruh role non `super-admin` yang memakai `/dashboard`, terutama `desa` dan `kecamatan`.
- Boundary data:
  - backend: `app/Http/Controllers/DashboardController.php`,
  - frontend: `resources/js/Pages/Dashboard.vue`,
  - test contract: `tests/Feature/DashboardActivityChartTest.php`, `tests/Feature/DashboardDocumentCoverageTest.php`,
  - referensi baseline: `docs/process/DASHBOARD_ARCHITECTURE_BASELINE_SEKRETARIS_KECAMATAN_2026_02_26.md`.
- Acceptance criteria:
  - perubahan filter dashboard memakai helper visit yang terpusat dan tidak duplikasi query-building,
  - interaksi filter menggunakan partial reload terarah untuk prop dashboard yang memang dibutuhkan ulang,
  - kontrak `tahun_anggaran`, `mode`, `level`, `sub_level`, `section1_month`, `section2_group`, `section3_group` tetap sinkron,
  - tidak ada perubahan perilaku akses role/scope/menu atau kontrak PDF dashboard,
  - seluruh test dashboard yang terdampak tetap hijau.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [ ] T1. Dashboard punya satu jalur visit filter yang konsisten dan mudah diaudit.
- [ ] T2. Reload akibat perubahan filter dashboard hanya meminta prop yang relevan.
- [ ] T3. Ada bukti test bahwa partial reload tidak merusak kontrak data dashboard lintas scope.

## Langkah Eksekusi

- [ ] L0. Audit jalur visit dan state sinkronisasi saat ini.
  - petakan semua pemanggilan `router.get('/dashboard', ...)` di `Dashboard.vue`,
  - kelompokkan mana yang berasal dari action user langsung dan mana yang hanya sinkronisasi query.
- [ ] L1. Konsolidasi helper query dan helper visit dashboard.
  - buat satu helper untuk membangun query canonical,
  - buat satu helper untuk visit dashboard dengan opsi `preserveState`, `replace`, dan partial reload yang konsisten.
- [ ] L2. Terapkan partial reload pada interaction loop utama.
  - target awal: tombol `Terapkan Filter Chart`, perubahan filter global, dan watcher sync query,
  - definisikan daftar prop reload minimum untuk dashboard interaction (`dashboardStats`, `dashboardCharts`, `dashboardBlocks`, `dashboardContext`).
- [ ] L3. Audit payload yang tidak wajib ikut pada reload filter.
  - cek apakah sebagian prop bisa dibuat lazy/closure-backed tanpa mengubah hasil first load,
  - jika belum aman, catat item itu sebagai follow-up Wave 2 dan jangan dipaksa pada batch ini.
- [ ] L4. Stabilkan perilaku query sync.
  - cegah visit berulang saat URL sudah sinkron,
  - pastikan filter sekretaris dan filter umum memakai jalur helper yang sama.
- [ ] L5. Hardening test dan evidence.
  - tambah/ubah test yang relevan untuk memverifikasi partial reload tetap mengembalikan prop dashboard yang dibutuhkan,
  - catat file target dan hasil validasi pada log operasional bila batch implementasi dijalankan.

## Validasi

- [ ] V1. L1: targeted audit pada `DashboardController.php` dan `Dashboard.vue` memastikan query key canonical tidak drift.
- [ ] V2. L1: `php artisan test --filter=DashboardActivityChartTest --compact`
- [ ] V3. L1: `php artisan test --filter=DashboardDocumentCoverageTest --compact`
- [ ] V4. L2: jika test baru partial reload ditambahkan, jalankan file test dashboard spesifik yang menyentuh jalur itu.
- [ ] V5. L3: `php artisan test --compact` jika patch dashboard menyentuh shared payload atau query contract lintas role.

## Risiko

- Risiko 1: partial reload menghilangkan prop yang diam-diam masih dipakai `Dashboard.vue`.
- Risiko 2: watcher sync query memicu loop visit karena helper baru tidak mengunci kondisi idempotent dengan benar.
- Risiko 3: optimasi payload dashboard memecahkan jalur cetak PDF atau filter sekretaris yang memakai query paralel per section.

## Keputusan

- [x] K1: batch ini fokus pada mekanisme visit dan slimming payload, bukan redesign visual dashboard.
- [x] K2: tidak menambah endpoint JSON baru pada Wave 1 dashboard; jalur utama tetap Inertia partial reload.
- [x] K3: test dashboard yang sudah ada diperlakukan sebagai guard utama anti-regresi sebelum menambah test baru.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru tidak diperlukan pada batch ini karena boundary `Controller -> UseCase -> Repository` tetap utuh.
- [x] Jika batch berikutnya membutuhkan endpoint dashboard non-Inertia yang resmi, concern ini harus dievaluasi ulang untuk ADR.

## Fallback Plan

- Jika partial reload membuat prop dashboard hilang atau brittle:
  - rollback ke visit Inertia penuh untuk dashboard,
  - pertahankan helper query yang sudah dirapikan jika masih aman,
  - pindahkan slimming payload yang lebih agresif ke wave berikutnya setelah kontrak test ditambah.
- Jika watcher sync masih memicu loop:
  - bekukan optimasi watcher,
  - prioritaskan helper visit manual pada action user terlebih dahulu.

## Output Final

- [ ] O1. Ringkasan perubahan mekanisme visit/filter dashboard dan alasan teknisnya.
- [ ] O2. Daftar file target batch implementasi dashboard.
- [ ] O3. Hasil validasi dashboard targeted + residual risk yang tersisa.
