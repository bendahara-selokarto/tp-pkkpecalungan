# TODO ACL26M1 Management Ijin Akses Berbasis Modul dan Group Role 2026-02-28

Tanggal: 2026-02-28  
Status: `planned`  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`

## Konteks
- Saat ini kontrol akses modul berbasis role/group masih hardcoded di `RoleMenuVisibilityService`.
- Perubahan akses seperti kasus `catatan-keluarga` memerlukan patch kode dan deploy, sehingga siklus koreksi akses tidak cukup cepat.
- Diperlukan menu baru pada `super-admin` untuk mengelola ijin akses berbasis `modul` dan `group role` secara terkontrol, tetap menjaga guardrail backend (`scope.role`, `module.visibility`, policy).

## Kontrak Concern (Lock)
- Domain: management ijin akses modul-group role oleh `super-admin`.
- Role/scope target:
  - pengelola: `super-admin`.
  - dampak runtime: semua role operasional `desa|kecamatan`.
- Boundary data:
  - backend: `Controller -> UseCase/Action -> Repository -> Model`.
  - enforcement: `RoleMenuVisibilityService`, `EnsureModuleVisibility`, `Policy` modul existing.
  - UI: halaman baru `SuperAdmin/AccessControl/*`.
- Acceptance criteria:
  - super-admin dapat melihat matrix akses `group role x modul`.
  - super-admin dapat mengubah mode akses (`read-only`/`read-write`/`hidden`) per kombinasi yang valid.
  - mapping akses modul `catatan-keluarga` dapat direpresentasikan dan diubah lewat UI baru tanpa patch kode hardcoded.
  - guardrail `scope-role-area` tidak melemah.
- Dampak keputusan arsitektur: `ya`.

## Target Hasil
- [ ] Menu baru pada sidebar super-admin: `Management Ijin Akses`.
- [ ] Halaman matrix akses menampilkan:
  - daftar group role aktif,
  - daftar modul per group domain,
  - mode akses efektif.
- [ ] Fitur ubah akses per modul-group role dengan validasi backend ketat.
- [ ] Integrasi runtime ke resolver akses tanpa behavior drift untuk role yang tidak diubah.
- [ ] Jejak audit perubahan ijin akses (minimal `changed_by`, waktu, before/after).

## Langkah Eksekusi
- [ ] Audit kontrak akses existing dari:
  - `RoleMenuVisibilityService`,
  - `EnsureModuleVisibility`,
  - `RoleScopeMatrix`,
  - mapping modul canonical pada `DOMAIN_CONTRACT_MATRIX`.
- [ ] Tetapkan kontrak data ijin akses:
  - entitas group role,
  - entitas modul,
  - mode akses yang diizinkan,
  - aturan prioritas jika ada override.
- [ ] Tambah route + controller super-admin untuk concern akses modul.
- [ ] Implementasi use case/action/repository untuk baca & update matrix ijin.
- [ ] Integrasi resolver runtime:
  - base matrix tetap ada sebagai fallback,
  - override dari storage digunakan jika tersedia.
- [ ] Implementasi UI Inertia untuk list/filter/update matrix ijin.
- [ ] Hardening copy UI user-facing (label natural, hindari token teknis mentah).
- [ ] Sinkronkan dokumentasi domain/process/ADR setelah implementasi.
- [ ] Jalankan audit dashboard trigger untuk menu/domain baru:
  - cek relevansi tampil di KPI/chart,
  - jika tidak relevan, tulis justifikasi eksplisit pada dokumen perubahan.

## Test Matrix Minimum (Concern Ini)
- [ ] Feature test jalur sukses super-admin mengubah ijin akses.
- [ ] Feature test role non super-admin ditolak mengakses menu management ijin.
- [ ] Feature test invalid kombinasi role-group/modul ditolak.
- [ ] Unit test resolver akses memastikan prioritas override vs fallback.
- [ ] Regression test untuk kasus `catatan-keluarga` (contoh mapping yang baru dipakai).
- [ ] `php artisan test` penuh sebelum finalisasi concern.

## Validasi
- [ ] `php artisan test tests/Feature/SuperAdmin/AccessControlManagementTest.php`
- [ ] `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php`
- [ ] `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`
- [ ] `php artisan test`

## Risiko
- Risiko eskalasi privilege jika validasi kombinasi role/modul longgar.
- Risiko drift akses lintas scope jika override tidak dibatasi `scope-role-area`.
- Risiko regresi besar karena concern ini menyentuh central gate akses modul.

## Keputusan
- [ ] K1: Model akses menggunakan konfigurasi terkelola super-admin dengan fallback matrix default.
- [ ] K2: Override akses harus bisa dinonaktifkan/rollback tanpa migrasi data destruktif.
- [ ] K3: Tidak ada perubahan authority ke frontend; authority tetap backend middleware/policy.

## Fallback Plan
- [ ] Sediakan flag/strategi rollback ke mode hardcoded penuh jika terjadi anomali runtime.
- [ ] Simpan snapshot matrix sebelum perubahan massal agar restore cepat.

## Output Final
- [ ] Ringkasan perubahan + alasan + dampak.
- [ ] Daftar file terdampak per layer (route/request/use case/repository/model/ui/test/docs).
- [ ] Hasil validasi + residual risk + opsi lanjutan.
