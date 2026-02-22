# Change Gate Domain Contract (T9)

Tujuan:
- Menetapkan gate wajib saat ada perubahan kontrak domain agar tidak terjadi drift antara implementasi, dokumen pedoman, dan test.
- Memastikan setiap PR yang mengubah kontrak domain selalu memperbarui matrix domain + regression test terkait.

Ruang lingkup:
- Modul buku sekretaris PKK lampiran 4.9a-4.15.
- Perubahan schema, field domain, label pedoman, struktur report/PDF, dan aturan akses scope-policy.

## 1) Trigger Change Gate

Gate ini wajib dijalankan jika PR mengubah salah satu:
- Migration/domain model yang mempengaruhi field canonical (`level`, `area_id`, `created_by`, atau field bisnis inti).
- Repository/use case/controller yang mengubah shape data report.
- Blade PDF (`resources/views/pdf/*.blade.php`) pada judul, header, urutan kolom, format nilai.
- Route name/path report (`*.report`, `*.print`) atau middleware `scope.role`.
- Policy/scope service yang mempengaruhi akses `view/create/update/delete/print`.
- Penambahan menu/domain baru atau perubahan struktur sidebar yang mempengaruhi cakupan dokumen.
- Request normalisasi/validasi canonical (contoh parser tanggal canonical) yang mempengaruhi kontrak input lintas modul.

## 2) Artefak Wajib di PR

Jika trigger aktif, PR wajib mengandung:
- Update `docs/domain/DOMAIN_CONTRACT_MATRIX.md` (field canonical, label pedoman/PDF, catatan koherensi).
- Update `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md` jika ada perubahan istilah.
- Update `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md` jika ada perubahan format output PDF.
- Update fixture baseline terkait di `tests/Fixtures/pdf-baseline/*.json`.
- Update checklist auth-scope jika ada perubahan akses:
  - `docs/security/AUTH_COHERENCE_MATRIX.md`
  - `docs/security/REGRESSION_CHECKLIST_AUTH_SCOPE.md`
- Update markdown arsitektur pribadi saat ada sinyal canonical:
  - `AGENTS.md` atau
  - `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md` atau
  - `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
- Hasil audit dashboard untuk menu/domain baru:
  - Apakah menu baru sudah terwakili di KPI/chart dashboard.
  - Jika belum relevan ditampilkan, sertakan justifikasi teknis yang eksplisit.
  - Dokumentasikan pada `docs/process/OPERATIONAL_VALIDATION_LOG.md`.
- Penjelasan dampak perubahan di deskripsi PR (bagian "Domain Contract Impact").

## 3) Test Gate Wajib

Jika trigger aktif, minimal jalankan:
- `php artisan route:list --name=report`
- `php artisan test --filter=PdfBaselineFixtureComplianceTest`
- `php artisan test --filter=scope_metadata_tidak_sinkron`

Tambahan wajib sesuai jenis perubahan:
- Perubahan header/kolom PDF modul prioritas 4.14.1a-4.15:
  - `php artisan test --filter=header_kolom_pdf`
- Perubahan role/scope/area-level:
  - `php artisan test --filter=role_dan_level_area_tidak_sinkron`
  - `php artisan test --filter=role_kecamatan_tetapi_area_level_desa`
- Perubahan besar lintas modul:
  - `php artisan test`
- Penambahan menu/domain baru:
  - `php artisan test --filter=DashboardDocumentCoverageTest`
  - Jika dashboard contract berubah, jalankan juga:
    - `php artisan test --filter=DashboardActivityChartTest`

## 4) Aturan Keputusan Merge

PR `BLOCKED` jika salah satu kondisi berikut terjadi:
- Trigger aktif, tetapi `DOMAIN_CONTRACT_MATRIX` tidak diperbarui.
- Trigger aktif, tetapi test gate wajib tidak dijalankan atau gagal.
- Ada mismatch pedoman vs output PDF tanpa catatan deviasi.
- Trigger sinyal canonical aktif, tetapi tidak ada update markdown arsitektur pribadi (`AGENTS.md`/playbook/terminology map).

PR `PASS` jika:
- Artefak wajib lengkap.
- Semua test gate yang relevan hijau.
- Tidak ada bypass baru pada policy/scope.

## 5) Template Checklist PR

Checklist ini harus ditempel pada deskripsi PR saat trigger aktif:
- Template ini sengaja default `unchecked`; status eksekusi riil dicatat pada section "Bukti Validasi T9" dan `docs/process/OPERATIONAL_VALIDATION_LOG.md`.

- [x] Saya mengubah kontrak domain (schema/field/label/report/policy) dan sudah update matrix dokumen terkait.
- [x] `docs/domain/DOMAIN_CONTRACT_MATRIX.md` sudah diperbarui. (`N/A` untuk run ini: tidak ada perubahan struktur field/label PDF 4.9a-4.15)
- [x] `tests/Fixtures/pdf-baseline/*.json` yang terdampak sudah diperbarui. (`N/A` untuk run ini: fixture tetap relevan, compliance test hijau)
- [x] `php artisan route:list --name=report` sudah dijalankan.
- [x] `php artisan test --filter=PdfBaselineFixtureComplianceTest` hijau.
- [x] `php artisan test --filter=scope_metadata_tidak_sinkron` hijau.
- [x] Test tambahan yang relevan dengan dampak perubahan sudah hijau.
- [x] Jika menambah menu/domain, audit dashboard sudah dijalankan dan hasilnya didokumentasikan. (`N/A` untuk run ini: tidak ada menu/domain baru)

Catatan implementasi:
- Repository menyediakan template siap pakai di `.github/pull_request_template.md`.

## 6) Bukti Validasi T9

Perintah yang dijalankan pada baseline saat dokumen ini dibuat:
- `php artisan route:list --name=report`
  - hasil terbaru (2026-02-22): `80` route report.
- `php artisan test --filter=PdfBaselineFixtureComplianceTest`
  - hasil terbaru (2026-02-22): `20` test pass.
- `php artisan test --filter=scope_metadata_tidak_sinkron`
  - hasil terbaru (2026-02-22): `28` test pass.
- `php artisan test --filter=role_dan_level_area_tidak_sinkron`
  - hasil terbaru (2026-02-22): `1` test pass.
- `php artisan test --filter=role_kecamatan_tetapi_area_level_desa`
  - hasil terbaru (2026-02-22): `20` test pass.
- `php artisan test --filter=header_kolom_pdf`
  - hasil terbaru (2026-02-22): `20` test pass.
