# TODO UI Pagination E2E 2026-02-24

Tanggal: 2026-02-24  
Status: `in-progress`

## Progress

- Fase 1 selesai untuk domain `Buku Kegiatan` (`desa/activities`, `kecamatan/activities`, `kecamatan/desa-activities`).
- Modul list domain lain masih mengikuti backlog concern yang sama dan belum dieksekusi.

## Hasil Scan Pending (2026-02-25)

- Baseline scan `Index.vue`:
  - total halaman list: `57`
  - sudah implementasi pagination: `4` (`SuperAdmin/Users` + 3 halaman `Activities`)
  - belum implementasi pagination: `53` halaman list
- Baseline scan backend:
  - repository yang sudah memakai `paginate(...)`: `UserManagementRepository`, `ActivityRepository`
  - repository domain lain masih dominan `Collection` pada jalur list scoped

### Backlog Modul Pending Pagination

- [x] `AgendaSurat` (Desa + Kecamatan)
- [x] `AnggotaPokja` (Desa + Kecamatan)
- [x] `AnggotaTimPenggerak` (Desa + Kecamatan)
- [x] `Bantuan` (Desa + Kecamatan)
- [x] `Bkl` (Desa + Kecamatan)
- [x] `Bkr` (Desa + Kecamatan)
- [x] `BukuKeuangan` (Desa + Kecamatan)
- [ ] `CatatanKeluarga` (Desa + Kecamatan)
- [ ] `DataIndustriRumahTangga` (Desa + Kecamatan)
- [ ] `DataKegiatanWarga` (Desa + Kecamatan)
- [ ] `DataKeluarga` (Desa + Kecamatan)
- [ ] `DataPelatihanKader` (Desa + Kecamatan)
- [ ] `DataPemanfaatanTanahPekaranganHatinyaPkk` (Desa + Kecamatan)
- [ ] `DataWarga` (Desa + Kecamatan)
- [ ] `Inventaris` (Desa + Kecamatan)
- [ ] `KaderKhusus` (Desa + Kecamatan)
- [ ] `KejarPaket` (Desa + Kecamatan)
- [ ] `Koperasi` (Desa + Kecamatan)
- [ ] `Paar` (Desa + Kecamatan)
- [ ] `Posyandu` (Desa + Kecamatan)
- [ ] `PrestasiLomba` (Desa + Kecamatan)
- [ ] `ProgramPrioritas` (Desa + Kecamatan)
- [ ] `SimulasiPenyuluhan` (Desa + Kecamatan)
- [ ] `TamanBacaan` (Desa + Kecamatan)
- [ ] `WarungPkk` (Desa + Kecamatan)
- [ ] `LaporanTahunanPkk` (Shared page lintas scope)
- [ ] `PilotProjectKeluargaSehat` (Shared page lintas scope)
- [ ] `PilotProjectNaskahPelaporan` (Shared page lintas scope)

## Konteks

- Saat ini pagination belum menjadi kontrak UI yang seragam lintas modul list.
- Query pagination yang tidak konsisten berisiko menyebabkan drift behavior (reset filter, halaman lompat, atau daftar terlalu panjang).
- Arsitektur wajib tetap mengikuti boundary `Controller -> UseCase/Action -> Repository -> Model` dan otorisasi backend (`Policy -> Scope Service`).
- Frontend tidak boleh menjadi authority akses; pagination hanya representasi data yang sudah scoped di backend.

## Target Hasil

- Tersedia kontrak pagination seragam untuk halaman list berbasis Inertia.
- UI memiliki komponen pagination reusable yang konsisten secara visual dengan dashboard minimalis terbaru.
- Filter aktif tetap tersimpan saat pindah halaman (deep-link aman via query string).
- Ada validasi E2E (backend + UI smoke) untuk mencegah behavior drift dan data leak antar area/scope.

## Kontrak Teknis Pagination (Locked)

- [x] Parameter query canonical: `page`, `per_page` (opsional, dinormalisasi backend).
- [x] Nilai `per_page` hanya dari whitelist (mis. `10, 25, 50`) dengan fallback default aman.
- [x] Semua query list tetap melalui repository boundary (tanpa query domain baru di controller/view).
- [x] Otorisasi + scope dieksekusi sebelum pagination; tidak ada bypass di UI.
- [x] Metadata pagination dikirim terstruktur ke Inertia (`data`, `current_page`, `last_page`, `per_page`, `total`, `links`).
- [x] URL state wajib mempertahankan filter aktif saat navigasi halaman.

## Langkah Eksekusi (Checklist)

- [x] `P1` Inventory modul list prioritas yang akan dipaginasi (desa + kecamatan), lalu petakan controller/use case/repository yang terdampak.
- [x] `P2` Tetapkan kontrak request pagination (normalisasi `page/per_page`, whitelist, fallback) di request layer.
- [x] `P3` Implementasi concern backend per modul:
  - repository gunakan `paginate(...)` + `withQueryString()` bila relevan,
  - use case mengembalikan payload pagination terstruktur,
  - controller tetap tipis (mapping Inertia props saja).
- [x] `P4` Implementasi concern UI:
  - buat komponen pagination reusable,
  - integrasikan ke halaman list target,
  - pastikan state filter tidak hilang saat klik next/prev/nomor halaman.
- [x] `P5` Tambahkan copywriting pass untuk label pagination agar natural user (bukan istilah teknis internal).
- [x] `P6` Tambahkan regression guard UI agar error runtime JavaScript pada event pagination tidak memutus alur utama.
- [x] `P7` Tambahkan test coverage E2E concern pagination:
  - jalur sukses role/scope valid,
  - tolak akses role tidak valid,
  - tolak mismatch role-area level,
  - anti data leak antar area pada halaman berbeda.
- [x] `P8` Doc-hardening pass: sinkronkan status TODO concern dashboard/UI terkait jika ada overlap kontrak query/filter.

## Rencana Commit By Concern

- [x] `C1` Contract & request normalization pagination.
- [x] `C2` Repository/use case/controller pagination integration.
- [x] `C3` UI pagination reusable + integrasi halaman.
- [x] `C4` Test E2E pagination (feature/unit) + hardening anti leak.
- [x] `C5` Doc-hardening + copywriting hardening + sinkron status TODO.

## Validasi Wajib

- [x] `php artisan test` (minimal targeted test concern pagination + policy/scope terkait).
- [x] `php artisan test` penuh setelah semua concern selesai.
- [x] `npm run build`.
- [ ] Smoke test manual:
  - pindah halaman tetap mempertahankan filter,
  - back/forward browser tetap konsisten,
  - empty-state halaman terakhir tetap benar,
  - role lain tidak dapat mengakses data di luar scope.

## Risiko

- Risiko regresi query filter existing saat parameter pagination ditambahkan.
- Risiko inkonsistensi payload pagination antar modul lama dan modul baru.
- Risiko performa jika sorting/filter tidak ditopang index saat data membesar.

## Mitigasi

- Terapkan bertahap per concern dan jalankan test targeted setiap concern selesai.
- Gunakan satu pola payload pagination lintas modul (hindari variasi key).
- Tambahkan benchmark sederhana pada query list yang paling berat bila ditemukan bottleneck.

## Keputusan

- [x] Implementasi pagination dilakukan bertahap `by concern`, bukan big-bang rewrite.
- [x] Kontrak akses backend tetap prioritas; UI hanya consumer.
- [x] Jika ditemukan konflik UX vs kontrak domain, kontrak domain canonical tetap menang.
