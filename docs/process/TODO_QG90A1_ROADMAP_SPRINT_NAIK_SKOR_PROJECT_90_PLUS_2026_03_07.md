# TODO QG90A1 Roadmap Sprint Naik Skor Project 90 Plus

Tanggal: 2026-03-07  
Status: `planned`
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Baseline audit 2026-03-07 menunjukkan kualitas runtime backend sangat kuat, tetapi skor keseluruhan belum stabil di 90+ karena dua blocker non-fungsional utama:
  - style debt tinggi (`./vendor/bin/pint --test`: `907 files`, `633 style issues`),
  - E2E smoke belum bisa dieksekusi penuh di environment Linux ini karena dependency browser (`libnspr4.so`) belum tersedia.
- Validasi yang sudah hijau pada baseline:
  - `php artisan test --compact`: `1057 passed`, `7110 assertions`,
  - `npm run build`: sukses,
  - `php artisan test tests/Unit/Frontend --compact`: `18 passed`, `81 assertions`.
- Concern ini fokus pada hardening quality gate untuk menaikkan skor proyek ke `>=90` tanpa mengubah kontrak domain bisnis.

## Kontrak Concern (Lock)

- Domain: quality gate engineering (style consistency + UI runtime evidence stability).
- Role/scope target: lintas role; tidak ada perubahan matriks akses `role/scope/area`.
- Boundary data:
  - tooling kualitas (`composer scripts`, command test/lint),
  - file kode yang dipilih untuk normalisasi style (diff kecil bertahap),
  - skrip/documentation operasional E2E runtime evidence.
- Acceptance criteria:
  - lint/style gate tidak lagi menjadi blocker utama untuk jalur merge concern prioritas,
  - E2E smoke dapat dijalankan sampai tahap browser launch + suite start tanpa missing OS dependency,
  - full test backend tetap hijau setelah hardening.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [ ] T1. Skor proyek naik ke `>=90` melalui penurunan technical friction pada quality gate.
- [ ] T2. Baseline style debt diturunkan signifikan pada scope prioritas sprint (modul aktif + auth/super-admin + test kritis).
- [ ] T3. Jalur E2E smoke dapat dieksekusi konsisten pada environment CI/dev yang menjadi target kerja tim.

## Langkah Eksekusi

- [ ] L0. Kunci baseline metrik sprint:
  - simpan angka awal (`pint issue count`, pass/fail test suite, e2e doctor/smoke status).
- [ ] L1. Hardening style gate bertahap (diff-first):
  - prioritas file concern aktif dan boundary sensitif (`app/Actions`, `app/Http/Controllers/SuperAdmin`, `app/UseCases/User`, test policy/auth),
  - jalankan auto-fix terarah dan review agar tidak ada behavior drift.
- [ ] L2. Hardening E2E runtime dependency:
  - pastikan dependency Linux untuk Chromium terpenuhi di target environment,
  - normalisasi preflight script agar pesan gagal eksplisit (dependency vs bug test).
- [ ] L3. Stabilkan command matrix kualitas:
  - `php artisan test --compact`,
  - `php artisan test tests/Unit/Frontend --compact`,
  - `npm run build`,
  - `npm run test:e2e:smoke`.
- [ ] L4. Sinkronisasi dokumen concern terkait (doc-hardening pass):
  - update registry concern aktif,
  - update operational validation log dengan status terbaru concern `QG90A1`.

## Validasi

- [ ] V1 (L1): `./vendor/bin/pint --test` pada scope sprint prioritas menunjukkan penurunan issue yang terukur.
- [ ] V2 (L2): targeted regression test pada file yang disentuh tetap hijau.
- [ ] V3 (L3): `php artisan test --compact` hijau.
- [ ] V4 (UI runtime): `npm run test:e2e:smoke` tidak lagi gagal akibat missing OS dependency.

## Risiko

- Risiko 1: auto-format massal memicu diff terlalu besar dan memperlambat review.
- Risiko 2: perbedaan dependency host lokal vs CI dapat membuat hasil E2E tidak konsisten.

## Keputusan

- [ ] K1: gunakan strategi hardening bertahap per scope prioritas, bukan reformat total repository dalam satu patch.
- [ ] K2: E2E failure karena dependency OS diklasifikasikan sebagai issue environment dan ditangani di layer tooling/preflight, bukan domain logic.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru tidak diperlukan (tidak ada perubahan boundary arsitektur runtime).
- [x] Status concern dikelola via TODO + registry + operational validation log.

## Fallback Plan

- Jika penurunan style debt menimbulkan diff besar/risiko regressi:
  - rollback parsial ke batch terakhir,
  - lanjutkan batch lebih kecil per concern.
- Jika E2E tetap gagal pada host tertentu:
  - lock status `partial` untuk evidence runtime lokal,
  - validasi final E2E dipindahkan ke environment yang dependency-nya tervalidasi.

## Output Final

- [ ] Ringkasan perubahan sprint dan alasan prioritas.
- [ ] Daftar file tooling/kode/dokumen yang terdampak per batch.
- [ ] Hasil validasi akhir (pass/fail) + residual risk yang tersisa.

