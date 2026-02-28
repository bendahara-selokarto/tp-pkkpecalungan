# TODO PDF26A1 Audit Ketersediaan Format PDF E2E

Tanggal: 2026-02-28  
Status: `done`  
Related ADR: `-`

## Konteks
- Permintaan user: pastikan semua format generate PDF memiliki UI, end-to-end valid, dan dimiliki minimal satu role.
- Scope audit mencakup:
  - route `*/report/pdf`
  - route `*/print` yang benar-benar menghasilkan PDF (activity detail print).

## Kontrak Concern (Lock)
- Domain: ketersediaan format PDF lintas modul.
- Role/scope target: `desa`, `kecamatan`, dan route dashboard berbasis `auth+verified`.
- Boundary data: route -> controller -> `loadView('pdf.*')` -> blade view -> trigger UI.
- Acceptance criteria:
  - [x] Semua route PDF punya trigger UI.
  - [x] Semua route PDF valid end-to-end (controller + view tersedia).
  - [x] Semua route PDF dimiliki minimal satu role/guard.
  - [x] Tidak ada PDF yatim tersisa.
- Dampak keputusan arsitektur: `tidak` (tidak mengubah boundary backend utama).

## Target Hasil
- [x] Laporan audit PDF terdokumentasi dalam markdown.
- [x] Registry kejadian PDF yatim tersedia dan terisi.
- [x] Jalur tracking berulang untuk audit berikutnya ditetapkan.

## Matriks Audit Ringkas
| Kategori | Jumlah | Status |
|---|---:|---|
| Route `report/pdf` | 103 | covered |
| Route `print` berbasis PDF (activity detail) | 3 | covered |
| Total route PDF diaudit | 106 | covered |
| Route dengan middleware `scope.role:desa` | 52 | covered |
| Route dengan middleware `scope.role:kecamatan` | 53 | covered |
| Route berbasis `auth+verified` (dashboard) | 1 | covered |

## Definisi PDF Yatim (Standar Audit)
- `A. Route yatim`: route PDF ada, tapi tidak ada trigger UI.
- `B. UI yatim`: trigger UI ada, tapi route PDF tidak terdaftar.
- `C. E2E yatim`: route ada, tapi controller/method/view PDF putus.
- `D. Role yatim`: route PDF tidak punya ownership akses minimal (`scope.role:*` atau guard yang disetujui).

## Temuan Audit 2026-02-28
- Kandidat awal dari scan literal UI: `12` route.
- Kandidat false-positive (jalur dinamis `scopePrefix`): `4` route.
- PDF yatim terkonfirmasi: `8` route (`A. Route yatim`).

Daftar 8 route yatim terkonfirmasi (sebelum perbaikan):
- `desa/buku-daftar-hadir/report/pdf`
- `desa/buku-notulen-rapat/report/pdf`
- `desa/buku-tamu/report/pdf`
- `kecamatan/buku-daftar-hadir/report/pdf`
- `kecamatan/buku-notulen-rapat/report/pdf`
- `kecamatan/buku-tamu/report/pdf`
- `desa/bantuans/keuangan/report/pdf`
- `kecamatan/bantuans/keuangan/report/pdf`

## Perbaikan yang Dilakukan
- Menambahkan trigger UI `Cetak PDF` pada:
  - `resources/js/Pages/Desa/BukuDaftarHadir/Index.vue`
  - `resources/js/Pages/Desa/BukuNotulenRapat/Index.vue`
  - `resources/js/Pages/Desa/BukuTamu/Index.vue`
  - `resources/js/Pages/Kecamatan/BukuDaftarHadir/Index.vue`
  - `resources/js/Pages/Kecamatan/BukuNotulenRapat/Index.vue`
  - `resources/js/Pages/Kecamatan/BukuTamu/Index.vue`
- Menambahkan trigger UI `Cetak PDF Keuangan` pada:
  - `resources/js/Pages/Desa/Bantuan/Index.vue`
  - `resources/js/Pages/Kecamatan/Bantuan/Index.vue`

## Validasi
- [x] Inventory route PDF: `php artisan route:list --path=report/pdf --json --except-vendor`
- [x] Inventory route print PDF: `php artisan route:list --path=print --json --except-vendor`
- [x] Verifikasi mapping UI route literal + dinamis (`scopePrefix` / `routes.print`) via `rg`
- [x] Verifikasi end-to-end controller PDF -> view PDF (`loadView('pdf.*')`) valid
- [x] Hasil akhir audit: `total=106; missing_ui=0; issues_controller_view=0`
- [ ] `php artisan test` penuh (tidak dijalankan; concern ini fokus audit linkage + UI trigger PDF)

## Registry PDF Yatim (Wajib Update Tiap Audit)
| ID | Tanggal | Jenis | Jumlah | Status | Ringkasan |
|---|---|---|---:|---|---|
| `PDFYATIM-20260228-01` | 2026-02-28 | `A. Route yatim` | 8 | `resolved` | Seluruh route yatim ditutup dengan penambahan trigger UI pada 8 jalur PDF. |

## Template Entri Registry Berikutnya
Gunakan format ini setiap audit:

```md
| ID | Tanggal | Jenis | Jumlah | Status | Ringkasan |
|---|---|---|---:|---|---|
| PDFYATIM-YYYYMMDD-XX | YYYY-MM-DD | A/B/C/D | N | open/resolved | Ringkasan temuan + tindakan |
```

## Tujuan Arsitektur Terstruktur Ketersediaan PDF
1. Gate inventaris route: semua endpoint PDF harus terdeteksi dari route registry.
2. Gate kontrak UI: semua endpoint PDF harus punya trigger UI yang dapat ditelusuri.
3. Gate integritas E2E: semua controller print harus punya view PDF valid.
4. Gate ownership akses: setiap endpoint PDF wajib punya ownership role/guard.
5. Gate incident log: setiap temuan PDF yatim wajib dicatat pada registry di dokumen ini.

## Protokol Audit Berkala (Operasional)
- [ ] Jalankan inventory route PDF dan print PDF.
- [ ] Jalankan scan trigger UI (`literal` + `dinamis`).
- [ ] Validasi controller -> view PDF.
- [ ] Klasifikasikan temuan dengan kategori yatim `A/B/C/D`.
- [ ] Update registry tabel di dokumen ini.
- [ ] Tutup gap sebelum rilis jika status masih `open`.

## Risiko
- Route dinamis dapat menghasilkan false-positive bila audit hanya berbasis pencarian literal URL.
- Drift dokumentasi dapat terjadi jika registry tidak diperbarui saat audit berikutnya.
- Endpoint dengan guard umum (`auth+verified`) perlu verifikasi owner operasional saat perubahan role matrix.

## Keputusan
- [x] K1: PDF yatim dikunci ke klasifikasi `A/B/C/D` untuk konsistensi lintas audit.
- [x] K2: Registry pada dokumen ini menjadi sumber log resmi kejadian PDF yatim concern ini.
- [x] K3: Audit dinamis (`scopePrefix`/`routes.print`) wajib dicatat sebagai evidence agar tidak dianggap yatim semu.

## Fallback Plan
- Jika audit berikutnya menemukan PDF yatim:
  - prioritas 1: pulihkan trigger UI jika route valid;
  - prioritas 2: hapus/disable trigger UI jika route tidak valid;
  - prioritas 3: sinkronkan route-controller-view dan ownership role sebelum release.

## Output Final
- [x] Ringkasan apa yang diubah dan kenapa.
- [x] Daftar file terdampak.
- [x] Hasil validasi + residual risk.
