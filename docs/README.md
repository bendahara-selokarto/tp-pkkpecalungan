# Dokumentasi Proyek

Struktur dokumentasi dibagi per concern agar mudah dicari dan dipelihara.

## Root (Dokumen Inti)
- `AGENTS.md`: kontrak eksekusi AI dan aturan teknis utama.
- `README.md`: panduan manusia untuk penggunaan dan pengembangan.
- `PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md`: ringkasan sinkronisasi pedoman domain utama (canonical lokal aktif).

## docs/domain
- Kontrak domain, deviasi, normalisasi istilah, dan pedoman domain ekstensi.
- File utama:
  - `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
  - `docs/domain/DOMAIN_DEVIATION_LOG.md`
  - `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
  - `docs/domain/PEDOMAN_DOMAIN_UTAMA_202_211.md`
  - `docs/domain/ADJUSTMENT_PLAN_4_14_1A_DAFTAR_WARGA_TP_PKK.md`

## docs/pdf
- Checklist dan validasi format output PDF.
- File utama:
  - `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`
  - `docs/pdf/VALIDASI_FORMAT_BUKU_SEKRETARIS_PDF.md`

## docs/process
- Runbook, rencana eksekusi, gate operasional, dan log validasi.
- File utama:
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
  - `docs/process/RUNBOOK_429_RATE_LIMITER.md`
  - `docs/process/OPERATIONAL_VALIDATION_LOG.md`
  - `docs/process/RELEASE_CHECKLIST_PDF.md`

## docs/user-guide
- Panduan penggunaan sistem untuk pengguna akhir per peran dan per alur kerja.
- File utama:
  - `docs/user-guide/README.md`
  - `docs/user-guide/mulai-cepat.md`
  - `docs/user-guide/peran/*.md`
  - `docs/user-guide/alur/*.md`
  - `docs/user-guide/faq.md`
  - `docs/user-guide/print/*.html` (versi siap cetak)

## docs/security
- Audit policy/scope dan checklist regresi akses.

## docs/ui
- Inventaris dan audit konsistensi UI.

## Konvensi Nama File
- Gunakan pola `UPPER_SNAKE_CASE` untuk dokumen proses/kontrak/checklist.
- Gunakan `README.md` untuk index folder.
- Hindari membuat dokumen operasional baru di root project; tempatkan di `docs/<concern>/`.
