# TODO SKC0201 Roadmap Concern Sekretaris Kecamatan

## Konteks
- Baseline pattern sudah dikunci pada commit `339275e` (concern `activities`):
  - mode `kecamatan` = data milik sendiri,
  - mode `desa` = monitoring seluruh desa dalam kecamatan sendiri,
  - monitoring tetap `read-only`.
- Pattern reusable juga sudah tercatat di playbook sebagai `P-020`.
- Permintaan saat ini: rencanakan concern lain dengan prioritas eksekusi untuk role `kecamatan-sekretaris` terlebih dahulu.

## Target Hasil
- Tersusun roadmap implementasi concern lintas modul untuk `kecamatan-sekretaris` dengan urutan prioritas yang jelas.
- Setiap concern baru mengikuti kontrak tunggal mode data (`kecamatan` vs `desa monitoring`) tanpa drift akses.
- Setiap concern memiliki gate validasi backend + frontend + test sebelum dinyatakan selesai.

## Scope Awal
- Role fokus: `kecamatan-sekretaris`.
- Group akses fokus:
  - `sekretaris-tpk` (`read-write`)  
    Keterangan: `TPK` = `Tim Penggerak PKK`.
  - `monitoring` (`read-only`)
  - `pokja-i..iv` (`read-only`) sebagai kandidat monitoring lintas desa jika concern membutuhkan.

## Status Eksekusi (2026-02-28)
- Implementasi backend concern sekretaris kecamatan lintas modul `sekretaris-tpk` sudah diterapkan:
  - mode `kecamatan` daftar difilter ke data milik user login untuk role `kecamatan-sekretaris` (`created_by = user_id`),
  - role lain (mis. `admin-kecamatan`) tidak berubah kontraknya.
- Implementasi mode monitoring desa (`read-only`) tetap berlaku pada concern `activities` sesuai baseline commit `339275e`.
- Implementasi mode monitoring desa (`read-only`) diperluas ke concern `arsip` melalui jalur `arsip` -> `desa-arsip` dengan toggle sekretaris kecamatan.
- Validasi selesai:
  - targeted feature tests concern lintas modul sekretaris kecamatan,
  - full suite `php artisan test` (hijau).

## Rencana Eksekusi

### A. Audit Baseline Concern
- [x] Bentuk matriks modul `kecamatan-sekretaris` dari `RoleMenuVisibilityService` (slug modul, mode akses, route index, route show, mutasi).
- [x] Klasifikasikan tiap modul ke salah satu tipe:
  - Tipe A: single-scope (`kecamatan` saja, tanpa monitoring desa).
  - Tipe B: dual-scope (`kecamatan` + `desa monitoring`).
  - Tipe C: monitoring-only (tanpa mutasi).
- [x] Kunci daftar concern prioritas gelombang 1 khusus `kecamatan-sekretaris`.

### B. Implementasi Concern Gelombang 1 (Sekretaris Kecamatan)
- [x] Terapkan kontrak `P-020` pada concern yang terklasifikasi Tipe B:
  - UI toggle radio `Kecamatan (default)` dan `Desa (Monitoring)`.
  - Backend filter mode `kecamatan` ke data milik user login untuk `kecamatan-sekretaris`.
  - Backend filter mode `desa` ke seluruh desa dalam parent kecamatan user login.
- [x] Pastikan mode monitoring selalu `read-only` pada middleware/module visibility.
- [x] Pastikan tidak ada bypass mutasi via URL langsung pada mode monitoring.

Catatan implementasi gelombang 1:
- Concern Tipe B aktif saat ini: `activities` (commit `339275e`) dan `arsip` (hardening akses 2026-02-28).
- Concern `sekretaris-tpk` non-`activities` saat ini berjalan sebagai Tipe A; implementasi difokuskan pada filter mode `kecamatan` (data milik sendiri) untuk role `kecamatan-sekretaris`.

### C. Implementasi Concern Gelombang 2 (Pokja Read-Only untuk Sekretaris Kecamatan)
- [ ] Audit concern pokja (`pokja-i..iv`) yang perlu pola monitoring lintas desa untuk sekretaris kecamatan.
- [ ] Untuk concern yang disetujui, terapkan pola list monitoring tanpa membuka akses mutasi.
- [ ] Sinkronkan payload visibilitas agar UI hanya menampilkan aksi sesuai mode.

### D. Validasi Wajib per Concern
- [x] Feature test mode `kecamatan`: hanya data sesuai kontrak concern.
- [x] Feature test mode `desa`: hanya data desa dalam kecamatan sendiri.
- [x] Feature test anti data leak untuk data di luar kecamatan.
- [x] Feature test/HTTP test anti bypass mutasi pada mode `read-only`.
- [x] Targeted suite concern + `php artisan test` penuh saat perubahan signifikan.

### E. Doc-Hardening dan Copywriting
- [x] Sinkronkan dokumen domain/process jika kontrak concern berubah.
- [x] Normalisasi istilah UI user-facing untuk mode:
  - `Kecamatan`
  - `Desa (Monitoring)`
- [x] Catat keputusan final concern per modul agar reusable di task berikutnya.

## Validasi Keberhasilan
- [x] Tidak ada concern `kecamatan-sekretaris` yang memakai toggle mode tanpa kontrak backend.
- [x] Tidak ada concern monitoring desa yang membuka endpoint mutasi.
- [x] Tidak ada mismatch antara payload visibilitas, route middleware, dan aksi UI.
- [x] Semua test concern yang terdampak hijau.

## Risiko
- Risiko drift kontrak jika toggle UI ditambah tanpa perubahan query backend.
- Risiko data leak lintas kecamatan jika filter parent area tidak konsisten.
- Risiko regressi akses jika mode `read-only` tidak ditegakkan di middleware.
- Risiko dokumentasi tidak sinkron jika perubahan concern tidak diikuti doc-hardening.

## Keputusan yang Perlu Dikunci
- [x] K1: Daftar modul gelombang 1 untuk `kecamatan-sekretaris` yang wajib dual-scope.
- [x] K2: Modul mana yang tetap single-scope dan tidak memakai monitoring desa.
- [x] K3: Kontrak default mode pada halaman list concern (`kecamatan` sebagai default).
- [x] K4: Paket test minimum yang wajib lulus sebelum concern dinyatakan selesai.

Keputusan final terkunci:
- K1: Dual-scope wajib untuk `activities` (`kecamatan/activities` + `kecamatan/desa-activities`) dengan monitoring `read-only`.
- K2: Modul `sekretaris-tpk` non-`activities` tetap single-scope saat ini, namun mode `kecamatan` untuk role `kecamatan-sekretaris` difilter ke data milik sendiri (`created_by`).
- K3: Default list mode tetap `kecamatan`.
- K4: Minimum validasi: targeted feature tests concern terdampak + full suite `php artisan test`.

## Output Setiap Eksekusi Concern
- [ ] Ringkasan perubahan (backend, frontend, middleware, test).
- [ ] Daftar file terdampak dan alasan.
- [ ] Hasil validasi test.
- [ ] Status keputusan K1-K4 (tetap/berubah) setelah concern dieksekusi.
