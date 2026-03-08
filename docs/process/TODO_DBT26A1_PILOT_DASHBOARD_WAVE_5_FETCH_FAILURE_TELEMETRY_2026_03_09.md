# TODO DBT26A1 Pilot Dashboard Wave 5 Fetch Failure Telemetry

Tanggal: 2026-03-09  
Status: `done` (`state:full-suite-and-build-validated`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Concern ini adalah child concern `SPA26A1` untuk `Wave 5` hardening observability dan regresi.
- Setelah `DBJ26A1`, dashboard memiliki fetch manual baru pada widget rincian block per-desa.
- Fallback UI lokal sudah ada, tetapi kegagalan fetch belum masuk ke jalur telemetry runtime UI yang sudah dipakai aplikasi.

## Kontrak Concern (Lock)

- Domain: dashboard async fetch observability concern.
- Role/scope target: semua user dashboard yang mengakses widget detail on-expand yang didukung.
- Boundary data:
  - `resources/js/app.js`,
  - `resources/js/Pages/Dashboard.vue`,
  - `tests/Feature/UiRuntimeErrorLogTest.php`,
  - `tests/Feature/DashboardBlockDetailWidgetTest.php`.
- Acceptance criteria:
  - fetch failure detail widget mengirim telemetry ke jalur runtime error yang sudah ada,
  - fallback UI user tetap non-blocking,
  - tidak ada endpoint telemetry baru untuk concern ini.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] T1. Jalur fetch failure widget dashboard masuk ke telemetry runtime existing.
- [x] T2. Source telemetry sempit dan spesifik per widget dashboard.
- [x] T3. Validasi regression concern + build/full suite tercatat penuh.

## Langkah Eksekusi

- [x] L0. Analisis scoped dependency pada runtime telemetry existing.
- [x] L1. Patch helper global runtime error agar bisa dipakai concern fetch async.
- [x] L2. Patch dashboard widget fetch failure agar memancarkan telemetry terarah.
- [x] L3. Sinkronisasi roadmap + registry + playbook + validation log.

## Validasi

- [x] V1. `php artisan test tests/Feature/UiRuntimeErrorLogTest.php --compact`
- [x] V2. `php artisan test tests/Feature/DashboardBlockDetailWidgetTest.php --compact`
- [x] V3. `npm run build`
- [x] V4. `php artisan test --compact`

## Risiko

- Risiko 1: telemetry terlalu bising jika source tidak spesifik atau failure berulang tanpa guard.
- Risiko 2: helper global runtime error dipakai tidak konsisten oleh concern async lain.
- Risiko 3: fallback UI berubah menjadi bergantung pada telemetry jika implementasi tidak hati-hati.

## Keputusan

- [x] K1: concern memakai endpoint runtime error existing, bukan route baru.
- [x] K2: telemetry source harus menyebut concern fetch dashboard, bukan label generic.
- [x] K3: UX fallback lokal tetap dipertahankan walau telemetry gagal.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru tidak diperlukan; concern ini masih berada dalam jalur observability UI existing.
- [x] Pattern reusable dicatat ke playbook sebagai fetch failure runtime telemetry hook.

## Fallback Plan

- Jika telemetry fetch failure memicu noise atau regresi:
  - rollback pemanggilan helper telemetry dari widget,
  - pertahankan fallback lokal,
  - evaluasi ulang kontrak source dan rate limit telemetry sebelum reaktifasi.

## Output Final

- [x] O1. Ringkasan jalur observability fetch failure dashboard.
- [x] O2. Daftar file terdampak.
- [x] O3. Hasil validasi concern sampai full suite/build.
