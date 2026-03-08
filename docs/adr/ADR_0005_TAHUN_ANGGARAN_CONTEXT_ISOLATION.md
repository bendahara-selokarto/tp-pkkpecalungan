# ADR 0005 Tahun Anggaran Context Isolation

Tanggal: 2026-03-07  
Status: `accepted`  
Owner: AI process governance  
Related TODO: `docs/process/TODO_TAG26A1_REFACTOR_ISOLASI_TAHUN_ANGGARAN_LINTAS_MODUL_2026_03_07.md`  
Supersedes: `-`  
Superseded by: `-`

## Konteks

- Konteks bisnis yang baru dikunci: administrasi TP PKK dikelompokkan berdasarkan `tahun anggaran`, sehingga data operasional harus terisolasi per tahun anggaran.
- Baseline implementasi saat ini hanya konsisten pada isolasi `level + area_id + created_by`; `Profile` belum memuat tahun anggaran aktif, dan mayoritas repository concern wilayah hanya mem-filter `level + area_id`.
- Beberapa concern sudah memiliki field periode (`tahun_laporan`, `tahun_awal`, `tahun_akhir`, `year`, `semester`), tetapi field tersebut bersifat domain-spesifik dan belum menjadi kontrak transversal isolasi data.
- Refactor harus menjaga concern yang sudah ada tetap utuh: tidak memecah ulang modul, tidak memindahkan logic keluar boundary repository/use case/action, dan tidak melemahkan authority backend.

## Opsi yang Dipertimbangkan
### Opsi A - Pertahankan struktur sekarang, tambahkan filter tahun per modul secara eksplisit di UI

- Ringkasan pendek: setiap modul diberi input/filter tahun sendiri, lalu controller/repository concern menanganinya masing-masing.
- Kelebihan:
  - implementasi awal tampak cepat pada modul tertentu.
  - tidak perlu context backend global di awal.
- Konsekuensi:
  - risiko drift sangat tinggi antar modul.
  - frontend menjadi terlalu dominan menentukan context data.
  - operator harus mengatur tahun berulang kali di banyak halaman.

### Opsi B - Tambahkan `tahun_anggaran` sebagai context transversal yang diset dari `Profile`, lalu dipersist ke record concern

- Ringkasan pendek: user memiliki tahun anggaran aktif yang dikendalikan backend; concern data administrasi menyimpan `tahun_anggaran` dan repository default mem-filter `level + area_id + tahun_anggaran`.
- Kelebihan:
  - isolasi data konsisten lintas concern tanpa mengubah struktur domain.
  - cocok dengan pola repository yang saat ini sudah homogen.
  - UX lebih sederhana karena satu titik set context di `Profile`.
- Konsekuensi:
  - butuh retrofit schema, repository, action, report, dan test lintas modul.
  - perlu definisi eksplisit untuk relasi `tahun_anggaran` versus field periode domain-spesifik.

### Opsi C - Pisahkan data per tahun anggaran dengan tabel/partisi concern terpisah

- Ringkasan pendek: setiap tahun anggaran diperlakukan seperti namespace penyimpanan terpisah yang lebih fisik.
- Kelebihan:
  - isolasi data sangat ketat pada level penyimpanan.
- Konsekuensi:
  - kompleksitas migrasi, query, maintenance, dan testing jauh lebih tinggi.
  - terlalu invasif terhadap concern existing untuk kebutuhan aplikasi saat ini.

## Keputusan

- Opsi terpilih: Opsi B.
- Alasan utama:
  - paling sejalan dengan arsitektur aktif `Controller -> UseCase/Action -> Repository -> Model`.
  - memungkinkan refactor bertahap per concern tanpa rewrite domain.
  - menjaga backend tetap menjadi authority context dan akses.
- Kontrak yang dikunci:
  - `Tahun anggaran adalah identitas isolasi data administrasi TP PKK per siklus tahunan, default 1 Januari-31 Desember, dan ditetapkan sebagai context kerja aktif user.`
  - `tahun_anggaran` adalah context transversal administrasi TP PKK, berbeda dari field periode domain-spesifik.
  - data administrasi yang relevan wajib terisolasi minimal dengan `level + area_id + tahun_anggaran`.
  - `Profile` menjadi entry point set tahun anggaran aktif, tetapi nilai efektif tetap di-resolve backend service.
  - repository concern yang relevan wajib menjadi jalur default untuk filter tahun anggaran.
  - concern existing, route utama, dan boundary arsitektur tidak diubah.
  - `Arsip` dikecualikan dari isolasi default `tahun_anggaran` pada ADR ini karena fungsi bisnisnya adalah menyediakan informasi lintas tahun, bukan dataset administrasi TP PKK yang wajib terisolasi per tahun anggaran.

## Dampak

- Dampak positif:
  - desain data menjadi sesuai konteks bisnis yang sebenarnya.
  - risiko tercampurnya data antar tahun anggaran turun drastis.
  - dashboard/report punya landasan context yang konsisten.
- Trade-off:
  - perlu gelombang migration dan retrofit cukup luas.
  - test matrix bertambah karena harus mencakup dimensi tahun aktif.
- Area terdampak (route/request/use case/repository/test/docs):
  - `app/Http/Controllers/ProfileController.php`
  - `app/Http/Requests/ProfileUpdateRequest.php`
  - `app/Models/User.php`
  - service context wilayah + service context tahun anggaran baru
  - repository concern wilayah yang sekarang hanya memakai `level + area_id`
  - migration/factory/seeder concern data administrasi
  - dashboard, report, export, dan dokumen domain/proses terkait

## Validasi

- [x] Targeted audit dokumen + baseline code scoped.
- [ ] Targeted test concern.
- [ ] Regression test concern terkait.
- [ ] `php artisan test` (jika perubahan signifikan).

Catatan validasi:

- Sesi ini bersifat `planning-only`; belum ada perubahan runtime.
- Baseline yang sudah diaudit:
  - `ProfileController`, `ProfileUpdateRequest`, `User`,
  - `UserAreaContextService`,
  - pola repository `paginateByLevelAndArea/getByLevelAndArea`,
  - migration concern wilayah untuk cek keberadaan kolom tahun.

## Rollback/Fallback Plan

- Langkah rollback minimum:
  - jika implementasi wave pilot gagal, batalkan concern wave tersebut tanpa memaksa rollout ke seluruh modul.
  - jika schema multi-wave mulai drift, kembalikan ke adapter repository transisional sebelum melanjutkan migrasi berikutnya.
- Kondisi kapan fallback dijalankan:
  - test anti data leak lintas tahun belum hijau,
  - definisi relasi `tahun_anggaran` vs periode domain masih ambigu,
  - flow `Profile` belum stabil menjadi source context backend.

## Referensi

- `AGENTS.md`
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/TODO_TAG26A1_REFACTOR_ISOLASI_TAHUN_ANGGARAN_LINTAS_MODUL_2026_03_07.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- Referensi dunia nyata:
  - BPK JDIH, pengelolaan keuangan desa: tahun anggaran berjalan `1 Januari sampai 31 Desember`.
  - DJPb Kemenkeu, aplikasi SAKTI: periodisasi transaksi tahunan dan closing period berbasis tahun anggaran.

## Status Log

- 2026-03-07: `proposed` dibuat untuk mengunci keputusan awal refactor tahun anggaran sebelum implementasi runtime.
- 2026-03-07: `proposed` -> `accepted` | contract lock, wave-1 pilot, dan target file implementasi pertama sudah dikunci sehingga concern siap dieksekusi.
