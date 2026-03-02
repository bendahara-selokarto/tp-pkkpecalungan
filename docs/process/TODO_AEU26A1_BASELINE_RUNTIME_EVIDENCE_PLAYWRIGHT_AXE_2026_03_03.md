# TODO AEU26A1 Baseline Runtime Evidence Playwright Axe

Tanggal: 2026-03-03  
Status: `done` (`state:l3-runtime-evidence-baseline`)
Related ADR: `-`

## Aturan Pakai
- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks
- Lane `UI/UX auditability gate` sudah dikunci di arsitektur, tetapi evidence runtime L3 belum tersedia secara otomatis.
- Concern ini menambahkan baseline Playwright + Axe agar smoke runtime UI/UX bisa direplay.

## Kontrak Concern (Lock)
- Domain: kualitas runtime UI/UX lintas halaman auth + shell aplikasi.
- Role/scope target: baseline umum (`login`) + smoke terautentikasi opsional lintas role.
- Boundary data: tooling frontend (`package.json`, config Playwright, spec E2E) + dokumentasi operasional.
- Acceptance criteria:
  - tersedia command E2E smoke/a11y standar,
  - tersedia spec runtime baseline (`@smoke`, `@a11y`),
  - validasi concern berjalan dan terdokumentasi.
- Dampak keputusan arsitektur: `tidak` (implementasi operasional, bukan boundary backend).

## Target Hasil
- [x] Baseline Playwright + Axe terpasang dan dapat dijalankan.
- [x] Evidence runtime L3 awal tersedia untuk login page + smoke auth shell.

## Langkah Eksekusi
- [x] Analisis scoped dependency + side effect.
- [x] Patch minimal pada boundary arsitektur.
- [x] Sinkronisasi dokumen concern terkait (README runtime evidence).

## Validasi
- [x] L1: `npm run test:e2e:smoke` (`2 passed`, `2 skipped` karena kredensial belum diset).
- [x] L2: `npm run test:e2e:a11y` (`2 passed`).
- [x] L3: tidak wajib `php artisan test` (perubahan concern frontend tooling E2E runtime).

## Risiko
- Risiko 1: smoke terautentikasi dapat ter-skip jika kredensial E2E tidak disediakan.
- Risiko 2: coverage baseline baru mencakup login + shell, belum mencakup semua flow CRUD.

## Keputusan
- [x] K1: baseline runtime memakai Playwright dengan dua project viewport (`desktop`, `mobile`).
- [x] K2: a11y baseline memakai Axe pada login page dengan gate `serious|critical`.

## Keputusan Arsitektur (Jika Ada)
- [x] Tidak membuat ADR baru (tidak ada perubahan boundary arsitektur lintas concern).
- [x] Status concern disinkronkan pada TODO ini sebagai artefak audit implementasi.

## Fallback Plan
- Jika environment belum siap browser/kredensial, jalankan lane minimum (`@smoke` login + `@a11y`) dan catat smoke auth sebagai `skipped` dengan alasan eksplisit.

## Output Final
- [x] Ringkasan apa yang diubah dan kenapa.
- [x] Daftar file terdampak.
- [x] Hasil validasi + residual risk.
