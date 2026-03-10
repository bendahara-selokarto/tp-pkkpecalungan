# AI Friendly Execution Playbook - Pattern Details (Annex: Runtime)

Tanggal efektif: 2026-03-09  
Status: `active`  
Parent: `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`  
Shard: `runtime`

Dokumen ini menyimpan detail langkah pattern runtime/auth/fetch agar file playbook utama tetap ringkas untuk proses routing cepat.

## 1) Detail Pattern Runtime
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

### P-031 - Fetch Failure Runtime Telemetry Hook

- Tanggal: 2026-03-09
- Status: active
- Konteks: setelah UI menambah fetch asinkron baru di luar lifecycle Inertia utama, kegagalan request tidak cukup hanya tampil sebagai empty/error state lokal; perlu masuk ke telemetry runtime agar bisa diaudit.
- Trigger:
  - halaman menambah fetch manual `fetch()` atau axios di level komponen untuk widget/detail async.
  - failure request berpotensi tersembunyi dari jalur `window.error`, `unhandledrejection`, atau handler global Vue.
- Langkah eksekusi:
  1) sediakan helper global yang tetap mengarah ke endpoint runtime error existing.
  2) saat fetch async gagal, kirim telemetry dengan source yang sempit per concern/widget.
  3) pertahankan user-facing fallback agar UX tidak tergantung telemetry.
  4) catat concern pilot dan hasil validasinya pada TODO/log concern terkait.
- Guardrail:
  - telemetry tidak boleh memblokir UI atau menjadi hard dependency render.
  - source telemetry harus cukup sempit agar mudah ditelusuri, jangan pakai label generik untuk semua fetch.
  - jangan kirim payload detail sensitif; cukup message, source, dan URL jalur existing.
- Validasi minimum:
  - regression concern async fetch tetap hijau.
  - endpoint runtime error existing tetap lulus.
  - build frontend lulus.
- Bukti efisiensi/akurasi:
  - menutup blind spot observability untuk widget dashboard on-expand yang gagal fetch tetapi tetap menampilkan fallback lokal.
- Risiko:
  - noise log meningkat jika source terlalu umum atau retry tidak dibatasi.
- Catatan reuse lintas domain/project:
  - cocok untuk tabel detail, inspector panel, atau widget accordion async yang tidak memakai Inertia visit penuh.
