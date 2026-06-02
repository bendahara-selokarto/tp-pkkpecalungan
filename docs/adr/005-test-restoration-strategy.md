# ADR 005: Strategi Pemulihan Test Suite dan Protokol "Green-Path"

## Status
Proposed (Accepted as Foundation Mandate)

## Context
Project ini memiliki test suite yang besar namun banyak yang mengalami *stale* (usang) akibat perubahan skema metadata (tahun anggaran, grouping wilayah) dan layering arsitektur. Untuk menghindari hambatan pengembangan namun tetap menjaga kualitas, test yang gagal telah ditandai sebagai `skipped`.

## Decision
1. **Gradual Restoration**: Test yang di-*skip* tidak boleh dihapus. Pemulihan dilakukan secara bertahap setiap kali ada perubahan pada modul terkait (Scout Rule).
2. **Definition of Done (DoD)**: Sebuah fitur atau perbaikan bug dianggap **FINAL** hanya jika test terkait (unit & feature) sudah berstatus **Hijau (Passed)**. Tidak diperbolehkan menutup tugas dengan status test yang masih di-*skip* untuk modul tersebut.
3. **Metadata Compliance**: Pemulihan test wajib menyertakan metadata terbaru:
   - `tahun_anggaran` yang valid.
   - `group` buku (sekretaris-tpk, pokja-i, dll) sesuai jabatan user.
   - `area_id` dan `level` yang sinkron dengan `RoleScopeMatrix`.

## Steps for Restoration (Standard Operating Procedure)
Setiap engineer yang menyentuh modul dengan test yang di-*skip* wajib:
1. Menghapus `$this->markTestSkipped()` pada file test terkait.
2. Menjalankan test modul tersebut: `php artisan test tests/Feature/NamaModulTest.php`.
3. Memperbaiki kegagalan otorisasi (403) dengan memastikan setup `User`, `Area`, dan `Model` memiliki metadata yang sinkron.
4. Memperbaiki *assertion* jika ada perubahan struktur JSON/Inertia.
5. Memastikan test lulus (Green) sebelum melakukan merge/commit.

## Consequences
- Kecepatan pengembangan mungkin sedikit melambat di awal karena beban pemulihan test.
- Keamanan jangka panjang (regresi) meningkat secara signifikan seiring dengan bertambahnya test yang aktif kembali.
