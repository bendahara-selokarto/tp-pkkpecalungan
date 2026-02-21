# Sidebar Domain Grouping Plan

Tujuan:
- Mengelompokkan menu administrasi menjadi domain organisasi:
  - Sekretaris TPK
  - Pokja I
  - Pokja II
  - Pokja III
  - Pokja IV

Sumber acuan:
- `AGENTS.md` (aturan teknis dan eksekusi)
- `PEDOMAN_DOMAIN_UTAMA_101_150.md` (kontrak domain lampiran 4.9-4.15)
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md` (matrix slug domain)

## Struktur Group Sidebar

1. Sekretaris TPK
- `anggota-tim-penggerak`
- `kader-khusus`
- `agenda-surat`
- `bantuans`
- `inventaris`
- `activities`
- `anggota-pokja`
- `prestasi-lomba`

2. Pokja I
- `data-warga`
- `data-kegiatan-warga`
- `bkl`
- `bkr`

3. Pokja II
- `data-pelatihan-kader`
- `taman-bacaan`
- `koperasi`
- `kejar-paket`

4. Pokja III
- `data-keluarga`
- `data-industri-rumah-tangga`
- `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`
- `warung-pkk`

5. Pokja IV
- `posyandu`
- `simulasi-penyuluhan`
- `catatan-keluarga`
- `program-prioritas`
- `pilot-project-naskah-pelaporan`
- `pilot-project-keluarga-sehat`

Catatan:
- Untuk scope kecamatan, group `Monitoring Kecamatan` tetap dipertahankan (`desa-activities`).

## Rencana Implementasi Teknis

1. Update definisi menu group di `resources/js/Layouts/DashboardLayout.vue`.
2. Gunakan generator scoped path (`/desa/*` dan `/kecamatan/*`) untuk mengurangi duplikasi.
3. Pertahankan pola UI existing:
- active state berdasarkan prefix URL
- collapse/expand sidebar tetap sama
- policy/scope backend tetap jadi authority akses
4. Validasi:
- `npm run build`
- `php artisan test` (full suite)

## Status

- [x] Struktur group sidebar disusun per Sekretaris TPK + Pokja I-IV.
- [x] Implementasi pada `DashboardLayout.vue`.
- [x] Build + test suite pasca perubahan.
