# TODO SIT26A1 Standardisasi Input Tanggal
Tanggal: 2026-03-02 (normalisasi metadata; perlu verifikasi historis)  
Status: `done`

## Konteks
- Project masih pre-release.
- Reset data development diperbolehkan (`php artisan migrate:fresh`).
- Kontrak tanggal saat ini campuran:
  - `type="date"` + `Y-m-d`
  - `type="text"` + `DD/MM/YYYY`
  - beberapa field berbasis string bebas (kasus domain khusus).

## Target Hasil
- Satu standar canonical untuk field tanggal kalender: `input type="date"` + payload `Y-m-d`.
- Backend validasi konsisten untuk tanggal kalender.
- Field exception domain tetap jelas dan terdokumentasi.
- Tidak ada regression otorisasi dan scope.

## Keputusan Kontrak
- [x] Tetapkan canonical: tanggal kalender = `Y-m-d` end-to-end.
- [x] Tetapkan exception domain:
  - `tanggal_masuk_tp_pkk` tetap string domain (bisa tanggal/sejak tahun).
  - `no_tgl_sk` tetap string dokumen.
- [x] Tetapkan aturan display:
  - Form edit/create: canonical (`Y-m-d` untuk input date).
  - Halaman list/show/report: format presentasi (`DD/MM/YYYY`) via formatter terpusat.

## Rencana Tindakan

### Phase 1 - Baseline dan Safety
- [x] Buat matriks field tanggal per modul (Desa + Kecamatan). (`docs/process/DATE_INPUT_FIELD_MATRIX.md`)
- [x] Tandai field yang wajib migrasi vs exception domain. (`docs/process/DATE_INPUT_FIELD_MATRIX.md`)
- [x] Tambah util formatter frontend terpusat untuk tampilan tanggal. (`resources/js/utils/dateFormatter.js`)

### Phase 2 - Migrasi UI Form
- [x] Ubah form tanggal kalender dari `type="text"` ke `type="date"`:
  - Activities
  - Bantuan
  - Inventaris
  - AgendaSurat
  - AnggotaTimPenggerak
  - AnggotaPokja
  - KaderKhusus
- [x] Pastikan data prefill edit sudah `Y-m-d`.
- [x] Hapus placeholder `DD/MM/YYYY` pada field yang sudah `type="date"`.

### Phase 3 - Migrasi Request/Validation
- [x] Untuk field tanggal kalender, ubah request ke kontrak `date_format:Y-m-d` (atau equivalent strict).
- [x] Deprecate jalur normalisasi `DD/MM/YYYY` bertahap.
- [x] Pertahankan parser lama hanya jika dibutuhkan compatibility window sementara.

### Phase 4 - Controller/Presenter Harmonization
- [x] Konsolidasikan mapping tanggal di controller agar:
  - payload ke form edit selalu `Y-m-d`,
  - payload ke show/index memakai formatter presentasi.
- [x] Hapus formatter ad-hoc yang hanya return raw value.

### Phase 5 - Data dan Migrasi Skema (Jika Diperlukan)
- [x] Audit cast model tanggal pada field yang seharusnya date.
- [x] Jika ada perubahan skema/normalisasi besar, jalankan:
  - `php artisan migrate:fresh`
  - `php artisan db:seed` (jika dibutuhkan)
- [x] Catat bahwa data lokal development ter-reset.

### Phase 6 - Test dan Regression Gate
- [x] Tambah/upgrade feature test create+update untuk semua modul tanggal (Desa + Kecamatan).
- [x] Tambah negative test format invalid untuk kontrak `Y-m-d`.
- [x] Jalankan `php artisan test` penuh setelah migrasi lintas-modul.

## Validasi Minimum
- [x] Semua field tanggal kalender menerima `Y-m-d`.
- [x] Tidak ada mismatch format antara create/edit/show/index.
- [x] Tidak ada perubahan tak diminta pada policy/scope authorization.
- [x] Test suite relevan lulus.

## Catatan Eksekusi 2026-02-22
- Backend strict `Y-m-d` ditutup untuk:
  - `anggota.*.tanggal_lahir` (DataWarga)
  - `surat_tanggal` (Pilot Project Naskah Pelaporan)
- Presentasi tanggal `DD/MM/YYYY` diseragamkan via util frontend:
  - Activity (Desa/Kecamatan + monitoring desa oleh kecamatan)
  - Bantuan (Desa/Kecamatan)
  - Agenda Surat (Desa/Kecamatan)
  - Inventaris (Desa/Kecamatan)
  - Anggota Tim Penggerak, Anggota Pokja, Kader Khusus (halaman show Desa/Kecamatan)
- Regression check lulus:
  - `php artisan test --filter="DesaDataWargaTest|DesaPilotProjectNaskahPelaporanTest|DesaActivityTest|KecamatanActivityTest|KecamatanDesaActivityTest|DesaBantuanTest|KecamatanBantuanTest|DesaInventarisTest|KecamatanInventarisTest|DesaAgendaSuratTest|KecamatanAgendaSuratTest"`
- Tidak ada perubahan skema database pada eksekusi ini, sehingga `migrate:fresh`/`db:seed` tidak dijalankan dan tidak ada reset data development.
- Coverage create+update modul tanggal sisi kecamatan ditambahkan untuk:
  - `KecamatanActivityTest`
  - `KecamatanAgendaSuratTest`
  - `KecamatanBantuanTest`
  - `KecamatanAnggotaTimPenggerakTest`
  - `KecamatanAnggotaPokjaTest`
  - `KecamatanKaderKhususTest`
  - `KecamatanDataWargaTest`
  - `KecamatanPilotProjectNaskahPelaporanTest`

## Risiko
- Risiko regression form edit jika prefill belum canonical.
- Risiko bug parsing pada modul lama yang masih mengandalkan `DD/MM/YYYY`.
- Risiko data dev hilang saat `migrate:fresh`.

## Fallback Plan
- [x] Simpan branch checkpoint sebelum migrasi lintas-modul.
- [x] Jika regression tinggi, rollback per-phase (bukan rollback massal).
- [x] Aktifkan compatibility parser sementara sambil menyelesaikan modul prioritas.

Catatan fallback:
- Checkpoint tersimpan sebagai commit by concern pada branch aktif.
- Rollback per-phase dapat dilakukan dengan revert commit concern terkait.
- Compatibility parser tanggal tidak diaktifkan karena seluruh modul target sudah strict `Y-m-d`.
