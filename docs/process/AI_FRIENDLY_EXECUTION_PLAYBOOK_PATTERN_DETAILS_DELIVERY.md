# AI Friendly Execution Playbook - Pattern Details (Annex: Delivery)

Tanggal efektif: 2026-03-09  
Status: `active`  
Parent: `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`  
Shard: `delivery`

Dokumen ini menyimpan detail langkah pattern delivery/data/UI agar file playbook utama tetap ringkas untuk proses routing cepat.

## 1) Detail Pattern Delivery
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

### P-028 - Deferred Secondary Inertia Prop

- Tanggal: 2026-03-08
- Status: active
- Konteks: Halaman Inertia memiliki prop sekunder berukuran besar yang tidak harus tersedia pada first paint, tetapi masih lebih aman dipertahankan pada route yang sama.
- Trigger: Payload sekunder besar mempengaruhi waktu render awal, sementara prop primer (`stats`, `charts`, context inti) harus tetap tampil segera.
- Langkah eksekusi:
  1) Pisahkan resolver backend antara prop primer dan prop sekunder agar prop primer tidak ikut menghitung payload sekunder.
  2) Ubah prop sekunder menjadi `Inertia::defer(...)` dengan group yang eksplisit.
  3) Render fallback di Vue memakai komponen `Deferred` agar state loading tidak ambigu.
  4) Gating watcher/query sync yang sebelumnya mengasumsikan prop sekunder selalu tersedia pada first load.
  5) Pindahkan assertion test prop sekunder ke `loadDeferredProps(<group>)`.
- Guardrail:
  - Jangan menandai prop sebagai deferred jika resolver prop primer masih menghitungnya diam-diam.
  - Jangan menambah JSON route baru hanya untuk payload sekunder yang masih satu concern halaman.
  - Pastikan state kosong nyata dibedakan dari state “belum dimuat”.
- Validasi minimum:
  - Initial Inertia response tidak memuat prop deferred.
  - `loadDeferredProps()` berhasil memuat prop group yang benar.
  - Targeted feature test concern tetap hijau.
  - Build frontend lulus jika halaman Vue disentuh.
- Bukti efisiensi/akurasi:
  - Diterapkan pada dashboard `dashboardBlocks` setelah wave 1 partial reload selesai, dengan route dashboard yang tetap tunggal.
- Risiko:
  - Watcher/filter yang tidak digating bisa salah memicu visit ulang sebelum prop deferred selesai dimuat.
- Catatan reuse lintas domain/project:
  - Cocok untuk dashboard atau halaman summary yang punya blok sekunder berat tetapi tidak layak dipecah menjadi API concern baru.

### P-029 - Remembered Presentational UI State

- Tanggal: 2026-03-08
- Status: active
- Konteks: Halaman Inertia memiliki state UI lokal seperti collapse, tab aktif, atau helper panel yang bukan bagian dari contract backend tetapi mengganggu UX bila selalu reset setelah visit.
- Trigger: State presentasional reset pada partial reload, deferred reload, atau visit Inertia berikutnya padahal tidak perlu ikut URL/query.
- Langkah eksekusi:
  1) Identifikasi state yang murni presentasional dan tidak mempengaruhi query/backend.
  2) Simpan state tersebut dengan `useRemember`.
  3) Gunakan remember key yang cukup sempit agar tidak bertabrakan antar concern atau user.
  4) Pertahankan sinkronisasi cleanup lokal bila data backend untuk elemen terkait berubah.
- Guardrail:
  - Jangan gunakan `useRemember` untuk state yang seharusnya canonical di URL atau backend.
  - Hindari remember key generik lintas concern.
  - Jangan menambah side effect backend hanya untuk menyimpan state presentasional.
- Validasi minimum:
  - Targeted regression concern tetap hijau.
  - Build frontend lulus.
- Bukti efisiensi/akurasi:
  - Diterapkan pada dashboard untuk mempertahankan state expand/collapse blok antar visit Inertia.
- Risiko:
  - Key yang terlalu luas bisa menabrak state page lain dalam browser yang sama.
- Catatan reuse lintas domain/project:
  - Cocok untuk collapse/tab/modal helper pada page Inertia yang sering melakukan partial/deferred reload.

### P-030 - On-Expand JSON Detail Widget

- Tanggal: 2026-03-08
- Status: active
- Konteks: Sebagian widget detail pada halaman Inertia hanya diperlukan ketika user membuka panel tertentu, sementara nested payload-nya terlalu berat untuk dibawa terus di response utama.
- Trigger: Ada block/panel detail dengan nested data yang tidak dibutuhkan untuk first paint tetapi tetap perlu interaksi cepat saat dibuka.
- Langkah eksekusi:
  1) Pertahankan summary/meta block di payload Inertia utama.
  2) Pindahkan nested detail ke endpoint JSON kecil yang sempit per widget/block key.
  3) Lindungi endpoint dengan auth + validasi key block yang eksplisit.
  4) Load detail hanya saat panel dibuka dan render fallback/error state yang jelas.
- Guardrail:
  - Jangan membuat endpoint dashboard generik tanpa whitelist block/widget yang didukung.
  - Jangan memindahkan authority akses ke frontend; backend tetap memverifikasi role/scope/visibility.
  - Pastikan first-load payload masih cukup untuk summary tile dan navigasi UI dasar.
- Validasi minimum:
  - Targeted test initial payload tidak lagi membawa nested detail.
  - Targeted test JSON endpoint hanya mengembalikan block yang didukung.
  - Build frontend lulus.
- Bukti efisiensi/akurasi:
  - Diterapkan pada block rincian per-desa dashboard dengan nested `per_module` yang hanya dibutuhkan saat expand.
- Risiko:
  - Naming block key yang drift dapat memutus koneksi antara payload Inertia dan endpoint detail.
- Catatan reuse lintas domain/project:
  - Cocok untuk accordions, inspector panel, atau breakdown table yang berat tetapi tidak perlu ikut payload awal.

## Annex Retrieval Guardrail

- Dokumen lampiran ini bersifat `on-demand` dan tidak masuk default pack baca harian.
- Saat butuh detail pattern, buka section yang relevan saja; hindari memuat lampiran penuh tanpa alasan teknis.
- Jika ukuran lampiran melewati `50,000` chars atau retrieval rutin membutuhkan lebih dari `3` pattern besar per sesi, pecah lampiran ini menjadi shard tematik atau rentang pattern.
