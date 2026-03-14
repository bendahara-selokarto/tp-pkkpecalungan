# TODO PKJ1A1 Sinkronisasi Menu Pokja I Desa dari Buku Autentik

Tanggal: 2026-03-14  
Status: `done`
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Baseline input autentik Pokja I desa tercatat pada `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md` (U002).
- Setelah audit artefak, sejumlah buku yang ada pada baseline Pokja I belum muncul sebagai menu atau belum mendapat kontrak domain yang setara.
- Concern ini fokus pada sinkronisasi dokumen canonical + rencana penyesuaian menu/akses agar konsisten dengan buku autentik.

## Kontrak Concern (Lock)

- Domain: sinkronisasi artefak menu Pokja I (desa) terhadap buku autentik.
- Role/scope target: `desa-pokja-i` (utama), `kecamatan-pokja-i` (monitoring bila relevan).
- Boundary data:
  - `docs/process/TODO_TBH26A1_BAHAN_AKTUAL_DAN_TERJEMAHAN_BERKALA_2026_03_08.md` (baseline buku autentik).
  - `docs/domain/dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md` (status ketersediaan).
  - `docs/domain/DOMAIN_CONTRACT_MATRIX.md` (kontrak domain + status implementasi).
  - `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md` (label canonical UI/PDF).
  - `docs/process/SIDEBAR_DOMAIN_GROUPING_PLAN.md` (rencana grouping menu).
  - `docs/referensi/MATRIX_AKSES_MODULE_2026_03_11.md` + `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php` (mode akses).
  - `resources/js/Layouts/DashboardLayout.vue` + `resources/js/menus/printMenuRegistry.js` (menu runtime).
  - `routes/web.php` (ketersediaan route).
- Acceptance criteria:
  - Tersedia matriks audit `buku autentik -> slug modul -> status menu/akses`.
  - Status gap (missing/hidden/alias) dikunci untuk 8 item Pokja I yang belum muncul.
  - Rencana sinkronisasi dokumen + menu + akses disusun, termasuk keputusan mapping istilah ambigu.
  - Jika ada domain baru, TODO turunan + trigger dashboard audit dicatat.
- Dampak keputusan arsitektur: `tidak` (doc-only plan).

## Target Hasil

- [x] Matriks audit Pokja I lengkap untuk 8 item missing vs artefak existing.
- [x] Rencana sinkronisasi lintas dokumen + menu + akses disepakati.
- [x] Daftar keputusan mapping istilah ambigu terdokumentasi.

## Matriks Audit Pokja I (Baseline vs Artefak)

| Buku (baseline Pokja I) | Slug/Modul | Status menu/akses saat ini | Catatan audit |
| --- | --- | --- | --- |
| Buku Program Kerja | `rencana-kerja` (dokumen) | Belum ada modul khusus | Disetarakan dengan `Buku Rencana Kerja Pokja I` (dokumen canonical). |
| Buku Pelaksanaan Program Kerja | `activities` | Menu tersedia | Disetarakan dengan `Buku Kegiatan` (alias pelaksanaan). |
| Buku Data Kegiatan Pokja | `data-kegiatan-pkk-pokja-i` | Report-only tersedia | Lampiran 4.21 sudah aktif sebagai report-only (desa/kecamatan). |
| Buku Bantuan | `bantuans` | Akses `desa-pokja-i` aktif | Modul ada, menu Pokja I sudah aktif. |
| Buku Prestasi | `prestasi-lomba` | Menu Pokja I aktif | Akses `desa-pokja-i` RW. |
| Buku Daftar Kader Khusus Pokja I | `kader-khusus` | Akses `desa-pokja-i` aktif | Label canonical 4.9b tetap dipakai. |
| Buku Kegiatan Simulasi | `simulasi-penyuluhan` | Akses `desa-pokja-i` aktif | Menu ada namun bergantung `moduleModes`. |
| Buku Grafik | `dashboard/charts` | Report external tersedia | Report-only; belum domain input mandiri. |
| Buku Kegiatan BKR | `bkr` | Menu tersedia | Label canonical perlu normalisasi. |
| Buku Kegiatan BKL | `bkl` | Menu tersedia | Label canonical perlu normalisasi. |
| Buku Administrasi PAAR | `paar` | Menu tersedia | Label canonical vs operasional perlu dicatat. |

## Langkah Eksekusi

- [x] Analisis scoped dependency + side effect.
- [ ] Audit artefak:
  - [x] Cocokkan baseline Pokja I (U002) dengan `dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md`.
  - [x] Cocokkan baseline Pokja I dengan `DOMAIN_CONTRACT_MATRIX.md` (cek `data-kegiatan-pkk-pokja-i`, `bantuans`, `prestasi-lomba`, `kader-khusus`, `simulasi-penyuluhan`).
  - [x] Cocokkan baseline Pokja I dengan `SIDEBAR_DOMAIN_GROUPING_PLAN.md` + `DashboardLayout.vue`.
  - [x] Cocokkan baseline Pokja I dengan `MATRIX_AKSES_MODULE_2026_03_11.md` + `RoleMenuVisibilityService.php`.
  - [x] Cocokkan kebutuhan `Buku Grafik` dengan artefak chart (`DashboardLayout.vue`, `printMenuRegistry.js`).
- [x] Putuskan mapping istilah ambigu:
  - [x] `Buku Program Kerja` Pokja I disetarakan dengan `Buku Rencana Kerja Pokja I`.
  - [x] `Buku Pelaksanaan Program Kerja` disetarakan dengan `Buku Kegiatan`.
  - [x] `Buku Bantu` disetarakan dengan `Buku Bantuan` (slug `bantuans`).
- [x] Rencana sinkronisasi dokumen canonical:
  - [x] Update status Pokja I pada `dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md`.
  - [x] Update `DOMAIN_CONTRACT_MATRIX.md` bila ada domain baru/alias.
  - [x] Update `TERMINOLOGY_NORMALIZATION_MAP.md` untuk label menu baru.
  - [x] Update `SIDEBAR_DOMAIN_GROUPING_PLAN.md` untuk item Pokja I yang ditambahkan.
- [x] Rencana sinkronisasi menu + akses:
  - [x] Tambahkan item Pokja I di `DashboardLayout.vue` sesuai mapping.
  - [x] Tambahkan/ubah akses di `RoleMenuVisibilityService.php` jika buku Pokja I harus RW/RO.
  - [x] Evaluasi `printMenuRegistry.js` untuk buku baru (report-only).
  - [x] Sinkronkan baseline akses di `docs/referensi/MATRIX_AKSES_MODULE_2026_03_11.md`.
- [x] Jika butuh domain baru (contoh: `data-kegiatan-pkk-pokja-i` atau `pelaksanaan-program`), buat TODO turunan via `scripts/generate_todo.ps1` dan catat trigger audit dashboard.

## Validasi

- [x] L1: audit `rg` untuk memastikan semua artefak terpetakan.
- [x] L2: verifikasi manual konsistensi label menu vs terminology map (doc-only).
- [x] L3: eksekusi test hanya saat implementasi runtime dilakukan.

## Risiko

- Risiko 1: Salah mapping istilah (Program Kerja vs Rencana Kerja vs Pelaksanaan) memicu drift kontrak domain.
- Risiko 2: Perubahan akses menu tanpa sinkron backend memicu item muncul tapi ditolak policy.

## Keputusan

- [x] K1: `Buku Program Kerja` Pokja I = `Buku Rencana Kerja Pokja I`; `Buku Pelaksanaan Program Kerja` = `Buku Kegiatan`.
- [x] K2: Item Pokja I yang ditetapkan sebagai domain baru wajib memicu audit dashboard.
 
### Catatan Keputusan
- `Buku Bantu` Pokja I dipetakan ke `Buku Bantuan` (`bantuans`).

## Keputusan Arsitektur (Jika Ada)

- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md` jika ada keputusan boundary domain baru.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jika mapping belum pasti, catat sebagai `[PERLU KONFIRMASI]` dan tunda perubahan runtime.

## Output Final

- [x] Ringkasan audit + rencana sinkronisasi.
- [x] Daftar file terdampak dan statusnya (doc vs runtime).
- [x] Hasil validasi doc-only + residual risk.
