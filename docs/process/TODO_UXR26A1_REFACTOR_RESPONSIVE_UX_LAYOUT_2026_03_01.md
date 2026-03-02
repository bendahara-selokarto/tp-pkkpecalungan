# TODO UXR26A1 Refactor Responsive UX Layout 2026-03-01

Tanggal: 2026-03-01  
Status: `active` (`responsive-ux-refactor`)

## Konteks
- Audit UI/UX menemukan isu struktural lintas halaman: tabel mobile kehilangan konteks kolom, target sentuh kecil, dan interaksi navigasi belum konsisten untuk aksesibilitas.
- Isu berdampak langsung pada modul CRUD utama karena pola komponen dipakai berulang pada banyak halaman.

## Target Hasil
- Pola responsif tabel mobile konsisten dan tetap terbaca tanpa ambigu kolom.
- Target sentuh mobile minimum 44px terpenuhi untuk aksi utama/sekunder.
- Navigasi dan modal lebih aksesibel (semantik interaksi + fokus keyboard).
- State loading/empty/error/disabled terstandar untuk flow CRUD.

## Langkah Eksekusi
- [ ] `R1` Inventarisasi halaman prioritas tinggi (dashboard + index CRUD + auth) untuk baseline refactor bertahap.
- [ ] `R2` Tetapkan kontrak komponen `ResponsiveDataTable` (desktop table + mobile card/label-aware).
- [ ] `R3` Terapkan metadata kolom/label mobile agar tidak bergantung pada header tersembunyi.
- [ ] `R4` Standarkan ukuran komponen interaktif mobile (`min-h-[44px]`) pada tombol, pagination, checkbox/radio wrapper.
- [ ] `R5` Refactor elemen klik non-semantic menjadi `button`/`Link` pada area navigasi dan dropdown.
- [ ] `R6` Tambahkan guard aksesibilitas modal (fokus awal, fokus kembali, escape handling konsisten).
- [ ] `R7` Standarkan komponen state (`loading`, `empty`, `error`, `disabled`) untuk list/form.
- [ ] `R8` Jalankan rollout bertahap per modul agar tidak memicu behavior drift lintas domain.

## Validasi
- [ ] Uji manual breakpoint `360/390/768/1024/1280` untuk halaman prioritas.
- [ ] Tidak ada kehilangan konteks kolom saat mobile pada halaman index yang direfactor.
- [ ] Aksi utama/sekunder tetap dapat dipicu dengan keyboard (`Tab`, `Enter`, `Space`, `Escape`).
- [ ] `php artisan test` tetap hijau setelah batch refactor yang signifikan.

## Risiko
- Risiko regresi visual lintas modul karena banyak halaman memakai pola tabel yang sama.
- Risiko ketidakkonsistenan jika refactor dilakukan parsial tanpa kontrak komponen tunggal.
- Risiko scope creep bila refactor UI bercampur perubahan domain/backend.

## Mitigasi per Risiko
- [ ] `M1` Rollout bertahap per batch halaman prioritas (`Dashboard` -> `SuperAdmin Users` -> `Arsip` -> CRUD lain).
- [ ] `M2` Gunakan feature flag `UI_RESPONSIVE_TABLE_V2` untuk transisi dan fallback cepat.
- [ ] `M3` Terapkan komponen tunggal `ResponsiveDataTable` dengan metadata kolom wajib (`key`, `label`, `mobileLabel`).
- [ ] `M4` Tambahkan guard CI/lint agar tabel baru tanpa metadata responsif tidak lolos review.
- [ ] `M5` Standarkan utility sentuh mobile minimum `min-h-[44px]` pada aksi primer/sekunder/destruktif.
- [ ] `M6` Refactor elemen klik non-semantic ke `button`/`a` dan pastikan state fokus terlihat.
- [ ] `M7` Terapkan guard modal aksesibel: initial focus, focus trap, restore focus, dan `Escape` close.
- [ ] `M8` Batasi scope PR concern ini hanya pada layer UI; perubahan backend/domain ditolak dan dipisah concern.
- [ ] `M9` Simpan jalur rollback per batch (commit kecil + fallback komponen lama) untuk minimalkan blast radius.
- [ ] `M10` Hardening khusus Dashboard: semua kontrol filter utama (`mode`, `level`, `sub_level`, CTA) wajib memenuhi target sentuh minimum 44px.
- [ ] `M11` Sinkronkan status concern dashboard pada registry SOT sebelum concern UI batch dinyatakan selesai.

## Exit Criteria Mitigasi
- [ ] Semua halaman batch aktif lolos uji breakpoint `360/390/768/1024/1280` tanpa layout break.
- [ ] Tabel mobile batch aktif tetap terbaca dengan label kolom yang jelas.
- [ ] Komponen interaktif utama batch aktif memenuhi minimum target sentuh 44px.
- [ ] Kontrol filter utama Dashboard (`Cara Tampil`, `Cakupan Wilayah`, `Wilayah Turunan`, `Tampilkan Data`) memenuhi minimum target sentuh 44px.
- [ ] Navigasi dan modal batch aktif usable penuh via keyboard (`Tab`, `Enter`, `Space`, `Escape`).
- [ ] Tidak ada perubahan route/use case/repository/policy pada PR refactor concern ini.

## Keputusan Dikunci
- [x] Refactor difokuskan ke layer UI dan aksesibilitas tanpa mengubah kontrak domain backend.
- [x] Concern ini memakai pendekatan bertahap, bukan rewrite menyeluruh satu rilis.
- [x] Priority pertama: readability mobile + touch ergonomics + interaksi aksesibel.

## ADR Terkait
- Tidak wajib ADR baru pada tahap ini (belum ada perubahan boundary arsitektur backend).
