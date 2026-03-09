# Taksonomi Istilah Kerja

Status: `non-canonical`  
Indexed: `no`  
Decision State: `working-agreement`  
Last Updated: `2026-03-10`  
Entry point keyword: `diskusi-md`

## Status Dokumen

Dokumen ini sekarang menjadi **entry memo tipis** untuk vocabulary diskusi.

Detail definisi sudah dipecah ke struktur `index + annex on-demand` agar agent tidak perlu memuat satu file monolitik setiap kali membaca istilah.

## Cara Pakai

Urutan baca yang disarankan:

1. `docs/discussion/vocabulary/VOCABULARY_INDEX.md`
2. Annex yang relevan dengan istilah yang sedang dibahas
3. Arsip monolitik hanya jika perlu melihat histori atau versi sebelum pemecahan

## Peta File

- `docs/discussion/vocabulary/VOCABULARY_INDEX.md`
  - index tipis seluruh istilah kerja.
- `docs/discussion/vocabulary/VOCAB_FLOW.md`
  - istilah flow, concern, router, dan pattern.
- `docs/discussion/vocabulary/VOCAB_PRODUCT_STRUCTURE.md`
  - istilah struktur produk seperti domain, modul, fitur, menu, dan page.
- `docs/discussion/vocabulary/VOCAB_DATA_TECHNICAL.md`
  - istilah data dan teknis seperti boundary, kontrak data, source of truth, projection, snapshot, override, dan sejenisnya.
- `docs/discussion/vocabulary/archive/DISKUSI_2026_03_09_TAKSONOMI_ISTILAH_KERJA_FULL_2026_03_10.md`
  - arsip versi monolitik sebelum dipecah.

## Tujuan Pemecahan

- Menjaga vocabulary tetap bisa dirujuk agent.
- Menghindari beban token berlebihan pada context default.
- Membuat retrieval lebih deterministik: baca index dulu, buka detail hanya jika perlu.
