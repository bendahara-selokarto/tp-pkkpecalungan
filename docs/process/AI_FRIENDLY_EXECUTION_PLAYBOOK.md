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

### P-007 - Canonical Date Input UI
- Tanggal: 2026-02-21
- Status: active
- Konteks: Standardisasi field tanggal lintas form Inertia + Vue agar konsisten di UI dan backend.
- Trigger: Menambah atau mengubah field tanggal pada form.
- Langkah eksekusi:
  1) Gunakan `input` dengan `type="date"` pada komponen Vue.
  2) Ikat nilai field dengan `v-model` ke properti form.
  3) Pertahankan nilai submit dalam format canonical `YYYY-MM-DD`.
- Guardrail:
  - Jangan ubah ke format teks bebas di frontend.
  - Hindari parsing manual tanggal di komponen jika tidak diperlukan.
  - Validasi backend tetap source of truth untuk format tanggal.
- Validasi minimum:
  - Verifikasi field tanggal tampil sebagai date picker native browser.
  - Verifikasi payload submit mengirim string tanggal canonical (`YYYY-MM-DD`).
- Bukti efisiensi/akurasi:
  - Sudah dipakai di `resources/js/admin-one/components/DataWargaAnggotaTable.vue` pada field `tanggal_lahir`.
- Risiko:
  - Tampilan visual date picker dapat sedikit berbeda antar browser/OS, tetapi format payload tetap konsisten.
- Catatan reuse lintas domain/project:
  - Gunakan pola ini sebagai default semua field tanggal baru, kecuali ada kebutuhan eksplisit format lain dari kontrak domain.

### P-008 - Pre-Release Legacy Upgrade Track
- Tanggal: 2026-02-21
- Status: active
- Konteks: Aplikasi masih pre-release dan diizinkan reset data development untuk percepatan upgrade legacy.
- Trigger: Refactor yang mengubah kontrak data lama/legacy atau merapikan boundary repository dari dependency legacy.
- Langkah eksekusi:
  1) Petakan coupling legacy saat ini (request, controller, action, repository, tests).
  2) Terapkan patch migrasi/refactor terkontrol dengan target pengurangan dependency legacy.
  3) Jika perlu reset struktur data, jalankan `php artisan migrate:fresh`.
  4) Validasi regresi area terdampak dengan test relevan.
- Guardrail:
  - Tetap pertahankan otorisasi backend.
  - `areas` tetap canonical wilayah.
  - Tidak boleh menambah debt legacy baru tanpa justifikasi tertulis.
- Validasi minimum:
  - Dokumen dampak dan fallback tersedia.
  - Hasil `migrate:fresh` berhasil.
  - Test relevan area terdampak lulus.
- Bukti efisiensi/akurasi:
  - Cocok untuk fase pre-release ketika perubahan struktur data masih aktif dan cepat berubah.
- Risiko:
  - Kehilangan data lokal development setelah `migrate:fresh`.
- Catatan reuse lintas domain/project:
  - Terapkan hanya untuk fase pre-release atau environment non-produksi.

### P-009 - Hybrid PDF Authenticity Verification
- Tanggal: 2026-02-22
- Status: active
- Konteks: Verifikasi struktur lampiran PDF dengan tabel kompleks (merge header) yang tidak stabil jika hanya mengandalkan ekstraksi text-layer otomatis.
- Trigger: Dokumen PDF pedoman dipakai sebagai sumber kontrak domain dan hasil parser teks tidak mencerminkan struktur tabel utuh.
- Langkah eksekusi:
  1) Baca: jalankan ekstraksi otomatis (contoh: Node parser) untuk token identitas dan verifikasi struktur tabel langsung ke dokumen autentik.
  2) Laporkan/Konfirmasi: laporkan temuan baca (termasuk gap parser) dan konfirmasi keputusan kontrak sebelum patch sinkronisasi.
  3) Sinkronkan: simpan hasil transformasi pada dokumen mapping domain, lalu sinkronkan terminology/domain matrix/implementasi yang terdampak.
- Guardrail:
  - Jangan tetapkan kontrak tabel kompleks hanya dari OCR/parser teks.
  - Saat hasil parser dan dokumen autentik konflik, dokumen autentik adalah sumber final.
  - Dokumentasikan gap parsing secara eksplisit agar tidak dianggap bug data aplikasi.
- Validasi minimum:
  - Ada bukti token identitas dokumen terdeteksi oleh parser.
  - Ada dokumen mapping domain yang mengunci transformasi struktur autentik ke representasi aplikasi.
  - Terminology/domain matrix menunjuk dokumen mapping tersebut.
- Bukti efisiensi/akurasi:
  - Diterapkan pada Lampiran 4.15 (`d:\pedoman\177.pdf`) saat parser Node membaca identitas dokumen tetapi tidak merekonstruksi header tabel 19 kolom secara penuh.
- Risiko:
  - Tambahan kerja manual transkripsi pada struktur tabel kompleks.
- Catatan reuse lintas domain/project:
  - Gunakan pattern ini untuk seluruh lampiran yang memiliki header bertingkat atau kolom gabungan yang padat.
