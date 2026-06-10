# Analisis Perbandingan: Buku Kegiatan vs Data Kegiatan

Status: `in-progress`

## 1. Pendahuluan
Dokumen ini menganalisis hubungan antara modul **Buku Kegiatan** (Input Log) dan **Data Kegiatan** (Laporan Agregat) dalam sistem TP PKK Pecalungan. Analisis ini bertujuan untuk memastikan bahwa data yang diinput pada level operasional (Buku) memiliki tingkat rincian (informasi) yang cukup untuk menghasilkan laporan yang akurat dan informatif tanpa perlu menebak data (guessing).

## 2. Kondisi Saat Ini

### A. Buku Kegiatan (`Activity` Model)
*   **Karakteristik**: Berupa log naratif harian.
*   **Field Utama**: `title`, `description`, `uraian`, `activity_date`.
*   **Kekurangan**: Data bersifat *free-text*. Laporan (seperti Pokja I) harus mencari kata kunci (seperti "kisah", "pkbn") di dalam uraian untuk menentukan kategori program. Informasi kuantitatif seperti "Volume" dan "Sasaran" tidak memiliki field khusus, sehingga laporan hanya bisa berasumsi nilai "1" untuk setiap entri yang cocok.

### B. Data Kegiatan Warga (`DataKegiatanWarga` Model)
*   **Karakteristik**: Checklist 7 kegiatan standar (Kerja Bakti, Rukun Kematian, dll).
*   **Field Utama**: `kegiatan` (enum), `aktivitas` (boolean).
*   **Kekurangan**: Terlalu kaku dan tidak mencerminkan detail aktivitas yang sebenarnya dilakukan (hanya Ya/Tidak).

### C. Laporan Data Kegiatan (Lampiran 4.14 - 4.24)
*   **Karakteristik**: Tabel agregat yang membutuhkan angka (Volume, Sasaran, Jumlah).
*   **Masalah**: Karena inputnya naratif, laporan kehilangan presisi. Jika satu kegiatan mencakup 50 orang sasaran, sistem saat ini tidak bisa mencatat angka "50" tersebut secara terstruktur di Buku Kegiatan.

## 3. Analisa Per Jabatan (Pokja)

Meskipun memiliki nama serupa ("Data Kegiatan"), setiap Pokja memiliki format laporan dan sumber data yang berbeda secara fundamental:

### Pokja I (Hukum & Pola Asuh)
*   **Sumber Utama**: `Activity` (Buku Kegiatan) & `AnggotaPokja`.
*   **Logika Laporan**: Bergantung pada **pencocokan kata kunci** (keyword matching) seperti "kisah", "pkbn", "krisan" pada uraian teks.
*   **Masalah Spesifik**: Sangat rentan terhadap kesalahan pengetikan dan inkonsistensi narasi.

### Pokja II (Pendidikan & Ekonomi)
*   **Sumber Utama**: `LiterasiWarga`, `KejarPaket`, `TamanBacaan`, `BkbKegiatan`, `TutorKhusus`, `Koperasi`, `PraKoperasiUp2k`.
*   **Logika Laporan**: Agregasi data dari modul-modul spesifik pendidikan dan ekonomi.
*   **Masalah Spesifik**: Data tersebar di banyak tabel kecil, menyulitkan sinkronisasi jika ada perubahan area/wilayah.

### Pokja III (Pangan, Sandang, Perumahan)
*   **Sumber Utama**: `DataWarga` (Household), `DataPemanfaatanTanahPekaranganHatinyaPkk`, `DataIndustriRumahTangga`.
*   **Logika Laporan**: Menghitung metrik rumah tangga (seperti rumah sehat/tidak sehat) dan komoditi pangan/industri.
*   **Masalah Spesifik**: Bergantung pada `activityFlags` yang ditarik dari checklist `DataKegiatanWarga`.

### Pokja IV (Kesehatan & Lingkungan)
*   **Sumber Utama**: `DataWarga` (Anggota), `Posyandu`, `KaderKhusus`, `ProgramPrioritas`, `DataKegiatanWarga`.
*   **Logika Laporan**: Fokus pada data kesehatan (imunisasi, KB, Posyandu) dan infrastruktur lingkungan (jamban, SPAL).
*   **Masalah Spesifik**: Menggunakan logika `isPusCandidate` (Pasangan Usia Subur) dan `WUS` (Wanita Usia Subur) yang dihitung dari umur anggota di `DataWarga`.

## 4. Temuan Analisa Umum
1.  **Ketidakcocokan Granularitas**: Laporan meminta data kuantitatif (berapa kali, berapa orang), sementara input hanya menyediakan data kualitatif (apa yang dilakukan).
2.  **Informasi Tersembunyi**: User mungkin menulis "Dihadiri 20 orang" di kolom `uraian`, tetapi sistem tidak bisa mengekstrak angka "20" tersebut untuk kolom "Sasaran" di laporan.
3.  **Fragilitas Keyword**: Jika user salah mengetik "KISAH" menjadi "KISA", kegiatan tersebut tidak akan muncul di laporan Pokja I karena sistem menggunakan *strict keyword matching*.
4.  **Duplikasi Logika**: Logika perhitungan metrik rumah tangga diulang di beberapa Pokja tetapi dengan parameter filter yang berbeda.

## 4. Rekomendasi Perbaikan (Plan)

### A. Re-strukturisasi Model `Activity`
Menambahkan field terstruktur pada Buku Kegiatan agar lebih informatif:
*   `program_category`: Dropdown kategori (misal: PKBN, Kadarkum, Pola Asuh) untuk menggantikan sistem keyword.
*   `volume`: Angka jumlah pelaksanaan kegiatan.
*   `sasaran_jumlah`: Angka jumlah orang/target yang dicapai.
*   `metode`: Dropdown/Text singkat metode yang digunakan.

### B. Sinkronisasi Otomatis
*   Setiap kali Buku Kegiatan diisi dengan kategori tertentu, data tersebut secara otomatis mengisi slot yang relevan di Laporan Data Kegiatan Pokja terkait.
*   Menghilangkan ketergantungan pada pencarian kata kunci di string narasi.

### C. Peningkatan UI Input
*   Input Buku Kegiatan harus dinamis berdasarkan Role. Jika Role adalah Pokja I, tampilkan field tambahan untuk memilih program standar Pokja I (Pola Asuh, PKBN, dll).

## 5. Rekomendasi Arsitektur: Form Dinamis (JSON Metadata)

Berdasarkan pertimbangan efisiensi pengembangan dan pemeliharaan, pendekatan **Form Dinamis dengan kolom JSON** pada tabel `activities` adalah yang paling optimal dibandingkan membuat modul terpisah untuk setiap Pokja.

### Mengapa Paling Efisien?
1.  **Reduksi Boilerplate**: Kita tidak perlu membuat 4-5 set Controller, Model, dan Migrasi yang hampir identik. Infrastruktur `Activities` yang ada (Auth, Scoping, Attachment, Printing) bisa langsung digunakan.
2.  **Fleksibilitas Tinggi**: Menambah field baru untuk Pokja tertentu hanya membutuhkan perubahan di UI (Vue) dan validasi, tanpa perlu melakukan migrasi database yang berisiko pada data yang sudah ada.
3.  **Laporan Global**: Memudahkan pembuatan "Feed Kegiatan Global" lintas Pokja karena semua data berada di satu tabel.
4.  **Konsistensi**: Memastikan metadata dasar (Tanggal, Tempat, Petugas) selalu seragam, sementara rincian spesifik (Volume, Sasaran, Kategori Program) disimpan di kolom `additional_info` (JSON).

## 6. Rencana Implementasi (Roadmap)

### Fase 1: Database & Model
*   [x] Tambahkan kolom `additional_info` (JSON) pada tabel `activities`.
*   [x] Casting kolom tersebut menjadi `array` di model `Activity`.

### Fase 2: Validasi & Backend
*   [x] Update `StoreActivityRequest` dan `UpdateActivityRequest` untuk menerima metadata terstruktur.
*   [ ] Tambahkan validasi kondisional wajib per Pokja jika kontrak laporan sudah final.

### Fase 3: Frontend (Inertia/Vue)
*   [x] Modifikasi Form `Create` dan `Edit` Desa untuk merender field tambahan secara dinamis berdasarkan Role/Group user yang sedang login.
*   [ ] Putuskan apakah form Kecamatan membutuhkan field dinamis yang sama atau tetap memakai form naratif.

### Fase 4: Laporan (Repository)
*   [ ] Update repository laporan terkait untuk menarik data dari `additional_info`, menggantikan logika pencarian kata kunci yang lama setelah kontrak kolom laporan dikunci.

## 7. Kesimpulan
Dengan pendekatan Form Dinamis, Buku Kegiatan bertransformasi menjadi alat input yang cerdas dan informatif sesuai kebutuhan masing-masing jabatan, sambil tetap menjaga kesederhanaan struktur kode sistem.
