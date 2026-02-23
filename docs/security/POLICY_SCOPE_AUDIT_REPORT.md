# Policy Scope Audit Report (T4)

Ruang lingkup audit:
- Modul buku sekretaris dan turunan lampiran 4.9a-4.15:
  - `anggota-tim-penggerak`, `kader-khusus`, `agenda-surat`, `buku-keuangan`, `bantuans`, `inventaris`, `activities`
  - `data-warga`, `data-kegiatan-warga`, `data-keluarga`
  - `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, `data-industri-rumah-tangga`, `data-pelatihan-kader`
  - `warung-pkk`, `taman-bacaan`, `koperasi`, `kejar-paket`, `posyandu`, `simulasi-penyuluhan`, `catatan-keluarga`

Metode audit:
- Verifikasi middleware route scope: `scope.role:{desa|kecamatan}`
- Verifikasi gate policy call (`authorize(...)`) di controller domain
- Verifikasi guard query scoped di use case + scope service
- Verifikasi boundary query domain (controller tidak query langsung ke model)

## Hasil Ringkas

- Status audit: `PASS` (tidak ditemukan bypass otorisasi kritikal).
- Tidak ada route modul audit yang lolos tanpa middleware `scope.role`.
- Tidak ada controller modul audit yang melakukan query domain langsung (query berada di repository boundary).
- Semua `GetScoped*UseCase` terverifikasi menggunakan guard `authorizeSameLevelAndArea(...)` atau `authorizeDesaInKecamatan(...)`.

## Temuan

1. `LOW` - Delegated authorize pada sebagian print endpoint.
- Lokasi:
  - `app/Domains/Wilayah/AnggotaTimPenggerak/Controllers/AnggotaTimPenggerakReportPrintController.php`
  - `app/Domains/Wilayah/BukuKeuangan/Controllers/BukuKeuanganReportPrintController.php`
- Detail:
  - Method publik `print*` mendelegasikan ke method private (`stream*`) yang memanggil `authorize(...)`.
  - Aman saat ini, tetapi rawan regresi jika helper private diubah tanpa test.
- Keputusan:
  - Diterima sebagai pola saat ini, dengan catatan wajib dijaga di checklist regression/auth.

2. `LOW` - Satu use case aggregator tidak memanggil `requireUserAreaId()` secara langsung.
- Lokasi:
  - `app/Domains/Wilayah/AnggotaTimPenggerak/UseCases/ListScopedAnggotaDanKaderUseCase.php`
- Detail:
  - Guard area-level diwariskan dari use case yang dipanggil (`ListScopedAnggotaTimPenggerakUseCase` dan `ListScopedKaderKhususUseCase`).
  - Tidak membuka bypass saat ini.
- Keputusan:
  - Diterima karena guard tetap aktif melalui delegated use case.

## Bukti Validasi

Perintah audit yang dijalankan:
- `php artisan route:list`
- `php artisan route:list --json`
- Scan controller authorize:
  - hasil: `issues=4` kandidat tanpa `authorize` langsung, seluruhnya pola delegasi ke helper private yang sudah mengandung `authorize`.
- Scan guard use case:
  - `ListScoped*UseCase`: total `20`, missing direct guard `1` (aggregator delegasi)
  - `GetScoped*UseCase`: total `18`, missing guard `0`
- Scan route middleware modul audit:
  - `module_routes_missing_scope_role=0`

## Verifikasi terhadap Kriteria T4

- Semua controller domain buku sekretaris diverifikasi memakai `authorize(...)`: `PASS`
  - langsung pada method publik atau terdelegasi ke helper private pada kelas yang sama.
- Tidak ada query akses lintas area tanpa guard `Policy -> Scope Service`: `PASS`
  - pattern utama: `requireUserAreaId()` + `authorizeSameLevelAndArea(...)`.
  - pengecualian yang disengaja: monitoring aktivitas desa oleh kecamatan melalui `authorizeDesaInKecamatan(...)` pada domain `activities`.

## Rekomendasi Lanjutan

- Tambahkan test guard untuk print endpoint yang otorisasinya delegated (anggota+kader, keuangan).
- Tambahkan checklist static audit: method publik controller modul domain harus punya `authorize` langsung atau delegasi helper yang tervalidasi.

## Addendum Audit Role-Policy: 2026-02-23

Ruang lingkup audit:
- Konsistensi `Policy -> Scope Service -> Middleware scope.role`.
- Koherensi `role` vs `scope` vs `areas.level`.
- Guardrail mutasi user administratif untuk role `super-admin`.

Metode audit:
- Pembacaan source pada komponen inti:
  - `app/Support/RoleScopeMatrix.php`
  - `app/Http/Middleware/EnsureScopeRole.php`
  - `app/Domains/Wilayah/Services/UserAreaContextService.php`
  - `app/Policies/*Policy.php`
  - `routes/web.php`
  - `app/Actions/User/*`
- Verifikasi eksekusi test terfokus policy dan super-admin flow.

Hasil ringkas:
- Status audit: `PASS`.
- Tidak ditemukan temuan `critical/high/medium`.
- Tidak ada perubahan kode pada siklus audit ini (audit-only).

Temuan:
1. `LOW` - `Gate::before` memberikan bypass penuh untuk `super-admin`.
   - Lokasi: `app/Providers/AppServiceProvider.php`.
   - Detail: untuk role `super-admin`, policy detail tidak lagi menjadi guard utama karena seluruh ability diizinkan.
   - Keputusan: diterima sebagai desain saat ini, dengan catatan tetap dipantau agar tidak terjadi privilege drift yang tidak disengaja.
2. `LOW` - `CatatanKeluargaPrintController` tidak memasang middleware scope di constructor.
   - Lokasi: `app/Domains/Wilayah/CatatanKeluarga/Controllers/CatatanKeluargaPrintController.php`.
   - Detail: saat ini aman karena route ada di group `scope.role:{desa|kecamatan}` pada `routes/web.php`.
   - Keputusan: diterima, namun rawan regresi jika route dipindah ke luar group tanpa guard setara.

Bukti validasi:
- `php artisan test tests/Unit/Policies`
  - hasil: `55` test pass (`88` assertions).
- `php artisan test tests/Feature/SuperAdmin/UserProtectionTest.php tests/Feature/SuperAdmin/UserScopePresentationTest.php`
  - hasil: `8` test pass (`65` assertions).
- `php artisan test tests/Feature/KecamatanReportReverseAreaMismatchTest.php tests/Feature/DesaActivityTest.php`
  - hasil: `26` test pass (`32` assertions).
