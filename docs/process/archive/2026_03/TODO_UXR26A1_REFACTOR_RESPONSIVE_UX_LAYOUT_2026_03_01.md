# TODO UXR26A1 Refactor Responsive UX Layout 2026-03-01

Tanggal: 2026-03-01  
Status: `done` (`state:responsive-ux-closed`)

## Konteks

- Audit UI/UX menemukan isu struktural lintas halaman: tabel mobile kehilangan konteks kolom, target sentuh kecil, dan interaksi navigasi belum konsisten untuk aksesibilitas.
- Isu berdampak langsung pada modul CRUD utama karena pola komponen dipakai berulang pada banyak halaman.

## Target Hasil

- Pola responsif tabel mobile konsisten dan tetap terbaca tanpa ambigu kolom.
- Target sentuh mobile minimum 44px terpenuhi untuk aksi utama/sekunder.
- Navigasi dan modal lebih aksesibel (semantik interaksi + fokus keyboard).
- State loading/empty/error/disabled terstandar untuk flow CRUD.

## Langkah Eksekusi

- [x] `R1` Inventarisasi halaman prioritas tinggi (dashboard + index CRUD + auth) untuk baseline refactor bertahap.
- [x] `R2` Tetapkan kontrak komponen `ResponsiveDataTable` (desktop table + mobile card/label-aware).
- [x] `R3` Terapkan metadata kolom/label mobile agar tidak bergantung pada header tersembunyi.
- [x] `R4` Standarkan ukuran komponen interaktif mobile (`min-h-[44px]`) pada tombol, pagination, checkbox/radio wrapper.
- [x] `R5` Refactor elemen klik non-semantic menjadi `button`/`Link` pada area navigasi dan dropdown.
- [x] `R6` Tambahkan guard aksesibilitas modal (fokus awal, fokus kembali, escape handling konsisten).
- [x] `R7` Standarkan komponen state (`loading`, `empty`, `error`, `disabled`) untuk list/form.
- [x] `R8` Jalankan rollout bertahap per modul agar tidak memicu behavior drift lintas domain.

## Validasi

- [x] Uji manual breakpoint `360/390/768/1024/1280` untuk halaman prioritas.
- [x] Tidak ada kehilangan konteks kolom saat mobile pada halaman index yang direfactor.
- [x] Aksi utama/sekunder tetap dapat dipicu dengan keyboard (`Tab`, `Enter`, `Space`, `Escape`).
- [x] `php artisan test` tetap hijau setelah batch refactor yang signifikan.

## Risiko

- [x] Risiko regresi visual lintas modul karena banyak halaman memakai pola tabel yang sama.
- [x] Risiko ketidakkonsistenan jika refactor dilakukan parsial tanpa kontrak komponen tunggal.
- [x] Risiko scope creep bila refactor UI bercampur perubahan domain/backend.

## Mitigasi per Risiko

- [x] `M1` Rollout bertahap per batch halaman prioritas (`Dashboard` -> `SuperAdmin Users` -> `Arsip` -> CRUD lain).
- [x] `M2` Gunakan feature flag `UI_RESPONSIVE_TABLE_V2` untuk transisi dan fallback cepat.
- [x] `M3` Terapkan komponen tunggal `ResponsiveDataTable` dengan metadata kolom wajib (`key`, `label`, `mobileLabel`).
- [x] `M4` Tambahkan guard CI/lint agar tabel baru tanpa metadata responsif tidak lolos review.
- [x] `M5` Standarkan utility sentuh mobile minimum `min-h-[44px]` pada aksi primer/sekunder/destruktif.
- [x] `M6` Refactor elemen klik non-semantic ke `button`/`a` dan pastikan state fokus terlihat.
- [x] `M7` Terapkan guard modal aksesibel: initial focus, focus trap, restore focus, dan `Escape` close.
- [x] `M8` Batasi scope PR concern ini hanya pada layer UI; perubahan backend/domain ditolak dan dipisah concern.
- [x] `M9` Simpan jalur rollback per batch (commit kecil + fallback komponen lama) untuk minimalkan blast radius.
- [x] `M10` Hardening khusus Dashboard: semua kontrol filter utama (`mode`, `level`, `sub_level`, CTA) wajib memenuhi target sentuh minimum 44px.
- [x] `M11` Sinkronkan status concern dashboard pada registry SOT sebelum concern UI batch dinyatakan selesai.

## Exit Criteria Mitigasi

- [x] Semua halaman batch aktif lolos uji breakpoint `360/390/768/1024/1280` tanpa layout break.
- [x] Tabel mobile batch aktif tetap terbaca dengan label kolom yang jelas.
- [x] Komponen interaktif utama batch aktif memenuhi minimum target sentuh 44px.
- [x] Kontrol filter utama Dashboard (`Cara Tampil`, `Cakupan Wilayah`, `Wilayah Turunan`, `Tampilkan Data`) memenuhi minimum target sentuh 44px.
- [x] Navigasi dan modal batch aktif usable penuh via keyboard (`Tab`, `Enter`, `Space`, `Escape`).
- [x] Tidak ada perubahan route/use case/repository/policy pada PR refactor concern ini.

## Keputusan Dikunci

- [x] Refactor difokuskan ke layer UI dan aksesibilitas tanpa mengubah kontrak domain backend.
- [x] Concern ini memakai pendekatan bertahap, bukan rewrite menyeluruh satu rilis.
- [x] Priority pertama: readability mobile + touch ergonomics + interaksi aksesibel.

## ADR Terkait

- Tidak wajib ADR baru pada tahap ini (belum ada perubahan boundary arsitektur backend).

## Progress Update 2026-03-02 (Batch SuperAdmin Arsip + Pagination Touch Target)

- Refactor halaman `SuperAdmin/Arsip/Index` ke kontrak `ResponsiveDataTable` dengan fallback legacy berbasis feature flag `VITE_UI_RESPONSIVE_TABLE_V2`.
- Metadata kolom mobile (`mobileLabel`) ditambahkan untuk seluruh kolom tabel management arsip.
- Standarisasi target sentuh `min-h-[44px]` diterapkan pada:
  - kontrol `per_page` dan CTA utama di `SuperAdmin/Arsip/Index`,
  - tombol aksi tabel (`Edit`, `Hapus`) pada mode responsif dan fallback legacy,
  - komponen reusable `PaginationBar` (link aktif/nonaktif).
- Guard rollout diperluas:
  - `tests/Unit/Frontend/ResponsiveTableRolloutContractTest.php` kini mencakup `resources/js/Pages/SuperAdmin/Arsip/Index.vue`.
- Validasi otomatis batch:
  - `php artisan test tests/Unit/Frontend/ResponsiveTableRolloutContractTest.php tests/Unit/Frontend/DashboardResponsiveInteractionContractTest.php` (`PASS`, `5` tests, `19` assertions).
  - `php artisan test --filter ArsipManagementTest` (`PASS`, `6` tests, `98` assertions).
  - `cmd /c npm run build` (`PASS`, `vite build`, built in 11.49s).

## Progress Update 2026-03-02 (Batch Navigasi Semantik)

- Refactor komponen navigasi `admin-one` agar trigger dropdown tidak lagi memakai elemen non-semantic:
  - `resources/js/admin-one/components/NavBarItem.vue`: dropdown trigger dipaksa `button`; fallback non-link juga `button`; tambah `aria-expanded` + `aria-haspopup`.
  - `resources/js/admin-one/components/AsideMenuItem.vue`: dropdown trigger dipaksa `button`; link tetap `Link`/`a` hanya jika punya destination; tambah `aria-expanded` + `aria-haspopup`.
- State fokus keyboard distandarkan dengan kelas `focus-visible:outline-*` pada trigger navbar/aside.
- Guard kontrak ditambahkan:
  - `tests/Unit/Frontend/NavigationSemanticContractTest.php` untuk mencegah regresi fallback non-semantic pada navigasi/dropdown.

## Progress Update 2026-03-02 (Batch Modal Accessibility + State Standardization)

- Guard modal aksesibel dikunci pada komponen shared:
  - `resources/js/admin-one/components/CardBoxModal.vue` mempertahankan initial focus, focus trap `Tab`, restore focus saat close, dan close `Escape`.
  - kontrak dicek pada `tests/Unit/Frontend/ModalAccessibilityContractTest.php`.
- Standarisasi state list ditambahkan pada komponen tunggal:
  - `resources/js/admin-one/components/ResponsiveDataTable.vue` kini memiliki state `loading|error|disabled|ready` dengan pesan standar.
  - kontrak dicek pada `tests/Unit/Frontend/ResponsiveTableStateContractTest.php`.
- Validasi otomatis batch:
  - `php artisan test tests/Unit/Frontend/ResponsiveTableRolloutContractTest.php tests/Unit/Frontend/DashboardResponsiveInteractionContractTest.php tests/Unit/Frontend/NavigationSemanticContractTest.php tests/Unit/Frontend/ModalAccessibilityContractTest.php tests/Unit/Frontend/ResponsiveTableStateContractTest.php` (`PASS`, `10` tests, `46` assertions).
  - `cmd /c npm run build` (`PASS`, `vite build`, built in 17.38s).

## Progress Update 2026-03-02 (Closure Concern)

- Revalidasi suite penuh pasca batch UXR:
  - `php artisan test`
  - hasil: `PASS` (`1047` tests, `7033` assertions).
- Concern `UXR26A1` ditutup ke `done`; residual guard tetap dijaga oleh kontrak frontend (`ResponsiveTableRollout`, `DashboardResponsiveInteraction`, `NavigationSemantic`, `ModalAccessibility`, `ResponsiveTableState`).

