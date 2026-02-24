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
- Status kontrak: metode default/baku project untuk pembacaan dokumen autentik bertabel hingga ada bukti metode baru yang lebih akurat.
- Langkah eksekusi:
  1) Baca: jalankan ekstraksi otomatis (contoh: Node parser) untuk token identitas dokumen.
  2) Baca lanjutan (presisi header): jika text-layer tidak menangkap header tabel utuh, render visual halaman (screenshot) lalu verifikasi manual struktur header (jumlah kolom, merge row/col, label grup/sub-header).
  3) Laporkan/Konfirmasi: laporkan temuan baca (termasuk gap parser) dan konfirmasi keputusan kontrak sebelum patch sinkronisasi.
  4) Sinkronkan: simpan hasil transformasi pada dokumen mapping domain, lalu sinkronkan terminology/domain matrix/implementasi yang terdampak.
- Guardrail:
  - Jangan tetapkan kontrak tabel kompleks hanya dari OCR/parser teks.
  - Jangan tetapkan kontrak header tabel hanya dari text-layer jika hasil baca parsial.
  - Saat hasil parser dan dokumen autentik konflik, dokumen autentik adalah sumber final.
  - Jika ada referensi ganda untuk concern yang sama (mis. revisi screenshot header), referensi terakhir dari user menjadi acuan final dan referensi sebelumnya ditandai `superseded`.
  - Screenshot header tabel yang memenuhi kriteria validasi adalah bukti kontrak resmi untuk merge cell (`rowspan`/`colspan`).
  - Dokumentasikan gap parsing secara eksplisit agar tidak dianggap bug data aplikasi.
  - Dilarang melanjutkan implementasi bila peta header belum lengkap sampai tingkat penggabungan sel (`rowspan`/`colspan`).
- Validasi minimum:
  - Ada bukti token identitas dokumen terdeteksi oleh parser.
  - Ada bukti visual (screenshot/crop) saat header tabel perlu verifikasi manual.
  - Bukti visual untuk merge cell dinyatakan valid jika:
    - mencakup area header tabel secara utuh,
    - grid/garis sel cukup jelas untuk identifikasi penggabungan,
    - baris nomor kolom terlihat,
    - label header utama/sub-header masih terbaca.
  - Ada peta header tabel lengkap yang mencakup urutan kolom + penggabungan sel (`rowspan`/`colspan`).
  - Ada dokumen mapping domain yang mengunci transformasi struktur autentik ke representasi aplikasi.
  - Terminology/domain matrix menunjuk dokumen mapping tersebut.
- Bukti efisiensi/akurasi:
  - Diterapkan pada Lampiran 4.15 (`d:\pedoman\177.pdf`) saat parser Node membaca identitas dokumen tetapi tidak merekonstruksi header tabel 19 kolom secara penuh.
- Risiko:
  - Tambahan kerja manual transkripsi pada struktur tabel kompleks.
- Catatan reuse lintas domain/project:
  - Gunakan pattern ini untuk seluruh lampiran yang memiliki header bertingkat atau kolom gabungan yang padat.
  - Perubahan metode wajib memperbarui `AGENTS.md` dan registry pattern pada sesi yang sama agar kontrak tetap sinkron.

### P-010 - Date Output Harmonization Without Persistence Drift
- Tanggal: 2026-02-22
- Status: active
- Konteks: Harmonisasi output tanggal sering membutuhkan konsistensi `Y-m-d` di payload controller, tetapi penambahan cast `date` pada model bisa mengubah format persistence (`YYYY-MM-DD HH:MM:SS`) pada tabel tertentu.
- Trigger: Refactor tanggal lintas layer yang menyentuh model cast + controller serializer + assert database di test.
- Langkah eksekusi:
  1) Terapkan strict validation di request (`date_format:Y-m-d`) terlebih dahulu.
  2) Harmonisasikan output tanggal di controller ke `Y-m-d` saat serialize payload (form/list/show).
  3) Gunakan formatter frontend terpusat untuk display (`DD/MM/YYYY`), bukan format ad-hoc.
  4) Verifikasi bahwa perubahan tidak menggeser format simpan di database pada tabel existing.
- Guardrail:
  - Jangan menambah cast model tanggal jika berdampak ke format simpan data existing tanpa kebutuhan eksplisit migrasi.
  - Jika cast model menimbulkan drift persistence, rollback cast dan pindahkan normalisasi ke layer controller/presenter.
  - Utamakan backward compatibility untuk assertion database di test existing.
- Validasi minimum:
  - Test fitur create/update modul terdampak tetap lulus.
  - Test invalid format tanggal (`DD/MM/YYYY`) ditolak untuk field canonical.
  - Nilai database existing tetap kompatibel dengan kontrak yang sedang berjalan.
- Bukti efisiensi/akurasi:
  - Diterapkan pada harmonisasi tanggal modul Activity, Bantuan, AgendaSurat, Inventaris, DataWarga, dan PilotProjectNaskahPelaporan.
- Risiko:
  - Jika coverage regression kurang, drift persistence bisa lolos ke branch utama.
- Catatan reuse lintas domain/project:
  - Jadikan controller serialization sebagai titik normalisasi utama saat schema historis belum seragam antar tabel.

### P-011 - Managed Super-Admin Assignment Guardrail
- Tanggal: 2026-02-22
- Status: active
- Konteks: Role `super-admin` wajib tetap bisa dipakai untuk akses sistem, tetapi tidak boleh ditetapkan dari jalur manajemen user administratif biasa.
- Trigger: Perubahan matrix role/scope, request create/update user, atau opsi role pada UI manajemen user.
- Langkah eksekusi:
  1) Pertahankan `super-admin` pada compatibility matrix untuk akses sistem.
  2) Keluarkan `super-admin` dari `assignableRolesForScope`.
  3) Tolak assignment `super-admin` di request + action create/update user.
  4) Terapkan filter defensif di UI create/edit user agar opsi `super-admin` tidak muncul.
- Guardrail:
  - Frontend bukan authority; backend wajib menolak assignment terlarang meski payload dipaksa manual.
  - Jangan melemahkan policy existing yang memang membutuhkan role `super-admin`.
- Validasi minimum:
  - Feature test create/update manajemen user menolak `role=super-admin`.
  - Unit test opsi role memastikan `super-admin` tidak muncul pada role assignable.
  - Test otorisasi super-admin tetap lulus.
- Bukti efisiensi/akurasi:
  - Diterapkan pada `RoleScopeMatrix`, `StoreUserRequest`, `UpdateUserRequest`, `CreateUserAction`, `UpdateUserAction`, dan halaman `SuperAdmin/Users`.
- Risiko:
  - Jika hanya UI yang dipatch, bypass HTTP langsung akan membuka celah assignment.
- Catatan reuse lintas domain/project:
  - Gunakan pola ini untuk semua flow administrasi role sistem yang bersifat reserved.

### P-012 - Unit Direct Coverage Gate by Discovery
- Tanggal: 2026-02-22
- Status: active
- Konteks: Requirement project mewajibkan seluruh unit Action/UseCase/Service/Repository memiliki direct test tanpa menunggu audit manual berulang.
- Trigger: Penambahan/renaming unit file di boundary `app/Actions`, `app/UseCases`, `app/Services`, `app/Repositories`, dan `app/Domains/Wilayah/*/{Actions,UseCases,Services,Repositories}`.
- Langkah eksekusi:
  1) Discover unit file by convention (`*Action.php`, `*UseCase.php`, `*Service.php`, `*Repository.php`) pada boundary yang disepakati.
  2) Bangun data provider otomatis untuk seluruh unit terdeteksi.
  3) Jalankan gate test per unit untuk memastikan seluruh unit ter-load dan terpetakan.
  4) Kunci expected total unit pada test agar penambahan unit baru wajib diikuti pembaruan coverage gate.
- Guardrail:
  - Unit discovery harus deterministic dan tidak menyapu folder di luar boundary.
  - Gate test tidak menggantikan test perilaku; gunakan sebagai minimum direct coverage contract.
- Validasi minimum:
  - `tests/Unit/Architecture/UnitCoverageGateTest.php` lulus dengan total unit sesuai kontrak.
  - `php artisan test` penuh tetap lulus setelah gate aktif.
- Bukti efisiensi/akurasi:
  - Menutup gap direct coverage dari audit awal `8/183` menjadi `183/183` dengan verifikasi ulang otomatis.
- Risiko:
  - Penambahan unit baru akan memecahkan gate jika coverage contract belum diperbarui.
- Catatan reuse lintas domain/project:
  - Pakai pattern ini sebagai baseline gate sebelum memperluas test perilaku high-risk per domain.

### P-013 - UI Slug Humanization for Role/Scope
- Tanggal: 2026-02-22
- Status: active
- Konteks: UI administratif bisa menerima slug teknis (`super-admin`, `admin-kecamatan`, `desa`) dari backend dan membuat teks terlihat tidak natural.
- Trigger: Halaman menampilkan role/scope/level/area ke user akhir.
- Langkah eksekusi:
  1) Pertahankan slug teknis sebagai kontrak data backend.
  2) Terapkan formatter label terpusat di frontend untuk role/scope/area.
  3) Jika endpoint tertentu lebih stabil dengan label siap pakai, backend boleh kirim label terformat.
  4) Gunakan formatter yang sama di halaman list + form + ringkasan layout.
- Guardrail:
  - Jangan ubah slug yang dipakai policy/authorization.
  - Hindari formatter ad-hoc per halaman; wajib util terpusat.
- Validasi minimum:
  - Feature test super-admin tetap hijau.
  - Role `super-admin` tampil `Super Admin`.
  - Scope tampil `Desa`/`Kecamatan`.
- Bukti efisiensi/akurasi:
  - Diterapkan pada `SuperAdmin/Users` dan `DashboardLayout` melalui `resources/js/utils/roleLabelFormatter.js`.
- Risiko:
  - Konsistensi bisa drift jika ada endpoint baru yang bypass formatter.
- Catatan reuse lintas domain/project:
  - Jadikan formatter role/scope sebagai dependency default semua halaman administratif.

### P-014 - Responsibility Visibility with Backend Read-Only Enforcement
- Tanggal: 2026-02-23
- Status: active
- Konteks: UI perlu menampilkan menu domain hanya sesuai penanggung jawab role, dengan mode `read-only` yang tidak boleh bisa dibypass via URL langsung.
- Trigger: Perubahan model akses role/menu atau kebutuhan segmentasi menu per peran operasional.
- Langkah eksekusi:
  1) Definisikan matrix tunggal role -> group menu -> mode akses di backend service.
  2) Resolve mode per group + per module dan share ke Inertia sebagai source of truth UI.
  3) Terapkan middleware akses modul yang memblokir modul di luar tanggung jawab dan menolak write intent saat mode `read-only`.
  4) Jadikan UI hanya consume payload backend; untuk mode `read-only`, sembunyikan tombol mutasi (`create/update/delete`) pada level layout.
- Guardrail:
  - Frontend bukan authority akses; backend wajib menolak bypass URL.
  - Matrix role harus sinkron dengan scope-area valid dari `UserAreaContextService`.
  - Read-only harus menolak `POST/PUT/PATCH/DELETE` dan endpoint `create/edit`.
- Validasi minimum:
  - Unit test matrix role-menu-mode.
  - Feature test payload Inertia untuk sekretaris/pokja/multi-role.
  - Feature test anti bypass URL untuk lintas modul dan read-only mutation.
  - Full suite `php artisan test`.
- Bukti efisiensi/akurasi:
  - Diterapkan pada `RoleMenuVisibilityService`, middleware `EnsureModuleVisibility`, share Inertia `HandleInertiaRequests`, dan `DashboardLayout`.
- Risiko:
  - Modul dengan slug route alias khusus (contoh route report gabungan) wajib ikut dipetakan agar tidak false-deny.
- Catatan reuse lintas domain/project:
  - Pattern ini direkomendasikan sebagai default untuk kebutuhan segmentasi menu lintas role dengan hardening backend.

### P-015 - Section-Scoped Query Key Contract for Role-Aware Dashboard
- Tanggal: 2026-02-23
- Status: active
- Konteks: Dashboard sekretaris memakai beberapa section dengan filter group berbeda sehingga token query generik (`by_group`) mudah menimbulkan drift state dan ambigu sumber filter.
- Trigger: Halaman dashboard atau report multi-section memerlukan filter paralel dalam satu URL.
- Langkah eksekusi:
  1) Tetapkan query key unik per section (contoh: `section2_group`, `section3_group`).
  2) Pastikan backend normalisasi token query mengikuti key per section, bukan key generik tunggal.
  3) Pastikan frontend memetakan kontrol filter ke query key section yang tepat dan menjaga state independen antarseksi.
  4) Kunci kontrak key yang sama di dokumen rencana dan dokumen arsitektur untuk mencegah drift terminologi.
- Guardrail:
  - Hindari query key generik untuk beberapa section berbeda.
  - Jangan izinkan perubahan filter section A mengubah payload section B tanpa kontrak eksplisit.
  - Frontend tetap consumer; validasi akhir dan enforcement filter context berada di backend.
- Validasi minimum:
  - Feature test filter context payload per section (`section2_group` dan `section3_group`).
  - Audit dokumen rencana dashboard memastikan tidak ada istilah kontrak lama (`by_group`) yang bertentangan.
  - Regression `DashboardDocumentCoverageTest` tetap hijau.
- Bukti efisiensi/akurasi:
  - Dipakai pada refactor dashboard role-aware 2026-02-23 untuk menjaga stabilitas state section 2/3 dan skenario section 4 berbasis `section3_group=pokja-i`.
- Risiko:
  - Query URL bertambah panjang pada mode kombinasi filter.
- Catatan reuse lintas domain/project:
  - Terapkan pada semua halaman analitik bertingkat yang memiliki lebih dari satu panel filter independen.

### P-016 - Triggered Doc-Hardening Pass
- Tanggal: 2026-02-23
- Status: active
- Konteks: Perubahan concern besar sering menyentuh beberapa dokumen sekaligus dan memicu drift istilah, status checklist, atau kontrak query/akses.
- Trigger:
  - Ada perubahan kontrak canonical (akses, scope, query key, dashboard representation, metadata sumber).
  - Ada perubahan lintas beberapa dokumen untuk concern yang sama.
  - Ada mismatch status dokumen vs implementasi aktual.
- Langkah eksekusi:
  1) Lakukan audit drift terbatas pada dokumen concern aktif (grep istilah kunci + diff).
  2) Normalisasi kontrak istilah canonical lintas TODO/process/domain/playbook.
  3) Sinkronkan status checklist dan keputusan terkunci dengan implementasi terbaru.
  4) Laporkan jejak hardening: file terdampak + validasi yang dijalankan.
- Guardrail:
  - Tetap scoped; hindari menyapu seluruh dokumen proyek tanpa trigger.
  - Jangan mengubah dokumen non-concern.
  - Hardening dokumen tidak boleh mengganti authority akses backend.
- Validasi minimum:
  - Tidak ada istilah kontrak lama yang konflik pada dokumen concern aktif.
  - Referensi antar dokumen concern tetap valid.
  - Status checklist utama sesuai kondisi implementasi saat itu.
- Bukti efisiensi/akurasi:
  - Dipakai pada hardening dashboard role-aware 2026-02-23 untuk menyinkronkan TODO refactor, TODO UI, skenario khusus, domain matrix, dan alignment plan.
- Risiko:
  - Over-documentation jika trigger diterapkan terlalu longgar.
- Catatan reuse lintas domain/project:
  - Terapkan sebagai opsi default saat terdeteksi sinyal drift canonical antar dokumen.

### P-017 - Zero-Ambiguity Single Path Routing
- Tanggal: 2026-02-23
- Status: active
- Konteks: Task lintas concern sering memicu multi-interpretasi ketika routing kerja AI hanya tersirat di beberapa dokumen.
- Trigger:
  - User meminta jalur tunggal AI.
  - Concern menyentuh lebih dari satu domain (akses, dashboard, seeder, dokumentasi) dan butuh urutan eksekusi deterministik.
- Langkah eksekusi:
  1) Klasifikasikan task ke concern utama.
  2) Kunci kontrak concern + file target + acceptance criteria.
  3) Eksekusi patch minimal sesuai boundary arsitektur.
  4) Jalankan validation ladder (L1-L3) sesuai tingkat dampak.
  5) Tutup dengan doc-hardening sinkron lintas dokumen concern.
- Guardrail:
  - Prioritas kebenaran tetap mengikuti `AGENTS.md`.
  - Tidak boleh bypass quality gate authorization/repository boundary.
  - Tidak boleh mengklaim selesai jika dokumen canonical masih drift.
- Validasi minimum:
  - `AGENTS.md` menunjuk ke dokumen single-path aktif.
  - Dokumen single-path memuat routing concern -> file -> validasi.
  - Ada jejak hardening di log operasional concern.
- Bukti efisiensi/akurasi:
  - Diterapkan untuk membangun `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md` sebagai rute operasional tunggal.
- Risiko:
  - Over-constraint jika deviasi edge-case tidak dicatat.
- Catatan reuse lintas domain/project:
  - Pattern ini cocok sebagai baseline di project yang punya guardrail domain/policy ketat dan kebutuhan konsistensi lintas sesi AI.
