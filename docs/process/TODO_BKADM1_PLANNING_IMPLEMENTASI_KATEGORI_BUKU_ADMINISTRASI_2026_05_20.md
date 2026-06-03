# TODO BKADM1 Planning Implementasi Kategori Buku Administrasi

Tanggal: 2026-05-20  
Status: `planned`
Related ADR: `docs/adr/ADR_0011_COMMON_BOOK_VISIBILITY_PER_ROLE_GROUP.md`

## Konteks

- Owner mengunci klasifikasi buku administrasi menjadi dua kategori umum: `buku wajib` dan `buku pembantu`.
- Khusus Sekretaris, ada kategori ketiga: `penunjang buku wajib`.
- Revisi ini menggantikan kontrak sebelumnya yang menempatkan `Buku Inventaris` sebagai buku pembantu bersama. Mulai concern ini, `Buku Inventaris` adalah buku wajib Sekretaris.
- Implementasi wajib memisahkan tiga hal: kategori menu, authority akses backend, dan format/output buku fisik.
- Revisi bisnis 2026-06-04: `Buku Kliping` dan `Buku Kader Khusus` tidak dimiliki oleh jabatan Sekretaris.

## Kontrak Concern (Lock)

- Domain: grouping buku administrasi dan visibilitas menu berbasis jabatan.
- Role/scope target: `sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, `pokja-iv` pada scope `desa` dan `kecamatan`.
- Boundary data: `RoleMenuVisibilityService` sebagai source of truth visibilitas; enforcement tetap lewat policy, middleware, scope service, repository, dan model.
- Acceptance criteria:
  - kategori menu tampil sebagai `Buku Wajib`, `Buku Pembantu`, dan khusus Sekretaris `Penunjang Buku Wajib`;
  - buku pembantu bersama hanya `Buku Prestasi`, `Buku Bantuan`, dan `Buku Kader Khusus`;
  - `Buku Inventaris` tidak lagi diperlakukan sebagai buku pembantu bersama;
  - setiap buku yang dipakai lintas role tetap terisolasi dengan `level + area_id + tahun_anggaran + group`;
  - gap modul yang belum punya slug tidak dipaksakan ke modul lain.
- Dampak keputusan arsitektur: `ya` (mengubah kontrak grouping dan visibilitas lintas role).

## Matrix Kategori Target

| Role group | Kategori | Buku target |
| --- | --- | --- |
| `sekretaris-tpk` | Buku Wajib | Agenda Surat Keluar/Masuk; Buku Daftar Anggota TP PKK; Buku Inventaris; Buku Kegiatan; Buku Notulen Rapat |
| `sekretaris-tpk` | Buku Pembantu | Buku Prestasi; Buku Bantuan |
| `sekretaris-tpk` | Penunjang Buku Wajib | Data Umum; Program Kerja; item penunjang lain hanya setelah dikunci owner |
| `pokja-i` | Buku Pembantu Bersama | Buku Prestasi; Buku Bantuan; Buku Kader Khusus |
| `pokja-i` | Buku Pembantu Pokja | Kegiatan Simulasi; Anggota Simulasi; Buku Tamu Simulasi; Daftar Hadir Simulasi; Notulen Simulasi; Kegiatan BKR; Buku Grafik; Data Lansia; Anggota Pokja I; Buku Data PAAR |
| `pokja-ii` | Buku Pembantu Bersama | Buku Prestasi; Buku Bantuan; Buku Kader Khusus |
| `pokja-ii` | Buku Pembantu Pokja | Buku Rekap Kelompok UP2K; Buku Grafik |
| `pokja-iii` | Buku Pembantu Bersama | Buku Prestasi; Buku Bantuan; Buku Kader Khusus |
| `pokja-iii` | Buku Pembantu Pokja | Buku Rumah Sehat dan Anak Sehat; Buku Bantu Pangan; Buku Jumlah Industri Rumah Tangga; Buku Konsultasi; Buku Kas; Buku Grafik; Buku Notulen; Buku Inventaris |
| `pokja-iv` | Buku Pembantu Bersama | Buku Prestasi; Buku Bantuan; Buku Kader Khusus |
| `pokja-iv` | Buku Pembantu Pokja | Data IVA Test; Data Umum; Data Pengunjung; Hasil Kegiatan Posyandu; Buku ASI Eksklusif |

## Target Hasil

- [ ] Matrix `role group -> kategori -> buku -> slug modul -> status implementasi` tersedia.
- [ ] Mapping runtime di `RoleMenuVisibilityService` mengikuti kategori target.
- [ ] Sidebar/menu cetak memakai label kategori natural sesuai matrix.
- [ ] Buku bersama menjaga konteks `book_group` untuk akun multi-role.
- [ ] Gap modul tanpa slug dicatat sebagai backlog, bukan reuse modul yang salah.
- [ ] Dashboard diaudit: coverage KPI/chart/progress input dipertahankan atau diberi justifikasi jika tidak relevan.
- [ ] Backlog: Implementasi modul dedicated untuk `Buku Konsultasi` dan `Buku Agenda SK` (status: `hidden`).
- [ ] Backlog: Verifikasi format autentik Rakernas X untuk `Buku Notulen Rapat`, `Buku Daftar Hadir`, dan `Buku Tamu` (saat ini `unverified-local-extension`).
- [ ] Backlog: sinkronkan matriks sekretaris agar `Buku Kliping` dan `Buku Kader Khusus` tidak lagi muncul pada grup Sekretaris.

## Langkah Eksekusi

- [ ] P0. Audit baseline slug modul aktual pada route, permission matrix, sidebar, print route, dan dashboard coverage.
- [ ] P0a. Perbaikan label/mapping salah: Pastikan "Buku Konsultasi" tidak mengarah ke "Buku Bantuan" (label disembunyikan sampai modul tersedia).
- [ ] P1. Susun matrix slug:
  - `Agenda Surat Keluar/Masuk` -> kandidat `agenda-surat`;
  - `Buku Daftar Anggota TP PKK` -> perlu konfirmasi slug aktif antara `anggota-tim-penggerak`, `anggota-tim-penggerak-kader`, atau modul baru;
  - `Buku Kegiatan` -> kandidat `activities`;
  - `Buku Notulen Rapat` -> kandidat `buku-notulen-rapat`;
  - buku Pokja tanpa slug khusus diberi status `gap-modul`.
- [ ] P2. Patch backend visibility: group kategori, mode akses, module overrides, dan middleware `module.visibility`.
- [ ] P3. Patch UI: sidebar/menu cetak/dashboard consume payload backend tanpa membuat authority baru di frontend.
- [ ] P4. Patch repository/query untuk buku bersama yang masih perlu `group` isolation.
- [ ] P5. Tambah/ubah test role visibility, middleware, payload Inertia, sidebar contract, dashboard sync, dan anti data leak.
- [ ] P6. Doc-hardening: sinkronkan TODO `RGM26A1`, TODO `BFT26A1`, ADR 0011, dan validation log.

## Validasi

- [ ] L1: `php artisan test tests/Unit/Services/RoleMenuVisibilityServiceTest.php --compact`.
- [ ] L2: `php artisan test tests/Feature/ModuleVisibilityMiddlewareTest.php tests/Feature/MenuVisibilityPayloadTest.php --compact`.
- [ ] L3: test dashboard/menu contract terkait grouping.
- [ ] L4: `npm run build` jika patch UI dilakukan.
- [ ] L5: `php artisan test --compact` untuk closure karena concern menyentuh akses lintas role.

## Risiko

- Risiko 1: slug buku fisik tidak selalu sama dengan nama buku owner, sehingga perlu matrix `nama buku -> slug`.
- Risiko 2: buku bernama sama pada Pokja berbeda dapat memiliki format/output berbeda; reuse format harus menunggu bukti autentik.
- Risiko 3: perubahan kategori UI tanpa enforcement backend dapat membuka akses salah.

## Keputusan

- [x] K1: kategori umum hanya `Buku Wajib` dan `Buku Pembantu`.
- [x] K2: kategori `Penunjang Buku Wajib` hanya berlaku untuk Sekretaris.
- [x] K3: buku pembantu bersama adalah `Buku Prestasi`, `Buku Bantuan`, dan `Buku Kader Khusus`.
- [x] K4: `Buku Inventaris` adalah buku wajib Sekretaris, bukan buku pembantu bersama.
- [ ] K5: mapping final `Buku Daftar Anggota TP PKK` ke slug runtime dikunci setelah audit route/model.

## Keputusan Arsitektur

- [x] ADR terkait: `docs/adr/ADR_0011_COMMON_BOOK_VISIBILITY_PER_ROLE_GROUP.md`.
- [ ] ADR baru hanya dibuat jika implementasi memerlukan boundary baru di luar `RoleMenuVisibilityService` dan pola repository/scope yang sudah ada.

## Fallback Plan

- Jika patch visibility gagal, rollback mapping kategori per role ke baseline terakhir yang lulus.
- Jika modul belum punya slug khusus, tahan sebagai `gap-modul` dan jangan arahkan ke modul bernama mirip.
- Jika ditemukan kebocoran data lintas jabatan, nonaktifkan slug terdampak dari group target sampai repository isolation hijau.

## Output Final

- [ ] Ringkasan perubahan kategori dan alasan.
- [ ] Daftar file runtime, test, dan dokumen terdampak.
- [ ] Hasil validasi + residual risk.
