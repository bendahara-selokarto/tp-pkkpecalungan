# Auth Coherence Matrix (Buku Sekretaris PKK)

Tujuan:
- Memetakan `role -> scope -> area level -> akses modul` untuk lampiran 4.9-4.15.
- Menjadi baseline audit sebelum perubahan policy/scope di modul sekretaris.

Sumber teknis:
- `app/Http/Middleware/EnsureScopeRole.php`
- `app/Support/RoleScopeMatrix.php`
- `app/Domains/Wilayah/Services/UserAreaContextService.php`
- `routes/web.php`
- `app/Policies/*Policy.php` (modul sekretaris)

## 1) Kontrak Role -> Scope -> Area Level

Kunci:
- `V` = view/list/show
- `C` = create/store
- `U` = update/edit
- `D` = delete
- `P` = print/report

| Role | Scope route yang boleh diakses | Area level wajib (`areas.level`) | Akses modul 4.9a-4.14.4f | Akses 4.15 `catatan-keluarga` |
| --- | --- | --- | --- | --- |
| `desa-sekretaris` | `desa` | `desa` | `V/C/U/D/P` | `V/P` |
| `desa-bendahara` | `desa` | `desa` | `V/C/U/D/P` | `V/P` |
| `desa-pokja-i` | `desa` | `desa` | `V/C/U/D/P` | `V/P` |
| `desa-pokja-ii` | `desa` | `desa` | `V/C/U/D/P` | `V/P` |
| `desa-pokja-iii` | `desa` | `desa` | `V/C/U/D/P` | `V/P` |
| `desa-pokja-iv` | `desa` | `desa` | `V/C/U/D/P` | `V/P` |
| `admin-desa` (compat) | `desa` | `desa` | `V/C/U/D/P` | `V/P` |
| `kecamatan-sekretaris` | `kecamatan` | `kecamatan` | `V/C/U/D/P` | `V/P` |
| `kecamatan-bendahara` | `kecamatan` | `kecamatan` | `V/C/U/D/P` | `V/P` |
| `kecamatan-pokja-i` | `kecamatan` | `kecamatan` | `V/C/U/D/P` | `V/P` |
| `kecamatan-pokja-ii` | `kecamatan` | `kecamatan` | `V/C/U/D/P` | `V/P` |
| `kecamatan-pokja-iii` | `kecamatan` | `kecamatan` | `V/C/U/D/P` | `V/P` |
| `kecamatan-pokja-iv` | `kecamatan` | `kecamatan` | `V/C/U/D/P` | `V/P` |
| `admin-kecamatan` (compat) | `kecamatan` | `kecamatan` | `V/C/U/D/P` | `V/P` |
| `super-admin` | `kecamatan` | `kecamatan` | `V/C/U/D/P` | `V/P` |

Catatan penting:
- Role di luar matrix (`RoleScopeMatrix`) ditolak oleh middleware `scope.role`.
- Jika `role` valid tapi `area_id` tidak sesuai level scope, akses ditolak (`EnsureScopeRole` + `UserAreaContextService`).
- `super-admin` tetap harus punya `area_id` yang mengarah ke area level `kecamatan` untuk masuk modul scope `kecamatan`.

## 2) Matrix Operasi per Modul 4.9-4.15

| Lampiran | Slug modul | Route shape | Policy gate | Operasi backend | Batas data |
| --- | --- | --- | --- | --- | --- |
| 4.9a | `anggota-tim-penggerak` | `Route::resource` + report pdf | `AnggotaTimPenggerakPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.9b | `kader-khusus` | `Route::resource` + report pdf | `KaderKhususPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.10 | `agenda-surat` | `Route::resource` + report + ekspedisi report | `AgendaSuratPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.11 | `buku-keuangan` | `Route::resource` + report (+ alias `bantuans.keuangan.report`) | `BukuKeuanganPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.12 | `inventaris` | `Route::resource` + report pdf | `InventarisPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.13 | `activities` | `Route::resource` + print per item | `ActivityPolicy` | `V/C/U/D/P` | Desa: same area. Kecamatan: same area untuk data kecamatan, plus boleh view/print data desa dalam kecamatannya (monitoring) |
| 4.14.1a | `data-warga` | `Route::resource` + report pdf | `DataWargaPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.14.1b | `data-kegiatan-warga` | `Route::resource` + report pdf | `DataKegiatanWargaPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.14.2a | `data-keluarga` | `Route::resource` + report pdf | `DataKeluargaPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.14.2b | `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | `Route::resource` + report pdf | `DataPemanfaatanTanahPekaranganHatinyaPkkPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.14.2c | `data-industri-rumah-tangga` | `Route::resource` + report pdf | `DataIndustriRumahTanggaPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.14.3 | `data-pelatihan-kader` | `Route::resource` + report pdf | `DataPelatihanKaderPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.14.4a | `warung-pkk` | `Route::resource` + report pdf | `WarungPkkPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.14.4b | `taman-bacaan` | `Route::resource` + report pdf | `TamanBacaanPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.14.4c | `koperasi` | `Route::resource` + report pdf | `KoperasiPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.14.4d | `kejar-paket` | `Route::resource` + report pdf | `KejarPaketPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.14.4e | `posyandu` | `Route::resource` + report pdf | `PosyanduPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.14.4f | `simulasi-penyuluhan` | `Route::resource` + report pdf | `SimulasiPenyuluhanPolicy` | `V/C/U/D/P` | Level + area harus sama user |
| 4.15 | `catatan-keluarga` | `Route::get(index)` + report pdf | `CatatanKeluargaPolicy` | `V/P` | Rekap scoped dari `data-warga` + `data-kegiatan-warga` |

## 3) Invariant Otorisasi yang Harus Tetap Benar

- Tidak ada akses lintas scope (`desa` user ke route `kecamatan`, dan sebaliknya).
- Tidak ada akses jika `role` dan `scope` cocok tetapi `areas.level` pada `area_id` tidak cocok.
- Tidak ada akses lintas area untuk data `level` yang sama.
- `catatan-keluarga` tetap read-only (tanpa create/update/delete).

## 4) Bukti Validasi (T3)

Perintah yang dijalankan:
- `php artisan route:list`
- `php artisan route:list --json`

Ringkasan hasil route (name-based count):
- `anggota-tim-penggerak`: desa=9, kecamatan=9
- `kader-khusus`: desa=8, kecamatan=8
- `agenda-surat`: desa=9, kecamatan=9
- `buku-keuangan`: desa=8, kecamatan=8
- `bantuans`: desa=9, kecamatan=9 (termasuk alias report keuangan legacy)
- `inventaris`: desa=8, kecamatan=8
- `activities`: desa=8, kecamatan=8, `kecamatan_desa_monitoring`=3
- `data-warga`: desa=8, kecamatan=8
- `data-kegiatan-warga`: desa=8, kecamatan=8
- `data-keluarga`: desa=8, kecamatan=8
- `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`: desa=8, kecamatan=8
- `data-industri-rumah-tangga`: desa=8, kecamatan=8
- `data-pelatihan-kader`: desa=8, kecamatan=8
- `warung-pkk`: desa=8, kecamatan=8
- `taman-bacaan`: desa=8, kecamatan=8
- `koperasi`: desa=8, kecamatan=8
- `kejar-paket`: desa=8, kecamatan=8
- `posyandu`: desa=8, kecamatan=8
- `simulasi-penyuluhan`: desa=8, kecamatan=8
- `catatan-keluarga`: desa=2, kecamatan=2
