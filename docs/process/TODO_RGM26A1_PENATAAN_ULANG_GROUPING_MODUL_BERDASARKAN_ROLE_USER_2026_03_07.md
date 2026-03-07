# TODO RGM26A1 Penataan Ulang Grouping Modul Berdasarkan Role User

Tanggal: 2026-03-07  
Status: `planned`
Related ADR: `-`

## Aturan Pakai
- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks
- Saat ini pengelompokan modul untuk visibilitas role tersebar pada:
  - `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php` (mapping group, mode role, override modul),
  - `app/Http/Middleware/EnsureModuleVisibility.php` (enforcement akses runtime),
  - `resources/js/Layouts/DashboardLayout.vue` (struktur menu/sidebar yang dikonsumsi user).
- Owner perlu menata ulang grouping modul berdasarkan role user secara sadar kontrak, bukan sekadar perubahan label/UI.
- Perubahan ini berdampak lintas backend, middleware, payload Inertia, sidebar, test matrix, dan dokumen canonical.

## Kontrak Concern (Lock)
- Domain: authorization visibility dan grouping menu domain berbasis role-scope (`desa`, `kecamatan`, `super-admin` bila relevan).
- Role/scope target: seluruh role operasional pada `RoleScopeMatrix` + role legacy yang masih aktif.
- Boundary data: `Controller -> UseCase/Action -> Repository -> Model` tetap; authority akses tetap backend (`Policy -> Scope Service -> module.visibility`).
- Acceptance criteria:
  - grouping modul baru terpetakan jelas per role-group,
  - mode akses (`read-write`/`read-only`/hidden) konsisten dengan justifikasi owner,
  - tidak ada data leak lintas level/scope,
  - payload `menuGroupModes` dan `moduleModes` sinkron dengan sidebar render,
  - test matrix akses utama lulus penuh.
- Dampak keputusan arsitektur: `ya` (menyentuh kontrak akses lintas concern).

## Input Owner (Wajib sebelum Implementasi)
- [ ] Owner memilih modul prioritas dari baseline tabel berikut.
- [ ] Owner mengunci `Group Target` dan `Mode Target` pada sesi finalisasi concern.
- [ ] Owner menyetujui batas scope rollout (`desa only`, `kecamatan only`, atau keduanya).
- Aturan isi tabel: jika `Group Target` dikosongkan, modul dianggap `tetap` (tidak diubah).

| No | Modul Slug | Group Saat Ini | Group Target |
| --- | --- | --- | --- |
| 1 | anggota-tim-penggerak | sekretaris-tpk |  |
| 2 | anggota-tim-penggerak-kader | sekretaris-tpk |  |
| 3 | kader-khusus | sekretaris-tpk |  |
| 4 | agenda-surat | sekretaris-tpk |  |
| 5 | buku-daftar-hadir | sekretaris-tpk |  |
| 6 | buku-tamu | sekretaris-tpk |  |
| 7 | buku-notulen-rapat | sekretaris-tpk |  |
| 8 | buku-keuangan | sekretaris-tpk |  |
| 9 | bantuans | sekretaris-tpk |  |
| 10 | inventaris | sekretaris-tpk |  |
| 11 | activities | sekretaris-tpk, pokja-i, pokja-ii, pokja-iii, pokja-iv |  |
| 12 | program-prioritas | sekretaris-tpk |  |
| 13 | anggota-pokja | sekretaris-tpk, pokja-i, pokja-ii, pokja-iii, pokja-iv |  |
| 14 | prestasi-lomba | sekretaris-tpk, pokja-i, pokja-ii, pokja-iii, pokja-iv |  |
| 15 | laporan-tahunan-pkk | sekretaris-tpk |  |
| 16 | data-warga | pokja-i |  |
| 17 | data-kegiatan-warga | pokja-i |  |
| 18 | bkl | pokja-i |  |
| 19 | bkr | pokja-i |  |
| 20 | paar | pokja-i |  |
| 21 | data-pelatihan-kader | pokja-ii |  |
| 22 | taman-bacaan | pokja-ii |  |
| 23 | koperasi | pokja-ii |  |
| 24 | kejar-paket | pokja-ii |  |
| 25 | data-keluarga | pokja-iii |  |
| 26 | data-industri-rumah-tangga | pokja-iii |  |
| 27 | data-pemanfaatan-tanah-pekarangan-hatinya-pkk | pokja-iii |  |
| 28 | warung-pkk | pokja-iii |  |
| 29 | posyandu | pokja-iv |  |
| 30 | simulasi-penyuluhan | pokja-iv |  |
| 31 | catatan-keluarga | pokja-iv |  |
| 32 | pilot-project-naskah-pelaporan | pokja-iv |  |
| 33 | pilot-project-keluarga-sehat | pokja-iv |  |
| 34 | desa-activities | monitoring |  |
| 35 | desa-arsip | monitoring |  |

Catatan realitas runtime saat ini:
- `inventaris` dan `buku-tamu` memiliki override mode untuk `desa-pokja-i..iv` pada level role-module, meski baseline group asal ada di `sekretaris-tpk`.

## Target Hasil
- [ ] Baseline mapping lama vs mapping target terdokumentasi lengkap per role dan scope.
- [ ] Entry point backend final disepakati (`RoleMenuVisibilityService`) dengan daftar efek turunannya.
- [ ] Rencana eksekusi end-to-end siap jalan tanpa ambigu (backend, middleware, UI, test, docs).
- [ ] Strategi rollout + fallback disetujui owner sebelum patch kode dimulai.

## Langkah Eksekusi Terstruktur (Tanpa Eksekusi Kode)
- [ ] P0. Baseline audit:
  - inventarisasi `GROUP_MODULES`, `ROLE_GROUP_MODES`, `ROLE_MODULE_MODE_OVERRIDES`,
  - inventarisasi konsumsi di middleware `module.visibility` dan layout sidebar.
- [ ] P1. Freeze keputusan owner:
  - lock tabel modul target,
  - lock mode akses target per role-scope,
  - lock out-of-scope agar tidak terjadi creep.
- [ ] P2. Desain mapping kontrak baru:
  - susun matrix `role -> group -> modules -> mode`,
  - tandai modul dengan override khusus (pengecualian dari baseline group).
- [ ] P3. Rencana patch backend:
  - urutan ubah `RoleMenuVisibilityService`,
  - validasi dampak ke `EnsureModuleVisibility` + policy/scope service.
- [ ] P4. Rencana patch frontend:
  - sinkronisasi `DashboardLayout.vue` dengan payload backend,
  - pastikan anti-duplicate menu + guard item hidden tetap aktif.
- [ ] P5. Rencana test hardening:
  - unit kontrak service,
  - feature payload Inertia,
  - feature middleware (allow + deny matrix),
  - unit kontrak frontend layout menu.
- [ ] P6. Rencana doc-hardening:
  - update `docs/domain/DOMAIN_CONTRACT_MATRIX.md`,
  - catat siklus di `docs/process/OPERATIONAL_VALIDATION_LOG.md`,
  - update deviation log bila ada keputusan menyimpang dari baseline.
- [ ] P7. Rencana rollout:
  - urutan deploy aman,
  - smoke checklist pasca deploy,
  - sign-off owner.

## Validation Gate Plan
- [ ] G1. Konfirmasi matrix owner sudah lengkap dan tidak ambigu.
- [ ] G2. Targeted test plan disetujui sebelum patch:
  - `RoleMenuVisibilityServiceTest`,
  - `RoleMenuVisibilityGlobalContractTest`,
  - `MenuVisibilityPayloadTest`,
  - `ModuleVisibilityMiddlewareTest`,
  - `DashboardLayoutMenuContractTest`.
- [ ] G3. Full regression plan:
  - `php artisan test`,
  - `npm run build`,
  - smoke role-based navigation.
- [ ] G4. Exit criteria:
  - tidak ada mismatch payload vs sidebar,
  - tidak ada privilege escalation,
  - dokumen canonical sinkron.

## Risiko
- Risiko 1: drift kontrak antara mapping backend dan struktur sidebar frontend.
- Risiko 2: privilege escalation jika override role-module tidak dipetakan ulang dengan benar.
- Risiko 3: regressi role legacy (`admin-*`) bila compatibility mapping tidak diuji.
- Risiko 4: keputusan owner berubah di tengah eksekusi tanpa freeze baseline.

## Keputusan
- [ ] K1: `RoleMenuVisibilityService` ditetapkan sebagai entry point utama refactor grouping.
- [ ] K2: authority akses tetap backend-first; frontend hanya consumer payload.
- [ ] K3: semua perubahan grouping wajib melewati gate test akses lintas scope.
- [ ] K4: implementasi baru hanya dimulai setelah tabel Input Owner terisi penuh.

## Keputusan Arsitektur (Jika Ada)
- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan
- Jika uji akses gagal, rollback ke baseline mapping terakhir yang lulus test penuh.
- Jika hanya sebagian modul bermasalah, lakukan rollback parsial per modul dengan guard matrix tetap aktif.
- Jika ada konflik keputusan owner, hentikan patch dan kembali ke tahap freeze tabel Input Owner.

## Output Final
- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak backend, frontend, test, dan dokumentasi.
- [ ] Hasil validasi otomatis + manual dan residual risk.
