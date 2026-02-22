# Date Input Field Matrix

Tanggal audit: 2026-02-22
Scope audit: form tanggal di modul Desa + Kecamatan

## Klasifikasi
- `MIGRATE_PHASE_2`: harus migrasi ke `type="date"` + payload `Y-m-d` + validasi strict.
- `CANONICAL_DONE`: UI dan backend sudah canonical strict `Y-m-d`.
- `CANONICAL_KEEP_STRICTEN`: UI sudah canonical; backend perlu diseragamkan ke validasi strict canonical.
- `EXCEPTION_DOMAIN`: bukan tanggal kalender tunggal, tetap string domain.

## Matrix

| Domain | Field | UI Saat Ini | Backend Saat Ini | Klasifikasi |
| --- | --- | --- | --- | --- |
| Activities | `activity_date` | `type="date"` | `required|date_format:Y-m-d` | `CANONICAL_DONE` |
| Bantuan | `received_date` | `type="date"` | `required|date_format:Y-m-d` | `CANONICAL_DONE` |
| Inventaris | `tanggal_penerimaan` | `type="date"` | `nullable|date_format:Y-m-d` | `CANONICAL_DONE` |
| AgendaSurat | `tanggal_surat`, `tanggal_terima` | `type="date"` | `required/nullable + date_format:Y-m-d` | `CANONICAL_DONE` |
| AnggotaTimPenggerak | `tanggal_lahir` | `type="date"` | `required|date_format:Y-m-d|before_or_equal:today` | `CANONICAL_DONE` |
| AnggotaPokja | `tanggal_lahir` | `type="date"` | `required|date_format:Y-m-d|before_or_equal:today` | `CANONICAL_DONE` |
| KaderKhusus | `tanggal_lahir` | `type="date"` | `required|date_format:Y-m-d|before_or_equal:today` | `CANONICAL_DONE` |
| DataWargaAnggota | `anggota.tanggal_lahir` | `type="date"` | `nullable|date` | `CANONICAL_KEEP_STRICTEN` |
| PilotProjectNaskahPelaporan | `surat_tanggal` | `type="date"` | `nullable|date` | `CANONICAL_KEEP_STRICTEN` |
| DataPelatihanKader | `tanggal_masuk_tp_pkk` | Text bebas (contoh `12/05/2020` atau `2020`) | `required|string|max:100` | `EXCEPTION_DOMAIN` |
| BKL / BKR | `no_tgl_sk` | Text bebas dokumen | `required|string|max:255` | `EXCEPTION_DOMAIN` |

## Sumber Utama Audit

- Frontend `type="date"`:
  - `resources/js/admin-one/components/DataWargaAnggotaTable.vue`
  - `resources/js/Pages/PilotProjectNaskahPelaporan/Create.vue`
  - `resources/js/Pages/PilotProjectNaskahPelaporan/Edit.vue`
- Frontend canonical hasil migrasi Phase 2:
  - `resources/js/Pages/Desa/Activities/Create.vue`
  - `resources/js/Pages/Desa/Bantuan/Create.vue`
  - `resources/js/Pages/Desa/Inventaris/Create.vue`
  - `resources/js/Pages/Desa/AgendaSurat/Create.vue`
  - `resources/js/Pages/Desa/AnggotaTimPenggerak/Create.vue`
  - `resources/js/Pages/Desa/AnggotaPokja/Create.vue`
  - `resources/js/Pages/Desa/KaderKhusus/Create.vue`
  - `resources/js/Pages/Kecamatan/Activities/Create.vue`
  - `resources/js/Pages/Kecamatan/Bantuan/Create.vue`
  - `resources/js/Pages/Kecamatan/Inventaris/Create.vue`
  - `resources/js/Pages/Kecamatan/AgendaSurat/Create.vue`
  - `resources/js/Pages/Kecamatan/AnggotaTimPenggerak/Create.vue`
  - `resources/js/Pages/Kecamatan/AnggotaPokja/Create.vue`
  - `resources/js/Pages/Kecamatan/KaderKhusus/Create.vue`
- Backend request:
  - `app/Http/Requests/Concerns/ParsesUiDate.php`
  - `app/Domains/Wilayah/Activities/Requests/StoreActivityRequest.php`
  - `app/Domains/Wilayah/Bantuan/Requests/StoreBantuanRequest.php`
  - `app/Domains/Wilayah/Inventaris/Requests/StoreInventarisRequest.php`
  - `app/Domains/Wilayah/AgendaSurat/Requests/StoreAgendaSuratRequest.php`
  - `app/Domains/Wilayah/AnggotaTimPenggerak/Requests/StoreAnggotaTimPenggerakRequest.php`
  - `app/Domains/Wilayah/AnggotaPokja/Requests/StoreAnggotaPokjaRequest.php`
  - `app/Domains/Wilayah/KaderKhusus/Requests/StoreKaderKhususRequest.php`
  - `app/Domains/Wilayah/DataWarga/Requests/StoreDataWargaRequest.php`
  - `app/Domains/Wilayah/PilotProjectNaskahPelaporan/Requests/PilotProjectNaskahPelaporanUpsertRequest.php`
  - `app/Domains/Wilayah/DataPelatihanKader/Requests/StoreDataPelatihanKaderRequest.php`
  - `app/Domains/Wilayah/Bkl/Requests/StoreBklRequest.php`
  - `app/Domains/Wilayah/Bkr/Requests/StoreBkrRequest.php`

## Catatan Risiko Phase 2
- Migrasi frontend ke `type="date"` tanpa update request akan menolak payload lama.
- Field yang masih `|date` perlu diperketat agar tidak ambigu parsing.
- Exception domain harus dipertahankan agar tidak kehilangan konteks bisnis dokumen.
