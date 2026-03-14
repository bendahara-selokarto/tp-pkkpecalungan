# TODO TBH26A1 Bahan Aktual dan Terjemahan Berkala

Tanggal: 2026-03-08  
Status: `done`  
Related ADR: `-`

## Aturan Pakai

- Dokumen ini adalah wadah kerja hidup untuk input aktual dari owner.
- Setiap informasi baru wajib ditambahkan di bagian paling bawah dokumen.
- Histori update lama tidak boleh ditimpa; koreksi dilakukan lewat update baru.
- Penomoran update wajib berurutan: `U001`, `U002`, `U003`, dan seterusnya.

## Konteks

- Owner akan menuliskan versi aktual dengan bahasa sendiri sebagai bahan sumber.
- Setiap input perlu disimpan dalam bentuk asli agar jejak makna tetap utuh.
- Setiap input asli kemudian diterjemahkan ke bahasa bahan formal yang siap dipakai pada bahan rapat atau dokumen resmi.
- Concern ini `doc-only`; tidak mengubah kontrak backend, domain runtime, route, schema, atau UI aplikasi.

## Kontrak Concern (Lock)

- Domain: dokumentasi proses bahan aktual dan terjemahan berkala.
- Role/scope target: owner dokumen dan AI agent pada scope `docs/process`.
- Boundary data: hanya konten markdown pada file ini dan log validasi operasional terkait.
- Acceptance criteria:
  - tersedia satu file TODO hidup di `docs/process`,
  - setiap update memakai format `Input Aktual (Asli) + Terjemahan Bahan (Formal)`,
  - histori update bersifat append-only,
  - istilah ambigu diberi tag `[PERLU KONFIRMASI]` tanpa mengubah makna input asli.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] Tersedia template TODO hidup untuk bahan aktual dan terjemahan berkala.
- [x] Tersedia protokol update append-only yang konsisten untuk sesi lanjutan.
- [x] Setiap informasi baru dari owner ditambahkan sebagai update baru.
- [x] Status concern diubah ke `done` hanya setelah owner menyatakan rangkaian update selesai.

## Langkah Eksekusi

- [x] Analisis scoped dependency + side effect.
- [x] Patch minimal pada boundary arsitektur.
- [x] Sinkronisasi dokumen concern terkait sesuai jalur `doc-only fast lane`.
- [x] Tambahkan protokol update berkala dan template update baku.
- [x] Kunci pola append-only untuk histori update.

## Protokol Update Berkala

- Timestamp update memakai format `YYYY-MM-DD HH:mm WIB`.
- Heading update memakai format `## Update YYYY-MM-DD HH:mm WIB (UXXX)`.
- `Input Aktual (Asli)` menyimpan teks owner dengan makna tetap; perapian hanya boleh sebatas struktur baris agar terbaca.
- `Terjemahan Bahan (Formal)` menulis ulang isi yang sama dalam bahasa Indonesia formal, ringkas, dan siap ditempel ke bahan.
- `Catatan (Opsional)` dipakai untuk tindak lanjut, istilah canonical, atau penanda `[PERLU KONFIRMASI]`.
- Jika ditemukan ambiguitas yang mempengaruhi makna, jangan tebak; pertahankan input asli dan beri tag `[PERLU KONFIRMASI]` di bagian catatan.

## Template Update Berikutnya

```md
## Update YYYY-MM-DD HH:mm WIB (UXXX)

### Input Aktual (Asli)
- Tulis input asli dari owner.

### Terjemahan Bahan (Formal)
- Tulis hasil terjemahan formal yang setara makna.

### Catatan (Opsional)
- Tulis aksi lanjutan, istilah canonical, atau `[PERLU KONFIRMASI]` bila ada ambiguitas.
```

## Update 2026-03-08 09:26 WIB (U001)

### Input Aktual (Asli)

- Inisialisasi dokumen kerja untuk menampung input aktual owner dan terjemahan bahan formal secara berkala.
- Format update dikunci append-only agar histori perubahan tetap utuh.

### Terjemahan Bahan (Formal)

- Dokumen kerja ini diinisialisasi sebagai wadah pembaruan berkala yang menyimpan informasi aktual dalam bentuk asli dan menerjemahkannya ke bahasa bahan formal.
- Seluruh pembaruan berikutnya wajib dicatat secara berurutan dengan pola append-only agar jejak perubahan tetap terpelihara.

### Catatan (Opsional)

- `U001` berfungsi sebagai baseline template operasional.
- Update berikutnya dimulai dari `U002`.

## Validasi

- [x] L1: audit scoped `rg` untuk judul, status, dan section update pada file concern.
- [x] L2: regression concern terkait tidak diperlukan karena tidak ada perubahan runtime/backend contract.
- [x] L3: `php artisan test` tidak dijalankan karena concern ini `doc-only`.

## Risiko

- Risiko 1: histori bisa drift jika update lama ditimpa manual.
- Risiko 2: input bebas yang ambigu dapat menghasilkan terjemahan terlalu asumtif jika tidak diberi tag `[PERLU KONFIRMASI]`.

## Keputusan

- [x] K1: format isi dikunci sebagai `Input Asli + Terjemahan`.
- [x] K2: pola update dikunci sebagai `append-only`.
- [x] K3: gaya terjemahan dikunci sebagai `bahasa bahan formal`.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru tidak diperlukan karena concern ini `doc-only` dan tidak mengubah boundary arsitektur runtime.
- [x] Status ADR tetap `-`.

## Fallback Plan

- Jika format template perlu diperluas, lakukan patch minimal pada section template tanpa menghapus histori update yang sudah ada.
- Jika satu update terlanjur kurang tepat, tambahkan koreksi sebagai update baru, bukan dengan menimpa entri lama.

## Output Final

- [x] Ringkasan apa yang diubah dan kenapa.
- [x] Daftar file terdampak.
- [x] Hasil validasi + residual risk.

## Update 2026-03-08 09:33 WIB (U002)

### Input Aktual (Asli)

- pertama, saya mulai dari pokja I level desa
- pokja i memiliki :
  1. buku wajib
  a. Buku Program Kerja
  b. Buku Pelaksanaan Program Kerja
  c. Buku Kegiatan
  d. Buku Data Kegiatan Pokja
  2. Buku Bantu
  3. Buku Prestasi
  4. Buku Daftar Kader Khusus Pokja I
  5. Buku Kegiatan Simulasi
  6. Buku Kegiatan BKR
  7. Buku Grafik
  8. Buku Kegiatn BKL
  9. Buku Administerasi PAAR (Pola Asuh Anak dan Remaja)

### Terjemahan Bahan (Formal)

- Pendataan awal dimulai dari Pokja I pada level desa.
- Pokja I tingkat desa memiliki dokumen administrasi sebagai berikut:
  1. Buku wajib, yang terdiri atas:
     a. Buku Program Kerja
     b. Buku Pelaksanaan Program Kerja
     c. Buku Kegiatan
     d. Buku Data Kegiatan Pokja
  2. Buku Bantu
  3. Buku Prestasi
  4. Buku Daftar Kader Khusus Pokja I
  5. Buku Kegiatan Simulasi
  6. Buku Kegiatan BKR
  7. Buku Grafik
  8. Buku Kegiatan BKL
  9. Buku Administrasi PAAR (Pola Asuh Anak dan Remaja)

### Catatan (Opsional)

- Perapian formal dilakukan pada ejaan `Kegiatn` menjadi `Kegiatan` dan `Administerasi` menjadi `Administrasi` tanpa mengubah makna input asli.
- Update berikutnya dimulai dari `U003`.

## Update 2026-03-08 09:38 WIB (U003)

### Input Aktual (Asli)

- Buku Administerasi Pokja III
  1. Buku Program Kerja
  2. Buku Kegiatan
  3. Buku Notulen
  4. Buku Daftar Hadir
  5. Buku Prestasi
  6. Buku Bantu
  7. Buku Inventaris
  8. Buku Kas
  9. Buku Kader Khusus
  10. Buku Rumah Sehat dan Tidak Sehat
  11. Buku Sususnan Pengurus
  12. Buku Grafik
  13. Buku Tanaman Keras
  14. Buku Kegiatan
  15. Buku Pelaksanaan Program
- memiliki juga administerasi KWT (Kelompok Wanita Tani) yang berisi
  1. Buku Daftar Anggota
  2. Buku Tamu
  3. Buku Notulen
  4. Buku Daftar Hadir
  5. Buku Kegiatan
  6. Buku Penerimaan Bantuan
  7. Buku Kas
  8. Buku Arisan

### Terjemahan Bahan (Formal)

- Administrasi Pokja III pada baseline ini dicatat sebagai berikut:
  1. Buku Program Kerja
  2. Buku Kegiatan
  3. Buku Notulen
  4. Buku Daftar Hadir
  5. Buku Prestasi
  6. Buku Bantu
  7. Buku Inventaris
  8. Buku Kas
  9. Buku Kader Khusus
  10. Buku Rumah Sehat dan Tidak Sehat
  11. Buku Susunan Pengurus
  12. Buku Grafik
  13. Buku Tanaman Keras
  14. Buku Kegiatan `[PERLU KONFIRMASI: item ini muncul kembali setelah nomor 2]`
  15. Buku Pelaksanaan Program
- Pokja III juga memiliki administrasi KWT (Kelompok Wanita Tani), yang terdiri atas:
  1. Buku Daftar Anggota
  2. Buku Tamu
  3. Buku Notulen
  4. Buku Daftar Hadir
  5. Buku Kegiatan
  6. Buku Penerimaan Bantuan
  7. Buku Kas
  8. Buku Arisan

### Catatan (Opsional)

- Konteks level untuk Pokja III diasumsikan mengikuti alur sebelumnya, yaitu level desa. `[PERLU KONFIRMASI]` bila yang dimaksud level berbeda.
- Perapian formal dilakukan pada ejaan `Administerasi` menjadi `Administrasi` dan `Sususnan` menjadi `Susunan` tanpa mengubah makna input asli.
- Daftar Pokja III memuat `Buku Kegiatan` pada nomor `2` dan `14`; keduanya dipertahankan sambil menunggu klarifikasi.
- Update berikutnya dimulai dari `U004`.

## Update 2026-03-08 09:43 WIB (U004)

### Input Aktual (Asli)

- Buku Administerasi Pokja IV
- A. Buku Wajib
  1. Buku Program Kerja Pokja IV
  2. Buku Kegiatan Pokja IV
  3. Buku Data Kegiatan
- B. Buku Bantu Pokja IV
  1. Buku Bantu
  2. Buku Data Pengunjung Petugas Posyandu
  3. Buku Data Hasil Kegiatan Posyandu
  4. Buku Kader Khusus
  5. Buku Prestasi
- C. Data Lain-lain
  1. Grafik Program Pokja IV
  2. Kliping
  3. Data IVA test
  4. Data Posyandu
  5. Data Umum

### Terjemahan Bahan (Formal)

- Administrasi Pokja IV pada baseline ini dicatat sebagai berikut:
  A. Buku Wajib
  1. Buku Program Kerja Pokja IV
  2. Buku Kegiatan Pokja IV
  3. Buku Data Kegiatan
  B. Buku Bantu Pokja IV
  1. Buku Bantu
  2. Buku Data Pengunjung Petugas Posyandu
  3. Buku Data Hasil Kegiatan Posyandu
  4. Buku Kader Khusus
  5. Buku Prestasi
  C. Data Lain-lain
  1. Grafik Program Pokja IV
  2. Kliping
  3. Data IVA Test
  4. Data Posyandu
  5. Data Umum

### Catatan (Opsional)

- Entri ini menggabungkan potongan input Pokja IV dari dua pesan terakhir agar tercatat sebagai satu update utuh.
- Perapian formal dilakukan pada ejaan `Administerasi` menjadi `Administrasi` dan kapitalisasi `IVA Test` tanpa mengubah makna input asli.
- Konteks level untuk Pokja IV diasumsikan mengikuti alur sebelumnya, yaitu level desa. `[PERLU KONFIRMASI]` bila yang dimaksud level berbeda.
- Update berikutnya dimulai dari `U005`.

## Update 2026-03-08 09:48 WIB (U005)

### Input Aktual (Asli)

- Administerasi Dasa Wisma
- Dasa Wisma I "Nama Dasa Wisma"
  1. Rekapitulasi Data Bumil dll
  2. Data Keluarga
  3. Catatan Keluarga
  4. Data Warga TP PKK
  5. Rekapitulasi Catatan dan Kegiatan Warga
  6. Rekapitulasi Data Bumil dll
- Dasa Wisma II "Nama Dasa Wisma" dan seterusnya...

### Terjemahan Bahan (Formal)

- Administrasi Dasa Wisma pada baseline ini dicatat dengan pola per unit Dasa Wisma.
- Untuk Dasa Wisma I dengan placeholder nama `"Nama Dasa Wisma"`, dokumen administrasinya meliputi:
  1. Rekapitulasi Data Bumil dll
  2. Data Keluarga
  3. Catatan Keluarga
  4. Data Warga TP PKK
  5. Rekapitulasi Catatan dan Kegiatan Warga
  6. Rekapitulasi Data Bumil dll `[PERLU KONFIRMASI: item ini muncul kembali seperti nomor 1]`
- Pola administrasi yang sama berlaku untuk Dasa Wisma II dengan placeholder nama `"Nama Dasa Wisma"` dan unit Dasa Wisma berikutnya.

### Catatan (Opsional)

- Perapian formal dilakukan pada ejaan `Administerasi` menjadi `Administrasi` tanpa mengubah makna input asli.
- Daftar Dasa Wisma I memuat `Rekapitulasi Data Bumil dll` pada nomor `1` dan `6`; keduanya dipertahankan sambil menunggu klarifikasi.
- Placeholder nama tetap dipertahankan sebagai `"Nama Dasa Wisma"` karena belum ada nama spesifik yang diberikan.
- Update berikutnya dimulai dari `U006`.

## Update 2026-03-08 09:50 WIB (U006)

### Input Aktual (Asli)

- Bendahara
  1. Buku Kas Umum
  2. Buku Kas Harian
  3. Buku Swadaya
  4. Buku BHP
  5. Kwitansi Pemasukan dan Pengeluaran
  6. Buku Program Kerja

### Terjemahan Bahan (Formal)

- Administrasi bendahara pada baseline ini meliputi:
  1. Buku Kas Umum
  2. Buku Kas Harian
  3. Buku Swadaya
  4. Buku BHP
  5. Kwitansi Pemasukan dan Pengeluaran
  6. Buku Program Kerja

### Catatan (Opsional)

- Entri ini dicatat sebagai baseline administrasi untuk fungsi bendahara.
- Tidak ada ambiguitas istilah yang perlu ditandai pada input ini.
- Update berikutnya dimulai dari `U007`.

## Update 2026-03-08 09:53 WIB (U007)

### Input Aktual (Asli)

- Sekretaris
- A. Buku Wajib
  1. Buku Daftar Anggota
  2. Buku Inventaris
  3. Buku Kegiatan
  4. Buku Notulen
  5. Buku Agenda Surat Keluar / Masuk
- Lampiran
  1. Bandel Surat Masuk
  2. Bandel Surat Keluar

### Terjemahan Bahan (Formal)

- Administrasi sekretaris pada baseline ini dicatat sebagai berikut:
  A. Buku Wajib
  1. Buku Daftar Anggota
  2. Buku Inventaris
  3. Buku Kegiatan
  4. Buku Notulen
  5. Buku Agenda Surat Keluar/Masuk
  B. Lampiran
  1. Bundel Surat Masuk
  2. Bundel Surat Keluar

### Catatan (Opsional)

- Perapian formal dilakukan pada penulisan `Keluar / Masuk` menjadi `Keluar/Masuk` dan `Bandel` menjadi `Bundel` tanpa mengubah makna input asli.
- Entri ini dicatat sebagai baseline administrasi untuk fungsi sekretaris.
- Update berikutnya dimulai dari `U008`.

## Update 2026-03-08 09:55 WIB (U008)

### Input Aktual (Asli)

- B.  Buku Bantu
  1. Daftar Hadir
  2. Buku Prestasi
  3. Buku Tamu
  4. Buku Konsutlatsi
  5. Buku Ekspedisi
  6. Buku Bantuan
  7. Buku Agenda Surat Keluar ( Lampitan Bandel Surat Keluar )

### Terjemahan Bahan (Formal)

- Lanjutan administrasi sekretaris pada bagian `B. Buku Bantu` dicatat sebagai berikut:
  1. Daftar Hadir
  2. Buku Prestasi
  3. Buku Tamu
  4. Buku Konsultasi
  5. Buku Ekspedisi
  6. Buku Bantuan
  7. Buku Agenda Surat Keluar (Lampiran Bundel Surat Keluar)

### Catatan (Opsional)

- Entri ini diposisikan sebagai lanjutan untuk administrasi sekretaris yang sudah dicatat pada `U007`.
- Perapian formal dilakukan pada ejaan `Konsutlatsi` menjadi `Konsultasi` dan `Lampitan Bandel` menjadi `Lampiran Bundel` tanpa mengubah makna input asli.
- Update berikutnya dimulai dari `U009`.

## Update 2026-03-08 09:56 WIB (U009)

### Input Aktual (Asli)

- C. Buku Penunjang Buku Wajib
  1. Buku Program Kerja
  2. Buku Data Umum
- Lampiran :
  1. Buku Grafik TP. PKK Desa

### Terjemahan Bahan (Formal)

- Lanjutan administrasi sekretaris pada bagian `C. Buku Penunjang Buku Wajib` dicatat sebagai berikut:
  1. Buku Program Kerja
  2. Buku Data Umum
- Lampiran:
  1. Buku Grafik TP PKK Desa

### Catatan (Opsional)

- Entri ini diposisikan sebagai lanjutan untuk administrasi sekretaris yang sudah dicatat pada `U007` dan `U008`.
- Perapian formal dilakukan pada penulisan `Lampiran :` menjadi `Lampiran:` dan `TP. PKK` menjadi `TP PKK` tanpa mengubah makna input asli.
- Update berikutnya dimulai dari `U010`.

## Update 2026-03-08 10:00 WIB (U010)

### Input Aktual (Asli)

- petakan sebagai bahan yang koheren untuk analisa keseuaian project yang sudah ada

### Terjemahan Bahan (Formal)

- Bahan yang sudah direkam dipetakan sebagai baseline analisa kesesuaian terhadap project existing.
- Sumber analisa teknis yang dipakai:
  - `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
  - `docs/process/SIDEBAR_DOMAIN_GROUPING_PLAN.md`
  - `routes/web.php`
- Kategori analisa:
  - `match langsung`: sudah ada modul/fitur dengan boundary yang dekat.
  - `reuse parsial/report-only`: bisa ditopang modul existing, tetapi perlu mapping operasional atau masih dominan report.
  - `gap/perlu concern baru`: belum ada boundary domain yang cukup spesifik.

| Kelompok bahan | Bahan administrasi | Modul/fitur existing | Status analisa | Catatan koherensi |
| --- | --- | --- | --- | --- |
| Sekretaris inti | daftar anggota, inventaris, kegiatan, notulen, daftar hadir, buku tamu, agenda surat, bundel surat | `anggota-tim-penggerak`, `inventaris`, `activities`, `buku-notulen-rapat`, `buku-daftar-hadir`, `buku-tamu`, `agenda-surat` + attachment `data_dukung_path` | `match langsung` | Cakupan sekretariat inti sudah paling dekat dengan kebutuhan bahan yang direkam. |
| Bendahara | buku kas umum, kas harian, swadaya, BHP, kwitansi, program kerja | `buku-keuangan`, `bantuans`, `program-prioritas` | `reuse parsial/report-only` | Project sudah punya domain keuangan dan bantuan, tetapi belum terpisah eksplisit per ledger `kas umum/harian/swadaya/BHP/kwitansi`. |
| Pokja I | buku kegiatan, data kegiatan pokja, data warga TP PKK, BKL, BKR, PAAR, prestasi, kader khusus | `activities`, `data-kegiatan-warga`, `data-warga`, `bkl`, `bkr`, `paar`, `prestasi-lomba`, `kader-khusus` | `match langsung` | Coverage Pokja I tergolong kuat; `program kerja/pelaksanaan program` masih lebih dekat ke `program-prioritas` + `activities`. |
| Pokja III | buku kegiatan, inventaris, prestasi, kader khusus | `activities`, `inventaris`, `prestasi-lomba`, `kader-khusus` | `match langsung` | Item inti operasional Pokja III sudah punya padanan modul aktif. |
| Pokja III lanjutan | data keluarga, rumah sehat/tidak sehat, tanaman keras, data umum, data kegiatan PKK | `data-keluarga`, `catatan-keluarga.data-umum-pkk.report`, `catatan-keluarga.data-kegiatan-pkk-pokja-iii.report`, `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, `warung-pkk` | `reuse parsial/report-only` | Sebagian kebutuhan tersedia sebagai report agregat atau perlu interpretasi lintas modul, belum seluruhnya sebagai CRUD mandiri. |
| Pokja IV | buku kegiatan, posyandu, simulasi, kader khusus, prestasi, inventaris, buku tamu | `activities`, `posyandu`, `simulasi-penyuluhan`, `kader-khusus`, `prestasi-lomba`, `inventaris`, `buku-tamu` | `match langsung` | Coverage operasional Pokja IV cukup kuat pada domain inti. |
| Pokja IV lanjutan | data hasil kegiatan posyandu, grafik program, data IVA test, data umum, data kegiatan PKK | `posyandu`, dashboard/chart, `catatan-keluarga.data-kegiatan-pkk-pokja-iv.report`, `catatan-keluarga.data-umum-pkk.report` | `reuse parsial/report-only` | `IVA test` dan `grafik program` belum tampak sebagai domain input mandiri; lebih dekat ke agregasi/report. |
| Dasa Wisma | data keluarga, catatan keluarga, data warga TP PKK, rekap catatan dan kegiatan warga, rekap data bumil | `data-warga`, `catatan-keluarga`, rekap `4.16a-4.19b` pada `catatan-keluarga` | `reuse parsial/report-only` | Kebutuhan Dasa Wisma lebih banyak tertopang oleh flow report dan agregasi, bukan menu CRUD bernama `dasa-wisma`. |
| KWT | daftar anggota, buku tamu, notulen, daftar hadir, kegiatan, penerimaan bantuan, kas, arisan | `buku-tamu`, `buku-notulen-rapat`, `buku-daftar-hadir`, `activities`, `bantuans`, `buku-keuangan` | `gap/perlu concern baru` | Modul-modul dasar ada, tetapi belum ada boundary entitas `KWT` untuk mengikat semua buku ini sebagai satu concern. |
| Program kerja lintas role | buku program kerja, buku pelaksanaan program | `program-prioritas`, `activities` | `reuse parsial/report-only` | Cocok sebagai pasangan `rencana + realisasi`, tetapi perlu keputusan kontrak bila ingin identik dengan istilah buku fisik yang Anda pakai. |

- Ringkasan analisa:
  - Area yang paling siap dipakai: sekretariat inti, kegiatan lintas pokja, inventaris, buku tamu, prestasi, kader khusus, data warga, BKL, BKR, PAAR, posyandu, simulasi.
  - Area yang relatif sudah ada tetapi belum identik: bendahara granular, data umum, data kegiatan Pokja III/IV, rekap Dasa Wisma, buku program kerja/pelaksanaan program.
  - Area yang paling berpotensi butuh concern baru atau wrapper domain: KWT, ledger bendahara yang lebih rinci, IVA test sebagai input mandiri, kliping/grafik sebagai arsip terstruktur.

### Catatan (Opsional)

- Entri ini adalah bahan analisa `doc-only`; tidak mengubah kontrak canonical modul yang sudah ada.
- Status `match langsung/reuse parsial/gap` adalah peta kerja awal untuk membaca kesesuaian project existing terhadap bahan yang sudah Anda berikan.
- Jika diperlukan, langkah berikutnya yang paling logis adalah menurunkan matriks ini menjadi tabel `bahan admin -> slug modul -> status -> tindak lanjut`.
- Update berikutnya dimulai dari `U011`.

## Update 2026-03-12 16:12 WIB (U011)

### Input Aktual (Asli)

- tidak ada tambahan

### Terjemahan Bahan (Formal)

- Tidak ada tambahan informasi pada pembaruan ini.

### Catatan (Opsional)

- Owner menyatakan rangkaian update sudah selesai.
