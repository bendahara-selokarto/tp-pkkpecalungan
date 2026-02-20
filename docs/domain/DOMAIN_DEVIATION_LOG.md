# Domain Deviation Log (T11)

Tujuan:
- Menjadi register resmi untuk semua deviasi antara implementasi domain aplikasi dan pedoman domain utama.
- Menjamin setiap deviasi memiliki jejak keputusan, dampak, mitigasi, dan status penyelesaian.

Sumber canonical:
- https://pubhtml5.com/zsnqq/vjcf/basic/101-150

Aturan pencatatan:
- Setiap deviasi wajib mencantumkan: alasan teknis, dampak, rencana mitigasi, status.
- Setiap perubahan status deviasi wajib menambah tanggal update.
- Jika deviasi mempengaruhi output PDF/auth scope, wajib sinkron ke:
  - `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`
  - `docs/security/REGRESSION_CHECKLIST_AUTH_SCOPE.md`

## Register Deviasi

| ID | Tanggal | Lampiran/Modul | Jenis deviasi | Deviasi | Alasan teknis | Dampak | Rencana mitigasi | Status |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `DV-001` | 2026-02-20 | 4.11 / `bantuans` | Naming internal | Pedoman memakai label `Buku Keuangan`, implementasi domain internal tetap `bantuans`. | Reuse domain existing untuk menghindari tabel baru dan duplikasi arus kas. | Risiko kebingungan saat maintenance internal; tidak mempengaruhi label UI/PDF. | Pertahankan label pedoman untuk UI/PDF, dokumentasikan mapping di matrix domain, jaga test baseline PDF. | `accepted` |
| `DV-002` | 2026-02-20 | 4.14.4a / `warung-pkk` | Naming internal | Pedoman memakai label `Data Aset (Sarana) Desa/Kelurahan`, slug teknis tetap `warung-pkk`. | Kompatibilitas route/model existing dan menghindari breaking change besar. | Risiko salah interpretasi istilah di level kode; tidak mempengaruhi output pedoman. | Pertahankan normalisasi label di UI/PDF + update terminology map saat ada perubahan terkait. | `accepted` |
| `DV-003` | 2026-02-20 | 4.14.5 | Coverage pedoman | Pada baseline sumber halaman 101-150, lampiran 4.14.5 belum ditemukan untuk divalidasi. | Ruang lingkup sumber canonical saat ini terbatas ke halaman 101-150. | Validasi kontrak domain untuk 4.14.5 belum bisa dilakukan penuh. | Jika sumber resmi tambahan tersedia, tambah ke matrix kontrak + checklist PDF + baseline fixture. | `open` |

## Kriteria Status

- `open`: deviasi teridentifikasi, belum dimitigasi penuh.
- `accepted`: deviasi disetujui sementara, mitigasi aktif, dipantau.
- `resolved`: deviasi sudah ditutup dan tidak lagi berdampak.
- `rejected`: deviasi tidak valid setelah verifikasi ulang.

## Bukti Validasi T11

Perintah yang dijalankan saat pembuatan log:
- `php artisan route:list --name=report`
- `php artisan test --filter=PdfBaselineFixtureComplianceTest`

Ringkasan hasil:
- Route report aktif: `52`.
- Baseline PDF compliance: `20` test pass.
