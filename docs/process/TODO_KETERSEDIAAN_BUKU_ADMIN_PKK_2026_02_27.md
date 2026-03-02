# TODO KBA26A1 Ketersediaan Buku Administrasi PKK
Tanggal: 2026-02-27  
Status: `in-progress`

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
- [x] Normalisasi label buku lintas dokumen agar konsisten dengan pedoman.

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
- [x] Definisikan kontrak field minimum per buku sebelum coding.
- [x] Definisikan boundary implementasi per buku: route, request, action/use case, repository, policy, test.
- [x] Definisikan fallback/compatibility plan untuk modul yang sudah berjalan agar tidak behavior drift.

### F. Validasi dan Gate
- [x] Tambah/rapikan test matrix untuk role-scope-area mismatch pada buku baru/yang diubah.
- [x] Jalankan regresi feature untuk akses lintas scope dan anti data leak.
- [x] Jalankan validasi print/report pada buku yang status autentiknya berubah.
- [x] Lakukan review akhir checklist sebelum menandai concern `done`.

## Validasi Keberhasilan
- [x] Tidak ada buku tanpa status ketersediaan dan status autentikasi.
- [x] Tidak ada buku tanpa penanggung jawab yang eksplisit.
- [x] Tidak ada konflik interpretasi yang belum diputuskan terhadap Rakernas X.
- [x] Backlog implementasi buku `missing` tersusun dengan urutan prioritas dan owner teknis.

## Risiko
- Risiko drift istilah antar dokumen jika normalisasi tidak dilakukan serentak.
- Risiko false-positive `verified` jika bukti visual/header belum lengkap.
- Risiko mismatch role ownership jika perubahan UI tidak diikuti guard backend.
- Risiko scope creep karena concern buku menyentuh banyak modul lintas Pokja.

## Keputusan yang Harus Dikunci
- [x] K1: Urutan prioritas implementasi buku `missing` per level.
- [x] K2: Kriteria final `verified` untuk autentikasi buku.
- [x] K3: Batas kewenangan pokja kecamatan untuk modul rekap (monitoring vs mutasi).
- [x] K4: Strategi migrasi jika ada buku yang perlu pemecahan domain/modul.

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
- `Buku Kegiatan Pokja I` (Desa): `partial` -> `verified`.
- `Buku Kegiatan Pokja III` (Desa): `partial` -> `verified`.
- Buku Notulen Rapat:
  - Level desa/kelurahan: `missing -> available` (autentikasi tetap `unverified`).
  - Level kecamatan: `missing -> available` (autentikasi tetap `unverified`).
- Buku Daftar Hadir:
  - Level desa/kelurahan: `missing -> available` (autentikasi tetap `unverified`).
  - Level kecamatan: `missing -> available` (autentikasi tetap `unverified`).
- Buku Tamu:
  - Level desa/kelurahan: `missing -> available` (autentikasi tetap `unverified`).
  - Level kecamatan: `missing -> available` (autentikasi tetap `unverified`).
- Alasan perubahan:
  - bukti autentik header 4.13 (`text-layer + visual`) dan baseline compliance PDF sudah lengkap/konsisten,
  - route resource aktif, domain module aktif, policy aktif, menu visibility aktif, dan test concern modul tersedia.

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

Owner teknis implementasi:
- Route + middleware: tim backend routing/auth scope.
- Request + Action/UseCase + Repository: tim backend domain sekretaris.
- Policy + Scope Service: tim backend authorization.
- Feature/Unit test: tim QA automation + backend owner modul.

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
- Guard query modul `activities` kini dikunci pada kombinasi `role group + level + area` khusus role `desa-pokja-i` s.d. `desa-pokja-iv` dan `kecamatan-pokja-i` s.d. `kecamatan-pokja-iv`, sehingga `pokja-i` hanya melihat detail/list kegiatan dari group-nya pada area yang sama; `kecamatan-sekretaris` pada mode kecamatan dibatasi ke data milik sendiri (`created_by` user login), sementara mode monitoring desa tetap menampilkan seluruh desa dalam wilayah kecamatan sendiri.
- Guard anti-bocor antar-pokja pada modul `activities` telah ditambah lewat feature test index + detail (satu area, role berbeda).
- Kontrak ketersediaan `Buku Kegiatan` (`activities`) kini dikunci untuk seluruh role operasional pada scope validnya, termasuk `kecamatan-pokja-i` s.d. `kecamatan-pokja-iv`; sinkronisasi backend visibility + middleware + payload menu tervalidasi test.
- Kontrak anti mismatch menu-vs-otorisasi dikunci: sidebar frontend hanya boleh menampilkan item dengan slug yang tersedia di `auth.user.moduleModes`; guard ini dikunci lewat unit test kontrak frontend.
- Guard header kolom PDF untuk `buku-notulen-rapat`, `buku-daftar-hadir`, dan `buku-tamu` sudah dikunci lewat feature test khusus.
- Baseline mapping autentik internal untuk 3 buku sekretaris inti sudah dikunci pada:
  - `docs/domain/BUKU_SEKRETARIS_INTI_AUTH_MAPPING.md`
  - sinkronisasi catatan canonical pada `docs/domain/DOMAIN_CONTRACT_MATRIX.md`.
- Setelah kontrak field terkunci, lanjut implementasi per buku dengan boundary:
  - route + request + action/use case + repository + policy + test.

### Keputusan Operasional Terkunci (K3/K4)
- K3 (monitoring vs mutasi pokja kecamatan):
  - Modul rekap lintas desa pada level kecamatan diposisikan sebagai monitoring/evaluasi (`read-only`) untuk pokja kecamatan.
  - Mutasi data sumber tetap dilakukan di level desa sesuai ownership pokja masing-masing.
  - Enforcement backend dikunci melalui `RoleMenuVisibilityService` + `EnsureModuleVisibility` + matrix test role-scope-area.
- K4 (strategi migrasi pemecahan domain/modul):
  - Fase 1: tambah modul target baru secara paralel tanpa memutus modul lama (read path tetap kompatibel).
  - Fase 2: tambah adapter normalisasi request/repository agar payload lama tetap diterima selama masa transisi.
  - Fase 3: migrasi route/menu bertahap, pertahankan alias report/print lama sampai test regresi concern hijau penuh.
  - Fase 4: hapus coupling lama hanya setelah parity test + audit data leak lintas scope dinyatakan aman.

### Kontrak Field Minimum Gelombang 2 (Siap Coding)
| Domain Target | Level | Field Minimum (di luar invariant `level`, `area_id`, `created_by`) | Owner Teknis |
| --- | --- | --- | --- |
| `evaluasi-program-pokja-i` | desa/kecamatan | `period_year`, `period_semester`, `program`, `indikator`, `target`, `realisasi`, `capaian_persen`, `evaluation_note`, `tindak_lanjut` | Backend Domain Wilayah |
| `evaluasi-program-pokja-ii` | desa/kecamatan | `period_year`, `period_semester`, `program`, `indikator`, `target`, `realisasi`, `capaian_persen`, `evaluation_note`, `tindak_lanjut` | Backend Domain Wilayah |
| `evaluasi-program-pokja-iii` | desa/kecamatan | `period_year`, `period_semester`, `program`, `indikator`, `target`, `realisasi`, `capaian_persen`, `evaluation_note`, `tindak_lanjut` | Backend Domain Wilayah |
| `evaluasi-program-pokja-iv` | desa/kecamatan | `period_year`, `period_semester`, `program`, `indikator`, `target`, `realisasi`, `capaian_persen`, `evaluation_note`, `tindak_lanjut` | Backend Domain Wilayah |
| Penguatan `program-prioritas` (Buku Program Kerja) | desa/kecamatan | `program`, `prioritas_program`, `kegiatan`, `sasaran_target`, `jadwal_bulan_1..12`, `sumber_dana_*`, `keterangan` | Backend Domain Wilayah |

### Boundary Implementasi Gelombang 2 (Mandatory)
- Route + middleware: `scope.role:{desa|kecamatan}` + `module.visibility`.
- Request: validasi canonical token periode + normalisasi boolean jadwal/sumber dana.
- UseCase/Action: hanya memuat business flow evaluasi/rencana kerja, tanpa query langsung controller.
- Repository Interface + Repository: seluruh query domain lewat boundary repository scoped `areas`.
- Policy + Scope Service: enforce ownership pokja/sekretaris sesuai level dan area.
- Inertia page mapping: frontend consume payload backend tanpa authority akses.
- Test matrix minimum:
  - feature success role/scope valid,
  - feature reject role tidak valid,
  - feature reject mismatch role-area-level,
  - unit policy/scope service,
  - anti data leak repository/use case.

## Rencana Sprint Mingguan (Eksekusi)

### Sprint 1 (P1) - Kontrak dan Bukti Canonical
- [x] Normalisasi label buku lintas dokumen agar konsisten dengan pedoman Rakernas X.
- [ ] Validasi peta header dokumen bertabel sampai `rowspan/colspan`.
- [ ] Simpan bukti validasi (text-layer + screenshot visual) dan tautkan ke dokumen mapping.
- [ ] Turunkan status ke `verified` hanya untuk buku dengan bukti lengkap.
- [x] Pastikan tidak ada modul buku di role yang tidak sesuai kontrak ownership.
- [x] Kunci keputusan K3: batas kewenangan pokja kecamatan untuk modul rekap.
- [x] Kunci keputusan K4: strategi migrasi jika perlu pemecahan domain/modul.
- [x] Definisikan kontrak field minimum per buku sebelum coding.
- [x] Definisikan boundary implementasi per buku: route, request, use case/action, repository, policy, test.
- [x] Definisikan fallback/compatibility plan agar tidak terjadi behavior drift.

Exit criteria Sprint 1:
- [ ] Semua buku bertabel target Sprint 1 memiliki bukti header valid (`rowspan/colspan`) yang terdokumentasi.
- [x] Keputusan K3/K4 berstatus terkunci.
- [x] Kontrak field + boundary implementasi untuk gelombang buku `missing` sudah final.

### Sprint 2 (P2) - Quality Gate dan Replikasi Role
- [x] Tambah/rapikan test matrix mismatch `role-scope-area` pada buku baru/diubah.
- [x] Jalankan regresi feature akses lintas scope dan anti data leak.
- [x] Jalankan validasi print/report pada buku dengan status autentik yang berubah.
- [x] Tetapkan kontrak section role dashboard baru (section aktif, source level, query key filter).
- [x] Sinkronkan mapping role ke group-mode di `RoleMenuVisibilityService` tanpa bypass scope gate.
- [x] Sinkronkan query URL dengan `sources.filter_context` untuk role yang direplikasi.
- [x] Tambah test sinkronisasi menu-vs-dashboard jika ada slug/group baru.

Exit criteria Sprint 2:
- [x] Seluruh test gate concern buku + dashboard role replication lulus.
- [x] Tidak ada temuan data leak lintas scope pada validasi regresi.
- [x] Kontrak dashboard role baru terdokumentasi dan tervalidasi test.

### Sprint 3 (P3) - Delivery Backlog Modul Missing
- [x] Implementasi modul `buku-notulen-rapat` untuk desa/kecamatan.
- [x] Implementasi modul `buku-daftar-hadir` untuk desa/kecamatan.
- [x] Implementasi modul `buku-tamu` untuk desa/kecamatan.
- [x] Tegaskan pemetaan `buku-program-kerja` agar tidak overlap domain.
- [x] Lakukan review akhir checklist concern sebelum status `done`.

Exit criteria Sprint 3:
- [x] Tidak ada buku tanpa status ketersediaan dan autentikasi.
- [x] Tidak ada buku tanpa penanggung jawab eksplisit.
- [x] Tidak ada konflik interpretasi Rakernas X yang belum diputuskan.
- [x] Backlog implementasi buku `missing` memiliki urutan prioritas dan owner teknis yang final.

## Review Akhir Checklist (2026-02-27)

Status concern saat review:
- Progress implementasi domain + security gate + dashboard gate: **terkunci**.
- Sprint 2 dan Sprint 3 exit criteria: **terpenuhi**.
- Concern belum ditutup `done` karena masih ada blocker autentikasi di Sprint 1.

Blocker tersisa sebelum concern `done`:
1. Validasi peta header dokumen bertabel sampai `rowspan/colspan` untuk buku yang masih `unverified`.
2. Bukti visual autentik (text-layer + screenshot header) belum lengkap untuk seluruh buku target Sprint 1.
3. Status autentikasi belum bisa diturunkan ke `verified` sebelum butir 1 dan 2 terpenuhi.

Rute eksekusi blocker:
- `docs/process/TODO_AUTENTIK_SEKRETARIS_INTI_2026_02_27.md`
