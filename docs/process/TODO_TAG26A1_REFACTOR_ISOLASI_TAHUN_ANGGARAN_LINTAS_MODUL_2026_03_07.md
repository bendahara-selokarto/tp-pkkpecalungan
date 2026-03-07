# TODO TAG26A1 Refactor Isolasi Tahun Anggaran Lintas Modul

Tanggal: 2026-03-07  
Status: `in-progress` (`state:wave2-data-keluarga-slice-implemented`)
Related ADR: `docs/adr/ADR_0005_TAHUN_ANGGARAN_CONTEXT_ISOLATION.md`

## Aturan Pakai
- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks
- Frasa definisi yang dikunci untuk concern ini:
  - `Tahun anggaran adalah identitas isolasi data administrasi TP PKK per siklus tahunan, default 1 Januari-31 Desember, dan ditetapkan sebagai context kerja aktif user.`
- Konteks wajib yang sempat terlewat pada desain sebelumnya: administrasi TP PKK dikelompokkan berdasarkan `tahun anggaran`, dan data operasional harus terisolasi per tahun anggaran.
- Baseline implementasi saat ini masih dominan memakai isolasi `level + area_id` pada hampir seluruh repository concern, sementara `Profile` baru memuat identitas user (`name`, `email`) dan belum mengunci tahun anggaran aktif.
- Sebagian concern memang sudah memiliki field periode sendiri (`tahun_laporan`, `tahun_awal`, `tahun_akhir`, `year`), tetapi field tersebut masih bersifat domain-spesifik dan belum menjadi kontrak transversal isolasi data administrasi.
- Refactor ini harus besar tetapi tidak boleh mengubah concern yang sudah ada: boundary domain, nama modul, route, dan pola `Controller -> UseCase/Action -> Repository -> Model` tetap dipertahankan.
- Dokumen ini adalah jalur planning resmi sebelum implementasi bertahap runtime.

## Kontrak Concern (Lock)
- Domain: isolasi data administrasi TP PKK lintas modul berbasis `tahun_anggaran` sebagai context transversal baru.
- Role/scope target: seluruh role operasional `desa|kecamatan`, `super-admin` untuk audit/backoffice, dan flow `profile` sebagai titik set tahun anggaran aktif.
- Boundary data:
  - source of truth tahun aktif: backend profile context milik user yang sedang login.
  - persistence transversal: tabel data administrasi TP PKK yang saat ini hanya bergantung pada `level + area_id` akan ditambah `tahun_anggaran` secara bertahap.
  - query path: repository/read model concern yang termasuk data administrasi wajib mem-filter `tahun_anggaran` bersama `level + area_id`.
  - write path: action/use case menginjeksi `tahun_anggaran` dari backend context; frontend tidak boleh menjadi authority.
  - reporting/dashboard/export: seluruh agregasi yang merepresentasikan data operasional wajib sadar tahun anggaran aktif.
  - pengecualian: field periode domain-spesifik (`tahun_laporan`, `tahun_awal`, `tahun_akhir`, `year`, `semester`) tetap dipertahankan dan tidak otomatis menggantikan `tahun_anggaran`.
- Acceptance criteria:
  - semua data administrasi yang termasuk concern ini dapat dibaca dan ditulis terisolasi per `tahun_anggaran` tanpa memindahkan concern existing.
  - pergantian tahun aktif di `Profile` hanya mengganti context kerja, bukan memutasi data tahun lain.
  - repository boundary tetap jadi satu-satunya jalur query, dengan filter default `level + area_id + tahun_anggaran` pada concern yang relevan.
  - tidak ada drift `role` vs `scope` vs `areas.level`, dan tidak ada data leak lintas tahun anggaran.
  - ada strategi migrasi/backfill untuk data existing yang belum punya `tahun_anggaran`.
- Dampak keputusan arsitektur: `ya`

## Target Hasil
- [ ] Tersusun klasifikasi modul/tabel yang wajib memakai `tahun_anggaran`, yang opsional, dan yang tetap memakai periode domain-spesifik saja.
- [x] Tersusun desain context backend `tahun anggaran aktif` yang terhubung ke `Profile` tanpa menggeser authority akses ke frontend.
- [x] Tersusun strategi retrofit repository/action/request/test secara bertahap tanpa rewrite concern.
- [ ] Tersusun strategi migrasi data, rollout, dan fallback bila isolasi tahun memicu regresi lintas modul.

## Readiness Lock
- Status kesiapan implementasi: `ready for wave-1`.
- Tidak ada blocker konseptual terbuka untuk memulai implementasi.
- Wave pilot yang dikunci:
  - `Profile` sebagai entry point tahun anggaran aktif.
  - `AgendaSurat` sebagai concern pilot CRUD/list/report dengan pola repository sederhana.
- Concern yang sengaja tidak masuk wave pilot:
  - `Activities`, karena memiliki query lintas role + dual-scope kecamatan/desa monitoring yang lebih kompleks.
  - dashboard agregat, report lintas tabel, dan concern dengan unique constraint majemuk; ditunda ke wave setelah pilot stabil.
- Target file awal wave-1:
  - `app/Http/Controllers/ProfileController.php`
  - `app/Http/Requests/ProfileUpdateRequest.php`
  - `app/Models/User.php`
  - `resources/js/Pages/Profile/Edit.vue`
  - `app/Domains/Wilayah/AgendaSurat/DTOs/AgendaSuratData.php`
  - `app/Domains/Wilayah/AgendaSurat/Models/AgendaSurat.php`
  - `app/Domains/Wilayah/AgendaSurat/Repositories/AgendaSuratRepositoryInterface.php`
  - `app/Domains/Wilayah/AgendaSurat/Repositories/AgendaSuratRepository.php`
  - `app/Domains/Wilayah/AgendaSurat/UseCases/ListScopedAgendaSuratUseCase.php`
  - `app/Domains/Wilayah/AgendaSurat/Actions/CreateScopedAgendaSuratAction.php`
  - `app/Domains/Wilayah/AgendaSurat/Actions/UpdateAgendaSuratAction.php`
  - migration baru untuk `users` context tahun aktif + migration baru untuk `agenda_surats.tahun_anggaran`
- Definition of done wave-1:
  - user dapat set tahun anggaran aktif dari `Profile`,
  - create/list/update `AgendaSurat` sudah terisolasi per `tahun_anggaran`,
  - report `AgendaSurat` mengikuti tahun aktif,
  - targeted tests untuk `Profile` dan `AgendaSurat` hijau,
  - tidak ada regresi auth/scope existing.

## Langkah Eksekusi
- [x] Fase 0 - Contract lock dan klasifikasi concern.
  - [x] Tetapkan definisi canonical `tahun_anggaran` sebagai context transversal administrasi TP PKK.
  - [x] Daftarkan modul yang terdampak langsung:
    - modul CRUD/list/report dengan repository `paginateByLevelAndArea` / `getByLevelAndArea`,
    - dashboard agregat,
    - flow `Profile`,
    - export/report yang membawa ringkasan data tahunan.
  - [x] Tandai modul pengecualian yang tetap memakai periode domain-spesifik sebagai data utama, tetapi tetap perlu context tahun aktif untuk akses daftar/summary.
  - [x] Kunci wave pilot implementasi pertama: `Profile + AgendaSurat`.
- [ ] Fase 1 - Inventory schema dan klasifikasi retrofit.
  - [ ] Audit semua tabel persisten administrasi TP PKK dan kelompokkan:
    - `kelas A`: tabel wajib tambah `tahun_anggaran`,
    - `kelas B`: tabel sudah punya field tahun/periode tetapi tetap butuh `tahun_anggaran`,
    - `kelas C`: master/reference yang tidak perlu isolasi tahun.
  - [ ] Tentukan naming canonical kolom: `tahun_anggaran` (`unsignedSmallInteger`).
  - [ ] Tentukan strategi index/unique baru per tabel, misalnya memperluas unique lama dari `level + area_id + ...` menjadi `level + area_id + tahun_anggaran + ...`.
- [ ] Fase 2 - Active budget year context.
  - [x] Tetapkan penyimpanan tahun aktif user pada flow `Profile` tanpa membuat frontend menjadi authority.
    - keputusan implementasi awal: simpan pada tabel `users` sebagai context aktif per user.
  - [x] Rancang service transversal baru untuk resolve tahun aktif user, sejajar dengan `UserAreaContextService`.
    - nama kerja: `ActiveBudgetYearContextService`.
  - [x] Tentukan apakah tahun aktif perlu ikut ke session/cache/request shared props untuk UX, dengan backend tetap sebagai pengendali final.
    - keputusan implementasi awal: kirim ke Inertia shared props/read payload setelah backend resolve nilai aktif.
- [ ] Fase 3 - Retrofit boundary backend.
  - [x] Tambah kontrak `tahun_anggaran` pada DTO/model/factory concern yang relevan untuk wave-1 (`AgendaSurat` + `User` context).
  - [x] Retrofit repository interface dan implementasi agar default query selalu include filter tahun aktif pada concern pilot `AgendaSurat`.
  - [x] Retrofit action/use case create/update agar otomatis mengisi atau mengunci `tahun_anggaran` pada concern pilot `AgendaSurat`.
  - [x] Audit report/export/dashboard supaya agregasi dan cetak memakai dataset tahun aktif yang benar untuk concern pilot (`AgendaSurat` report + dashboard coverage yang sudah year-aware).
- [ ] Fase 4 - Migration/backfill/compatibility.
  - [x] Siapkan migration bertahap per wave, bukan big-bang tunggal (wave-1: `users.active_budget_year`, `agenda_surats.tahun_anggaran`).
  - [x] Definisikan default backfill untuk data existing development secara bertahap per wave concern yang sudah diimplementasikan:
    - jika sumber tahun eksplisit tersedia, gunakan sumber tersebut,
    - untuk concern dokumen secretary books (`AgendaSurat`, `BukuTamu`, `BukuDaftarHadir`, `BukuNotulenRapat`), backfill memakai tahun dari kolom tanggal dokumen masing-masing,
    - jika concern wave berikutnya belum punya sumber tahun yang cukup presisi, pakai tahun anggaran baseline yang dikunci eksplisit saat wave concern tersebut dieksekusi.
  - [ ] Siapkan aturan transisi untuk unique constraint, seed, dan fixture test.
- [ ] Fase 5 - Rollout validation waves.
  - [x] Wave 1 dikunci: `Profile` + `ActiveBudgetYearContextService` + `AgendaSurat`.
  - [ ] Wave 2: concern CRUD mayoritas yang pattern query-nya homogen (`BukuTamu`, `BukuDaftarHadir`, `BukuNotulenRapat`, `Inventaris`, `AnggotaTimPenggerak`, `KaderKhusus`, dll).
    - [x] Slice awal wave-2 terimplementasi: `BukuTamu`, `BukuDaftarHadir`, `BukuNotulenRapat`.
    - [x] Slice lanjutan wave-2 terimplementasi: `Inventaris`, `AnggotaTimPenggerak`, `KaderKhusus`.
    - [x] Slice pendidikan/usaha wave-2 terimplementasi: `Koperasi`, `WarungPkk`, `TamanBacaan`, `KejarPaket`.
    - [x] Slice layanan keluarga wave-2 terimplementasi: `BKL`, `BKR`, `Posyandu`, `DataPelatihanKader`.
    - [x] Slice administrasi operasional wave-2 terimplementasi: `Bantuan`, `PrestasiLomba`, `AnggotaPokja`, `BukuKeuangan`.
    - [x] Slice komunitas/penyuluhan wave-2 terimplementasi: `DataIndustriRumahTangga`, `DataPemanfaatanTanahPekaranganHatinyaPkk`, `Paar`, `SimulasiPenyuluhan`.
    - [x] Slice data keluarga wave-2 terimplementasi: `DataKeluarga`.
    - [ ] `DataWarga` dan `DataKegiatanWarga` ditahan sampai `CatatanKeluarga` ikut year-aware agar tidak terjadi data leak agregat lintas tahun.
    - [ ] Concern homogen wave-2 yang masih pending: concern sejenis lain di luar slice yang sudah terkunci.
  - [ ] Wave 3: concern yang punya periodisasi/constraint lebih kompleks (`LaporanTahunanPkk`, `PilotProjectKeluargaSehat`, `Activities`, monitoring kecamatan/desa, dashboard/report agregat).
  - [ ] Wave 4: hardening docs, seed, full suite, dan smoke regression lintas role/scope.
- [ ] Sinkronisasi dokumen concern terkait (trigger hardening aktif).
  - [x] TODO concern + ADR.
  - [x] registry concern aktif + log validasi.
  - [x] dokumen proses/domain canonical yang menyebut invariant data lintas modul.

## Validasi
- [x] L1: planning/doc audit scoped (sesi ini).
  - [x] Audit baseline `ProfileController`, `ProfileUpdateRequest`, `User`, `UserAreaContextService`.
  - [x] Audit pola repository `paginateByLevelAndArea/getByLevelAndArea` pada concern wilayah.
  - [x] Audit migration untuk mendeteksi tabel yang belum memiliki `tahun_anggaran`.
- [ ] L2: targeted regression saat implementasi.
  - [x] feature test flow `profile` untuk set/switch tahun anggaran aktif.
  - [x] unit/service test resolver tahun anggaran aktif.
  - [x] feature/repository tests `AgendaSurat` untuk anti data leak lintas tahun.
  - [x] print/report test `AgendaSurat` mengikuti tahun aktif.
  - [x] feature/policy/report tests `Inventaris`, `AnggotaTimPenggerak`, dan `KaderKhusus` untuk anti data leak lintas tahun.
  - [x] feature/policy/report tests `Koperasi`, `WarungPkk`, `TamanBacaan`, dan `KejarPaket` untuk anti data leak lintas tahun.
  - [x] feature/policy/report tests `BKL`, `BKR`, `Posyandu`, dan `DataPelatihanKader` untuk anti data leak lintas tahun.
  - [x] feature/policy/report tests `Bantuan`, `PrestasiLomba`, `AnggotaPokja`, dan `BukuKeuangan` untuk anti data leak lintas tahun.
- [x] L3: `php artisan test --compact` setelah rollout signifikan lintas concern.

### Matrix Implementasi Wave-1
- [x] Migration test:
  - [x] `users.active_budget_year` canonical tersedia dan tervalidasi.
  - [x] `agenda_surats.tahun_anggaran` tersedia + index/query path aman.
- [x] Feature test:
  - [x] user valid bisa menyimpan tahun aktif dari `Profile`.
  - [x] daftar `AgendaSurat` hanya menampilkan data tahun aktif.
  - [x] create `AgendaSurat` otomatis menyimpan `tahun_anggaran` aktif.
  - [x] update `AgendaSurat` tidak memindahkan record ke tahun lain secara diam-diam.
  - [x] print/report `AgendaSurat` tidak bocor lintas tahun.
- [x] Regression auth:
  - [x] `scope.role` dan creator filter existing tetap berjalan.
  - [x] role kecamatan sekretaris tetap hanya melihat data yang sesuai scope + creator rule + tahun aktif.

## Risiko
- Risiko 1: data leak lintas tahun anggaran karena ada repository atau report yang masih hanya mem-filter `level + area_id`.
  - Mitigasi: retrofit repository interface terlebih dahulu, lalu audit seluruh pemanggilnya.
- Risiko 2: unique constraint lama menjadi tidak valid setelah data multi-tahun masuk.
  - Mitigasi: klasifikasikan constraint per tabel sebelum migration wave dimulai.
- Risiko 3: user salah persepsi saat pindah tahun aktif dan mengira data hilang.
  - Mitigasi: copy UI natural pada `Profile` dan indicator tahun aktif pada layout concern terdampak.
- Risiko 4: concern yang sudah punya field periode domain-spesifik mengalami tumpang tindih makna dengan `tahun_anggaran`.
  - Mitigasi: kunci definisi bahwa `tahun_anggaran` adalah context isolasi administratif, bukan pengganti semua field periode domain.
- Risiko 5: patch besar lintas modul sulit dibatalkan jika dieksekusi sekaligus.
  - Mitigasi: rollout per wave + concern pilot + fallback migration terpisah.

## Keputusan
- [x] K1: `tahun_anggaran` diperlakukan sebagai context transversal baru, bukan alasan untuk membentuk ulang domain concern.
- [x] K2: `Profile` menjadi entry point set tahun aktif, tetapi authority final tetap backend service.
- [x] K3: retrofit dilakukan per wave berbasis repository pattern yang sudah ada, bukan rewrite besar per modul.
- [x] K4: field periode domain-spesifik tetap hidup dan dipetakan eksplisit terhadap `tahun_anggaran`.
- [x] K5: dashboard/report ikut dalam scope mandatory, bukan follow-up opsional.
- [x] K6: wave implementasi pertama memakai `AgendaSurat`, bukan `Activities`, untuk menekan risiko regresi pada concern dengan query lintas role yang lebih kompleks.
- [x] K7: storage tahun aktif user pada wave-1 dikunci di backend persistence user, bukan session-only, agar context kerja konsisten lintas request.

## Keputusan Arsitektur (Jika Ada)
- [x] Tautkan ADR di `docs/adr/ADR_0005_TAHUN_ANGGARAN_CONTEXT_ISOLATION.md`.
- [x] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern saat implementasi dimulai/diterima.

## Fallback Plan
- Jalur fallback 1: hentikan rollout setelah wave concern pilot jika test anti data leak belum stabil.
- Jalur fallback 2: pertahankan kolom/perilaku lama sementara dengan adapter repository per concern sampai migration wave berikutnya siap.
- Jalur fallback 3: untuk data development pre-release, lakukan `migrate:fresh` hanya bila klasifikasi dan backfill manual sudah terkunci; laporkan reset data secara eksplisit.
- Jalur fallback 4: jika flow `Profile` belum siap, backend dapat sementara memakai tahun anggaran default yang dikunci di config/service internal khusus wave pilot, tanpa membuka input manual di tiap modul.

## Output Final
- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak (schema/backend/frontend/test/docs) per wave.
- [ ] Hasil validasi + residual risk.
- [ ] Keputusan rollout: `pilot-ready` / `hold` / `needs-contract-clarification`.

## Status Readiness
- [x] Contract lock selesai.
- [x] ADR sinkron.
- [x] Wave-1 target file sudah dikunci.
- [x] Modul pilot sudah dipilih.
- [x] Matrix validasi wave-1 sudah ditulis.
- [x] TODO siap dipakai implementasi.

## Hasil Implementasi Wave-1
- [x] `Profile` kini menyimpan `active_budget_year` sebagai context kerja aktif user.
- [x] Service backend `ActiveBudgetYearContextService` aktif untuk runtime/shared props.
- [x] `AgendaSurat` kini menyimpan `tahun_anggaran` dan seluruh read/write path pilot sudah terisolasi per tahun aktif.
- [x] PDF `AgendaSurat` dan `Ekspedisi Surat` menampilkan metadata tahun anggaran aktif.
- [x] Dashboard coverage untuk model yang sudah punya `tahun_anggaran` ikut sadar tahun aktif pada wave-1.

## Hasil Implementasi Wave-2 (Slice Buku Administrasi)
- [x] `BukuTamu` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `BukuDaftarHadir` kini menyimpan `tahun_anggaran`, mengunci read/write path per tahun aktif, dan opsi `Activity` di concern ini hanya menerima kegiatan pada tahun aktif.
- [x] `BukuNotulenRapat` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] PDF `BukuTamu`, `BukuDaftarHadir`, dan `BukuNotulenRapat` menampilkan metadata tahun anggaran aktif.
- [x] Targeted regression wave-2 slice lulus: `46 passed`.
- [x] Full suite setelah rollout slice wave-2 lulus: `1071 passed`.

## Hasil Implementasi Wave-2 (Slice CRUD Homogen Lanjutan)
- [x] `Inventaris` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `AnggotaTimPenggerak` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `KaderKhusus` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] PDF `Inventaris`, `AnggotaTimPenggerak`, dan `KaderKhusus` menampilkan metadata tahun anggaran aktif.
- [x] Backfill development untuk wave ini dikunci: `Inventaris` memakai tahun dari `tanggal_penerimaan` bila tersedia, sedangkan `AnggotaTimPenggerak` dan `KaderKhusus` memakai baseline eksplisit `2026` karena belum ada sumber tahun domain yang lebih presisi.
- [x] Targeted regression tambahan wave-2 slice lulus: `62 passed`.
- [x] Full suite setelah rollout slice lanjutan wave-2 lulus: `1088 passed`.

## Hasil Implementasi Wave-2 (Slice Pendidikan dan Usaha)
- [x] `Koperasi` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `WarungPkk` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `TamanBacaan` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `KejarPaket` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] PDF `Koperasi`, `WarungPkk`, `TamanBacaan`, dan `KejarPaket` menampilkan metadata tahun anggaran aktif.
- [x] Backfill development untuk wave ini dikunci: seluruh concern pada slice ini memakai baseline eksplisit `2026` karena belum ada sumber tahun domain yang lebih presisi.
- [x] Targeted regression tambahan wave-2 slice lulus: `68 passed`.
- [x] Full suite setelah rollout slice pendidikan/usaha lulus: `1100 passed`.

## Hasil Implementasi Wave-2 (Slice Layanan Keluarga)
- [x] `BKL` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `BKR` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `Posyandu` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `DataPelatihanKader` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] PDF concern layanan keluarga menampilkan metadata tahun anggaran aktif dengan fallback kompatibel untuk baseline fixture lama.
- [x] Full suite setelah rollout slice layanan keluarga lulus: `1112 passed`.

## Hasil Implementasi Wave-2 (Slice Administrasi Operasional)
- [x] `Bantuan` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `PrestasiLomba` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `AnggotaPokja` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `BukuKeuangan` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] PDF `Bantuan`, `PrestasiLomba`, `AnggotaPokja`, dan `BukuKeuangan` menampilkan metadata tahun anggaran aktif.
- [x] Backfill development untuk slice ini dikunci: `Bantuan` memakai tahun dari `received_date`, `PrestasiLomba` memakai `tahun`, `BukuKeuangan` memakai tahun dari `transaction_date`, dan `AnggotaPokja` memakai baseline eksplisit `2026`.
- [x] Targeted regression tambahan wave-2 slice lulus: `58 passed`.
- [x] Full suite setelah rollout slice administrasi operasional lulus: `1114 passed`.

## Hasil Implementasi Wave-2 (Slice Komunitas dan Penyuluhan)
- [x] `DataIndustriRumahTangga` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `DataPemanfaatanTanahPekaranganHatinyaPkk` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `Paar` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] `SimulasiPenyuluhan` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] PDF `DataIndustriRumahTangga`, `DataPemanfaatanTanahPekaranganHatinyaPkk`, `Paar`, dan `SimulasiPenyuluhan` menampilkan metadata tahun anggaran aktif.
- [x] Backfill development untuk slice ini dikunci: seluruh concern pada slice ini memakai baseline eksplisit `2026` karena belum ada sumber tahun domain yang lebih presisi.
- [x] Unique constraint `Paar` diperluas dari `level + area_id + indikator` menjadi `level + area_id + tahun_anggaran + indikator` agar indikator yang sama bisa hidup lintas tahun tanpa bentrok.
- [x] Targeted regression tambahan wave-2 slice lulus: `68 passed`.
- [x] Full suite setelah rollout slice komunitas/penyuluhan lulus: `1119 passed`.

## Hasil Implementasi Wave-2 (Slice Data Keluarga)
- [x] `DataKeluarga` kini menyimpan `tahun_anggaran` dan seluruh list/detail/create/update/report sudah terisolasi per tahun aktif.
- [x] PDF `DataKeluarga` menampilkan metadata tahun anggaran aktif.
- [x] Backfill development untuk slice ini dikunci: `DataKeluarga` memakai baseline eksplisit `2026` karena belum ada sumber tahun domain yang lebih presisi.
- [x] Targeted regression tambahan slice `DataKeluarga` lulus: `21 passed`.
- [x] Full suite setelah rollout slice `DataKeluarga` lulus: `1122 passed`.
- [x] Keputusan dependency lock: `DataWarga` dan `DataKegiatanWarga` tidak dilanjutkan pada patch yang sama karena `CatatanKeluarga` masih membaca keduanya langsung dan harus di-retrofit bersama pada wave berikutnya.
