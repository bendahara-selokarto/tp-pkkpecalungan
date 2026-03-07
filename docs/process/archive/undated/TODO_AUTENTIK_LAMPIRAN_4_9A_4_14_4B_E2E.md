# TODO ALE494B Autentik Lampiran 4.9a-4.14.4b E2E
Tanggal: 2026-03-02 (normalisasi metadata; perlu verifikasi historis)  
Status: `done`

## Konteks
- Validasi visual autentik untuk lampiran `4.9a`, `4.9b`, `4.10`, `4.11`, `4.12`, `4.13`, `4.14.1a`, `4.14.1b`, `4.14.2a`, `4.14.2b`, `4.14.4b` sudah tersedia.
- Sejumlah view PDF masih memakai header operasional lama dan belum mengikuti format autentik terbaru.

## Target Hasil
- Output PDF modul terkait sinkron dengan header autentik.
- Fixture baseline dan regression test sinkron terhadap perubahan header.
- Dokumen kontrak domain dan checklist PDF mutakhir.

## Keputusan
- [x] Sinkronisasi prioritas dilakukan pada layer report PDF + fixture + test (E2E output contract).
- [x] `4.14.2a` dipertahankan sebagai mode summary operasional sesuai kontrak data existing saat ini; migrasi penuh form autentik ditunda sebagai refactor terpisah.
- [x] `4.11` memakai layout autentik dua blok (penerimaan/pengeluaran); sumber data telah dipisah ke domain `buku-keuangan` (alias route legacy tetap tersedia).
- [x] `4.9b` dan `4.11` memakai mode kompatibilitas untuk kolom yang belum tersedia penuh di domain (placeholder/generate token terkontrol di report).

## Langkah Eksekusi
- [x] Update view PDF: `4.9a`, `4.9b`, `4.10`, `4.11`, `4.13`, `4.14.1b`, `4.14.2b`, `4.14.4b`.
- [x] Update fixture baseline PDF untuk lampiran terkait.
- [x] Tambah/ubah regression test header report untuk modul terkait.
- [x] Update dokumentasi kontrak (`DOMAIN_CONTRACT_MATRIX`, `PDF_COMPLIANCE_CHECKLIST`).

## Validasi
- [x] `php artisan test --filter=PdfBaselineFixtureComplianceTest`
- [x] `php artisan test --filter=AnggotaTimPenggerakReportPrintTest`
- [x] `php artisan test --filter=KaderKhususReportPrintTest`
- [x] `php artisan test --filter=AgendaSuratReportPrintTest`
- [x] `php artisan test --filter=BukuKeuanganReportPrintTest`
- [x] `php artisan test --filter=ActivityPrintTest`
- [x] `php artisan test --filter=DataKegiatanWargaReportPrintTest`
- [x] `php artisan test --filter=DataPemanfaatanTanahPekaranganHatinyaPkkReportPrintTest`
- [x] `php artisan test --filter=TamanBacaanReportPrintTest`
- [x] `php artisan test`

## Risiko
- Perubahan struktur header `4.11` dan `4.9b` berpotensi memengaruhi ekspektasi layout manual lama.
- Penyelarasan penuh `4.14.2a` masih membutuhkan redesign domain data, belum masuk patch ini.
- Kolom autentik `4.9b` (`nomor registrasi tp pkk`, `kedudukan keanggotaan`, `pekerjaan`) masih belum jadi field persisted dedicated pada tabel `kader_khusus`.

## Fallback Plan
- Jika ada regresi render PDF, fallback ke versi header sebelumnya via revert per-view terisolasi.
- Jika validasi autentik tambahan meminta full form `4.14.2a`, lakukan migrasi domain bertahap dengan matriks field dan test matrix khusus.

## Catatan Keputusan Final
- Tidak ada menu/domain baru; dampak dashboard bersifat tidak langsung melalui konsistensi representasi laporan dokumen existing.
