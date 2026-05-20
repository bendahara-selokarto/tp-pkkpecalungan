# TODO BFT26A1 Audit Format Buku Fisik Per Jabatan

Tanggal: 2026-05-02  
Status: `in-progress` (`state:program-activities-crud-isolation-implemented`)
Related ADR: `docs/adr/ADR_0011_COMMON_BOOK_VISIBILITY_PER_ROLE_GROUP.md`

## Konteks

- Owner mengunci koreksi domain: buku yang memiliki format fisik sama persis pada jalur buku utama adalah `Buku Program Kerja` dan `Buku Kegiatan`.
- Owner mengunci kelompok `buku bantu`: `Buku Prestasi`, `Buku Bantuan`, dan `Buku Kader Khusus` memiliki format fisik yang sama.
- Revisi owner 2026-05-20: `Buku Inventaris` bukan lagi buku bantu seragam; posisinya menjadi buku wajib Sekretaris dan buku spesifik Pokja III jika format/slugnya dikunci.
- Buku lain boleh memiliki nama yang sama lintas jabatan, tetapi format tabel/kolom/struktur dokumennya berbeda sesuai jabatan atau konteks buku fisik.
- Kelompok `buku bantu` dimiliki oleh `sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, dan `pokja-iv`; data wajib terisolasi untuk masing-masing jabatan/group.
- `Data Kegiatan` dimiliki oleh `sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, dan `pokja-iv` dengan format output berbeda per jabatan.
- Data `Data Kegiatan` tetap wajib terisolasi untuk masing-masing jabatan/group walaupun berada pada level wilayah dan area yang sama.
- Perubahan sebelumnya sudah membuat beberapa buku tersedia pada masing-masing jabatan dan mengisolasi data per `group`; concern ini tidak boleh langsung menganggap format PDF/form/table bisa reuse hanya karena nama bukunya sama.
- Implementasi berikutnya wajib dimulai dari audit format autentik, bukan dari penyamaan nama menu.

## Kontrak Concern (Lock)

- Domain: audit dan rencana implementasi format buku fisik per jabatan.
- Role/scope target: `sekretaris-tpk`, `bendahara-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, `pokja-iv` pada scope `desa` dan `kecamatan` jika formatnya tersedia.
- Boundary data: patch runtime hanya boleh untuk buku yang format samanya sudah dikunci owner; perubahan implementasi tetap mengikuti `Controller -> UseCase/Action -> Repository -> Model`.
- Acceptance criteria:
  - `Buku Program Kerja` dan `Buku Kegiatan` ditetapkan sebagai buku utama yang boleh memakai format sama persis lintas jabatan.
  - `Buku Prestasi`, `Buku Bantuan`, dan `Buku Kader Khusus` ditetapkan sebagai kelompok `buku bantu` dengan format sama.
  - Kelompok `buku bantu` wajib tersedia untuk `sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, dan `pokja-iv`.
  - Data kelompok `buku bantu` wajib terisolasi per `group` sebagai bagian dari boundary query dan policy/scope.
  - Buku bernama sama selain `Buku Program Kerja` dan `Buku Kegiatan` wajib punya baris matrix format sendiri per jabatan/konteks.
  - `Data Kegiatan` wajib punya format output sendiri per jabatan (`sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, `pokja-iv`).
  - Data `Data Kegiatan` wajib terisolasi per `group` sebagai bagian dari boundary query dan policy/scope.
  - Setiap format wajib bersumber dari dokumen autentik atau screenshot valid header tabel sampai peta merge cell (`rowspan`/`colspan`).
  - Tidak ada reuse komponen PDF/form/table lintas jabatan tanpa bukti format sama.
  - Rencana implementasi memisahkan keputusan `visibilitas menu`, `isolasi data`, dan `format dokumen`.
- Dampak keputusan arsitektur: `ya` (mengubah kontrak format lintas concern dan mencegah reuse lintas jabatan yang salah).

## Target Hasil

- [ ] Matrix format buku per jabatan tersedia sebelum implementasi.
- [x] Daftar buku yang boleh reuse format dikunci, dengan `Buku Program Kerja` dan `Buku Kegiatan` sebagai reuse penuh.
- [x] Daftar kelompok `buku bantu` dikunci: `Buku Prestasi`, `Buku Bantuan`, `Buku Kader Khusus`.
- [x] Revisi 2026-05-20 mengeluarkan `Buku Inventaris` dari kelompok buku bantu seragam.
- [ ] Matrix isolasi data kelompok `buku bantu` per jabatan/group dikunci sebelum implementasi.
- [ ] Daftar buku bernama sama tetapi format berbeda dikunci beserta sumber bukti, termasuk `Data Kegiatan` per jabatan.
- [ ] Matrix isolasi data `Data Kegiatan` per jabatan/group dikunci sebelum implementasi.
- [x] Patch runtime untuk buku berformat sama terpecah ke `activities` dan `program-prioritas`.

## Langkah Eksekusi

- [ ] P0. Audit dokumen autentik dan screenshot yang sudah tersedia untuk buku lintas jabatan.
- [ ] P1. Susun matrix `jabatan -> nama buku -> format id -> sumber bukti -> status implementasi`.
- [ ] P2. Tandai `Buku Program Kerja` dan `Buku Kegiatan` sebagai `reuse-full` hanya jika header/kolom/merge cell terbukti identik.
- [ ] P3. Tandai semua buku bernama sama selain `Buku Program Kerja` dan `Buku Kegiatan` sebagai `format-specific` sampai bukti autentik menyatakan identik.
- [x] P3c. Tandai `Buku Prestasi`, `Buku Bantuan`, dan `Buku Kader Khusus` sebagai kelompok `buku bantu` berformat sama untuk `sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, dan `pokja-iv`.
- [ ] P3d. Audit kebutuhan isolasi data kelompok `buku bantu` per `group` pada route, request, repository, policy/scope, UI, dan PDF/report output.
- [ ] P3a. Tandai `Data Kegiatan` sebagai `format-specific` per jabatan untuk `sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, dan `pokja-iv`.
- [ ] P3b. Audit kebutuhan isolasi data `Data Kegiatan` per `group` pada route, request, repository, policy/scope, UI, dan PDF/report output.
- [ ] P4. Audit runtime saat ini: route, Inertia page, request, repository, PDF view, dan print registry yang masih reuse format berdasarkan slug/nama buku.
- [ ] P5. Buat rencana patch minimal per kelompok format, dimulai dari buku dengan bukti paling lengkap.
- [ ] P6. Sinkronkan ADR/TODO terkait jika ditemukan format yang mengubah boundary data atau kontrak PDF.
- [x] P7. Implementasi hak CRUD dan isolasi `group` untuk `Buku Program Kerja` (`program-prioritas`) dan verifikasi ulang `Buku Kegiatan` (`activities`).

## Validasi

- [ ] L1: audit markdown matrix lengkap dan tidak ada baris buku lintas jabatan tanpa status format.
- [ ] L2: targeted grep memastikan tidak ada narasi dokumen yang menyatakan semua buku bernama sama memakai format sama.
- [x] L3: targeted feature/report/policy/menu tests per buku terdampak hijau.
- [x] L4: `npm run build` dan full `php artisan test --compact --do-not-cache-result` hijau.

## Risiko

- Risiko 1: UI terlihat benar karena menu terpisah per jabatan, tetapi PDF/form masih salah karena reuse format lama.
- Risiko 2: data sudah terisolasi per `group`, tetapi label buku bernama sama membuat auditor mengira formatnya identik.
- Risiko 3: implementasi terlalu luas bila semua buku diperbaiki sekaligus tanpa matrix format prioritas.

## Keputusan

- [x] K1: hanya `Buku Program Kerja` dan `Buku Kegiatan` yang boleh diasumsikan memakai format sama persis lintas jabatan.
- [x] K2: buku lain yang namanya sama tetap diperlakukan sebagai format berbeda sampai terbukti sebaliknya dari dokumen autentik.
- [x] K3: `Data Kegiatan` dimiliki oleh `sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, dan `pokja-iv` dengan output format berbeda per jabatan.
- [x] K4: data `Data Kegiatan` wajib terisolasi untuk masing-masing jabatan/group.
- [ ] K5: setiap reuse format PDF/form wajib punya bukti header/kolom/merge cell di matrix.
- [x] K6: `Buku Program Kerja` dan `Buku Kegiatan` wajib memiliki hak CRUD serta isolasi data per `group`.
- [x] K7: `Buku Prestasi`, `Buku Bantuan`, dan `Buku Kader Khusus` dikunci sebagai kelompok `buku bantu` dengan format sama.
- [x] K7A: `Buku Inventaris` dikeluarkan dari buku bantu seragam berdasarkan revisi owner 2026-05-20.
- [x] K8: kelompok `buku bantu` dimiliki oleh `sekretaris-tpk`, `pokja-i`, `pokja-ii`, `pokja-iii`, dan `pokja-iv`; data terisolasi per jabatan/group.

## Keputusan Arsitektur (Jika Ada)

- [x] Tautkan ADR terkait visibilitas dan isolasi buku umum: `docs/adr/ADR_0011_COMMON_BOOK_VISIBILITY_PER_ROLE_GROUP.md`.
- [ ] Buat ADR baru hanya jika audit format memaksa perubahan boundary model/repository/PDF engine lintas concern.

## Fallback Plan

- Jika bukti format belum lengkap, status buku tetap `belum siap implementasi format`.
- Jika implementasi format tertentu gagal, rollback hanya format buku itu dan pertahankan isolasi data/menu yang sudah berjalan.
- Jika ditemukan format ternyata identik selain `Buku Program Kerja` dan `Buku Kegiatan`, keputusan harus masuk ADR atau update TODO ini dengan bukti autentik.

## Output Final

- [ ] File matrix/rencana yang dihasilkan.
- [ ] Daftar buku `reuse-full`, `format-specific`, dan `belum siap`.
- [ ] Daftar file runtime yang akan terdampak pada implementasi berikutnya.
- [ ] Hasil validasi markdown dan residual risk.
