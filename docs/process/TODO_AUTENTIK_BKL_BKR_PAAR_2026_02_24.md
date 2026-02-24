# TODO Autentik BKL, BKR, dan PAAR (2026-02-24)

## Konteks
- User memberikan bukti visual header tabel untuk:
  - `REKAPITULASI DATA KELOMPOK BKL`
  - `REKAPITULASI DATA KELOMPOK BKR`
  - `DATA POLA ASUH ANAK DAN REMAJA (PAAR)` (2 varian layout, struktur kolom sama)
- Dokumen ini mengunci kontrak header tabel berdasarkan verifikasi visual screenshot.
- Identitas file sumber (path workbook autentik) untuk concern ini belum dikonfirmasi di sesi yang sama, sehingga sinkronisasi implementasi tetap ditahan.

## Target Hasil
- Peta header final per format tervalidasi sampai level merge/subkolom.
- Mapping `kolom autentik -> field input/storage/report` bisa disusun tanpa ambigu.
- Deviasi antara tampilan autentik vs implementasi existing terpetakan sebelum patch.

## Status Umum
- [x] Verifikasi visual header tabel selesai untuk BKL, BKR, dan PAAR.
- [x] Peta header final tidak ambigu untuk ketiga format.
- [ ] Konfirmasi file sumber autentik (workbook/sheet) selesai.
- [ ] Matrix mapping field per format selesai.
- [ ] Audit dampak route/request/use case/repository/policy/inertia/report selesai.

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
- PAAR belum ditemukan modul/domain aktif pada codebase saat audit cepat concern ini.

## Langkah Lanjut (Minimal)
- [ ] Konfirmasi sumber file autentik concern ini (nama workbook + nama sheet).
- [ ] Bentuk matrix mapping kolom ke field domain:
  - BKL/BKR: validasi apakah perlu field tambahan metadata untuk kebutuhan tanda tangan/catatan cetak autentik.
  - PAAR: tetapkan kontrak penyimpanan indikator (fixed list vs master dinamis).
- [ ] Audit output PDF agar format tanda tangan, judul, dan metadata area sesuai format autentik.
- [ ] Siapkan test matrix minimum (feature + policy/scope + scoped data leak check).

## Progress Implementasi (2026-02-24)
- [x] Copy UI halaman daftar BKL/BKR dinormalisasi ke istilah autentik:
  - `Rekapitulasi Data Kelompok BKL`
  - `Rekapitulasi Data Kelompok BKR`
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
- [ ] Audit final tanda tangan + metadata area untuk PDF autentik tetap pending sampai konfirmasi sumber workbook/sheet selesai (untuk validasi final redaksi titik/placeholder per sheet).

## Risiko
- Tanpa konfirmasi file sumber, risiko drift nomenklatur sheet antar dokumen referensi masih ada.
- PAAR punya 2 varian metadata atas tabel; perlu diputuskan apakah beda scope (desa vs kecamatan) atau hanya variasi template.

## Keputusan
- [x] Screenshot user sesi 2026-02-24 dikunci sebagai bukti visual resmi untuk kontrak header concern ini.
- [x] Concern dinyatakan `siap sinkronisasi kontrak header`.
- [x] Concern tetap `belum siap implementasi` sampai mapping field dan konfirmasi sumber file selesai.
