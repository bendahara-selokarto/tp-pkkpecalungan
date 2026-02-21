# PDF Compliance Checklist (Lampiran 4.9-4.15)

Tujuan:
- Menjadi checklist baku verifikasi output PDF agar identik dengan pedoman domain utama.
- Menjadi baseline regression untuk perubahan header, urutan kolom, format nilai, orientasi, dan metadata cetak.

Sumber acuan:
- Pedoman domain utama: https://pubhtml5.com/zsnqq/vjcf/basic/101-150
- Validasi awal format: `docs/pdf/VALIDASI_FORMAT_BUKU_SEKRETARIS_PDF.md`
- Kontrak domain: `docs/domain/DOMAIN_CONTRACT_MATRIX.md`

## A. Checklist Global (Wajib untuk semua modul)

| Item | Kriteria lulus |
| --- | --- |
| Judul report | Harus sama dengan label pedoman pada lampiran terkait (huruf besar/kecil boleh berbeda, makna tidak boleh berubah). |
| Header tabel | Semua kolom wajib ada; urutan kolom harus sesuai pedoman. |
| Urutan data | Nomor urut dan urutan baris stabil sesuai aturan modul (umumnya sort by `id`/tanggal). |
| Format nilai | Tanggal, angka, boolean, dan teks kosong harus konsisten (`-`/`Ya-Tidak`/format tanggal). |
| Orientasi PDF | Default `landscape` (sesuai `PdfViewFactory`); `portrait` hanya jika diminta eksplisit. |
| Footer metadata cetak | Wajib ada informasi minimal: area, dicetak oleh, dicetak pada (lokasi boleh header/footer selama konsisten). |
| Scope output | Sample PDF `desa` dan `kecamatan` harus menampilkan data sesuai scope area user. |

## B. Checklist Per Modul

Keterangan status:
- `pending`: belum divalidasi pada siklus ini.
- `pass`: sudah diverifikasi sesuai pedoman.
- `fail`: ada mismatch yang harus diperbaiki.

Siklus validasi terbaru:
- `2026-02-21`: validasi otomatis lulus via:
  - `php artisan test --filter=PdfBaselineFixtureComplianceTest`
  - `php artisan test --filter=header_kolom_pdf`
  - `php artisan test --filter=ReportPrintTest`

| Lampiran | Modul | File PDF | Acuan urutan kolom/header | Format nilai kritikal | Orientasi | Footer metadata cetak | Status |
| --- | --- | --- | --- | --- | --- | --- | --- |
| 4.9a | `anggota-tim-penggerak` | `resources/views/pdf/anggota_tim_penggerak_report.blade.php` | No, Nama, Jabatan, JK, TTL, Status, Alamat, Pendidikan, Pekerjaan, Ket. | Tanggal lahir valid; field kosong jadi `-` | `landscape` | area + printedBy + printedAt | `pass` |
| 4.9b | `kader-khusus` | `resources/views/pdf/kader_khusus_report.blade.php` | No, Nama, JK, TTL, Status, Alamat, Pendidikan, Jenis Kader, Ket. | Enum/teks kader konsisten | `landscape` | area + printedBy + printedAt | `pass` |
| 4.10 | `agenda-surat` | `resources/views/pdf/agenda_surat_report.blade.php` | Group SURAT MASUK dan SURAT KELUAR sesuai template | Tanggal surat/terima konsisten; nomor surat tampil | `landscape` | area + printedBy + printedAt | `pass` |
| 4.11 | `bantuans` (keuangan) | `resources/views/pdf/buku_keuangan_report.blade.php` | No, Tanggal, Uraian, Pemasukan, Pengeluaran, Saldo | Nominal uang, saldo berjalan, kategori debit/kredit | `landscape` | area + printedBy + printedAt | `pass` |
| 4.12 | `inventaris` | `resources/views/pdf/inventaris_report.blade.php` | No, Nama Barang, Asal, Tgl Terima, Jumlah, Satuan, Kondisi, Lokasi, Ket. | Angka jumlah valid; kondisi sesuai enum | `landscape` | area + printedBy + printedAt | `pass` |
| 4.13 | `activities` | `resources/views/pdf/activity.blade.php` | Identitas kegiatan, petugas, jabatan, tanggal, uraian, tanda tangan | Tanggal kegiatan dan status valid | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.1a | `data-warga` | `resources/views/pdf/data_warga_report.blade.php` | No, Dasawisma, Kepala Keluarga, Alamat, L, P, Total, Ket. | Total = L + P | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.1b | `data-kegiatan-warga` | `resources/views/pdf/data_kegiatan_warga_report.blade.php` | No, Kegiatan, Aktivitas, Ket. | Aktivitas tampil `Ya/Tidak` | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.2a | `data-keluarga` | `resources/views/pdf/data_keluarga_report.blade.php` | No, Kategori Keluarga, Jumlah, Ket. | Nilai jumlah numerik non-negatif | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.2b | `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | `resources/views/pdf/data_pemanfaatan_tanah_pekarangan_hatinya_pkk_report.blade.php` | No, Kategori Lahan, Komoditi, Jumlah | Nilai jumlah konsisten satuan | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.2c | `data-industri-rumah-tangga` | `resources/views/pdf/data_industri_rumah_tangga_report.blade.php` | No, Kategori Industri, Komoditi, Jumlah | Nilai jumlah konsisten satuan | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.3 | `data-pelatihan-kader` | `resources/views/pdf/data_pelatihan_kader_report.blade.php` | No Reg, Nama, Tgl/Th Masuk, Jabatan, No Urut, Judul, Kriteria, Tahun, Institusi, Sertifikat | Tahun numerik; status sertifikat valid | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4a | `warung-pkk` | `resources/views/pdf/warung_pkk_report.blade.php` | No, Nama Warung, Pengelola, Komoditi, Kategori, Volume | Volume/kategori tidak kosong | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4b | `taman-bacaan` | `resources/views/pdf/taman_bacaan_report.blade.php` | No, Nama, Pengelola, Jml Buku, Jenis Buku, Kategori, Jumlah | Jumlah numerik/string konsisten | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4c | `koperasi` | `resources/views/pdf/koperasi_report.blade.php` | No, Nama Koperasi, Jenis Usaha, Berbadan Hukum, Belum BH, Anggota L, Anggota P | Boolean tampil `Ya/-`; jumlah anggota numerik | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4d | `kejar-paket` | `resources/views/pdf/kejar_paket_report.blade.php` | No, Nama, Jenis, Warga Belajar L/P, Pengajar L/P | Nilai L/P numerik | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4e | `posyandu` | `resources/views/pdf/posyandu_report.blade.php` | No, Nama, Pengelola, Sekretaris, Jenis, Kader, Kegiatan, Frek, Pengunjung L/P, Petugas L/P | Seluruh angka kuantitatif numerik | `landscape` | area + printedBy + printedAt | `pass` |
| 4.14.4f | `simulasi-penyuluhan` | `resources/views/pdf/simulasi_penyuluhan_report.blade.php` | No, Nama Kegiatan, Jenis, Jml Kelompok, Jml Sosialisasi, Kader L/P, Ket. | Nilai jumlah numerik | `landscape` | area + printedBy + printedAt | `pass` |
| 4.15 | `catatan-keluarga` | `resources/views/pdf/catatan_keluarga_report.blade.php` | No, Nama KRT, Jml ART, Kerja Bakti, Rukun Kematian, Keagamaan, Jimpitan, Arisan, Lain-lain, Ket. | Nilai kegiatan tampil `Ya/Tidak` | `landscape` | area + printedBy + printedAt | `pass` |

## C. Prosedur Eksekusi Checklist

1. Generate sample PDF `desa` dan `kecamatan` untuk modul yang diuji.
2. Cocokkan judul + header + urutan kolom dengan pedoman.
3. Cocokkan format nilai (tanggal/angka/boolean/empty).
4. Verifikasi orientasi output adalah `landscape` (default).
5. Verifikasi metadata cetak (area, printedBy, printedAt) tersedia.
6. Catat hasil `pass/fail` dan deviasi ke `docs/domain/DOMAIN_DEVIATION_LOG.md` jika ada.


