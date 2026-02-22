# Domain Deviation Log (T11)

Tujuan:
- Menjadi register resmi untuk semua deviasi antara implementasi domain aplikasi dan pedoman domain utama.
- Menjamin setiap deviasi memiliki jejak keputusan, dampak, mitigasi, dan status penyelesaian.

Sumber canonical:
- https://pubhtml5.com/zsnqq/vjcf/basic/101-150

Aturan pencatatan:
- Setiap deviasi wajib mencantumkan: alasan teknis, dampak, rencana mitigasi, status.
- Setiap perubahan status deviasi wajib menambah tanggal update.
- Jika deviasi mempengaruhi output PDF/auth scope, wajib sinkron ke:
  - `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`
  - `docs/security/REGRESSION_CHECKLIST_AUTH_SCOPE.md`

## Register Deviasi

| ID | Tanggal | Lampiran/Modul | Jenis deviasi | Deviasi | Alasan teknis | Dampak | Rencana mitigasi | Status |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `DV-001` | 2026-02-20 | 4.11 / `bantuans` | Naming internal | Pedoman memakai label `Buku Keuangan`, implementasi domain internal tetap `bantuans`. | Reuse domain existing untuk menghindari tabel baru dan duplikasi arus kas. | Risiko kebingungan saat maintenance internal; tidak mempengaruhi label UI/PDF. | Pertahankan label pedoman untuk UI/PDF, dokumentasikan mapping di matrix domain, jaga test baseline PDF. | `accepted` |
| `DV-002` | 2026-02-20 | 4.14.4a / `warung-pkk` | Naming internal | Pedoman memakai label `Data Aset (Sarana) Desa/Kelurahan`, slug teknis tetap `warung-pkk`. | Kompatibilitas route/model existing dan menghindari breaking change besar. | Risiko salah interpretasi istilah di level kode; tidak mempengaruhi output pedoman. | Pertahankan normalisasi label di UI/PDF + update terminology map saat ada perubahan terkait. | `accepted` |
| `DV-003` | 2026-02-20 | 4.14.5 | Coverage pedoman | Pada baseline sumber halaman 101-150, lampiran 4.14.5 belum ditemukan untuk divalidasi. | Ruang lingkup sumber canonical saat ini terbatas ke halaman 101-150. | Validasi kontrak domain untuk 4.14.5 belum bisa dilakukan penuh. | Jika sumber resmi tambahan tersedia, tambah ke matrix kontrak + checklist PDF + baseline fixture. | `open` |
| `DV-004` | 2026-02-21 | Sidebar Domain Grouping | Klasifikasi organisasi | Pedoman utama tersusun per lampiran, sedangkan sidebar dikelompokkan per `Sekretaris TPK` dan `Pokja I-IV` untuk kebutuhan operasional menu. | Struktur lampiran dan struktur organisasi tidak selalu 1:1; perlu pengelompokan praktis agar menu lebih operasional. | Potensi perbedaan persepsi penempatan modul lintas Pokja jika acuan organisasi diperbarui. | Simpan mapping resmi pada `docs/domain/DOMAIN_CONTRACT_MATRIX.md` (section mapping sidebar), evaluasi berkala saat ada pembaruan pedoman organisasi. | `accepted` |
| `DV-005` | 2026-02-21 | 4.14.1a / `data-warga` | Kontrak struktur data | Dokumen autentik `153.pdf` menunjukkan format detail anggota rumah tangga (kolom 1-20), sementara implementasi aktif masih agregat (`WARGA L/P`, `TOTAL`). | Implementasi awal mengikuti ringkasan pedoman 101-150 dan belum memuat tabel detail anggota. | Output modul 4.14.1a sempat tidak identik dengan format autentik. | Rencana transisi `docs/domain/ADJUSTMENT_PLAN_4_14_1A_DAFTAR_WARGA_TP_PKK.md` C1-C7 telah dieksekusi: data detail anggota aktif, summary kompatibel untuk `catatan-keluarga`, PDF autentik `portrait`, dan audit dashboard lulus. | `resolved` |
| `DV-006` | 2026-02-22 | 4.16d / `rekap-catatan-data-kegiatan-warga-rw` | Label canonical sementara | Pada implementasi awal, judul 4.16d masih inferensi. Konfirmasi screenshot halaman penuh menetapkan judul canonical: `CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK DUSUN/LINGKUNGAN`. | Fase awal belum memiliki bukti halaman penuh, sehingga dipakai label sementara agar jalur report tetap aktif end-to-end. | Drift judul PDF telah dieliminasi setelah sinkronisasi implementasi dan dokumen kontrak (update 2026-02-22). | Tindak lanjut selesai: judul PDF + terminology + domain contract matrix + TODO autentik 4.16d disinkronkan. | `resolved` |
| `DV-007` | 2026-02-22 | 4.17a / `catatan-data-kegiatan-warga-tp-pkk-desa-kelurahan` | Sumber field agregasi | Header autentik 4.17a membutuhkan basis `NAMA DUSUN/LINGKUNGAN`, namun data sumber saat ini belum memiliki field dedicated dusun/lingkungan. | Struktur tabel operasional `data_wargas` belum menyimpan kolom dusun/lingkungan terpisah. | Potensi inkonsistensi label grup bila pola alamat tidak baku. | Gunakan ekstraksi dari `alamat` (fallback `dasawisma`) sebagai solusi sementara; evaluasi penambahan field dedicated pada concern migrasi terpisah jika dibutuhkan. | `accepted` |
| `DV-008` | 2026-02-22 | 4.17b / `catatan-data-kegiatan-warga-tp-pkk-kecamatan` | Sumber field agregasi | Header autentik 4.17b membutuhkan basis `NAMA DESA/KELURAHAN`, namun data sumber saat ini belum memiliki field dedicated desa/kelurahan. | Struktur tabel operasional `data_wargas` belum menyimpan kolom desa/kelurahan terpisah. | Potensi inkonsistensi pengelompokan desa/kelurahan bila pola alamat tidak baku. | Gunakan ekstraksi pattern `DESA|KELURAHAN|KEL` dari `alamat`/`dasawisma` sebagai solusi sementara; evaluasi penambahan field dedicated pada concern migrasi terpisah jika dibutuhkan. | `accepted` |
| `DV-009` | 2026-02-22 | 4.17c / `catatan-data-kegiatan-warga-tp-pkk-kabupaten-kota` | Sumber field agregasi + batas scope | Header autentik 4.17c membutuhkan basis `NAMA KECAMATAN` dan `JML DESA/KEL`, namun data sumber saat ini belum memiliki field dedicated kecamatan/desa serta belum ada scope operasional `kabupaten/kota`. | Struktur tabel operasional `data_wargas` menyimpan konteks area sesuai scope aktif (`desa`/`kecamatan`) dan belum ada role/scope `kabupaten/kota` pada boundary aplikasi saat ini. | Potensi hasil agregasi 4.17c tidak merepresentasikan lintas kecamatan penuh pada satu output jika data sumber tidak memuat token kecamatan/desa secara baku. | Gunakan ekstraksi pattern `KECAMATAN`/`DESA|KELURAHAN|KEL` dari `alamat`/`dasawisma` dengan fallback area user; evaluasi concern terpisah bila diperlukan penambahan scope `kabupaten/kota` dan field dedicated sumber data. | `accepted` |
| `DV-010` | 2026-02-22 | 4.17d / `catatan-data-kegiatan-warga-tp-pkk-provinsi` | Sumber field agregasi + batas scope | Header autentik 4.17d membutuhkan basis `NAMA KAB/KOTA` dan `JML KEC`, namun data sumber saat ini belum memiliki field dedicated provinsi/kabupaten/kota serta belum ada scope operasional `provinsi` pada boundary aplikasi. | Struktur tabel operasional `data_wargas` menyimpan konteks area sesuai scope aktif (`desa`/`kecamatan`) dan tidak menyimpan hirarki administrasi penuh hingga provinsi. | Potensi hasil agregasi 4.17d tidak merepresentasikan lintas kabupaten/kota penuh pada satu output jika data sumber tidak memuat token kabupaten/kota secara baku. | Gunakan ekstraksi pattern `KAB|KABUPATEN|KOTA` serta `KECAMATAN` dari `alamat`/`dasawisma`; jika token tidak ada, fallback `-` untuk identitas kabupaten/kota. Evaluasi concern terpisah bila dibutuhkan scope `provinsi` dan field dedicated sumber data. | `accepted` |
| `DV-011` | 2026-02-22 | 4.18a-4.19b / `rekap-ibu-hamil-*` | Sumber field maternal/kelahiran/kematian | Lampiran autentik 4.18a-4.19b membutuhkan indikator eksplisit (hamil/melahirkan/nifas, kematian ibu/bayi/balita, akte kelahiran), namun kontrak input saat ini belum memiliki field dedicated untuk seluruh indikator tersebut. | Struktur operasional `data_wargas` dan `data_warga_anggotas` masih general-purpose, sehingga status maternal/kematian diturunkan dari `keterangan` dan atribut umur/jenis kelamin. | Risiko undercount/overcount pada indikator 4.18-4.19 saat data `keterangan` tidak baku atau tidak lengkap. | Pertahankan report 4.18-4.19 sebagai proyeksi operasional (report-only), dokumentasikan keterbatasan pada mapping domain, dan evaluasi concern migrasi terpisah untuk field dedicated indikator maternal/kelahiran/kematian/akte. | `accepted` |

## Kriteria Status

- `open`: deviasi teridentifikasi, belum dimitigasi penuh.
- `accepted`: deviasi disetujui sementara, mitigasi aktif, dipantau.
- `resolved`: deviasi sudah ditutup dan tidak lagi berdampak.
- `rejected`: deviasi tidak valid setelah verifikasi ulang.

## Bukti Validasi T11

Perintah yang dijalankan saat pembuatan log:
- `php artisan route:list --name=report`
- `php artisan test --filter=PdfBaselineFixtureComplianceTest`

Ringkasan hasil:
- Route report aktif (2026-02-21): `56`.
- Baseline PDF compliance (2026-02-21): `20` test pass.
- Catatan: `DV-003` tetap `open` sampai sumber canonical lampiran `4.14.5` tersedia.
