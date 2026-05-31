# PDF Rendering Standards - DomPDF

## Tujuan

Dokumen ini menjadi referensi wajib untuk seluruh proses yang berkaitan dengan pembuatan, modifikasi, refactoring, optimasi, atau penambahan fitur laporan PDF. Sebelum menghasilkan kode PDF baru atau mengubah template PDF yang sudah ada, lakukan pemeriksaan terhadap standar ini.

## Permasalahan yang Pernah Ditemukan

Pada beberapa laporan PDF, lebar kolom tidak mengikuti proporsi yang telah ditentukan meskipun atribut width sudah diberikan pada elemen `<th>`. Setelah dianalisis, penyebab utamanya adalah:

*   Penggunaan `table-layout: fixed`.
*   Definisi lebar kolom langsung pada elemen `<th>`.
*   Penggunaan `width` pada header yang memiliki `colspan`.
*   DomPDF tidak selalu menghormati width pada `<th>`, terutama pada tabel kompleks yang menggunakan `rowspan` dan `colspan`.
*   Selisih width antar kolom terlalu kecil sehingga tidak memberikan perbedaan visual yang signifikan.

## Standar Implementasi

### 1. Hindari table-layout: fixed

Jangan menggunakan:

```css
table-layout: fixed;
```

Gunakan:

```css
table-layout: auto;
```

atau hilangkan properti tersebut jika tidak diperlukan.

### 2. Gunakan colgroup untuk mengatur lebar kolom

Seluruh pengaturan lebar kolom harus dilakukan melalui `<colgroup>`.

Alasan:

*   Lebih konsisten pada DomPDF.
*   Lebih mudah dipelihara.
*   Menghasilkan proporsi yang lebih stabil.

### 3. Jangan mengatur width pada th yang memiliki colspan

Contoh yang harus dihindari:

```html
<th colspan="12" style="width:168px">
```

Lebar harus ditentukan pada kolom anak melalui `<colgroup>`.

### 4. Gunakan proporsi yang jelas

Jangan menggunakan perbedaan lebar yang terlalu kecil seperti:

```text
92px
95px
91px
```

Gunakan perbedaan yang benar-benar mencerminkan prioritas informasi.

### 5. Prioritaskan ruang untuk kolom teks

Kolom berikut harus memperoleh ruang lebih besar dibanding kolom indikator atau checklist:

*   Program
*   Prioritas Program
*   Kegiatan
*   Sasaran Target
*   Keterangan

Kolom bulan, status, checklist, dan sumber dana harus dibuat sesempit mungkin tanpa mengurangi keterbacaan.

## Kewajiban AI CLI

Untuk setiap file yang berhubungan dengan PDF:

1.  Periksa apakah terdapat `table-layout: fixed`.
2.  Periksa apakah width didefinisikan langsung pada `<th>`.
3.  Periksa apakah terdapat width pada elemen yang menggunakan colspan.
4.  Evaluasi apakah proporsi kolom sudah sesuai dengan jenis datanya.
5.  Jika menemukan pola yang bertentangan dengan standar ini, berikan rekomendasi perbaikan atau lakukan refactoring yang sesuai.
6.  Jadikan dokumen ini sebagai referensi otomatis setiap kali menghasilkan atau memodifikasi template PDF.
