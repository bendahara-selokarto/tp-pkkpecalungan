# Referensi Domain (Lokal)

Folder ini menyimpan bahan referensi domain (PDF/Excel/screenshot) yang dipakai untuk sinkronisasi kontrak.

## Struktur Target

- `docs/referensi/canonical/` untuk dokumen referensi utama (pedoman/lampiran PDF).
- `docs/referensi/supporting/` untuk bahan pendamping (cara pengisian, workbook, docx).
- `docs/referensi/evidence/screenshots/` untuk bukti visual/screenshot.
- `docs/referensi/_local/` tetap untuk artefak non-tracked.

## Konvensi Nama

- Huruf kecil, tanpa spasi (gunakan `-`).
- Berbasis `doc-key` yang deskriptif.
- Contoh: `rakernas-x.pdf`, `lampiran-4-22-cara-pengisian.pdf`, `laporan-tahunan-pkk-2025.docx`.

## Manifest Migrasi

- Source of truth migrasi path ada di `docs/referensi/MIGRATION_MANIFEST.md`.
- Path lama tetap dipakai sampai entry manifest dipindahkan dan seluruh referensi sudah disinkronkan.

## Catatan Transisi

- Saat ini beberapa dokumen masih berada di root `docs/referensi/` (mis. `Rakernas X.pdf`).
- Jangan memindahkan file referensi tanpa memperbarui manifest dan referensi di `docs/domain/**`/`docs/process/**`.
