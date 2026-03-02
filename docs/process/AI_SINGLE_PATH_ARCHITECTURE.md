# AI Single Path Architecture (Zero Ambiguity + Self-Reflective Routing)

Tanggal efektif: 2026-02-23  
Status: `active`  
Audience: AI agent eksekusi teknis pada repository ini.

## 1. Tujuan

Dokumen ini menetapkan jalur tunggal eksekusi AI agar:
- keputusan deterministik,
- checkpoint refleksi terkontrol,
- file target dan validasi jelas,
- hasil lintas sesi konsisten.

Dokumen ini tidak menggantikan prioritas dokumen pada `AGENTS.md`, tetapi menjadi rute operasional default yang wajib diikuti.

## DSL Contract

```dsl
ROUTER_VERSION: 1
ROUTER_MODE: SINGLE_PATH_REFLECTIVE
MODEL_TIER_MAP: low=small, medium=mid, high=large
REFLECTIVE_LIMIT: max_route_correction=1
SYNC_REQUIRED: playbook,single-path,todo,adr
```

## 2. Prioritas Sumber Kebenaran

Jika ada konflik, gunakan urutan ini:
1. `AGENTS.md`
2. `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md` (dokumen ini)
3. `PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md`
4. `docs/adr/ADR_*.md`
5. dokumen domain/proses lain di `docs/`
6. `README.md`

Aturan anti-ambiguity:
- Prioritas tetap mengikuti `AGENTS.md`; konflik instruksi yang melanggar invariants harus ditolak.
- Istilah domain mengikuti pedoman utama; status dokumen harus sinkron dengan implementasi sebelum final report.
- Untuk concern ganda, acuan final adalah referensi user terakhir dan referensi lama ditandai `superseded`.
- Resolver ambiguity TODO wajib memakai registry SOT: `docs/process/TODO_TTM25R1_REGISTRY_SOURCE_OF_TRUTH_TODO_2026_02_25.md`.
- TODO baru wajib berkode unik; ADR baru wajib bernomor 4 digit + status eksplisit.

## 3. Jalur Tunggal Eksekusi (Mandatory)

1. `Classify`
- Klasifikasikan task ke satu concern utama (lihat Section 4); jika multi-concern, pecah berurutan.

2. `Self-Reflective Checkpoint`
- Sebelum patch besar, evaluasi ulang hasil `Classify` menggunakan bukti dari scoped read awal.
- Jika concern/file target/validation ladder tidak cocok, lakukan satu kali koreksi rute secara eksplisit.
- Tetapkan tier model: `low -> small`, `medium -> mid`, `high -> large`.
- Kunci hasil koreksi pada TODO concern (dan ADR jika concern strategis lintas concern).

3. `Contract Lock`
- Kunci target, scope, boundary data, acceptance criteria, dan file target.
- Jika menyentuh arsitektur, kunci pasangan `TODO concern + ADR`.

4. `Scoped Read`
- Baca hanya file concern + dependensi langsung.
- Dilarang scan massal tanpa alasan teknis.

5. `Minimal Patch`
- Patch sekecil mungkin pada boundary:
  - `Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`
  - `Policy -> Scope Service`

6. `Validation Ladder`
- L1: lint/syntax/test targeted concern.
- L2: regression test concern terkait.
- L3: `php artisan test` full untuk perubahan signifikan.
- Fast-lane `doc-only`: jika perubahan hanya `docs/**`, cukup L1 audit scoped (`rg`) + catat di validation log.

7. `Doc-Hardening`
- Wajib saat trigger canonical aktif (akses, scope, dashboard, query key, metadata sumber, atau lintas dokumen concern).
- TODO baru wajib mengikuti format `TODO <KODE_UNIK> ...`.

8. `ADR Sync`
- Wajib untuk keputusan arsitektur lintas concern; status ADR harus sinkron dengan TODO concern.

9. `Report`
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
- map ke concern terdekat, tulis asumsi eksplisit, lalu lanjut scoped read.

## 5. Decision Gates (No-Assumption Rules)

Gate A - Scope & Area:
- Data wilayah wajib memiliki `level`, `area_id`, `created_by`.
- `area_id` user wajib konsisten dengan `scope` dan `areas.level`.

Gate B - Authorization:
- Frontend bukan authority akses; enforcement wajib terbukti di backend (policy/middleware/scope service).

Gate C - Repository Boundary:
- Query domain baru hanya melalui repository boundary; query ad-hoc di controller/view/helper dilarang.

Gate D - Legacy Control:
- `areas` adalah source of truth wilayah; coupling baru ke artefak legacy non-canonical dilarang.

Gate E - Documentation Sync:
- Perubahan canonical tanpa update dokumen terkait = `belum selesai`.

## 6. Validation Matrix (Single Path)

Urutan validasi:
1. Syntax/lint file yang diubah.
2. Test targeted concern.
3. Regression concern terdekat.
4. Full suite (`php artisan test`) bila perubahan lintas domain, akses/policy/scope, dashboard agregat, atau migrasi/seeder canonical.
5. Khusus `doc-only` process/domain/adr: boleh selesai di step 1 + log operasional, jika tidak ada perubahan runtime/backend contract.

Kriteria selesai:
- Semua gate concern terpenuhi.
- Tidak ada drift `role` vs `scope` vs `areas.level`.
- Dokumen concern sinkron.

## 7. Commit by Concern

Aturan commit:
- Satu commit = satu concern logis.
- Pesan commit format: `type(scope): intent`.
- Concern besar dipecah ke commit independen agar rollback granular.

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
- daftar file terdampak, keputusan terkunci, dan validasi yang dijalankan.

## 9. Residual Risk & Mitigation

Risiko:
- Dokumen jalur tunggal bisa usang saat arsitektur berevolusi.
- AI terlalu kaku pada kasus edge yang butuh eksplorasi non-standar.

Mitigasi:
- Perubahan pola eksekusi wajib update playbook + dokumen ini pada sesi yang sama.
- Deviasi edge case wajib ditulis di TODO concern + log validasi.
- Checkpoint refleksi dibatasi satu koreksi rute utama per concern.

## 10. Referensi Operasional

- `AGENTS.md`
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`
- `docs/adr/README.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/security/AUTH_COHERENCE_MATRIX.md`
- `docs/process/OPERATIONAL_VALIDATION_LOG.md`
