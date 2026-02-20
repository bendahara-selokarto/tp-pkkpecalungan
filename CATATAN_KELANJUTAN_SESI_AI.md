# CATATAN_KELANJUTAN_SESI_AI.md

Dokumen ini dipakai sebagai log kerja AI agar sesi berikutnya bisa lanjut tanpa analisa ulang dari nol.
Fokus dokumen: efisiensi eksekusi, jebakan/buntu, dan status kelanjutan.

## 1. Jalur Kerja Efisiensi (Terbukti Efektif)

1. Gunakan pola duplikasi domain yang sudah stabil.
- Ambil domain yang paling mirip sebagai template (contoh: `prestasi-lomba` -> `simulasi-penyuluhan`).
- Rename namespace + route slug + nama view + policy + repository interface lebih dulu.
- Setelah rename global selesai, baru ubah kontrak field domain.

2. Terapkan checklist integrasi global setelah domain core jadi.
- `routes/web.php`: resource route desa/kecamatan + route cetak PDF.
- `app/Providers/AppServiceProvider.php`: bind repository interface + register policy gate.
- `resources/js/Layouts/DashboardLayout.vue`: menu desa/kecamatan + active state.
- `README.md`: daftar modul aktif.

3. Terapkan checklist validasi cepat sebelum full test.
- `php artisan route:list --name=<slug-domain>`.
- `php artisan test` untuk test domain baru saja.
- Jika sudah hijau, lanjut `php artisan test` full suite.

4. Pakai guardrail pencarian sisa istilah domain lama.
- Perintah efektif:
```powershell
rg -n "prestasi|lomba|jenis_lomba|prestasi_" app tests resources -S
```
- Ini wajib setelah proses copy domain agar tidak ada field lama tertinggal.

5. Simpan urutan kerja domain baru.
- Migration -> Model -> DTO -> Repository -> Scope Service -> UseCase/Action -> Policy -> Controller -> Pages -> PDF -> Routes/Provider/Menu -> Tests -> Verifikasi.

## 2. Jalur Kegagalan/Buntu yang Pernah Terjadi

1. Dokumen referensi di `AGENTS.md` tidak ada di repo.
- Gejala: file seperti `CONTEXT_INDEX.md`, `ARCHITECTURE.md` tidak ditemukan.
- Dampak: onboarding konteks jadi lambat jika memaksa cari dokumen yang tidak ada.
- Mitigasi: fallback ke `README.md` + pola kode existing + test existing sebagai source of truth.

2. Copy domain tanpa pembersihan istilah lama menyebabkan test salah kontrak.
- Gejala: test `simulasi-penyuluhan` masih pakai kolom `prestasi-lomba` (`jenis_lomba`, `prestasi_*`).
- Dampak: test gagal, validasi request tidak cocok migration/model.
- Mitigasi: lakukan rewrite test penuh berbasis kolom final domain sebelum verifikasi.

3. Integrasi global sering terlewat saat fokus di domain folder.
- Gejala: domain sudah ada tapi route/menu/policy binding belum aktif.
- Dampak: fitur terlihat tidak tersedia atau akses ditolak walau code domain sudah benar.
- Mitigasi: pakai checklist integrasi global (lihat bagian 1 poin 2) sebelum testing akhir.

4. Risiko mismatch role/scope/area level.
- Gejala: user punya role scope tertentu, tapi `area_id` menunjuk level area yang berbeda.
- Dampak: akses menjadi 403 walau role tampak benar.
- Mitigasi: pertahankan validasi policy lewat `UserAreaContextService` dan selalu uji kasus mismatch di test.

## 3. Status Lanjutan

### Status terakhir (checkpoint)
- Commit terakhir modul: `62885ad`
- Modul baru selesai: `simulasi-penyuluhan`, `bkl`, `bkr` (belum commit pada sesi ini)
- Verifikasi terakhir: `php artisan test` lulus penuh (175 test).
- TODO domain prioritas saat ini: `tidak ada` (sudah dihapus karena selesai).

### Resume protocol (jika sesi terputus / mati listrik)
1. Cek posisi terbaru:
```powershell
git log --oneline -n 10
git status --short
```
2. Validasi modul terakhir masih sehat:
```powershell
php artisan route:list --name=bkl
php artisan route:list --name=bkr
php artisan test tests/Feature/BklReportPrintTest.php tests/Feature/BkrReportPrintTest.php
```
3. Lanjut domain berikut dari template:
- salin struktur domain paling mirip
- lakukan rename menyeluruh
- ubah kontrak field sesuai tabel target
- jalankan checklist integrasi global
- jalankan test domain -> full test
