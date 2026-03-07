# Code Placement Policy (Maintainable Structure)

Tanggal efektif: 2026-03-07  
Status: `active`

## Tujuan

Mengunci pola penempatan kode agar:
- concern baru cepat ditemukan,
- review manual tidak bingung karena campuran pola,
- boundary arsitektur tetap konsisten.

## Zona Struktur (Wajib)

### 1) Domain Feature Wilayah
Semua concern domain wilayah baru wajib ditempatkan di:
- `app/Domains/Wilayah/<Concern>/<Layer>`

Layer canonical:
- `Controllers`, `Requests`, `Actions`, `UseCases`, `Repositories`, `Models`, `Services`, `DTOs`

Contoh:
- `app/Domains/Wilayah/AgendaSurat/*`
- `app/Domains/Wilayah/ProgramPrioritas/*`

### 2) Global Platform (Lintas Domain)
Folder global `app/*` hanya untuk concern lintas-domain/platform:
- `app/Http/*` (auth, dashboard shell, middleware global)
- `app/Policies/*` (policy lintas concern)
- `app/Support/*` (enum/matrix/util canonical)
- `app/Actions`, `app/UseCases`, `app/Repositories`, `app/Services` hanya jika concern tidak spesifik domain wilayah.

## Aturan Keputusan Placement

Gunakan rule ini sebelum membuat file baru:
1. Jika concern menyentuh `level/area_id/scope role wilayah` -> masuk `app/Domains/Wilayah/<Concern>`.
2. Jika concern dipakai lintas-domain dan tidak membawa aturan domain wilayah spesifik -> boleh di folder global `app/*`.
3. Jika ragu, default ke domain folder lalu ekstrak shared utility terpisah setelah ada bukti reuse.

## Frontend Mapping

Penempatan halaman UI domain:
- scope desa: `resources/js/Pages/Desa/<Concern>`
- scope kecamatan: `resources/js/Pages/Kecamatan/<Concern>`
- lintas scope/global: `resources/js/Pages/<ConcernGlobal>`

## Dokumen Mapping

- TODO concern: `docs/process/TODO_<KODE>_*.md`
- ADR jika ada perubahan arsitektur lintas concern: `docs/adr/ADR_*.md`
- Jangan taruh dokumen operasional concern di root repository.

## Legacy Compatibility

Struktur global lama yang sudah ada tetap valid sebagai historis.
Namun, concern baru tidak boleh menambah penyebaran pola campuran tanpa justifikasi eksplisit pada TODO concern.

## Checklist Review Cepat

- Concern baru punya folder domain yang eksplisit.
- Tidak ada file domain wilayah baru tercecer di `app/Actions|UseCases|Repositories|Services` global tanpa justifikasi.
- Mapping backend concern selaras dengan lokasi page Inertia.
