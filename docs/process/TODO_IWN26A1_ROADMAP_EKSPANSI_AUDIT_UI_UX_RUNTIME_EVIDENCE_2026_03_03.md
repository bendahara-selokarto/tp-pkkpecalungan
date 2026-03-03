# TODO IWN26A1 Roadmap Ekspansi Audit UI UX Runtime Evidence

Tanggal: 2026-03-03  
Status: `in-progress` (`batch:P1-P2-P3-P4-P5-P6-P8-implemented`)
Related ADR: `-`

## Aturan Pakai
- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks
- Baseline runtime evidence (`Playwright + Axe`) sudah tersedia melalui concern `AEU26A1`, namun coverage masih dasar.
- Skenario authenticated test masih dapat `skip` jika kredensial E2E tidak tersedia.
- Belum ada gate CI resmi yang memaksa eksekusi lane runtime UI/UX untuk concern UI berisiko.

## Kontrak Concern (Lock)
- Domain: perencanaan rollout audit UI/UX runtime berbasis kode.
- Role/scope target: `desa`, `kecamatan`, `super-admin` (progressive coverage).
- Boundary data: `e2e/*`, workflow CI, dokumen process concern UI.
- Acceptance criteria:
  - tersedia roadmap fase implementasi yang dapat dieksekusi berurutan,
  - target coverage role/scope dan flow prioritas jelas,
  - gate validasi dan fallback terspesifikasi.
- Dampak keputusan arsitektur: `tidak` (planning operasional, tanpa ubah boundary backend).

## Target Hasil
- [x] Tersusun roadmap 3 fase ekspansi runtime evidence yang executable.
- [x] Concern UI prioritas memiliki target smoke/a11y/visual/performance yang terukur.

## Langkah Eksekusi
- [x] `P1` Finalisasi kredensial E2E non-produksi dan secret management untuk CI.
- [x] `P2` Tambah smoke authenticated matrix:
  - dashboard role `desa`,
  - dashboard role `kecamatan`,
  - shell `super-admin` (`users`, `access-control`, `arsip`).
  - catatan: lane `a11y` authenticated strict-mode (`E2E_REQUIRE_AUTH_A11Y=1`) sudah hijau untuk role `desa`, `kecamatan`, dan `super-admin`.
- [x] `P3` Tambah smoke CRUD prioritas (minimal create+filter+pagination+delete guard):
  - `activities`,
  - `agenda-surat`,
  - `arsip`.
  - catatan: lane CRUD dijalankan pada project desktop (`chromium-desktop`), sementara project mobile `skip` by design untuk menjaga stabilitas baseline gate.
- [x] `P4` Tambah baseline visual regression untuk halaman prioritas (`login`, `dashboard`, `super-admin/users`).
  - catatan: lane visual dijalankan sebagai candidate gate non-blocking di workflow runtime evidence.
- [x] `P5` Tambah baseline performance audit (Lighthouse CI) untuk halaman prioritas.
  - catatan: fase awal diwujudkan sebagai lane `@perf` berbasis budget runtime timing (candidate gate non-blocking); migrasi ke Lighthouse CI penuh tetap dapat dilanjutkan sebagai iterasi lanjutan.
- [x] `P6` Integrasi gate CI bertahap:
  - gate wajib `@smoke` + `@a11y`,
  - lane tambahan `a11y deep audit` non-blocking (tanpa exclude `#nprogress` dan dengan `color-contrast` aktif),
  - gate kandidat `visual` + `performance`.
- [x] `P8` Hardening auditability runtime:
  - stabilisasi lane `@visual` dashboard dengan masking area chart dinamis,
  - persist evidence `@perf` ke `reports/ui-runtime/perf/latest`,
  - generate ringkasan `summary.json` + `summary.md` + `history/perf-history.jsonl`.
- [ ] `P7` Sinkronisasi TODO concern UI aktif agar setiap concern menyertakan evidence runtime.

## Validasi
- [x] L1: roadmap tervalidasi terhadap kontrak `AI_SINGLE_PATH_ARCHITECTURE` lane UI/UX auditability.
- [x] L2: baseline matrix role dan secret mapping terdokumentasi + terimplementasi di workflow.
- [x] L3: dry-run lokal setara CI untuk lane smoke/a11y berhasil:
  - `npm run test:e2e:smoke` (`11 passed`, `3 skipped`) pada mode `E2E_REQUIRE_AUTH=1`;
  - `npm run test:e2e:a11y` (`8 passed`) pada mode `E2E_REQUIRE_AUTH_A11Y=1`;
  - `npm run test:e2e:visual` (`6 passed`) untuk baseline visual prioritas;
  - `npm run test:e2e:perf` (`3 passed`, `3 skipped`) untuk baseline performance budget desktop;
  - `npm run test:e2e:perf:summary` (`entries=3`, `status=within-budget`) menghasilkan artefak audit `reports/ui-runtime/perf/*`;
  - `php artisan test --filter=DashboardLayoutMenuContractTest` (`PASS`).

## Risiko
- Risiko 1: flakiness E2E meningkat saat coverage diperluas tanpa stabilisasi test data.
- Risiko 2: waktu CI membengkak jika semua lane dijadikan wajib sekaligus.

## Keputusan
- [x] K1: rollout coverage runtime dilakukan bertahap per fase, bukan big-bang.
- [x] K2: lane `smoke+a11y` dijadikan mandatory lebih dulu; visual/performance sebagai candidate gate.

## Keputusan Arsitektur (Jika Ada)
- [ ] Tidak perlu ADR baru selama boundary arsitektur backend tetap.
- [ ] Jika ada keputusan CI strategis lintas banyak concern (cost/perf/security), buka ADR terpisah.

## Fallback Plan
- Jika gate CI runtime terlalu flakey, fallback sementara:
  - mandatory hanya `@smoke` desktop,
  - mobile/a11y tetap dijalankan sebagai non-blocking report,
  - buka TODO mitigasi flakiness dengan due date eksplisit.

## Output Final
- [x] Ringkasan roadmap fase + deliverable per fase.
- [x] Daftar concern UI yang masuk batch eksekusi pertama.
- [x] Hasil dry-run gate CI + residual risk flakiness.
