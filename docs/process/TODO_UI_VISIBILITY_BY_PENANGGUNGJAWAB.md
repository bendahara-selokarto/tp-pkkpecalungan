# TODO UVPJ23R1 UI Visibility by Penanggung Jawab

Tanggal: 2026-02-23  
Status: `implemented` (`historical-e2e`, sinkronisasi concern sidebar aktif ada di `docs/process/TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md`)

## Marker Keterbaruan

- Todo Code: `UVPJ23R1`
- Dokumen ini adalah catatan implementasi E2E historis.
- Untuk matrix penanggung jawab buku lintas desa/kecamatan yang berlaku sebagai acuan domain aktif, gunakan:
  - `docs/domain/dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md`
- Untuk concern eksperimen UI visibility saat ini, acuan wajib terbaru adalah:
  - `docs/process/TODO_UI_MENU_VISIBILITY_ALIGNMENT_2026_02_25.md`
- Jika ada analisa yang mencampur concern eksperimen UI dengan dokumen historis ini, wajib ikuti acuan terbaru di atas.

## Scope Sinkronisasi Sidebar

- Dokumen ini tidak menjadi acuan aktif untuk penataan urutan/group/copy menu sidebar eksperimen.
- Fungsi dokumen ini adalah bukti implementasi kontrak backend + UI + test pada fase E2E.
- Untuk keputusan UI sidebar yang masih berubah cepat, ikuti `UVM25R1`.

## Konteks

- Saat ini grouping menu domain sudah ada per organisasi (`Sekretaris TPK`, `Pokja I-IV`) di:
  - `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
  - `resources/js/Layouts/DashboardLayout.vue`
- Namun visibilitas menu masih berbasis `scope` (`desa`/`kecamatan`), belum berbasis `penanggung jawab` (role spesifik sekretaris/pokja).
- Guard backend saat ini juga masih berbasis `scope.role:{desa|kecamatan}` + validasi area-level.
- Keputusan produk terbaru sesi ini: tidak menggunakan role `admin` lagi sebagai model akses operasional.
- Keputusan produk terbaru sesi ini: tidak menggunakan role `bendahara`; administrasi keuangan dibebankan ke `sekretaris`.

## Target Hasil

- User hanya melihat menu domain yang menjadi tanggung jawab role-nya.
- Visibilitas UI tetap ditentukan backend (frontend bukan authority).
- Tidak terjadi drift dengan kontrak domain canonical (`PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md` dan `DOMAIN_CONTRACT_MATRIX`).
- Sekretaris PKK mendapat akses:
  - penuh (`read/write`) pada group `Sekretaris TPK`,
  - baca saja (`read-only`) pada group `Pokja I-IV`.
- Akses `Monitoring Kecamatan` bersifat `read-only`.

## Matrix Rencana Visibility (Role -> Group Menu + Mode)

- `kecamatan-sekretaris`:
  - `Sekretaris TPK` -> `read/write`
  - `Pokja I` -> `read-only`
  - `Pokja II` -> `read-only`
  - `Pokja III` -> `read-only`
  - `Pokja IV` -> `read-only`
  - `Monitoring Kecamatan` -> `read-only`
- `desa-sekretaris`:
  - `Sekretaris TPK` -> `read/write`
  - `Pokja I` -> `read-only`
  - `Pokja II` -> `read-only`
  - `Pokja III` -> `read-only`
  - `Pokja IV` -> `read-only`
- `kecamatan-pokja-i` -> `Pokja I` (`read/write`)
- `kecamatan-pokja-ii` -> `Pokja II` (`read/write`)
- `kecamatan-pokja-iii` -> `Pokja III` (`read/write`)
- `kecamatan-pokja-iv` -> `Pokja IV` (`read/write`)
- `desa-pokja-i` -> `Pokja I` (`read/write`)
- `desa-pokja-ii` -> `Pokja II` (`read/write`)
- `desa-pokja-iii` -> `Pokja III` (`read/write`)
- `desa-pokja-iv` -> `Pokja IV` (`read/write`)

Catatan kompatibilitas (rencana):
- `super-admin` tetap melihat menu administratif (`Manajemen User`) + bypass gate.
- `admin-desa`/`admin-kecamatan` tidak dipakai untuk model akses baru.
- Jika masih ada data user legacy dengan role `admin-*`, wajib migrasi role sebelum enforcement final visibility.
- Role `desa-bendahara`/`kecamatan-bendahara` dihapus dari model role aktif dan dimigrasikan ke role sekretaris sesuai scope-area.

## Langkah Eksekusi (Status)

- [x] `R1` Kunci kontrak role->menu di dokumen domain:
  - tambah tabel `Role Responsibility Matrix + Access Mode` pada dokumen canonical arsitektur/domain.
  - tetapkan aturan user multi-role (rekomendasi: union dari seluruh role aktif).
- [x] `R2` Tambah matrix teknis backend:
  - buat komponen matrix tunggal (mis. `RoleMenuVisibilityMatrix`) untuk mapping role ke group key + mode (`read-only` / `read/write`).
  - pastikan selaras dengan `RoleScopeMatrix`.
- [x] `R3` Tambah resolver visibility backend:
  - backend menghasilkan payload menu-domain terfilter per user (scope + role + valid area).
  - injeksikan ke Inertia shared props (single source untuk UI), termasuk metadata mode akses per item.
- [x] `R4` Ubah UI consume payload backend:
  - `DashboardLayout.vue` tidak lagi menampilkan seluruh group by scope.
  - render hanya group/item yang diizinkan payload backend.
  - tandai menu `read-only` secara eksplisit pada UI (badge/indikator).
  - implementasi `read-only` untuk sekretaris pada group `Pokja I-IV` dilakukan dengan menyembunyikan tombol aksi mutasi (`create`, `update`, `delete`) pada halaman index/detail/form terkait.
- [x] `R5` Hardening backend (wajib setelah UI visibility):
  - tambah guard akses modul berbasis responsibility + mode agar direct URL tidak bypass.
  - kemampuan minimum:
    - `read-only`: hanya `index/show/report/print`,
    - `read/write`: tambah `create/store/edit/update/destroy` sesuai policy domain.
- [x] `R6` Migrasi role legacy:
  - bersihkan role `admin-desa/admin-kecamatan` dari user aktif.
  - bersihkan role `desa-bendahara/kecamatan-bendahara` dari user aktif.
  - map ke role sekretaris/pokja yang sesuai area dan fungsi.
- [x] `R7` Dokumentasi & changelog:
  - update `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md` jika pattern baru dipakai lintas modul.
  - update laporan security/audit policy setelah rollout.

## Validasi yang Wajib Saat Implementasi

- [x] Unit test matrix role->menu + access mode (semua role target + super-admin).
- [x] Feature test payload menu Inertia:
  - role sekretaris lihat `Sekretaris TPK` (`read/write`) + `Pokja I-IV` (`read-only`).
  - role pokja hanya lihat group pokja terkait (`read/write`).
  - role kecamatan yang berhak monitoring melihat `Monitoring Kecamatan` (`read-only`).
  - multi-role menerima union group.
- [x] Feature test anti-bypass URL:
  - role pokja tidak bisa akses route modul di luar tanggung jawab.
  - role dengan mode `read-only` ditolak pada endpoint mutasi (`store/update/destroy`).
  - mismatch role-area-level tetap 403.
- [x] Feature/UI test visibilitas aksi:
  - pada sekretaris di modul `Pokja I-IV`, tombol `Tambah/Ubah/Hapus` tidak muncul.
  - pada sekretaris di modul `Sekretaris TPK`, tombol aksi mutasi tetap muncul sesuai policy.
- [x] Regression test super-admin flow (`users` management) tetap aman.
- [x] Jalankan `php artisan test` full sebelum final merge.

## Risiko

- Risiko UX: user multi-role bisa bingung jika group bertambah sesuai union role.
- Risiko migrasi: user legacy `admin-*` gagal dipetakan jika data area/role historis tidak bersih.
- Risiko migrasi: user legacy `bendahara` gagal dipetakan ke sekretaris jika data area/role historis tidak bersih.
- Risiko keamanan: jika berhenti di UI-only, URL langsung masih bisa diakses (wajib hardening backend).
- Risiko drift dokumen: mapping role-menu tidak sinkron dengan domain matrix/sidebar plan.

## Keputusan yang Perlu Dikunci Sebelum Implementasi

- [x] `K1` Aturan multi-role: `union`.
- [x] `K2` `sekretaris` memiliki akses ke tanggung jawabnya + `Pokja I-IV` dalam mode `read-only`.
- [x] `K3` Role `admin-desa/admin-kecamatan` dihentikan dari model akses baru; dilakukan migrasi role legacy.
- [x] `K4` `Monitoring Kecamatan` bersifat `read-only`.
- [x] `K5` Tidak ada role `bendahara`; administrasi keuangan dibebankan ke `sekretaris`.
- [x] `K6` Untuk mode `read-only` sekretaris (akses Pokja I-IV), UI diimplementasikan dengan menyembunyikan tombol `create/update/delete`.
- [x] `K7` Penegasan akhir aktor monitoring:
  - opsi A: hanya `kecamatan-sekretaris`,
  - opsi B: seluruh role kecamatan,
  - opsi C: subset role kecamatan tertentu.

## Catatan Implementasi

- Matrix role->group->mode diimplementasikan pada `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`.
- Guard anti bypass URL diimplementasikan pada middleware `module.visibility`:
  - `app/Http/Middleware/EnsureModuleVisibility.php`
  - registrasi alias di `bootstrap/app.php`
  - diterapkan pada route group `desa` dan `kecamatan` di `routes/web.php`.
- Payload visibility backend di-share ke Inertia melalui `app/Http/Middleware/HandleInertiaRequests.php`.
- Konsumsi payload + indikator `RO` + hide aksi mutasi UI read-only di `resources/js/Layouts/DashboardLayout.vue`.
- Migrasi data role legacy (`R6`) dijalankan melalui `database/seeders/MigrateLegacyRoleAssignmentsSeeder.php` dan dipanggil dari `database/seeders/DatabaseSeeder.php`.
- Aturan migrasi:
  - `admin-desa`/`desa-bendahara` -> `desa-sekretaris`.
  - `admin-kecamatan`/`kecamatan-bendahara` -> `kecamatan-sekretaris`.
  - Jika `scope` user valid (`desa`/`kecamatan`), target role diprioritaskan mengikuti `scope` untuk mencegah drift.

## Output yang Diharapkan Setelah Implementasi Nanti

- Sidebar domain tampil sesuai tanggung jawab role.
- Endpoint/domain di luar tanggung jawab role tertolak dari backend.
- Endpoint mutasi pada area `read-only` tertolak dari backend.
- Kontrak domain + policy/scope audit terbarui dan lulus test matrix.
