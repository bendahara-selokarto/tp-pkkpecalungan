# Taksonomi Istilah Kerja

Status: `non-canonical`  
Indexed: `no`  
Decision State: `working-agreement`  
Last Updated: `2026-03-10`  
Entry point keyword: `diskusi-md`

## Apa itu Vocabulary

Dalam konteks diskusi ini, `vocabulary` berarti **kumpulan kata atau istilah yang disepakati untuk dipakai bersama** agar percakapan lebih presisi.

Padanan yang lebih natural dalam bahasa Indonesia:

- `kosa kata`
- `daftar istilah`
- `istilah kerja`

Makna praktisnya:

- kita tidak hanya memilih kata yang terdengar enak,
- kita juga menyepakati arti kerja dari kata itu,
- sehingga saat kata itu dipakai lagi di sesi berikutnya, maknanya tetap konsisten.

Contoh sederhana:

- jika kita sepakat `modul` berarti unit fitur dengan akses, route, dan boundary backend,
- maka kata `modul` tidak lagi dipakai longgar untuk menyebut apa saja.

## Jenis Vocabulary

Beberapa jenis `vocabulary` yang relevan untuk repo ini:

- `Vocabulary umum`
  - kata umum untuk percakapan sehari-hari.
- `Vocabulary kerja`
  - istilah yang dipakai user dan agent untuk berdiskusi dan memberi instruksi.
- `Vocabulary domain`
  - istilah bisnis atau istilah pedoman resmi domain.
- `Vocabulary teknis`
  - istilah untuk arsitektur, layer kode, payload, boundary, projection, dan sejenisnya.
- `Vocabulary user-facing`
  - istilah yang tampil ke user pada UI, menu, PDF, dan user guide.
- `Vocabulary governance/proses`
  - istilah untuk jalur kerja AI seperti concern, router concern, pattern, validation ladder, dan ADR.

## Tujuan

Mengunci kosa kata kerja umum untuk percakapan user-agent agar pembahasan fitur, struktur, dan scope perubahan lebih presisi.

## Prinsip

- Istilah ini adalah kosa kata kerja percakapan, belum otomatis menjadi istilah canonical dokumen proses/domain.
- Jika nanti disepakati final lintas sesi, ringkasannya dipromosikan ke dokumen canonical yang relevan.
- Satu istilah harus punya level yang jelas: di atas modul, setara modul, atau di bawah modul.

## Taksonomi Kerja

| Istilah | Level relatif ke modul | Definisi kerja | Catatan batas |
| --- | --- | --- | --- |
| `Sistem` / `Aplikasi` | di atas | Produk utuh yang dipakai user dan dioperasikan sebagai satu kesatuan | Bukan unit delivery harian |
| `Kapabilitas` | di atas | Kumpulan modul yang melayani satu tujuan besar user | Cocok untuk kelompok seperti administrasi sekretaris, monitoring, atau pelaporan |
| `Domain` | setara atau di atas | Boundary masalah bisnis dan data di backend | Fokusnya boundary logika, bukan navigasi UI |
| `Modul` | acuan utama | Unit fitur yang punya identitas akses, route/menu, boundary backend, dan biasanya halaman UI sendiri | Tidak wajib CRUD penuh |
| `Submodul` | di bawah | Pecahan modul yang masih cukup besar dan punya alur sendiri, tetapi belum layak disebut modul mandiri | Dipakai jika satu modul mulai terlalu lebar |
| `Fitur` | di bawah | Kemampuan spesifik di dalam modul atau submodul | Contoh: ekspor PDF, filter tahun, approval |
| `Alur` / `Use case` | di bawah | Skenario kerja end-to-end untuk satu tujuan user | Contoh: input data, cetak laporan, rollback override |
| `Entitas` | di bawah | Objek data utama yang dikelola sistem | Contoh: area, user, catatan keluarga |
| `Operasi` / `Aksi` | di bawah | Tindakan pada entitas atau alur | Contoh: create, update, print, sync, validate |
| `Concern` | lintas level | Unit pembahasan atau unit perubahan yang sedang dikerjakan | Bisa lebih kecil dari modul atau melintasi beberapa modul |
| `Flow analisa concern` | lintas level | Jalur kerja untuk mengklasifikasi, memetakan, dan mengunci satu concern sebelum atau selama eksekusi | Default-nya memakai single-path + router concern + pattern analisa |
| `Router concern` | lintas level | Peta pemilihan concern canonical berdasarkan jenis permintaan | Menentukan file primer dan validasi minimum |
| `Pattern` | lintas level | Flow reusable untuk situasi tertentu | Contoh: `P-001`, `P-017`, `P-022` |
| `Boundary` | lintas level | Batas tanggung jawab yang memisahkan kepemilikan logic, data, atau akses | Contoh: repository boundary, module boundary |
| `Kontrak data` | lintas level | Bentuk data yang disepakati antar layer atau antar modul | Meliputi field, key, tipe, arti, dan aturan pakai |
| `Sumber kebenaran` | lintas level | Referensi otoritatif yang harus dimenangkan saat ada konflik data atau aturan | Padanan teknis: `source of truth` |
| `Skema data` | lintas level | Struktur penyimpanan dan kontrak data: entitas, tabel, kolom, relasi, dan constraint | Fokus bentuk data, bukan alur pakai |
| `Aggregator` | lintas level | Komponen yang menggabungkan beberapa sumber data menjadi satu hasil baca/ringkasan | Bukan pemilik utama data |
| `Projection` | lintas level | Representasi turunan data untuk kebutuhan tampilan, report, atau read model tertentu | Bukan bentuk data otoritatif |
| `Snapshot` | lintas level | Potret keadaan data atau status pada satu titik waktu | Tidak selalu live dan tidak selalu source of truth |
| `Baseline` | lintas level | Nilai, aturan, atau perilaku dasar sebelum ada penyesuaian | Acuan pembanding untuk override atau perubahan |
| `Jejak data` | lintas level | Penelusuran asal, alur olah, dan pemakaian suatu data lintas modul | Padanan teknis: `data lineage` |
| `Relasi modul` | lintas level | Hubungan antar modul karena berbagi data, output, atau ketergantungan proses | Fokus keterkaitan modul, bukan satu data spesifik |
| `Alur data` | lintas level | Perjalanan data dari input, validasi, simpan, olah, hingga output | Fokus flow end-to-end, bukan struktur tabel |
| `Override` | lintas level | Pengecualian atau penggantian terkontrol terhadap baseline/default | Harus eksplisit dan bisa diaudit |
| `CRUD` | bukan level | Pola operasi data: create, read, update, delete | Bukan definisi modul |
| `Menu` | artefak navigasi | Entry navigasi menuju modul atau halaman tertentu | Menu bukan boundary bisnis |
| `Halaman` / `Page` | artefak UI | Representasi layar untuk satu modul, submodul, atau alur | Satu modul bisa punya lebih dari satu halaman |
| `Slug` | artefak teknis | Identifier teknis stabil untuk route, visibility, atau mapping internal | Jangan disamakan dengan label user-facing |

## Definisi Inti yang Dikunci

### Modul

`Modul` adalah unit fitur operasional yang dikenali user dan sistem sebagai satu boundary akses.

Ciri minimum modul:

- punya identitas akses backend,
- punya route atau entry menu,
- punya boundary backend sendiri,
- biasanya punya satu atau lebih halaman UI,
- bisa punya operasi CRUD, report, monitoring, import, export, atau kombinasi.

Kesimpulan:

- Modul **bukan** sinonim CRUD.
- Modul **bukan** sekadar halaman.
- Modul adalah unit fitur.

### Domain

`Domain` adalah boundary masalah bisnis dan data.

Catatan:

- Jika `modul` dilihat dari sisi produk/user, maka `domain` dilihat dari sisi struktur backend.
- Dalam kasus sederhana, satu modul bisa berkorespondensi dengan satu domain utama.
- Dalam kasus kompleks, satu domain bisa melayani beberapa modul, atau satu modul menyentuh beberapa subdomain.

### Concern

`Concern` adalah unit pembahasan, unit perubahan, atau unit audit.

Catatan:

- Concern tidak harus sama dengan modul.
- Concern bisa sangat sempit, misalnya `filter tahun_anggaran`.
- Concern bisa lintas modul, misalnya `visibility modul`, `normalisasi label`, atau `isolasi authorization`.

### Flow analisa concern

`Flow analisa concern` adalah jalur kerja untuk memahami satu concern secara terstruktur sebelum atau selama eksekusi.

Bentuk default yang sudah hidup di repo:

- klasifikasikan concern,
- cek ulang routing bila perlu,
- kunci kontrak concern,
- baca file concern secara scoped,
- lalu tentukan validation ladder yang sesuai.

Rule of thumb:

- Jika user berkata, "analisa concern ini", yang dimaksud adalah menjalankan `flow analisa concern`, bukan langsung lompat ke patch acak.

### Router concern

`Router concern` adalah peta concern canonical berdasarkan jenis permintaan.

Fungsinya:

- memilih concern utama,
- menentukan file primer,
- menentukan validasi minimum.

Rule of thumb:

- Jika user berkata, "ini masuk concern apa?", sebut itu `router concern`.

### Pattern

`Pattern` adalah flow reusable untuk kondisi tertentu yang sudah didokumentasikan.

Contoh:

- `P-001` untuk scoped analysis,
- `P-017` untuk routing deterministik,
- `P-022` untuk routing reflektif,
- `P-025` untuk audit UI/UX via code.

Rule of thumb:

- Jika user berkata, "pakai flow yang biasa untuk kasus seperti ini", kemungkinan yang dirujuk adalah `pattern`.

### Boundary

`Boundary` adalah batas tanggung jawab yang menentukan:

- logic ini milik layer mana,
- query ini boleh hidup di mana,
- data ini boleh dibaca atau ditulis lewat jalur apa,
- dan siapa authority dari satu keputusan teknis.

Contoh di repo ini:

- `repository boundary` untuk query domain,
- `policy -> scope service` sebagai boundary authority akses,
- `controller -> use case/action -> repository -> model` sebagai boundary arsitektur utama.

Rule of thumb:

- Jika user bertanya, "ini seharusnya hidup di layer mana?" atau "bolehkah query ini dari controller?", sebut itu `boundary`.

### Kontrak data

`Kontrak data` adalah bentuk data yang disepakati agar dua sisi sistem bisa saling bicara tanpa ambigu.

Cakupan umumnya:

- nama field atau key,
- tipe dan format nilai,
- field wajib/opsional,
- arti bisnis tiap field,
- aturan kompatibilitas jika berubah.

Rule of thumb:

- Jika user bertanya, "payload ini seharusnya bentuknya seperti apa?" atau "kalau key ini diganti apa dampaknya?", sebut itu `kontrak data`.

### Sumber kebenaran

`Sumber kebenaran` adalah referensi otoritatif yang harus dimenangkan jika ada konflik.

Contoh di repo ini:

- `areas` adalah single source of truth wilayah,
- backend authorization adalah source of truth akses,
- payload visibility backend adalah source of truth UI untuk menu akses.

Rule of thumb:

- Jika user bertanya, "patokan finalnya data/aturan yang mana?", sebut itu `sumber kebenaran`.

### Skema data

`Skema data` adalah istilah kerja untuk bentuk dan kontrak data yang disimpan sistem.

Cakupan umumnya:

- entitas apa yang ada,
- tabel atau model apa yang menyimpan data,
- kolom apa yang tersedia,
- relasi dan constraint apa yang berlaku.

Rule of thumb:

- Jika user bertanya, "data ini disimpan di tabel apa, field apa, relasinya bagaimana?", sebut itu `skema data`.

### Aggregator

`Aggregator` adalah komponen yang menggabungkan beberapa sumber data menjadi satu hasil baca atau ringkasan.

Sifat utamanya:

- membaca dari beberapa sumber,
- menyusun hasil gabungan,
- tidak otomatis menjadi pemilik data asal.

Contoh bentuk:

- use case dashboard yang menggabungkan beberapa repository,
- summary builder yang merangkum beberapa modul menjadi satu blok tampilan.

Rule of thumb:

- Jika user bertanya, "komponen ini tugasnya menggabungkan data dari beberapa tempat?", sebut itu `aggregator`.

### Projection

`Projection` adalah representasi turunan dari data asli untuk kebutuhan baca tertentu.

Sifat utamanya:

- dibentuk dari source data yang lebih otoritatif,
- dipakai untuk tampilan, laporan, export, atau read model,
- boleh lebih ringkas, lebih lebar, atau sudah ditransformasi.

Contoh di repo ini:

- struktur autentik 19 kolom yang diproyeksikan menjadi report operasional 10 kolom.

Rule of thumb:

- Jika user bertanya, "data asli ini ditampilkan ulang dalam bentuk lain untuk report atau UI?", sebut itu `projection`.

### Snapshot

`Snapshot` adalah potret keadaan pada satu titik waktu.

Sifat utamanya:

- merekam status saat itu,
- dipakai untuk audit, log, cache, atau histori,
- tidak selalu identik dengan keadaan live saat ini.

Contoh di repo ini:

- snapshot concern aktif,
- snapshot historis registry atau validation log.

Rule of thumb:

- Jika user bertanya, "kita ingin lihat keadaan pada saat tertentu, bukan keadaan live sekarang?", sebut itu `snapshot`.

### Baseline

`Baseline` adalah acuan dasar sebelum ada perubahan, penyesuaian, atau pengecualian.

Contoh bentuk:

- baseline mode akses,
- baseline tampilan PDF,
- baseline perilaku sebelum rollout baru.

Rule of thumb:

- Jika user bertanya, "patokan default awalnya apa sebelum disesuaikan?", sebut itu `baseline`.

### Jejak data

`Jejak data` adalah istilah kerja untuk menelusuri satu data secara menyeluruh:

- data itu diinput dari modul mana,
- dibaca atau diolah oleh modul mana,
- diturunkan menjadi output apa,
- dan bergantung pada entitas atau sumber data apa.

Padanan teknis:

- `data lineage` untuk penelusuran asal-ke-pemakaian,
- `upstream/downstream` jika sedang menyorot arah ketergantungan.

Rule of thumb:

- Jika user bertanya, "data ini masuk dari mana dan dipakai di mana saja?", sebut itu `jejak data`.
- Jika yang dicari hanya relasi ketergantungan teknis antar modul, boleh dipersempit menjadi `dependency data`, tetapi istilah default percakapan tetap `jejak data`.

### Override

`Override` adalah pengecualian atau penggantian terkontrol terhadap baseline/default.

Sifat utamanya:

- harus eksplisit,
- lingkupnya jelas,
- bisa di-audit atau di-rollback,
- tidak otomatis mengganti source of truth utama; biasanya dia bekerja di atas baseline yang sudah ada.

Contoh di repo ini:

- module access override yang mengubah mode efektif modul tertentu untuk kombinasi role-scope tertentu.

Rule of thumb:

- Jika user bertanya, "default-nya begini, tapi untuk kasus tertentu kita ganti sementara atau khusus", sebut itu `override`.

### Relasi modul

`Relasi modul` adalah istilah kerja untuk hubungan antar modul.

Cakupan umumnya:

- modul mana menghasilkan data untuk modul lain,
- modul mana membaca atau bergantung pada output modul lain,
- modul mana berdiri sendiri dan mana yang saling terkait.

Rule of thumb:

- Jika user bertanya, "modul ini terhubung ke modul mana saja?", sebut itu `relasi modul`.

### Alur data

`Alur data` adalah istilah kerja untuk perjalanan data secara end-to-end dalam satu proses.

Cakupan umumnya:

- data masuk dari form, import, atau sumber lain,
- divalidasi dan disimpan,
- diolah atau diagregasi,
- lalu dipakai untuk dashboard, PDF, export, atau laporan.

Rule of thumb:

- Jika user bertanya, "dari input sampai laporan, data ini melewati langkah apa saja?", sebut itu `alur data`.

### Kapabilitas

`Kapabilitas` adalah istilah yang saya usulkan sebagai level di atas modul.

Fungsi istilah ini:

- mengelompokkan beberapa modul yang melayani tujuan besar yang sama,
- membantu saat user bicara pada level yang lebih tinggi dari satu modul,
- lebih presisi daripada memakai kata `bagian` atau `kelompok` yang terlalu umum.

Contoh bentuk pikir:

- Kapabilitas `Sekretariat` dapat menaungi beberapa modul buku administrasi.
- Kapabilitas `Pokja IV` dapat menaungi modul operasional dan modul laporan terkait.
- Kapabilitas `Monitoring` dapat menaungi modul pantauan lintas wilayah.

## Relasi Singkat

Urutan pikir yang disarankan:

`Sistem -> Kapabilitas -> Modul -> Submodul -> Fitur -> Alur/Use case -> Operasi`

Istilah pendamping:

- `Domain` = boundary logika/data
- `Concern` = unit diskusi/perubahan
- `Flow analisa concern` = jalur analisa concern terstruktur
- `Router concern` = peta pemilihan concern
- `Pattern` = flow reusable
- `Boundary` = batas tanggung jawab
- `Kontrak data` = bentuk data yang disepakati
- `Sumber kebenaran` = referensi otoritatif
- `Skema data` = bentuk penyimpanan dan kontrak data
- `Aggregator` = penggabung beberapa sumber data
- `Projection` = representasi turunan untuk baca/tampil
- `Snapshot` = potret status pada satu waktu
- `Baseline` = acuan default awal
- `Jejak data` = peta asal, alur olah, dan pemakaian data
- `Relasi modul` = peta keterkaitan antar modul
- `Alur data` = urutan perjalanan data end-to-end
- `Override` = pengecualian terkontrol dari baseline
- `Menu` / `Halaman` = artefak UI
- `CRUD` = pola operasi, bukan level struktur

## Rule of Thumb Percakapan

- Jika pembicaraan menyangkut unit yang punya akses, slug, route, dan halaman sendiri: sebut `modul`.
- Jika pembicaraan lebih besar dari satu modul: sebut `kapabilitas`.
- Jika pembicaraan hanya bagian kemampuan di dalam modul: sebut `fitur`.
- Jika pembicaraan fokus pada skenario kerja user: sebut `alur` atau `use case`.
- Jika pembicaraan fokus pada perubahan teknis yang sedang dikerjakan: sebut `concern`.
- Jika pembicaraan fokus pada cara menganalisis satu concern secara utuh: sebut `flow analisa concern`.
- Jika pembicaraan fokus pada pemilihan concern canonical dan jalur validasinya: sebut `router concern`.
- Jika pembicaraan fokus pada flow reusable yang sudah terdokumentasi: sebut `pattern`.
- Jika pembicaraan fokus pada batas tanggung jawab layer, modul, atau jalur query: sebut `boundary`.
- Jika pembicaraan fokus pada bentuk payload/DTO/response yang disepakati: sebut `kontrak data`.
- Jika pembicaraan fokus pada patokan final yang harus dimenangkan saat konflik: sebut `sumber kebenaran`.
- Jika pembicaraan fokus pada boundary data dan logika: sebut `domain`.
- Jika pembicaraan fokus pada bentuk tabel, field, relasi, dan constraint: sebut `skema data`.
- Jika pembicaraan fokus pada komponen penggabung beberapa sumber baca: sebut `aggregator`.
- Jika pembicaraan fokus pada bentuk turunan untuk report/UI/read model: sebut `projection`.
- Jika pembicaraan fokus pada keadaan pada satu titik waktu: sebut `snapshot`.
- Jika pembicaraan fokus pada default awal sebelum penyesuaian: sebut `baseline`.
- Jika pembicaraan fokus pada satu data yang ingin ditelusuri dari input sampai konsumsi lintas modul: sebut `jejak data`.
- Jika pembicaraan fokus pada keterkaitan antar modul: sebut `relasi modul`.
- Jika pembicaraan fokus pada perjalanan data dari input sampai laporan: sebut `alur data`.
- Jika pembicaraan fokus pada pengecualian khusus dari default: sebut `override`.

## Beda Cepat Istilah Penelusuran

- `Skema data` = bentuk data disimpan.
- `Jejak data` = satu data berasal dari mana dan dipakai di mana.
- `Relasi modul` = modul mana terkait dengan modul lain.
- `Alur data` = data melewati langkah proses apa saja dari awal sampai output.

## Beda Cepat Istilah Kode

- `Flow analisa concern` = jalur analisa concern dari klasifikasi sampai validasi awal.
- `Router concern` = peta concern -> file primer -> validasi minimum.
- `Pattern` = recipe reusable untuk situasi tertentu.
- `Boundary` = batas siapa boleh melakukan apa.
- `Kontrak data` = bentuk data yang harus dipatuhi antar sisi sistem.
- `Sumber kebenaran` = patokan final saat ada konflik.
- `Aggregator` = penggabung beberapa sumber data.
- `Projection` = bentuk turunan untuk kebutuhan baca/tampil.
- `Snapshot` = potret pada satu waktu.
- `Baseline` = default awal.
- `Override` = pengecualian terkontrol dari baseline.

## Catatan Presisi Repo Saat Ini

- Konteks repo aktif memakai istilah `modul/menu baru` untuk unit delivery fitur.
- Daftar slug seperti `data-warga`, `posyandu`, `laporan-tahunan-pkk`, dan `desa-arsip` memperlihatkan bahwa modul dapat berupa input, laporan, atau monitoring.
- Karena itu, `CRUD` tidak boleh dipakai sebagai definisi tunggal modul.

## Flow Resmi yang Sudah Ada

### 1. Flow Dasar Wajib

Flow dasar repo ini adalah jalur tunggal:

`Classify -> Self-Reflective Checkpoint -> Contract Lock -> Scoped Read -> Minimal Patch -> Validation Ladder -> Doc-Hardening -> ADR Sync -> Report -> Commit Closure`

### 2. Flow Analisa Concern Default

Jika user meminta analisa concern tertentu, flow default yang dipakai adalah:

- jalur tunggal step `Classify` sampai `Scoped Read`,
- `router concern` untuk memilih concern canonical,
- pattern `P-001 Scoped Analysis + Diff-First`,
- pattern `P-017 Zero-Ambiguity Single Path Routing`,
- pattern `P-022 Self-Reflective Routing` jika klasifikasi awal masih meragukan.

### 3. Router Concern Canonical yang Sudah Ada

- `Authorization & visibility`
- `Domain module delivery`
- `Dashboard representation`
- `Pre-release upgrade track`
- `Contract sync doc`
- `Copywriting hardening`
- `UI/UX auditability gate`
- `Arsitektur & risiko`
- `ADR governance`

### 4. Registry Pattern yang Sudah Ada

Saat ini sudah ada `33` pattern terdokumentasi:

- `32` berstatus `active`,
- `1` berstatus `deprecated` (`P-027`).

Kelompok pattern yang sudah ada:

- analisa/routing/governance,
- domain/backend/data,
- UI/frontend/runtime,
- dokumentasi,
- validasi/closure.

## Open Items

- Apakah `kapabilitas` akan dipakai sebagai istilah resmi jangka panjang, atau diganti `kelompok modul` / `area produk`.
- Apakah `submodul` perlu dipakai aktif, atau cukup diperlakukan sebagai `fitur besar`.
- Apakah `domain` dan `modul` di repo ini ingin dijaga 1:1 pada level percakapan, atau tetap dibedakan seperti definisi di atas.
