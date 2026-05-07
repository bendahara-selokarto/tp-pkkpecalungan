# ADR 0010 Prestasi Group Book Isolation

Tanggal: 2026-05-02  
Status: `accepted`  
Owner: AI + owner domain  
Related TODO: `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`  
Supersedes: `-`  
Superseded by: `-`

## Konteks

- Owner mengunci bahwa Buku Prestasi mengikuti kontrak yang sama dengan Buku Kegiatan: setiap jabatan/unit kerja memiliki buku sendiri pada level dan area yang sama.
- Risiko utama bila hanya memakai `level + area_id` adalah data prestasi Sekretaris TP-PKK, Bendahara, dan Pokja tercampur pada list, detail, update, delete, atau cetak.

## Opsi yang Dipertimbangkan

### Opsi A - Filter Berdasarkan Creator

- Ringkasan pendek: akses dibatasi berdasarkan akun pembuat.
- Kelebihan: cocok untuk data personal.
- Konsekuensi: tidak cocok untuk buku jabatan bersama; dua akun pada jabatan yang sama tidak otomatis berbagi Buku Prestasi.

### Opsi B - Filter Berdasarkan Prestasi Group

- Ringkasan pendek: `prestasi_lombas.group` menjadi dimensi isolasi buku bersama `level + area_id`.
- Kelebihan: sesuai kontrak domain jabatan/unit kerja dan stabil lintas akun.
- Konsekuensi: semua create/list/detail/update/delete/print wajib membawa dan mengecek group.

## Keputusan

- Opsi terpilih: Opsi B.
- Alasan utama: Buku Prestasi adalah buku per jabatan/unit kerja, bukan per akun pembuat.
- Kontrak yang dikunci: akses Buku Prestasi memakai `level + area_id + group`; `desa-bendahara` dan `kecamatan-bendahara` menulis/membaca group `bendahara-tpk`; Sekretaris dan Pokja tetap memakai group canonical masing-masing.

## Dampak

- Dampak positif: tidak ada kebocoran Buku Prestasi lintas Sekretaris/Bendahara/Pokja pada area yang sama.
- Trade-off: data lama tanpa group valid perlu normalisasi sebelum dipakai pada runtime aktif.
- Area terdampak: model, migration, DTO, repository, scope service, create/update action, list/detail use case, menu visibility, sidebar/cetak registry, feature test, policy test, dan dokumen process.

## Validasi

- [x] Targeted prestasi/menu/policy tests: `56` passed.
- [x] Frontend build: `npm run build` passed.
- [x] Full regression: `php artisan test --compact` -> `1269` passed.

## Rollback/Fallback Plan

- Rollback minimum: kembalikan filter Buku Prestasi ke baseline sebelum group isolation.
- Kondisi fallback: jika ditemukan data produksi lama tanpa group valid; lakukan normalisasi data ke group canonical sebelum mengaktifkan ulang filter.

## Referensi

- `docs/process/TODO_RGM26A1_PENATAAN_ULANG_GROUPING_MODUL_BERDASARKAN_ROLE_USER_2026_03_07.md`

## Status Log

- 2026-05-02: `accepted`; owner mengunci Buku Prestasi terpisah per jabatan/unit kerja.
- 2026-05-02: group `bendahara-tpk` ditambahkan agar Bendahara pada scope desa/kecamatan memiliki CRUD Buku Prestasi sendiri.
