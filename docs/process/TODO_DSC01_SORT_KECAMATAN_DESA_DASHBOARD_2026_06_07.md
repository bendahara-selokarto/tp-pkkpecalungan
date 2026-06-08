# TODO DSC01 Sort Desa Kecamatan untuk Dashboard/Tampilan Terkait

Tanggal: 2026-06-07  
Status: `done`  
Related ADR: `docs/adr/ADR_0013_STANDARISASI_KODE_WILAYAH_Pecalungan.md`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka.
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Beberapa tampilan kecamatan menampilkan daftar desa anak canonical.
- Kontrak yang diminta:
  - urutan desa mengikuti `areas.code` ascending,
  - perilaku ini berlaku untuk dashboard dan tampilan terkait di scope `kecamatan`,
  - source of truth tetap `areas`.
- Tujuan utama adalah konsistensi visual dan urutan canonical di seluruh tampilan kecamatan yang merinci desa.

## Kontrak Concern (Lock)

- Domain:
  - dashboard kecamatan,
  - tampilan list kecamatan yang merender desa anak canonical,
  - repository area scope yang menjadi sumber daftar desa.
- Role/scope target:
  - scope `kecamatan`.
- Boundary data:
  - `app/Domains/Wilayah/Repositories/AreaRepository.php`
  - `app/Domains/Wilayah/Dashboard/Repositories/DashboardGroupCoverageRepository.php`
  - `app/Domains/Wilayah/Dashboard/UseCases/BuildRoleAwareDashboardBlocksUseCase.php`
  - `app/Domains/Wilayah/Dashboard/UseCases/BuildDashboardBlockDetailWidgetUseCase.php`
  - `app/Domains/Wilayah/Activities/Controllers/KecamatanDesaActivityController.php`
  - `app/Domains/Wilayah/Activities/Repositories/ActivityRepository.php`
  - `app/Domains/Wilayah/Arsip/Controllers/KecamatanDesaArsipController.php`
- Acceptance criteria:
  - daftar desa anak pada tampilan kecamatan mengikuti urutan `areas.code`,
  - perubahan dilakukan pada sumber daftar, bukan sorting manual di tiap view,
  - dashboard dan tampilan terkait tetap memakai source data canonical yang sama.
- Dampak keputusan arsitektur: `tidak` (kontrak urutan data dan shared repository behaviour).

## Target Hasil

- [x] Urutan desa anak canonical di scope kecamatan konsisten pada dashboard dan tampilan terkait.
- [x] Source order canonically berasal dari `AreaRepository`.

## Langkah Eksekusi

- [x] Audit scoped semua pemakai daftar desa anak pada scope kecamatan.
- [x] Tambahkan urutan `areas.code` pada source repository daftar desa.
- [x] Sinkronkan dokumen dashboard baseline agar kontrak urutan eksplisit.

## Validasi

- [x] L1: lint file yang diubah.
- [x] L1: test target yang memverifikasi urutan desa anak canonical.
- [x] L2: smoke terhadap pemakai dashboard/tampilan terkait.

## Risiko

- Risiko 1: sorting di source yang terlalu umum bisa mempengaruhi list lain di scope desa jika tidak dibatasi.
- Risiko 2: pemakai lama yang mengandalkan urutan implisit id lama bisa berubah hasilnya.

## Keputusan

- [x] K1: Urutan canonical di scope kecamatan wajib mengikuti `areas.code`.
- [x] K2: Sorting dilakukan di source repository bersama, bukan per komponen UI.

## Hasil

- `AreaRepository::getDesaByKecamatan()` dan `AreaRepository::getByUser()` sekarang mengurutkan desa anak berdasarkan `areas.code`.
- Dashboard kecamatan dan tampilan terkait yang bergantung pada source repository area ikut memperoleh urutan canonical yang sama.
