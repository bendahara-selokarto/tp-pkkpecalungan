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

## Progress Update 2026-03-02 (Section Simplification Lock)

- Konfirmasi owner: domain Pokja tetap `I-IV` (tidak ada `V`).
- Representasi section dashboard disederhanakan:
  - section aktif disisakan `sekretaris-section-1` dan `sekretaris-section-2`,
  - `sekretaris-section-3` dan `sekretaris-section-4` dinonaktifkan dari alur build block.
- Scope tetap dikunci:
  - role pokja hanya menampilkan cakupan group miliknya sendiri,
  - sekretaris desa tetap scoped ke desa sendiri,
  - sekretaris kecamatan tetap scoped ke kecamatan sendiri (dengan agregasi desa turunan tetap pada chart aktivitas section 1).
- Validasi:
  - `php artisan test tests/Feature/DashboardDocumentCoverageTest.php`
  - `php artisan test tests/Feature/DashboardActivityChartTest.php`
  - `php artisan test tests/Unit/Dashboard/DashboardGroupCoverageRepositoryTest.php`
  - hasil: `PASS`.

## Kontrak Aturan Dashboard Final (Lock 2026-03-02)

### A. Aturan Section

- Semua role dashboard hanya memakai `section 1` dan `section 2`.
- `section 3` dan `section 4` tidak dipakai pada build block aktif.
- Domain Pokja tetap `I-IV` (tidak ada `V`).

### B. Aturan Cakupan Data per Role

- `kecamatan-pokja`: hanya data Pokja miliknya sendiri.
- `desa-pokja`: hanya data Pokja miliknya sendiri.
- `desa-sekretaris`: hanya data desanya sendiri.
- `kecamatan-sekretaris`:
  - mencakup agregasi desa dalam kecamatannya sendiri,
  - tetap menampilkan 2 section dengan cakupan kecamatannya sendiri.

### C. Aturan Filter per Role

- Role desa (`desa-pokja`, `desa-sekretaris`): hanya filter `bulan`.
- Role pokja kecamatan (`kecamatan-pokja`): hanya filter `bulan`.
- Role sekretaris kecamatan (`kecamatan-sekretaris`): filter `bulan` + `level`.

### D. Aturan Visual Chart

- Chart kegiatan menggunakan `pie` dan wajib responsif terhadap filter aktif.
- Perbandingan `Jumlah Buku` vs `Buku Terisi` menggunakan chart `bar`.
- Warna bar dikunci kontras tinggi untuk keterbacaan:
  - `Jumlah Buku`: biru tegas (`#1d4ed8`)
  - `Buku Terisi`: ungu tegas (`#7e22ce`)

### E. Aturan Interaktivitas Ringkasan

- Kartu ringkasan (`Total Kegiatan`, `Bulan Ini`, `Jumlah Buku`, `Buku Terisi`, `Buku Belum Terisi`) wajib interaktif terhadap:
  - user aktif (role/scope),
  - filter aktif (minimal bulan; dan level jika role mendukung).
- Nilai ringkasan harus mengikuti dataset chart aktif, bukan angka statis yang terlepas dari filter.

### F. Guardrail Implementasi

- Frontend hanya render/consume; authority akses tetap backend (`Policy` + `Scope Service`).
- Perubahan visual/filter tidak boleh mengubah kontrak canonical `role/scope/area`.
- Jika ada penambahan menu/domain baru, wajib audit ulang KPI/chart coverage dashboard dan tulis justifikasi bila tidak ditampilkan.

