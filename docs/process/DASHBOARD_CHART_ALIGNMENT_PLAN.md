# Dashboard Chart Alignment Plan

Tujuan:
- Menyelaraskan dashboard agar merepresentasikan cakupan dokumen pedoman domain 4.9a-4.15, bukan hanya domain `activities`.

Status dokumen:
- Arsip rencana implementasi dashboard coverage lintas domain.
- Operasional validasi lanjutan dicatat di `docs/process/OPERATIONAL_VALIDATION_LOG.md`.

Status eksekusi (2026-02-20):
- `D1` selesai: kontrak backend `UseCase + Repository` sudah aktif.
- `D2` selesai: dashboard sudah menampilkan KPI dokumen + chart coverage.
- `D3` selesai: feature test scope dokumen (`desa`, `kecamatan`, stale metadata) sudah ditambahkan.
- `D4` selesai: unit test agregasi per modul/lampiran sudah ditambahkan.
- `D5` selesai: cache TTL pendek per `scope:area` diterapkan pada use case dashboard dokumen.

## 1) Analisa Kondisi Awal (Historis)

Temuan utama saat audit dimulai:
- Backend dashboard saat ini hanya menarik data dari `activities` melalui `DashboardActivityChartService`.
  - Referensi: `app/Services/DashboardActivityChartService.php`
- Frontend dashboard pada saat audit belum merender komponen chart; saat itu hanya menampilkan 4 widget angka.
  - Referensi: `resources/js/Pages/Dashboard.vue`
- Akibat kondisi awal tersebut, dashboard belum mewakili isi buku sekretaris secara keseluruhan (lampiran 4.9a-4.15).

Gap terhadap pedoman pada kondisi awal:
- Pedoman sudah mencakup banyak domain buku (anggota, surat, inventaris, keuangan, data warga, dst).
- Dashboard belum memberi visibilitas lintas domain (coverage per buku, buku kosong, progres input per lampiran).

## 2) Target State (Yang Harus Tercapai)

Dashboard menampilkan:
1. KPI lintas buku (scoped by user area):
- `total_buku_tracked` (jumlah modul yang dipantau, baseline: 19).
- `buku_terisi` (modul dengan jumlah data > 0).
- `buku_belum_terisi` (modul dengan jumlah data = 0).
- `total_entri_buku` (akumulasi seluruh entri lintas modul).

2. Chart representatif lintas domain:
- `chart_coverage_per_buku` (bar): jumlah entri per modul 4.9a-4.15.
- `chart_coverage_per_lampiran` (bar/pie): agregasi per kelompok lampiran.
- `chart_level_distribution` (stack/dual): distribusi data `desa` vs `kecamatan` untuk scope kecamatan.

3. Tetap menjaga guard auth/scope:
- User `desa`: hanya data buku desanya.
- User `kecamatan`: data kecamatan sendiri + desa turunan hanya pada modul yang memang mengizinkan turunan (contoh: `activities`).
- Metadata stale (`scope/area` mismatch): nol data / akses ditolak sesuai kebijakan existing.

## 3) Rencana Implementasi Bertahap

### Fase A - Kontrak Data Dashboard (Backend First)
- Buat use case baru: `BuildDashboardDocumentCoverageUseCase`.
- Buat repository interface + repository khusus dashboard coverage:
  - `DashboardDocumentCoverageRepositoryInterface`
  - `DashboardDocumentCoverageRepository`
- Output canonical Inertia:
  - `dashboardStats.documents`
  - `dashboardCharts.documents`
- Pertahankan `dashboardStats.activity` + `dashboardCharts.activity` untuk backward compatibility transisi.

Acceptance:
- Response dashboard memiliki blok data `documents` lintas modul.
- Tidak ada query domain di controller (tetap lewat use case/repository).

### Fase B - Rendering Chart di Frontend
- Update `resources/js/Pages/Dashboard.vue`:
  - Render chart coverage lintas buku.
  - Render chart agregasi lampiran.
  - Tetap tampilkan card KPI dokumen.
- Gunakan komponen chart existing (`resources/js/admin-one/components/Charts/LineChart.vue`) atau tambah komponen bar sederhana.

Acceptance:
- Chart benar-benar tampil di UI (desktop + mobile).
- Widget angka sinkron dengan data backend.

### Fase C - Test Matrix Dashboard Dokumen
- Feature test baru:
  - desa hanya lihat coverage buku desanya.
  - kecamatan lihat coverage sesuai kontrak modul (kecamatan sendiri, dan turunan hanya untuk modul yang mengizinkan).
  - metadata stale tidak bocor.
- Unit test use case/repository:
  - hitung modul terisi vs belum terisi benar.
  - agregasi lintas lampiran benar.

Acceptance:
- Seluruh test baru hijau.
- Tidak ada regresi test dashboard existing.

### Fase D - Performance Guard (Opsional, setelah Fase C stabil)
- Terapkan cache pendek per scope-area:
  - key: `dashboard:documents:{scope}:{area_id}`
  - TTL: 30-120 detik.
- Invalidasi saat create/update/delete domain buku terkait.

Acceptance:
- Response dashboard stabil pada data besar.
- Tidak ada stale signifikan di luar TTL.

## 4) Risiko & Mitigasi

Risiko:
- Query lintas banyak tabel bisa berat.
- Drift kontrak data dashboard antara backend dan frontend.
- Potensi kebocoran data lintas area jika agregasi tidak scoped.

Mitigasi:
- Gunakan repository boundary terpusat untuk semua query dashboard coverage.
- Tambahkan test scope ketat (termasuk stale metadata dua arah).
- Implementasi bertahap + cache setelah akurasi tervalidasi.

## 5) Deliverables

1. Kode:
- UseCase + Repository coverage dashboard.
- Update page dashboard untuk render chart lintas dokumen.
- Test feature + unit terkait.

2. Dokumen:
- Update `docs/security/REGRESSION_CHECKLIST_AUTH_SCOPE.md` bila ada skenario akses baru.
- Update `docs/process/OPERATIONAL_VALIDATION_LOG.md` setelah rollout.

## 6) Definition of Done

- Dashboard menampilkan chart yang merepresentasikan modul 4.9a-4.15.
- Data chart ter-scope sesuai role/scope/area.
- Test dashboard coverage hijau.
- Tidak ada regresi auth-scope.
