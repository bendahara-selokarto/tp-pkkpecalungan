# DOKUMEN KONTROL BUKU ADMINISTRASI PKK
## Desa/Kelurahan dan Kecamatan

---

## Status Canonical
Dokumen ini adalah acuan canonical tunggal untuk concern:
1. Ketersediaan buku.
2. Autentikasi buku.
3. Penempatan penanggung jawab buku.

Dokumen ini merupakan interpretasi operasional dari Rakernas X, bukan sumber primer.
Sumber primer tetap:
1. `PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md`
2. `docs/referensi/Rakernas X.pdf`

Aturan interpretasi:
1. Setiap entri buku harus dapat ditelusuri ke lampiran/istilah Rakernas X.
2. Jika ada konflik istilah, struktur, atau makna, keputusan final wajib kembali ke sumber primer Rakernas X.
3. Status `verified` hanya boleh dipakai jika bukti autentik (text-layer + verifikasi visual bila diperlukan) tersedia.

Dokumen lain yang menyinggung concern serupa diperlakukan sebagai:
1. Bukti validasi teknis (mis. PDF/auth/audit).
2. Catatan historis implementasi.
3. Turunan operasional terbatas.

---

## I. Fungsi Dokumen
Dokumen ini menjadi sumber tunggal untuk:
1. Merekam ketersediaan buku administrasi per level.
2. Merencanakan pemenuhan gap buku yang belum tersedia.
3. Memastikan autentikasi buku berdasarkan format resmi Rakernas X.
4. Memastikan penempatan buku pada penanggung jawab yang tepat.

Dokumen ini menjadi acuan untuk sinkronisasi:
- Role & permission.
- Modul aplikasi (route/controller/use case/repository/policy).
- Artefak validasi autentik (header tabel + struktur merge cell jika bertabel).

---

## II. Ruang Lingkup dan Level Operasional
Level operasional:
1. TP PKK Desa/Kelurahan (input + rekap awal).
2. TP PKK Kecamatan (rekap wilayah + validasi berjenjang).

Prinsip operasional:
1. Tidak ada edit silang antar level.
2. Arus data berjenjang: Dasawisma -> Desa/Kelurahan -> Kecamatan.
3. Penanggung jawab buku wajib sesuai struktur kelembagaan.
4. `areas` tetap source of truth wilayah pada implementasi.

---

## III. Status Kontrol Buku (Baseline)
Gunakan status berikut:
- `available`: modul/fitur buku sudah tersedia.
- `planned`: sudah masuk rencana implementasi.
- `missing`: belum tersedia dan belum masuk backlog aktif.

Gunakan status autentikasi:
- `verified`: format autentik tervalidasi.
- `partial`: sebagian tervalidasi, belum final.
- `unverified`: belum tervalidasi autentik.

### A. Level Desa/Kelurahan
| Kelompok | Nama Buku | Penanggung Jawab | Status Ketersediaan | Status Autentikasi | Catatan Tindak Lanjut |
|---|---|---|---|---|---|
| Sekretaris | Buku Daftar Anggota Tim Penggerak PKK | Sekretaris Desa/Kelurahan | available | partial | Label diselaraskan dengan lampiran autentik |
| Sekretaris | Buku Agenda Surat Masuk/Keluar | Sekretaris Desa/Kelurahan | available | verified | Jaga konsistensi format output |
| Sekretaris | Buku Notulen Rapat | Sekretaris Desa/Kelurahan | available | unverified | Modul dedicated aktif; finalisasi kontrak field autentik |
| Sekretaris | Buku Daftar Hadir | Sekretaris Desa/Kelurahan | available | unverified | Modul dedicated aktif; finalisasi kontrak field autentik |
| Sekretaris | Buku Inventaris | Sekretaris Desa/Kelurahan | available | verified | Pertahankan kontrak autentik aktif |
| Sekretaris | Buku Tamu | Sekretaris Desa/Kelurahan | available | unverified | Modul dedicated aktif; finalisasi kontrak field autentik |
| Sekretaris | Buku Program Kerja TP PKK | Sekretaris Desa/Kelurahan | partial | partial | Konsolidasikan domain program kerja |
| Sekretaris | Rekap Data Ibu Hamil/Melahirkan/Nifas/Kelahiran/Kematian | Sekretaris Desa/Kelurahan | available | verified | Pertahankan chain rekap berjenjang |
| Pokja I | Buku Rencana Kerja Pokja I | Pokja I Desa | partial | partial | Tegaskan pemisahan rencana vs kegiatan |
| Pokja I | Buku Kegiatan Pokja I | Pokja I Desa | available | partial | Audit coverage field kegiatan |
| Pokja I | Buku Daftar Hadir Kegiatan | Pokja I Desa | missing | unverified | Reuse model daftar hadir lintas pokja |
| Pokja I | Buku Data Kegiatan | Pokja I Desa | available | partial | Normalisasi istilah data kegiatan |
| Pokja I | Buku Evaluasi Program | Pokja I Desa | planned | unverified | Tambah struktur evaluasi periodik |
| Pokja II | Buku Rencana Kerja Pokja II | Pokja II Desa | partial | partial | Satukan indikator rencana kerja |
| Pokja II | Buku Kegiatan Pendidikan dan Keterampilan | Pokja II Desa | available | partial | Validasi mapping bidang |
| Pokja II | Buku Data Kelompok Belajar/Keterampilan | Pokja II Desa | partial | partial | Lengkapi kamus data kelompok |
| Pokja II | Buku Data UP2K-PKK | Pokja II Desa | partial | partial | Sinkronkan ke domain koperasi/UP2K |
| Pokja II | Buku Evaluasi Program | Pokja II Desa | planned | unverified | Tambah format evaluasi |
| Pokja III | Buku Data Ketahanan Pangan Keluarga | Pokja III Desa | partial | partial | Tegaskan indikator ketahanan |
| Pokja III | Buku Data Pemanfaatan Pekarangan | Pokja III Desa | available | verified | Pertahankan format autentik aktif |
| Pokja III | Buku Data Rumah Sehat | Pokja III Desa | partial | partial | Definisikan indikator rumah sehat |
| Pokja III | Buku Kegiatan Pokja III | Pokja III Desa | available | partial | Sinkronkan sumber kegiatan |
| Pokja III | Buku Evaluasi Program | Pokja III Desa | planned | unverified | Tambahkan rubric evaluasi |
| Pokja IV | Buku Data Ibu Hamil/Kelahiran/Kematian | Pokja IV Desa | available | verified | Pertahankan rekap konsisten |
| Pokja IV | Buku Kegiatan Posyandu | Pokja IV Desa | available | verified | Pastikan cakupan layanan lengkap |
| Pokja IV | Buku PHBS | Pokja IV Desa | partial | partial | Tetapkan field PHBS canonical |
| Pokja IV | Buku Perencanaan Sehat | Pokja IV Desa | partial | partial | Tetapkan indikator perencanaan |

### B. Level Kecamatan
| Kelompok | Nama Buku | Penanggung Jawab | Status Ketersediaan | Status Autentikasi | Catatan Tindak Lanjut |
|---|---|---|---|---|---|
| Sekretaris | Buku Daftar Anggota Tim Penggerak PKK Kecamatan | Sekretaris Kecamatan | available | partial | Label diselaraskan dengan lampiran autentik |
| Sekretaris | Buku Agenda Surat Masuk/Keluar | Sekretaris Kecamatan | available | verified | Konsisten antar level |
| Sekretaris | Buku Notulen Rapat | Sekretaris Kecamatan | available | unverified | Modul dedicated aktif; finalisasi kontrak field autentik |
| Sekretaris | Buku Daftar Hadir | Sekretaris Kecamatan | available | unverified | Modul dedicated aktif; finalisasi kontrak field autentik |
| Sekretaris | Buku Tamu | Sekretaris Kecamatan | available | unverified | Modul dedicated aktif; finalisasi kontrak field autentik |
| Sekretaris | Buku Program Kerja TP PKK Kecamatan | Sekretaris Kecamatan | partial | partial | Konsolidasi kontrak program kerja |
| Sekretaris | Buku Inventaris | Sekretaris Kecamatan | available | verified | Pertahankan validasi autentik |
| Sekretaris | Rekapitulasi Ibu Hamil/Melahirkan/Nifas/Kelahiran/Kematian | Sekretaris Kecamatan | available | verified | Pertahankan rekap lintas desa |
| Sekretaris | Rekap Catatan Data dan Kegiatan Warga | Sekretaris Kecamatan | available | verified | Pertahankan konsistensi agregasi |
| Pokja I | Buku Rekap Kegiatan Pokja I dari Desa | Pokja I Kecamatan | available | partial | Kunci mode monitoring/evaluasi |
| Pokja II | Buku Rekap Kegiatan Pokja II dari Desa | Pokja II Kecamatan | available | partial | Kunci mode monitoring/evaluasi |
| Pokja III | Buku Rekap Kegiatan Pokja III dari Desa | Pokja III Kecamatan | available | partial | Kunci mode monitoring/evaluasi |
| Pokja IV | Buku Rekap Kegiatan Pokja IV dari Desa | Pokja IV Kecamatan | available | partial | Kunci mode monitoring/evaluasi |

---

## IV. Standar Autentikasi Buku
Metode baku autentikasi:
1. Ekstraksi text-layer dokumen autentik.
2. Jika header tabel belum utuh, lakukan verifikasi visual screenshot.
3. Validasi peta header sampai merge cell (`rowspan`/`colspan`).
4. Simpan bukti validasi sebagai artefak resmi.

Kriteria bukti screenshot header:
1. Seluruh area header tabel tercakup.
2. Garis batas sel terlihat.
3. Baris nomor kolom terlihat.
4. Teks header terbaca.

Aturan keputusan:
1. Jika peta header belum lengkap: status `belum siap sinkronisasi`.
2. Sinkronisasi kontrak dan implementasi hanya setelah status siap.

---

## V. Matriks Penanggung Jawab dan Akses
Prinsip penempatan:
1. Sekretaris: buku sekretariat + finalisasi level masing-masing.
2. Pokja Desa: input dan update pada buku pokja masing-masing.
3. Pokja Kecamatan: monitoring/evaluasi rekap bidang dari desa.

Kontrol akses minimal:
1. Desa: `scope.role:desa`.
2. Kecamatan: `scope.role:kecamatan`.
3. Konsistensi role-scope-area wajib tervalidasi backend.
4. Untuk modul shared lintas group (contoh: `Buku Kegiatan`/`activities`), list/detail role `desa-pokja-i` s.d. `desa-pokja-iv` dan `kecamatan-pokja-i` s.d. `kecamatan-pokja-iv` wajib terfilter kombinasi `role group + level + area`; role sekretaris tetap by area sesuai levelnya.
5. Frontend wajib merender item menu hanya dari slug yang ada pada `auth.user.moduleModes`; item menu tanpa mode backend dianggap tidak valid dan tidak boleh ditampilkan.
6. Modul `Buku Kegiatan` (`activities`) wajib tersedia untuk seluruh role operasional pada scope validnya masing-masing (sekretaris desa/kecamatan, pokja I-IV desa/kecamatan, role admin kompatibilitas, dan `super-admin`) dengan mode akses mengikuti kontrak backend.

Keputusan operasional terkunci (2026-02-27):
1. Pokja kecamatan pada concern rekap lintas desa diposisikan sebagai monitoring/evaluasi (`read-only`) dan bukan jalur mutasi data sumber.
2. Mutasi data sumber tetap terjadi di level desa sesuai ownership pokja terkait.
3. Enforcement backend mengikuti guard `RoleMenuVisibilityService` + middleware visibilitas modul + policy/scope service.
4. Jika modul perlu dipecah domain, migrasi wajib bertahap (parallel compatibility -> adapter transisi -> cutover route/menu -> cleanup setelah parity test).

---

## VI. Rencana Implementasi Gap (Checklist Kerja)
### A. Ketersediaan Buku
- [x] Tambah modul `buku-notulen-rapat` desa/kecamatan.
- [x] Tambah modul `buku-daftar-hadir` desa/kecamatan.
- [x] Tambah modul `buku-tamu` desa/kecamatan.
- [x] Tegaskan pemetaan `buku-program-kerja` agar tidak overlap (ownership `sekretaris-tpk`, tidak berada pada group `pokja-iv`).

### B. Autentikasi Buku
- [ ] Lengkapi validasi autentik untuk buku berstatus `partial`.
- [ ] Kunci artefak bukti screenshot untuk setiap format tabel autentik.
- [ ] Sinkronkan kontrak field per buku dengan hasil baca autentik final.

### C. Penanggung Jawab Buku
- [x] Audit seluruh modul agar ownership sesuai sekretaris/pokja.
- [x] Audit mode akses pokja kecamatan agar sesuai kebijakan monitoring.
- [x] Tambah test untuk mismatch role-area-level pada modul gap baru.

---

## VII. Validasi Berkala
Checklist validasi setiap perubahan:
1. Buku tercatat di tabel status kontrol.
2. Status autentikasi terbarui (`verified/partial/unverified`).
3. Penanggung jawab dan mode akses sudah tepat.
4. Tidak ada drift role-scope-area terhadap `areas.level`.
5. Test relevan lulus.

---

## VIII. Output Wajib per Update
Setiap update dokumen ini wajib menyebut:
1. Buku yang berubah status.
2. Alasan perubahan.
3. Dampak ke modul/rute/policy/test.
4. Bukti validasi autentik yang digunakan.
