# Autentik Screenshot Coverage Matrix (Rakernas X)

Tanggal: 2026-03-11  
Status: `active`

Tujuan:

- Melacak bukti screenshot autentik yang sudah tersedia.
- Membedakan status `match`, `mismatch`, dan `belum ada screenshot terdaftar`.
- Menjadi rujukan cepat sebelum sinkronisasi kontrak/implementasi.

Sumber folder utama:

- `docs/referensi/_screenshots/rakernas-x-autentik`

## Coverage Screenshot Terdaftar

| Lampiran | Screenshot | Status | Catatan |
| --- | --- | --- | --- |
| 2.9 | `lampiran_2_9_data_kader_umum_kader_khusus.png` | belum siap sinkronisasi | Nomor kolom tidak terlihat pada screenshot; peta header belum bisa dikunci. |
| 4.8 | `lampiran_4_8_lembar_disposisi.png` | gap modul | Format lembar disposisi (form); belum ada modul/kontrak operasional. |
| 4.9a | `lampiran_4_9a_buku_daftar_anggota_tim_penggerak_pkk.png` | match | Header 11 kolom sesuai `anggota-tim-penggerak`. |
| 4.9b | `lampiran_4_9b_buku_daftar_anggota_tp_pkk_dan_kader.png` | mismatch | Screenshot menunjukkan tabel gabungan 13 kolom; modul saat ini hanya `kader-khusus`. |
| 4.10 | `lampiran_4_10_buku_agenda_surat_masuk_keluar.png` | match | Header 15 kolom (Surat Masuk/Keluar) sesuai `agenda-surat`. |
| 4.11 | `lampiran_4_11_buku_tabungan.png` | match | Header 12 kolom (penerimaan berpasangan) sesuai `buku-keuangan`. |
| 4.12 | `lampiran_4_12_buku_inventaris.png` | match | Header 8 kolom sesuai `inventaris`. |
| 4.13 | `lampiran_4_13_buku_kegiatan.png` | match | Header 7 kolom dengan group `KEGIATAN` sesuai `activities`. |
| 4.14.1a | `lampiran_4_14_1a_daftar_warga_tp_pkk.png` | match | Form detail 1-20 sesuai kontrak `data-warga`. |
| 4.14.1b | `lampiran_4_14_1b_kegiatan_warga.png` | match | 7 baris kegiatan baku sesuai `data-kegiatan-warga`. |
| 4.14.2a | `lampiran_4_14_2a_data_keluarga.png` | partial | Form autentik lebih detail; modul saat ini summary. |
| 4.14.2b | `lampiran_4_14_2b_pemanfaatan_tanah_pekarangan_aku_hatinya_pkk.png` | accepted deviation | Kontrak modul mengikuti normalisasi operasional. |
| 4.14.2c | `lampiran_4_14_2c_industri_rumah_tangga.png` | accepted deviation | Kontrak modul mengikuti normalisasi operasional. |
| 4.14.3 | `lampiran_4_14_3_data_pelatihan_kader.png` | match | Struktur form + tabel pelatihan sesuai modul. |
| 4.14.4a | `lampiran_4_14_4a_data_aset_warung_pkk.png` | match | Tabel komoditi/volume sesuai `warung-pkk`. |
| 4.14.4b | `lampiran_4_14_4b_taman_bacaan_perpustakaan.png` | match | Tabel jenis buku/kategori sesuai `taman-bacaan`. |
| 4.14.4c | `lampiran_4_14_4c_koperasi.png` | match | Tabel status hukum + anggota sesuai `koperasi`. |
| 4.14.4d | `lampiran_4_14_4d_kejar_paket_kf_paud.png` | match | Tabel warga belajar/pengajar sesuai `kejar-paket`. |
| 4.14.4e | `lampiran_4_14_4e_posyandu.png` | match | Tabel kegiatan + jumlah pengunjung/petugas sesuai `posyandu`. |
| 4.14.4f | `lampiran_4_14_4f_kelompok_simulasi_penyuluhan.png` | match | Header tabel sesuai `simulasi-penyuluhan`. |
| 4.15 | `lampiran_4_15_catatan_keluarga.png` | match | Struktur autentik 19 kolom; modul report-only. |
| 4.16a | `lampiran_4_16a_rekap_catatan_data_kegiatan_warga_dasa_wisma.png` | match | Rekap 29 kolom; report-only via `catatan-keluarga`. |
| 4.16b | `lampiran_4_16b_rekap_catatan_data_kegiatan_warga_pkk_rt.png` | match | Rekap 30 kolom; report-only via `catatan-keluarga`. |
| 4.16c | `lampiran_4_16c_catatan_data_kegiatan_warga_pkk_rw.png` | match | Report 32 kolom; report-only via `catatan-keluarga`. |
| 4.16d | `lampiran_4_16d_catatan_data_kegiatan_warga_pkk_dusun_lingkungan.png` | match | Report 33 kolom; report-only via `catatan-keluarga`. |
| 4.17a | `lampiran_4_17a_catatan_data_kegiatan_warga_tp_pkk_desa_kelurahan.png` | match | Report 33 kolom; report-only via `catatan-keluarga`. |
| 4.17b | `lampiran_4_17b_catatan_data_kegiatan_warga_tp_pkk_kecamatan.png` | match | Report 35 kolom; report-only via `catatan-keluarga`. |
| 4.17c | `lampiran_4_17c_catatan_data_kegiatan_warga_tp_pkk_kabupaten_kota.png` | match | Report 36 kolom; report-only via `catatan-keluarga`. |
| 4.18a | `lampiran_4_18a_rekap_ibu_hamil_dasawisma.png` | match | Report 17 kolom; report-only via `catatan-keluarga`. |
| 4.18b | `lampiran_4_18b_rekap_ibu_hamil_pkk_rt.png` | match | Report 15 kolom; report-only via `catatan-keluarga`. |
| 4.18c | `lampiran_4_18c_rekap_ibu_hamil_pkk_rw.png` | match | Report 16 kolom; report-only via `catatan-keluarga`. |
| 4.18d | `lampiran_4_18d_rekap_ibu_hamil_dusun_lingkungan.png` | match | Report 17 kolom; report-only via `catatan-keluarga`. |
| 4.19a | `lampiran_4_19a_rekap_ibu_hamil_tp_pkk_desa_kelurahan.png` | not implemented | Belum ada modul/report khusus tingkat TP PKK desa/kelurahan. |
| 4.19b | `lampiran_4_19b_rekap_ibu_hamil_tp_pkk_kecamatan.png` | match | Report 19 kolom; report-only via `catatan-keluarga`. |
| 4.19c | `lampiran_4_19c_rekap_ibu_hamil_tp_pkk_kabupaten_kota.png` | not implemented | Belum ada modul/report khusus tingkat TP PKK kabupaten/kota. |
| 4.20a | `lampiran_4_20a_data_umum_pkk.png` | match | Report 20 kolom; report-only via `catatan-keluarga`. |
| 4.20b | `lampiran_4_20b_data_umum_pkk_kecamatan.png` | match | Report 21 kolom; report-only via `catatan-keluarga`. |
| 4.21 | `lampiran_4_21_data_kegiatan_pkk_pokja_i.png` | not implemented | Belum ada modul/report khusus Pokja I. |
| 4.22 | `lampiran_4_22_data_kegiatan_pkk_pokja_ii.png` | not implemented | Belum ada modul/report khusus Pokja II; mapping header autentik sudah lengkap. |
| 4.23 | `lampiran_4_23_data_kegiatan_pkk_pokja_iii.png` | match | Report-only via `catatan-keluarga`. |
| 4.24 | `lampiran_4_24_data_kegiatan_pkk_pokja_iv.png` | match | Report-only via `catatan-keluarga`. |

## Lampiran Tanpa Screenshot Terdaftar

- Lampiran yang tidak tercantum di tabel coverage dianggap belum memiliki screenshot autentik terdaftar pada folder utama.

## Ekstensi Lokal (Non-Lampiran) - Screenshot Terdaftar

| Dokumen | Screenshot | Status | Catatan |
| --- | --- | --- | --- |
| Program Unggulan Gelari Pelangi (Rumah Pendidikan dan Keterampilan) | `program_unggulan_gelari_pelangi_rumah_pendidikan_keterampilan.png` | belum dipetakan | Belum ada mapping ke modul/kontrak domain. |
| Program Unggulan Gelari Pelangi (Kampung Mandiri) | `program_unggulan_gelari_pelangi_kampung_mandiri.png` | belum dipetakan | Belum ada mapping ke modul/kontrak domain. |
