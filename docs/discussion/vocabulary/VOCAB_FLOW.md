# Vocabulary Flow

Status: `non-canonical`  
Indexed: `no`  
Retrieval Mode: `on-demand`

## Cakupan

File ini memuat istilah untuk jalur kerja, concern, routing, dan pattern.

### Concern

`Concern` adalah unit pembahasan, unit perubahan, atau unit audit.

Catatan:

- concern tidak harus sama dengan modul,
- concern bisa sangat sempit,
- concern bisa lintas modul.

### Flow analisa concern

`Flow analisa concern` adalah jalur kerja untuk memahami satu concern secara terstruktur sebelum atau selama eksekusi.

Bentuk default di repo ini:

- `Classify`,
- `Self-Reflective Checkpoint`,
- `Contract Lock`,
- `Scoped Read`,
- penentuan `Validation Ladder`.

### Router concern

`Router concern` adalah peta concern canonical berdasarkan jenis permintaan.

Fungsinya:

- memilih concern utama,
- menentukan file primer,
- menentukan validasi minimum.

Concern canonical yang sudah ada:

- `Authorization & visibility`
- `Domain module delivery`
- `Dashboard representation`
- `Pre-release upgrade track`
- `Contract sync doc`
- `Copywriting hardening`
- `UI/UX auditability gate`
- `Arsitektur & risiko`
- `ADR governance`

### Pattern

`Pattern` adalah flow reusable untuk kondisi tertentu yang sudah didokumentasikan.

Contoh pattern penting:

- `P-001` = scoped analysis + diff-first,
- `P-017` = zero-ambiguity single path routing,
- `P-022` = self-reflective routing,
- `P-025` = UI/UX auditability gate.

### Alur / Use case

`Alur` atau `use case` adalah skenario end-to-end untuk satu tujuan user.

Contoh:

- input data,
- cetak laporan,
- rollback override.

## Beda Cepat

- `Concern` = unit yang sedang dibahas atau diubah.
- `Flow analisa concern` = cara menganalisis concern itu.
- `Router concern` = alat memilih concern canonical yang tepat.
- `Pattern` = recipe reusable untuk kasus tertentu.
- `Use case` = skenario kerja user.
