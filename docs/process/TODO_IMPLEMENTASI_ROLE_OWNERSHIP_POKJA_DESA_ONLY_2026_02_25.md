# TODO Implementasi Role Ownership Pokja Desa-Only 2026-02-25

Tanggal: 2026-02-25  
Status: `done`

## Konteks

- Audit ownership modul menemukan mismatch: beberapa modul pokja masih memberi `RW` pada role pokja kecamatan.
- Koreksi domain menetapkan modul ini `RW Desa` saja.

## Target Hasil

- Role `kecamatan-pokja-*` tidak lagi memiliki mode `read-write` pada modul pokja desa-only.
- Tidak ada drift akses antara `RoleMenuVisibilityService`, middleware, dan route scope.

## Langkah Eksekusi

- [x] Update mapping `ROLE_MODULE_MODE_OVERRIDES` pada `RoleMenuVisibilityService` untuk menurunkan akses `kecamatan-pokja-*` di modul pokja desa-only.
- [x] Verifikasi modul terdampak:
  - [x] `pokja-i`: `bkl`, `bkr`, `paar`, `data-warga`, `data-kegiatan-warga`
  - [x] `pokja-ii`: `kejar-paket`, `koperasi`, `taman-bacaan`
  - [x] `pokja-iii`: `data-keluarga`, `data-industri-rumah-tangga`, `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, `warung-pkk`
  - [x] `pokja-iv`: `posyandu`, `simulasi-penyuluhan`
- [x] Tambah/ubah test feature untuk memastikan role pokja kecamatan ditolak saat mutasi modul desa-only.
- [x] Tambah/ubah unit test service visibilitas untuk matrix role baru.

## Validasi

- [x] `php artisan test --filter=RoleMenuVisibilityService`
- [x] `php artisan test --filter=scope_metadata_tidak_sinkron|role_dan_level_area_tidak_sinkron` (dijalankan terpisah per token filter karena karakter `|` tidak kompatibel dengan shell command parser sesi ini)
- [x] `php artisan test` penuh

Catatan validasi:
- `php artisan test` penuh lulus pada eksekusi 2026-02-25 (suite hijau end-to-end).

## Risiko

- Potensi regresi pada user role legacy (`admin-kecamatan`) yang masih kompatibilitas.
- Potensi mismatch dokumentasi jika matrix role berubah tanpa sinkronisasi dokumen domain.

## Keputusan

- [x] Concern ini dikunci sebagai turunan langsung hasil audit ownership 2026-02-25.
- [x] Perubahan runtime disetujui pada sesi ini untuk daftar modul desa-only dengan skema penurunan ke `read-only` (bukan pencabutan akses baca).
