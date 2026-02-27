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
- [ ] Pastikan tidak ada modul buku yang ditempatkan pada role yang tidak sesuai kontrak.

### E. Rencana Implementasi Gap
- [x] Susun prioritas implementasi buku `missing` (gelombang 1: sekretaris inti, gelombang 2: buku pokja pendukung).
- [ ] Definisikan kontrak field minimum per buku sebelum coding.
- [ ] Definisikan boundary implementasi per buku: route, request, action/use case, repository, policy, test.
- [ ] Definisikan fallback/compatibility plan untuk modul yang sudah berjalan agar tidak behavior drift.

### F. Validasi dan Gate
- [ ] Tambah/rapikan test matrix untuk role-scope-area mismatch pada buku baru/yang diubah.
- [ ] Jalankan regresi feature untuk akses lintas scope dan anti data leak.
- [ ] Jalankan validasi print/report pada buku yang status autentiknya berubah.
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
- Tidak ada perubahan status buku pada eksekusi ini.
- Perubahan fokus: validasi baseline implementasi terhadap status yang sudah ada.

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
