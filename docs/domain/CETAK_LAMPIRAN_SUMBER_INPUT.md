# Cetak Lampiran - Peta Sumber Input

Dokumen ini menjelaskan hubungan antara laporan pada menu `Cetak Lampiran` dan modul input sumbernya.
Tujuannya agar tidak ada kekhawatiran data yatim (laporan tanpa sumber input).

Definisi singkat:
- `Input langsung`: data diisi pada modul tersebut, lalu dicetak sebagai laporan.
- `Rekap otomatis`: laporan hasil agregasi dari modul input lain (tanpa form input khusus).

## Laporan Dengan Input Langsung

| Group | Laporan (href) | Sumber Input | Catatan |
| --- | --- | --- | --- |
| Sekretaris PKK | `/dashboard/charts/report/pdf` | Dashboard | Rekap ringkasan, bukan form input. |
| Sekretaris PKK | `/${scope}/activities/report/pdf` | `/${scope}/activities` | Buku Kegiatan. |
| Sekretaris PKK | `/${scope}/agenda-surat/report/pdf` | `/${scope}/agenda-surat` | Agenda Surat Masuk/Keluar. |
| Sekretaris PKK | `/${scope}/agenda-surat/ekspedisi/report/pdf` | `/${scope}/agenda-surat` | Format ekspedisi dari data agenda. |
| Sekretaris PKK | `/${scope}/anggota-pokja/report/pdf` | `/${scope}/anggota-pokja` | Buku Anggota Pokja. |
| Sekretaris PKK | `/${scope}/anggota-tim-penggerak/report/pdf` | `/${scope}/anggota-tim-penggerak` | Anggota Tim Penggerak PKK. |
| Sekretaris PKK | `/${scope}/anggota-tim-penggerak-kader/report/pdf` | `/${scope}/anggota-tim-penggerak` | Variasi format dari data yang sama. |
| Sekretaris PKK | `/${scope}/kader-khusus/report/pdf` | `/${scope}/kader-khusus` | Kader Khusus. |
| Sekretaris PKK | `/${scope}/prestasi-lomba/report/pdf` | `/${scope}/prestasi-lomba` | Prestasi Lomba. |
| Sekretaris PKK | `/${scope}/buku-notulen-rapat/report/pdf` | `/${scope}/buku-notulen-rapat` | Buku Notulen Rapat. |
| Sekretaris PKK | `/${scope}/buku-daftar-hadir/report/pdf` | `/${scope}/buku-daftar-hadir` | Buku Daftar Hadir. |
| Sekretaris PKK | `/${scope}/buku-tamu/report/pdf` | `/${scope}/buku-tamu` | Buku Tamu. |
| Sekretaris PKK | `/${scope}/buku-keuangan/report/pdf` | `/${scope}/buku-keuangan` | Buku Keuangan. |
| Sekretaris PKK | `/${scope}/bantuans/report/pdf` | `/${scope}/bantuans` | Buku Bantuan. |
| Sekretaris PKK | `/${scope}/bantuans/keuangan/report/pdf` | `/${scope}/buku-keuangan` | Rekap keuangan bantuan dari buku keuangan. |
| Sekretaris PKK | `/${scope}/inventaris/report/pdf` | `/${scope}/inventaris` | Buku Inventaris. |
| Sekretaris PKK | `/${scope}/program-prioritas/report/pdf` | `/${scope}/program-prioritas` | Buku Program Kerja TP PKK. |
| Sekretaris PKK | `/${scope}/data-warga/report/pdf` | `/${scope}/data-warga` | Data Warga. |
| Sekretaris PKK | `/${scope}/data-kegiatan-warga/report/pdf` | `/${scope}/data-kegiatan-warga` | Kegiatan Warga. |
| Pokja I | `/${scope}/simulasi-penyuluhan/report/pdf` | `/${scope}/simulasi-penyuluhan` | Kelompok Simulasi dan Penyuluhan. |
| Pokja I | `/${scope}/bkr/report/pdf` | `/${scope}/bkr` | BKR. |
| Pokja I | `/${scope}/paar/report/pdf` | `/${scope}/paar` | Buku PAAR. |
| Pokja II | `/${scope}/data-pelatihan-kader/report/pdf` | `/${scope}/data-pelatihan-kader` | Data Pelatihan Kader. |
| Pokja II | `/${scope}/taman-bacaan/report/pdf` | `/${scope}/taman-bacaan` | Taman Bacaan/Perpustakaan. |
| Pokja II | `/${scope}/koperasi/report/pdf` | `/${scope}/koperasi` | Koperasi. |
| Pokja II | `/${scope}/kejar-paket/report/pdf` | `/${scope}/kejar-paket` | Kejar Paket/KF/PAUD. |
| Pokja II | `/${scope}/bkl/report/pdf` | `/${scope}/bkl` | BKL. |
| Pokja III | `/${scope}/data-keluarga/report/pdf` | `/${scope}/data-keluarga` | Data Keluarga. |
| Pokja III | `/${scope}/data-industri-rumah-tangga/report/pdf` | `/${scope}/data-industri-rumah-tangga` | Industri Rumah Tangga. |
| Pokja III | `/${scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk/report/pdf` | `/${scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | HATINYA PKK. |
| Pokja III | `/${scope}/warung-pkk/report/pdf` | `/${scope}/warung-pkk` | Data Aset Sarana Desa/Kelurahan. |
| Pokja IV | `/${scope}/posyandu/report/pdf` | `/${scope}/posyandu` | Data Isian Posyandu oleh TP PKK. |
| Pokja IV | `/${scope}/pilot-project-naskah-pelaporan/report/pdf` | `/${scope}/pilot-project-naskah-pelaporan` | Naskah Pelaporan Pilot Project Pokja IV. |
| Pokja IV | `/${scope}/pilot-project-keluarga-sehat/report/pdf` | `/${scope}/pilot-project-keluarga-sehat` | Laporan Pilot Project Keluarga Sehat. |

## Laporan Rekap Otomatis

Laporan berikut tidak memiliki form input khusus. Data diambil dari modul input lain dan diringkas otomatis.

| Laporan (href) | Sumber Input Utama | Catatan |
| --- | --- | --- |
| `/${scope}/catatan-keluarga/report/pdf` | `/${scope}/data-warga`, `/${scope}/data-kegiatan-warga` | Catatan Keluarga adalah rekap dari data warga dan kegiatan warga. |
| `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf` | `/${scope}/catatan-keluarga` + modul pendukung | Rekap kegiatan Pokja IV dari data lintas modul. |
| `/${scope}/catatan-keluarga/rekap-dasa-wisma/report/pdf` | `/${scope}/catatan-keluarga` | Rekap Dasawisma. |
| `/${scope}/catatan-keluarga/rekap-pkk-rt/report/pdf` | `/${scope}/catatan-keluarga` | Rekap PKK RT. |
| `/${scope}/catatan-keluarga/catatan-pkk-rw/report/pdf` | `/${scope}/catatan-keluarga` | Catatan PKK RW. |
| `/${scope}/catatan-keluarga/rekap-rw/report/pdf` | `/${scope}/catatan-keluarga` | Rekap PKK Dusun/Lingkungan. |
| `/${scope}/catatan-keluarga/tp-pkk-desa-kelurahan/report/pdf` | `/${scope}/catatan-keluarga` | Rekap TP PKK Desa/Kelurahan. |
| `/${scope}/catatan-keluarga/tp-pkk-kecamatan/report/pdf` | `/${scope}/catatan-keluarga` | Rekap TP PKK Kecamatan. |
| `/${scope}/catatan-keluarga/tp-pkk-kabupaten-kota/report/pdf` | `/${scope}/catatan-keluarga` | Rekap TP PKK Kabupaten/Kota. |
| `/${scope}/catatan-keluarga/tp-pkk-provinsi/report/pdf` | `/${scope}/catatan-keluarga` | Rekap TP PKK Provinsi. |
| `/${scope}/catatan-keluarga/rekap-ibu-hamil-dasawisma/report/pdf` | `/${scope}/catatan-keluarga` | Rekap Ibu Hamil Dasawisma. |
| `/${scope}/catatan-keluarga/rekap-ibu-hamil-pkk-rt/report/pdf` | `/${scope}/catatan-keluarga` | Rekap Ibu Hamil PKK RT. |
| `/${scope}/catatan-keluarga/rekap-ibu-hamil-pkk-rw/report/pdf` | `/${scope}/catatan-keluarga` | Rekap Ibu Hamil PKK RW. |
| `/${scope}/catatan-keluarga/rekap-ibu-hamil-pkk-dusun-lingkungan/report/pdf` | `/${scope}/catatan-keluarga` | Rekap Ibu Hamil PKK Dusun/Lingkungan. |
| `/${scope}/catatan-keluarga/rekap-ibu-hamil-tp-pkk-kecamatan/report/pdf` | `/${scope}/catatan-keluarga` | Rekap Ibu Hamil TP PKK Kecamatan. |
| `/${scope}/catatan-keluarga/data-umum-pkk/report/pdf` | `/${scope}/catatan-keluarga` + modul pendukung | Rekap Data Umum PKK. |
| `/${scope}/catatan-keluarga/data-umum-pkk-kecamatan/report/pdf` | `/${scope}/catatan-keluarga` + modul pendukung | Rekap Data Umum PKK Kecamatan. |

Catatan tambahan:
- Rekap otomatis memakai data lintas modul (contoh: `data-warga`, `data-kegiatan-warga`, `anggota-pokja`, `kader-khusus`, `program-prioritas`, `posyandu`).
- Untuk audit detail sumber data rekap, rujuk `docs/domain/DOMAIN_CONTRACT_MATRIX.md` dan mapping di `docs/domain/*_MAPPING.md`.
