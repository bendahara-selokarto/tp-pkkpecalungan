# TODO RGM26A1 Penataan Ulang Grouping Modul Berdasarkan Role User

Tanggal: 2026-03-07  
Status: `planned` (`state:awaiting-owner-mode-target`)
Related ADR: `-`

## Interpretasi Status Aktif

- Status aktif concern ini adalah `planned` dengan `state:awaiting-owner-mode-target`.
- Audit trail no-op historis concern ini dipindahkan ke `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md` agar file aktif tetap tipis.
- Entri historis yang sempat menyebut concern `done` tidak lagi berlaku sebagai status aktif karena sudah disupersede oleh reset concern pada 2026-03-07.

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Pengelompokan modul untuk visibilitas role saat ini tersebar pada `RoleMenuVisibilityService`, `EnsureModuleVisibility`, dan `DashboardLayout.vue`.
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

- [x] Owner memilih modul prioritas dari baseline tabel berikut.
- [ ] Owner mengunci `Group Target` dan `Mode Target` pada sesi finalisasi concern.
- [x] Owner menyetujui batas scope rollout (`desa only`, `kecamatan only`, atau keduanya).
- Aturan isi tabel: jika `Group Target` dikosongkan, modul dianggap `tetap` (tidak diubah).

Konfirmasi owner 2026-03-08:

- Shortlist aman tahap-1 disetujui untuk dipindahkan ke tabel utama.
- Scope rollout owner dikunci ke `desa only` untuk tahap pertama.
- `Mode Target` belum dikunci; state aktif concern bergerak ke `awaiting-owner-mode-target`.

### Draft Input Owner Aman (Hasil Analisa 2026-03-08)

Shortlist aman tahap-1 sudah direfleksikan langsung pada kolom `Group Target` di tabel utama.
- Justifikasi naratif shortlist, modul yang sengaja ditunda, dan catatan blast radius dipindahkan ke `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.
- Scope rollout aman yang masih berlaku: `desa only`.

| No | Modul Slug | Group Saat Ini | Group Target |
| --- | --- | --- | --- |
| 1 | anggota-tim-penggerak | sekretaris-tpk |  |
| 2 | anggota-tim-penggerak-kader | sekretaris-tpk |  |
| 3 | kader-khusus | sekretaris-tpk |  |
| 4 | agenda-surat | sekretaris-tpk | sekretaris-tpk |
| 5 | buku-daftar-hadir | sekretaris-tpk | sekretaris-tpk |
| 6 | buku-tamu | sekretaris-tpk |  |
| 7 | buku-notulen-rapat | sekretaris-tpk | sekretaris-tpk |
| 8 | buku-keuangan | sekretaris-tpk |  |
| 9 | bantuans | sekretaris-tpk |  |
| 10 | inventaris | sekretaris-tpk |  |
| 11 | activities | sekretaris-tpk, pokja-i, pokja-ii, pokja-iii, pokja-iv |  |
| 12 | program-prioritas | sekretaris-tpk |  |
| 13 | anggota-pokja | sekretaris-tpk, pokja-i, pokja-ii, pokja-iii, pokja-iv |  |
| 14 | prestasi-lomba | sekretaris-tpk, pokja-i, pokja-ii, pokja-iii, pokja-iv |  |
| 15 | laporan-tahunan-pkk | sekretaris-tpk |  |
| 16 | data-warga | pokja-i | pokja-i |
| 17 | data-kegiatan-warga | pokja-i | pokja-i |
| 18 | bkl | pokja-i | pokja-i |
| 19 | bkr | pokja-i | pokja-i |
| 20 | paar | pokja-i | pokja-i |
| 21 | data-pelatihan-kader | pokja-ii |  |
| 22 | taman-bacaan | pokja-ii |  |
| 23 | koperasi | pokja-ii |  |
| 24 | kejar-paket | pokja-ii |  |
| 25 | data-keluarga | pokja-iii | pokja-iii |
| 26 | data-industri-rumah-tangga | pokja-iii |  |
| 27 | data-pemanfaatan-tanah-pekarangan-hatinya-pkk | pokja-iii |  |
| 28 | warung-pkk | pokja-iii |  |
| 29 | posyandu | pokja-iv | pokja-iv |
| 30 | simulasi-penyuluhan | pokja-iv | pokja-iv |
| 31 | catatan-keluarga | pokja-iv |  |
| 32 | pilot-project-naskah-pelaporan | pokja-iv |  |
| 33 | pilot-project-keluarga-sehat | pokja-iv |  |
| 34 | desa-activities | monitoring |  |
| 35 | desa-arsip | monitoring |  |

Catatan runtime ringkas:

- `inventaris` dan `buku-tamu` tetap dianggap modul override khusus dan belum masuk shortlist aman tahap-1.

## Target Hasil

- [ ] Baseline mapping lama vs mapping target terdokumentasi lengkap per role dan scope.
- [ ] Entry point backend final disepakati (`RoleMenuVisibilityService`) dengan daftar efek turunannya.
- [ ] Rencana eksekusi end-to-end siap jalan tanpa ambigu (backend, middleware, UI, test, docs).
- [ ] Strategi rollout + fallback disetujui owner sebelum patch kode dimulai.

## Langkah Eksekusi Terstruktur (Tanpa Eksekusi Kode)

- [ ] P0. Audit baseline `GROUP_MODULES`, `ROLE_GROUP_MODES`, `ROLE_MODULE_MODE_OVERRIDES`, middleware `module.visibility`, dan sidebar.
- [ ] P1. Freeze keputusan owner pada `Group Target`, `Mode Target`, scope rollout, dan out-of-scope.
- [ ] P2. Susun matrix kontrak baru `role -> group -> modules -> mode`, termasuk override khusus.
- [ ] P3. Rancang patch backend + frontend + test hardening dari `RoleMenuVisibilityService` sampai `DashboardLayout.vue`.
- [ ] P4. Jalankan doc-hardening + rollout checklist setelah keputusan owner terkunci.

## Validation Gate Plan

- [ ] G1. Matrix owner lengkap dan tidak ambigu.
- [ ] G2. Targeted plan siap: `RoleMenuVisibilityServiceTest`, `RoleMenuVisibilityGlobalContractTest`, `MenuVisibilityPayloadTest`, `ModuleVisibilityMiddlewareTest`, `DashboardLayoutMenuContractTest`.
- [ ] G3. Full regression siap: `php artisan test`, `npm run build`, dan smoke role-based navigation.
- [ ] G4. Exit criteria tetap: tidak ada mismatch payload/sidebar, privilege escalation, atau drift dokumen canonical.

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

## Pointer Audit Historis

- Audit trail no-op concern `RGM26A1` pada 2026-03-07 tetap tersedia di `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.
- Ringkasan historis:
  - owner sebelumnya belum mengisi `Group Target`, sehingga concern sempat ditutup sebagai no-op tervalidasi,
  - concern kemudian di-reset ke status aktif `planned` saat input owner baru diminta,
  - status aktif terbaru pada file ini tetap `planned` (`state:awaiting-owner-mode-target`).
