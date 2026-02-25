# TODO UI Pagination E2E 2026-02-24

Tanggal: 2026-02-24  
Status: `planned`

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

- [ ] Parameter query canonical: `page`, `per_page` (opsional, dinormalisasi backend).
- [ ] Nilai `per_page` hanya dari whitelist (mis. `10, 25, 50`) dengan fallback default aman.
- [ ] Semua query list tetap melalui repository boundary (tanpa query domain baru di controller/view).
- [ ] Otorisasi + scope dieksekusi sebelum pagination; tidak ada bypass di UI.
- [ ] Metadata pagination dikirim terstruktur ke Inertia (`data`, `current_page`, `last_page`, `per_page`, `total`, `links`).
- [ ] URL state wajib mempertahankan filter aktif saat navigasi halaman.

## Langkah Eksekusi (Checklist)

- [ ] `P1` Inventory modul list prioritas yang akan dipaginasi (desa + kecamatan), lalu petakan controller/use case/repository yang terdampak.
- [ ] `P2` Tetapkan kontrak request pagination (normalisasi `page/per_page`, whitelist, fallback) di request layer.
- [ ] `P3` Implementasi concern backend per modul:
  - repository gunakan `paginate(...)` + `withQueryString()` bila relevan,
  - use case mengembalikan payload pagination terstruktur,
  - controller tetap tipis (mapping Inertia props saja).
- [ ] `P4` Implementasi concern UI:
  - buat komponen pagination reusable,
  - integrasikan ke halaman list target,
  - pastikan state filter tidak hilang saat klik next/prev/nomor halaman.
- [ ] `P5` Tambahkan copywriting pass untuk label pagination agar natural user (bukan istilah teknis internal).
- [ ] `P6` Tambahkan regression guard UI agar error runtime JavaScript pada event pagination tidak memutus alur utama.
- [ ] `P7` Tambahkan test coverage E2E concern pagination:
  - jalur sukses role/scope valid,
  - tolak akses role tidak valid,
  - tolak mismatch role-area level,
  - anti data leak antar area pada halaman berbeda.
- [ ] `P8` Doc-hardening pass: sinkronkan status TODO concern dashboard/UI terkait jika ada overlap kontrak query/filter.

## Rencana Commit By Concern

- [ ] `C1` Contract & request normalization pagination.
- [ ] `C2` Repository/use case/controller pagination integration.
- [ ] `C3` UI pagination reusable + integrasi halaman.
- [ ] `C4` Test E2E pagination (feature/unit) + hardening anti leak.
- [ ] `C5` Doc-hardening + copywriting hardening + sinkron status TODO.

## Validasi Wajib

- [ ] `php artisan test` (minimal targeted test concern pagination + policy/scope terkait).
- [ ] `php artisan test` penuh setelah semua concern selesai.
- [ ] `npm run build`.
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

- [ ] Implementasi pagination dilakukan bertahap `by concern`, bukan big-bang rewrite.
- [ ] Kontrak akses backend tetap prioritas; UI hanya consumer.
- [ ] Jika ditemukan konflik UX vs kontrak domain, kontrak domain canonical tetap menang.
