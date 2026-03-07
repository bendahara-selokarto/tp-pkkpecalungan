# TODO AUI26A1 Auditability Gate UI UX Berbasis Kode

Tanggal: 2026-03-03  
Status: `done` (`state:doc-governance-locked`)
Related ADR: `docs/adr/ADR_0004_UI_UX_AUDITABILITY_GATE.md`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Baseline saat ini sudah memiliki frontend contract test berbasis PHPUnit, tetapi belum ada kontrak arsitektur tunggal yang mengunci artefak audit UI/UX berbasis kode secara eksplisit.
- Concern ini menutup gap governance agar setiap audit UI/UX dapat dilacak dari routing task -> validasi -> bukti eksekusi.

## Kontrak Concern (Lock)

- Domain: arsitektur proses AI + auditability UI/UX.
- Role/scope target: seluruh concern UI lintas role `desa|kecamatan|super-admin`.
- Boundary data: dokumen `docs/process/*` + `docs/adr/*` (tanpa perubahan runtime aplikasi).
- Acceptance criteria:
  - AI single-path memuat jalur audit UI/UX berbasis kode.
  - Ada ADR yang mengunci trade-off + fallback.
  - Pattern registry playbook tersinkron.
- Dampak keputusan arsitektur: `ya`

## Target Hasil

- [x] Kontrak auditability UI/UX masuk ke arsitektur single-path.
- [x] Jejak keputusan lintas sesi terkunci melalui ADR + TODO sinkron.

## Langkah Eksekusi

- [x] Analisis scoped dependency + side effect.
- [x] Patch minimal pada boundary arsitektur.
- [x] Sinkronisasi dokumen concern terkait (single-path + playbook + ADR).

## Validasi

- [x] L1: audit scoped dokumen (`rg` + review diff) lulus.
- [x] L2: sinkronisasi referensi TODO-ADR-playbook tervalidasi.
- [x] L3: tidak wajib (`doc-only`, tanpa perubahan runtime/backend contract).

## Risiko

- Risiko 1: kontrak auditability terlalu ketat terhadap kondisi tooling saat ini.
- Risiko 2: drift jika TODO/ADR tidak ikut disinkronkan saat perubahan berikutnya.

## Keputusan

- [x] K1: audit UI/UX via code ditetapkan sebagai lane arsitektur resmi.
- [x] K2: bukti audit wajib berupa artefak test + catatan validasi operasional concern.

## Keputusan Arsitektur (Jika Ada)

- [x] Buat/tautkan ADR di `docs/adr/ADR_0004_UI_UX_AUDITABILITY_GATE.md`.
- [x] Sinkronkan status ADR (`accepted`) dengan status concern (`done`).

## Fallback Plan

- Jika kontrak baru memblokir concern karena tooling E2E belum siap, fallback ke lane `Frontend Contract + Feature Regression` sementara dengan status concern `partial` dan evidence gap eksplisit.

## Output Final

- [x] Ringkasan apa yang diubah dan kenapa.
- [x] Daftar file terdampak.
- [x] Hasil validasi + residual risk.

