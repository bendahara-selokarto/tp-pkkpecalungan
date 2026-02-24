# TODO Implementasi Autentik Buku Bantu Lanjutan (2026-02-24)

## Konteks
- Sumber autentik: `docs/referensi/excel/BUKU BANTU.xlsx`.
- Pada sesi 2026-02-24 user memberikan 5 screenshot visual header untuk sheet:
  - `Buku Kader Khusus`
  - `Buku Prestasi`
  - `Buku Inventaris`
  - `Buku Anggota Pokja`
  - `BukuKelompok Simulasi`
- Status saat ini:
  - `Buku Kader Khusus`: implemented (PDF + copy UI + kontrak dokumen sinkron).
  - `Buku Prestasi`: implemented (copy UI + label PDF sinkron).
  - `Buku Inventaris`, `Buku Anggota Pokja`, `BukuKelompok Simulasi`: implemented (copy UI + label menu + label PDF sinkron).

## Target Hasil
- Kontrak header final 5 sheet tervalidasi hingga level merge cell (`rowspan`/`colspan`).
- Mapping `kolom autentik -> field input/storage/report` siap disusun tanpa ambigu.
- Tersedia rencana patch minimal lintas layer arsitektur.

## Langkah Eksekusi
- [x] Ambil bukti visual valid untuk 5 sheet (header utuh, garis sel terlihat, nomor kolom terlihat, teks terbaca).
- [x] Finalisasi peta header per sheet sampai level merge (`rowspan`/`colspan`).
- [x] Susun matrix mapping `kolom autentik -> field input/storage/report` per sheet untuk `Buku Kader Khusus` dan `Buku Prestasi`.
- [x] Susun matrix mapping `kolom autentik -> field input/storage/report` per sheet untuk `Buku Inventaris`, `Buku Anggota Pokja`, dan `BukuKelompok Simulasi`.
- [x] Audit dampak implementasi ke route/request/use case/repository/policy/inertia.
- [x] Definisikan test matrix minimum untuk akses scoped dan integritas data.
- [x] Jalankan doc-hardening jika muncul drift istilah/kontrak saat sinkronisasi mapping.

## Progress Implementasi (Parsial)
- [x] `Buku Kader Khusus`
  - Mapping terkunci:
    - `NAMA` -> `nama`
    - `JENIS KELAMIN (L/P)` -> `jenis_kelamin` (proyeksi report)
    - `TEMPAT TANGGAL LAHIR` -> `tempat_lahir` + `tanggal_lahir`
    - `STATUS (NIKAH/BLM NIKAH)` -> `status_perkawinan` (proyeksi report)
    - `ALAMAT` -> `alamat`
    - `PENDIDIKAN` -> `pendidikan`
    - `JENIS KADER KHUSUS` -> `jenis_kader_khusus`
    - `KETERANGAN` -> `keterangan`
  - Implementasi:
    - PDF `pdf.kader_khusus_report` disesuaikan ke header autentik 11 kolom.
    - UI label desa/kecamatan dinormalisasi ke istilah `Buku Kader Khusus`.
- [x] `Buku Prestasi`
  - Mapping tetap:
    - `TAHUN`, `JENIS LOMBA`, `LOKASI`, `PRESTASI (KEC/KAB/PROV/NAS)`, `KETERANGAN`.
  - Implementasi:
    - Label UI desa/kecamatan dinormalisasi ke istilah `Buku Prestasi`.
    - Judul PDF dinormalisasi ke `BUKU PRESTASI`.
- [x] `Buku Inventaris`
  - Mapping terkunci:
    - `NAMA BARANG` -> `name`
    - `ASAL BARANG` -> `asal_barang`
    - `TANGGAL PENERIMAAN/PEMBELIAN` -> `tanggal_penerimaan`
    - `JUMLAH` -> `quantity` + `unit`
    - `TEMPAT PENYIMPANAN` -> `tempat_penyimpanan`
    - `KONDISI BARANG` -> `condition`
    - `KETERANGAN` -> `keterangan` (fallback kompatibilitas `description`)
  - Implementasi:
    - Judul UI dinormalisasi ke istilah `Buku Inventaris` (desa/kecamatan, index/create/edit/show).
    - Header tabel daftar dinormalisasi ke `Daftar Buku Inventaris`.
- [x] `Buku Anggota Pokja`
  - Mapping terkunci:
    - `NAMA` -> `nama`
    - `JABATAN` -> `jabatan`
    - `JENIS KELAMIN (L/P)` -> `jenis_kelamin` (proyeksi report)
    - `TEMP, TGL/BLN/LAHIR (UMUR)` -> `tempat_lahir` + `tanggal_lahir` (+ `umur` turunan)
    - `STATUS (KAWIN/TIDAK KAWIN)` -> `status_perkawinan` (proyeksi report)
    - `ALAMAT` -> `alamat`
    - `PENDIDIKAN` -> `pendidikan`
    - `PEKERJAAN` -> `pekerjaan`
    - `KET` -> `keterangan`
  - Implementasi:
    - Judul UI dinormalisasi ke istilah `Buku Anggota Pokja` (desa/kecamatan, index/create/edit/show).
    - Judul PDF dinormalisasi ke `DAFTAR ANGGOTA POKJA I`.
- [x] `BukuKelompok Simulasi`
  - Mapping terkunci:
    - `NAMA KEGIATAN` -> `nama_kegiatan`
    - `JENIS SIMULASI/PENYULUHAN` -> `jenis_simulasi_penyuluhan`
    - `JUMLAH - KELOMPOK` -> `jumlah_kelompok`
    - `JUMLAH - SOSIALISASI` -> `jumlah_sosialisasi`
    - `JUMLAH KADER - L` -> `jumlah_kader_l`
    - `JUMLAH KADER - P` -> `jumlah_kader_p`
    - `KETERANGAN` -> `keterangan`
  - Implementasi:
    - Judul UI/menu dinormalisasi ke istilah `Kelompok Simulasi dan Penyuluhan`.
    - Judul PDF dinormalisasi ke `KELOMPOK SIMULASI DAN PENYULUHAN`.

## Bukti Visual dan Peta Header Terkunci

### Sheet `Buku Kader Khusus`
- Jumlah kolom data: 11.
- Grup header:
  - `JENIS KELAMIN` -> `L`, `P` (`colspan=2`).
  - `STATUS` -> `NIKAH`, `BLM NIKAH` (`colspan=2`).
- Header `rowspan=2`:
  - `NO`, `NAMA`, `TEMPAT TANGGAL LAHIR`, `ALAMAT`, `PENDIDIKAN`, `JENIS KADER KHUSUS`, `KETERANGAN`.
- Urutan kolom final:
  - 1 `NO`
  - 2 `NAMA`
  - 3 `JENIS KELAMIN - L`
  - 4 `JENIS KELAMIN - P`
  - 5 `TEMPAT TANGGAL LAHIR`
  - 6 `STATUS - NIKAH`
  - 7 `STATUS - BLM NIKAH`
  - 8 `ALAMAT`
  - 9 `PENDIDIKAN`
  - 10 `JENIS KADER KHUSUS`
  - 11 `KETERANGAN`

### Sheet `Buku Prestasi`
- Jumlah kolom data: 9.
- Grup header:
  - `PRESTASI/KEBERHASILAN YANG DICAPAI` -> `KECAMATAN`, `KABUPATEN`, `PROVINSI`, `NASIONAL` (`colspan=4`).
- Header `rowspan=2`:
  - `NO`, `TAHUN`, `JENIS LOMBA`, `LOKASI`, `KETERANGAN`.
- Urutan kolom final:
  - 1 `NO`
  - 2 `TAHUN`
  - 3 `JENIS LOMBA`
  - 4 `LOKASI`
  - 5 `PRESTASI - KECAMATAN`
  - 6 `PRESTASI - KABUPATEN`
  - 7 `PRESTASI - PROVINSI`
  - 8 `PRESTASI - NASIONAL`
  - 9 `KETERANGAN`

### Sheet `Buku Inventaris`
- Jumlah kolom data: 8.
- Tidak ada grup subkolom horizontal.
- Semua header utama efektif `rowspan=2` karena baris kedua dipakai nomor kolom.
- Urutan kolom final:
  - 1 `NO`
  - 2 `NAMA BARANG`
  - 3 `ASAL BARANG`
  - 4 `TANGGAL PENERIMAAN/PEMBELIAN`
  - 5 `JUMLAH`
  - 6 `TEMPAT PENYIMPANAN`
  - 7 `KONDISI BARANG`
  - 8 `KETERANGAN`

### Sheet `Buku Anggota Pokja`
- Jumlah kolom data: 12.
- Grup header:
  - `JENIS KELAMIN` -> `L`, `P` (`colspan=2`).
  - `STATUS` -> `KAWIN`, `TIDAK KAWIN` (`colspan=2`).
- Header `rowspan=2`:
  - `NO`, `NAMA`, `JABATAN`, `TEMP, TGL/BLN/LAHIR (UMUR)`, `ALAMAT`, `PENDIDIKAN`, `PEKERJAAN`, `KET`.
- Urutan kolom final:
  - 1 `NO`
  - 2 `NAMA`
  - 3 `JABATAN`
  - 4 `JENIS KELAMIN - L`
  - 5 `JENIS KELAMIN - P`
  - 6 `TEMP, TGL/BLN/LAHIR (UMUR)`
  - 7 `STATUS - KAWIN`
  - 8 `STATUS - TIDAK KAWIN`
  - 9 `ALAMAT`
  - 10 `PENDIDIKAN`
  - 11 `PEKERJAAN`
  - 12 `KET`

### Sheet `BukuKelompok Simulasi`
- Jumlah kolom data: 7.
- Grup header:
  - `JUMLAH` -> `KELOMPOK`, `SOSIALISASI` (`colspan=2`).
  - `JUMLAH KADER` -> `L`, `P` (`colspan=2`).
- Header `rowspan=2`:
  - `NO`, `NAMA KEGIATAN`, `JENIS SIMULASI/PENYULUHAN`.
- Urutan kolom final:
  - 1 `NO`
  - 2 `NAMA KEGIATAN`
  - 3 `JENIS SIMULASI/PENYULUHAN`
  - 4 `JUMLAH - KELOMPOK`
  - 5 `JUMLAH - SOSIALISASI`
  - 6 `JUMLAH KADER - L`
  - 7 `JUMLAH KADER - P`

## Validasi
- [x] Bukti visual 5 sheet valid dan terbaca.
- [x] Peta header final per sheet tidak ambigu untuk level merge/subkolom.
- [x] Mapping field disetujui untuk implementasi (`Buku Kader Khusus`, `Buku Prestasi`).
- [x] Mapping field untuk 3 sheet tersisa disetujui untuk implementasi.
- [x] Rencana test untuk 3 sheet tersisa terdefinisi dan dapat dijalankan.

## Test Matrix Concern
- [x] Feature akses scoped:
  - `DesaInventarisTest`, `KecamatanInventarisTest`
  - `DesaAnggotaPokjaTest`, `KecamatanAnggotaPokjaTest`
  - `DesaSimulasiPenyuluhanTest`, `KecamatanSimulasiPenyuluhanTest`
- [x] Feature report print scoped:
  - `SimulasiPenyuluhanReportPrintTest`
  - `StructuredDomainReportPrintTest`
- [x] Kontrak baseline PDF:
  - `PdfBaselineFixtureComplianceTest`
- [x] Sinkronisasi coverage dashboard-menu:
  - `DashboardCoverageMenuSyncTest`

## Risiko
- Istilah lokal seperti `KET`, `BLM NIKAH`, dan `TEMP, TGL/BLN/LAHIR (UMUR)` berpotensi drift saat dijadikan label UI/query.
- Sheet dengan subkolom (contoh `PRESTASI`, `JUMLAH`, `STATUS`) berisiko salah map jika naming field tidak dikunci sejak awal.

## Keputusan
- [x] Lima screenshot user pada sesi 2026-02-24 dikunci sebagai bukti kontrak visual header resmi.
- [x] Implementasi dilepas bertahap per concern; `Kader Khusus` dan `Prestasi` sudah sinkron.
- [x] Implementasi 3 sheet tersisa diselesaikan setelah matrix mapping dikunci.
