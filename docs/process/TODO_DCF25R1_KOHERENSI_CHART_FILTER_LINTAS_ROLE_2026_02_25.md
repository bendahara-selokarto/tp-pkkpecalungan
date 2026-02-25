# TODO DCF25R1 Koherensi Chart dan Filter Lintas Role Pasca Apex 2026-02-25

Tanggal: 2026-02-25  
Status: `done` (`experimental-ui-only`, non-final, rolling)

## Force Latest Marker

- Todo Code: `DCF25R1`
- Marker: `DASH-CHART-FILTER-COHERENCE-2026-02-25-R1`
- Dokumen ini adalah acuan tunggal aktif untuk concern: `pie/bar`, `filter bulan`, dan sinkron query chart lintas role.
- Jika ada analisa memakai dokumen lama sebagai acuan final concern ini, analisa tersebut dianggap usang.

## Konteks

- Gap koherensi lintas role mulai terlihat setelah integrasi `vue3-apexcharts` pada dashboard.
- Perubahan chart terpusat ke jalur role tertentu lebih dulu, sementara role lain mengikuti belakangan.
- Concern aktif ini dibatasi pada layer UI chart/filter (`resources/js`) tanpa perubahan kontrak otorisasi backend.

## Hasil Audit Awal (Phase-1)

| Concern | Kondisi Saat Ini | Gap |
| --- | --- | --- |
| Renderer `pie` | `pie` dipakai saat blok punya `charts.by_desa.*` | Belum ada aturan kepadatan data (desa banyak => label padat). |
| Filter bulan (`section1_month`) | Sudah terkirim di query dan dipakai di blok dengan data by-desa | Kontrak visibilitas filter per role/per-blok belum dikunci eksplisit. |
| Query sinkron | Query utama: `mode`, `level`, `sub_level`, `section1_month`, `section2_group`, `section3_group` | Belum ada rule prioritas saat kombinasi filter tidak relevan pada blok tertentu. |
| Konsistensi antar role | Renderer by-desa sudah digeneralisasi berbasis payload | Belum ada matriks UX final lintas role untuk jenis chart per kondisi data. |
| Empty state chart | Sudah ada empty-state per chart utama | Belum ada standar tunggal nada/teks untuk seluruh varian chart. |

## Target Hasil

- Keputusan visual chart lintas role dikunci berbasis rule data, bukan branch role.
- Filter bulan jelas: kapan tampil, blok mana yang terdampak, dan bagaimana perilaku query saat tidak relevan.
- Renderer chart konsisten untuk semua role non `super-admin` pada concern data sejenis.
- Tidak ada perubahan authority akses backend; UI tetap renderer dari payload backend.

## Scope

- In scope:
  - `resources/js/Pages/Dashboard.vue` (kontrak render chart/filter),
  - TODO sinkronisasi concern dashboard terkait,
  - regression test concern dashboard yang sudah ada (tanpa tambah E2E baru pada fase awal).
- Out of scope:
  - perubahan policy/middleware/repository untuk akses,
  - perubahan matrix role backend di luar kebutuhan payload chart.

## Langkah Eksekusi (Checklist)

- [x] `C1` Lock rule pemilihan tipe chart by-desa:
  - tetapkan threshold kapan `pie` dipakai dan kapan fallback ke `bar`.
- [x] `C2` Lock rule filter bulan:
  - tetapkan visibilitas filter bulan berbasis blok data (bukan nama role),
  - tetapkan perilaku query saat blok tidak memakai month filter.
- [x] `C3` Hardening query synchronization:
  - normalisasi query tak relevan agar tidak memunculkan state ambigu.
- [x] `C4` Standardisasi copy chart-state:
  - samakan style helper/empty-state untuk semua varian chart.
- [x] `C5` Validasi regresi lintas role:
  - `DashboardDocumentCoverageTest`,
  - `DashboardActivityChartTest`,
  - build frontend.

## Validasi Wajib

- [x] `npm run build`
- [x] `php artisan test --filter=DashboardDocumentCoverageTest`
- [x] `php artisan test --filter=DashboardActivityChartTest`
- [x] Smoke check query URL:
  - `mode`, `level`, `sub_level`, `section1_month`, `section2_group`, `section3_group`

## Risiko

- Drift UX jika rule chart ditetapkan hanya untuk satu skenario data.
- Kebingungan user jika filter bulan tampil tanpa dampak visual yang jelas.
- Regresi lintas role jika sinkron query tidak diuji dengan matrix payload berbeda.

## Mitigasi

- Kunci rule berbasis payload chart, bukan branch role.
- Pastikan semua perubahan chart/filter melalui regression test dashboard.
- Dokumentasikan rule final di TODO ini dan tandai dokumen lama sebagai historis.

## Keputusan (To Lock)

- [x] Concern chart/filter lintas role memakai dokumen ini sebagai acuan tunggal.
- [x] Perubahan fase ini tetap UI-only eksperimen.
- [x] Perubahan backend/E2E dibuka hanya jika rule UI sudah stabil dan ada concern terpisah.
