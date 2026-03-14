# ADR 0008 Single Path Auth Flow Exceptions

Tanggal: 2026-03-15  
Status: `proposed`  
Owner: santoso  
Related TODO: `docs/process/TODO_SPA26B1_EXCEPTION_SINGLE_PATH_FLOW_AUTH_FRAMEWORK_2026_03_15.md`  
Supersedes: `-`  
Superseded by: `-`

## Konteks

- Arsitektur single-path mewajibkan mutasi data melalui `Controller -> UseCase/Action -> Repository -> Model`.
- Audit menunjukkan beberapa flow auth framework (reset password dan profil user sendiri) masih melakukan mutasi `User` langsung.
- Tanpa kontrak exception yang eksplisit, pola ini berisiko menyebar ke flow non-auth dan melemahkan boundary repository.

## Opsi yang Dipertimbangkan
### Opsi A - Strict tanpa exception

- Ringkasan pendek: semua mutasi, termasuk auth framework, harus lewat repository boundary.
- Kelebihan: konsistensi arsitektur penuh; audit lebih sederhana.
- Konsekuensi: refactor besar pada flow auth Laravel bawaan; risiko gangguan autentikasi.

### Opsi B - Exception terbatas untuk auth framework

- Ringkasan pendek: izinkan mutasi langsung hanya pada flow auth framework yang terdefinisi.
- Kelebihan: menjaga stabilitas auth bawaan, perubahan minimal.
- Konsekuensi: perlu guardrail ketat agar exception tidak meluas.

## Keputusan

- Opsi terpilih: Opsi B.
- Alasan utama: meminimalkan refactor pada flow auth bawaan tanpa mengorbankan boundary domain lainnya.
- Kontrak yang dikunci:
  - Exception hanya untuk **flow auth framework** yang memodifikasi **user sendiri**.
  - Mutasi dibatasi pada `name`, `email`, `password`, `remember_token`.
  - Tidak boleh menyentuh `role`, `scope`, `area_id`, atau data domain.
  - Daftar flow yang diizinkan:
    - `app/Http/Controllers/Auth/*`
    - `app/Http/Controllers/ProfileController.php`
  - Semua flow di luar daftar wajib kembali ke repository boundary.

## Dampak

- Dampak positif: stabilitas auth tetap terjaga, single-path tetap konsisten untuk domain utama.
- Trade-off: ada pengecualian resmi yang harus dijaga ketat.
- Area terdampak (route/request/use case/repository/test/docs):
  - docs: ADR ini + TODO concern terkait.
  - audit: pengecekan berkala agar exception tidak melebar.

## Validasi

- [ ] Targeted test concern.
- [ ] Regression test concern terkait.
- [ ] `php artisan test` (jika perubahan signifikan).

## Rollback/Fallback Plan

- Kunci kembali kebijakan strict single-path.
- Buat concern refactor flow auth untuk memindahkan mutasi ke repository boundary.

## Referensi

- `AGENTS.md`
- `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`
- `docs/process/TODO_SPA26B1_EXCEPTION_SINGLE_PATH_FLOW_AUTH_FRAMEWORK_2026_03_15.md`

## Status Log

- 2026-03-15: `proposed` (inisialisasi kontrak exception auth flow).
