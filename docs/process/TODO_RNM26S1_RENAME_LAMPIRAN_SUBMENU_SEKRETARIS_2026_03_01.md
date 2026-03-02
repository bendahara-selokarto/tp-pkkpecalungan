# TODO RNM26S1 Rename Lampiran ke Nama Modul dan Pindah ke Submenu Sekretaris 2026-03-01

Tanggal: 2026-03-01  
Status: `done`  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`

## Konteks
- Pada halaman `Catatan Keluarga` saat ini masih ada kumpulan tombol cetak berlabel teknis (`Cetak 4.15`, `Cetak 4.16a`, dst).
- Label berbasis kode lampiran membingungkan user karena tidak merepresentasikan nama modul/domain secara natural.
- Secara navigasi, report turunan `catatan-keluarga` perlu diposisikan sebagai submenu di group `Sekretaris TPK` agar konsisten dengan pola akses/menu.
- Concern ini termasuk trigger copywriting pass karena menyentuh teks user-facing lintas section.

## Target Hasil
- [x] Semua label report turunan lampiran diubah dari format kode (`4.xx`) menjadi nama modul/domain natural user.
- [x] Aksi report-only dipindahkan dari blok tombol pada `Index Catatan Keluarga` ke submenu group `Sekretaris TPK`.
- [x] Tombol cetak lampiran pada halaman index `Catatan Keluarga` dihapus agar tidak duplikasi jalur navigasi.
- [x] Kontrak backend/query/route report tetap sama (hanya perubahan navigasi + copy UI).
- [x] Konsistensi nama modul lintas sidebar, halaman, dan mapping domain terjaga.

## Ruang Lingkup Modul (Awal)
- [x] `Catatan Keluarga`
- [x] `Rekap Catatan Warga Dasa Wisma`
- [x] `Rekap Catatan Warga PKK RT`
- [x] `Catatan Warga PKK RW`
- [x] `Catatan Warga Dusun/Lingkungan`
- [x] `Catatan Warga TP PKK Desa/Kelurahan`
- [x] `Catatan Warga TP PKK Kecamatan`
- [x] `Catatan Warga TP PKK Kabupaten/Kota`
- [x] `Catatan Warga TP PKK Provinsi`
- [x] `Rekap Ibu Hamil Dasawisma`
- [x] `Rekap Ibu Hamil PKK RT`
- [x] `Rekap Ibu Hamil PKK RW`
- [x] `Rekap Ibu Hamil PKK Dusun/Lingkungan`
- [x] `Rekap Ibu Hamil TP PKK Kecamatan`
- [x] `Data Umum PKK`
- [x] `Data Umum PKK Kecamatan`
- [x] `Data Kegiatan PKK Pokja III`
- [x] `Data Kegiatan PKK Pokja IV`

## Langkah Eksekusi
- [x] Audit mapping `route report -> nama modul canonical` dari:
  - `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
  - `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
  - `resources/js/Pages/{Desa,Kecamatan}/CatatanKeluarga/Index.vue`
  - `resources/js/Layouts/DashboardLayout.vue`
- [x] Finalisasi daftar label user-facing natural per report (tanpa kode lampiran di teks utama).
- [x] Pindahkan seluruh entry report ke submenu group `Sekretaris TPK` pada sidebar.
- [x] Hapus tombol cetak lampiran dari `Index Catatan Keluarga` desa/kecamatan.
- [x] Pastikan tidak ada duplikasi item menu dan route tetap bisa diakses sesuai `moduleModes`.
- [x] Sinkronkan jejak dokumentasi concern:
  - `docs/process/TODO_MVI26A1_INVENTARIS_MODUL_VISIBILITY_2026_03_01.md`
  - jika ada perubahan keputusan akses lintas concern, sinkronkan ke ADR terkait.

## Validasi
- [ ] Smoke test UI:
  - login `desa-sekretaris`,
  - verifikasi submenu `Sekretaris TPK` menampilkan report turunan dengan label nama modul,
  - verifikasi tombol `Cetak 4.xx` sudah tidak ada di `Index Catatan Keluarga`.
- [x] Validasi akses:
  - submenu hanya tampil untuk role dengan `moduleModes` aktif,
  - route report tetap terproteksi `scope.role` + `module.visibility` + policy.
- [x] Jalankan test relevan minimal:
  - `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`
  - `php artisan test tests/Feature/ModuleVisibilityMiddlewareTest.php`
  - `php artisan test tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
  - `php artisan test tests/Feature/RekapCatatanDataKegiatanWargaReportPrintTest.php`

## Risiko
- Risiko drift istilah jika label sidebar tidak disinkronkan dengan terminology/domain matrix.
- Risiko UX regresi jika user lama masih mengandalkan tombol di halaman index.
- Risiko duplikasi navigasi jika sebagian tombol lama tertinggal.

## Keputusan Awal
- [x] Prioritas concern adalah konsistensi navigasi dan copywriting; backend report contract tidak diubah.
- [x] Nama modul menjadi label utama; kode lampiran tidak ditampilkan sebagai label utama menu.
- [x] Jalur akses report terpusat di sidebar submenu `Sekretaris TPK`.

## Output Final yang Diharapkan
- [x] Sidebar `Sekretaris TPK` menjadi single-entry point report turunan catatan keluarga dengan nama modul natural.
- [x] Halaman `Catatan Keluarga` fokus pada data/rekap utama tanpa panel tombol cetak lampiran.
- [x] Dokumen TODO/arsitektur terkait concern visibility tetap koheren.

## Catatan Implementasi
- Sidebar `Sekretaris TPK` kini memuat semua report turunan `catatan-keluarga` via `buildCatatanKeluargaReportItems(scope)`.
- Group `Pokja IV` tidak lagi memuat entry report-only `catatan-keluarga` untuk menghindari duplikasi jalur.
- Halaman index `Catatan Keluarga` desa/kecamatan menghapus panel tombol `Cetak 4.xx` dan menggantinya dengan helper text navigasi submenu.
- Tidak ada perubahan kontrak route/backend report; perubahan terbatas pada penempatan menu dan copywriting UI.

## Hasil Validasi Eksekusi
- `php artisan test tests/Feature/MenuVisibilityPayloadTest.php`
- `php artisan test tests/Feature/ModuleVisibilityMiddlewareTest.php`
- `php artisan test tests/Unit/Frontend/DashboardLayoutMenuContractTest.php`
- `php artisan test tests/Feature/RekapCatatanDataKegiatanWargaReportPrintTest.php`
- Ringkasan: `43 passed (823 assertions)`, durasi `75.56s`.
- Catatan: smoke test UI manual belum dijalankan pada sesi ini (mode eksekusi cepat).
