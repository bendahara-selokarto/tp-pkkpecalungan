# TODO AGS26D1 Hardening Data Dukung Agenda Surat 2026-02-28

Tanggal: 2026-02-28  
Status: `done`  
Related ADR: `-`

## Konteks
- Perubahan fitur `agenda-surat` menambah field upload user-facing `Data Dukung (Unggah Berkas)` untuk surat masuk/keluar.
- Kontrak domain markdown belum tersinkron terhadap field baru `data_dukung_path` dan status normalisasi istilah 4.10 masih tertulis `partial`.
- Sesuai kontrak AGENTS, perubahan lintas-file dan perubahan kontrak concern harus memiliki jejak TODO markdown + sinkronisasi dokumen domain.

## Target Hasil
- [x] Kontrak field canonical modul `agenda-surat` pada domain matrix memuat `data_dukung_path` sebagai ekstensi operasional.
- [x] Jejak migration kontrak field `agenda-surat` tersinkron di matrix teknis.
- [x] Terminology map 4.10 tersinkron dengan implementasi label UI terbaru (menu/index + label upload file).
- [x] Jejak hardening concern terdokumentasi dalam TODO process.

## Langkah Eksekusi
- [x] Audit drift scoped pada `docs/domain/DOMAIN_CONTRACT_MATRIX.md` dan `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`.
- [x] Patch row domain matrix lampiran 4.10 untuk menambahkan `data_dukung_path` (opsional) beserta catatan koherensi.
- [x] Patch daftar migration kontrak agar memuat migration penambahan kolom `data_dukung_path`.
- [x] Patch terminology map row 4.10 agar status menjadi `match` dan menegaskan label user-facing `Data Dukung (Unggah Berkas)`.

## Validasi
- [x] Verifikasi implementasi UI label 4.10:
  - `resources/js/Layouts/DashboardLayout.vue`
  - `resources/js/Pages/Desa/AgendaSurat/Index.vue`
  - `resources/js/Pages/Kecamatan/AgendaSurat/Index.vue`
  - `resources/js/Pages/Desa/AgendaSurat/Create.vue`
  - `resources/js/Pages/Kecamatan/AgendaSurat/Create.vue`
- [x] Verifikasi kontrak backend upload/simpan path + route attachment:
  - `app/Domains/Wilayah/AgendaSurat/*`
  - `routes/web.php`
- [x] Validasi test concern (fitur + policy):
  - `php artisan test tests/Feature/DesaAgendaSuratTest.php tests/Feature/KecamatanAgendaSuratTest.php tests/Unit/Policies/AgendaSuratPolicyTest.php`

## Risiko
- Field `data_dukung_path` merupakan ekstensi operasional; perlu dijaga agar tidak mengubah struktur autentik header tabel PDF Lampiran 4.10.
- Jika label upload file diubah ke istilah teknis mentah, copywriting concern bisa drift kembali.

## Keputusan
- [x] `data_dukung_path` dikunci sebagai field opsional operasional pada concern `agenda-surat`.
- [x] Label user-facing tetap natural: `Data Dukung (Unggah Berkas)`.
- [x] Tidak diperlukan ADR baru karena boundary arsitektur/policy lintas concern tidak berubah.
