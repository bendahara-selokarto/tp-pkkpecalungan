# Pedoman Domain Utama 202-211 (Analisis + Rencana Implementasi Fullstack)

Sumber utama domain:
- https://pubhtml5.com/zsnqq/vjcf/basic/201-241

Catatan penting:
- Halaman target yang dianalisis: `202` s.d. `211`.
- Ekstraksi OCR dari sumber memiliki noise teks, tetapi struktur domain utamanya konsisten dan dapat dipetakan untuk kontrak implementasi.
- Canonical wilayah tetap `areas` (`level`, `area_id`, `created_by`) sebagai acuan otorisasi data.

Status eksekusi (2026-02-21):
- [x] `F1` Domain contract + terminology mapping.
- [x] `F2` Data layer scaffold (migration, model, repository boundary, config catalog awal).
- [x] `F3` Authorization & scope (ScopeService + Policy + Gate binding).
- [x] `F4` Use case/action.
- [x] `F5` HTTP layer.
- [x] `F6` Frontend Inertia.
- [x] `F7` PDF render.
- [x] `F8` Test matrix modul.
- [x] `F9` Operasional validation siklus modul.

## 1) Ringkasan Domain Halaman 202-211

## 1.1 Struktur Form dan Dokumen

- Halaman `202`:
  - Muncul format **Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana** (format naratif/laporan pengantar).
  - Ada poin naratif: dasar pelaksanaan, pendahuluan, maksud/tujuan, pelaksanaan, dokumentasi, penutup.
- Halaman `202-204`:
  - **Lampiran 3 - Laporan Manual Pilot Project**.
  - Bagian `A. Data Dukung` berisi indikator umum (jumlah penduduk, keluarga, kelompok PKK, kader, bank sampah, posko bencana, dst) dengan kolom periode tahunan/semester dan evaluasi.
- Halaman `205-210`:
  - Bagian `B. Data Pilot Project` berisi 9 klaster indikator:
    - I. Peduli Stunting
    - II. Menuju PHBS
    - III. Peduli KIA
    - IV. Siaga Kebakaran Lingkungan
    - V. Bencana Alam
    - VI. Peduli Lingkungan
    - VII. Menuju Keluarga Sehat Berkualitas
    - VIII. Menuju Keuangan Sehat
    - IX. Mewujudkan Keluarga Sehat Pasangan Usia Subur (PUS)
  - Tiap klaster berisi daftar indikator kuantitatif per periode.
- Halaman `210-211`:
  - Terdapat rujukan tautan formulir/laporan perkembangan per klaster (tautan pendek `s.id` per tema).
  - Konteks domain tetap pada **Bidang Kesehatan Keluarga dan Lingkungan - Pokja IV**.

## 1.2 Karakteristik Data

- Data bersifat:
  - **Periodik** (tahun + semester).
  - **Multi-indikator** (indikator umum + indikator klaster).
  - **Terstruktur tabular** + **narasi pelaksanaan**.
- Output yang dibutuhkan:
  - Laporan operasional (input/update) per area.
  - Generate PDF yang meniru urutan, pengelompokan, dan label pedoman.

## 2) Kontrak Domain Teknis (Usulan)

Slug modul usulan:
- `pilot-project-keluarga-sehat`

Boundary domain:
- `app/Domains/Wilayah/PilotProjectKeluargaSehat/*`

Entitas utama:
1. `pilot_project_keluarga_sehat_reports`
   - Menyimpan header laporan + narasi (dasar, pendahuluan, tujuan, pelaksanaan, penutup).
   - Field wajib domain: `level`, `area_id`, `created_by`.
   - Field periode: `tahun_awal`, `tahun_akhir` (default awal dari pedoman: 2021-2024).
2. `pilot_project_keluarga_sehat_values`
   - Menyimpan nilai indikator per periode.
   - Kunci indikator: `cluster_code` + `indicator_code`.
   - Kunci periode: `year`, `semester` (`I|II`).
   - Nilai utama: `value` (integer), `evaluation_note` (nullable).
3. `pilot_project_keluarga_sehat_indicator_catalog` (tanpa tabel, berbasis config/enum)
   - Master statis indikator dari pedoman untuk menjaga koherensi label.
   - Menghindari drift naming antar backend, frontend, dan PDF.

Aturan koherensi:
- Label indikator di UI/PDF harus berasal dari catalog statis yang sama.
- Nomor urut indikator di PDF tidak boleh dihitung dinamis dari data user.
- Struktur klaster I-IX harus tetap.

## 3) Rencana Implementasi Fullstack (End-to-End)

## F1 - Domain Contract & Mapping Pedoman

- Tambah kontrak baru ke:
  - `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
  - `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
- Tambah catatan sumber halaman:
  - `202-211` untuk modul pilot project Pokja IV.

Acceptance:
- Ada mapping: slug, label pedoman, daftar klaster, daftar indikator, dan aturan periode.

## F1.1 - Pemetaan Posisi Sidebar (Rencana UI Navigasi)

Target file:
- `resources/js/Layouts/DashboardLayout.vue`

Posisi menu yang direncanakan:
1. Scope `desa`
   - Tambah group baru setelah `Lampiran 4.15` dan sebelum `Program Pendukung`.
   - Group:
     - `key`: `pilotproject`
     - `label`: `Pilot Project Pokja IV`
     - `code`: `PP4`
   - Item:
     - `href`: `/desa/pilot-project-keluarga-sehat`
     - `label`: `Laporan Pilot Project Keluarga Sehat`
2. Scope `kecamatan`
   - Tambah group baru setelah `Lampiran 4.15` dan sebelum `Program Pendukung`.
   - Group:
     - `key`: `pilotproject`
     - `label`: `Pilot Project Pokja IV`
     - `code`: `PP4`
   - Item:
     - `href`: `/kecamatan/pilot-project-keluarga-sehat`
     - `label`: `Laporan Pilot Project Keluarga Sehat`

Acceptance:
- Menu muncul konsisten pada sidebar desa/kecamatan.
- Group aktif/open state mengikuti pola group lain di `DashboardLayout`.

## F2 - Data Layer (Migration + Model + Repository)

- Buat migration:
  - `create_pilot_project_keluarga_sehat_reports_table`
  - `create_pilot_project_keluarga_sehat_values_table`
- Buat model:
  - `PilotProjectKeluargaSehatReport`
  - `PilotProjectKeluargaSehatValue`
- Buat repository boundary:
  - interface + implementation untuk query scoped area dan query report.

Acceptance:
- Semua query domain melalui repository, tidak ada query liar di controller.

## F3 - Authorization & Scope

- Tambah policy:
  - `PilotProjectKeluargaSehatPolicy`
- Registrasi di `AppServiceProvider`.
- Terapkan `scope.role:{desa|kecamatan}` + policy `view/create/update/delete/print`.

Acceptance:
- Tidak ada data leak lintas area.
- Role/scope mismatch ditolak (stale metadata scenario).

## F4 - UseCase/Action

- Use case minimum:
  - `ListScopedPilotProjectKeluargaSehatUseCase`
  - `CreatePilotProjectKeluargaSehatAction`
  - `UpdatePilotProjectKeluargaSehatAction`
  - `DeletePilotProjectKeluargaSehatAction`
  - `BuildPilotProjectKeluargaSehatReportUseCase`

Acceptance:
- Business flow terkonsentrasi di use case/action, controller tetap tipis.

## F5 - HTTP Layer (Routes + Controllers + Requests)

- Tambah route `desa` dan `kecamatan`:
  - `Route::resource('pilot-project-keluarga-sehat', ...)`
  - `GET .../report/pdf`
- Request object:
  - validasi numeric/non-negative untuk indikator kuantitatif.
  - normalisasi semester (`I|II`).

Acceptance:
- CRUD + print route tersedia di dua scope area.

## F6 - Frontend Inertia (Vue)

- Halaman minimal:
  - `Index.vue` (list + status periode)
  - `Create.vue` / `Edit.vue` (input narasi + indikator)
  - `Show.vue` (preview sebelum print)
- Komponen reusable:
  - section klaster indikator I-IX.
  - matrix input per tahun/semester.

Acceptance:
- Input indikator konsisten dengan catalog statis.
- Flash/confirm mengikuti komponen Admin-One (sudah distandardkan).

## F7 - PDF Render (Konsistensi Pedoman)

- Tambah view:
  - `resources/views/pdf/pilot_project_keluarga_sehat_report.blade.php`
- Struktur PDF:
  - Halaman awal narasi laporan (format lampiran pengantar).
  - Halaman tabel `A. Data Dukung`.
  - Halaman tabel `B. Data Pilot Project` per klaster I-IX.
  - Lampirkan metadata cetak (area, printedBy, printedAt).
- Orientasi default:
  - `landscape`.

Acceptance:
- Header, urutan klaster, urutan indikator, dan label sesuai pedoman.

## F8 - Test Matrix (Mandatory)

Feature tests:
1. jalur sukses role/scope valid (desa, kecamatan).
2. role tidak valid ditolak.
3. mismatch role-area level ditolak (stale metadata).
4. report PDF scoped sesuai area.

Unit tests:
1. policy `view/update/delete/print`.
2. use case agregasi report (urutan klaster/indikator stabil).
3. serializer PDF context (judul, header, metadata cetak).

Regression:
- Tambah fixture baseline PDF khusus modul ini.
- Tambah assertion header/label klaster pada report print test.

## F9 - Operasional Validation

Per batch:
1. `php artisan route:list --name=pilot-project-keluarga-sehat`
2. `php artisan test --filter=PilotProjectKeluargaSehat`
3. `php artisan test`
4. verifikasi PDF sample `desa` + `kecamatan` terhadap pedoman.

## 4) Strategi Delivery (Disarankan)

Fase 1 (backend contract dulu):
- F1-F4

Fase 2 (UI + print):
- F5-F7

Fase 3 (hardening):
- F8-F9

Keuntungan:
- Risiko drift domain turun karena catalog indikator ditetapkan dari awal.
- PDF bisa distabilkan lebih cepat karena struktur tabel tidak dibangun ad-hoc.

## 5) Risiko & Mitigasi

Risiko utama:
- OCR pedoman mengandung noise pada beberapa label indikator.

Mitigasi:
- Bekukan `indicator_catalog` dari review manual final sebelum coding penuh.
- Simpan mismatch teks ke `docs/domain/DOMAIN_DEVIATION_LOG.md` jika ditemukan.

Risiko kedua:
- Volume field indikator cukup banyak dan rentan typo.

Mitigasi:
- Gunakan catalog berbasis enum/config + generator mapping kolom UI/PDF.
- Tambah test urutan indikator sebagai guard regresi.

## 6) Definition of Done

- Modul `pilot-project-keluarga-sehat` tersedia untuk `desa` dan `kecamatan`.
- Tidak ada query domain di luar repository boundary.
- Policy/scope aman dan lolos skenario stale metadata.
- Output PDF konsisten dengan pedoman halaman 202-211.
- Test matrix minimum terpenuhi dan `php artisan test` hijau.

