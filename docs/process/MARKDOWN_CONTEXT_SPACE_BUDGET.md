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
| `AGENTS.md` | `15,653` | `3,913` | kontrak tertinggi; harus tetap padat |
| `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md` | `10,654` | `2,664` | routing operasional default |
| `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md` | `11,344` | `2,836` | registry pattern ringkas |
| `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md` | `5,817` | `1,454` | thin registry aktif |
| `docs/process/OPERATIONAL_VALIDATION_LOG.md` | `12,928` | `3,232` | snapshot aktif + pointer arsip |
| `docs/process/TODO_SPA26A1_ROADMAP_OPTIMASI_BERTAHAP_INERTIA_TANPA_MIGRASI_SPA_MURNI_2026_03_08.md` | `7,726` | `1,932` | contoh parent concern aktif |
| `docs/process/TODO_DBT26A1_PILOT_DASHBOARD_WAVE_5_FETCH_FAILURE_TELEMETRY_2026_03_09.md` | `3,404` | `851` | contoh child concern aktif |
| `docs/adr/ADR_0005_TAHUN_ANGGARAN_CONTEXT_ISOLATION.md` | `6,602` | `1,650` | contoh ADR concern strategis |

### Pack Baca Aktif

| Pack | Komposisi | Est. Tokens | Est. Ideal Context Window |
| --- | --- | ---: | ---: |
| `Minimum routing pack` | `AGENTS + thin registry + validation log` | `8,600` | `13,231` |
| `Default execution pack (child concern)` | `minimum routing + single-path + 1 child TODO` | `12,114` | `18,637` |
| `Default execution pack (parent concern)` | `minimum routing + single-path + 1 parent TODO` | `13,194` | `20,299` |
| `Extended governance pack (child concern)` | `default child + playbook` | `14,950` | `23,000` |
| `Extended governance pack (parent concern + ADR)` | `default parent + playbook + 1 ADR` | `17,681` | `27,202` |

## Estimasi Konteks Ideal Repo Saat Ini

- Band kerja harian markdown aktif:
  - `12k-18k` estimated tokens.
- Band ideal context window repo saat ini:
  - `20k-28k` tokens.
- Interpretasi operasional:
  - `~20k` cukup untuk jalur harian dengan parent concern aktif,
  - `~28k` dibutuhkan saat doc-hardening + ADR + playbook ikut dimuat bersamaan,
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

## Aturan Operasional

1. Pack baca default mengikuti urutan:
   - `AGENTS.md`
   - `TTM25R1` thin registry
   - `OPERATIONAL_VALIDATION_LOG.md`
   - `1 TODO concern aktif`
2. Tambahkan `AI_SINGLE_PATH_ARCHITECTURE.md` hanya saat concern perlu routing/lock boundary yang lebih tegas.
3. Tambahkan `AI_FRIENDLY_EXECUTION_PLAYBOOK.md` hanya saat perlu pattern reusable atau hardening proses.
4. Tambahkan ADR aktif hanya saat concern menyentuh keputusan strategis lintas concern.
5. Jika soft cap file atau band pack terlewati, lakukan thinning/archive pada sesi yang sama sebelum menambah detail baru.

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
- TODO `done` tetap berada di root `docs/process/` tanpa alasan SOT aktif.

## Command Audit Ringkas

Gunakan command berikut untuk audit ulang baseline:

```bash
wc -lcw AGENTS.md \
  docs/process/AI_SINGLE_PATH_ARCHITECTURE.md \
  docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md \
  docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md \
  docs/process/OPERATIONAL_VALIDATION_LOG.md
```

Untuk estimasi pack, jumlahkan `chars` file terkait lalu hitung `ceil(chars / 4)`.
