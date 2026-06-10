# TODO DSK26 Review Keselarasan Menu Desa Dengan Kecamatan

Tanggal: 2026-06-08  
Status: `done`  
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

## Hasil Analisis (Audit Complete)

Lihat laporan lengkap di: `docs/process/AUDIT_REPORT_DSK26_KESELARASAN_MENU_2026_06_08.md`

### Ringkasan per jabatan desa

| Jabatan | Status akhir | Catatan analisis |
| --- | --- | --- |
| `admin-desa` | deferred | Tidak dianalisis di sesi ini sesuai instruksi terbaru. |
| `desa-sekretaris` | stable | 95% Mirror; delta `monitoring` adalah intentional. |
| `desa-bendahara` | stable | 100% Mirror; sangat konsisten. |
| `desa-pokja-i` | stable | Mirror dengan overlay `sekretaris-bantu` (intentional). |
| `desa-pokja-ii` | drift | Missing visibility untuk modul feeder Lampiran 4.22 (BKB, Literasi, Tutor). |
| `desa-pokja-iii` | drift (403 risk) | Permission backend `anggota-pokja` hanya `view` padahal menu RW. |
| `desa-pokja-iv` | stable | Mirror dengan overlay `sekretaris-bantu` (intentional). |

## Target Hasil

- [x] Matrix per jabatan desa vs baseline kecamatan tersedia.
- [x] Daftar kepemilikan menu yang semestinya mirror antar level sepadan tersedia.
- [x] Setiap gap diklasifikasikan sebagai `intentional`, `drift`, atau `needs-confirmation`.
- [x] Daftar prioritas implementasi untuk mismatch berisiko 403 disiapkan.
- [x] Daftar pengecualian yang sengaja berbeda dari kecamatan disimpan agar tidak salah dibetulkan.
- [x] Test kontrak yang perlu diaktifkan kembali atau ditambah dicatat per jabatan.

## Langkah Eksekusi

- [x] P0. Kunci kecamatan sebagai baseline pembanding untuk role yang sepadan; `admin-desa` ditunda.
- [x] P1. Audit `RoleMenuVisibilityService` per grup desa dan bandingkan dengan grup kecamatan yang ekuivalen.
- [x] P2. Audit `RoleScopeMatrix` per role desa untuk menemukan permission `view` yang hilang atau terlalu longgar.
- [x] P3. Tabelkan kepemilikan menu: role kecamatan ↔ role desa, lalu tandai mana yang harus mirror dan mana yang pengecualian.
- [x] P4. Tandai semua delta yang memang sengaja berbeda, terutama overlay `sekretaris-bantu`, `monitoring`, dan jalur report-only.
- [x] P5. Susun backlog implementasi terpisah berdasarkan tingkat risiko: 403, mismatch menu, atau mismatch report.
- [x] P6. Siapkan daftar test yang nanti harus diaktifkan ulang setelah implementasi.

## Validasi

- [x] L1: validasi analisis lewat inspeksi file terarah.
- [x] L2: validasi silang dengan test kontrak menu dan policy yang sudah ada.
- [x] L3: setelah implementasi, jalankan test terarah pada role desa yang terdampak.

## Output Final

- [x] Ringkasan analisis per jabatan desa (Dokumentasi Audit).
- [x] Daftar mismatch yang wajib diprioritaskan (Risiko 403).
- [x] Daftar pengecualian yang memang sengaja berbeda dari kecamatan.
- [x] Residual risk untuk tahap implementasi berikutnya.
