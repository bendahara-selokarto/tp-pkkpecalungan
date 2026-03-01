# TODO MVI26A1 Inventaris Modul Domain untuk Rancang Visibility Sidebar 2026-03-01

Tanggal: 2026-03-01  
Status: `in-progress`  
Related ADR: `docs/adr/ADR_0002_MODULAR_ACCESS_MANAGEMENT_SUPER_ADMIN.md`

## Konteks
- Tujuan concern ini adalah menyiapkan inventaris modul domain yang sudah terbangun sebagai bahan desain grouping visibility yang konsisten.
- Kondisi saat ini menunjukkan beberapa modul dapat diakses (berdasarkan `module.visibility`) tetapi tidak muncul di sidebar, sehingga membingungkan user.
- Source of truth akses backend saat ini berada pada:
  - `RoleMenuVisibilityService` (group -> module mode),
  - `EnsureModuleVisibility` (enforcement route berdasarkan slug modul),
  - payload Inertia `menuGroupModes` + `moduleModes`.
- Sidebar domain saat ini masih dirender dari daftar item statis di `DashboardLayout.vue`.

## Tujuan Hasil
- [ ] Tersusun inventaris modul domain lintas scope `desa|kecamatan` dari sumber runtime aktual.
- [ ] Tersusun matriks koherensi `route tersedia` vs `module mode` vs `menu sidebar`.
- [ ] Tersusun daftar gap + klasifikasi status:
  - `koheren`,
  - `akses ada, menu tidak ada`,
  - `menu disembunyikan sengaja`,
  - `perlu keputusan produk`.
- [ ] Tersusun usulan grouping visibility final sebagai dasar implementasi patch berikutnya.

## Inkoherensi Baseline (Temuan Awal)
- [x] `bantuans` ada di `GROUP_MODULES` dan route `desa`, tetapi belum ada item sidebar domain.
- [x] `simulasi-penyuluhan` termasuk modul `pokja-iv`, tetapi item sidebar dibatasi `desa-pokja-i-only`; berpotensi menutupi akses `desa-sekretaris`.
- [x] `catatan-keluarga`, `pilot-project-keluarga-sehat`, `pilot-project-naskah-pelaporan` memiliki mode akses runtime (`read-only` untuk role tertentu), tetapi item sidebar diset `uiVisibility: disabled`.
- [x] Pola saat ini menghasilkan dual source pada level presentasi menu (payload runtime vs daftar item hardcoded), yang memicu drift UX.

## Langkah Eksekusi
- [ ] Audit scoped source akses dan menu:
  - `routes/web.php`
  - `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`
  - `app/Http/Middleware/EnsureModuleVisibility.php`
  - `resources/js/Layouts/DashboardLayout.vue`
- [ ] Bangun tabel inventaris modul per scope:
  - `module_slug`,
  - group role,
  - mode akses baseline + override pilot (jika ada),
  - ketersediaan route,
  - keberadaan entry sidebar.
- [ ] Definisikan aturan normalisasi untuk modul non-sidebar (contoh: endpoint turunan/report-only) agar tidak dianggap false-positive.
- [ ] Finalisasi daftar gap prioritas tinggi (yang berdampak langsung ke kebingungan navigasi user).
- [ ] Rumuskan opsi desain grouping visibility (minimal 2 opsi) beserta trade-off.
- [ ] Pilih opsi final dan siapkan TODO implementasi patch UI/backend terpisah.

## Validasi
- [ ] Validasi teknis inventaris dengan query scoped (`rg`) agar tidak ada modul terlewat.
- [ ] Validasi koherensi terhadap enforcement backend:
  - modul yang tampil di sidebar wajib memiliki mode akses aktif,
  - modul yang mode aktif tetapi tidak tampil harus memiliki justifikasi eksplisit.
- [ ] Validasi cepat UX:
  - login sebagai `desa-sekretaris`,
  - pastikan daftar menu domain dapat dipahami tanpa mengandalkan URL manual.

## Risiko
- Risiko over-correction: semua modul dipaksa tampil tanpa mempertimbangkan intent produk/eksperimen.
- Risiko mismatch antar role jika inventaris hanya diuji pada satu role.
- Risiko drift ulang jika inventaris tidak dijadikan artefak referensi perubahan berikutnya.

## Keputusan Awal
- [x] Concern ini fokus inventaris + keputusan desain, belum melakukan patch behavior.
- [x] Authority akses tetap backend (`module.visibility` + policy); sidebar adalah representasi, bukan sumber kebenaran akses.
- [x] Hasil concern ini menjadi input wajib untuk TODO implementasi alignment visibility berikutnya.

## Output Final yang Diharapkan
- [ ] Dokumen inventaris modul domain siap pakai untuk perancangan grouping visibility.
- [ ] Daftar inkoherensi terprioritas dengan status keputusan (`fix sekarang`, `tetap hidden`, `butuh keputusan produk`).
- [ ] Rekomendasi jalur implementasi berikutnya (scope perubahan + validasi + fallback).

## Progress Fast Mode (2026-03-01)
- [x] Baseline `moduleModes` role `desa-sekretaris` dinaikkan ke full `read-write` lintas group (`sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, `pokja-iv`).
- [x] Resolver override per-modul untuk role `desa-sekretaris` sementara dilewati agar mode tidak turun oleh pilot override.
- [x] Sidebar `Pokja IV` menampilkan modul report-only turunan `catatan-keluarga` (termasuk `Data Umum PKK Kecamatan`) dengan label natural tanpa kode lampiran.
- [ ] Audit lanjutan tetap diperlukan untuk alignment sidebar agar navigasi UI konsisten dengan mode akses backend.
