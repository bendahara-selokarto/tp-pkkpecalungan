# TODO Autentik Lampiran 4.14.4c-4.14.4f E2E

## Konteks
- Bukti visual autentik untuk `Lampiran 4.14.4c`, `4.14.4d`, `4.14.4e`, `4.14.4f` sudah tervalidasi sampai level merge header.
- Ada gap implementasi pada layout PDF dan kontrak field (khususnya `4.14.4e` butuh kolom `keterangan`).

## Target Hasil
- Kontrak domain, PDF view, dan baseline test untuk `4.14.4c-4.14.4f` sinkron dengan peta header autentik.
- Flow `posyandu` mencakup `keterangan` end-to-end (request -> action -> repository -> model -> UI -> PDF).
- Dokumen kontrak domain dan checklist PDF terbarui.

## Keputusan
- [x] Jadikan screenshot autentik sebagai bukti resmi merge cell (`rowspan`/`colspan`) untuk 4 lampiran.
- [x] Pertahankan struktur domain `kejar-paket` existing yang menyertakan `jenis_kejar_paket` untuk kompatibilitas data saat ini.
- [x] Sinkronkan `posyandu` dengan menambahkan field `keterangan` agar sesuai kolom autentik nomor 8.
- [x] Untuk `simulasi-penyuluhan`, `keterangan` diposisikan sebagai kompatibilitas internal dan bukan header tabel autentik.

## Langkah Eksekusi
- [x] Tambah migration incremental untuk `posyandus.keterangan`.
- [x] Sinkronkan backend `posyandu` (DTO, request, action, repository, model, controller).
- [x] Sinkronkan UI `posyandu` desa/kecamatan (create, edit, index, show).
- [x] Sinkronkan PDF `koperasi`, `kejar-paket`, `posyandu`, `simulasi-penyuluhan` ke header autentik.
- [x] Sinkronkan fixture baseline PDF 4 lampiran.
- [x] Sinkronkan dokumen kontrak domain dan checklist PDF.
- [x] Tambah/ubah regression test terkait.

## Validasi
- [x] `php artisan test --filter=PdfBaselineFixtureComplianceTest`
- [x] `php artisan test --filter=PosyanduReportPrintTest`
- [x] `php artisan test --filter=DesaPosyanduTest`
- [x] `php artisan test --filter=KecamatanPosyanduTest`
- [x] `php artisan test --filter=KoperasiReportPrintTest`
- [x] `php artisan test --filter=KejarPaketReportPrintTest`
- [x] `php artisan test --filter=SimulasiPenyuluhanReportPrintTest`

## Risiko
- Jika grouping PDF `posyandu` berubah, urutan tampilan data historis bisa berbeda dari versi sebelumnya.
- Penambahan field `keterangan` pada `posyandu` menambah satu titik sinkronisasi UI dan seed data.

## Fallback Plan
- Jika rendering grouped `posyandu` menimbulkan regresi, fallback ke layout tabel linear dengan header autentik tetap dipertahankan.
- Jika migration belum dapat dijalankan di environment tertentu, field `keterangan` dapat sementara diperlakukan nullable di layer view dengan fallback `-`.

## Catatan Keputusan Final
- Perubahan ini tidak menambah menu/domain baru, sehingga trigger audit dashboard hanya berdampak pada coverage dokumen yang sudah ada (`4.14.4c-4.14.4f`).
