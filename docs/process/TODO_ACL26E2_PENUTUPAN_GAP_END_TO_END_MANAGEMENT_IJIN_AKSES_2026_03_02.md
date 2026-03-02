# TODO ACL26E2 Penutupan Gap End To End Management Ijin Akses

Tanggal: 2026-03-02  
Status: `in-progress` (`state:e2e-closure-plan`)
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`

## Aturan Pakai
- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks
- Concern parent `ACL26M1` sudah menyelesaikan tahap `read-only matrix`, pilot `catatan-keluarga`, dan rollout batch 1 `activities`.
- Status saat ini belum `end-to-end` penuh karena:
  - validasi stakeholder matrix belum dieksekusi (`go/hold/adjust` belum diputus),
  - modul lain di luar batch 1 belum masuk rollout override terkelola.
- Diperlukan rencana penutupan gap E2E yang eksplisit, bertahap, dan aman terhadap regressi akses.

## Kontrak Concern (Lock)
- Domain: closure implementasi end-to-end management ijin akses modul x role-scope.
- Role/scope target:
  - pengelola: `super-admin`,
  - runtime terdampak: role operasional `desa|kecamatan` sesuai `RoleScopeMatrix`.
- Boundary data:
  - `routes/web.php`,
  - `app/Http/Controllers/SuperAdmin/AccessControlManagementController.php`,
  - `app/Http/Requests/SuperAdmin/*OverrideRequest.php`,
  - `app/UseCases/SuperAdmin/ListAccessControlMatrixUseCase.php`,
  - `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`,
  - `app/Http/Middleware/EnsureModuleVisibility.php`,
  - `resources/js/Pages/SuperAdmin/AccessControl/Index.vue`,
  - test concern ACL (`Feature + Unit`) dan docs parent/registry.
- Acceptance criteria:
  - keputusan stakeholder matrix tercatat (`go/hold/adjust`) dengan dampak dan alasan,
  - batch rollout modul lanjutan memiliki daftar modul, aturan kompatibilitas role-scope, dan fallback jelas,
  - regression suite ACL + visibility + middleware tetap hijau,
  - status parent `ACL26M1` bisa ditutup `done` setelah semua gate tercapai.
- Dampak keputusan arsitektur: `tidak` (tetap dalam boundary ADR 0002).

## Target Hasil
- [ ] Tersusun roadmap eksekusi penutupan gap E2E per batch modul.
- [ ] Keputusan stakeholder matrix terdokumentasi dan tersinkron ke parent concern.
- [ ] Eksekusi batch lanjutan (minimal 1 batch) tervalidasi end-to-end.
- [ ] Kriteria close parent concern `ACL26M1` terdefinisi dan dapat diuji.

## Langkah Eksekusi
- [x] Analisis scoped dependency + side effect.
- [x] Patch minimal pada boundary arsitektur (planning docs only).
- [x] Sinkronisasi dokumen concern terkait (parent + registry).
- [ ] L1. Kunci daftar kandidat modul rollout batch 2 dari matrix runtime (`read-only|read-write|hidden`) beserta alasan prioritas.
- [ ] L2. Jalankan sesi stakeholder decision (`go/hold/adjust`) dan catat keputusan final di concern ini + parent.
- [ ] L3. Eksekusi rollout batch 2 pada modul yang disetujui.
- [ ] L4. Jalankan regression suite ACL dan visibility pasca rollout batch 2.
- [ ] L5. Evaluasi kebutuhan batch 3; jika tidak ada gap residual, tutup parent concern.

## Validasi
- [ ] L1: `php artisan test tests/Feature/SuperAdmin/AccessControlManagementReadOnlyTest.php tests/Feature/SuperAdmin/AccessControlManagementWritePilotTest.php`.
- [ ] L2: `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Feature/ModuleVisibilityMiddlewareTest.php`.
- [ ] L3: `php artisan test` setelah batch rollout modul lanjutan.

## Risiko
- Salah konfigurasi kombinasi `module x role x scope` dapat memblokir akses role kompatibel.
- Rollout batch terlalu besar berisiko meningkatkan blast radius regressi.
- Keputusan stakeholder yang tertunda memperpanjang status `in-progress` parent concern.

## Keputusan
- [x] K1: Penutupan gap E2E dijalankan sebagai child concern terpisah dari parent governance.
- [x] K2: Rollout modul lanjutan wajib berbasis batch kecil + regression gate.
- [ ] K3: Keputusan stakeholder final (`go/hold/adjust`) dan dampak modul lanjutan.

## Keputusan Arsitektur (Jika Ada)
- [x] ADR baru tidak diperlukan pada tahap planning ini (masih dalam ADR 0002).
- [x] Status ADR 0002 tetap `accepted`.

## Fallback Plan
- Fallback level 1: rollback override per kombinasi `module x role x scope`.
- Fallback level 2: nonaktifkan rollout override global via config agar kembali ke hardcoded penuh.
- Fallback level 3: hold batch rollout lanjutan dan pertahankan modul terkelola saat ini (`catatan-keluarga`, `activities`) sampai keputusan stakeholder final.

## Output Final
- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak.
- [ ] Hasil validasi + residual risk.
