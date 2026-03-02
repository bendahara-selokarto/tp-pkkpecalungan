# TODO ARS26A1 Menu Arsip Dokumen Statis

## Konteks
- Menambahkan menu `Arsip` sejajar `Dashboard` untuk menyediakan daftar dokumen statis yang dapat diunduh.
- Sumber dokumen arsip menggunakan folder canonical lokal: `docs/referensi`.

## Target Hasil
- [x] Route halaman arsip dan route unduh dokumen tersedia untuk user terautentikasi.
- [x] Menu `Arsip` tampil sejajar `Dashboard` pada header layout utama.
- [x] Halaman arsip menampilkan daftar dokumen statis dengan aksi unduh.
- [x] Guard path traversal aktif pada alur unduh dokumen.

## Langkah Eksekusi
- [x] Tambah boundary backend: `Controller -> UseCase -> Repository Interface -> Repository`.
- [x] Tambah halaman Inertia `Arsip/Index` untuk render daftar dokumen.
- [x] Tambah feature test untuk akses halaman, unduh sukses, dan unduh invalid.
- [x] Jalankan validasi test concern arsip.

## Validasi
- [x] `php artisan test tests/Feature/ArsipTest.php`

## Risiko
- Folder `docs/referensi` kosong akan membuat daftar arsip kosong di UI.
- Nama file dokumen yang berubah akan ikut berubah di daftar unduh.

## Keputusan
- Menu `Arsip` diposisikan sebagai menu utilitas dokumen statis, bukan modul input domain.
- Audit dashboard trigger: tidak menambah KPI/chart/progress input baru karena menu ini tidak membuat data domain baru.
- Ekstensi yang diizinkan untuk arsip: `pdf`, `doc`, `docx`, `xls`, `xlsx`.
