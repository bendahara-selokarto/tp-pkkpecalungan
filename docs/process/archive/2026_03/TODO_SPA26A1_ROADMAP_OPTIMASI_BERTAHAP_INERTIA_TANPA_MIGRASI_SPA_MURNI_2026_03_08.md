# TODO SPA26A1 Roadmap Optimasi Bertahap Inertia Tanpa Migrasi SPA Murni

Tanggal: 2026-03-08  
Status: `done` (`state:wave1-wave5-pilots-validated`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Baseline aplikasi saat ini sudah berbentuk app-like melalui `Laravel + Inertia + Vue`, bukan multi-page app klasik.
- Analisis arsitektur 2026-03-08 mengonfirmasi bahwa migrasi ke SPA murni akan memindahkan beban kerja besar ke layer transport/presentasi:
  - `268` titik `Inertia::render` di backend,
  - `212` titik coupling frontend ke `@inertiajs/vue3` (`useForm`, `router.get`, dan pola navigasi sejenis),
  - `188` feature test masih mengunci kontrak `assertInertia(...)`.
- Tidak ada `routes/api.php` aktif dan guard auth saat ini masih berbasis session `web`, sehingga migrasi SPA murni akan menambah pekerjaan auth/API yang tidak memberi ROI cepat untuk kebutuhan sekarang.
- Keputusan kerja yang dikunci: pertahankan Inertia sebagai transport utama, lalu optimasi bertahap pada area yang memang butuh interaksi lebih reaktif.

## Kontrak Concern (Lock)

- Domain: hardening UX/runtime delivery berbasis Inertia tanpa mengganti arsitektur canonical backend.
- Role/scope target: lintas role (`super-admin`, `desa`, `kecamatan`) tanpa mengubah matriks `role/scope/area`.
- Boundary data:
  - bootstrap payload Inertia (`auth`, `flash`, dashboard context, filter state),
  - endpoint halaman Inertia yang saat ini memuat payload berat,
  - endpoint JSON kecil untuk widget/komponen yang memang sangat interaktif,
  - state lokal frontend yang saat ini bergantung pada full page visit Inertia.
- Acceptance criteria:
  - jalur optimasi dibagi bertahap dan bisa dieksekusi per concern kecil,
  - tidak ada rencana yang mendorong drift ke SPA murni tanpa justifikasi baru,
  - backend authorization (`Policy`, `Scope Service`, middleware `scope.role`, `module.visibility`) tetap menjadi authority,
  - concern prioritas awal untuk partial reload/lazy fetch terpetakan jelas.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [x] T1. Tersusun roadmap bertahap untuk mengurangi reload/payload berlebih tanpa mengganti stack Inertia.
- [x] T2. Ada prioritas concern awal dengan ROI tertinggi untuk partial reload, lazy data fetch, komponen stateful, dan endpoint JSON kecil.
- [x] T3. Ada validation ladder yang menjaga agar optimasi tidak melemahkan auth, scope, dan kontrak domain canonical.

## Langkah Eksekusi

- [ ] L0. Baseline dan pemetaan hotspot Inertia.
  - hitung payload/page yang paling berat,
  - petakan halaman yang paling sering melakukan refresh query penuh,
  - identifikasi komponen yang cukup menjadi stateful local component tanpa perlu kunjungan Inertia baru.
- [x] L1. Wave 1: partial reload untuk halaman list/filter.
  - prioritas pada dashboard agregat dan halaman index yang hanya mengubah filter/paginasi,
  - gunakan partial reload/prop refresh agar payload yang dikirim ulang hanya blok data yang berubah.
  - child concern pilot dashboard: `docs/process/archive/2026_03/TODO_DWI26A1_PILOT_DASHBOARD_WAVE_1_PARTIAL_RELOAD_DAN_PAYLOAD_SLIMMING_2026_03_08.md`.
  - child concern pilot user management index: `docs/process/archive/2026_03/TODO_USR26A1_PILOT_USER_MANAGEMENT_INDEX_PARTIAL_RELOAD_DAN_PAYLOAD_SLIMMING_2026_03_08.md`.
- [x] L2. Wave 2: lazy data fetch untuk blok sekunder.
  - pindahkan blok non-kritis di dashboard atau halaman detail yang tidak perlu hadir pada first paint,
  - gunakan endpoint kecil berbasis auth session yang tetap lewat boundary controller/use case/repository.
  - child concern pilot dashboard deferred blocks: `docs/process/archive/2026_03/TODO_DBL26A1_PILOT_DASHBOARD_WAVE_2_DEFERRED_BLOCKS_DAN_LAZY_FETCH_2026_03_08.md`.
- [x] L3. Wave 3: komponen stateful lokal.
  - pertahankan state UI seperti tab, collapse, draft filter, modal, dan helper panel di client,
  - kurangi visit Inertia hanya untuk perubahan state presentasional.
  - child concern pilot dashboard presentational state: `docs/process/archive/2026_03/TODO_DBS26A1_PILOT_DASHBOARD_WAVE_3_STATEFUL_PRESENTATIONAL_UI_2026_03_08.md`.
- [x] L4. Wave 4: endpoint JSON kecil untuk widget sangat interaktif.
  - batasi pada use case yang memang buruk jika tetap full prop refresh,
  - gunakan kontrak response tipis, bukan API generik lintas aplikasi,
  - larang bypass policy/repository boundary.
  - child concern pilot dashboard per-desa detail widget: `docs/process/archive/2026_03/TODO_DBJ26A1_PILOT_DASHBOARD_WAVE_4_JSON_DETAIL_WIDGET_PER_DESA_2026_03_08.md`.
- [x] L5. Hardening observability dan regresi.
  - catat metrik sebelum/sesudah pada concern pilot,
  - pastikan telemetry runtime error dan smoke test tetap relevan setelah pola fetch baru ditambah.
  - child concern pilot dashboard fetch failure telemetry: `docs/process/archive/2026_03/TODO_DBT26A1_PILOT_DASHBOARD_WAVE_5_FETCH_FAILURE_TELEMETRY_2026_03_09.md`.
- [x] L6. Sinkronisasi dokumen concern terkait.
  - update registry concern aktif,
  - update log validasi operasional saat concern ini bergerak dari `planned` ke batch implementasi.

## Validasi

- [x] V1. `Doc-only`: audit scoped file arsitektur/frontend/backend yang menjadi dasar roadmap tetap konsisten dengan isi TODO.
- [x] V2. Saat wave implementasi dimulai: targeted feature test untuk halaman concern yang dioptimasi.
- [x] V3. Saat ada endpoint JSON baru: test anti data leak + auth/authorization regression pada boundary concern.
- [x] V4. Jika perubahan lintas dashboard/auth/shared payload signifikan: `php artisan test --compact` dan smoke UI concern terkait.

## Risiko

- Risiko 1: partial reload diterapkan terlalu agresif dan membuat sinkronisasi filter/context `tahun_anggaran` atau scope user menjadi tidak konsisten.
- Risiko 2: endpoint JSON kecil berkembang liar menjadi pseudo-API baru di luar boundary concern.
- Risiko 3: optimasi frontend menutupi masalah payload/backend query yang seharusnya dibenahi di use case atau repository.

## Keputusan

- [x] K1: stack utama tetap `Laravel + Inertia + Vue`; tidak ada program migrasi ke SPA murni pada concern ini.
- [x] K2: partial reload menjadi jalur optimasi pertama sebelum menambah endpoint JSON baru.
- [x] K3: endpoint JSON hanya dibenarkan untuk widget/blok sangat interaktif dengan kontrak kecil dan auth backend tetap ketat.
- [x] K4: concern pilot prioritas awal adalah `Dashboard` dan satu modul list/filter yang sering berubah query, bukan seluruh modul sekaligus.

## Keputusan Arsitektur (Jika Ada)

- [x] ADR baru belum diperlukan karena tidak ada perubahan boundary arsitektur utama; concern ini masih berupa roadmap optimasi dalam stack yang sudah diterima.
- [x] Jika nanti muncul keputusan strategis baru seperti penambahan lapisan API resmi lintas concern atau perubahan model auth frontend, concern ini wajib memicu ADR baru.

## Fallback Plan

- Jika wave optimasi tertentu menambah kompleksitas tanpa hasil terukur:
  - hentikan concern di batch terakhir yang stabil,
  - rollback batch teknis kecil yang bermasalah,
  - kembali ke pola Inertia standar untuk halaman tersebut sambil mempertahankan hasil wave sebelumnya.
- Jika endpoint JSON kecil mulai melebar ruang lingkupnya:
  - bekukan penambahan endpoint baru,
  - konsolidasikan kembali ke Inertia response sampai kontrak yang lebih formal disetujui.

## Output Final

- [x] O1. Ringkasan roadmap final per wave dan alasan prioritas.
- [x] O2. Daftar concern pilot yang dipilih dan file target implementasi awal.
- [x] O3. Hasil validasi scoped audit/test pada tiap wave + residual risk yang masih terbuka.
