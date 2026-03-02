# TODO UGN26A1 Pembuatan User Guide Natural Humanis
Tanggal: 2026-02-24  
Status: `done`

## Konteks
- Sistem sudah memiliki modul luas berbasis role/scope (`desa`, `kecamatan`, `sekretaris`, `pokja`) namun belum ada user guide operasional yang terstruktur untuk pengguna akhir.
- User meminta user guide dengan copywriting natural-humanis (bahasa mudah dipahami, tidak teknis, dan berorientasi tindakan pengguna).
- Dokumen ini menjadi rencana eksekusi tunggal sebelum penulisan konten final.

## Target Hasil
- Tersedia user guide berbasis skenario tugas nyata pengguna, bukan berbasis istilah teknis internal.
- Setiap peran utama punya panduan ringkas: tujuan, langkah kerja, hasil yang diharapkan, dan solusi saat kendala.
- Standar copywriting natural-humanis terkunci dan dipakai konsisten pada seluruh halaman panduan.
- Ada quality gate konten agar panduan tidak ambigu, tidak terlalu teknis, dan tetap sinkron dengan perilaku sistem.

## Ruang Lingkup Peran dan Audiens
- `super-admin` (manajemen user dan kontrol administrasi sistem).
- `kecamatan-sekretaris` dan `desa-sekretaris`.
- `kecamatan-pokja-i` s.d. `kecamatan-pokja-iv`.
- `desa-pokja-i` s.d. `desa-pokja-iv`.

## Arsitektur Dokumen User Guide (Rencana Struktur)
- `docs/user-guide/README.md` (beranda panduan + peta navigasi).
- `docs/user-guide/mulai-cepat.md` (login, navigasi, istilah dasar).
- `docs/user-guide/peran/sekretaris-desa.md`
- `docs/user-guide/peran/sekretaris-kecamatan.md`
- `docs/user-guide/peran/pokja-desa.md`
- `docs/user-guide/peran/pokja-kecamatan.md`
- `docs/user-guide/peran/super-admin.md`
- `docs/user-guide/alur/kelola-data-harian.md`
- `docs/user-guide/alur/filter-dashboard-dan-membaca-grafik.md`
- `docs/user-guide/alur/cetak-dan-ekspor-laporan.md`
- `docs/user-guide/faq.md`

## Standar Copywriting Natural Humanis (Wajib)
- Gunakan sudut pandang pengguna: `Anda`.
- Pakai kalimat aktif, singkat, dan langsung ke tindakan.
- Hindari istilah teknis internal (contoh slug role, nama query key, istilah backend).
- Jika istilah domain wajib dipakai, beri penjelasan sederhana pada kemunculan pertama.
- Gunakan pola tetap per bagian:
  - tujuan singkat,
  - langkah berurutan,
  - hasil yang terlihat di layar,
  - jika gagal lakukan apa.
- Hindari nada menggurui; gunakan nada membantu dan jelas.

## Langkah Eksekusi
- [x] `U1` Tetapkan dokumen rencana concern user guide ini di `docs/process/`.
- [x] `U2` Audit menu dan flow aktual per role dari sisi UI/route agar daftar topik panduan sesuai perilaku sistem.
- [x] `U3` Kunci glossary istilah user-facing di `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md` agar bahasa panduan konsisten.
- [x] `U4` Buat skeleton dokumen di `docs/user-guide/` sesuai struktur arsitektur di atas.
- [x] `U5` Tulis konten fase 1 (mulai cepat + peran sekretaris + FAQ umum).
- [x] `U6` Tulis konten fase 2 (peran pokja + super-admin + alur dashboard/filter/laporan).
- [x] `U7` Jalankan copywriting pass: audit bahasa teknis, konsistensi istilah, dan kejelasan instruksi tindakan.
- [x] `U8` Lakukan review lintas peran (minimal 1 skenario sukses + 1 skenario kendala per peran).
- [x] `U9` Finalisasi indeks panduan dan tautan antarhalaman agar navigasi satu arah dan tidak membingungkan.
- [x] `U10` Siapkan dokumen cetak awal (pilot) berbasis screenshot login untuk kebutuhan distribusi lapangan.
- [x] `U11` Siapkan dokumen cetak gabungan berurutan untuk seluruh user guide tanpa screenshot.
- [x] `U12` Pertahankan dokumen login bergambar sebagai referensi visual saat dokumen gabungan digunakan.
- [x] `U13` Susun ulang dokumen cetak gabungan agar menggunakan tepat 1 gambar (login).

## Acceptance Criteria
- [x] Pengguna baru bisa menyelesaikan 3 tugas inti tanpa pendamping:
  - login dan memahami halaman kerja,
  - input/update data sesuai peran,
  - membaca dashboard dan mengekspor/cetak laporan.
- [x] Tidak ada label teknis internal pada judul/subjudul/CTA user guide.
- [x] Setiap halaman maksimal fokus pada satu tujuan utama.
- [x] Semua langkah instruksi dapat dipetakan ke perilaku UI/route yang benar.
- [x] Minimal satu dokumen panduan siap cetak tersedia untuk uji distribusi.

## Validasi
- [x] Review konten oleh tim domain: istilah sesuai pedoman domain utama.
- [x] Review konten oleh tim produk/UI: bahasa natural dan tidak ambigu.
- [x] Smoke test manual: ikuti panduan langkah demi langkah pada akun role berbeda dan pastikan hasilnya sesuai.
- [x] Konsistensi lintas dokumen: terminologi user guide sinkron dengan `TERMINOLOGY_NORMALIZATION_MAP`.

## Artefak Eksekusi U2 dan U4
- Audit role-flow: `docs/process/USER_GUIDE_ROLE_FLOW_AUDIT_2026_02_24.md`
- Skeleton user guide:
  - `docs/user-guide/README.md`
  - `docs/user-guide/mulai-cepat.md`
  - `docs/user-guide/peran/sekretaris-desa.md`
  - `docs/user-guide/peran/sekretaris-kecamatan.md`
  - `docs/user-guide/peran/pokja-desa.md`
  - `docs/user-guide/peran/pokja-kecamatan.md`
  - `docs/user-guide/peran/super-admin.md`
  - `docs/user-guide/alur/kelola-data-harian.md`
  - `docs/user-guide/alur/filter-dashboard-dan-membaca-grafik.md`
  - `docs/user-guide/alur/cetak-dan-ekspor-laporan.md`
  - `docs/user-guide/faq.md`
  - `docs/user-guide/print/README.md`
  - `docs/user-guide/print/00-user-guide-lengkap-siap-cetak.html`
  - `docs/user-guide/print/01-login-siap-cetak.html`

## Risiko
- Risiko drift: panduan tidak diupdate saat UI berubah.
- Risiko bahasa: istilah teknis kembali masuk saat update cepat.
- Risiko over-detail: panduan terlalu panjang dan sulit dipakai di lapangan.

## Mitigasi
- Tetapkan ritme update: setiap perubahan UI mayor wajib memicu update user guide concern terkait.
- Wajib copywriting pass untuk setiap PR yang menambah teks user-facing.
- Pisahkan panduan `mulai cepat` dan `panduan rinci` agar pengguna bisa memilih kedalaman informasi.

## Keputusan
- [x] User guide akan berbasis role + alur tugas nyata pengguna.
- [x] Copywriting natural-humanis adalah kontrak wajib, bukan opsional.
- [x] Implementasi konten dilakukan bertahap per fase agar kualitas tetap terjaga.
