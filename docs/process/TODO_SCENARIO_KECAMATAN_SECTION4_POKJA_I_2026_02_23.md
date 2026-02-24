# TODO Skenario Kecamatan Section 4 Pokja I 2026-02-23

Tanggal: 2026-02-23  
Status: `done`

## Konteks

- Dashboard sekretaris kecamatan saat ini sudah memiliki:
  - `section 1`: domain sekretaris.
  - `section 2`: pokja level kecamatan.
  - `section 3`: pokja level desa turunan.
- Kebutuhan baru: saat user di level kecamatan memilih `pokja-i` pada `section 3`, sistem menampilkan `section 4` untuk rincian sumber data per desa.
- Prioritas tetap: keterbacaan, label sumber jelas, dan anti data leak lintas kecamatan.

## Target Hasil

- `Section 4` muncul hanya pada kondisi:
  - user scope efektif `kecamatan`,
  - user role `kecamatan-sekretaris` (dan `admin-kecamatan` selama mode kompatibilitas legacy aktif),
  - filter `section3_group = pokja-i`.
- `Section 4` menampilkan rincian per desa:
  - nama desa,
  - total entri pokja I,
  - rincian sumber modul pokja I (minimal: `data-warga`, `data-kegiatan-warga`, `bkl`, `bkr`, `paar`).
- Saat filter `section3_group` selain `pokja-i`, `section 4` tidak dirender.

## Kontrak Data Section 4

- Payload backend menambahkan blok dengan metadata:
  - `section.key = sekretaris-section-4`.
  - `section.label = Section 4 - Rincian Pokja I per Desa`.
  - `section.depends_on = section3_group:pokja-i`.
  - `sources.source_scope = kecamatan`.
  - `sources.source_area_type = desa-turunan`.
  - `sources.source_group = pokja-i`.
- Data agregasi wajib lewat boundary repository (tidak query langsung di controller/UI).

## Langkah Eksekusi (Checklist)

- [x] `S4-1` Backend contract:
  - tambahkan rule trigger `section 4` di use case dashboard role-aware.
  - aktif hanya untuk `kecamatan` + `section3_group=pokja-i`.
- [x] `S4-2` Repository boundary:
  - tambah query agregasi pokja I per desa turunan kecamatan.
  - hasil minimal: `desa_id`, `desa_name`, `total`, `per_module`.
- [x] `S4-3` UI rendering:
  - render section 4 setelah section 3.
  - tampilkan chart/table per desa dan daftar sumber modul.
  - tambah empty-state khusus saat tidak ada data pokja I.
- [x] `S4-4` Query sync:
  - pastikan state URL section 3 tidak conflict dengan section 4.
  - section 4 tidak memiliki filter baru (mengikuti section 3).
- [x] `S4-5` Label clarity:
  - judul section + subjudul sumber menyebut eksplisit "Pokja I per Desa".
  - hindari label generik tanpa konteks asal data.

## Validasi Wajib

- [x] Feature test:
  - `kecamatan-sekretaris` + `section3_group=pokja-i` -> section 4 muncul.
  - `kecamatan-sekretaris` + `section3_group!=pokja-i` -> section 4 tidak muncul.
  - `desa-sekretaris` -> section 4 tidak muncul.
- [x] Feature test anti data leak:
  - section 4 hanya memuat desa turunan dari kecamatan user aktif.
- [x] Assertion metadata sumber:
  - `source_group`, `source_scope`, `source_area_type` konsisten.
- [x] Regression:
  - `php artisan test --filter=DashboardDocumentCoverageTest`
  - `php artisan test --filter=DashboardActivityChartTest`
  - `php artisan test`

## Risiko

- [ ] Risiko query berat karena breakdown per desa + per modul pada runtime.
- [ ] Risiko overload UI jika section 3 dan 4 sama-sama padat.
- [ ] Risiko drift filter jika state query section 3 tidak sinkron dengan kondisi trigger section 4.

## Mitigasi

- [ ] Gunakan agregasi repository + cache key kontekstual (scope, area, section3_group).
- [x] Tampilkan section 4 hanya saat trigger terpenuhi (`pokja-i`), bukan default.
- [ ] Gunakan tampilan ringkas default (top-level total per desa) dengan detail modul on-demand.

## Keputusan

- [x] Section 4 adalah skenario khusus kecamatan, bergantung pada pilihan `section3_group=pokja-i`.
- [x] Section 4 fokus pada rincian sumber data per desa untuk Pokja I.
- [x] Tidak menambah authority akses di frontend; kontrol akses tetap di backend.
- [x] Selama mode kompatibilitas legacy aktif, `admin-kecamatan` mengikuti aturan visibilitas sekretaris kecamatan.
