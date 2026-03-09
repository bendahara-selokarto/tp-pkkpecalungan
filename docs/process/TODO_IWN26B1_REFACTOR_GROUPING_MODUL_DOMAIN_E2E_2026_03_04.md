# TODO IWN26B1 Rencana Penataan Ulang Grouping Modul Domain End To End

Tanggal: 2026-03-04  
Status: `planned`  
Related ADR: `-`

## Dependensi Concern Aktif

- Concern parent ini tetap `planned` dan belum boleh dieksekusi sebagai regroup aktif sebelum input owner pada concern turunan `RGM26A1` benar-benar terkunci.
- Acuan child concern:
  - `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`
  - `docs/process/TODO_SPT26A1_PENATAAN_MENU_SIDEBAR_FLOW_PDF_TURUNAN_TANPA_FORM_INPUT_2026_03_09.md`
- Selama `RGM26A1` masih `planned` dengan `state:awaiting-owner-group-target`, concern ini diperlakukan sebagai planning-only dan tidak menganggap histori no-op `RGM26A1` sebagai baseline final implementasi baru.
- `SPT26A1` diperlakukan sebagai child concern IA menu/sidebar untuk flow PDF turunan; hasilnya menjadi input planning sebelum regroup menu dieksekusi lebih jauh.

## Konteks

- Perlu rencana penataan ulang grouping modul by domain yang berdampak langsung ke otorisasi backend, bukan sekadar perubahan tampilan menu.
- Source of truth akses aktif saat ini berada di `RoleMenuVisibilityService`, dengan enforcement lewat `scope.role` + `module.visibility`.
- Perubahan harus stabil end-to-end: kontrak akses, middleware, payload Inertia, menu UI, dashboard coverage, dan regression test harus konsisten.
- Dokumen ini adalah planning-only dan tidak mengeksekusi perubahan kode.

## Kontrak Concern (Lock)

- Domain: refactor grouping modul domain untuk penataan ulang ijin akses runtime.
- Role/scope target: `desa` dan `kecamatan` untuk role operasional + dampak observabilitas pada `super-admin` (read matrix).
- Boundary data:
  - backend contract: `RoleMenuVisibilityService`, `RoleScopeMatrix`, `EnsureScopeRole`, `EnsureModuleVisibility`, `routes/web.php`.
  - frontend consume-only: `HandleInertiaRequests`, `DashboardLayout`.
  - dashboard coupling: `BuildRoleAwareDashboardBlocksUseCase`, `DashboardGroupCoverageRepository`.
  - docs/process gate: `DOMAIN_CONTRACT_MATRIX`, `MONITORING_VISIBILITY_MODUL`, TODO concern aktif, ADR jika boundary berubah.
- Acceptance criteria:
  - daftar modul yang diubah disetujui owner per scope-role sebelum implementasi.
  - perubahan akses tervalidasi dari route entry sampai write-guard (create/edit/store/update/delete).
  - payload `menuGroupModes` dan `moduleModes` sinkron dengan perilaku middleware.
  - tidak ada drift `role` vs `scope` vs `areas.level`.
  - tidak ada mismatch baru antara menu group dan representasi dashboard coverage.
- Dampak keputusan arsitektur: `ya` (berpotensi, tergantung perubahan boundary enforcement).

## Ruang Input Owner (Wajib Diisi Sebelum Eksekusi)

### A. Daftar Modul yang Akan Diubah
| No | Modul slug | Scope target (`desa/kecamatan`) | Perubahan (`add/remove/regroup/change-mode`) | Mode saat ini | Mode target | Alasan bisnis owner |
| --- | --- | --- | --- | --- | --- | --- |
| 1 |  |  |  |  |  |  |
| 2 |  |  |  |  |  |  |
| 3 |  |  |  |  |  |  |

### B. Konfirmasi Owner per Dampak

- [ ] Owner menyetujui modul prioritas gelombang 1.
- [ ] Owner menyetujui modul high-risk yang harus pakai rollout bertahap.
- [ ] Owner menyetujui fallback jika hasil UAT tidak sesuai.
- [ ] Owner menyetujui daftar role yang terdampak langsung.

### C. Batasan Non-Negotiable

- [ ] `areas` tetap source of truth wilayah.
- [ ] Otorisasi tetap authority backend, bukan frontend.
- [ ] Tidak menambah coupling ke artefak legacy non-canonical.

## Target Hasil

- [ ] Tersusun blueprint perubahan akses per modul, role, scope, dan group domain.
- [ ] Tersusun urutan eksekusi end-to-end dari contract lock sampai validasi stabilitas.
- [ ] Tersusun test matrix wajib untuk anti-regresi akses, payload, middleware, dan dashboard sync.
- [ ] Tersusun rollback/fallback yang bisa dieksekusi cepat jika terjadi anomali akses.

## Flow Eksekusi Terstruktur (Rencana)

### Fase 0 - Intake dan Contract Lock

- [ ] Kumpulkan input owner pada tabel modul (bagian "Ruang Input Owner").
- [ ] Petakan perubahan ke kategori: `add`, `remove`, `regroup`, `change-mode`.
- [ ] Tetapkan scope concern: apakah menyentuh hanya matrix modul atau juga boundary scope-role.
- [ ] Kunci acceptance criteria + urutan rollout (wave 1, wave 2, dst).

### Fase 1 - Impact Mapping (Tanpa Patch)

- [ ] Petakan dampak backend pada:
  - `GROUP_MODULES`, `GROUPS_BY_SCOPE`, `ROLE_GROUP_MODES`, `ROLE_MODULE_MODE_OVERRIDES`.
- [ ] Petakan dampak enforcement pada:
  - `module.visibility`, `scope.role`, dan route prefix per scope.
- [ ] Petakan dampak frontend pada:
  - render group/menu item dari `menuGroupModes` dan `moduleModes`.
- [ ] Petakan dampak dashboard pada:
  - block by group dan coverage sinkronisasi menu.

### Fase 2 - Rencana Implementasi Bertahap

- [ ] Wave 1: perubahan modul prioritas rendah risiko (validasi cepat).
- [ ] Wave 2: perubahan modul lintas role/scope dengan risiko medium.
- [ ] Wave 3: perubahan high-risk (monitoring + fallback wajib siap).
- [ ] Tiap wave wajib menutup validasi L1-L2 sebelum lanjut wave berikutnya.

### Fase 3 - Stabilization Gate

- [ ] Gate A: role-scope-area coherence tetap valid.
- [ ] Gate B: middleware memblokir bypass write pada modul `read-only`.
- [ ] Gate C: UI tidak menampilkan modul yang tidak ada di `moduleModes`.
- [ ] Gate D: dashboard group-module tidak drift pasca perubahan.

### Fase 4 - Doc Hardening dan Auditability

- [ ] Sinkronkan istilah/matrix di dokumen canonical concern.
- [ ] Sinkronkan status checklist TODO concern dengan implementasi aktual.
- [ ] Catat evidence di `OPERATIONAL_VALIDATION_LOG` setelah eksekusi teknis.

## Validasi

- [ ] L1: syntax/lint/targeted test concern.
  - `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
  - `php artisan test tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php`
- [ ] L2: regression test concern terkait.
  - `php artisan test tests/Feature/ModuleVisibilityMiddlewareTest.php`
  - `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`
  - `php artisan test tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
  - `php artisan test tests/Unit/Dashboard/DashboardCoverageMenuSyncTest.php`
- [ ] L3: `php artisan test` jika perubahan lintas domain/akses/dashboard signifikan.

## Risiko

- Risiko 1: drift antara mapping backend dan grouping UI.
  - Mitigasi: backend contract jadi sumber utama, UI hanya consume payload.
- Risiko 2: kebocoran akses write pada role yang seharusnya read-only.
  - Mitigasi: verifikasi middleware + test write-intent route (`create/edit/store/update/delete`).
- Risiko 3: mismatch coverage dashboard setelah regroup modul.
  - Mitigasi: wajib jalankan `DashboardCoverageMenuSyncTest` dan audit non-coverage list.
- Risiko 4: perubahan besar sekaligus menyebabkan rollback sulit.
  - Mitigasi: rollout per wave + fallback per kombinasi role-scope-module.

## Keputusan

- [ ] K1: baseline refactor memakai pendekatan bertahap per wave (bukan big-bang).
- [ ] K2: perubahan group name/slug baru harus disetujui owner sebelum implementasi.
- [ ] K3: modul high-risk hanya dieksekusi setelah wave sebelumnya stabil.
- [ ] K4: dashboard audit dianggap mandatory, bukan optional.

## Keputusan Arsitektur (Jika Ada)

- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md` jika boundary enforcement berubah.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jalur fallback 1: rollback per kombinasi `scope x role x module` pada wave yang gagal.
- Jalur fallback 2: nonaktifkan override rollout terkelola untuk modul terkait jika diperlukan.
- Jalur fallback 3: freeze wave berikutnya sampai root cause terverifikasi dan regression hijau.

## Output Final

- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak (backend/frontend/test/docs).
- [ ] Hasil validasi + residual risk.
- [ ] Keputusan owner final (`go/hold/adjust`) per wave.
