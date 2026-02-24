# Mapping Laporan Tahunan PKK 2025 (Ekstensi Lokal)

## Sumber Dokumen
- Dokumen utama: `docs/referensi/LAPORAN TAHUNAN PKK th 2025.docx`
- Identitas yang terbaca:
  - `LAPORAN TAHUNAN`
  - `TIM PENGGERAK PKK KEC. PECALUNGAN`
  - `TAHUN 2025`

## Konteks Visual Dokumen (Wajib)
- Dokumen ini adalah `naskah .docx`, bukan dokumen tabel formal.
- Struktur `w:tbl` pada OOXML dipakai sebagai alat `perataan/pengendalian tata letak`, bukan representasi tabel ber-border.
- Saat rendering output, elemen layout grid wajib tampil tanpa border agar visual tetap seperti naskah contoh.
- Dokumen contoh dipakai sebagai `struktur/layout template` saja; konten runtime tidak di-hardcode dari file contoh.

## Metode Baca Kontrak
- Ekstraksi text-layer dari `word/document.xml` pada file `.docx`.
- Verifikasi struktur tabel menggunakan node `w:tbl`, `w:tr`, `w:tc`, `w:gridSpan`, `w:vMerge`.
- Hasil verifikasi merge cell:
  - Tabel `1-5`: tidak ada `rowspan/colspan`.
  - Tabel `6`: tidak ada `rowspan/colspan`.
  - Tabel `7`: tidak ada `rowspan/colspan`.

## Peta Struktur Layout Grid (Final)

### Grid 1-5: Kegiatan Sekretariat + Pokja I-IV
- Jumlah kolom: `4`
- Peta kolom operasional:
  - Kolom 1: `nomor_urut`
  - Kolom 2: `tanggal_kegiatan`
  - Kolom 3: `pemisah` (token `:`; tidak perlu disimpan sebagai field domain)
  - Kolom 4: `uraian_kegiatan`
- Catatan:
  - Baris header label kolom tidak muncul eksplisit pada dokumen sumber.
  - Kontrak kolom dikunci dari struktur kolom aktual + pola isi setiap baris.

### Grid 6: Hambatan
- Jumlah kolom: `1`
- Field narasi: `narasi_hambatan`.

### Grid 7: Penutup/Tanda Tangan
- Jumlah kolom: `2`
- Field operasional:
  - `jabatan_penanda_tangan`
  - `nama_penanda_tangan`

## Kontrak Data Canonical (Backend Baseline)

### A. Entitas Header Laporan Tahunan
Field inti:
- `judul_laporan`
- `tahun_laporan`
- `nama_tim_penggerak`
- `level`
- `area_id`
- `created_by`
- `disusun_oleh` (opsional)
- `tanggal_penyusunan` (opsional)

Invariant:
- `level`, `area_id`, `created_by` wajib ada dan konsisten dengan scope user serta `areas.level`.

### B. Entitas Item Kegiatan Tahunan
Field inti:
- `laporan_tahunan_id`
- `bidang` (`sekretariat|pokja-i|pokja-ii|pokja-iii|pokja-iv`)
- `nomor_urut`
- `tanggal_kegiatan`
- `uraian_kegiatan`
- `level`
- `area_id`
- `created_by`

Aturan:
- `tanggal_kegiatan` menggunakan format canonical `Y-m-d`.
- `nomor_urut` unik per `laporan_tahunan_id + bidang`.

### C. Entitas Narasi Evaluasi dan Penutup
Field inti:
- `laporan_tahunan_id`
- `section_key` (`keberhasilan|hambatan|kesimpulan|penutup`)
- `isi_narasi`
- `jabatan_penanda_tangan` (opsional pada `penutup`)
- `nama_penanda_tangan` (opsional pada `penutup`)
- `level`
- `area_id`
- `created_by`

## Kontrak Pengisian Data Runtime (Wajib)
- Generator dokumen wajib memakai struktur template `.docx` sebagai kerangka visual.
- Isi dokumen wajib diambil dari database aplikasi melalui boundary repository/use case, bukan menyalin teks statis dari dokumen contoh.
- Pengisian isi dokumen diperbolehkan dari `lintas tabel` karena dokumen ini adalah ringkasan umum kegiatan tahunan.
- Blok data yang wajib sourced from DB:
  - metadata laporan (`judul_laporan`, `tahun_laporan`, identitas area/tim),
  - seluruh item kegiatan per bidang (`sekretariat`, `pokja-i` s.d. `pokja-iv`),
  - narasi evaluasi (`keberhasilan`, `hambatan`, `kesimpulan`, `penutup`) dan identitas penanda tangan.
- Jika data DB untuk section tertentu belum tersedia, gunakan fallback kosong terkontrol pada section tersebut tanpa mengubah urutan dokumen.

### Fallback Kelengkapan Data (Wajib)
- Jika hasil pencarian data dari DB tidak ditemukan atau belum cukup untuk section tertentu, sistem `boleh` menyediakan form isian baru untuk melengkapi dokumen laporan tahunan.
- Form isian pelengkap wajib disimpan ke storage aplikasi (bukan hanya state sementara) agar bisa dipakai ulang pada generate berikutnya.
- Form pelengkap wajib tunduk ke kontrak scope yang sama:
  - menyimpan `level`, `area_id`, `created_by`,
  - hanya bisa diakses/mutasi oleh role yang berwenang pada area yang sama.
- Prioritas sumber saat generate:
  1) data operasional lintas tabel,
  2) data isian pelengkap laporan tahunan,
  3) fallback kosong terkontrol.
- Form pelengkap hanya melengkapi data yang tidak tersedia; tidak mengubah struktur template dan urutan dokumen.

### Aturan Agregasi Lintas Tabel
- Agregasi wajib dilakukan melalui repository concern `laporan-tahunan-pkk` (tidak query ad-hoc di controller/view).
- Data lintas tabel yang dipakai harus tetap memenuhi scope user aktif (`level`, `area_id`) untuk mencegah data leak antar wilayah.
- Sumber lintas tabel minimum yang dapat dipakai:
  - tabel kegiatan operasional (`activities`) sebagai basis kronologi utama,
  - tabel pendukung sekretariat/pokja lain sesuai kebutuhan narasi tahunan (misalnya agenda kegiatan, indikator program, atau modul pokja terkait).
- Urutan output tetap mengikuti struktur dokumen contoh; lintas tabel hanya memengaruhi sumber isi, bukan struktur/urutan dokumen.

## Kontrak Output Dokumen (Wajib)
- Output laporan tahunan harus berupa `1 file utuh` (single document) berformat `.docx`, bukan pecahan per section.
- Urutan konten wajib mengikuti urutan dokumen contoh sumber secara penuh:
  - halaman identitas laporan,
  - pendahuluan,
  - pelaksanaan kegiatan umum,
  - kegiatan per bidang (`sekretariat`, `pokja-i`, `pokja-ii`, `pokja-iii`, `pokja-iv`),
  - keberhasilan,
  - hambatan,
  - kesimpulan,
  - penutup dan tanda tangan.
- Untuk grid layout kegiatan, urutan baris mengikuti urutan input sumber pada tiap bidang (tidak diurut ulang otomatis kecuali diminta eksplisit).
- Kontrak format output dikunci ke `.docx`; tidak ada fallback default ke `.pdf` untuk concern ini.
- Struktur visual mengikuti template contoh, tetapi nilai konten diisi dari DB aplikasi saat proses generate.
- Opsi teknis `Laravel Office` diperbolehkan sebagai pertimbangan implementasi, namun tidak menjadi ketergantungan wajib pada kontrak domain ini.

## Status Sinkronisasi
- Status: `implemented`
- Implementasi backend/UI: `selesai (CRUD + form pelengkap + generator .docx + policy/scope + test)`
- Referensi proses: `docs/process/TODO_KONTRAK_DOMAIN_LAPORAN_TAHUNAN_PKK_2025.md`

## Catatan Koherensi
- Concern ini tidak berasal dari lampiran canonical 4.9-4.24, sehingga diklasifikasikan sebagai `Ekstensi Lokal 2025`.
- Jika modul ini diimplementasikan sebagai menu baru, wajib mengikuti protokol `New Menu/Domain` dan trigger audit dashboard sesuai `AGENTS.md`.
