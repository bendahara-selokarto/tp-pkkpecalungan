# TODO ARO25A1 Audit Kepemilikan Modul dan Penempatan Role

Tanggal: 2026-02-25  
Status: `done`

Catatan supersede 2026-02-27:
- Ownership `program-prioritas` sudah dipindah ke grup `sekretaris-tpk` (bukan `pokja-iv`) mengikuti sinkronisasi canonical terbaru pada `RoleMenuVisibilityService` dan `DOMAIN_CONTRACT_MATRIX`.

## Konteks

- Diperlukan checklist menyeluruh untuk memetakan seluruh modul operasional yang ada di sistem.
- Checklist ini dipakai untuk audit: modul saat ini dimiliki role apa saja, level aksesnya (`read-write`/`read-only`), dan tindak lanjut jika ada salah penempatan role.
- Source of truth audit akses modul:
  - `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
  - `app/Http/Middleware/EnsureModuleVisibility.php`
  - `routes/web.php`

## Target Hasil

- Tersedia satu dokumen checklist operasional untuk audit penempatan role per modul.
- Setiap modul punya kolom aksi perbaikan role yang bisa diisi saat temuan audit muncul.
- Audit bisa dijalankan berulang tanpa ambigu (format tetap, kolom tetap).

## Update 2026-02-25 (Hasil Koreksi Domain)

Sumber:
- `docs/referensi/Pemetaan Modul.xlsx`

Validasi struktur header tabel:
- Sheet: `Daftar Modul`.
- Dimensi data: `A1:W34`.
- Merge header tervalidasi: `A1:A3`, `B1:B3`, `C1:C3`, `D1:M1` (Kecamatan), `N1:W1` (Desa), pasangan role baris 2 (`D2:E2` s.d. `V2:W2`).
- Header operasional yang dipakai: `Kecamatan/Desa -> Sekretaris/Pokja I-IV -> RW/RO`.

Koreksi nama modul (kolom `Nama Modul yang benar`) yang dikunci:
- `Buku Anggota Tim Penggerak` -> `Buku Daftar Anggota Tim Penggerak PKK`
- `Buku Anggota Tim Penggerak Kader` -> `Buku Daftar Anggota TP PKK dan Kader`
- `Buku Agenda Surat` -> `Buku Agenda Surat Masuk/Keluar`
- `Data PAAR` -> `Buku PAAR`
- `Data Industri Rumah Tangga` -> `Buku Industri Rumah Tangga`
- `Data HATINYA PKK` -> `Buku HATINYA PKK`

Temuan delta ownership terhadap implementasi saat ini:
- Koreksi domain menandai banyak modul Pokja sebagai `RW Desa` saja (tanpa `RW Kecamatan`), sedangkan implementasi backend saat ini masih memberi `RW` untuk desa + kecamatan pada grup Pokja.
- `data-pelatihan-kader` ditandai `tidak usah`.
- `catatan-keluarga`, `program-prioritas`, `pilot-project-naskah-pelaporan`, `pilot-project-keluarga-sehat`, `desa-activities` tidak ditandai owner `RW/RO` pada hasil koreksi.

Keputusan sesi ini:
- Sinkronisasi label modul untuk dokumen audit/generator sudah diterapkan pada script ekspor.
- Perubahan otorisasi runtime belum diterapkan karena membutuhkan keputusan eksplisit pada boundary `RoleMenuVisibilityService` + test matrix.

## Update 2026-02-25 (Eksekusi Runtime Batch-2)

Perubahan runtime yang dieksekusi:
- `RoleMenuVisibilityService` menambahkan override modul per-role (`ROLE_MODULE_MODE_OVERRIDES`) untuk menurunkan akses `kecamatan-pokja-*` dari `read-write` ke `read-only` pada modul pokja desa-only:
  - `kecamatan-pokja-i`: `bkl`, `bkr`, `paar`, `data-warga`, `data-kegiatan-warga`
  - `kecamatan-pokja-ii`: `taman-bacaan`, `koperasi`, `kejar-paket`
  - `kecamatan-pokja-iii`: `data-keluarga`, `data-industri-rumah-tangga`, `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, `warung-pkk`
  - `kecamatan-pokja-iv`: `posyandu`, `simulasi-penyuluhan`

Validasi runtime batch-2:
- `tests/Unit/Services/RoleMenuVisibilityServiceTest.php` lulus.
- `tests/Feature/ModuleVisibilityMiddlewareTest.php` lulus (termasuk guard RO untuk `kecamatan-pokja-i` pada `data-warga`).

## Update 2026-02-25 (Konfirmasi Tabel untuk UI Eksperimental)

- Konfirmasi tabel terbaru yang diterima:
  - `Data Warga` dan `Data Kegiatan Warga`: `hanya sekretaris`.
  - `Kelompok Simulasi dan Penyuluhan`: `role pokja 1 desa`.
  - Baris `27-31`: `tidak digunakan`.
- Dampak pada concern ini:
  - Belum dieksekusi sebagai perubahan ownership runtime/E2E.
  - Diperlakukan sebagai input untuk concern `C-SIDEBAR-UI` (penempatan modul eksperimen UI-only).
  - Authority akses final tetap mengikuti backend (`RoleMenuVisibilityService` + middleware), sampai ada keputusan runtime terpisah.

## Definisi Kolom

- `Desa RW`: role yang saat ini punya hak tulis di scope desa.
- `Desa RO`: role yang saat ini hanya baca di scope desa.
- `Kecamatan RW`: role yang saat ini punya hak tulis di scope kecamatan.
- `Kecamatan RO`: role yang saat ini hanya baca di scope kecamatan.
- `Checklist Perbaikan Role`: centang jika ditemukan salah penempatan role.
- `Catatan Audit`: isi perubahan yang diminta (role yang harus ditambah/dicabut + alasan).

## Checklist Global Audit

- [x] Validasi modul pada matrix sesuai modul route aktif di `routes/web.php`.
- [x] Validasi mode akses tiap modul terhadap kontrak `RoleMenuVisibilityService`.
- [x] Sinkronisasi hasil koreksi nama modul ke generator dokumen audit.
- [x] Tandai modul yang salah penempatan role pada kolom `Checklist Perbaikan Role`.
- [x] Isi `Catatan Audit` dengan usulan koreksi role yang eksplisit.
- [x] Buat concern implementasi terpisah untuk setiap perubahan role yang disetujui.

## Matrix Modul x Role Saat Ini

| Modul | Grup | Desa RW | Desa RO | Kecamatan RW | Kecamatan RO | Checklist Perbaikan Role | Catatan Audit |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `activities` | pokja-i, pokja-ii, pokja-iii, pokja-iv, sekretaris-tpk | admin-desa, admin-kecamatan, desa-pokja-i, desa-pokja-ii, desa-pokja-iii, desa-pokja-iv, desa-sekretaris, kecamatan-pokja-i, kecamatan-pokja-ii, kecamatan-pokja-iii, kecamatan-pokja-iv, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-pokja-i, desa-pokja-ii, desa-pokja-iii, desa-pokja-iv, desa-sekretaris, kecamatan-pokja-i, kecamatan-pokja-ii, kecamatan-pokja-iii, kecamatan-pokja-iv, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `agenda-surat` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `anggota-pokja` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `anggota-tim-penggerak` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `anggota-tim-penggerak-kader` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `bantuans` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `bkl` | pokja-i | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-i` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `bkr` | pokja-i | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-i` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `buku-keuangan` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `catatan-keluarga` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Hapus owner `RW/RO` pokja untuk modul ini sesuai koreksi domain; tetapkan akses via sekretaris/monitoring sesuai keputusan produk. |
| `data-industri-rumah-tangga` | pokja-iii | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-iii` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `data-kegiatan-warga` | pokja-i | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-i` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `data-keluarga` | pokja-iii | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-iii` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `data-pelatihan-kader` | pokja-ii | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Modul ditandai `tidak usah` pada koreksi domain; rekomendasi: keluarkan dari matrix visibilitas aktif setelah konfirmasi bisnis. |
| `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | pokja-iii | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-iii` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `data-warga` | pokja-i | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-i` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `desa-activities` | monitoring | - | - | super-admin | admin-kecamatan, kecamatan-sekretaris | [x] Perlu koreksi | Koreksi domain tidak menandai owner `RW/RO`; validasi ulang apakah modul tetap monitoring-only atau dipindah keluar matrix ownership. |
| `inventaris` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `kader-khusus` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `kejar-paket` | pokja-ii | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-ii` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `koperasi` | pokja-ii | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-ii` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `laporan-tahunan-pkk` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `paar` | pokja-i | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-i, kecamatan-pokja-i, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-i` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `pilot-project-keluarga-sehat` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Hapus owner `RW/RO` pokja untuk modul ini sesuai koreksi domain; akses ditetapkan ulang via keputusan lintas peran. |
| `pilot-project-naskah-pelaporan` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Hapus owner `RW/RO` pokja untuk modul ini sesuai koreksi domain; akses ditetapkan ulang via keputusan lintas peran. |
| `posyandu` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-iv` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `prestasi-lomba` | sekretaris-tpk | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | admin-desa, admin-kecamatan, desa-sekretaris, kecamatan-sekretaris, super-admin | - | [ ] Perlu koreksi | - |
| `program-prioritas` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Hapus owner `RW/RO` pokja untuk modul ini sesuai koreksi domain; akses ditetapkan ulang via keputusan lintas peran. |
| `simulasi-penyuluhan` | pokja-iv | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iv, kecamatan-pokja-iv, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-iv` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `taman-bacaan` | pokja-ii | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-ii, kecamatan-pokja-ii, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-ii` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |
| `warung-pkk` | pokja-iii | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | admin-desa, admin-kecamatan, desa-pokja-iii, kecamatan-pokja-iii, super-admin | desa-sekretaris, kecamatan-sekretaris | [x] Perlu koreksi | Cabut `kecamatan-pokja-iii` dari RW untuk modul pokja desa-only sesuai koreksi domain (`RW Desa` saja). |

## Concern Implementasi Turunan (Disetujui untuk Ditindaklanjuti)

- [x] Concern A (pokja desa-only): `docs/process/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_POKJA_DESA_ONLY_2026_02_25.md`.
- [x] Concern B (modul non-ownership RW/RO): `docs/process/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_NON_RW_RO_2026_02_25.md`.
- [x] Concern C (deprecate modul `data-pelatihan-kader`): `docs/process/TODO_IMPLEMENTASI_ROLE_OWNERSHIP_DEPRECATE_DATA_PELATIHAN_KADER_2026_02_25.md`.

## Modul Global (Non Scope Prefix `desa|kecamatan`)

| Modul | Route Utama | Role Saat Ini | Checklist Perbaikan Role | Catatan Audit |
| --- | --- | --- | --- | --- |
| `dashboard` | `/dashboard` | semua user login non super-admin (super-admin dialihkan ke `super-admin.users.index`) | [ ] Perlu koreksi | - |
| `profile` | `/profile` | semua user login | [ ] Perlu koreksi | - |
| `super-admin.users` | `/super-admin/users` | super-admin | [ ] Perlu koreksi | - |

## Risiko

- Matrix ini merepresentasikan kondisi saat ini; perubahan role selanjutnya harus memperbarui dokumen ini.
- Peran legacy (`admin-desa`, `admin-kecamatan`) masih muncul karena mode kompatibilitas.

## Keputusan

- [x] Checklist audit role-modul dikunci berbasis source of truth backend, bukan asumsi UI.
- [x] Kolom perbaikan role disiapkan agar bisa dipakai tim domain saat validasi penempatan akses.
