# TODO WIL26A1 Standarisasi Kode Wilayah Pecalungan

Tanggal: 2026-06-05  
Status: `done`
Related ADR: `docs/adr/ADR_0013_STANDARISASI_KODE_WILAYAH_PEcalungan.md`

## Konteks

- Kecamatan Pecalungan dipatok sebagai wilayah canonical aktif untuk satu kecamatan dan 10 desa.
- Kode desa sudah ditetapkan oleh user sebagai identitas administratif dunia nyata yang stabil.
- Repository perlu menjadikan kode tersebut sebagai standar canonical agar `areas` tidak drift terhadap implementasi, dashboard, PDF, dan scope wilayah.

## Kontrak Concern (Lock)

- Domain: wilayah canonical `areas`.
- Role/scope target: seluruh role `desa` dan `kecamatan`.
- Boundary data: `areas` sebagai source of truth; legacy `kecamatans`, `desas`, `user_assignments` tetap non-canonical.
- Acceptance criteria:
  - `areas.code` menjadi identitas stabil dan unik.
  - Kecamatan Pecalungan dan 10 desa canonical terseed dengan kode resmi.
  - Jumlah desa canonical tetap 10 kecuali ada ADR baru.
  - Dokumen canonical lebih dulu disinkronkan daripada implementasi.
- Dampak keputusan arsitektur: `ya`

## Target Hasil

- [x] Kode wilayah Pecalungan dikunci sebagai standar canonical repo.
- [x] Struktur `areas` dan seed wilayah mendukung kode stabil untuk kecamatan dan 10 desa.
- [x] Dokumen referensi wilayah dan kontrak domain tersinkron.

## Langkah Eksekusi

- [x] Audit scoped dependency + side effect pada `areas`, seeder wilayah, dan referensi domain.
- [x] Tambahkan/ubah dokumen canonical yang menjelaskan kode wilayah resmi Pecalungan.
- [x] Setelah dokumen final, implementasikan migrasi/seeder/test minimum.
- [x] Sinkronisasi dokumen concern terkait jika ada drift istilah atau kontrak.

## Validasi

- [x] L1: review dokumen canonical dan konsistensi istilah.
- [x] L2: targeted test wilayah canonical dan seeder.
- [x] L3: `php artisan test` bila perubahan implementasi sudah masuk.

## Risiko

- Risiko 1: kode wilayah tidak konsisten antara seed, UI, dan data existing.
- Risiko 2: perubahan struktur `areas` memengaruhi scope dan relasi dashboard bila tidak diuji.

## Keputusan

- [x] `areas.code` diperlakukan sebagai identitas wilayah stabil.
- [x] Pecalungan dipatok sebagai 1 kecamatan dan 10 desa canonical.

## Keputusan Arsitektur (Jika Ada)

- [x] Buat/tautkan ADR di `docs/adr/ADR_0013_STANDARISASI_KODE_WILAYAH_PEcalungan.md`.
- [x] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Jika implementasi kode wilayah menimbulkan drift, rollback ke seed/migrasi terakhir dan pertahankan `name` + `level` sebagai fallback sementara.

## Output Final

- [x] Ringkasan apa yang diubah dan kenapa.
- [x] Daftar file terdampak.
- [x] Hasil validasi + residual risk.
