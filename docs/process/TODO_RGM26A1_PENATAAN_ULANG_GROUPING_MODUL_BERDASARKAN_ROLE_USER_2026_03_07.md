# TODO RGM26A1 Penataan Ulang Grouping Modul Berdasarkan Role User

Tanggal: 2026-03-07  
Status: `in-progress` (`state:runtime-mapping-updated-gap-modules-pending`)
Related ADR: `docs/adr/ADR_0009_ACTIVITY_GROUP_BOOK_ISOLATION.md`, `docs/adr/ADR_0010_PRESTASI_GROUP_BOOK_ISOLATION.md`, `docs/adr/ADR_0011_COMMON_BOOK_VISIBILITY_PER_ROLE_GROUP.md`, `docs/adr/ADR_0012_KECAMATAN_UNOWNED_MODULE_AUDIT_GROUP.md`

## Konteks

- Pengelompokan modul tersebar pada `RoleMenuVisibilityService`, `EnsureModuleVisibility`, dan `DashboardLayout`.
- Klarifikasi owner 2026-05-02: tiap jabatan/unit kerja memiliki buku terpisah walaupun level dan area sama.
- Dampak: backend, middleware, payload Inertia, sidebar, test, dan dokumen.

## Kontrak Concern (Lock)

- Domain: authorization visibility dan grouping menu domain berbasis role-scope (`desa`, `kecamatan`, `super-admin` bila relevan).
- Role/scope target: seluruh role operasional pada `RoleScopeMatrix`.
- Boundary data: `Controller -> UseCase/Action -> Repository -> Model` tetap; authority akses tetap backend (`Policy -> Scope Service -> module.visibility`).
- Acceptance criteria:
  - grouping modul jelas per role-group,
  - buku umum lintas jabatan memakai boundary `level + area_id + tahun_anggaran + group`,
  - mode akses konsisten,
  - tidak ada data leak lintas level/scope/jabatan,
  - payload `menuGroupModes`/`moduleModes` sinkron dengan sidebar,
  - test matrix akses utama lulus.
- Aturan perubahan input bisnis/non-teknis:
  - setiap informasi baru dari divisi bisnis/non-teknis wajib diterjemahkan dulu menjadi dokumen pedoman paling kuat yang relevan sebelum implementasi kode,
  - jika input baru mengubah logika lama, update dokumen terdampak adalah bagian dari definisi selesai concern, bukan langkah opsional.
- Dampak keputusan arsitektur: `ya` (menyentuh kontrak akses lintas concern).

## Input Owner (Wajib sebelum Implementasi)

- [x] Owner memilih modul prioritas dari baseline tabel berikut.
- [ ] Owner mengunci `Group Target` dan `Mode Target`.
- [x] Owner menyetujui batas scope rollout.
- Aturan isi tabel: jika `Group Target` dikosongkan, modul dianggap `tetap` (tidak diubah).

Konfirmasi owner ringkas:

- 2026-03-08 s.d. 2026-05-06: histori dan evidence detail ada di `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.
- 2026-05-06: owner aktif hanya Sekretaris dan Pokja I-IV; `bendahara-tpk` tidak menjadi pemilik modul struktur administrasi aktual.
- 2026-05-06: modul bersama lintas jabatan wajib membawa `book_group` dan menyimpan isolasi `level + area_id + tahun_anggaran + group`.
- 2026-05-20: kategori umum dikunci menjadi `Buku Wajib` dan `Buku Pembantu`; khusus Sekretaris ada `Penunjang Buku Wajib`.
- 2026-05-20: `Buku Inventaris` dipindahkan ke `Buku Wajib` Sekretaris, bukan buku pembantu seragam.
- 2026-05-20: buku pembantu bersama untuk semua role aktif hanya `prestasi-lomba`, `bantuans`, dan `kader-khusus`.

### Draft Input Owner Aman (Hasil Analisa 2026-03-08)

Detail shortlist historis ada di `docs/process/logs/OPERATIONAL_VALIDATION_LOG_2026_Q1.md`.

Ringkasan target aktif:

- Sekretaris: buku wajib (`agenda-surat`, daftar anggota TP PKK, `inventaris`, `activities`, `buku-notulen-rapat`), buku pembantu bersama, dan `Penunjang Buku Wajib`.
- Pokja I-IV: buku pembantu bersama dan buku pembantu spesifik Pokja sesuai matrix `BKADM1`.
- Gap modul tanpa slug khusus: grafik, IVA test, ASI eksklusif, konsultasi, kas Pokja III, sub-buku simulasi terpisah, dan item Pokja lain yang belum punya route/model khusus.
- `Belum Ada Pemilik` tidak memberi hak tulis; submenu audit tetap khusus kecamatan-sekretaris, sedangkan desa memakai mode baca legacy tanpa submenu.

Catatan runtime: `buku-tamu` umum tidak diberi owner Pokja karena kebutuhan aktual adalah buku tamu simulasi khusus.

## Target Hasil Aktif

- [x] Matrix buku kegiatan dan buku prestasi terdokumentasi sebagai `level + area_id + group`, bukan hanya `level + area_id`.
- [ ] Matrix visibilitas `inventaris` direvisi: buku wajib Sekretaris; Pokja III hanya jika slug/format spesifik dikunci.
- [x] Matrix visibilitas dan data `program-prioritas` mengikuti Sekretaris sebagai penunjang dan Pokja I-IV sebagai buku wajib.
- [x] Sidebar Sekretaris memisahkan Data Umum dan Program Kerja ke group `Penunjang Buku Wajib`.
- [ ] Matrix data buku pembantu bersama diperkuat menjadi `level + area_id + tahun_anggaran + group` tanpa `inventaris`.
- [x] Matrix `buku bantu` dikunci untuk `prestasi-lomba`, `bantuans`, `kader-khusus` pada Sekretaris dan Pokja I-IV.
- [x] Matrix CRUD `kader-khusus` mengikuti isolasi per role-group seperti buku bantu lain.
- [x] Seeder menyediakan minimal satu data contoh untuk create utama yang belum terwakili, tanpa menyentuh grafik/chart.
- [x] Matrix `Data Kegiatan` terdokumentasi sebagai data terisolasi per jabatan/group dengan output format berbeda per jabatan.
- [x] Submenu audit `Belum Ada Pemilik` tersedia untuk `kecamatan-sekretaris`.
- [x] Policy permission membaca assignment role aktual dan konstanta canonical repo.
- [x] Rencana eksekusi end-to-end awal dibuat pada `docs/process/TODO_BKADM1_PLANNING_IMPLEMENTASI_KATEGORI_BUKU_ADMINISTRASI_2026_05_20.md`.

## Langkah Eksekusi Terstruktur (Tanpa Eksekusi Kode)

- [x] P0. Audit baseline `GROUP_MODULES`, `ROLE_GROUP_MODES`, `ROLE_MODULE_MODE_OVERRIDES`, middleware `module.visibility`, dan sidebar.
- [ ] P1. Freeze keputusan owner pada `Group Target`, `Mode Target`, scope rollout, dan out-of-scope.
- [ ] P2. Susun matrix kontrak baru `role -> group -> modules -> mode`, termasuk override khusus dan kontrak buku kegiatan/buku prestasi terpisah per group.
- [ ] P3. Rancang patch backend + frontend + test hardening dari `RoleMenuVisibilityService` sampai `DashboardLayout.vue`.
- [ ] P4. Jalankan doc-hardening + rollout checklist setelah keputusan owner terkunci.

## Validation Gate Plan

- [ ] G1. Matrix owner lengkap; targeted test plan siap untuk service, middleware, payload, sidebar.
- [x] G1A. Lint syntax patch authorization/grouping hijau; regression test PHP tertahan blocker runner `mbstring` yang belum tersedia.
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
- [x] K7: revisi 2026-05-20 menetapkan buku inventaris sebagai buku wajib Sekretaris; mapping lintas Pokja sebelumnya disupersede.
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
