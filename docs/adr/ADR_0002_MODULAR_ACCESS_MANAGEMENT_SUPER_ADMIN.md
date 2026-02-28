# ADR 0002 Modular Access Management Super Admin

Tanggal: 2026-02-28  
Status: `accepted`  
Owner: Access governance  
Related TODO: `docs/process/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`  
Supersedes: `-`  
Superseded by: `-`

## Konteks
- Akses modul saat ini ditentukan oleh konstanta hardcoded pada `RoleMenuVisibilityService`.
- Perubahan akses operasional (contoh: penyesuaian akses `catatan-keluarga`) membutuhkan perubahan kode dan deployment.
- Dibutuhkan mekanisme terkelola oleh `super-admin` untuk mengatur ijin akses per `modul x group role` tanpa melemahkan guardrail backend.

## Opsi yang Dipertimbangkan
### Opsi A - Tetap Hardcoded Penuh
- Ringkasan pendek: semua mapping akses dipertahankan di kode.
- Kelebihan: sederhana, tidak menambah kompleksitas storage.
- Konsekuensi: perubahan akses lambat, beban patch berulang, sulit didelegasikan ke operator super-admin.

### Opsi B - Override Terkelola di Database + Fallback Hardcoded
- Ringkasan pendek: base matrix tetap di kode, super-admin dapat menulis override valid di storage.
- Kelebihan: fleksibel, tetap punya fallback aman, perubahan akses lebih cepat.
- Konsekuensi: butuh desain validasi, audit log, dan sinkronisasi resolver agar tidak drift.

### Opsi C - Full Dynamic Tanpa Hardcoded Base
- Ringkasan pendek: seluruh matrix akses dipindah ke storage, kode hanya membaca konfigurasi runtime.
- Kelebihan: paling fleksibel.
- Konsekuensi: risiko tinggi jika seed/konfigurasi rusak, kompleksitas bootstrap besar, rollback lebih sulit.

## Keputusan
- Opsi terpilih: Opsi B.
- Alasan utama: menjaga baseline keamanan existing sambil membuka jalur pengelolaan akses oleh super-admin.
- Kontrak yang dikunci:
  - fallback matrix hardcoded wajib tetap tersedia,
  - override hanya bisa dilakukan pada kombinasi role-group/modul yang valid,
  - enforcement akhir tetap di backend (`EnsureModuleVisibility` + policy), bukan frontend,
  - implementasi dilakukan bertahap per concern modul (bukan big-bang),
  - tahap awal wajib read-only untuk validasi desain sebelum write override.

## Dampak
- Dampak positif:
  - perubahan mapping akses tidak selalu memerlukan perubahan kode.
  - audit keputusan akses menjadi lebih eksplisit.
  - risiko rollout menurun karena aktivasi write dilakukan per modul.
- Trade-off:
  - kompleksitas runtime resolver bertambah.
  - butuh test regression lebih luas pada seluruh role operasional.
- Area terdampak (route/request/use case/repository/test/docs):
  - route/controller super-admin,
  - layer use case/action/repository untuk konfigurasi akses,
  - `RoleMenuVisibilityService` dan middleware visibilitas,
  - UI super-admin,
  - test feature/unit akses,
  - dokumen TODO/domain matrix/terminology map.

## Validasi
- [ ] Targeted test concern.
- [ ] Regression test concern terkait.
- [ ] `php artisan test` (jika perubahan signifikan).

## Rollback/Fallback Plan
- Langkah rollback minimum: nonaktifkan pembacaan override storage dan pakai matrix hardcoded penuh.
- Kondisi kapan fallback dijalankan: ditemukan anomali akses lintas role/scope pada smoke test atau produksi.

## Referensi
- `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
- `app/Http/Middleware/EnsureModuleVisibility.php`
- `app/Support/RoleScopeMatrix.php`
- `docs/process/TODO_ACL26M1_MANAGEMENT_IJIN_AKSES_MODUL_GROUP_ROLE_2026_02_28.md`
- `docs/process/TODO_ACL26S1_SUPER_ADMIN_MATRIX_READ_ONLY_2026_02_28.md`
- `docs/process/TODO_ACL26C1_PILOT_OVERRIDE_CATATAN_KELUARGA_2026_02_28.md`

## Status Log
- 2026-02-28: `proposed` | baseline keputusan concern.
- 2026-02-28: `proposed` -> `accepted` | disetujui implementasi Opsi B bertahap per concern modul, dimulai observasi + finalisasi markdown.
