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
- [ ] Risiko regresi visual lintas modul karena banyak halaman memakai pola tabel yang sama.
- [ ] Risiko ketidakkonsistenan jika refactor dilakukan parsial tanpa kontrak komponen tunggal.
- [ ] Risiko scope creep bila refactor UI bercampur perubahan domain/backend.

## Keputusan Dikunci
- [x] Refactor difokuskan ke layer UI dan aksesibilitas tanpa mengubah kontrak domain backend.
- [x] Concern ini memakai pendekatan bertahap, bukan rewrite menyeluruh satu rilis.
- [x] Priority pertama: readability mobile + touch ergonomics + interaksi aksesibel.

## ADR Terkait
- Tidak wajib ADR baru pada tahap ini (belum ada perubahan boundary arsitektur backend).
