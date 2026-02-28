# Monitoring Visibility Modul

Tanggal efektif: 2026-02-27  
Status: `active`  
Tujuan: memastikan setiap penambahan, pengurangan, atau perubahan visibility modul selalu terdeteksi, tervalidasi, dan terdokumentasi.

## 1. Scope Monitoring

Dokumen ini wajib dipakai untuk seluruh perubahan visibility yang menyentuh:
- Mapping role/scope/group/module.
- Mode akses modul (`read-only` / `read-write` / tidak tersedia).
- Guard backend (`policy`, `scope service`, `module.visibility` middleware).
- Kontrak menu frontend terhadap `auth.user.moduleModes`.

## 2. Trigger Wajib

Jalankan monitoring ini setiap ada:
1. Penambahan modul baru ke role/group.
2. Pengurangan modul dari role/group.
3. Perubahan mode akses modul pada role/group.
4. Perubahan override modul per role.
5. Perubahan slug menu yang berdampak pada `moduleModes`.
6. Perubahan guard query scoped (contoh: filter role-group + level + area).

## 3. Source of Truth yang Harus Diaudit

- `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- `app/Http/Middleware/EnsureModuleVisibility.php`
- `app/Domains/Wilayah/Activities/Services/ActivityScopeService.php` (jika concern menyentuh `activities`)
- `app/Policies/ArsipDocumentPolicy.php` (jika concern menyentuh `desa-arsip`)
- `resources/js/Layouts/DashboardLayout.vue`
- Test visibility:
  - `tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
  - `tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php`
  - `tests/Feature/ModuleVisibilityMiddlewareTest.php`
  - `tests/Feature/MenuVisibilityPayloadTest.php`
  - `tests/Feature/KecamatanDesaArsipTest.php` (jika concern menyentuh `desa-arsip`)
  - `tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
- Dokumen canonical/process concern:
  - `docs/domain/dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md`
  - TODO concern aktif di `docs/process/`

## 4. Checklist Eksekusi (Mandatory)

### A. Analisis
- [ ] Identifikasi perubahan visibility: `add` / `remove` / `change-mode`.
- [ ] Tetapkan role terdampak (`desa/kecamatan`, sekretaris/pokja/admin/super-admin).
- [ ] Tetapkan dampak scope-area-level (anti mismatch `role` vs `scope` vs `areas.level`).

### B. Implementasi
- [ ] Patch minimal pada source of truth backend (utamakan `RoleMenuVisibilityService`).
- [ ] Pastikan frontend hanya menampilkan menu berdasarkan `auth.user.moduleModes`.
- [ ] Pastikan tidak ada bypass authority dari frontend.

### C. Validasi Teknis
- [ ] Jalankan test targeted visibility:
  - `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
  - `php artisan test tests/Unit/Services/RoleMenuVisibilityGlobalContractTest.php`
  - `php artisan test tests/Feature/ModuleVisibilityMiddlewareTest.php`
  - `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`
  - `php artisan test tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
- [ ] Jika perubahan lintas modul/auth: jalankan `php artisan test`.

### D. Doc-Hardening
- [ ] Sinkronkan dokumen canonical/process concern yang terdampak.
- [ ] Catat keputusan kontrak yang dikunci (role, scope, mode, slug modul).
- [ ] Tambah bukti eksekusi ke `docs/process/OPERATIONAL_VALIDATION_LOG.md`.

## 5. Output Wajib per Perubahan

Setiap perubahan visibility wajib menghasilkan:
1. Daftar role terdampak.
2. Daftar modul terdampak + mode sebelum/sesudah.
3. File yang diubah (backend, frontend, tests, docs).
4. Hasil validasi command (targeted + full suite jika relevan).
5. Risiko residual (jika ada) dan rencana mitigasi.

## 6. Template Log Eksekusi

Gunakan template ini saat menambah entri ke `OPERATIONAL_VALIDATION_LOG.md`:

```md
## Siklus Monitoring Visibility Modul: YYYY-MM-DD

Ruang lingkup:
- Concern:
- Trigger: add/remove/change-mode/override/slug/filter

Perubahan kontrak:
- Role terdampak:
- Modul terdampak:
- Mode sebelum -> sesudah:

Artefak:
- [file-1]
- [file-2]

Perintah validasi:
- `php artisan test ...`
  - hasil:
- `php artisan test` (jika relevan)
  - hasil:

Keputusan:
- ...

Status:
- PASS / PENDING / FAIL
```

## 7. Gate Merge

Perubahan visibility dinyatakan **belum siap merge** jika salah satu kondisi ini terjadi:
- Test visibility belum hijau.
- Frontend menu masih bisa menampilkan slug tanpa mode backend.
- Dokumentasi canonical/process belum sinkron.
- Dampak role-scope-area belum dinyatakan eksplisit.
