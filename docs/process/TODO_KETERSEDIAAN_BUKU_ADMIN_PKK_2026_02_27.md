# TODO Ketersediaan Buku Administrasi PKK (2026-02-27)

## Konteks
- Concern aktif: memastikan seluruh buku administrasi PKK tersedia, tervalidasi autentik, dan ditempatkan pada penanggung jawab yang tepat.
- Dokumen canonical acuan:
  - `docs/domain/dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md`
  - `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- Sumber primer interpretasi:
  - `PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md`
  - `docs/referensi/Rakernas X.pdf`

## Target Hasil
- Tersedia baseline status ketersediaan buku per level (`available/planned/missing`) yang konsisten.
- Setiap buku memiliki status autentikasi (`verified/partial/unverified`) berbasis bukti.
- Penempatan penanggung jawab buku (Sekretaris/Pokja I-IV) selaras dengan kontrak domain.
- Tersusun backlog implementasi prioritas untuk buku yang masih `missing`/`partial`.

## Ruang Lingkup
- Level `desa/kelurahan`.
- Level `kecamatan`.
- Concern: ketersediaan buku, autentikasi format buku, role ownership buku.

## Langkah Eksekusi

### A. Baseline dan Inventaris
- [x] Sinkronkan daftar buku aktif dari dokumen canonical ke snapshot implementasi terbaru (route + menu + policy).
- [x] Tandai buku `available/planned/missing` berdasarkan bukti implementasi aktual.
- [x] Tandai buku yang masih overlap domain agar tidak terjadi duplikasi modul.

### B. Validasi Interpretasi Rakernas X
- [x] Pastikan setiap entri buku punya referensi lampiran/istilah Rakernas X.
- [x] Jika ada mismatch istilah, catat deviasi dan kunci keputusan fallback ke sumber primer.
- [ ] Normalisasi label buku lintas dokumen agar konsisten dengan pedoman.

### C. Autentikasi Buku
- [x] Prioritaskan buku status `partial/unverified` untuk validasi autentik bertahap.
- [ ] Untuk buku bertabel: validasi peta header sampai `rowspan/colspan`.
- [ ] Simpan bukti validasi (text-layer + screenshot visual) dan tautkan ke dokumen mapping terkait.
- [ ] Turunkan status ke `verified` hanya setelah bukti lengkap dan konsisten.

### D. Penanggung Jawab Buku
- [x] Audit matriks penanggung jawab buku per level (sekretaris vs pokja).
- [x] Validasi mode akses (`read-write` vs `read-only`) terhadap tanggung jawab buku.
- [x] Pastikan tidak ada modul buku yang ditempatkan pada role yang tidak sesuai kontrak.

### E. Rencana Implementasi Gap
- [x] Susun prioritas implementasi buku `missing` (gelombang 1: sekretaris inti, gelombang 2: buku pokja pendukung).
- [ ] Definisikan kontrak field minimum per buku sebelum coding.
- [ ] Definisikan boundary implementasi per buku: route, request, action/use case, repository, policy, test.
- [ ] Definisikan fallback/compatibility plan untuk modul yang sudah berjalan agar tidak behavior drift.

### F. Validasi dan Gate
- [x] Tambah/rapikan test matrix untuk role-scope-area mismatch pada buku baru/yang diubah.
- [x] Jalankan regresi feature untuk akses lintas scope dan anti data leak.
- [x] Jalankan validasi print/report pada buku yang status autentiknya berubah.
- [ ] Lakukan review akhir checklist sebelum menandai concern `done`.

## Validasi Keberhasilan
- [ ] Tidak ada buku tanpa status ketersediaan dan status autentikasi.
- [ ] Tidak ada buku tanpa penanggung jawab yang eksplisit.
- [ ] Tidak ada konflik interpretasi yang belum diputuskan terhadap Rakernas X.
- [ ] Backlog implementasi buku `missing` tersusun dengan urutan prioritas dan owner teknis.

## Risiko
- Risiko drift istilah antar dokumen jika normalisasi tidak dilakukan serentak.
- Risiko false-positive `verified` jika bukti visual/header belum lengkap.
- Risiko mismatch role ownership jika perubahan UI tidak diikuti guard backend.
- Risiko scope creep karena concern buku menyentuh banyak modul lintas Pokja.

## Keputusan yang Harus Dikunci
- [x] K1: Urutan prioritas implementasi buku `missing` per level.
- [x] K2: Kriteria final `verified` untuk autentikasi buku.
- [ ] K3: Batas kewenangan pokja kecamatan untuk modul rekap (monitoring vs mutasi).
- [ ] K4: Strategi migrasi jika ada buku yang perlu pemecahan domain/modul.

## Output Wajib Tiap Update
- [x] Daftar buku yang berubah status (`available/planned/missing`, `verified/partial/unverified`).
- [x] File terdampak (domain/process/security/ui/test) dan alasan perubahan.
- [x] Bukti validasi yang digunakan.
- [x] Dampak ke rencana implementasi gelombang berikutnya.

## Progress Eksekusi (2026-02-27)

### Bukti Validasi yang Digunakan
- Snapshot implementasi route modul: `routes/web.php`.
- Snapshot ownership dan mode akses role: `app/Domains/Wilayah/Services/RoleMenuVisibilityService.php`.
- Snapshot canonical status buku: `docs/domain/dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md`.
- Snapshot kontrak domain/lampiran: `docs/domain/DOMAIN_CONTRACT_MATRIX.md`.

### Daftar Buku yang Berubah Status (Update Dokumen)
- Buku Notulen Rapat:
  - Level desa/kelurahan: `missing -> available` (autentikasi tetap `unverified`).
  - Level kecamatan: `missing -> available` (autentikasi tetap `unverified`).
- Buku Daftar Hadir:
  - Level desa/kelurahan: `missing -> available` (autentikasi tetap `unverified`).
  - Level kecamatan: `missing -> available` (autentikasi tetap `unverified`).
- Buku Tamu:
  - Level desa/kelurahan: `missing -> available` (autentikasi tetap `unverified`).
  - Level kecamatan: `missing -> available` (autentikasi tetap `unverified`).
- Bukti implementasi: route resource aktif, domain module aktif, policy aktif, menu visibility aktif, dan test concern modul tersedia.

### Temuan Audit Baseline
- Modul inti buku sekretaris/pokja desa-kecamatan sudah tersedia pada route utama (`resource` + `report/pdf`).
- Gelombang 1 sekretaris inti (`buku-notulen-rapat`, `buku-daftar-hadir`, `buku-tamu`) sudah tersedia sebagai modul dedicated.
- Status interpretasi Rakernas X sudah terkunci pada dokumen canonical + matrix domain.

### Prioritas Implementasi Gap (K1 Dikunci)
- Gelombang 1 (sekretaris inti):
  - `buku-notulen-rapat` (`done`)
  - `buku-daftar-hadir` (`done`)
  - `buku-tamu` (`done`)
- Gelombang 2 (pokja pendukung):
  - `buku-evaluasi-program` per pokja (I-IV) dengan kontrak data minimum.
  - penguatan buku rencana kerja pokja agar tidak overlap dengan domain kegiatan.

### Kriteria Final Verified (K2 Dikunci)
- Status `verified` hanya jika:
  1. Referensi lampiran Rakernas X jelas.
  2. Header tabel tervalidasi sampai `rowspan/colspan` untuk dokumen bertabel.
  3. Bukti text-layer + screenshot visual tersedia dan terdokumentasi.
  4. Kontrak field/report sinkron dengan bukti autentik.

### Dampak ke Gelombang Berikutnya
- Pemetaan `buku-program-kerja` telah dikunci pada ownership `sekretaris-tpk` agar tidak overlap dengan domain `pokja-iv`.
- Test matrix mismatch `role-scope-area` untuk `program-prioritas` telah ditambah (desa + kecamatan) dan lolos regresi suite.
- Validasi print/report untuk `buku-notulen-rapat`, `buku-daftar-hadir`, dan `buku-tamu` telah ditambah dan lolos pada scope desa/kecamatan.
- Guard ownership modul buku sekretaris terhadap role pokja telah dikunci lewat test unit mapping + middleware feature.
- Kontrak dashboard role-aware kini aktif untuk section sekretaris:
  - section 1 (ringkasan sekretaris),
  - section 2 (ringkasan pokja level aktif, filter `section2_group`),
  - section 3 (ringkasan pokja per desa untuk scope kecamatan, filter `section3_group`),
  - section 4 (rincian Pokja I per desa saat `section3_group=pokja-i`).
- Sinkronisasi query URL ke `sources.filter_context` pada blok dashboard sekretaris telah divalidasi lewat feature test.
- Sinkronisasi menu-vs-dashboard pada level group dikunci dengan unit test agar setiap group utama punya minimal satu slug coverage.
- Role-menu mapping kini menolak `scope` mismatch untuk role non-super-admin di service boundary (anti bypass scope gate).
- Setelah kontrak field terkunci, lanjut implementasi per buku dengan boundary:
  - route + request + action/use case + repository + policy + test.

## Rencana Sprint Mingguan (Eksekusi)

### Sprint 1 (P1) - Kontrak dan Bukti Canonical
- [ ] Normalisasi label buku lintas dokumen agar konsisten dengan pedoman Rakernas X.
- [ ] Validasi peta header dokumen bertabel sampai `rowspan/colspan`.
- [ ] Simpan bukti validasi (text-layer + screenshot visual) dan tautkan ke dokumen mapping.
- [ ] Turunkan status ke `verified` hanya untuk buku dengan bukti lengkap.
- [x] Pastikan tidak ada modul buku di role yang tidak sesuai kontrak ownership.
- [ ] Kunci keputusan K3: batas kewenangan pokja kecamatan untuk modul rekap.
- [ ] Kunci keputusan K4: strategi migrasi jika perlu pemecahan domain/modul.
- [ ] Definisikan kontrak field minimum per buku sebelum coding.
- [ ] Definisikan boundary implementasi per buku: route, request, use case/action, repository, policy, test.
- [ ] Definisikan fallback/compatibility plan agar tidak terjadi behavior drift.

Exit criteria Sprint 1:
- [ ] Semua buku bertabel target Sprint 1 memiliki bukti header valid (`rowspan/colspan`) yang terdokumentasi.
- [ ] Keputusan K3/K4 berstatus terkunci.
- [ ] Kontrak field + boundary implementasi untuk gelombang buku `missing` sudah final.

### Sprint 2 (P2) - Quality Gate dan Replikasi Role
- [x] Tambah/rapikan test matrix mismatch `role-scope-area` pada buku baru/diubah.
- [x] Jalankan regresi feature akses lintas scope dan anti data leak.
- [x] Jalankan validasi print/report pada buku dengan status autentik yang berubah.
- [x] Tetapkan kontrak section role dashboard baru (section aktif, source level, query key filter).
- [x] Sinkronkan mapping role ke group-mode di `RoleMenuVisibilityService` tanpa bypass scope gate.
- [x] Sinkronkan query URL dengan `sources.filter_context` untuk role yang direplikasi.
- [x] Tambah test sinkronisasi menu-vs-dashboard jika ada slug/group baru.

Exit criteria Sprint 2:
- [ ] Seluruh test gate concern buku + dashboard role replication lulus.
- [ ] Tidak ada temuan data leak lintas scope pada validasi regresi.
- [ ] Kontrak dashboard role baru terdokumentasi dan tervalidasi test.

### Sprint 3 (P3) - Delivery Backlog Modul Missing
- [x] Implementasi modul `buku-notulen-rapat` untuk desa/kecamatan.
- [x] Implementasi modul `buku-daftar-hadir` untuk desa/kecamatan.
- [x] Implementasi modul `buku-tamu` untuk desa/kecamatan.
- [x] Tegaskan pemetaan `buku-program-kerja` agar tidak overlap domain.
- [ ] Lakukan review akhir checklist concern sebelum status `done`.

Exit criteria Sprint 3:
- [ ] Tidak ada buku tanpa status ketersediaan dan autentikasi.
- [ ] Tidak ada buku tanpa penanggung jawab eksplisit.
- [ ] Tidak ada konflik interpretasi Rakernas X yang belum diputuskan.
- [ ] Backlog implementasi buku `missing` memiliki urutan prioritas dan owner teknis yang final.
