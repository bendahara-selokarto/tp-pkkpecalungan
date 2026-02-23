# TODO Refactor 4.11 Buku Keuangan E2E

## Konteks
- Keluhan operasional: menu `Buku Keuangan` menampilkan domain `Bantuan`.
- Akar masalah saat audit: lampiran `4.11` masih ditopang domain teknis `bantuans` (deviasi `DV-001`).
- Dampak: drift terminologi, risiko salah input transaksi, dan kebingungan operator.

## Target Hasil
- Domain `Buku Keuangan` berdiri mandiri end-to-end (route, request, action/use case, repository, policy/scope, UI Inertia, report PDF, dashboard coverage, test).
- Jalur lama `bantuans/keuangan/report/pdf` tetap tersedia sebagai fallback kompatibilitas route.
- Dokumen kontrak domain/pedoman sinkron ke domain baru.

## Langkah Eksekusi
- [x] Tambah skema data `buku_keuangans` + migrasi backfill dari data `bantuans` kategori keuangan.
- [x] Implementasi domain backend `BukuKeuangan` sesuai boundary arsitektur (Controller -> UseCase/Action -> Repository Interface -> Repository -> Model).
- [x] Tambah policy + scope service `BukuKeuangan` dan registrasi binding/gate di `AppServiceProvider`.
- [x] Tambah route resource + report untuk `desa`/`kecamatan`, termasuk alias fallback route lama.
- [x] Implementasi halaman Inertia `Desa/Kecamatan/BukuKeuangan` (index/create/edit/show) dengan label dan field transaksi keuangan.
- [x] Ubah sidebar dan dashboard coverage agar `Buku Keuangan` mengarah ke slug baru `buku-keuangan`.
- [x] Sinkronkan dokumen domain/pedoman/checklist agar canonical `4.11` tidak lagi menunjuk `bantuans`.
- [x] Tambahkan/ubah test feature + unit policy untuk matrix minimum modul baru.

## Validasi
- [x] Jalankan test terfokus: fitur `BukuKeuangan`, policy `BukuKeuangan`, dan reverse area mismatch terkait route report.
- [x] Jalankan `php artisan test` untuk validasi regresi akhir.

Ringkasan hasil validasi:
- Targeted suite: `54` tests pass (`555` assertions).
- Full suite: `705` tests pass (`3084` assertions).

## Risiko
- [x] Risiko regresi pada endpoint lama yang masih dipakai bookmark/operator.
- [x] Risiko mismatch coverage dashboard jika slug baru belum ikut dipetakan.
- [x] Risiko data lama tidak terbawa jika backfill tidak menutup seluruh pola kategori keuangan lama.

## Keputusan
- [x] Pertahankan route lama `bantuans.keuangan.report` sebagai alias kompatibilitas menuju controller baru.
- [x] Domain `Bantuan` tetap dipertahankan untuk kebutuhan entitas bantuan non-keuangan agar perubahan tetap diff-first.
- [x] Rollback plan teknis: rollback migration `buku_keuangans`, revert route/menu ke `bantuans` apabila ditemukan blocker kritis pada produksi.
