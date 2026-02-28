# AI Single Path Architecture (Zero Ambiguity)

Tanggal efektif: 2026-02-23  
Status: `active`  
Audience: AI agent eksekusi teknis pada repository ini.

## 1. Tujuan

Dokumen ini menetapkan jalur tunggal eksekusi AI agar:
- keputusan kerja deterministik,
- file target jelas,
- validasi tidak ambigu,
- dan hasil lintas sesi konsisten.

Dokumen ini tidak menggantikan prioritas dokumen pada `AGENTS.md`, tetapi menjadi rute operasional default yang wajib diikuti.

## 2. Prioritas Sumber Kebenaran

Jika ada konflik, gunakan urutan ini:
1. `AGENTS.md`
2. `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md` (dokumen ini)
3. `PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md`
4. `docs/adr/ADR_*.md`
5. dokumen domain/proses lain di `docs/`
6. `README.md`

Aturan anti-ambiguity:
- Jika instruksi user bertentangan dengan invariants `AGENTS.md`, tolak jalur yang melanggar invariant.
- Jika dokumen internal berbeda istilah dengan pedoman utama, istilah domain mengikuti pedoman utama.
- Jika status dokumen berbeda dengan implementasi aktual, status dokumen wajib diperbarui sebelum final report (`doc-hardening pass`).
- Jika ada referensi ganda pada concern yang sama, gunakan referensi terakhir dari user sebagai acuan final dan tandai referensi sebelumnya sebagai `superseded`.
- Untuk TODO baru, gunakan kode unik singkat setelah kata `TODO` agar targeting spesifik tidak ambigu.
- Untuk ADR baru, gunakan nomor 4 digit (`ADR_0001`, dst) dan status eksplisit (`proposed/accepted/superseded/deprecated`).
- Untuk ambiguity lintas TODO concern yang sama, resolver wajib memakai registry:
  - `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`
- Jika concern memiliki dampak arsitektur, resolver wajib memastikan TODO concern menunjuk ADR yang aktif.

## 3. Jalur Tunggal Eksekusi (Mandatory)

1. `Classify`
- Klasifikasikan task user ke satu concern utama (lihat Section 4).
- Jika task multi-concern, pecah per concern dan eksekusi berurutan.

2. `Contract Lock`
- Kunci kontrak concern: target, scope role, boundary data, acceptance criteria.
- Tetapkan file target sebelum patch.
- Jika menyentuh arsitektur, kunci juga `TODO concern + ADR` sebagai pasangan dokumen keputusan/eksekusi.

3. `Scoped Read`
- Baca hanya file concern + dependensi langsung.
- Dilarang scan massal tanpa alasan teknis.

4. `Minimal Patch`
- Patch sekecil mungkin di boundary arsitektur:
  - `Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`
  - `Policy -> Scope Service`

5. `Validation Ladder`
- L1: lint/syntax/test targeted concern.
- L2: regression test concern terkait.
- L3: `php artisan test` full untuk perubahan signifikan.

6. `Doc-Hardening`
- Wajib saat trigger canonical aktif (akses, scope, dashboard representation, query key, metadata sumber, atau lintas dokumen concern).
- Saat membuat TODO baru, pastikan format judul mengikuti `TODO <KODE_UNIK> ...` sesuai `AGENTS.md`.

7. `ADR Sync`
- Wajib saat concern menyentuh keputusan arsitektur lintas concern.
- Pastikan status ADR sinkron dengan status TODO concern.

8. `Report`
- Laporkan: apa diubah, kenapa, file terdampak, hasil validasi, risiko residual.

## 4. Task Router (Deterministik)

| Jenis Permintaan | Concern Canonical | File Primer | Validasi Minimum |
| --- | --- | --- | --- |
| Akses role/scope/menu | Authorization & visibility | `app/Support/RoleScopeMatrix.php`, `app/Domains/Wilayah/Services/*Scope*`, `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`, `app/Http/Middleware/EnsureModuleVisibility.php` | Feature auth/policy terkait + full test jika lintas modul |
| Domain CRUD baru | Domain module delivery | `routes/web.php`, `app/Http/Requests/*`, `app/Domains/Wilayah/*`, `resources/js/Pages/*` | Matrix test minimal AGENTS section 8 |
| Dashboard agregat/chart/filter | Dashboard representation | `app/Http/Controllers/DashboardController.php`, `app/Domains/Wilayah/Dashboard/*`, `resources/js/Pages/Dashboard.vue` | `DashboardDocumentCoverageTest` + test dashboard relevan |
| Seeder/migrasi legacy | Pre-release upgrade track | `database/migrations/*`, `database/seeders/*` | `migrate:fresh --seed` + regression impacted area |
| Dokumen pedoman autentik | Contract sync doc | `docs/domain/*_MAPPING.md`, `docs/process/TODO_AUTENTIK_*` | Validasi header tabel sampai merge cell (`rowspan/colspan`) |
| Normalisasi label/copy UI | Copywriting hardening | `resources/js/**/*`, `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md` | Smoke UI + test feature terdampak |
| Audit/risk assessment | Arsitektur & risiko | `docs/process/*RISK*`, `docs/security/*` | Evidence command + keputusan mitigasi |
| Keputusan arsitektur lintas concern | ADR governance | `docs/adr/ADR_*.md`, `docs/process/TODO_*` | ADR terhubung ke TODO concern + validasi concern terdampak |

Jika permintaan tidak cocok tabel:
- map ke concern paling dekat,
- tulis asumsi eksplisit,
- lanjutkan dengan scoped read.

## 5. Decision Gates (No-Assumption Rules)

Gate A - Scope & Area:
- Semua data wilayah harus punya `level`, `area_id`, `created_by`.
- `area_id` user harus konsisten dengan `scope` dan `areas.level`.

Gate B - Authorization:
- Frontend bukan authority akses.
- Semua enforcement akses harus bisa dibuktikan dari backend (policy/middleware/scope service).

Gate C - Repository Boundary:
- Query domain baru hanya melalui repository boundary.
- Dilarang menambah query domain ad-hoc di controller/view/helper.

Gate D - Legacy Control:
- `areas` adalah satu-satunya source of truth wilayah.
- Tidak boleh menambah coupling baru ke artefak legacy non-canonical.

Gate E - Documentation Sync:
- Perubahan canonical tanpa update dokumen terkait = `belum selesai`.

## 6. Validation Matrix (Single Path)

Urutan validasi:
1. Syntax/lint file yang diubah.
2. Test targeted concern.
3. Regression concern terdekat.
4. Full suite (`php artisan test`) bila:
   - perubahan lintas domain,
   - perubahan akses/policy/scope,
   - perubahan dashboard agregat,
   - perubahan migrasi/seeder canonical.

Kriteria selesai:
- Semua gate concern terpenuhi.
- Tidak ada drift `role` vs `scope` vs `areas.level`.
- Dokumen concern sinkron.

## 7. Commit by Concern

Aturan commit:
- Satu commit = satu concern logis.
- Pesan commit format: `type(scope): intent`.
- Jika concern besar: pecah menjadi beberapa commit yang independen dan bisa rollback granular.

Contoh concern:
- `refactor(seeder): migrate legacy role assignments to sekretaris`
- `feat(dashboard): add section3 filter context isolation`
- `docs(process): harden single path routing contract`

## 8. Trigger Wajib Hardening

Jalankan hardening bila salah satu terjadi:
- perubahan kontrak query key/filter,
- perubahan role/scope/matrix akses,
- perubahan section dashboard berbasis hak akses,
- perubahan istilah user-facing lintas komponen,
- perubahan status implementasi tanpa update TODO/process terkait,
- perubahan keputusan arsitektur tanpa sinkronisasi ADR.

Output hardening minimum:
- daftar file terdampak,
- keputusan yang dikunci,
- validasi yang dijalankan.

## 9. Residual Risk & Mitigation

Risiko:
- Dokumen jalur tunggal bisa usang saat arsitektur berevolusi.
- AI terlalu kaku pada kasus edge yang butuh eksplorasi non-standar.

Mitigasi:
- Setiap perubahan pola eksekusi wajib update playbook + dokumen ini dalam sesi yang sama.
- Jika edge case butuh deviasi, deviasi harus tertulis eksplisit di TODO concern dan log validasi.

## 10. Referensi Operasional

- `AGENTS.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
- `docs/adr/README.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/security/AUTH_COHERENCE_MATRIX.md`
- `docs/process/OPERATIONAL_VALIDATION_LOG.md`
