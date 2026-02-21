# Adjustment Plan 4.14.1a Daftar Warga TP PKK

## Tujuan
- Menyesuaikan modul `data-warga` agar konsisten dengan dokumen autentik Lampiran `4.14.1a`.
- Menjaga kompatibilitas fitur existing (terutama `catatan-keluarga`) selama transisi.
- Menjamin output UI + PDF + dashboard tetap koheren dan aman secara scope.

## Status Eksekusi
- [x] `C1` Domain contract update (matrix, terminology, deviation log).
- [x] `C2` Data layer scaffold awal (`data_warga_anggotas` migration + model + relation).
- [x] `C3` backend HTTP/action payload detail anggota (request validation + repository sync + summary auto count).
- [x] `C4` UI Inertia table-input anggota (desa + kecamatan, create/edit).
- [x] `C5` PDF autentik 4.14.1a (judul + kolom 1-20, portrait).
- [x] `C6` Compatibility penuh `catatan-keluarga` dari detail anggota.
- [x] `C7` Dashboard trigger audit pasca perubahan kontrak.

## Sumber Autentik
- Dokumen: `d:\pedoman\153.pdf`
- Header utama terdeteksi:
  - `LAMPIRAN 4.14.1a`
  - `DAFTAR WARGA TP PKK`
- Elemen struktur yang terdeteksi:
  - Header form: `Dasa Wisma`, `Nama Kepala Rumah Tangga`.
  - Kolom tabel berurutan (1-20):
    - `No. Registrasi`
    - `No. KTP/KK`
    - `Nama`
    - `Jabatan`
    - `Jenis Kelamin` (terdeteksi sublabel `Perempuan`, indikasi ada split L/P)
    - `Tempat Lahir`
    - `Tgl. Lahir/Umur` (terdeteksi suffix `Tahun`)
    - `Status Perkawinan`
    - `Status Dalam Keluarga`
    - `Agama`
    - `Alamat ... Desa Kel/Sejenis`
    - `Pendidikan`
    - `Pekerjaan`
    - `Akseptor KB`
    - `Aktif dalam kegiatan Posyandu`
    - `Mengikuti Program Bina Keluarga Balita`
    - `Memiliki Tabungan`
    - `Mengikuti Kelompok Belajar (Jenis)`
    - `Mengikuti PAUD/Sejenis`
    - `Ikut dalam Kegiatan Koperasi`

## Gap Implementasi Saat Ini
- Kontrak data sekarang masih agregat (8 kolom PDF): `dasawisma`, `nama_kepala_keluarga`, `alamat`, `warga_l`, `warga_p`, `total`, `keterangan`.
- Form create/edit belum memfasilitasi tabel anggota keluarga berkolom 1-20.
- PDF `resources/views/pdf/data_warga_report.blade.php` belum mengikuti struktur autentik 1-20.
- `catatan-keluarga` bergantung pada `data_wargas` existing (risiko breaking change jika refactor langsung).

## Strategi Arsitektur (Disarankan)
- Gunakan model transisi non-breaking dua layer:
  - `data_wargas` dipertahankan sebagai header/summary rumah tangga (kompatibilitas).
  - Tambah tabel detail anggota, contoh: `data_warga_anggota` (1-n ke `data_wargas`) untuk kolom 1-20.
- `jumlah_warga_laki_laki` dan `jumlah_warga_perempuan` diarahkan menjadi nilai turunan dari detail anggota (bukan input manual) setelah fase stabil.
- Hindari perubahan besar sekali jalan pada route slug; pertahankan `data-warga`.

## Rencana Eksekusi (By Concern)

### C1. Domain Contract Update
- Update `docs/domain/DOMAIN_CONTRACT_MATRIX.md` untuk 4.14.1a versi autentik.
- Update `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md` untuk label kolom 1-20.
- Tambah catatan deviasi transisi di `docs/domain/DOMAIN_DEVIATION_LOG.md` jika ada kompromi tahap awal.

### C2. Data Layer
- Tambah migration tabel detail anggota (`data_warga_anggota`) dengan invariant:
  - `level`, `area_id`, `created_by`.
  - FK ke `data_wargas`.
- Update model + repository interface agar query detail tetap lewat boundary repository.
- Tambah normalizer untuk nilai boolean/opsi (KB, Posyandu, BKB, PAUD, Koperasi).

### C3. HTTP + UseCase
- Refactor request DTO/use case untuk menerima payload:
  - header rumah tangga (`dasawisma`, `nama_kepala_rumah_tangga`, area/sejenis).
  - daftar anggota (array baris 1-20).
- Validasi granular per kolom (format tanggal, umur, enum status, numeric, panjang teks).

### C4. UI Inertia
- Ubah form create/edit menjadi:
  - blok header rumah tangga.
  - table-input dinamis anggota keluarga (add/remove row).
- Show/index tampilkan ringkas (header + jumlah anggota), detail di halaman show.
- Pastikan komponen input number tetap memiliki lebar minimum agar tidak collapse.

### C5. PDF Alignment
- Refactor `resources/views/pdf/data_warga_report.blade.php` mengikuti layout autentik:
  - judul `DAFTAR WARGA TP PKK`.
  - header `Dasa Wisma` + `Nama Kepala Rumah Tangga`.
  - tabel kolom 1-20 sesuai urutan.
- Orientasi: set `portrait` eksplisit untuk lampiran ini jika hasil visual autentik menuntut portrait.
- Update baseline fixture `tests/Fixtures/pdf-baseline/4.14.1a-data-warga.json`.

### C6. Compatibility Catatan Keluarga
- Jaga `catatan-keluarga` tetap berjalan:
  - fase transisi: baca summary existing.
  - fase stabil: hitung jumlah anggota rumah tangga dari tabel detail.
- Tambahkan anti-regression test untuk mencegah kebocoran data/behavior drift.

### C7. Dashboard Trigger (Wajib)
- Karena ini perubahan menu/domain behavior, jalankan audit dashboard:
  - pastikan `data-warga` tetap terhitung pada `DashboardDocumentCoverage`.
  - update chart/summary jika kontrak count berubah (summary vs detail row).
- Jalankan:
  - `php artisan test --filter=DashboardDocumentCoverageTest`
  - `php artisan test --filter=DashboardActivityChartTest` (jika kontrak dashboard berubah)

## Test Matrix Minimum
- Feature:
  - jalur sukses desa/kecamatan.
  - tolak role tidak valid.
  - tolak mismatch role-area level (stale metadata).
- Unit:
  - policy `view/update/delete/print`.
  - use case/repository detail anggota anti data leak.
- PDF:
  - `php artisan test --filter=header_kolom_pdf`
  - `php artisan test --filter=PdfBaselineFixtureComplianceTest`

## Definition of Done
- Form + penyimpanan + PDF 4.14.1a identik secara struktur dengan dokumen autentik.
- Tidak ada regressi pada scope/policy dan `catatan-keluarga`.
- Dashboard coverage tetap representatif pasca perubahan.
- Semua gate test relevan hijau.
