# TODO GCP26A4 Governance Audit Wave Followup

Tanggal: 2026-03-09  
Status: `in-progress`
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Audit automation governance markdown sudah aktif, tetapi corpus planning belum diaudit penuh sampai level `TODO + ADR + link/path + warning-level thinning + annex sharding`.
- Batch sebelumnya juga masih menyisakan concern `done` di root `docs/process/`, sehingga context pack harian berisiko memuat histori yang seharusnya sudah diarsipkan.
- Concern ini menjadi parent follow-up untuk menutup audit gelombang lanjutan secara bertahap dengan boundary commit/push yang jelas per batch.

## Kontrak Concern (Lock)

- Domain: governance markdown + routing context AI.
- Role/scope target: process governance lintas concern.
- Boundary data: `docs/process/TODO_*.md`, `docs/process/archive/**`, `docs/adr/ADR_*.md`, file router/index/log terkait, dan workflow audit dokumentasi.
- Acceptance criteria:
  - root `docs/process/` hanya menyisakan concern aktif + registry/index canonical,
  - referensi `TODO + ADR` tetap hidup setelah archiving dan thinning,
  - warning-level file menurun atau terdokumentasi jelas,
  - annex pattern details siap dipecah tanpa merusak jalur routing on-demand,
  - push remote tervalidasi dan gate GitHub Actions bisa diaudit.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] Batch 1: audit global `TODO + ADR`, arsipkan concern `done`, sinkronkan referensi aktif.
- [x] Batch 2: audit link/path markdown governance corpus aktif dan perbaiki referensi mati.
- [x] Batch 3: thin warning-level governance files sampai budget lebih sehat.
- [x] Batch 4: shard `AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md` menjadi annex yang lebih ringan.
- [ ] Batch 5: verifikasi push remote + hasil workflow GitHub Actions.

## Langkah Eksekusi

- [x] Analisis scoped dependency + side effect.
- [ ] Patch minimal per batch dengan boundary commit yang terpisah.
- [ ] Sinkronisasi dokumen concern terkait (registry/index/log/budget/router) saat trigger hardening aktif.

## Validasi

- [x] L1: audit `TODO + ADR` corpus dan referensi root/archive konsisten.
- [x] L2: audit link/path markdown + governance audit script `PASS`.
- [ ] L3: verifikasi workflow remote/GitHub Actions untuk gate dokumentasi.

## Risiko

- Risiko 1: link/path drift setelah file TODO `done` dipindahkan ke archive periodik.
- Risiko 2: thinning berlebihan dapat menghapus pointer closure yang masih dipakai routing concern aktif.

## Keputusan

- [x] K1: concern `done` tidak boleh tetap tinggal di root `docs/process/` kecuali dokumen memang berfungsi sebagai registry/index aktif.
- [x] K2: follow-up audit dijalankan bertahap dengan commit/push per batch agar rollback dan verifikasi remote tetap mudah.

## Keputusan Arsitektur (Jika Ada)

- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jika sinkronisasi arsip memutus routing, kembalikan file concern yang bermasalah ke root batch terkait lalu audit ulang referensi sebelum push.

## Output Final

- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak per batch.
- [ ] Hasil validasi + residual risk.

## Progress Log

- 2026-03-09 batch 1:
  - concern `done` di root `docs/process/` dipindahkan ke `docs/process/archive/2026_03/` agar routing harian tetap tipis,
  - pointer closure pada `TTM25R1`, `ADR_0006`, dan parent TODO `SPA26A1` disinkronkan ke jalur arsip baru,
  - `GCP26A4` diregistrasikan ke `TTM25R1` dan index aktif `OPERATIONAL_VALIDATION_LOG.md`,
  - `scripts/audit_markdown_governance.ps1` tetap `PASS` setelah archiving dan sinkronisasi.
- 2026-03-09 batch 2:
  - ditambahkan `scripts/audit_markdown_paths.ps1` untuk audit referensi path/link pada governance corpus aktif,
  - generator TODO dan workflow CI diperluas agar menjalankan governance audit + markdown path audit,
  - referensi drift aktif diperbaiki pada `README.md`, `docs/adr/README.md`, dan `docs/process/COMMAND_NUMBER_SHORTCUTS.md`,
  - `scripts/audit_markdown_governance.ps1` dan `scripts/audit_markdown_paths.ps1` sama-sama `PASS`.
- 2026-03-09 batch 3:
  - warning-level file aktif ditipiskan sampai kembali masuk soft cap: `AI_SINGLE_PATH_ARCHITECTURE`, `AI_FRIENDLY_EXECUTION_PLAYBOOK`, `IWN26B1`, dan `RGM26A1`,
  - boilerplate TODO aktif yang berulang dihapus dari concern planning agar context pack lebih efisien,
  - `scripts/audit_markdown_governance.ps1` kembali `PASS` tanpa warning soft cap aktif.
- 2026-03-10 batch 4:
  - `AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md` dijadikan indeks ringkas dengan shard `delivery`, `governance`, dan `runtime`,
  - detail pattern tetap hidup pada shard terpisah agar retrieval tetap ringan.
