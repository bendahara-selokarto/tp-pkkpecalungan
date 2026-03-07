# TODO Archive Strategy

Tanggal efektif: 2026-03-07  
Status: `active`

## Tujuan

Menjaga `docs/process` tetap mudah dinavigasi saat jumlah TODO bertambah.

## Prinsip

- TODO aktif (`planned`, `in-progress`) tetap di root `docs/process/`.
- TODO `done` dipindah berkala ke `docs/process/archive/<YYYY_MM>/`.
- Concern yang menjadi Source of Truth (SOT) tetap boleh berada di root meski `done` jika masih jadi acuan aktif lintas concern.

## Cadence

- Review arsip: mingguan (setiap Senin) atau setiap selesai wave besar.
- Batch arsip minimum: TODO `done` yang tidak lagi menjadi SOT aktif dan tidak disentuh > 7 hari.

## Prosedur Operasional

1. Identifikasi kandidat arsip:
   - status `done`,
   - bukan SOT aktif di registry,
   - tidak sedang direferensikan concern `in-progress`.
2. Pindahkan file ke folder bulan berjalan:
   - `docs/process/archive/<YYYY_MM>/`.
3. Sinkronkan referensi:
   - update registry SOT jika ada path yang berubah,
   - update link dokumen process lain yang merujuk file terdampak.
4. Catat eksekusi arsip ke `docs/process/OPERATIONAL_VALIDATION_LOG.md`.

## Guardrail

- Dilarang mengarsipkan TODO `planned` atau `in-progress`.
- Dilarang mengarsipkan TODO `done` yang masih menjadi SOT concern aktif.
- Semua pemindahan harus `commit by concern`.

