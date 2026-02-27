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
- [ ] Sinkronkan daftar buku aktif dari dokumen canonical ke snapshot implementasi terbaru (route + menu + policy).
- [ ] Tandai buku `available/planned/missing` berdasarkan bukti implementasi aktual.
- [ ] Tandai buku yang masih overlap domain agar tidak terjadi duplikasi modul.

### B. Validasi Interpretasi Rakernas X
- [ ] Pastikan setiap entri buku punya referensi lampiran/istilah Rakernas X.
- [ ] Jika ada mismatch istilah, catat deviasi dan kunci keputusan fallback ke sumber primer.
- [ ] Normalisasi label buku lintas dokumen agar konsisten dengan pedoman.

### C. Autentikasi Buku
- [ ] Prioritaskan buku status `partial/unverified` untuk validasi autentik bertahap.
- [ ] Untuk buku bertabel: validasi peta header sampai `rowspan/colspan`.
- [ ] Simpan bukti validasi (text-layer + screenshot visual) dan tautkan ke dokumen mapping terkait.
- [ ] Turunkan status ke `verified` hanya setelah bukti lengkap dan konsisten.

### D. Penanggung Jawab Buku
- [ ] Audit matriks penanggung jawab buku per level (sekretaris vs pokja).
- [ ] Validasi mode akses (`read-write` vs `read-only`) terhadap tanggung jawab buku.
- [ ] Pastikan tidak ada modul buku yang ditempatkan pada role yang tidak sesuai kontrak.

### E. Rencana Implementasi Gap
- [ ] Susun prioritas implementasi buku `missing` (gelombang 1: sekretaris inti, gelombang 2: buku pokja pendukung).
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
- [ ] K1: Urutan prioritas implementasi buku `missing` per level.
- [ ] K2: Kriteria final `verified` untuk autentikasi buku.
- [ ] K3: Batas kewenangan pokja kecamatan untuk modul rekap (monitoring vs mutasi).
- [ ] K4: Strategi migrasi jika ada buku yang perlu pemecahan domain/modul.

## Output Wajib Tiap Update
- [ ] Daftar buku yang berubah status (`available/planned/missing`, `verified/partial/unverified`).
- [ ] File terdampak (domain/process/security/ui/test) dan alasan perubahan.
- [ ] Bukti validasi yang digunakan.
- [ ] Dampak ke rencana implementasi gelombang berikutnya.

