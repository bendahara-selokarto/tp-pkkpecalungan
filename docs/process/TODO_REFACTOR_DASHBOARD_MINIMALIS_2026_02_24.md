# TODO Refactor Dashboard Minimalis 2026-02-24

Tanggal: 2026-02-24  
Status: `planned`

## Konteks

- Dashboard role-aware sudah berjalan dengan sumber utama `dashboardBlocks[]`, tetapi tampilan masih padat (teks metadata panjang, banyak panel sekaligus, dan fallback legacy masih ada).
- Preferensi user: dashboard minimalis, ringkas, fokus pada informasi inti.
- Acuan visual utama diset dari implementasi dashboard role `kecamatan-sekretaris` yang sudah diperbaiki user pada 24 Februari 2026.
- Rencana ini adalah fase lanjutan dari:
  - `docs/process/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`
  - `docs/process/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`
- Kontrak akses backend, scope, dan anti data leak tetap harus dipertahankan.

## Target Hasil

- Dashboard tampil lebih minimalis tanpa mengubah kontrak data backend.
- Informasi utama terlihat dalam sekali lihat: judul blok, KPI inti, chart inti, dan empty-state yang jelas.
- Metadata teknis (`sumber/cakupan/filter context`) dipadatkan agar tidak mendominasi area konten.
- Tidak ada regresi behavior dari penyesuaian dashboard terakhir.

## Kontrak UI Yang Harus Dipertahankan (Locked)

- [x] Header tetap minimal: aksi kanan `Mode`, `Profil`, `Keluar`; desktop sisi kiri header kosong.
- [x] Sidebar tidak menampilkan `Referensi` dan tidak menampilkan section `Akun`.
- [x] Dashboard tetap berbasis `dashboardBlocks[]` (frontend bukan authority akses).
- [x] Metrik status publikasi (`published/draft`) tidak ditampilkan pada dashboard.
- [x] Section 1 level kecamatan:
  - `Jumlah Kegiatan per Desa` = pie (nilai absolut, bukan persen),
  - `Jumlah Buku vs Buku Terisi` = bar,
  - filter bulan `section1_month` dengan opsi `all + 1..12`.
- [x] Warna chart buku dipertahankan ungu-hijau dan kontras tegas.
- [x] Guardrail runtime JS tetap aktif (`ui-runtime-error` + fallback banner).
- [x] Struktur dan hirarki visual dashboard `kecamatan-sekretaris` saat ini menjadi baseline refactor minimalis (acuan utama).

## Langkah Eksekusi (Checklist)

- [ ] `M1` Audit surface UI dashboard saat ini (spasi, hirarki visual, kepadatan teks) dan tandai elemen yang bisa dipadatkan.
- [ ] `M2` Refactor struktur komponen `Dashboard.vue` menjadi blok presentasi yang lebih kecil:
  - header blok ringkas,
  - panel filter ringkas,
  - empty-state konsisten.
- [ ] `M3` Ringkas copywriting dashboard:
  - ubah kalimat panjang menjadi label natural, pendek, dan konsisten.
  - pertahankan istilah domain canonical.
- [ ] `M4` Sederhanakan metadata tampilan:
  - tampilkan ringkasan sumber/cakupan dalam 1 baris.
  - detail filter context dipindahkan ke mode ringkas (mis. helper text kecil).
- [ ] `M5` Kurangi noise visual:
  - kurangi jumlah border/kotak yang tidak esensial,
  - standarkan tinggi chart dan jarak antar section,
  - hindari duplikasi informasi antar kartu.
- [ ] `M6` Transisi fallback legacy:
  - evaluasi penggunaan blok fallback legacy (`dashboardStats/dashboardCharts`),
  - jika aman, sembunyikan dari UI utama dan jadikan fallback internal-only.
- [ ] `M7` Hardening behavior:
  - pastikan perubahan minimalis tidak memecah filter URL (`mode`, `level`, `sub_level`, `section1_month`, `section2_group`, `section3_group`),
  - pastikan tidak ada behavior drift pada role sekretaris/pokja.
- [x] `M8` Doc-hardening pass:
  - sinkronkan status di TODO dashboard lama + matrix domain bila ada kontrak tampilan yang berubah.

## File Target (Rencana)

- `resources/js/Pages/Dashboard.vue`
- `app/Http/Controllers/DashboardController.php` (hanya jika mapping payload perlu dipadatkan)
- `app/Domains/Wilayah/Dashboard/UseCases/*` (hanya jika metadata perlu penyederhanaan terstruktur)
- `tests/Feature/DashboardDocumentCoverageTest.php`
- `tests/Feature/DashboardActivityChartTest.php`
- `docs/process/TODO_REFACTOR_DASHBOARD_AKSES_2026_02_23.md`
- `docs/process/TODO_UI_DASHBOARD_CHART_DINAMIS_AKSES_2026_02_23.md`

## Validasi Wajib

- [ ] `npm run build`
- [ ] `php artisan test --filter=DashboardDocumentCoverageTest`
- [ ] `php artisan test --filter=DashboardActivityChartTest`
- [ ] `php artisan test --filter=DashboardCoverageMenuSyncTest`
- [ ] Smoke test manual:
  - role `desa-sekretaris`,
  - role `kecamatan-sekretaris`,
  - role `desa-pokja-*`,
  - role `kecamatan-pokja-*`.

## Risiko

- [ ] Risiko informasi penting tersembunyi jika minimalisasi terlalu agresif.
- [ ] Risiko drift antara UI minimalis dan metadata audit yang dibutuhkan operasional.
- [ ] Risiko regresi query/filter URL pada section sekretaris.

## Mitigasi

- [ ] Terapkan minimalisasi bertahap per section, bukan rewrite total.
- [ ] Pertahankan metadata kritikal, ringkas hanya layer presentasi.
- [ ] Jalankan regression test dashboard setiap concern selesai.

## Keputusan

- [x] Fokus refactor ini hanya pada presentasi UI dashboard (minimalis), bukan perubahan kontrak akses.
- [x] Penyesuaian dashboard terakhir dijadikan baseline wajib (tidak boleh diregresikan).
- [x] Baseline utama diambil dari implementasi dashboard role `kecamatan-sekretaris` yang sudah disetujui user (24 Februari 2026).
- [x] Jika ada konflik preferensi visual vs kontrak data, kontrak data backend tetap prioritas.
