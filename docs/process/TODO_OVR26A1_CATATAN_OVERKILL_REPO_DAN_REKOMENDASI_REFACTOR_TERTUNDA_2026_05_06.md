# TODO OVR26A1 Catatan Overkill Repo Dan Rekomendasi Refactor Tertunda

Tanggal: 2026-05-06  
Status: `planned`
Related ADR: `-`

## Konteks

- Catatan ini menyimpan diagnosis overkill repo berdasarkan audit ringan 2026-05-06.
- Status concern adalah backlog penyederhanaan, bukan refactor darurat.
- Baseline yang diamati:
  - `app/Domains/Wilayah` berisi sekitar 626 file.
  - `docs` berisi sekitar 241 file.
  - `resources/js/Pages` berisi sekitar 297 file.
  - `tests/Feature` berisi sekitar 130 file.
  - `app/Policies` berisi sekitar 39 policy.
- Arsitektur saat ini tetap dianggap aman untuk authorization dan domain wilayah, tetapi biaya maintenance tinggi karena banyak boilerplate lintas modul.

## Kontrak Concern (Lock)

- Domain: lintas domain wilayah dan governance repo.
- Role/scope target: semua role/scope; tidak mengubah kontrak runtime.
- Boundary data: tidak ada perubahan data/migrasi pada concern ini.
- Acceptance criteria:
  - Catatan overkill terdokumentasi dan bisa dipakai sebagai referensi refactor bertahap.
  - Tidak ada patch runtime dari concern ini sebelum ada keputusan refactor terpisah.
  - Setiap refactor turunan wajib dibuat sebagai concern tersendiri dengan scope kecil.
- Dampak keputusan arsitektur: `tidak` untuk pencatatan ini; `ya` hanya jika nanti mulai mengubah boundary layer.

## Target Hasil

- [x] Simpan diagnosis area overkill repo sebagai backlog non-darurat.
- [ ] Saat ada waktu refactor, pilih satu area kecil dan buat TODO turunan dengan target jelas.
- [ ] Hindari big-bang refactor lintas domain.

## Langkah Eksekusi

- [ ] Kandidat 1: kurangi repository interface satu-implementasi secara bertahap jika tidak dibutuhkan untuk testing/substitusi.
- [ ] Kandidat 2: ekstrak pola umum `level + area_id + tahun_anggaran` ke shared scoped authorization/query helper.
- [ ] Kandidat 3: satukan pola controller/use case desa-kecamatan yang hanya berbeda `ScopeLevel`.
- [ ] Kandidat 4: rapikan test berulang dengan helper/data provider, tanpa mengurangi coverage auth-scope inti.
- [ ] Kandidat 5: evaluasi schema-driven form/table untuk modul CRUD sederhana di frontend.
- [ ] Kandidat 6: thinning dokumen governance yang terlalu operasional jika soft cap mulai mengganggu eksekusi.

## Validasi

- [x] L1: audit baca ringan struktur repo dan sampel layer.
- [ ] L2: untuk refactor turunan, jalankan targeted test sesuai modul.
- [ ] L3: untuk refactor lintas boundary authorization/repository/frontend, jalankan `php artisan test` dan build/test frontend relevan.

## Risiko

- Risiko 1: over-simplification bisa melemahkan auditability authorization jika shared abstraction terlalu generik.
- Risiko 2: refactor besar lintas modul rawan regression karena pola desa/kecamatan dan group role tidak selalu identik.
- Risiko 3: menghapus dokumentasi governance tanpa pengganti bisa membuat eksekusi AI lintas sesi kembali ambigu.

## Keputusan

- [x] K1: refactor tidak darurat; eksekusi hanya saat menyentuh area terkait atau saat ada slot cleanup khusus.
- [x] K2: bagian yang tidak overkill dan tetap dipertahankan adalah `areas` sebagai canonical wilayah, backend authorization, active budget year, dan validasi scope.
- [x] K3: prioritas pengurangan biaya maintenance adalah boilerplate layer dan duplikasi test/UI, bukan melemahkan policy/scope.

## Keputusan Arsitektur (Jika Ada)

- [ ] ADR baru hanya dibuat jika refactor turunan mengubah boundary arsitektur utama atau enforcement authorization.
- [ ] Jika hanya cleanup boilerplate internal tanpa perubahan kontrak, ADR tidak wajib.

## Fallback Plan

- Jika refactor turunan bermasalah, rollback per concern kecil.
- Jika abstraction baru membuat flow lebih sulit diaudit, hentikan dan pertahankan pola eksplisit per domain.
- Jangan menghapus test auth-scope sampai shared coverage pengganti terbukti setara.

## Output Final

- [x] Ringkasan diagnosis disimpan sebagai backlog.
- [x] File terdampak: dokumen TODO ini saja.
- [x] Validasi: scoped audit baca; tidak ada test runtime karena tidak ada perubahan kode.
