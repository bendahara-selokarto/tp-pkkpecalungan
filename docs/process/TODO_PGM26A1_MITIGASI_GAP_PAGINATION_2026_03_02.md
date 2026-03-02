# TODO PGM26A1 Mitigasi Gap Implementasi Pagination

Tanggal: 2026-03-02  
Status: `in-progress`  
Related ADR: `-`

## Konteks
- Audit implementasi pagination per 2026-03-02 menemukan gap kontrak pada modul list yang masih memakai `Collection` tanpa payload paginator.
- Terdapat drift status dokumen concern pagination (`TODO_UI_PAGINATION_E2E_2026_02_24.md` berstatus `done`, namun sebagian modul belum memenuhi kontrak canonical `page/per_page`).
- Jalur Super Admin (`User Management`, `Management Arsip`) sudah memakai `paginate`, tetapi masih hardcoded `10` dan belum punya normalisasi request `per_page` seperti modul domain lain.

## Kontrak Concern (Lock)
- Domain: pagination list Inertia lintas modul wilayah + super admin.
- Role/scope target: `desa`, `kecamatan`, `super-admin`.
- Boundary data: `Controller -> UseCase/Action -> Repository -> Model` tetap dijaga; frontend hanya consumer metadata pagination.
- Acceptance criteria:
  - semua halaman list target mengirim payload paginator (`data`, `current_page`, `last_page`, `per_page`, `total`, `links`);
  - request list memakai normalisasi `per_page` whitelist (`10,25,50`) + fallback default;
  - query state filter tetap stabil saat pindah halaman/per-page;
  - test feature pagination tersedia pada seluruh concern target.
- Dampak keputusan arsitektur: `tidak` (tidak ubah boundary utama, hanya hardening konsistensi implementasi).

## Scope Mitigasi
- Modul wilayah non-paginated:
  - `Koperasi`, `KejarPaket`, `Posyandu`, `ProgramPrioritas`, `SimulasiPenyuluhan`, `WarungPkk` (Desa + Kecamatan).
  - `PilotProjectKeluargaSehat`, `PilotProjectNaskahPelaporan` (Desa + Kecamatan).
- Modul super admin:
  - `User Management`.
  - `Management Arsip`.
- Dokumen process:
  - sinkronisasi `TODO_UI_PAGINATION_E2E_2026_02_24.md` dan log validasi concern terkait.

## Target Hasil
- [x] Seluruh modul dalam scope mitigasi berpindah dari list `Collection` ke kontrak pagination canonical.
- [x] Kontrak request list `per_page` seragam lintas concern target.
- [ ] UI list concern target memakai komponen pagination reusable dan mempertahankan query aktif.
- [x] Drift status dokumen concern pagination ditutup lewat doc-hardening pass.

## Langkah Eksekusi
- [x] P1. Re-baseline concern: petakan file controller/use case/repository/request/ui/test untuk seluruh modul scope mitigasi.
- [x] P2. Backend contract hardening:
  - [x] tambah request list untuk modul yang belum punya (`per_page` whitelist + fallback);
  - [x] ubah repository list ke `paginate($perPage)->withQueryString()`;
  - [x] ubah use case/controller agar mengirim metadata paginator + `filters` + `pagination.perPageOptions`.
- [ ] P3. Frontend hardening:
  - [ ] ubah props list dari `Array` ke payload paginator;
  - [ ] tambahkan selector `Per halaman` dan `PaginationBar`;
  - [ ] pastikan `router.get` menjaga query filter aktif.
- [x] P4. Super admin pagination normalization:
  - [x] hilangkan hardcode `execute(10)` dengan request normalisasi `per_page`;
  - [x] sinkronkan UI super admin untuk konsumsi query `per_page`.
- [ ] P5. Test hardening:
  - [ ] tambah feature test pagination sukses + invalid `per_page` fallback;
  - [x] tambah guard anti data leak saat page/per_page berubah.
- [x] P6. Doc-hardening pass:
  - [x] sinkronkan status `TODO_UI_PAGINATION_E2E_2026_02_24.md`;
  - [x] catat hasil validasi di `OPERATIONAL_VALIDATION_LOG.md`.

## Validasi
- [x] L1: targeted test concern pagination per modul yang diubah.
- [x] L2: regression test lintas concern list wilayah + super admin.
- [ ] L3: `php artisan test`.
- [x] L4: `npm run build`.
- [ ] L5: smoke test manual (pindah halaman, ubah per-page, back/forward browser, filter persistence).

## Risiko
- Risiko 1: perubahan massal list endpoint berpotensi memicu drift payload antar halaman.
- Risiko 2: query filter existing bisa hilang saat migrasi ke paginator jika mapping URL state tidak seragam.
- Risiko 3: durasi eksekusi tinggi karena scope menyentuh backend, frontend, test, dan dokumentasi.

## Keputusan
- [x] K1: Eksekusi mitigasi dilakukan bertahap per domain (bukan big-bang) agar rollback lebih aman.
- [x] K2: Prioritas awal pada modul dengan gap terbesar (12 modul wilayah non-paginated), lanjut pilot project dan super admin.
- [ ] K3: Concern dinyatakan selesai hanya jika implementasi + test + doc-hardening sudah sinkron.

## Fallback Plan
- Jika regresi ditemukan pada domain tertentu, rollback domain tersebut ke commit stabil terakhir tanpa menghentikan domain lain.
- Jika beban perubahan terlalu besar dalam satu siklus, pecah ke sub-concern per modul dengan status terukur (`planned/in-progress/done`) di dokumen turunan.

## Output Final
- [ ] Ringkasan implementasi mitigasi per domain.
- [ ] Daftar file terdampak (backend, frontend, test, docs).
- [ ] Bukti validasi otomatis + manual dan residual risk.

## Progress Update 2026-03-02
- Backend lintas modul target sudah memakai jalur `paginateByLevelAndArea + execute(level, perPage)` serta `executeAll(level)` untuk kebutuhan print.
- Normalisasi request `per_page` (`10,25,50` + fallback) sudah ditambahkan untuk seluruh modul target dan super-admin (`users`, `arsip`).
- UI super-admin (`Users`, `Arsip`) sudah memakai kontrol `per_page` + `PaginationBar`.
- UI list modul wilayah/pilot masih menggunakan pola array lama; metadata pagination sudah dikirim backend sebagai tahap transisi.
- Validasi otomatis yang sudah dijalankan:
  - `php artisan test --filter Koperasi`
  - `php artisan test --filter KejarPaket`
  - `php artisan test --filter Posyandu`
  - `php artisan test --filter ProgramPrioritas`
  - `php artisan test --filter SimulasiPenyuluhan`
  - `php artisan test --filter WarungPkk`
  - `php artisan test --filter PilotProjectKeluargaSehat`
  - `php artisan test --filter PilotProjectNaskahPelaporan`
  - `php artisan test --filter UserManagementIndexPagination`
  - `php artisan test --filter ArsipManagement`
  - `npm run build`
