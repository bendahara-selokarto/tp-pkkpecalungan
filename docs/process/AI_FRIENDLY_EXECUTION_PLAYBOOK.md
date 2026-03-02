# AI Friendly Execution Playbook (Domain Agnostic)

Tujuan:
- Menyimpan pola eksekusi yang efisien, akurat, dan valid untuk dipakai ulang lintas project.
- Menjaga agar jalur kerja AI selalu bisa ditingkatkan saat ditemukan pendekatan yang lebih baik.

## 1) Core Loop (Wajib)

1. Contract first
- Tetapkan kontrak masalah: target, batasan, acceptance criteria, dan risiko.

2. Scoped dependency map
- Baca hanya file yang relevan.
- Petakan side effect sebelum patch.

3. Minimal reversible patch
- Ubah sekecil mungkin.
- Hindari rewrite luas tanpa alasan teknis kuat.

4. Tiered validation
- L1: cek lokal cepat (lint/build/test targeted).
- L2: regression area terkait.
- L3: full suite untuk perubahan signifikan.

5. Learning capture
- Jika jalur baru lebih efisien/akurat, update playbook ini.
- Jika jalur lama kalah efektif, tandai deprecated + alasan.

## 2) Pattern Registry

Gunakan status:
- `active`: direkomendasikan.
- `candidate`: baru diuji sebagian.
- `deprecated`: tidak direkomendasikan.

| ID | Pattern | Trigger | Outcome Target | Validation Minimum | Status |
| --- | --- | --- | --- | --- | --- |
| `P-001` | Scoped Analysis + Diff-First | Task menyentuh beberapa layer | Waktu analisa turun, patch kecil | L1 + cek side effect | `active` |
| `P-002` | Contract -> Backend -> Frontend -> Test | Modul/menu baru | Drift kontrak turun | L2 + test matrix modul | `active` |
| `P-003` | Reusable UI Component + Audit Command | Konsistensi UI lintas halaman | Duplikasi style turun | L1 build + audit rg | `active` |
| `P-004` | Targeted Test Before Full Suite | Perubahan terlokalisir | Feedback lebih cepat | L1 targeted, L3 jika signifikan | `active` |
| `P-005` | Docs Ref Path Normalization | Refactor dokumentasi | Link putus = 0 | Script cek referensi markdown | `active` |
| `P-006` | New Menu -> Dashboard Trigger Audit | Ada menu/domain baru | Dashboard tetap representatif dan tidak drift | `DashboardDocumentCoverageTest` (+ `DashboardActivityChartTest` jika kontrak berubah) | `active` |
| `P-007` | Canonical Date Input UI | Form menambah field tanggal | Format UI konsisten dan payload backend stabil | Cek `type="date"` + submit payload `YYYY-MM-DD` | `active` |
| `P-008` | Pre-Release Legacy Upgrade Track | Refactor masih menyentuh legacy | Coupling legacy turun tanpa mengorbankan keamanan scope | Validasi mapping dampak + `php artisan migrate:fresh` + test relevan | `active` |
| `P-009` | Hybrid PDF Authenticity Verification | PDF lampiran punya merge-row/merge-col kompleks | Kontrak domain tetap akurat walau parser teks terbatas | Parser text extraction + verifikasi manual terhadap dokumen autentik + dokumen mapping | `active` |
| `P-010` | Date Output Harmonization Without Persistence Drift | Standardisasi tanggal menyentuh model + controller + test DB assertion | Output tanggal konsisten tanpa mengubah format simpan data | Targeted regression + assert DB value tetap kompatibel | `active` |
| `P-011` | Managed Super-Admin Assignment Guardrail | Perubahan matrix role/scope, request create/update user, atau opsi role pada UI manajemen user | Role sistem tetap aman tanpa bisa di-assign dari flow administratif biasa | Regression create/update user management + unit matrix role + auth super-admin test | `active` |
| `P-012` | Unit Direct Coverage Gate by Discovery | Penambahan/renaming unit Action/UseCase/Service/Repository | Contract `1 unit = minimal 1 direct test` tetap terjaga otomatis | `UnitCoverageGateTest` + full suite | `active` |
| `P-013` | UI Slug Humanization for Role/Scope | UI menampilkan slug teknis role/scope/area | Label user-facing konsisten manusiawi tanpa ubah kontrak teknis backend | Regression SuperAdmin view + render role badge di layout utama | `active` |
| `P-014` | Responsibility Visibility with Backend Read-Only Enforcement | Kebutuhan menu per penanggung jawab + mode akses read-only | UI hanya menampilkan tanggung jawab role, backend menolak bypass URL mutasi pada area read-only | Unit matrix + feature payload Inertia + feature anti bypass + full suite | `active` |
| `P-015` | Section-Scoped Query Key Contract for Role-Aware Dashboard | Dashboard memiliki section filter lebih dari satu dalam satu halaman | State filter tidak saling bertabrakan dan kontrak URL stabil lintas backend/frontend/docs | Feature test filter context + audit kontrak query key di docs | `active` |
| `P-016` | Triggered Doc-Hardening Pass | Ada sinyal canonical drift pada dokumentasi concern aktif | Kontrak dokumen lintas file tetap koheren dan tidak mismatch dengan implementasi | Scoped drift audit + sinkronisasi TODO/process/domain + ringkasan validasi | `active` |
| `P-017` | Zero-Ambiguity Single Path Routing | User meminta kepastian jalur tunggal AI atau task lintas concern berisiko multi-interpretasi | Task routing deterministik (concern -> file target -> validation ladder) dan output konsisten lintas sesi | Sinkronisasi `AGENTS.md` + dokumen single-path + log hardening concern | `active` |
| `P-018` | UI Runtime Safety Guardrail | Perubahan UI kritikal berbasis JavaScript (layout, dropdown, theme, dynamic state) | Behavior UI tetap terkontrol saat terjadi runtime error JavaScript | Guard global JS + fallback UI + build frontend | `active` |
| `P-019` | Attachment Render Recovery via Protected Stream Route | Lampiran (foto/berkas) tidak tampil di halaman show, terutama pada setup Apache/Windows | Lampiran tetap bisa preview dan dibuka tanpa bergantung pada static `/storage` URL | Targeted feature tests concern + `php artisan route:list --name=attachments.show` | `active` |
| `P-020` | Kecamatan Dual-Scope List Contract (`kecamatan` vs `desa monitoring`) | Daftar modul di scope kecamatan butuh mode data sendiri + mode monitoring desa | Mode `kecamatan` konsisten ke data milik sendiri, mode `desa` konsisten ke seluruh desa dalam kecamatan, dan monitoring tetap read-only | Feature test list kedua mode + payload mode visibility + middleware anti write bypass | `active` |
| `P-021` | ADR + TODO Coupled Governance | Ada keputusan arsitektur/canonical berdampak lintas sesi atau lintas modul | Keputusan teknis punya jejak trade-off yang bisa diaudit dan eksekusi concern tetap terikat checklist validasi | ADR template terisi + TODO concern aktif + sinkronisasi status keputusan | `active` |
| `P-022` | Self-Reflective Routing | User meminta jalur reflektif atau task berisiko salah klasifikasi concern pada routing awal | Jalur eksekusi tetap deterministik tetapi punya checkpoint refleksi terkontrol sebelum patch besar | Re-check concern+boundary+validation ladder + sinkronisasi single-path doc/playbook/TODO/ADR concern | `active` |
| `P-023` | Doc-Only Fast Lane Validation | Perubahan hanya dokumentasi process/domain/adr tanpa runtime change | Siklus validasi lebih cepat tanpa menurunkan guardrail sinkronisasi kontrak | L1 audit scoped (`rg`/link/status) + skip L3 bila tidak ada dampak runtime | `active` |

## 3) Protocol Update Pattern

Tambahkan pattern baru jika:
- Dipakai berulang minimal 2 kali.
- Mengurangi waktu eksekusi atau tingkat error secara nyata.
- Punya guardrail dan langkah validasi yang jelas.

Ubah pattern existing jika:
- Ada jalur baru dengan hasil lebih cepat dan coverage validasi setara/lebih baik.
- Jalur lama sering memicu rework atau false positive.

Deprecate pattern jika:
- Tidak kompatibel dengan arsitektur saat ini.
- Menambah risiko drift/bug.

## 4) Template Entri Pattern Baru

Gunakan template berikut saat menambah pattern:

```md
### P-XXX - <Nama Pattern>
- Tanggal:
- Status: candidate|active|deprecated
- Konteks:
- Trigger:
- Langkah eksekusi:
  1) ...
  2) ...
- Guardrail:
- Validasi minimum:
- Bukti efisiensi/akurasi:
- Risiko:
- Catatan reuse lintas domain/project:
```

## 5) Reuse Pack Lintas Project

Artefak yang direkomendasikan untuk dibawa ke project lain:
- Kontrak eksekusi AI (`AGENTS.md` atau setara).
- Playbook pattern ini.
- Checklist quality gate (auth, boundary, test).
- Runbook insiden (mis. rate limiter, outage, rollback).
- Template log validasi operasional.

## 6) Aturan Operasional Ringkas

- Setiap menemukan jalur baru yang lebih efisien: update registry + protocol.
- Setiap menemukan jalur lama tidak efektif: ubah status ke `deprecated` dan beri alternatif.
- Jangan simpan pattern hanya di chat; wajib masuk dokumen agar reusable.

## 7) Detail Pattern Tanggal

Detail operasional lengkap setiap pattern dipindahkan ke lampiran berikut agar konteks default lebih ringkas:
- `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK_PATTERN_DETAILS.md`

Aturan pakai:
- Baca file utama ini terlebih dahulu untuk routing cepat.
- Buka lampiran detail hanya saat perlu mengeksekusi pattern spesifik.
- Setiap perubahan status/isi pattern wajib disinkronkan di file utama dan lampiran pada sesi yang sama.
