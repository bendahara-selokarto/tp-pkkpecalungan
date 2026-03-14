# ADR 0007 Deprecate Legacy Runtime Compatibility

Tanggal: 2026-03-15  
Status: `accepted`  
Owner: manto  
Related TODO: `docs/process/TODO_LGC26A1_DEPRECATE_LEGACY_RUNTIME_COMPATIBILITY_2026_03_15.md`  
Supersedes: `-`  
Superseded by: `-`

## Konteks

- Runtime masih memuat kompatibilitas legacy (role `admin-*`, payload fallback dashboard `dashboardStats/dashboardCharts`, alias route `bantuans/keuangan`, dan normalisasi field legacy Program Prioritas).
- Kompatibilitas ini memicu drift kontrak akses dan membebani hardening.

## Opsi yang Dipertimbangkan
### Opsi A - Pertahankan kompatibilitas runtime legacy

- Ringkasan pendek: biarkan role/route/payload legacy tetap aktif.
- Kelebihan: tidak ada perubahan perilaku untuk pengguna legacy.
- Konsekuensi: drift kontrak berlanjut; kompleksitas akses dan dashboard meningkat.

### Opsi B - Deprecate kompatibilitas runtime legacy (hapus dari runtime)

- Ringkasan pendek: hapus role/route/payload/normalisasi legacy dari runtime, simpan sebagai catatan dokumen.
- Kelebihan: kontrak akses lebih jelas; beban teknis menurun.
- Konsekuensi: user legacy harus migrasi; bookmark alias lama menjadi 404.

## Keputusan

- Opsi terpilih: Opsi B.
- Alasan utama: mengunci kontrak canonical dan mengurangi drift.
- Kontrak yang dikunci: hanya role canonical aktif; tidak ada alias route/payload legacy di runtime.

## Dampak

- Dampak positif: akses role/scope lebih bersih, dashboard payload single-path, UI/menu lebih konsisten.
- Trade-off: perlu migrasi user legacy dan komunikasi perubahan link.
- Area terdampak (route/request/use case/repository/test/docs):
  - routes/web.php
  - app/Support/RoleScopeMatrix.php
  - app/Domains/Wilayah/Services/RoleMenuVisibilityService.php
  - app/Domains/Wilayah/Activities/Services/ActivityScopeService.php
  - app/Policies/ArsipDocumentPolicy.php
  - app/Domains/Wilayah/ProgramPrioritas/Requests/*
  - app/Http/Controllers/DashboardController.php
  - resources/js/Pages/Dashboard.vue
  - resources/js/Layouts/DashboardLayout.vue
  - resources/js/menus/printMenuRegistry.js
  - tests/*
  - docs/process/*, docs/domain/*, docs/pdf/*

## Validasi

- [ ] Targeted test concern.
- [ ] Regression test concern terkait.
- [ ] `php artisan test` (perubahan signifikan).

## Rollback/Fallback Plan

- Revert commit concern ini atau reintroduce mapping legacy secara sementara jika akses operasional terganggu.
- Fallback dijalankan bila pengguna aktif tidak dapat mengakses modul karena belum migrasi.

## Referensi

- AGENTS.md
- docs/process/AI_SINGLE_PATH_ARCHITECTURE.md
- docs/process/TODO_LGC26A1_DEPRECATE_LEGACY_RUNTIME_COMPATIBILITY_2026_03_15.md

## Status Log

- 2026-03-15: `proposed` -> `accepted` (user meminta legacy diarahkan ke dokumentasi saja).
