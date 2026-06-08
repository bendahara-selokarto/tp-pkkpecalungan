# TODO DSK26 Review Keselarasan Menu Desa Dengan Kecamatan

Tanggal: 2026-06-08  
Status: `in-progress`
Related ADR: `docs/adr/ADR_0011_COMMON_BOOK_VISIBILITY_PER_ROLE_GROUP.md`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Review ini memeriksa semua jabatan di scope `desa` terhadap baseline menu/permission `kecamatan`, karena `kecamatan` dipakai sebagai acuan canonical untuk struktur menu yang semestinya hampir sama.
- Fokus utama adalah menemukan mismatch antara menu yang tampil, permission backend yang mengizinkan klik, kelompok buku yang dipetakan oleh `RoleMenuVisibilityService`, dan kepemilikan menu yang semestinya mirror antar level sepadan.
- Analisis ini sengaja belum menyentuh implementasi; keluaran yang diinginkan adalah peta drift yang rapi, terklasifikasi, dan siap dijadikan backlog kerja terpisah.

## Kontrak Concern (Lock)

- Domain: keselarasan menu sidebar dan authority backend untuk jabatan level `desa`.
- Role/scope target: `desa-sekretaris`, `desa-bendahara`, `desa-pokja-i`, `desa-pokja-ii`, `desa-pokja-iii`, `desa-pokja-iv` (`admin-desa` ditinggalkan sementara).
- Boundary data: `RoleMenuVisibilityService`, `RoleScopeMatrix`, policy kelas modul, route/controller resource, dan test kontrak menu/payload.
- Acceptance criteria:
  - struktur menu desa terpetakan terhadap baseline kecamatan per role yang sepadan;
  - kepemilikan menu untuk role yang sepadan harus mirror, misalnya `kecamatan-sekretaris` ↔ `desa-sekretaris`;
  - setiap slug yang tampil punya permission backend yang konsisten;
  - divergence yang memang sengaja dibuat dicatat sebagai pengecualian, bukan dianggap bug;
  - gap yang berpotensi 403 harus diidentifikasi sebelum implementasi;
  - hasil analisis ditulis sebagai matriks kerja yang bisa dipakai untuk tahap implementasi berikutnya.
- Dampak keputusan arsitektur: `ya`

## Hasil Analisis Awal

### 1) Pemetaan umum

- `desa-sekretaris` dan `kecamatan-sekretaris` sama-sama berangkat dari grup `sekretaris-tpk`, jadi secara struktur menu keduanya memang dekat.
- `desa-pokja-i..iv` dan `kecamatan-pokja-i..iv` juga berbagi kerangka grup yang sama, tetapi desa membawa overlay tambahan `sekretaris-bantu` pada beberapa role.
- `monitoring` hanya muncul di `kecamatan`; itu terlihat sebagai delta scope yang memang sengaja eksklusif untuk kecamatan.

### 2) Aturan kepemilikan menu

- Jika sebuah menu dimiliki oleh satu role di kecamatan untuk fungsi yang sama, maka role desa yang sepadan seharusnya juga memiliki kepemilikan menu yang sama, kecuali ada pengecualian yang sudah dikunci di dokumen.
- Contoh yang sedang diverifikasi: jika `kecamatan-sekretaris` memiliki `inventaris`, maka `desa-sekretaris` juga harus memiliki `inventaris`.
- Prinsip ini dipakai untuk menilai kepemilikan menu, bukan untuk menyamakan semua isi grup secara buta; exception yang memang khusus scope tetap dipertahankan.

### 3) Ringkasan per jabatan desa

| Jabatan | Status awal | Catatan analisis |
| --- | --- | --- |
| `admin-desa` | deferred | Tidak dianalisis di sesi ini sesuai instruksi terbaru. |
| `desa-sekretaris` | drift prioritas | Struktur menu sangat mirip `kecamatan-sekretaris`, tetapi permission backend masih belum simetris untuk buku pembantu bersama dan beberapa penunjang lintas grup. |
| `desa-bendahara` | relatif stabil | Pemetaan grup dan permission terlihat konsisten; tidak muncul gap besar dari scan awal. |
| `desa-pokja-i` | perlu verifikasi | Menu pokja-i hampir mirror kecamatan, tapi ada gap pada `data-kegiatan-pkk-pokja-i` dan `agenda-surat-tugas`. |
| `desa-pokja-ii` | perlu verifikasi | Menu pokja-ii hampir mirror kecamatan, tapi ada gap pada `data-kegiatan-pkk-pokja-ii` dan `agenda-surat-tugas`. |
| `desa-pokja-iii` | perlu verifikasi | Menu pokja-iii hampir mirror kecamatan, tapi ada gap pada `data-kegiatan-pkk-pokja-iii`, `buku-kliping`, dan `agenda-surat-tugas`. |
| `desa-pokja-iv` | perlu verifikasi | Menu pokja-iv hampir mirror kecamatan, tapi ada gap pada `data-kegiatan-pkk-pokja-iv`, `data-umum-pkk`, `data-umum-pkk-kecamatan`, dan `agenda-surat-tugas`. |

### 4) Delta yang perlu dijaga

- `sekretaris-bantu` tetap hadir di level desa sebagai overlay menu tambahan; ini tidak otomatis harus dipaksakan identik dengan kecamatan.
- `monitoring` tetap tidak masuk level desa; ini adalah pengecualian scope yang harus dipertahankan.
- `data-umum-pkk` dan `data-umum-pkk-kecamatan` perlu perlakuan khusus karena sebagian jalurnya report-only dan tidak sama dengan modul CRUD biasa.

## Target Hasil

- [ ] Matrix per jabatan desa vs baseline kecamatan tersedia.
- [ ] Daftar kepemilikan menu yang semestinya mirror antar level sepadan tersedia.
- [ ] Setiap gap diklasifikasikan sebagai `intentional`, `drift`, atau `needs-confirmation`.
- [ ] Daftar prioritas implementasi untuk mismatch berisiko 403 disiapkan.
- [ ] Daftar pengecualian yang sengaja berbeda dari kecamatan disimpan agar tidak salah dibetulkan.
- [ ] Test kontrak yang perlu diaktifkan kembali atau ditambah dicatat per jabatan.

## Langkah Eksekusi

- [x] P0. Kunci kecamatan sebagai baseline pembanding untuk role yang sepadan; `admin-desa` ditunda.
- [ ] P1. Audit `RoleMenuVisibilityService` per grup desa dan bandingkan dengan grup kecamatan yang ekuivalen.
- [ ] P2. Audit `RoleScopeMatrix` per role desa untuk menemukan permission `view` yang hilang atau terlalu longgar.
- [ ] P3. Tabelkan kepemilikan menu: role kecamatan ↔ role desa, lalu tandai mana yang harus mirror dan mana yang pengecualian.
- [ ] P4. Tandai semua delta yang memang sengaja berbeda, terutama overlay `sekretaris-bantu`, `monitoring`, dan jalur report-only.
- [ ] P5. Susun backlog implementasi terpisah berdasarkan tingkat risiko: 403, mismatch menu, atau mismatch report.
- [ ] P6. Siapkan daftar test yang nanti harus diaktifkan ulang setelah implementasi.

## Validasi

- [ ] L1: validasi analisis lewat inspeksi file terarah.
- [ ] L2: validasi silang dengan test kontrak menu dan policy yang sudah ada.
- [ ] L3: setelah implementasi, jalankan test terarah pada role desa yang terdampak.

## Risiko

- Risiko 1: menganggap delta yang sengaja ada sebagai bug dan menimpa kontrak yang memang berbeda.
- Risiko 2: hanya membenahi menu tanpa permission backend, sehingga 403 tetap muncul saat klik.

## Keputusan

- [ ] K1: kecamatan dipakai sebagai baseline canonical untuk review ini.
- [ ] K2: kepemilikan menu untuk role yang sepadan harus mirror kecamatan kecuali ada pengecualian terdokumentasi.
- [ ] K3: `admin-desa` ditandai `deferred` dan tidak masuk evaluasi lanjutan pada sesi ini.
- [ ] K4: hanya gap berisiko yang diprioritaskan untuk patch berikutnya.

## Keputusan Arsitektur (Jika Ada)

- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jika hasil review menunjukkan delta yang ternyata intentional, backlog tidak diubah menjadi patch.
- Jika saat implementasi nanti muncul 403 baru, rollback dilakukan pada permission matrix atau route/policy yang terakhir disentuh, bukan pada struktur menu canonical.

## Output Final

- [ ] Ringkasan analisis per jabatan desa.
- [ ] Daftar mismatch yang wajib diprioritaskan.
- [ ] Daftar pengecualian yang memang sengaja berbeda dari kecamatan.
- [ ] Residual risk untuk tahap implementasi berikutnya.
