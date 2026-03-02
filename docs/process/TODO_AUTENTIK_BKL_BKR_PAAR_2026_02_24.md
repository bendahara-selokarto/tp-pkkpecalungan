# TODO Autentik BKL, BKR, dan PAAR (2026-02-24)
Tanggal: 2026-02-24  
Status: `done`

## Konteks
- User memberikan bukti visual header tabel untuk:
  - `REKAPITULASI DATA KELOMPOK BKL`
  - `REKAPITULASI DATA KELOMPOK BKR`
  - `DATA POLA ASUH ANAK DAN REMAJA (PAAR)` (2 varian layout, struktur kolom sama)
- Dokumen ini mengunci kontrak header tabel berdasarkan verifikasi visual screenshot.
- Referensi terakhir user untuk concern ini dikunci sebagai acuan final:
  - Workbook: `docs/referensi/excel/BUKU BANTU.xlsx`
  - Untuk referensi ganda, berlaku aturan `referensi terakhir menjadi acuan final`.

## Target Hasil
- Peta header final per format tervalidasi sampai level merge/subkolom.
- Mapping `kolom autentik -> field input/storage/report` bisa disusun tanpa ambigu.
- Deviasi antara tampilan autentik vs implementasi existing terpetakan sebelum patch.

## Status Umum
- [x] Verifikasi visual header tabel selesai untuk BKL, BKR, dan PAAR.
- [x] Peta header final tidak ambigu untuk ketiga format.
- [x] Konfirmasi file sumber autentik (workbook/sheet) selesai.
- [x] Matrix mapping field per format selesai.
- [x] Audit dampak route/request/use case/repository/policy/inertia/report selesai.

## Peta Header Final

### 1) Rekapitulasi Data Kelompok BKL
- Judul: `REKAPITULASI DATA KELOMPOK BKL`.
- Metadata atas tabel:
  - `KEC ..........`
- Header tabel: 1 baris, tanpa subkolom.
- Jumlah kolom: 7.
- Urutan kolom:
  - 1 `NO`
  - 2 `DESA`
  - 3 `NAMA BKL`
  - 4 `NO/TGL SK`
  - 5 `NAMA KETUA KELOMPOK`
  - 6 `JUMLAH ANGGOTA`
  - 7 `KEGIATAN`

### 2) Rekapitulasi Data Kelompok BKR
- Judul: `REKAPITULASI DATA KELOMPOK BKR`.
- Metadata atas tabel:
  - `KEC ..........`
- Header tabel: 1 baris, tanpa subkolom.
- Jumlah kolom: 7.
- Urutan kolom:
  - 1 `NO`
  - 2 `DESA`
  - 3 `NAMA BKR`
  - 4 `NO/TGL SK`
  - 5 `NAMA KETUA KELOMPOK`
  - 6 `JUMLAH ANGGOTA`
  - 7 `KEGIATAN`

### 3) Data Pola Asuh Anak dan Remaja (PAAR)
- Judul: `DATA POLA ASUH ANAK DAN REMAJA (PAAR)`.
- Varian metadata atas tabel:
  - Varian A: tanpa baris `DESA/KEC` (hanya tabel utama).
  - Varian B: ada baris `DESA :` dan `KEC :`.
  - Keputusan final: gunakan **Varian B** mengikuti referensi terakhir user.
- Header tabel: 1 baris, tanpa subkolom.
- Jumlah kolom: 4.
- Urutan kolom:
  - 1 `NO`
  - 2 `INDIKATOR`
  - 3 `JUMLAH`
  - 4 `KETERANGAN`
- Daftar indikator tetap yang terlihat:
  - 1 `Jumlah Penduduk yang mempunyai Akte Kelahiran`
  - 2 `Jumlah Anak yang mempunyai Kartu Identitas Anak (KIA)`
  - 3 `Kasus Kekerasan Seksual pada Anak`
  - 4 `Kasus Kekerasan Dalam Rumah Tangga`
  - 5 `Kasus Perdagangan Anak (Trafficking)`
  - 6 `Kasus Narkoba`

## Catatan Sinkronisasi dengan Implementasi Saat Ini
- BKL/BKR pada implementasi existing sudah memakai struktur kolom yang sama dengan header autentik utama (7 kolom).
- PAAR sudah diimplementasikan sebagai modul domain aktif (scope `desa` dan `kecamatan`) dengan kontrak tabel `paars`.

## Langkah Lanjut (Minimal)
- [x] Konfirmasi sumber file autentik concern ini (nama workbook + nama sheet).
- [x] Bentuk matrix mapping kolom ke field domain:
  - BKL/BKR: validasi field report + metadata cetak.
  - PAAR: kontrak indikator ditetapkan sebagai fixed list (6 indikator autentik).
- [x] Audit output PDF agar format tanda tangan, judul, dan metadata area sesuai format autentik.
- [x] Siapkan test matrix minimum (feature + policy/scope + scoped data leak check).

## Progress Implementasi (2026-02-24)
- [x] Copy UI halaman daftar BKL/BKR dinormalisasi ke istilah autentik:
  - `Rekapitulasi Data Kelompok BKL`
  - `Rekapitulasi Data Kelompok BKR`
- [x] Copy UI halaman tambah/edit/detail BKL/BKR dinormalisasi ke istilah `Data Kelompok`.
- [x] Flash message aksi create/update/delete BKL/BKR dinormalisasi agar konsisten dengan copy UI.
- [x] Judul PDF BKL/BKR diselaraskan ke format autentik:
  - `REKAPITULASI DATA KELOMPOK BKL`
  - `REKAPITULASI DATA KELOMPOK BKR`
- [x] Metadata cetak PDF BKL/BKR diselaraskan ke format visual autentik:
  - Baris metadata atas tabel menggunakan pola `KEC ...`.
  - Blok tanda tangan bawah tabel disamakan (`Mengetahui`, `Batang`, `KETUA TP. PKK`, `KETUA POKJA I`).
  - Catatan footer BKL `Keterangan : Diisi oleh TP. PKK Kecamatan` ditambahkan.
- [x] Validasi regresi terarah lulus:
  - `tests/Feature/DesaBklTest.php`
  - `tests/Feature/KecamatanBklTest.php`
  - `tests/Feature/DesaBkrTest.php`
  - `tests/Feature/KecamatanBkrTest.php`
  - `tests/Feature/BklReportPrintTest.php`
  - `tests/Feature/BkrReportPrintTest.php`
- [x] Modul PAAR terimplementasi end-to-end:
  - backend: route, request, use case/action, repository, policy/scope, migration `paars`.
  - frontend: halaman desa/kecamatan (`index/create/edit/show`) + menu Pokja I.
  - report: PDF `DATA POLA ASUH ANAK DAN REMAJA (PAAR)` dengan metadata `DESA/KEC`.
- [x] Validasi regresi terarah PAAR lulus:
  - `tests/Feature/DesaPaarTest.php`
  - `tests/Feature/KecamatanPaarTest.php`
  - `tests/Feature/PaarReportPrintTest.php`
  - `tests/Unit/Policies/PaarPolicyTest.php`

## Risiko
- PAAR punya 2 varian metadata atas tabel; risiko drift ditutup dengan keputusan referensi terakhir (Varian B).
- Risiko tersisa: perubahan template autentik di luar referensi sesi ini perlu audit ulang kontrak header.

## Keputusan
- [x] Screenshot user sesi 2026-02-24 dikunci sebagai bukti visual resmi untuk kontrak header concern ini.
- [x] Concern dinyatakan `siap sinkronisasi kontrak header`.
- [x] Concern dinyatakan `siap implementasi` setelah mapping field + konfirmasi sumber + validasi test terarah selesai.
