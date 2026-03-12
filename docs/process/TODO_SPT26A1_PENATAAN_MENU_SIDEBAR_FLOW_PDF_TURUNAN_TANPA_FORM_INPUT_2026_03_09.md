# TODO SPT26A1 Penataan Menu Sidebar Flow PDF Turunan Tanpa Form Input

Tanggal: 2026-03-09  
Status: `in-progress` (`state:hub-catatan-keluarga-implemented`)
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Sidebar aktif di `resources/js/Layouts/DashboardLayout.vue` menempelkan semua item `report/pdf` langsung ke group domain asal.
- Pola ini sudah mencampur dua jenis item:
  - modul input utama yang memang punya halaman create/edit/store sendiri,
  - flow PDF turunan yang tidak punya form input mandiri dan hanya mengambil data dari modul lain.
- Hasil audit scoped pada route, controller, dan halaman index menunjukkan ada `21` item sidebar yang masuk kategori flow PDF turunan/report-only, plus `1` gap visibilitas (`4.23`) yang ada di halaman index `catatan-keluarga` tetapi belum muncul di sidebar.
- Concern ini khusus menyiapkan task penataan IA menu/sidebar agar flow report-only tidak lagi bercampur tanpa pola dengan modul input utama.

## Kontrak Concern (Lock)

- Domain: IA sidebar untuk flow PDF turunan/report-only pada scope `desa` dan `kecamatan`.
- Role/scope target: seluruh role operasional yang mengonsumsi sidebar domain (`sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, `pokja-iv`).
- Boundary data:
  - sumber menu aktif: `resources/js/Layouts/DashboardLayout.vue`,
  - baseline route: `routes/web.php`,
  - bukti source-data turunan: controller/use case/report page concern terkait.
- Acceptance criteria:
  - seluruh flow PDF tanpa form input mandiri sudah terkelompok per source modul,
  - gap sidebar vs halaman sumber (`4.23`) terdokumentasi eksplisit,
  - tersedia opsi penataan menu yang bisa diputuskan owner tanpa mengubah authority backend,
  - tidak ada rekomendasi yang menambah coupling baru atau memindahkan business logic ke frontend.
- Dampak keputusan arsitektur: `tidak` (planning IA menu, belum mengubah boundary akses/backend).

## Baseline Audit Flow Turunan

| Kelompok | Flow PDF | Evidence scoped | Sumber data utama | Catatan sidebar saat ini |
| --- | --- | --- | --- | --- |
| A | `dashboard/charts/report/pdf` | `app/Http/Controllers/DashboardController.php` `printChartPdf()` | agregasi `DashboardActivityChartService` + `BuildDashboardDocumentCoverageUseCase` | item utilitas dicampur ke group `sekretaris-tpk` |
| B | `agenda-surat/ekspedisi/report/pdf` | `app/Domains/Wilayah/AgendaSurat/Controllers/AgendaSuratReportPrintController.php` `streamEkspedisiReport()` | subset `AgendaSurat` dengan filter `jenis_surat = keluar` | tampil sebagai item sidebar sendiri, padahal hanya varian print dari modul `agenda-surat` |
| B | `anggota-tim-penggerak-kader/report/pdf` | `app/Domains/Wilayah/AnggotaTimPenggerak/Controllers/AnggotaTimPenggerakReportPrintController.php` dan `app/Domains/Wilayah/AnggotaTimPenggerak/UseCases/ListScopedAnggotaDanKaderUseCase.php` | gabungan `anggota-tim-penggerak` + `kader-khusus` | item sidebar sendiri, padahal tidak punya form input mandiri |
| C | `bantuans/keuangan/report/pdf` | `app/Domains/Wilayah/BukuKeuangan/Controllers/BukuKeuanganReportPrintController.php` | `buku-keuangan`, bukan data `bantuans` | label/penempatan berisiko membingungkan karena entry berada di konteks `bantuans` |
| D | `catatan-keluarga/report/pdf` + turunan `4.16a-4.24` | `app/Domains/Wilayah/CatatanKeluarga/Controllers/DesaCatatanKeluargaController.php`, `app/Domains/Wilayah/CatatanKeluarga/Controllers/CatatanKeluargaPrintController.php`, `app/Domains/Wilayah/CatatanKeluarga/Repositories/CatatanKeluargaRepository.php` | agregasi lintas `data-warga`, `data-kegiatan-warga`, `anggota_tim_penggerak`, `anggota_pokja`, `kader_khusus`, `posyandu`, `program_prioritas`, dan sumber turunan lain di repository | cluster report-only terbesar, saat ini tersebar sebagai banyak item datar dalam group `pokja-iv` |

## Inventaris Cluster Yang Perlu Ditata

### 1. Utilitas Dashboard

- `Laporan PDF Grafik Dashboard`
- Karakter: tidak punya form input, bergantung pada filter dashboard aktif, dan lebih cocok diposisikan sebagai utilitas/global report daripada item domain `sekretaris-tpk`.

### 2. Varian Print Dalam Modul Input Yang Sama

- `Laporan PDF Ekspedisi Agenda Surat`
- `Laporan PDF Anggota dan Kader Tim Penggerak PKK`
- Karakter: masih satu konteks kerja dengan modul input asal; secara IA lebih natural jika menjadi aksi sekunder di halaman sumber atau submenu ringkas, bukan entry setara modul baru.

### 3. Alias Print Lintas Modul

- `Laporan PDF Keuangan Bantuan`
- Karakter: entry muncul dari konteks `bantuans`, tetapi controller dan data yang dipakai berasal dari `buku-keuangan`.
- Risiko UX: user mengira ada formulir atau dataset keuangan khusus di modul bantuan, padahal source of truth-nya berbeda.

### 4. Hub Report Agregasi Lintas Modul

- `Catatan Keluarga | 4.15`
- `Kelompok Dasa Wisma | 4.16a`
- `Kelompok PKK RT | 4.16b`
- `Catatan PKK RW | 4.16c`
- `Kelompok PKK Dusun | 4.16d`
- `Catatan TP PKK Desa/Kelurahan | 4.17a`
- `Catatan TP PKK Kecamatan | 4.17b`
- `Catatan TP PKK Kabupaten/Kota | 4.17c`
- `Catatan TP PKK Provinsi | 4.17d`
- `Kelompok Dasawisma | 4.18a`
- `Kelompok PKK RT | 4.18b`
- `Kelompok PKK RW | 4.18c`
- `Kelompok PKK Dusun | 4.18d`
- `Kelompok PKK Kecamatan | 4.19b`
- `Data Umum PKK | 4.20a`
- `Data Umum PKK Kecamatan | 4.20b`
- `Kegiatan PKK Pokja IV | 4.24`
- Gap audit:
  - `Kegiatan PKK Pokja III | 4.23` tersedia pada halaman index `resources/js/Pages/Desa/CatatanKeluarga/Index.vue` dan `resources/js/Pages/Kecamatan/CatatanKeluarga/Index.vue`, tetapi belum ada di sidebar aktif.
- Karakter: seluruh cluster ini tidak memiliki form input mandiri; `catatan-keluarga` sendiri hanya punya route `index`, sementara PDF turunannya dibangun dari repository agregasi lintas modul.

## Target Hasil

- [ ] Tersusun daftar cluster flow PDF turunan yang dapat dipakai owner untuk memutuskan pola IA sidebar.
- [ ] Tersusun task implementasi lanjutan yang membedakan penataan utilitas, varian print, alias lintas-modul, dan hub report agregasi.
- [ ] Tersusun keputusan awal item mana yang sebaiknya tetap di halaman sumber, mana yang layak jadi submenu, dan mana yang perlu hub khusus.

## Langkah Eksekusi

- [x] Analisis scoped dependency + side effect.
- [ ] Kunci keputusan owner per cluster:
  - `Dashboard utility`,
  - `Varian print satu modul`,
  - `Alias lintas-modul`,
  - `Hub report agregasi`.
- [x] Tetapkan target IA:
  - opsi A: tetap di halaman sumber sebagai CTA sekunder,
  - opsi B: submenu `Laporan Turunan` di bawah modul asal,
  - opsi C: hub `Laporan Agregasi` terpusat untuk cluster `catatan-keluarga`.
- [ ] Definisikan aturan copywriting:
  - label sidebar harus menunjukkan sumber data asal,
  - item alias lintas-modul wajib menghindari label yang memberi kesan ada form input baru.
- [x] Rencanakan patch frontend terarah pada `resources/js/menus/printMenuRegistry.js` dan `resources/js/Pages/CetakLampiran/Index.vue`.
- [x] Implementasi hub `catatan-keluarga` pada `Cetak Lampiran` (print menu) untuk menutup cluster laporan agregasi.
- [ ] Sinkronkan concern parent/menu grouping bila owner sudah memilih pola IA final.

## Validasi

- [x] L1: validasi analisis scoped via audit file route/controller/page sumber.
- [ ] L2: saat implementasi, jalankan targeted test/frontend contract:
  - `php artisan test tests/Unit/Frontend/DashboardLayoutMenuContractTest.php --compact`
  - `npm run build`
- [ ] L3: jika penataan menu berdampak lintas concern/otorisasi, lanjutkan ke `php artisan test --compact`.
- [ ] L4: evidence runtime UI/UX untuk perubahan sidebar/menu tersedia dan ditautkan (smoke/a11y/visual/perf).

## Risiko

- Risiko 1: sidebar makin padat dan sulit dipindai jika seluruh report-only flow tetap diperlakukan setara dengan modul input utama.
- Risiko 2: label item lintas-modul seperti `Keuangan Bantuan` menimbulkan ekspektasi source data yang salah.
- Risiko 3: cluster `catatan-keluarga` terus melebar dan menciptakan drift antara halaman sumber dan sidebar jika tidak diperlakukan sebagai hub khusus.
- Risiko 4: memindahkan item tanpa rule cluster yang jelas berisiko memunculkan gap baru seperti kasus `4.23`.

## Keputusan

- [x] K1: concern ini dikunci sebagai planning-only; belum mengubah backend, route, atau authority akses.
- [x] K2: pengelompokan dilakukan berdasarkan source data dan keberadaan form input, bukan berdasarkan nama route PDF semata.
- [x] K3: owner memilih pola IA final untuk cluster `catatan-keluarga` -> opsi C (hub khusus).
- [ ] K4: gap `4.23` wajib diputuskan bersamaan dengan desain cluster `catatan-keluarga`, bukan ditambal terpisah tanpa arah IA.

Catatan keputusan:

- 2026-03-12: owner mengunci cluster `catatan-keluarga` sebagai hub khusus (opsi C).

## Keputusan Arsitektur (Jika Ada)

- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jika owner belum mengunci IA final, pertahankan sidebar existing dan gunakan concern ini hanya sebagai baseline diskusi.
- Jika implementasi nanti memicu regressi navigasi, rollback cukup pada layer UI/sidebar tanpa mengubah route/controller report yang sudah stabil.

## Output Final

- [ ] Ringkasan cluster flow PDF turunan yang akan ditata.
- [ ] Keputusan IA final per cluster dan daftar item yang dipindah/ditahan.
- [ ] Daftar file UI/docs yang terdampak saat implementasi dimulai.
- [ ] Hasil validasi frontend contract + residual risk.

## Progress Log

- 2026-03-12: hub `catatan-keluarga` diterapkan di `Cetak Lampiran` sebagai pusat laporan agregasi.
