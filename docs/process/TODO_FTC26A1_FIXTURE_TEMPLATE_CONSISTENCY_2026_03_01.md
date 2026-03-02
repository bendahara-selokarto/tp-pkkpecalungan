# TODO FTC26A1 Fixture Template Consistency 2026-03-01

Tanggal: 2026-03-01  
Status: `active` (`isolated-regression-fix`)

## Konteks
- Setelah hardening memory-limit test gate, blocker residual bergeser ke concern fixture/template consistency.
- Concern ini dipisah dari process routing agar fokus.

## Target Hasil
- Satu jalur concern khusus untuk menutup mismatch fixture/template pada test feature.
- Root cause + fallback teknis terdokumentasi.
- Gate concern lain tidak tertahan backlog ini.

## Langkah Eksekusi
- [x] `F1` Inventarisasi daftar kegagalan residual (`template .docx` hilang + mismatch token fixture).
- [x] `F2` Tetapkan mapping file/template canonical yang hilang atau berubah.
- [x] `F3` Sinkronkan fixture token dengan kontrak judul yang aktif.
- [ ] `F4` Jalankan targeted feature tests yang terdampak.
- [ ] `F5` Catat hasil penutupan di `OPERATIONAL_VALIDATION_LOG.md`.

## Validasi
- [ ] `php artisan test --filter=LaporanTahunanPkkReportPrintTest`
- [ ] `php artisan test --filter=PdfBaselineFixtureComplianceTest`
- [ ] Tidak ada fail residual terkait fixture/template consistency.

## Risiko
- [ ] Risiko false-fix jika hanya mengubah expected test tanpa verifikasi kontrak dokumen canonical.
- [ ] Risiko regresi report print lain jika template dipindahkan tanpa mapping.

## Keputusan Dikunci
- [x] Concern `fixture/template consistency` dipisah sebagai jalur tunggal tersendiri.
- [x] Concern ini tidak mencampur perubahan routing process kecuali ada dampak lintas concern valid.

## ADR Terkait
- Tidak wajib ADR baru pada tahap ini (scope perbaikan regresi terisolasi).
