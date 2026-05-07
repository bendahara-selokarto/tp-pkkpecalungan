# ADR 0009 Activity Group Book Isolation

Tanggal: 2026-05-02  
Status: `accepted`  
Owner: AI + owner domain  
Related TODO: `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`  
Supersedes: `-`  
Superseded by: `-`

## Konteks

- Buku Kegiatan pada level wilayah yang sama tidak selalu satu buku bersama. Setiap jabatan/unit kerja memiliki buku sendiri: `sekretaris-tpk`, `bendahara-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, dan `pokja-iv`.
- Risiko utama bila hanya memakai `level + area_id` adalah data Sekretaris TP-PKK dan Pokja pada area yang sama tercampur pada list, detail, cetak, atau dashboard.

## Opsi yang Dipertimbangkan

### Opsi A - Filter Berdasarkan Creator

- Ringkasan pendek: akses dibatasi dari role pembuat atau `created_by`.
- Kelebihan: mudah pada data lama.
- Konsekuensi: salah untuk buku jabatan bersama; dua akun sekretaris pada area yang sama tidak otomatis berbagi buku sekretariat.

### Opsi B - Filter Berdasarkan Activity Group

- Ringkasan pendek: `activities.group` menjadi dimensi isolasi buku bersama `level + area_id`.
- Kelebihan: sesuai kontrak domain jabatan/unit kerja dan stabil lintas akun.
- Konsekuensi: semua create/list/detail/update/print wajib membawa dan mengecek group.

## Keputusan

- Opsi terpilih: Opsi B.
- Alasan utama: buku kegiatan adalah buku per jabatan/unit kerja, bukan per akun pembuat.
- Kontrak yang dikunci: akses same-level Buku Kegiatan memakai `level + area_id + group`; `desa-bendahara` dan `kecamatan-bendahara` menulis/membaca group `bendahara-tpk`; monitoring kecamatan atas kegiatan desa tetap eksplisit lewat route monitoring.

## Dampak

- Dampak positif: tidak ada kebocoran Buku Kegiatan lintas Sekretaris/Pokja pada area yang sama.
- Trade-off: data lama tanpa group valid perlu normalisasi sebelum dipakai di runtime aktif.
- Area terdampak: role/scope matrix, menu visibility, Activity scope service, repository, create/update action, model fallback, feature test, policy test, dan dokumen process.

## Validasi

- [x] Targeted activity/policy/dashboard/menu tests: `60` passed.
- [x] Full regression: `php artisan test --compact` -> `1265` passed.

## Rollback/Fallback Plan

- Rollback minimum: kembalikan filter activity ke baseline sebelum group isolation.
- Kondisi fallback: jika ditemukan data produksi lama tanpa group valid; lakukan normalisasi data ke group canonical sebelum mengaktifkan ulang filter.

## Referensi

- `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`

## Status Log

- 2026-05-02: `accepted`; owner mengunci buku kegiatan terpisah per jabatan/unit kerja.
- 2026-05-02: group `bendahara-tpk` ditambahkan agar Bendahara pada scope desa/kecamatan memiliki CRUD Buku Kegiatan sendiri.
