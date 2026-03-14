# TODO PKJ1B1 Implementasi Report Data Kegiatan PKK Pokja I

Tanggal: 2026-03-14  
Status: `done`
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Buku Data Kegiatan PKK Pokja I (Lampiran 4.21) sudah memiliki mapping autentik dan bukti screenshot di `docs/domain/DATA_KEGIATAN_PKK_POKJA_I_4_21_MAPPING.md`.
- Status pada `docs/domain/dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md` = `planned` (autentik `verified`, modul/report khusus belum ada).
- Perlu implementasi report-only agar konsisten dengan Pokja II-IV yang sudah tersedia via `catatan-keluarga`.

## Kontrak Concern (Lock)

- Domain: `data-kegiatan-pkk-pokja-i` (report-only Lampiran 4.21).
- Role/scope target: `desa-pokja-i` (RW), `kecamatan-pokja-i` (monitoring RO), `desa/kecamatan-sekretaris` (RO).
- Boundary data:
  - Mapping autentik: `docs/domain/DATA_KEGIATAN_PKK_POKJA_I_4_21_MAPPING.md`.
  - Baseline status: `docs/domain/dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md`.
  - Kontrak domain: `docs/domain/DOMAIN_CONTRACT_MATRIX.md`.
  - Rencana sidebar: `docs/process/SIDEBAR_DOMAIN_GROUPING_PLAN.md`.
  - PDF compliance: `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`.
  - Sumber cetak: `docs/domain/CETAK_LAMPIRAN_SUMBER_INPUT.md`.
  - Implementasi report sejenis: `app/Domains/Wilayah/CatatanKeluarga/Controllers/CatatanKeluargaPrintController.php`.
- Acceptance criteria:
  - Report PDF `data-kegiatan-pkk-pokja-i` tersedia untuk desa dan kecamatan.
  - Header tabel dan merge cell sesuai mapping autentik 4.21 (landscape, F4).
  - Akses mengikuti scope + area + tahun anggaran aktif.
  - Dokumen canonical (contract matrix + compliance + cetak lampiran) tersinkron.
  - Dashboard coverage diaudit saat menu domain baru aktif.
- Dampak keputusan arsitektur: `tidak` (reuse `CatatanKeluargaPrintController` untuk report-only tanpa CRUD baru).

## Target Hasil

- [x] Report PDF 4.21 tersedia untuk desa/kecamatan dengan sumber data terpetakan.
- [x] Sinkronisasi dokumen canonical + sidebar + access selesai.

## Langkah Eksekusi

- [x] Analisis scoped dependency + side effect.
- [ ] Implementasi report-only:
  - [x] Tambah route report desa/kecamatan di `routes/web.php`.
  - [x] Tambah method print di `CatatanKeluargaPrintController`.
  - [x] Tambah view PDF `resources/views/pdf/data_kegiatan_pkk_pokja_i_report.blade.php`.
  - [x] Tambah mapping data pada `CatatanKeluargaRepository` sesuai 4.21.
- [x] Sinkronisasi dokumen canonical (trigger doc-hardening aktif):
  - [x] Update `DOMAIN_CONTRACT_MATRIX.md` status `implemented (report-only)`.
  - [x] Update `TERMINOLOGY_NORMALIZATION_MAP.md` untuk label menu/PDF.
  - [x] Update `SIDEBAR_DOMAIN_GROUPING_PLAN.md` dan menu runtime.
  - [x] Update `PDF_COMPLIANCE_CHECKLIST.md` + `CETAK_LAMPIRAN_SUMBER_INPUT.md`.
- [x] Audit dashboard coverage untuk domain baru (report-only, tidak menambah KPI/chart baru).

## Validasi

- [x] L1: targeted feature test report 4.21 (akses valid).
- [x] L2: tolak role tidak valid + tolak mismatch role-area-level.
- [x] L3: `php artisan test` (mandatory jika concern baru diaktifkan).

## Risiko

- Risiko 1: Mapping 4.21 bergantung pada agregasi lintas modul dan bisa undercount jika data input belum lengkap.
- Risiko 2: Salah konfigurasi akses membuat report terlihat pada role yang tidak semestinya.

## Keputusan

- [x] K1: Implementasi report-only via `CatatanKeluargaPrintController` (tanpa CRUD baru).
- [x] K2: Dashboard coverage perlu diaudit saat menu domain aktif.

## Keputusan Arsitektur (Jika Ada)

- [ ] Buat/tautkan ADR jika boundary repository/report-only dipisah dari `catatan-keluarga`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jika report belum stabil, nonaktifkan menu/route dan kembali ke status `planned` di dokumen canonical.

## Output Final

- [x] Ringkasan apa yang diubah dan kenapa.
- [x] Daftar file terdampak.
- [x] Hasil validasi + residual risk.
