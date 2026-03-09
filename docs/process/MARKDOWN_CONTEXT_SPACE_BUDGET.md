# Markdown Context Space Budget

Tanggal efektif: 2026-03-09  
Status: `active`  
Owner: process governance

## Tujuan

- Menetapkan budget numerik untuk markdown aktif agar context management AI tidak hanya bergantung pada intuisi "ringkas".
- Mengunci baseline repo saat ini, termasuk estimasi konteks ideal yang dibutuhkan AI untuk bekerja nyaman pada jalur governance aktif.
- Menentukan cara memperluas space saat model AI punya context window yang lebih besar tanpa membuat file governance utama kembali membengkak.

## Formula Canonical

- Estimasi token per file:
  - `estimated_tokens = ceil(chars / 4)`
- Estimasi token pack:
  - `pack_tokens = sum(estimated_tokens(file_i))`
- Estimasi ideal context window repo:
  - `ideal_context_window = ceil(pack_tokens / 0.65)`
- Kontrak reserve:
  - maksimal `65%` dari ideal context window boleh dipakai markdown aktif,
  - minimal `35%` harus tersisa untuk prompt user, cuplikan kode, diff, output validasi, dan reasoning.

Alasan:

- `chars / 4` murah dihitung dengan shell biasa dan cukup stabil untuk budgeting lintas sesi.
- Reserve `35%` mencegah pack dokumen memakan hampir seluruh jendela konteks sebelum file kode dan prompt user ikut dimuat.

## Baseline Audit 2026-03-09

Ukuran artefak aktif yang diukur:

| File | Chars | Est. Tokens | Catatan |
| --- | ---: | ---: | --- |
| `AGENTS.md` | `17,308` | `4,327` | kontrak tertinggi; harus tetap padat |
| `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md` | `12,310` | `3,078` | routing operasional default |
| `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md` | `12,562` | `3,141` | registry pattern ringkas |
| `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md` | `4,303` | `1,076` | thin registry aktif pasca compaction |
| `docs/process/OPERATIONAL_VALIDATION_LOG.md` | `3,132` | `783` | index aktif ringkas pasca compaction |
| `docs/process/TODO_IWN26A1_ROADMAP_EKSPANSI_AUDIT_UI_UX_RUNTIME_EVIDENCE_2026_03_03.md` | `6,949` | `1,738` | contoh child concern aktif |
| `docs/process/TODO_IWN26B1_REFACTOR_GROUPING_MODUL_DOMAIN_E2E_2026_03_04.md` | `8,238` | `2,060` | contoh parent concern aktif |
| `docs/adr/ADR_0005_TAHUN_ANGGARAN_CONTEXT_ISOLATION.md` | `6,602` | `1,651` | contoh ADR concern strategis |

### Pack Baca Aktif

| Pack | Komposisi | Est. Tokens | Est. Ideal Context Window |
| --- | --- | ---: | ---: |
| `Minimum routing pack` | `AGENTS + thin registry + validation log` | `6,186` | `9,517` |
| `Default execution pack (child concern)` | `minimum routing + single-path + 1 child TODO` | `11,002` | `16,927` |
| `Default execution pack (parent concern)` | `minimum routing + single-path + 1 parent TODO` | `11,324` | `17,422` |
| `Extended governance pack (child concern)` | `default child + playbook` | `14,143` | `21,759` |
| `Extended governance pack (parent concern + ADR)` | `default parent + playbook + 1 ADR` | `16,116` | `24,794` |

## Estimasi Konteks Ideal Repo Saat Ini

- Band kerja harian markdown aktif:
  - `10k-16.5k` estimated tokens.
- Band ideal context window repo saat ini:
  - `17k-25k` tokens.
- Interpretasi operasional:
  - `~17k` cukup untuk jalur harian dengan parent concern aktif,
  - `~25k` dibutuhkan saat doc-hardening + ADR + playbook ikut dimuat bersamaan,
  - jika model/context runner berada di bawah band ini, prioritas pertama adalah thinning pack, bukan menambah detail dokumen.

## Soft Cap per Artefak

| Artefak | Soft Cap Chars | Soft Cap Est. Tokens | Tindakan Jika Terlampaui |
| --- | ---: | ---: | --- |
| `AGENTS.md` | `18,000` | `4,500` | pindahkan detail operasional ke process docs, jangan menambah histori |
| `AI_SINGLE_PATH_ARCHITECTURE.md` | `12,000` | `3,000` | pertahankan routing inti, pindahkan detail ke playbook/doc khusus |
| `AI_FRIENDLY_EXECUTION_PLAYBOOK.md` | `12,500` | `3,125` | ringkas summary pattern; detail ke annex |
| `TTM25R1` thin registry | `6,500` | `1,625` | pindahkan closure/histori ke snapshot arsip |
| `OPERATIONAL_VALIDATION_LOG.md` | `14,000` | `3,500` | pindahkan detail ke log kuartalan/periodik |
| `TODO concern aktif` | `8,000` | `2,000` | pecah child concern atau pindahkan histori ke arsip setelah `done` |
| `ADR aktif` | `7,000` | `1,750` | ringkas trade-off; detail eksekusi tetap di TODO/log |
| `PLAYBOOK_PATTERN_DETAILS annex` | `50,000` | `12,500` | tetap on-demand; shard per tema/rentang pattern jika terlampaui |

## Aturan Operasional

1. Pack baca default mengikuti urutan:
   - `AGENTS.md`
   - `TTM25R1` thin registry
   - `OPERATIONAL_VALIDATION_LOG.md`
   - `1 TODO concern aktif`
2. Tambahkan `AI_SINGLE_PATH_ARCHITECTURE.md` hanya saat concern perlu routing/lock boundary yang lebih tegas.
3. Tambahkan `AI_FRIENDLY_EXECUTION_PLAYBOOK.md` hanya saat perlu pattern reusable atau hardening proses.
4. Tambahkan ADR aktif hanya saat concern menyentuh keputusan strategis lintas concern.
5. `OPERATIONAL_VALIDATION_LOG.md` index aktif hanya memuat concern `planned/in-progress` dan pointer closure ringkas; detail concern `done` harus dipindah ke arsip periodik.
6. `AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md` adalah annex on-demand dan tidak masuk default pack; buka section yang relevan saja.
7. Jika soft cap file atau band pack terlewati, lakukan thinning/archive pada sesi yang sama sebelum menambah detail baru.
8. Gunakan `scripts/audit_markdown_governance.ps1` sebagai audit otomatis lokal/CI untuk soft cap, thin registry, index aktif, dan guard annex on-demand.

## Expansion Policy Saat Context Window AI Meningkat

Hitung ulang space baru:

- `new_markdown_space = floor(new_ideal_context_window * 0.65)`

Aturan ekspansi:

1. Wajib pertahankan reserve minimal `35%`; reserve tidak boleh dikonsumsi hanya karena model lebih besar.
2. Ekspansi baru boleh dilakukan jika `new_markdown_space` setidaknya `15%` di atas extended governance pack aktif terakhir.
3. Urutan ekspansi:
   - `OPERATIONAL_VALIDATION_LOG.md`: tambah snapshot concern aktif yang lebih kaya sebelum membuka arsip.
   - `TTM25R1` thin registry: tambah metadata ringkas concern aktif bila memang membantu routing.
   - `AI_FRIENDLY_EXECUTION_PLAYBOOK.md`: naikkan sedikit detail summary pattern tanpa memindahkan annex ke file utama.
   - `Default pack`: izinkan kombinasi `parent TODO + child TODO` atau `1 ADR` ekstra dalam sesi strategis.
   - `AGENTS.md`: hanya bertambah jika ada invariant baru; bukan karena ruang model sedang longgar.
4. Setelah ekspansi, update dokumen ini, `AGENTS.md`, dan `AI_SINGLE_PATH_ARCHITECTURE.md` jika load order atau budget band berubah.

## Trigger Compaction

Lakukan compaction pada sesi yang sama jika salah satu terjadi:

- default execution pack melampaui `18k` estimated markdown tokens,
- extended governance pack melampaui `65%` budget context window runner yang sedang dipakai,
- file aktif melewati soft cap lebih dari `10%`,
- `OPERATIONAL_VALIDATION_LOG.md` kembali memuat detail closure concern `done` sehingga index aktif berubah menjadi log historis,
- `TTM25R1` kembali memuat daftar concern `done` penuh sehingga tidak lagi berfungsi sebagai thin registry,
- TODO `done` tetap berada di root `docs/process/` tanpa alasan SOT aktif.

## Command Audit Ringkas

Gunakan command berikut untuk audit ulang baseline cepat:

```bash
wc -lcw AGENTS.md \
  docs/process/AI_SINGLE_PATH_ARCHITECTURE.md \
  docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md \
  docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md \
  docs/process/OPERATIONAL_VALIDATION_LOG.md
```

Untuk estimasi pack, jumlahkan `chars` file terkait lalu hitung `ceil(chars / 4)`.

Audit otomatis yang direkomendasikan:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/audit_markdown_governance.ps1
```
