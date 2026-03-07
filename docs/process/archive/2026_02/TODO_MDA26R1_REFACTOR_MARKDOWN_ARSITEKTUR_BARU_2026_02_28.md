# TODO MDA26R1 Refactor Markdown Arsitektur Baru 2026-02-28

Tanggal: 2026-02-28  
Status: `done`  
Related ADR: `docs/adr/ADR_0001_DOCUMENTATION_GOVERNANCE_TODO_ADR.md`

## Konteks
- Struktur markdown sebelumnya belum sepenuhnya merefleksikan arsitektur dokumentasi baru berbasis `TODO + ADR`.
- Dokumen source-of-truth (`AGENTS.md` dan `AI_SINGLE_PATH_ARCHITECTURE.md`) perlu disinkronkan agar jalur eksekusi dan jalur keputusan konsisten.

## Kontrak Concern (Lock)
- Domain: governance dokumentasi eksekusi AI.
- Role/scope target: semua eksekusi concern lintas modul.
- Boundary data: dokumen markdown (`AGENTS.md`, `docs/process/*`, `docs/adr/*`, `README.md`, `docs/README.md`).
- Acceptance criteria:
  - aturan TODO + ADR tercermin pada dokumen inti,
  - referensi silang antardokumen valid,
  - registry SOT concern diperbarui.
- Dampak keputusan arsitektur: `ya`

## Target Hasil
- [x] `AGENTS.md` memuat kontrak ADR pass dan aturan sinkronisasi TODO + ADR.
- [x] `AI_SINGLE_PATH_ARCHITECTURE.md` memuat routing deterministik untuk concern ADR.
- [x] `README.md` dan `docs/README.md` menampilkan arsitektur dokumentasi baru.
- [x] Template TODO mendukung tautan ADR.
- [x] Concern ini tercatat sebagai SOT di registry TODO.

## Langkah Eksekusi
- [x] Audit scoped dokumen source-of-truth yang terdampak.
- [x] Patch minimal lintas dokumen inti (`AGENTS`, single-path, indeks docs, template).
- [x] Tambah ADR baseline untuk keputusan governance dokumentasi.
- [x] Sinkronkan registry SOT concern.

## Validasi
- [x] L1: scoped `rg` untuk memastikan keyword/rujukan TODO + ADR muncul pada dokumen target.
- [x] L2: scoped `git diff` memastikan perubahan hanya di file concern.
- [x] L3: `php artisan test` jika perubahan signifikan (tidak dijalankan karena concern ini dokumentasi saja).

## Risiko
- Overhead dokumentasi meningkat pada concern kecil jika ADR dipakai tanpa seleksi trigger.
- Drift tetap bisa terjadi bila registry SOT tidak diupdate saat muncul concern baru.

## Keputusan
- [x] K1: Arsitektur dokumentasi baru resmi memakai pasangan TODO + ADR untuk concern arsitektural.
- [x] K2: Source-of-truth update dilakukan di AGENTS + single-path + playbook + index docs.

## Keputusan Arsitektur (Jika Ada)
- [x] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`.
- [x] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan
- Jika governance baru dinilai terlalu berat, fallback ke TODO-only untuk concern non-strategis sambil mempertahankan ADR pada concern arsitektural utama.

## Output Final
- [x] Ringkasan perubahan + alasan + keputusan terkunci.
- [x] Daftar file terdampak.
- [x] Hasil validasi + residual risk.
