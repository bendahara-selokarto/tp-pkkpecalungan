# TODO RGM26A1 Penataan Ulang Grouping Modul Berdasarkan Role User

Tanggal: 2026-03-07  
Status: `in-progress` (`state:runtime-mapping-updated-gap-modules-pending`)
Related ADR: `docs/adr/ADR_0009_ACTIVITY_GROUP_BOOK_ISOLATION.md`, `docs/adr/ADR_0010_PRESTASI_GROUP_BOOK_ISOLATION.md`, `docs/adr/ADR_0011_COMMON_BOOK_VISIBILITY_PER_ROLE_GROUP.md`, `docs/adr/ADR_0012_KECAMATAN_UNOWNED_MODULE_AUDIT_GROUP.md`

## Interpretasi Status Aktif

- Status aktif: `in-progress` (`state:runtime-mapping-updated-gap-modules-pending`).
- Audit historis: `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`; blocker: `Group Target`, `Mode Target`, scope rollout, out-of-scope.

## Konteks

- Pengelompokan modul tersebar pada `RoleMenuVisibilityService`, `EnsureModuleVisibility`, dan `DashboardLayout`.
- Klarifikasi owner 2026-05-02: tiap jabatan/unit kerja memiliki buku terpisah walaupun level dan area sama.
- Dampak: backend, middleware, payload Inertia, sidebar, test matrix, dan dokumen.

## Kontrak Concern (Lock)

- Domain: authorization visibility dan grouping menu domain berbasis role-scope (`desa`, `kecamatan`, `super-admin` bila relevan).
- Role/scope target: seluruh role operasional pada `RoleScopeMatrix` + role legacy yang masih aktif.
- Boundary data: `Controller -> UseCase/Action -> Repository -> Model` tetap; authority akses tetap backend (`Policy -> Scope Service -> module.visibility`).
- Acceptance criteria:
  - grouping modul terpetakan jelas per role-group,
  - buku umum lintas jabatan memakai boundary `level + area_id + tahun_anggaran + group`,
  - mode akses konsisten dengan justifikasi owner,
  - tidak ada data leak lintas level/scope/jabatan,
  - payload `menuGroupModes`/`moduleModes` sinkron dengan sidebar,
  - test matrix akses utama lulus.
- Dampak keputusan arsitektur: `ya` (menyentuh kontrak akses lintas concern).

## Input Owner (Wajib sebelum Implementasi)

- [x] Owner memilih modul prioritas dari baseline tabel berikut.
- [ ] Owner mengunci `Group Target` dan `Mode Target` pada sesi finalisasi concern.
- [x] Owner menyetujui batas scope rollout.
- Aturan isi tabel: jika `Group Target` dikosongkan, modul dianggap `tetap` (tidak diubah).

Konfirmasi owner ringkas:

- 2026-03-08: shortlist aman tahap-1 disetujui; scope awal `desa only`; `Mode Target` belum final.
- 2026-03-12: 5 modul Pokja II baru ikut masuk mapping.
- 2026-05-02: owner mengunci prinsip buku kegiatan terpisah per jabatan/unit kerja pada level wilayah yang sama.
- 2026-05-06: struktur aktual meniadakan owner modul `bendahara-tpk`; owner aktif hanya Sekretaris dan Pokja I-IV.
- 2026-05-02: isolasi `activities` per `group` selesai; targeted `60` test dan full `1265` test hijau.
- 2026-05-02: Buku Prestasi mengikuti kontrak Buku Kegiatan.
- 2026-05-02: isolasi `prestasi-lomba` per `group` selesai; targeted `56` test, build, dan full `1269` test hijau.
- 2026-05-02: `inventaris` dikunci tersedia lintas jabatan seperti `activities`; `prestasi-lomba` dan `bantuans` kemudian dipindah ke kontrak `buku bantu`.
- 2026-05-02: `inventaris` dan `bantuans` dikunci ikut isolasi data per `group`.
- 2026-05-02: `Data Kegiatan` dimiliki Sekretaris dan Pokja I-IV dengan output berbeda serta data terisolasi.
- 2026-05-02: implementasi hak CRUD dan isolasi `program-prioritas` per `group` selesai; `activities` tetap menjadi buku umum lintas jabatan dengan isolasi `group`.
- 2026-05-02: `buku bantu` dikunci: `prestasi-lomba`, `bantuans`, `kader-khusus`; dimiliki Sekretaris dan Pokja I-IV, data terisolasi.
- 2026-05-06: `kecamatan-sekretaris` mendapat submenu audit `Belum Ada Pemilik` (`read-only`, lihat ADR 0012).
- 2026-05-06: buku bantu unik Pokja dipetakan ke slug yang sudah ada; item tanpa slug khusus dicatat sebagai gap, bukan diberi owner salah.
- 2026-05-06: Sekretaris mendapat group `Penunjang Buku Wajib` (`penunjang-buku-wajib`) untuk item Data Umum dan Program Kerja; mode tetap `read-write`.
- 2026-05-06: `kader-khusus` ditutup sebagai gap isolasi CRUD; data kini memakai `level + area_id + tahun_anggaran + group`, sementara grafik/chart sengaja out-of-scope untuk pembahasan terpisah.
- 2026-05-06: seeder disesuaikan dengan kontrak role-group aktif; user semua jabatan tersedia per area, modul CRUD bersama punya contoh data lintas group, dan modul create yang belum terisi diberi contoh minimal.
- 2026-05-06: menu/sidebar/cetak untuk buku seragam lintas jabatan membawa `book_group`; backend menyimpan konteks group aktif per modul agar akun multi-role tidak melihat gabungan data Pokja lain saat membuka Pokja I. `inventaris` disinkronkan sebagai buku seragam Sekretaris dan Pokja I-IV.

### Draft Input Owner Aman (Hasil Analisa 2026-03-08)

Detail shortlist historis ada di `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.

Ringkasan target aktif:

- Sekretaris: `agenda-surat`, daftar anggota, `inventaris`, `activities`, `buku-notulen-rapat`, buku bantu seragam, serta group penunjang buku wajib berisi data umum dan `program-prioritas`.
- Pokja I-IV: `program-prioritas`, buku data kegiatan berbeda format, `activities`, buku bantu seragam termasuk `inventaris`, dan buku bantu unik sesuai slug yang tersedia.
- `bendahara-tpk` tidak memiliki owner modul pada struktur aktual ini.
- Gap modul tanpa slug khusus: grafik, IVA test, ASI eksklusif, konsultasi, kas Pokja III, dan sub-buku simulasi terpisah.
- `Belum Ada Pemilik` tidak memberi hak tulis; submenu audit tetap khusus kecamatan-sekretaris, sedangkan desa memakai mode baca legacy tanpa submenu.

Catatan runtime: `buku-tamu` umum tidak diberi owner Pokja karena kebutuhan aktual adalah buku tamu simulasi khusus.

## Target Hasil Aktif

- [x] Matrix buku kegiatan dan buku prestasi terdokumentasi sebagai `level + area_id + group`, bukan hanya `level + area_id`.
- [x] Matrix visibilitas `inventaris` mengikuti struktur buku seragam: Sekretaris dan Pokja I-IV.
- [x] Matrix visibilitas dan data `program-prioritas` mengikuti Sekretaris sebagai penunjang dan Pokja I-IV sebagai buku wajib.
- [x] Sidebar Sekretaris memisahkan Data Umum dan Program Kerja ke group `Penunjang Buku Wajib`.
- [x] Matrix data `bantuans` dan `inventaris` diperkuat menjadi `level + area_id + tahun_anggaran + group`.
- [x] Matrix `buku bantu` dikunci untuk `prestasi-lomba`, `bantuans`, `kader-khusus` pada Sekretaris dan Pokja I-IV.
- [x] Matrix CRUD `kader-khusus` mengikuti isolasi per role-group seperti buku bantu lain.
- [x] Seeder menyediakan minimal satu data contoh untuk create utama yang belum terwakili, tanpa menyentuh grafik/chart.
- [x] Matrix `Data Kegiatan` terdokumentasi sebagai data terisolasi per jabatan/group dengan output format berbeda per jabatan.
- [x] Submenu audit `Belum Ada Pemilik` tersedia untuk `kecamatan-sekretaris`.
- [ ] Rencana eksekusi end-to-end siap jalan tanpa ambigu (backend, middleware, UI, test, docs).

## Langkah Eksekusi Terstruktur (Tanpa Eksekusi Kode)

- [x] P0. Audit baseline `GROUP_MODULES`, `ROLE_GROUP_MODES`, `ROLE_MODULE_MODE_OVERRIDES`, middleware `module.visibility`, dan sidebar.
- [ ] P1. Freeze keputusan owner pada `Group Target`, `Mode Target`, scope rollout, dan out-of-scope.
- [ ] P2. Susun matrix kontrak baru `role -> group -> modules -> mode`, termasuk override khusus dan kontrak buku kegiatan/buku prestasi terpisah per group.
- [ ] P3. Rancang patch backend + frontend + test hardening dari `RoleMenuVisibilityService` sampai `DashboardLayout.vue`.
- [ ] P4. Jalankan doc-hardening + rollout checklist setelah keputusan owner terkunci.

## Validation Gate Plan

- [ ] G1. Matrix owner lengkap; targeted test plan siap untuk service, middleware, payload, sidebar.
- [x] G2. Full regression backend siap: `php artisan test --compact` hijau.
- [x] G3. Exit criteria: tidak ada mismatch payload/sidebar, privilege escalation, drift dokumen, atau data buku kegiatan/buku prestasi tercampur lintas group.

## Risiko

- Risiko utama: drift backend vs sidebar, privilege escalation, regresi role canonical, keputusan owner berubah tanpa freeze baseline, atau filter buku umum tidak memakai `group`.

## Keputusan

- [ ] K1: `RoleMenuVisibilityService` ditetapkan sebagai entry point utama refactor grouping.
- [ ] K2: authority akses tetap backend-first; frontend hanya consumer payload.
- [ ] K3: semua perubahan grouping wajib melewati gate test akses lintas scope.
- [x] K4: buku kegiatan wajib dipisahkan per role-group pada level dan area yang sama.
- [x] K6: buku prestasi wajib dipisahkan per role-group pada level dan area yang sama.
- [x] K7: buku inventaris tersedia pada Sekretaris dan Pokja I-IV, dengan isolasi `level + area_id + tahun_anggaran + group`.
- [x] K8: buku bantuan wajib terisolasi per role-group seperti buku kegiatan.
- [x] K9: `Data Kegiatan` wajib terisolasi per role-group untuk `sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, dan `pokja-iv`; format outputnya tidak boleh direuse lintas jabatan tanpa bukti autentik.
- [x] K10: buku program kerja wajib tersedia CRUD dan terisolasi per role-group seperti buku kegiatan.
- [x] K10A: Data Umum dan Program Kerja Sekretaris tampil pada group `penunjang-buku-wajib`, bukan bercampur di group utama Sekretaris PKK.
- [x] K11: kelompok `buku bantu` adalah `prestasi-lomba`, `bantuans`, dan `kader-khusus`, tersedia untuk sekretaris dan Pokja I-IV, tidak termasuk bendahara.
- [x] K12: data kelompok `buku bantu` wajib terisolasi per role-group.
- [x] K12A: seluruh modul CRUD bersama lintas role aktif wajib memakai isolasi role-group; chart/grafik bukan bagian kontrak ini.
- [x] K13: `belum-ada-pemilik` adalah group audit `read-only`, bukan owner input baru.
- [x] K14: modul aktual tanpa slug khusus tetap dicatat sebagai gap implementasi, bukan dipetakan ke slug yang salah.
- [ ] K5: implementasi baru hanya dimulai setelah tabel Input Owner terisi penuh.

## Keputusan Arsitektur (Jika Ada)

- [x] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jika uji akses gagal, rollback ke baseline mapping terakhir yang lulus; jika hanya sebagian modul bermasalah, rollback parsial per modul; jika keputusan owner konflik, kembali ke freeze tabel Input Owner.

## Pointer Audit Historis

- Detail historis `RGM26A1`: `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.
