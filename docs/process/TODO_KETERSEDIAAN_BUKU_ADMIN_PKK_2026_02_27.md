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
- Alasan perubahan: bukti autentik header 4.13 (`text-layer + visual`) dan baseline compliance PDF sudah lengkap/konsisten.

### Temuan Audit Baseline
- Modul inti buku sekretaris/pokja desa-kecamatan sudah tersedia pada route utama (`resource` + `report/pdf`).
- Buku yang belum tersedia sebagai modul dedicated masih konsisten dengan baseline `missing`:
  - Buku Notulen Rapat
  - Buku Daftar Hadir
  - Buku Tamu
- Status interpretasi Rakernas X sudah terkunci pada dokumen canonical + matrix domain.

### Prioritas Implementasi Gap (K1 Dikunci)
- Gelombang 1 (sekretaris inti):
  - `buku-notulen-rapat`
  - `buku-daftar-hadir`
  - `buku-tamu`
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
- Eksekusi berikut wajib fokus ke definisi kontrak field minimum Gelombang 1 sebelum coding.
- Setelah kontrak field terkunci, lanjut implementasi per buku dengan boundary:
  - route + request + action/use case + repository + policy + test.

## Progress Eksekusi Lanjutan (2026-02-27)

### Keputusan Tambahan yang Dikunci
- `K3` dikunci: untuk buku rekap level kecamatan, pokja kecamatan berada pada mode monitoring (`read-only`) dan tidak menjadi aktor mutasi data sumber.
- `K4` dikunci: strategi migrasi modul baru mengikuti pola `dedicated module` tanpa coupling ke tabel legacy (`kecamatans`, `desas`, `user_assignments`) dan tanpa dual-write ke modul lama.

### Kontrak Field Minimum Gelombang 1
Referensi kontrak canonical:
- `docs/domain/dokumen_arsitektur_buku_admin_pkk_desa_kecamatan.md` (Bagian IX).

Ringkasan field minimum:
- `buku-notulen-rapat`: `tanggal_rapat`, `waktu_mulai`, `waktu_selesai`, `tempat`, `agenda`, `pimpinan_rapat`, `peserta_hadir`, `ringkasan_pembahasan`, `keputusan`, `tindak_lanjut`, `notulis`, `keterangan`.
- `buku-daftar-hadir`: `tanggal_kegiatan`, `nama_kegiatan`, `tempat`, `nama_peserta`, `jabatan`, `instansi_atau_kelompok`, `nomor_kontak`, `status_kehadiran`, `tanda_tangan`, `keterangan`.
- `buku-tamu`: `tanggal_kunjungan`, `nama_tamu`, `instansi_atau_asal`, `keperluan`, `diterima_oleh`, `nomor_kontak`, `waktu_datang`, `waktu_pulang`, `keterangan`.

### Boundary Implementasi (Locked)
- Route: prefix scope (`desa`/`kecamatan`) + middleware `scope.role:{desa|kecamatan}` + `module.visibility`.
- Request: validasi field wajib + normalisasi tanggal/waktu ke format canonical.
- Action/Use Case: simpan flow bisnis, tanpa logika domain di controller.
- Repository Interface + Repository: query scoped by `level`, `area_id`, `created_by`.
- Policy + Scope Service: enforce role/scope/area consistency + mode `read-only` vs `read-write`.
- Tests minimum: sukses role valid, tolak role tidak valid, tolak mismatch role-area-level, anti data leak repository.

### Fallback/Compatibility Plan (Locked)
- Tidak ada dual-write ke modul buku lain; modul gelombang 1 berdiri sebagai domain dedicated.
- Jika perlu integrasi dashboard, gunakan agregasi read-only dari repository modul baru.
- Jika kontrak autentik final berubah, lakukan migrasi additive (nullable column) lalu isi data via backfill terkontrol; dilarang destructive rewrite tanpa rencana rollback.

### Update Normalisasi Label Lintas Dokumen (2026-02-27)
File terdampak:
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`

Keputusan yang dikunci:
- Slug canonical buku bantuan diselaraskan ke route aktif: `bantuans` (sebelumnya drift `bantuan`).
- Label canonical backlog gelombang 1 ditambahkan eksplisit:
  - `buku-notulen-rapat` -> Buku Notulen Rapat
  - `buku-daftar-hadir` -> Buku Daftar Hadir
  - `buku-tamu` -> Buku Tamu

### Update Validasi Gate (2026-02-27)
Perintah validasi yang dijalankan:
- `php artisan test tests/Feature/ModuleVisibilityMiddlewareTest.php tests/Feature/MenuVisibilityPayloadTest.php tests/Unit/Services/RoleMenuVisibilityServiceTest.php tests/Feature/KecamatanReportReverseAreaMismatchTest.php tests/Feature/StructuredDomainReportPrintTest.php`

Ringkasan hasil:
- `42` test lulus, `167` assertion, tanpa kegagalan.
- Cakupan tervalidasi:
  - guard `module.visibility` (read-only/read-write + anti bypass),
  - payload menu role-scope,
  - matrix role-menu-mode,
  - mismatch role-area-level pada route report kecamatan,
  - smoke print/report domain terstruktur.

Catatan status print/report:
- Pada batch ini ada perubahan status autentik terbatas (`partial` -> `verified`) pada buku kegiatan Pokja I dan Pokja III; validasi print/report dipakai sebagai regression guard agar tidak ada behavior drift setelah penurunan status.

### Batch Autentikasi `partial/unverified` per Buku (2026-02-27, Opsi 1)

Kode bukti:
- `B1`: `docs/process/TODO_IMPLEMENTASI_AUTENTIK_BUKU_KEGIATAN_2026_02_24.md`
- `B2`: `docs/process/TODO_AUTENTIK_LAMPIRAN_4_9A_4_14_4B_E2E.md`
- `B3`: `docs/pdf/PDF_COMPLIANCE_CHECKLIST.md`
- `B4`: `docs/process/TODO_IMPLEMENTASI_AUTENTIK_BUKU_PROGRAM_KERJA_2026_02_24.md`
- `B5`: `docs/process/TODO_AUTENTIK_DATA_KEGIATAN_PKK_4_23_4_24.md` + `docs/domain/DATA_KEGIATAN_PKK_POKJA_IV_4_24_MAPPING.md`
- `B6`: `tests/Feature/ModuleVisibilityMiddlewareTest.php` + `tests/Feature/MenuVisibilityPayloadTest.php`

| Level | Buku | Prioritas | Bukti | Keputusan Batch |
| --- | --- | --- | --- | --- |
| Desa | Buku Daftar Anggota TP PKK | P2 | B2, B3 | hold `partial` (normalisasi istilah UI belum final) |
| Desa | Buku Notulen Rapat | P1 | - | tetap `unverified` (modul belum ada) |
| Desa | Buku Daftar Hadir | P1 | - | tetap `unverified` (modul belum ada) |
| Desa | Buku Tamu | P1 | - | tetap `unverified` (modul belum ada) |
| Desa | Buku Program Kerja TP PKK | P2 | B4 | hold `partial` (konsolidasi overlap domain belum final) |
| Desa | Buku Rencana Kerja Pokja I | P2 | B4 | hold `partial` (bergantung finalisasi domain program kerja) |
| Desa | Buku Kegiatan Pokja I | P1 | B1, B3 | turun ke `verified` |
| Desa | Buku Daftar Hadir Kegiatan | P1 | - | tetap `unverified` (modul belum ada) |
| Desa | Buku Data Kegiatan | P2 | B2, B3 | hold `partial` (normalisasi istilah lintas modul masih berjalan) |
| Desa | Buku Evaluasi Program (Pokja I) | P3 | - | tetap `unverified` (planned, belum ada artefak autentik) |
| Desa | Buku Rencana Kerja Pokja II | P2 | B4 | hold `partial` (bergantung finalisasi domain program kerja) |
| Desa | Buku Kegiatan Pendidikan dan Keterampilan | P2 | B3 | hold `partial` (mapping lintas modul belum satu kontrak final) |
| Desa | Buku Data Kelompok Belajar/Keterampilan | P2 | B3 | hold `partial` (kamus data kelompok belum final) |
| Desa | Buku Data UP2K-PKK | P2 | B3 | hold `partial` (kontrak UP2K lintas domain belum final) |
| Desa | Buku Evaluasi Program (Pokja II) | P3 | - | tetap `unverified` (planned, belum ada artefak autentik) |
| Desa | Buku Data Ketahanan Pangan Keluarga | P2 | B3 | hold `partial` (indikator ketahanan belum terkunci penuh) |
| Desa | Buku Data Rumah Sehat | P2 | B5 | hold `partial` (masih report-only agregasi) |
| Desa | Buku Kegiatan Pokja III | P1 | B1, B3 | turun ke `verified` |
| Desa | Buku Evaluasi Program (Pokja III) | P3 | - | tetap `unverified` (planned, belum ada artefak autentik) |
| Desa | Buku PHBS | P2 | B5 | hold `partial` (masih report-only agregasi) |
| Desa | Buku Perencanaan Sehat | P2 | B5 | hold `partial` (masih report-only agregasi) |
| Kecamatan | Buku Daftar Anggota TP PKK Kecamatan | P2 | B2, B3 | hold `partial` (normalisasi istilah UI belum final) |
| Kecamatan | Buku Notulen Rapat | P1 | - | tetap `unverified` (modul belum ada) |
| Kecamatan | Buku Program Kerja TP PKK Kecamatan | P2 | B4 | hold `partial` (konsolidasi overlap domain belum final) |
| Kecamatan | Buku Rekap Kegiatan Pokja I dari Desa | P2 | B1, B6 | hold `partial` (monitoring mode sudah aman, kontrak rekap khusus belum final) |
| Kecamatan | Buku Rekap Kegiatan Pokja II dari Desa | P2 | B6 | hold `partial` (monitoring mode sudah aman, kontrak rekap khusus belum final) |
| Kecamatan | Buku Rekap Kegiatan Pokja III dari Desa | P2 | B6 | hold `partial` (monitoring mode sudah aman, kontrak rekap khusus belum final) |
| Kecamatan | Buku Rekap Kegiatan Pokja IV dari Desa | P2 | B6 | hold `partial` (monitoring mode sudah aman, kontrak rekap khusus belum final) |

### Review Akhir Checklist (2026-02-27)
- Review checklist sudah dijalankan.
- Status concern tetap `in-progress`, belum `done`.
- Alasan concern belum `done`:
  - masih ada modul `missing` (`buku-notulen-rapat`, `buku-daftar-hadir`, `buku-tamu`),
  - masih ada buku `planned/unverified` untuk evaluasi program pokja,
  - beberapa domain masih `partial` karena kontrak lintas modul belum final.
