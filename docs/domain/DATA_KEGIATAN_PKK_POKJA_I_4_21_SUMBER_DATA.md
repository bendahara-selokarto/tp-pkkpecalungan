# Sumber Data Lampiran 4.21 - Data Kegiatan PKK (Pokja I)

## Tujuan

- Menyiapkan bahan implementasi report Lampiran `4.21` (Pokja I).
- Mengunci sumber data per kolom dan memastikan normalisasi tetap konsisten.

## Status Validasi

- Status sinkronisasi kontrak domain: **implemented (report-only)**.
- Status cek manual header autentik: **selesai, sesuai**.
- Status cek manual sumber data report: **selesai** (repository + view).
- Tanggal cek manual terakhir (header): `2026-03-11`.
- Tanggal cek manual terakhir (sumber data): `2026-03-14`.
- Bukti verifikasi PDF (lokal): `docs/referensi/evidence/screenshots/4_21_data_kegiatan_pkk_pokja_i_pdf.png`.
- Bukti acuan visual: screenshot Lampiran `4.21` di `docs/referensi/_screenshots/rakernas-x-autentik/lampiran_4_21_data_kegiatan_pkk_pokja_i.png`.

## Jalur Eksekusi Report (Aktif)

- Route desa: `desa.data-kegiatan-pkk-pokja-i.report`
- Route kecamatan: `kecamatan.data-kegiatan-pkk-pokja-i.report`
- Controller print: `app/Domains/Wilayah/CatatanKeluarga/Controllers/CatatanKeluargaPrintController.php`
- Use case: `app/Domains/Wilayah/CatatanKeluarga/UseCases/ListScopedDataKegiatanPkkPokjaIUseCase.php`
- Repository: `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php`
- View PDF: `resources/views/pdf/data_kegiatan_pkk_pokja_i_report.blade.php`

## Sumber Data Per Kolom (Aktif)

| Kolom | Header | Sumber data | Catatan implementasi | Normalisasi |
| --- | --- | --- | --- | --- |
| 1 | `NO` | agregasi report | nomor urut hasil iterasi | - |
| 2 | `NAMA WILAYAH` | `areas` (area aktif user) | label wilayah diambil dari area scope | - |
| 3 | `JML KADER` | `anggota_pokjas.pokja` | count anggota pokja dengan token Pokja I | `isPokjaI` |
| 4 | `KISAH - Kegiatan` | `activities` | count aktivitas yang mengandung keyword `kisah` | `containsAnyKeyword` |
| 5 | `KISAH - Vol. Keg` | `activities` | sementara = jumlah kegiatan KISAH | `containsAnyKeyword` |
| 6 | `KISAH - Metode` | - | belum ada field dedicated, fallback `0` | - |
| 7 | `KISAH - Jml. Sasaran` | - | belum ada field dedicated, fallback `0` | - |
| 8 | `KRISAN - Kegiatan` | `activities` | count aktivitas yang mengandung keyword `krisan` | `containsAnyKeyword` |
| 9 | `KRISAN - Vol. Keg` | `activities` | sementara = jumlah kegiatan KRISAN | `containsAnyKeyword` |
| 10 | `KRISAN - Metode` | - | belum ada field dedicated, fallback `0` | - |
| 11 | `KRISAN - Jml. Sasaran` | - | belum ada field dedicated, fallback `0` | - |
| 12 | `KILAS - Kegiatan` | `activities` | count aktivitas yang mengandung keyword `kilas` | `containsAnyKeyword` |
| 13 | `KILAS - Vol. Keg` | `activities` | sementara = jumlah kegiatan KILAS | `containsAnyKeyword` |
| 14 | `KILAS - Metode` | - | belum ada field dedicated, fallback `0` | - |
| 15 | `KILAS - Jml. Sasaran` | - | belum ada field dedicated, fallback `0` | - |
| 16 | `KTIAT - Kegiatan` | `activities` | count aktivitas yang mengandung keyword `ktiat` | `containsAnyKeyword` |
| 17 | `KTIAT - Vol. Keg` | `activities` | sementara = jumlah kegiatan KTIAT | `containsAnyKeyword` |
| 18 | `KTIAT - Metode` | - | belum ada field dedicated, fallback `0` | - |
| 19 | `KTIAT - Jml. Sasaran` | - | belum ada field dedicated, fallback `0` | - |
| 20 | `KISAK - Kegiatan` | `activities` | count aktivitas yang mengandung keyword `kisak` | `containsAnyKeyword` |
| 21 | `KISAK - Vol. Keg` | `activities` | sementara = jumlah kegiatan KISAK | `containsAnyKeyword` |
| 22 | `KISAK - Metode` | - | belum ada field dedicated, fallback `0` | - |
| 23 | `KISAK - Jml. Sasaran` | - | belum ada field dedicated, fallback `0` | - |
| 24 | `PKBN - Kegiatan` | `activities` | count aktivitas yang mengandung keyword `pkbn` | `containsAnyKeyword` |
| 25 | `PKBN - Vol. Keg` | `activities` | sementara = jumlah kegiatan PKBN | `containsAnyKeyword` |
| 26 | `PKBN - Metode` | - | belum ada field dedicated, fallback `0` | - |
| 27 | `PKBN - Jml. Sasaran` | - | belum ada field dedicated, fallback `0` | - |

## Catatan Normalisasi

- Sumber `activities` membaca field `title`, `description`, dan `uraian` sebagai payload keyword.
- Keyword check bersifat case-insensitive dan memicu penambahan hit pada setiap program yang match.
- `Vol. Keg` sementara diproksikan sama dengan jumlah kegiatan per program.

## Keputusan Terkunci

- `Vol. Keg` mengikuti jumlah kegiatan per program sampai ada field volume dedicated.
- `Metode` dan `Jml. Sasaran` bernilai `0` sampai ada field dedicated.

## Status Sinkronisasi

- Dokumen ini mencerminkan implementasi report aktif Lampiran 4.21.
- Jika normalizer keyword atau sumber data berubah, dokumen ini wajib diperbarui pada sesi yang sama.
