# Vocabulary Product Structure

Status: `non-canonical`  
Indexed: `no`  
Retrieval Mode: `on-demand`

## Cakupan

File ini memuat istilah untuk struktur produk, fitur, dan artefak UI.

### Sistem / Aplikasi

Produk utuh yang dipakai user dan dioperasikan sebagai satu kesatuan.

### Kapabilitas

Kumpulan modul yang melayani satu tujuan besar user.

Contoh bentuk pikir:

- `Sekretariat`
- `Monitoring`
- `Pokja IV`

### Domain

Boundary masalah bisnis dan data di backend.

Catatan:

- `domain` dilihat dari sisi logic/data,
- `modul` dilihat dari sisi produk/user,
- keduanya tidak harus selalu 1:1.

### Modul

Unit fitur operasional yang dikenali user dan sistem sebagai satu boundary akses.

Ciri minimum:

- punya identitas akses backend,
- punya route atau entry menu,
- punya boundary backend sendiri,
- biasanya punya satu atau lebih halaman UI.

Catatan:

- modul bukan sinonim CRUD,
- modul bukan sekadar halaman.

### Submodul

Pecahan modul yang masih cukup besar dan punya alur sendiri, tetapi belum layak disebut modul mandiri.

### Fitur

Kemampuan spesifik di dalam modul atau submodul.

Contoh:

- ekspor PDF,
- filter tahun,
- approval.

### Menu

Entry navigasi menuju modul atau halaman tertentu.

Catatan:

- menu adalah artefak navigasi,
- menu bukan boundary bisnis.

### Halaman / Page

Representasi layar untuk satu modul, submodul, atau alur.

Catatan:

- satu modul bisa punya lebih dari satu halaman.

### Slug

Identifier teknis stabil untuk route, visibility, atau mapping internal.

Catatan:

- slug tidak sama dengan label user-facing.

## Beda Cepat

- `Kapabilitas` = kelompok besar di atas modul.
- `Domain` = boundary logic/data.
- `Modul` = unit fitur utama.
- `Submodul` = pecahan modul.
- `Fitur` = kemampuan spesifik di dalam modul.
- `Menu` = pintu navigasi.
- `Halaman` = layar UI.
- `Slug` = nama teknis stabil.
