# Vocabulary Data Technical

Status: `non-canonical`  
Indexed: `no`  
Retrieval Mode: `on-demand`

## Cakupan

File ini memuat istilah data dan teknis yang sering dipakai saat membahas kode, kontrak, dan alur data.

### Boundary

Batas tanggung jawab yang menentukan:

- logic ini milik layer mana,
- query ini boleh hidup di mana,
- data ini boleh dibaca atau ditulis lewat jalur apa,
- siapa authority dari satu keputusan teknis.

### Kontrak data

Bentuk data yang disepakati antar layer atau antar modul.

Cakupan umumnya:

- nama field atau key,
- tipe dan format nilai,
- field wajib/opsional,
- arti bisnis field,
- aturan kompatibilitas jika berubah.

### Sumber kebenaran

Referensi otoritatif yang harus dimenangkan jika ada konflik.

Contoh repo ini:

- `areas` sebagai source of truth wilayah,
- backend authorization sebagai source of truth akses,
- payload visibility backend sebagai source of truth UI.

### Skema data

Struktur penyimpanan data:

- entitas,
- tabel atau model,
- kolom,
- relasi,
- constraint.

### Aggregator

Komponen yang menggabungkan beberapa sumber data menjadi satu hasil baca atau ringkasan.

Catatan:

- aggregator bukan pemilik utama data asal.

### Projection

Representasi turunan dari data asli untuk kebutuhan baca tertentu.

Contoh:

- data asli dipetakan ulang untuk report atau read model.

### Snapshot

Potret keadaan pada satu titik waktu.

Contoh:

- snapshot status,
- snapshot registry,
- snapshot validation log.

### Baseline

Acuan dasar sebelum ada penyesuaian atau pengecualian.

### Jejak data

Penelusuran satu data secara menyeluruh:

- masuk dari modul mana,
- dibaca atau diolah modul mana,
- diturunkan menjadi output apa,
- bergantung pada sumber data apa.

Padanan teknis:

- `data lineage`

### Relasi modul

Hubungan antar modul karena berbagi data, output, atau ketergantungan proses.

### Alur data

Perjalanan data dari input, validasi, simpan, olah, hingga output.

### Override

Pengecualian atau penggantian terkontrol terhadap baseline/default.

Sifat utamanya:

- eksplisit,
- lingkup jelas,
- bisa diaudit,
- bisa di-rollback.

### CRUD

Pola operasi:

- create,
- read,
- update,
- delete.

Catatan:

- CRUD adalah pola operasi,
- bukan definisi modul.

## Beda Cepat

- `Boundary` = batas tanggung jawab.
- `Kontrak data` = bentuk data yang harus dipatuhi.
- `Sumber kebenaran` = patokan final saat konflik.
- `Skema data` = bentuk data saat disimpan.
- `Aggregator` = penggabung beberapa sumber data.
- `Projection` = bentuk turunan untuk baca/tampil.
- `Snapshot` = potret pada satu waktu.
- `Baseline` = default awal.
- `Jejak data` = asal dan pemakaian satu data.
- `Relasi modul` = keterkaitan antar modul.
- `Alur data` = langkah perjalanan data.
- `Override` = pengecualian dari baseline.
- `CRUD` = pola operasi data.
