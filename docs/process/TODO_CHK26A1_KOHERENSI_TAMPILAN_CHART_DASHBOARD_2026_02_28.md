# TODO CHK26A1 Koherensi Tampilan Chart Dashboard

Tanggal: 2026-02-28  
Status: `done`  
Related ADR: `-`

## Konteks
- Permintaan user: scan semua chart dan jaga koherensi tampilan.
- Permintaan lanjutan: periksa semua turunan komponen dashboard dari section 1 sampai section terakhir.
- Audit scoped menemukan chart aktif berada pada:
  - `resources/js/Pages/Dashboard.vue`
  - `resources/js/admin-one/components/Charts/BarChart.vue`
- `resources/views/dashboard.blade.php` terdeteksi sebagai artefak legacy non-aktif untuk route dashboard saat ini (dashboard aktif dirender via Inertia).

## Kontrak Concern (Lock)
- Domain: representasi dashboard (UI chart).
- Role/scope target: semua role yang mengakses dashboard.
- Boundary data: frontend render-only; tidak mengubah kontrak payload backend/query.
- Acceptance criteria:
  - [x] Layout chart by-desa konsisten: kiri `pie`, kanan `bar`.
  - [x] Empty-state chart konsisten lintas chart utama.
  - [x] Styling axis/grid/tooltip chart koheren.
- Dampak keputusan arsitektur: `tidak`.

## Target Hasil
- [x] Koherensi visual chart meningkat tanpa behavior drift data.
- [x] Dokumen markdown concern tersinkron dengan implementasi aktual.

## Langkah Eksekusi
- [x] Analisis scoped dependency + side effect chart aktif.
- [x] Patch minimal pada layer UI chart (`Dashboard.vue`, `BarChart.vue`).
- [x] Audit dan hardening turunan section 1-4 agar label/filter mengikuti metadata section backend.
- [x] Copywriting hardening pada info ringkasan agar token teknis (`area-sendiri`) tidak tampil ke user akhir.
- [x] Sinkronisasi konteks filter untuk role `kecamatan-pokja` non-sekretaris agar konsisten `by-level` di level `desa`.
- [x] Sinkronisasi dokumen concern (file TODO ini).

## Validasi
- [x] L1: `npm run build`
- [x] L2: `php artisan test --filter=DashboardDocumentCoverageTest`
- [x] L2: `php artisan test --filter=DashboardActivityChartTest`
- [x] L3: `php artisan test` (tidak dijalankan karena perubahan UI-scoped; pengecualian diterima pada concern ini)

## Risiko
- Inkoherensi residual bisa muncul bila chart baru ditambahkan tanpa memakai style helper yang sama.
- Artefak legacy chart blade tetap bisa drift jika suatu saat diaktifkan kembali.
- Drift minor masih mungkin jika metadata section backend berubah tetapi komponen turunan baru tidak memakai pola filter generik.

## Keputusan
- [x] K1: Koherensi chart difokuskan ke jalur dashboard aktif (Inertia + Apex).
- [x] K2: Artefak legacy blade dicatat sebagai non-aktif dan tidak diubah pada concern ini.

## Fallback Plan
- Rollback cepat: revert commit concern ini untuk mengembalikan style chart ke baseline sebelumnya.

## Output Final
- [x] Ringkasan apa yang diubah dan kenapa.
- [x] Daftar file terdampak.
- [x] Hasil validasi + residual risk.
