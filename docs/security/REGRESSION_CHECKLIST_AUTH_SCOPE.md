# Regression Checklist Auth Scope (T8)

Tujuan:
- Menjaga konsistensi `role`, `scope`, dan `area_id` agar tidak terjadi bypass akses lintas level wilayah.
- Menjadi checklist wajib sebelum merge perubahan yang menyentuh policy, middleware `scope.role`, route report, atau scope service.

Sumber acuan:
- `AGENTS.md` (hard invariants role/scope/area)
- `docs/security/AUTH_COHERENCE_MATRIX.md`
- `docs/security/POLICY_SCOPE_AUDIT_REPORT.md`

## 1) Skenario Wajib (Stale Metadata)

| ID | Skenario | Setup user stale | Ekspektasi | Mapping test yang menutup |
| --- | --- | --- | --- | --- |
| `A1` | `scope=desa` tetapi `area_id` mengarah ke area level `kecamatan` | role `admin-desa`, scope `desa`, area kecamatan | request route `desa.*.report` ditolak `403` | `tests/Feature/AnggotaTimPenggerakReportPrintTest.php`, `tests/Feature/KaderKhususReportPrintTest.php`, `tests/Feature/AgendaSuratReportPrintTest.php`, `tests/Feature/BukuKeuanganReportPrintTest.php`, `tests/Feature/BklReportPrintTest.php`, `tests/Feature/BkrReportPrintTest.php`, `tests/Feature/DataWargaReportPrintTest.php`, `tests/Feature/DataKegiatanWargaReportPrintTest.php`, `tests/Feature/DataKeluargaReportPrintTest.php`, `tests/Feature/DataPemanfaatanTanahPekaranganHatinyaPkkReportPrintTest.php`, `tests/Feature/DataIndustriRumahTanggaReportPrintTest.php`, `tests/Feature/DataPelatihanKaderReportPrintTest.php`, `tests/Feature/WarungPkkReportPrintTest.php`, `tests/Feature/TamanBacaanReportPrintTest.php`, `tests/Feature/KoperasiReportPrintTest.php`, `tests/Feature/KejarPaketReportPrintTest.php`, `tests/Feature/PosyanduReportPrintTest.php`, `tests/Feature/SimulasiPenyuluhanReportPrintTest.php`, `tests/Feature/CatatanKeluargaReportPrintTest.php`, `tests/Feature/StructuredDomainReportPrintTest.php`, `tests/Feature/AnggotaDanKaderGabunganReportPrintTest.php`, `tests/Feature/PrestasiLombaReportPrintTest.php`, `tests/Feature/ProgramPrioritasReportPrintTest.php` |
| `A2` | `scope=kecamatan` tetapi `area_id` mengarah ke area level `desa` | role `admin-kecamatan`, scope `kecamatan`, area desa | request route `kecamatan.*.report` ditolak `403` atau scope dinetralkan (no access) | `tests/Feature/KecamatanReportReverseAreaMismatchTest.php` (multi-route matrix), `tests/Feature/AgendaSuratReportPrintTest.php` (`ekspedisi.report`), `tests/Feature/DashboardActivityChartTest.php` (`role_kecamatan_tetapi_area_level_desa`) |

Catatan:
- `A2` sengaja diverifikasi di endpoint report (`agenda-surat ekspedisi`) dan agregasi dashboard untuk memastikan guard berlaku lintas entry-point.

## 2) Checklist Eksekusi Review

- [ ] Cek route report tetap berada di scope middleware:
  - Jalankan `php artisan route:list --name=report`
- [ ] Jalankan regression stale metadata route report:
  - `php artisan test --filter=scope_metadata_tidak_sinkron`
- [ ] Jalankan regression mismatch role-level area:
  - `php artisan test --filter=role_dan_level_area_tidak_sinkron`
  - `php artisan test --filter=role_kecamatan_tetapi_area_level_desa`
- [ ] Pastikan hasil skenario `A1` dan `A2` sesuai ekspektasi (`403` atau scope netral tanpa data bocor).
- [ ] Jika ada deviasi, catat ke `docs/domain/DOMAIN_DEVIATION_LOG.md` sebelum merge.

## 3) Bukti Validasi T8

Perintah yang dijalankan:
- `php artisan route:list --name=report`
  - hasil: `52` route report terdaftar (desa + kecamatan).
- `php artisan test --filter=scope_metadata_tidak_sinkron`
  - hasil: `25` test pass.
- `php artisan test --filter=role_dan_level_area_tidak_sinkron`
  - hasil: `1` test pass.
- `php artisan test --filter=role_kecamatan_tetapi_area_level_desa`
  - hasil: `20` test pass (termasuk matrix multi-route untuk report kecamatan).

Status:
- `PASS` untuk checklist regression auth-scope pada baseline saat ini.
