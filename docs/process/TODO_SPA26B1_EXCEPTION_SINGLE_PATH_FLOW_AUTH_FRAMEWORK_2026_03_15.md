# TODO SPA26B1 Exception Single Path Flow Auth Framework

Tanggal: 2026-03-15  
Status: `planned`
Related ADR: `docs/adr/ADR_0008_SINGLE_PATH_AUTH_FLOW_EXCEPTIONS.md`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Audit penuh menunjukkan beberapa flow auth framework (reset password, update profil) masih memodifikasi `User` langsung tanpa repository boundary.
- Untuk menjaga konsistensi single-path sekaligus menghindari refactor besar yang tidak diminta, dibutuhkan kontrak pengecualian yang eksplisit dan sempit.
- Kontrak ini hanya mengatur **exception resmi** agar tidak melebar ke modul domain lain.

## Kontrak Concern (Lock)

- Domain: governance arsitektur eksekusi (single-path).
- Role/scope target: semua role yang memakai flow auth framework (login/reset/password/profile).
- Boundary data: mutasi terbatas pada identitas user sendiri (nama/email/password/remember_token); tidak menyentuh `role`, `scope`, `area_id`, atau data domain.
- Acceptance criteria:
  - ADR exception single-path auth flow dibuat dan ditautkan.
  - Daftar flow yang diizinkan jelas dan sempit.
  - Registry concern aktif ter-update.
- Dampak keputusan arsitektur: `ya`

## Target Hasil

- [ ] ADR exception single-path auth flow tersusun dan ditautkan.
- [ ] Registry + operational log mencatat concern ini sebagai planned.

## Langkah Eksekusi

- [ ] Finalisasi scope exception + daftar flow yang diizinkan.
- [ ] Tulis ADR + tautkan ke TODO ini.
- [ ] Sinkronkan registry concern aktif + operational log.

## Validasi

- [ ] L1: syntax/lint/targeted test concern.
- [ ] L2: regression test concern terkait.
- [ ] L3: `php artisan test` jika perubahan signifikan.

## Risiko

- Risiko: exception meluas ke flow non-auth sehingga single-path melemah.
- Risiko: refactor besar di masa depan jika exception tidak dikelola secara ketat.

## Keputusan

- [ ] K1: Exception hanya untuk flow auth framework yang terbatas.
- [ ] K2: Semua flow di luar daftar wajib kembali ke repository boundary.

## Keputusan Arsitektur (Jika Ada)

- [x] Buat/tautkan ADR di `docs/adr/ADR_0008_SINGLE_PATH_AUTH_FLOW_EXCEPTIONS.md`.
- [x] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.
Status ADR: `proposed` (2026-03-15).

## Fallback Plan

- Jika exception dianggap berisiko, kunci kembali kebijakan strict single-path dan buat concern refactor auth flow ke repository boundary.

## Output Final

- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak.
- [ ] Hasil validasi + residual risk.
