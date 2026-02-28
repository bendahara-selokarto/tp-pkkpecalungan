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

### P-018 - UI Runtime Safety Guardrail
- Tanggal: 2026-02-24
- Status: active
- Konteks: Interaksi UI yang ditenagai JavaScript (dropdown, theme switch, state sidebar, event-driven update) dapat memicu behavior tidak diinginkan saat error runtime tidak ditangani.
- Trigger:
  - Perubahan pada layout utama atau komponen navigasi global.
  - Penambahan interaksi UI yang bergantung pada event JavaScript global.
- Langkah eksekusi:
  1) Pasang guard global untuk `window.error`, `window.unhandledrejection`, dan `app.config.errorHandler`.
  2) Emit event internal runtime error agar layer layout bisa menampilkan fallback terkontrol.
  3) Tampilkan fallback banner non-blocking + aksi pemulihan (`Muat Ulang`).
  4) Lindungi akses storage browser dengan wrapper aman (`try/catch`) untuk mencegah crash awal render.
- Guardrail:
  - Jangan biarkan error runtime diam tanpa sinyal ke UI.
  - Jangan menggantungkan state kritikal pada `localStorage` tanpa fallback in-memory.
  - Fallback tidak boleh membuka bypass otorisasi atau mengubah kontrak akses backend.
- Validasi minimum:
  - `npm run build` sukses.
  - Test backend terkait visibilitas/akses menu tetap hijau.
  - Fallback banner muncul saat event internal `ui-runtime-error` ditembak.
- Bukti efisiensi/akurasi:
  - Diterapkan pada bootstrap `resources/js/app.js` dan layout global `resources/js/Layouts/DashboardLayout.vue`.
- Risiko:
  - Tanpa deduplikasi error, banner bisa muncul berulang untuk root cause yang sama.
- Catatan reuse lintas domain/project:
  - Terapkan pada aplikasi SPA/Inertia yang mengandalkan layout global untuk interaksi kritikal.

### P-019 - Attachment Render Recovery via Protected Stream Route
- Tanggal: 2026-02-27
- Status: active
- Konteks: Pada environment Apache/Windows, URL lampiran berbasis static path (`/storage/...`) bisa gagal render (404/403) walau file ada dan symlink sudah benar.
- Trigger:
  - User melaporkan halaman show tidak menampilkan foto/berkas.
  - Browser membuka error page Apache saat klik lampiran.
- Langkah eksekusi:
  1) Lakukan cek cepat environment: `APP_URL`, keberadaan `public/storage`, dan keberadaan file pada `storage/app/public`.
  2) Jika issue tetap muncul, hindari ketergantungan direct static URL; tambahkan route attachment terproteksi per scope (`desa`, `kecamatan`, `kecamatan/desa-activities`).
  3) Di controller, stream file via `Storage::disk('public')->response(...)` setelah `authorize('view', $activity)` agar akses tetap mengikuti policy/scope.
  4) Ubah payload `image_url`/`document_url` menjadi URL route attachment terproteksi.
  5) Di UI show, render preview inline (gambar/PDF) dan sediakan fallback tautan `Buka berkas`.
- Guardrail:
  - Route attachment wajib berada di belakang middleware + policy concern yang sama dengan halaman detail.
  - Dilarang membuka file path langsung tanpa validasi akses backend.
  - Gunakan perubahan minimal; jangan mengubah kontrak domain `areas`, role, atau scope.
- Validasi minimum:
  - `php artisan test tests/Feature/DesaActivityTest.php tests/Feature/KecamatanActivityTest.php tests/Feature/KecamatanDesaActivityTest.php`
  - `php artisan route:list --name=attachments.show`
  - Build frontend sukses (`npm run build`) jika ada perubahan renderer preview.
- Bukti efisiensi/akurasi:
  - Menangani kegagalan render lampiran tanpa perlu perubahan server Apache global.
- Risiko:
  - Route attachment menambah endpoint baru; regression auth wajib dijalankan untuk mencegah data leak.
- Catatan reuse lintas domain/project:
  - Jadikan pattern pertama untuk insiden lampiran tidak tampil di modul Inertia/Laravel yang menyimpan file pada disk `public`.

### P-020 - Kecamatan Dual-Scope List Contract (`kecamatan` vs `desa monitoring`)
- Tanggal: 2026-02-28
- Status: active
- Konteks: Role level kecamatan (khususnya `kecamatan-sekretaris`) membutuhkan dua mode daftar dalam satu concern: mode kerja level kecamatan dan mode monitoring desa.
- Trigger:
  - UI daftar level kecamatan menambahkan toggle cakupan (`kecamatan`/`desa`).
  - Ada kebutuhan mencegah data campur antara daftar kerja kecamatan dan daftar monitoring desa.
- Langkah eksekusi:
  1) Tetapkan kontrak mode:
     - mode `kecamatan`: list kegiatan level kecamatan milik aktor login (`created_by = user_id` untuk `kecamatan-sekretaris`).
     - mode `desa`: list seluruh data level desa dalam parent kecamatan aktor login.
  2) Implementasikan filter di backend (use case/repository), bukan di frontend.
  3) Pastikan mode monitoring desa tetap `read-only` di payload visibilitas + middleware anti bypass mutasi.
  4) Gunakan toggle UI hanya sebagai pengalih endpoint/list source, bukan authority akses data.
- Guardrail:
  - Jangan menyamakan mode `kecamatan` dengan semua data kecamatan by-area jika kontrak menyebut data milik sendiri.
  - Jangan membolehkan mode monitoring desa melakukan `create/update/delete`.
  - Pastikan boundary query tetap melalui repository dan tetap scoped ke `areas` canonical.
- Validasi minimum:
  - Feature test mode `kecamatan` hanya menampilkan data milik aktor.
  - Feature test mode `desa` menampilkan data semua desa dalam kecamatan sendiri dan menolak desa luar kecamatan.
  - Feature test payload visibilitas/middleware memastikan `desa-activities` untuk `kecamatan-sekretaris` tetap `read-only`.
- Bukti efisiensi/akurasi:
  - Diterapkan pada commit `339275e` untuk concern `activities` (`kecamatan/activities` + `kecamatan/desa-activities`) beserta test kontrak dan hardening dokumentasi.
  - Direuse pada concern `arsip` (2026-02-28) untuk toggle `Arsip Saya` vs `Desa (Monitoring)` dengan guard monitoring tetap `read-only`.
- Risiko:
  - Jika tidak didokumentasikan, concern lain mudah mereplikasi toggle UI tanpa konsistensi kontrak query backend.
- Catatan reuse lintas domain/project:
  - Gunakan sebagai template default untuk semua daftar scope kecamatan yang punya mode operasional internal + monitoring desa.

### P-021 - ADR + TODO Coupled Governance
- Tanggal: 2026-02-28
- Status: active
- Konteks: Hindari keputusan arsitektur hanya tersimpan di chat/TODO.
- Trigger:
  - Perubahan kontrak arsitektur, authorization, atau boundary repository yang berdampak lintas modul.
  - Keputusan teknis strategis yang perlu jejak audit lintas sesi.
  - Bukan untuk perubahan minor `doc-only`.
- Langkah eksekusi:
  1) Kunci rencana eksekusi concern pada TODO aktif (`docs/process/TODO_*`) dengan basis `docs/process/TEMPLATE_TODO_CONCERN.md`.
  2) Catat keputusan arsitektur pada ADR (`docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`) dengan basis `docs/adr/ADR_TEMPLATE.md`.
  3) Tautkan ADR ke TODO concern dan area validasi test.
  4) Saat keputusan berubah, buat ADR baru dan tandai ADR lama sebagai `superseded`.
- Guardrail:
  - TODO tetap sumber rencana eksekusi; ADR sumber keputusan arsitektur.
  - Jangan ubah status ADR ke `accepted` tanpa rencana validasi yang jelas.
  - `doc-only` wording/checklist/link: cukup update TODO concern.
- Validasi minimum:
  - ADR berisi konteks, opsi, keputusan, dampak, validasi, fallback.
  - TODO concern menyimpan checklist eksekusi dan hasil validasi terbaru.
  - Referensi silang ADR <-> TODO konsisten.
- Bukti efisiensi/akurasi:
  - Menurunkan ambiguitas keputusan jangka panjang.
- Risiko:
  - Tambahan overhead dokumentasi jika dipakai untuk perubahan kecil yang tidak strategis.
- Catatan reuse lintas domain/project:
  - Cocok untuk project dengan kebutuhan audit trail.

### P-023 - Doc-Only Fast Lane Validation
- Tanggal: 2026-03-01
- Status: active
- Konteks: Perubahan dokumen sering tidak butuh full suite.
- Trigger:
  - Perubahan hanya pada `docs/**`.
  - Tidak ada perubahan runtime/backend contract.
- Langkah eksekusi:
  1) Audit scoped istilah + referensi silang TODO/ADR/process.
  2) Validasi cepat dengan `rg` token concern.
  3) Catat hasil di `OPERATIONAL_VALIDATION_LOG.md` (`doc-only fast lane`).
  4) Jalankan `L3` hanya jika ada sinyal drift ke runtime.
- Guardrail:
  - Tidak boleh dipakai jika ada perubahan kode aplikasi.
  - Tetap wajib sinkron lintas dokumen concern saat trigger `P-016` aktif.
  - Tetap wajib ADR jika perubahan bersifat keputusan arsitektur lintas concern.
- Validasi minimum:
  - `rg` token concern lintas `single-path/playbook/TODO/ADR` konsisten.
  - Registry SOT tetap benar.
  - Log operasional mencatat artefak + command.
- Bukti efisiensi/akurasi:
  - Mengurangi latency concern dokumentasi.
- Risiko:
  - False negative jika task salah diklasifikasikan sebagai `doc-only`.
- Catatan reuse lintas domain/project:
  - Cocok untuk repo dengan governance dokumen ketat.

### P-022 - Self-Reflective Routing
- Tanggal: 2026-03-01
- Status: active
- Konteks: mencegah salah klasifikasi concern pada routing awal.
- Trigger: user meminta self-reflective routing atau ditemukan mismatch concern pasca scoped read.
- Langkah: klasifikasi awal -> checkpoint refleksi -> tier model (`low/small`, `medium/mid`, `high/large`) -> koreksi rute 1x jika mismatch -> kunci keputusan di TODO/ADR.
- Guardrail: tidak boleh loop; maksimal 1 koreksi rute utama per concern, tetap patuh `AGENTS.md`, dan tidak mengubah boundary auth/backend.
- Validasi minimum: single-path, playbook, TODO, dan ADR concern sinkron.
- Bukti efisiensi/akurasi: rework karena salah route awal menurun.
- Risiko: tanpa batas koreksi, eksekusi melambat.
- Reuse: cocok untuk governance dokumen ketat.

```dsl
PATTERN_ID: P-022
PATTERN_NAME: SELF_REFLECTIVE_ROUTING
TRIGGER: user_request_self_reflective|post_scoped_read_mismatch
FLOW: classify>reflective_checkpoint>model_tier_select>route_fix_once>lock_todo_adr
MODEL_TIER_MAP: low=small, medium=mid, high=large
GUARDRAIL: max_route_correction=1
VALIDATION: sync(single-path,playbook,todo,adr)
STATUS: active
```
