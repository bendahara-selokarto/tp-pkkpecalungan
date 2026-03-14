<!-- markdownlint-disable MD056 MD001 MD038 MD034 -->

# Cetak Lampiran - Peta Sumber Input

Dokumen ini menjelaskan hubungan antara laporan pada menu `Cetak Lampiran` dan modul input sumbernya.
Tujuannya agar tidak ada kekhawatiran data yatim (laporan tanpa sumber input).

Definisi singkat:

- `Input langsung`: data diisi pada modul tersebut, lalu dicetak sebagai laporan.
- `Rekap otomatis`: laporan hasil agregasi dari modul input lain (tanpa form input khusus).

## Audit IU per Lampiran (Printable)

IU = input utama. Untuk laporan rekap lintas modul, detail sumber data per kolom tetap mengacu pada dokumen mapping terkait.

| Lampiran | Laporan (href) | IU (Input Utama) | Referensi |
| --- | --- | --- | --- |
| 4.9a | `/${scope}/anggota-tim-penggerak/report/pdf` | `/${scope}/anggota-tim-penggerak` | Input langsung. |
| 4.10 | `/${scope}/agenda-surat/report/pdf`, `/${scope}/agenda-surat/ekspedisi/report/pdf` | `/${scope}/agenda-surat` | Input langsung. |
| 4.11 | `/${scope}/buku-keuangan/report/pdf` | `/${scope}/buku-keuangan` | Input langsung. |
| 4.12 | `/${scope}/inventaris/report/pdf` | `/${scope}/inventaris` | Input langsung. |
| 4.13 | `/${scope}/activities/report/pdf` | `/${scope}/activities` | Input langsung. |
| 4.14.1a | `/${scope}/data-warga/report/pdf` | `/${scope}/data-warga` | Input langsung (detail anggota ikut dipakai). |
| 4.14.1b | `/${scope}/data-kegiatan-warga/report/pdf` | `/${scope}/data-kegiatan-warga` | Input langsung. |
| 4.14.2a | `/${scope}/data-keluarga/report/pdf` | `/${scope}/data-keluarga` | Input langsung. |
| 4.14.2b | `/${scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk/report/pdf` | `/${scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | Input langsung. |
| 4.14.2c | `/${scope}/data-industri-rumah-tangga/report/pdf` | `/${scope}/data-industri-rumah-tangga` | Input langsung. |
| 4.14.3 | `/${scope}/data-pelatihan-kader/report/pdf` | `/${scope}/data-pelatihan-kader` | Input langsung. |
| 4.14.4 | `/${scope}/warung-pkk/report/pdf` | `/${scope}/warung-pkk` | Input langsung. |
| 4.14.4b | `/${scope}/taman-bacaan/report/pdf` | `/${scope}/taman-bacaan` | Input langsung. |
| 4.14.4c | `/${scope}/koperasi/report/pdf` | `/${scope}/koperasi` | Input langsung. |
| 4.14.4d | `/${scope}/kejar-paket/report/pdf` | `/${scope}/kejar-paket` | Input langsung. |
| 4.14.4e | `/${scope}/posyandu/report/pdf` | `/${scope}/posyandu` | Input langsung. |
| 4.14.4f | `/${scope}/simulasi-penyuluhan/report/pdf` | `/${scope}/simulasi-penyuluhan` | Input langsung. |
| 4.15 | `/${scope}/catatan-keluarga/report/pdf` | Rekap dari `/${scope}/data-warga` + `/${scope}/data-kegiatan-warga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.16a | `/${scope}/catatan-keluarga/rekap-dasa-wisma/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.16b | `/${scope}/catatan-keluarga/rekap-pkk-rt/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.16c | `/${scope}/catatan-keluarga/catatan-pkk-rw/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.16d | `/${scope}/catatan-keluarga/rekap-rw/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.17a | `/${scope}/catatan-keluarga/tp-pkk-desa-kelurahan/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.17b | `/${scope}/catatan-keluarga/tp-pkk-kecamatan/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.17c | `/${scope}/catatan-keluarga/tp-pkk-kabupaten-kota/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.17d | `/${scope}/catatan-keluarga/tp-pkk-provinsi/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.18a | `/${scope}/catatan-keluarga/rekap-ibu-hamil-dasawisma/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.18b | `/${scope}/catatan-keluarga/rekap-ibu-hamil-pkk-rt/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.18c | `/${scope}/catatan-keluarga/rekap-ibu-hamil-pkk-rw/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.18d | `/${scope}/catatan-keluarga/rekap-ibu-hamil-pkk-dusun-lingkungan/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.19b | `/${scope}/catatan-keluarga/rekap-ibu-hamil-tp-pkk-kecamatan/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.20a | `/${scope}/catatan-keluarga/data-umum-pkk/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.20b | `/${scope}/catatan-keluarga/data-umum-pkk-kecamatan/report/pdf` | Rekap via `catatan-keluarga` | `docs/domain/DOMAIN_CONTRACT_MATRIX.md`. |
| 4.21 | `/${scope}/data-kegiatan-pkk-pokja-i/report/pdf` | Rekap lintas `activities` + `anggota-pokja` | `docs/domain/DATA_KEGIATAN_PKK_POKJA_I_4_21_SUMBER_DATA.md`. |
| 4.22 | `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-ii/report/pdf` | Rekap lintas modul Pokja II | `docs/domain/DATA_KEGIATAN_PKK_POKJA_II_4_22_SUMBER_DATA.md`. |
| 4.23 | `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iii/report/pdf` | Rekap lintas `anggota-pokja`, `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, `data-industri-rumah-tangga`, `data-warga` | `docs/domain/DATA_KEGIATAN_PKK_POKJA_III_4_23_MAPPING.md`. |
| 4.24 | `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iv/report/pdf` | Rekap lintas modul Pokja IV | `docs/domain/DATA_KEGIATAN_PKK_POKJA_IV_4_24_SUMBER_DATA.md`. |

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
Catatan: jika kolom `Sumber Input Utama` menampilkan `catatan-keluarga`, itu berarti jalur rekap/hub laporan
(route + repository), bukan form input baru. Sumber data primer tetap mengikuti mapping di
`docs/domain/DOMAIN_CONTRACT_MATRIX.md` dan `docs/domain/*_MAPPING.md`.

| Laporan (href) | Sumber Input Utama | Catatan |
| --- | --- | --- |
| `/${scope}/catatan-keluarga/report/pdf` | `/${scope}/data-warga`, `/${scope}/data-kegiatan-warga` | Catatan Keluarga adalah rekap dari data warga dan kegiatan warga. |
| `/${scope}/data-kegiatan-pkk-pokja-i/report/pdf` | `/${scope}/activities`, `/${scope}/anggota-pokja` | Rekap kegiatan Pokja I; detail sumber data per kolom di `docs/domain/DATA_KEGIATAN_PKK_POKJA_I_4_21_SUMBER_DATA.md`. |
| `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-ii/report/pdf` | `/${scope}/catatan-keluarga` + modul Pokja II terkait | Rekap kegiatan Pokja II; detail sumber data per kolom di `docs/domain/DATA_KEGIATAN_PKK_POKJA_II_4_22_SUMBER_DATA.md`. |
| `/${scope}/catatan-keluarga/data-kegiatan-pkk-pokja-iii/report/pdf` | `/${scope}/catatan-keluarga` + modul Pokja III terkait | Rekap kegiatan Pokja III; detail sumber data per kolom di `docs/domain/DATA_KEGIATAN_PKK_POKJA_III_4_23_MAPPING.md`. |
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
