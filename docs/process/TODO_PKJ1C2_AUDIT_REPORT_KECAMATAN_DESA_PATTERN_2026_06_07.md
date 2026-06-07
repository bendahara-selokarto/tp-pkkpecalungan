# TODO PKJ1C2 Audit Pattern Report Kecamatan Desa

Tanggal: 2026-06-07  
Status: `done`  
Related ADR: `docs/adr/ADR_0013_STANDARISASI_KODE_WILAYAH_Pecalungan.md`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Ada kebutuhan konsistensi lintas report level `kecamatan` yang mengambil data dari level `desa`.
- Pola yang dimaksud:
  - tampilkan semua desa anak canonical pada kecamatan aktif,
  - urutkan berdasarkan `areas.code`,
  - desa tanpa data tetap tampil `0`,
  - tutup report dengan baris `JUMLAH` sebagai rekap 1 kecamatan.
- Pola ini sudah dipastikan relevan untuk `data-kegiatan-pkk-pokja-i` dan harus dijadikan baseline audit untuk report kecamatan lain yang memakai sumber desa.

## Kontrak Concern (Lock)

- Domain:
  - `catatan-keluarga` report kecamatan yang sumber datanya dari desa,
  - report level `kecamatan` lain yang membaca child desa dalam satu kecamatan.
- Role/scope target:
  - scope `kecamatan`,
  - role monitoring/operasional kecamatan yang diberi akses report.
- Boundary data:
  - `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php`
  - `app/Domains/Wilayah/CatatanKeluarga/Controllers/CatatanKeluargaPrintController.php`
  - `app/Domains/Wilayah/Activities/Repositories/ActivityRepository.php`
  - `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
  - `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`
  - `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- Acceptance criteria:
  - semua report kecamatan yang bersumber dari desa memakai pola `10 desa child + JUMLAH kecamatan`,
  - tidak ada report kecamatan yang masih mengandalkan ringkasan satu area saat sumbernya desa,
  - kontrak ini terdokumentasi sebelum implementasi patch.
- Dampak keputusan arsitektur: `tidak` (audit dan standarisasi kontrak dulu).

## Target Hasil

- [x] Daftar report kecamatan yang sumbernya desa terinventaris dengan status.
- [x] Pola output standar dikunci di dokumen domain.

## Langkah Eksekusi

- [x] Audit scoped controller/repository/report yang memakai `level = kecamatan` dan `area_id` child desa.
- [x] Kelompokkan report yang sudah sesuai pola dan yang masih ringkasan satu area.
- [x] Sinkronkan dokumen domain agar pola baku tertulis jelas untuk report serupa di masa depan.

## Validasi

- [x] L1: scoped `rg` dan baca file concern.
- [x] L2: verifikasi daftar report kecamatan terhadap matrix domain.
- [x] L3: lanjut implementasi hanya setelah kontrak audit terkunci.

## Risiko

- Risiko 1: Beberapa report kecamatan mungkin bukan rekap desa murni, jadi tidak semua wajib dipaksa ke pola ini.
- Risiko 2: Salah mengelompokkan report dapat memicu patch yang terlalu luas.

## Keputusan

- [x] K1: Standar untuk report kecamatan berbasis desa adalah 10 desa canonical + JUMLAH kecamatan.
- [x] K2: Report yang bukan berbasis desa tidak ikut dipaksa ke pola ini.

## Hasil Audit Scoped

Report level `kecamatan` yang terkonfirmasi bersumber dari desa dan perlu mengikuti pola standar:

| Report | Status audit | Catatan |
| --- | --- | --- |
| `data-kegiatan-pkk-pokja-i` | `needs standardization` | Saat ini masih ringkasan satu area; harus diubah menjadi 10 desa child + JUMLAH kecamatan. |
| `catatan-keluarga` 4.20b (`data-umum-pkk-kecamatan`) | `aligned` | Sudah memakai child desa canonical sebagai basis iterasi. |
| `catatan-keluarga` 4.19b (`rekap-ibu-hamil-tp-pkk-kecamatan`) | `aligned` | Sudah berbasis desa anak dan rekap kecamatan. |
| `catatan-keluarga` 4.17b (`catatan-data-kegiatan-warga-tp-pkk-kecamatan`) | `aligned` | Sudah berbasis desa anak dan rekap kecamatan. |

Report level `kecamatan` yang tidak dipaksa ke pola ini karena bukan report desa-murni atau bukan rekap desa canonical:

| Report | Status audit | Catatan |
| --- | --- | --- |
| `activities` report kecamatan | `not in scope` | Berbasis data kegiatan level kecamatan + view list, bukan rekap desa 10 baris. |
| `anggota-pokja`, `data-warga`, `data-keluarga`, `data-industri-rumah-tangga`, `data-pelatihan-kader`, `warung-pkk`, `koperasi`, `kejar-paket`, `posyandu`, `taman-bacaan`, `buku` report kecamatan | `not in scope` | Masing-masing adalah report dataset sendiri; bukan pola child desa + JUMLAH kecamatan yang dimaksud. |

## Keputusan Arsitektur (Jika Ada)

- [x] Tautkan hasil audit ke ADR 0013 sebagai basis canonical wilayah.
- [x] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jika ada report kecamatan yang tidak jelas sumbernya, tandai `needs review` dan jangan diubah sebelum kontrak sumber data dipastikan.

## Output Final

- [x] Ringkasan report yang masuk pola.
- [x] Ringkasan report yang tidak masuk pola dan alasannya.
- [x] File dokumen yang disinkronkan.
