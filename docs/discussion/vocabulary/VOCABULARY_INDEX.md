# Vocabulary Index

Status: `non-canonical`  
Indexed: `no`  
Retrieval Mode: `index-first`  
Entry point keyword: `diskusi-md`

## Tujuan

Menjadi index tipis untuk istilah kerja yang dipakai dalam percakapan user-agent.

## Aturan Pakai

- Baca file ini terlebih dahulu.
- Buka annex hanya untuk kategori istilah yang benar-benar sedang dibahas.
- Jangan buka arsip monolitik kecuali user meminta histori atau perlu audit perubahan istilah.
- Jika satu istilah sudah stabil dan berdampak ke kontrak repo, promosi ringkasannya ke dokumen canonical yang relevan.

## Peta Annex

| Annex | Fokus |
| --- | --- |
| `VOCAB_FLOW.md` | flow analisa concern, router concern, pattern, concern, use case |
| `VOCAB_PRODUCT_STRUCTURE.md` | sistem, kapabilitas, domain, modul, submodul, fitur, menu, halaman, slug |
| `VOCAB_DATA_TECHNICAL.md` | boundary, kontrak data, sumber kebenaran, skema data, aggregator, projection, snapshot, baseline, jejak data, override |

## Index Istilah

| Istilah | Kategori | Definisi singkat | Detail |
| --- | --- | --- | --- |
| `Sistem` / `Aplikasi` | product-structure | Produk utuh yang dipakai user | `VOCAB_PRODUCT_STRUCTURE.md` |
| `Kapabilitas` | product-structure | Kumpulan modul untuk satu tujuan besar | `VOCAB_PRODUCT_STRUCTURE.md` |
| `Domain` | product-structure | Boundary masalah bisnis dan data | `VOCAB_PRODUCT_STRUCTURE.md` |
| `Modul` | product-structure | Unit fitur dengan akses, route/menu, dan boundary backend | `VOCAB_PRODUCT_STRUCTURE.md` |
| `Submodul` | product-structure | Pecahan modul yang masih cukup besar | `VOCAB_PRODUCT_STRUCTURE.md` |
| `Fitur` | product-structure | Kemampuan spesifik di dalam modul | `VOCAB_PRODUCT_STRUCTURE.md` |
| `Menu` | product-structure | Entry navigasi ke modul/halaman | `VOCAB_PRODUCT_STRUCTURE.md` |
| `Halaman` / `Page` | product-structure | Representasi UI untuk modul atau alur | `VOCAB_PRODUCT_STRUCTURE.md` |
| `Slug` | product-structure | Identifier teknis stabil | `VOCAB_PRODUCT_STRUCTURE.md` |
| `Concern` | flow | Unit pembahasan atau unit perubahan | `VOCAB_FLOW.md` |
| `Flow analisa concern` | flow | Jalur analisa concern dari klasifikasi sampai validasi awal | `VOCAB_FLOW.md` |
| `Router concern` | flow | Peta concern canonical berdasarkan jenis permintaan | `VOCAB_FLOW.md` |
| `Pattern` | flow | Flow reusable untuk kondisi tertentu | `VOCAB_FLOW.md` |
| `Alur` / `Use case` | flow | Skenario end-to-end untuk tujuan user | `VOCAB_FLOW.md` |
| `Boundary` | data-technical | Batas tanggung jawab layer, modul, atau akses | `VOCAB_DATA_TECHNICAL.md` |
| `Kontrak data` | data-technical | Bentuk data yang disepakati antar sisi sistem | `VOCAB_DATA_TECHNICAL.md` |
| `Sumber kebenaran` | data-technical | Referensi otoritatif saat ada konflik | `VOCAB_DATA_TECHNICAL.md` |
| `Skema data` | data-technical | Struktur simpan data: tabel, kolom, relasi | `VOCAB_DATA_TECHNICAL.md` |
| `Aggregator` | data-technical | Penggabung beberapa sumber data | `VOCAB_DATA_TECHNICAL.md` |
| `Projection` | data-technical | Bentuk turunan untuk tampilan atau report | `VOCAB_DATA_TECHNICAL.md` |
| `Snapshot` | data-technical | Potret status pada satu waktu | `VOCAB_DATA_TECHNICAL.md` |
| `Baseline` | data-technical | Acuan default awal | `VOCAB_DATA_TECHNICAL.md` |
| `Jejak data` | data-technical | Asal dan pemakaian data lintas modul | `VOCAB_DATA_TECHNICAL.md` |
| `Relasi modul` | data-technical | Keterkaitan antar modul | `VOCAB_DATA_TECHNICAL.md` |
| `Alur data` | data-technical | Perjalanan data dari input sampai output | `VOCAB_DATA_TECHNICAL.md` |
| `Override` | data-technical | Pengecualian terkontrol dari baseline | `VOCAB_DATA_TECHNICAL.md` |
| `CRUD` | data-technical | Pola create, read, update, delete | `VOCAB_DATA_TECHNICAL.md` |

## Retrieval Contract

Saat user menulis:

- `diskusi-md, definisikan modul`
  - baca index ini, lalu buka `VOCAB_PRODUCT_STRUCTURE.md`.
- `diskusi-md, pakai flow analisa concern`
  - baca index ini, lalu buka `VOCAB_FLOW.md`.
- `diskusi-md, cek source of truth data ini`
  - baca index ini, lalu buka `VOCAB_DATA_TECHNICAL.md`.
