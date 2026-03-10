# AI Friendly Execution Playbook - Pattern Details (Annex: Governance)

Tanggal efektif: 2026-03-09  
Status: `active`  
Parent: `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`  
Shard: `governance`

Dokumen ini menyimpan detail langkah pattern governance/routing/closure agar file playbook utama tetap ringkas untuk proses routing cepat.

## 1) Detail Pattern Governance
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

### P-024 - TODO Generator Canonicalization

- Tanggal: 2026-03-02
- Status: active
- Konteks: pembuatan TODO concern manual rawan drift pada format judul, kode unik, metadata tanggal, dan nama file.
- Trigger:
  - user/AI perlu membuat TODO concern baru.
  - ditemukan drift format TODO terhadap kontrak `AGENTS.md`.
- Langkah eksekusi:
  1) jalankan `scripts/generate_todo.ps1` dengan input wajib `-Code` dan `-Title`.
  2) gunakan `-Date` bila perlu tanggal spesifik; default hari ini.
  3) gunakan `-RelatedAdr` bila concern terkait keputusan arsitektur.
  4) verifikasi hasil file berada di `docs/process/TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.

- Guardrail:
  - `Code` wajib `^[A-Z0-9]{4,8}$`.
  - generator wajib bersumber dari `docs/process/TEMPLATE_TODO_CONCERN.md`.
  - jika file sudah ada, wajib eksplisit `-Force` untuk overwrite.
- Validasi minimum:
  - dry-run menghasilkan path output sesuai kontrak.
  - file hasil generate berisi judul `# TODO <KODE_UNIK> <Judul Ringkas>`.
  - metadata `Tanggal` dan `Related ADR` terisi.
- Bukti efisiensi/akurasi:
  - mengurangi variasi format TODO antar concern dan mempercepat bootstrap dokumen eksekusi.
- Risiko:
  - judul dengan kata sangat umum bisa menghasilkan ringkasan nama file kurang deskriptif.
- Catatan reuse lintas domain/project:
  - bisa dipakai lintas project Laravel/Inertia selama pola dokumentasi TODO mengikuti kontrak serupa.

### P-026 - TTY Wrapper for Non-TTY Test Runner

- Tanggal: 2026-03-07
- Status: active
- Konteks: runner non-TTY pada environment tertentu menelan progres per-file dan membuat subset failure sulit diinspeksi saat `composer test` dikunci ke mode `--compact`.
- Trigger:
  - debugging subset test pada shell non-TTY / output buffering berat.
  - butuh progres interaktif tanpa mengubah script CI yang tetap memakai output ringkas.
- Langkah eksekusi:
  1) pertahankan script CI default (`composer test`, `composer test:full`, dst.) tetap `--compact`.
  2) sediakan wrapper project-level berbasis `script -qefc` untuk memaksa pseudo-TTY.
  3) expose wrapper lewat alias `composer test:tty`, `composer test:debug`, dan bila perlu loop per-file `composer test:tty:files`.
  4) teruskan argumen subset lewat `composer run <script> -- <args>`.

- Guardrail:
  - jangan ubah script CI existing hanya demi kebutuhan debugging lokal.
  - wrapper harus fallback ke eksekusi langsung jika utilitas `script` tidak tersedia atau environment menolak pembuatan pseudo-TTY.
  - tetap jalankan `php artisan config:clear --ansi` agar perilaku test lokal selaras dengan wrapper composer utama.
- Validasi minimum:
  - `composer run test:tty -- --help` atau subset test ringan bisa dieksekusi dari shell interaktif.
  - `composer run test:debug -- --filter=<Subset>` menampilkan trace `phpunit --debug`.
  - `composer test` tetap mempertahankan output compact untuk jalur non-interaktif/CI.
- Bukti efisiensi/akurasi:
  - mengurangi kebutuhan pindah manual ke runner ad-hoc di home directory dan menjaga jalur debug tetap terikat ke repo.
- Risiko:
  - utilitas `script` bergantung pada paket `util-linux`; tanpa paket itu, atau jika PTY diblokir sandbox, wrapper jatuh ke mode direct exec.
- Catatan reuse lintas domain/project:
  - cocok untuk repo PHP/Laravel yang memakai wrapper test ringkas di Composer tetapi tetap butuh mode inspeksi interaktif di WSL/Linux.

### P-027 - Heavy Validation Offload to Local Operator

- Tanggal: 2026-03-08
- Status: deprecated
- Konteks: pattern ini dulu dipakai saat validasi berat dianggap lebih efisien jika dijalankan operator lokal, tetapi pada praktik repo ini loop koordinasinya justru memperlambat closure concern.
- Trigger:
  - user secara eksplisit meminta offload command berat ke operator lokal.
  - runner AI mengalami blocker teknis nyata yang mencegah eksekusi validasi mandatory.
- Langkah eksekusi:
  1) AI mencoba menjalankan validasi mandatory sendiri terlebih dahulu.
  2) Hanya jika ada permintaan user atau blocker teknis, AI mengalihkan command ke operator lokal.
  3) AI menyebutkan command, tujuan validasi, dan output minimum yang harus dilaporkan.
  4) operator menjalankan command lokal dan mengirim ringkasan `pass/fail`, error utama, serta file/line relevan bila ada.
  5) jika `fail`, AI kembali ke loop `analyze -> patch -> request rerun`.
  6) jika `pass`, AI mencatat evidence tersebut pada laporan akhir atau dokumen concern bila diperlukan.

- Guardrail:
  - offload hanya fallback, bukan default.
  - offload hanya mengalihkan eksekusi command, bukan tanggung jawab AI untuk menentukan kebutuhan validasi.
  - final report tidak boleh mengklaim validasi berat lulus jika operator belum mengirim hasil eksplisit.
  - jika blocker AI sudah hilang, validasi berat kembali ke jalur default dieksekusi AI.
- Validasi minimum:
  - AI mendokumentasikan alasan offload atau blocker teknisnya.
  - AI menyebut command dan alasan validasinya secara eksplisit.
  - operator mengirim balik hasil minimal `pass/fail` + error inti bila gagal.
  - closure concern hanya dilakukan setelah hasil mandatory validation tercatat.
- Bukti efisiensi/akurasi:
  - kini dipertahankan hanya sebagai fallback untuk kasus blocker atau permintaan eksplisit user.
- Risiko:
  - loop kerja bisa macet jika operator hanya memberi status umum tanpa potongan error yang cukup.
- Catatan reuse lintas domain/project:
  - jangan dijadikan default pada repo yang closure concern-nya lebih cepat jika AI menjalankan validasi langsung.

### P-032 - Markdown Context Space Budgeting

- Tanggal: 2026-03-09
- Status: active
- Konteks: repo sudah memiliki thinning registry dan archive TODO, tetapi belum memiliki angka budget yang menjelaskan seberapa besar markdown aktif yang aman untuk dimuat bersama dalam satu sesi AI.
- Trigger:
  - ada audit/optimasi dokumen governance markdown.
  - file governance aktif mulai membesar dan berisiko membebani routing AI.
  - model/context runner berubah sehingga space markdown perlu dievaluasi ulang.
- Langkah eksekusi:
  1) ukur file governance aktif dengan `wc -c` atau setara.
  2) hitung `estimated_tokens = ceil(chars / 4)` untuk tiap file dan pack baca aktif.
  3) hitung `ideal_context_window = ceil(pack_tokens / 0.65)` agar reserve `35%` tetap tersedia.
  4) tetapkan soft cap per artefak aktif dan jalankan thinning/archive jika terlampaui.
  5) jika `OPERATIONAL_VALIDATION_LOG.md` melewati soft cap lebih dari `10%`, tipiskan file index menjadi snapshot concern `planned/in-progress` + pointer closure; pindahkan detail concern `done` ke arsip periodik aktif.
  6) jika context window AI naik, ekspansi space mengikuti urutan `validation log -> thin registry -> playbook summary -> concern pack tambahan/ADR`.

- Guardrail:
  - jangan memperbesar `AGENTS.md` hanya karena context window model meningkat.
  - budget dihitung pada level pack baca aktif, bukan hanya file tunggal.
  - jika file melewati soft cap, utamakan pindah ke annex/arsip daripada menambah ringkasan panjang di file utama.
  - `OPERATIONAL_VALIDATION_LOG.md` tidak boleh menjadi tempat penyimpanan detail closure concern `done`; file itu hanya index aktif.
  - setiap perubahan budget atau urutan load order wajib sinkron ke `AGENTS.md` dan `AI_SINGLE_PATH_ARCHITECTURE.md`.
- Validasi minimum:
  - baseline file aktif tercatat dengan ukuran `chars` dan estimasi token.
  - dokumen budget context space tersedia dan direferensikan dokumen governance utama.
  - registry/log concern sinkron dengan perubahan concern doc-only ini.
- Bukti efisiensi/akurasi:
  - menurunkan ambiguity saat memutuskan kapan dokumen harus di-thin, diarsip, atau boleh diperluas karena model AI yang dipakai lebih besar.
- Risiko:
  - heuristic `chars/4` tidak identik dengan tokenizer model tertentu.
  - tanpa disiplin update baseline, angka budget bisa drift dari kondisi repo aktual.
- Catatan reuse lintas domain/project:
  - cocok untuk project yang governance AI-nya sudah memakai banyak dokumen markdown dan membutuhkan batas pertumbuhan yang bisa diaudit.

### P-033 - Commit by Concern at Closure

- Tanggal: 2026-03-09
- Status: active
- Konteks: pada repo ini user mendelegasikan `git commit` ke AI sebagai bagian dari rangkaian closure concern, tetapi tetap menahan kontrol `git push`.
- Trigger:
  - satu concern sudah tervalidasi penuh atau cukup untuk closure sesuai quality gate.
  - working tree bisa dipisahkan bersih per concern.
- Langkah eksekusi:
  1) audit `git status` dan petakan file concern aktif.
  2) pastikan validasi mandatory concern sudah tercatat.
  3) stage hanya file concern aktif.
  4) buat commit message yang mewakili boundary concern.
  5) laporkan hash commit dan residual working tree yang sengaja tidak ikut.
- Guardrail:
  - jangan commit file concern lain yang kebetulan sedang berubah di working tree.
  - jangan menganggap izin commit sebagai izin `git push`.
  - jika boundary file tidak jelas, tunda commit atau pecah concern lebih dulu.
- Validasi minimum:
  - concern sudah lulus validation ladder yang relevan.
  - audit `git status --short` dilakukan sebelum commit.
  - laporan final menyebut commit hash dan file utama yang masuk.
- Bukti efisiensi/akurasi:
  - mengurangi loop bolak-balik “implement -> validasi -> minta commit” pada concern yang sudah stabil.
- Risiko:
  - commit terlalu dini jika validation ladder belum benar-benar tertutup.
  - working tree campuran bisa menyebabkan commit boundary bocor jika audit tidak disiplin.
- Catatan reuse lintas domain/project:
  - cocok untuk repo kolaboratif di mana AI dipercaya menutup batch kerja, tetapi distribusi ke remote tetap dikendalikan manusia.
