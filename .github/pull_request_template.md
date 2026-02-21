## Summary

- Ringkasan perubahan:
- Ruang lingkup modul/lampiran:
- Risiko utama:

## Domain Contract Impact

- [ ] Tidak ada perubahan kontrak domain.
- [ ] Ada perubahan kontrak domain (schema/field/label/report/policy) dan artefak wajib sudah diupdate.

Dokumen referensi:
- `docs/process/CHANGE_GATE_DOMAIN_CONTRACT.md`
- `docs/process/RELEASE_CHECKLIST_PDF.md`

## Mandatory Checklist (Jika Kontrak Domain/PDF/Auth Berubah)

- [ ] `docs/domain/DOMAIN_CONTRACT_MATRIX.md` diperbarui.
- [ ] `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md` diperbarui (jika istilah berubah).
- [ ] `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md` diperbarui (jika format PDF berubah).
- [ ] `tests/Fixtures/pdf-baseline/*.json` diperbarui (jika kontrak PDF berubah).
- [ ] `docs/security/AUTH_COHERENCE_MATRIX.md` diperbarui (jika auth/scope berubah).
- [ ] `docs/security/REGRESSION_CHECKLIST_AUTH_SCOPE.md` diperbarui (jika auth/scope berubah).
- [ ] `docs/domain/DOMAIN_DEVIATION_LOG.md` diperbarui (jika ada deviasi baru).
- [ ] `AGENTS.md` / `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md` / `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md` diperbarui jika ada perubahan canonical.

## Validation Evidence

Tempel output/ringkasan hasil:

- [ ] `php artisan route:list --name=report`
- [ ] `php artisan test --filter=PdfBaselineFixtureComplianceTest`
- [ ] `php artisan test --filter=scope_metadata_tidak_sinkron`
- [ ] `php artisan test --filter=header_kolom_pdf` (jika perubahan PDF prioritas)
- [ ] `php artisan test --filter=role_dan_level_area_tidak_sinkron` (jika perubahan auth/scope)
- [ ] `php artisan test --filter=role_kecamatan_tetapi_area_level_desa` (jika perubahan auth/scope)
- [ ] `php artisan test` (jika perubahan signifikan)

## Manual PDF Verification (Release Gate)

Untuk modul terdampak:
- [ ] Sample PDF `desa` diverifikasi.
- [ ] Sample PDF `kecamatan` diverifikasi.
- [ ] Judul/header/urutan kolom sesuai pedoman.
- [ ] Metadata cetak (`area`, `printedBy`, `printedAt`) tampil.
- [ ] Orientasi default `landscape`.
