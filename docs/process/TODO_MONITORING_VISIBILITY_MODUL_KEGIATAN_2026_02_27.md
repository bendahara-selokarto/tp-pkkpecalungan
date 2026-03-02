# TODO MVK26A1 Monitoring Visibility Modul Kegiatan
Tanggal: 2026-02-27  
Status: `in-progress`

## Konteks
- Status concern: `active` sebagai sub-scope modul kegiatan; baseline lintas semua modul dilanjutkan di `docs/process/TODO_MONITORING_VISIBILITY_SEMUA_MODUL_2026_02_27.md`.
- Dokumen acuan monitoring global: `docs/process/MONITORING_VISIBILITY_MODUL.md`.
- Modul target: `Buku Kegiatan` (`activities`) dan modul monitoring turunan kecamatan (`desa-activities`).
- Concern: mencegah mismatch antara menu frontend, visibilitas backend, dan otorisasi policy/scope.

## Target Hasil
- Kontrak visibility modul `activities` terkunci dan terdokumentasi per role.
- Trigger monitoring untuk perubahan `add/remove/change-mode` pada modul kegiatan aktif.
- Bukti validasi teknis (test visibility + auth + scoped data access) tersedia.

## Baseline Kontrak Visibility (Saat Ini)

### A. Modul `activities`
| Role | Scope valid | Mode |
| --- | --- | --- |
| `desa-sekretaris` | `desa` | `read-write` |
| `kecamatan-sekretaris` | `kecamatan` | `read-write` |
| `desa-pokja-i..iv` | `desa` | `read-write` |
| `kecamatan-pokja-i..iv` | `kecamatan` | `read-write` |
| `admin-desa` | `desa` | `read-write` |
| `admin-kecamatan` | `kecamatan` | `read-write` |
| `super-admin` | `desa` + `kecamatan` | `read-write` |

### B. Modul `desa-activities` (monitoring kecamatan)
| Role | Scope valid | Mode |
| --- | --- | --- |
| `kecamatan-sekretaris` | `kecamatan` | `read-only` |
| `admin-kecamatan` | `kecamatan` | `read-only` |
| `super-admin` | `kecamatan` | `read-write` |
| Role lain | `kecamatan` | tidak tersedia |

## Trigger Monitoring Khusus Modul Kegiatan
- [x] Penambahan role baru yang menerima `activities`.
- [x] Pengurangan role yang kehilangan `activities`.
- [x] Perubahan mode akses `activities` pada role existing.
- [x] Perubahan mode akses `desa-activities`.
- [x] Perubahan guard scoped filter `role group + level + area` pada query/list/detail kegiatan.
- [x] Perubahan kontrak menu frontend terhadap `auth.user.moduleModes.activities`.

## Langkah Eksekusi

### A. Source of Truth Audit
- [x] Audit `RoleMenuVisibilityService` untuk mapping group-module dan override role.
- [x] Audit `EnsureModuleVisibility` untuk enforcement mode akses modul.
- [x] Audit `ActivityScopeService` untuk guard scoped akses data kegiatan.
- [x] Audit `DashboardLayout.vue` untuk kontrak anti-mismatch menu vs backend module modes.

### B. Validasi Teknis
- [x] Jalankan test scoped CRUD + anti data leak modul kegiatan.
- [x] Jalankan test visibilitas menu/middleware/payload untuk `activities`.
- [x] Jalankan test policy kegiatan + kontrak frontend menu guard.

## Bukti Validasi (2026-02-27)
- [x] `php artisan test tests/Feature/DesaActivityTest.php tests/Feature/KecamatanActivityTest.php tests/Feature/KecamatanDesaActivityTest.php tests/Feature/ActivityPrintTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Unit/Policies/ActivityPolicyTest.php tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
  - hasil: `PASS` (`55` tests, `371` assertions).

## Risiko Residual
- Drift dokumen jika perubahan visibility dilakukan tanpa update log monitoring concern ini.
- Potensi regressi menu frontend jika guard `moduleModes` diubah tanpa test kontrak frontend.
- Potensi kebocoran data antar pokja jika scoped filter query kegiatan diubah tanpa regresi test anti data leak.

## Keputusan
- [x] Modul `activities` ditetapkan sebagai modul shared lintas role operasional pada scope valid masing-masing.
- [x] `kecamatan-pokja-i..iv` tetap memiliki `activities` dengan mode `read-write`.
- [x] Modul `desa-activities` tetap diposisikan sebagai monitoring kecamatan, bukan jalur mutasi data sumber untuk role pokja.

## Output Wajib Saat Update Berikutnya
- [ ] Catat diff mode sebelum/sesudah untuk role terdampak.
- [ ] Lampirkan perintah validasi dan hasil run terbaru.
- [ ] Sinkronkan dokumen canonical/process jika kontrak role-scope berubah.
