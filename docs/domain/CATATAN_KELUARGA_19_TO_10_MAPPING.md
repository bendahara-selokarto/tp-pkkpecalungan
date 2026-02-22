# Mapping Catatan Keluarga (Autentik 19 Kolom -> Operasional 10 Kolom)

Tanggal pemetaan: 2026-02-22

Sumber autentik:
- `d:\pedoman\177.pdf` (Lampiran 4.15, Catatan Keluarga)

Tujuan:
- Mengunci kontrak transformasi dari layout formulir autentik (19 kolom fisik) ke layout report operasional aplikasi (10 kolom).
- Menjelaskan batasan pembacaan otomatis PDF berbasis Node.js pada dokumen ini.

## Struktur Header Autentik (Fisik)

Konfirmasi layout fisik berdasarkan dokumen autentik:
- Kolom `1-10`: masing-masing header `rowspan=2`.
- Kolom `11-18`: baris 1 digabung menjadi satu header grup `KEGIATAN PKK YANG DIIKUTI`, baris 2 dipecah per kolom.
- Kolom `19`: header `rowspan=2`.

Catatan:
- Dokumen ini mengunci struktur fisik kolom.
- Keputusan fase saat ini: implementasi operasional tetap 10 kolom, sehingga kontrak referensi autentik dipertahankan pada level blok transformasi 19 -> 10.
- Jika roadmap berubah ke implementasi 19 kolom penuh, transkripsi detail per kolom dilakukan pada fase migrasi khusus (bukan OCR tekstual otomatis).

## Kontrak Transformasi ke Report Operasional

Representasi report saat ini memakai 10 kolom (`resources/views/pdf/catatan_keluarga_report.blade.php`):
1. `NO`
2. `NAMA KEPALA RUMAH TANGGA`
3. `JUMLAH ANGGOTA RUMAH TANGGA`
4. `KERJA BAKTI`
5. `RUKUN KEMATIAN`
6. `KEAGAMAAN`
7. `JIMPITAN`
8. `ARISAN`
9. `LAIN-LAIN`
10. `KETERANGAN`

### Mapping Blok

| Blok autentik 19 kolom | Bentuk autentik | Proyeksi report 10 kolom | Sumber data aplikasi | Status |
| --- | --- | --- | --- | --- |
| `1-10` | Header individual (`rowspan=2`) | Diproyeksikan parsial menjadi kolom 1-3 report | `DataWarga` (`nama_kepala_keluarga`, `total_warga`) | partial |
| `11-18` | Grup `KEGIATAN PKK YANG DIIKUTI` (baris 1 merge, baris 2 detail) | Diproyeksikan parsial menjadi kolom 4-9 report | `DataKegiatanWarga` -> `CatatanKeluargaRepository` | partial |
| `19` | Header individual (`rowspan=2`) | Diproyeksikan ke kolom 10 report (`KETERANGAN`) | `DataWarga.keterangan` | match |

### Aktivitas yang sudah termodelkan saat ini

Aplikasi saat ini memetakan aktivitas ke flag `Ya/Tidak` untuk:
- `Kerja Bakti`
- `Rukun Kematian`
- `Kegiatan Keagamaan`
- `Jimpitan`
- `Arisan`
- `Lain-Lain`

Kegiatan yang tersedia pada master `DataKegiatanWarga` tetapi belum diproyeksikan ke report catatan keluarga:
- `Penghayatan dan Pengamalan Pancasila`

## Temuan Akurasi Baca Node.js (dokumentasi teknis)

Pengukuran pada `d:\pedoman\177.pdf`:
- Identitas form (mis. `LAMPIRAN 4.15`, `CATATAN KELUARGA`, `ANGGOTA KELOMPOK DASA WISMA`) terbaca konsisten.
- Struktur tabel 19 kolom tidak terbaca lengkap dari text-layer parser Node.js (`pdfjs-dist` dan `pdf-parse`) pada dokumen ini.

Kesimpulan operasional:
- Parser Node.js cukup untuk deteksi token identitas dokumen.
- Parser Node.js tidak dijadikan sumber kebenaran untuk rekonstruksi merge-header 19 kolom pada lampiran ini.
- Sumber kebenaran struktur kolom tetap dokumen autentik + transkripsi manual terkontrol.

## Jejak Implementasi Terkait

- `resources/views/pdf/catatan_keluarga_report.blade.php`
- `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php`
- `app/Domains/Wilayah/DataKegiatanWarga/Models/DataKegiatanWarga.php`
- `tests/Feature/CatatanKeluargaReportPrintTest.php`
- `tests/Fixtures/pdf-baseline/4.15-catatan-keluarga.json`
