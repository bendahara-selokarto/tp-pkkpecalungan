# TODO Autentik Buku Wajib Pokja I dan Buku Bantu (2026-02-24)

## Konteks
- User menetapkan dua dokumen Excel sebagai format autentik:
  - `docs/referensi/excel/Buku Wajib Pokja I.xlsx`
  - `docs/referensi/excel/BUKU BANTU.xlsx`
- Pada sesi ini sudah dilakukan pembacaan struktur workbook (sheet, header awal, dan merge-cell) berbasis XML workbook.
- Sinkronisasi kontrak implementasi belum boleh lanjut sebelum verifikasi visual header tabel untuk sheet prioritas selesai.

## Target Hasil
- Kontrak header tabel autentik per sheet prioritas terkunci sampai level merge cell (`rowspan`/`colspan` ekuivalen Excel merge range).
- Tersedia matrix mapping kolom autentik -> field/domain canonical untuk implementasi backend/frontend.
- Dokumen proses/domain terkait tersinkron, tanpa drift istilah.
- Siap dieksekusi untuk patch implementasi bertahap (query, form, report, dan validasi test).

## Ruang Lingkup Prioritas
- `Buku Wajib Pokja I.xlsx`:
  - `Buku Rencana Program`
  - `Buku Kegiatan`
- `BUKU BANTU.xlsx`:
  - `Buku Bantuan`
  - `Buku Kader Khusus`
  - `Buku Prestasi`
  - `Buku Inventaris`
  - `Buku Anggota Pokja`
  - `BukuKelompok Simulasi`

## Rencana Eksekusi Bertahap
- [ ] Fase 1A: `Buku Wajib Pokja I - Buku Kegiatan`:
  - `docs/process/TODO_IMPLEMENTASI_AUTENTIK_BUKU_KEGIATAN_2026_02_24.md`
- [x] Fase 1B: `BUKU BANTU - Buku Bantuan`:
  - `docs/process/TODO_IMPLEMENTASI_AUTENTIK_BUKU_BANTUAN_2026_02_24.md`
  - status: implementasi concern selesai (mapping field autentik, UI desa/kecamatan, normalisasi request/repository, PDF, test).
- [x] Fase 2: `Buku Rencana Program`, `Buku Kader Khusus`, `Buku Prestasi`.
  - `docs/process/TODO_IMPLEMENTASI_AUTENTIK_BUKU_PROGRAM_KERJA_2026_02_24.md`
  - status: implementasi `Buku Rencana Program`, `Buku Kader Khusus`, dan `Buku Prestasi` selesai (sinkron label UI/PDF + kontrak mapping field).
- [ ] Fase 3: `Buku Inventaris`, `Buku Anggota Pokja`, `BukuKelompok Simulasi`.
  - `docs/process/TODO_IMPLEMENTASI_AUTENTIK_BUKU_BANTU_LANJUTAN_2026_02_24.md`
  - status: verifikasi visual header selesai untuk 5 sheet lanjutan BUKU BANTU; lanjut matrix mapping field.

## Langkah Eksekusi
- [x] Inventarisasi struktur workbook (sheet list + merge ranges) untuk kedua file autentik.
- [x] Verifikasi visual header tabel untuk `BUKU BANTU.xlsx` sheet `Buku Bantuan`.
- [x] Tetapkan peta header final sheet `Buku Bantuan` (termasuk merge `JENIS BANTUAN -> UANG/BARANG`).
- [x] Verifikasi visual header tabel untuk `Buku Kader Khusus`, `Buku Prestasi`, `Buku Inventaris`, `Buku Anggota Pokja`, `BukuKelompok Simulasi`.
- [x] Tetapkan peta header final untuk 5 sheet lanjutan `BUKU BANTU.xlsx` (termasuk grup subkolom pada `JENIS KELAMIN`, `STATUS`, `PRESTASI/KEBERHASILAN`, `JUMLAH`, `JUMLAH KADER`).
- [x] Verifikasi visual header tabel untuk `Buku Wajib Pokja I.xlsx` sheet `Buku Rencana Program` (`BUKU PROGRAM KERJA`).
- [x] Tetapkan peta header final `Buku Rencana Program` (22 kolom; `JADWAL WAKTU` 12 subkolom + `SUMBER DANA` 4 subkolom).
- [ ] Verifikasi visual header tabel untuk setiap sheet prioritas (wajib bukti screenshot header utuh).
- [ ] Tetapkan peta header final per sheet: urutan kolom, merge horizontal/vertikal, label header final.
- [ ] Susun matrix mapping `kolom autentik -> field input/storage/report` per sheet.
- [ ] Audit dampak ke layer arsitektur: route/request/use case/repository/policy/inertia.
- [ ] Tentukan urutan patch minimal per concern (mulai dari sheet dengan dampak user terbesar).
- [ ] Sinkronkan dokumen canonical yang terdampak (TODO/process/domain matrix/deviasi bila ada).

## Validasi
- [x] Pembacaan text-layer Excel (via XML workbook) berhasil untuk dua file autentik.
- [x] Bukti visual + peta merge header tervalidasi untuk sheet `Buku Bantuan`.
- [x] Bukti visual + peta merge header tervalidasi untuk 5 sheet lanjutan `BUKU BANTU.xlsx`.
- [x] Bukti visual + peta merge header tervalidasi untuk sheet `Buku Rencana Program`.
- [ ] Bukti visual header tabel tersedia dan memenuhi kriteria validasi (header utuh, garis sel terlihat, label terbaca).
- [ ] Tidak ada ambigu mapping untuk kolom yang akan dipakai implementasi.
- [ ] Rencana patch per concern terdokumentasi dengan fallback plan teknis.

## Risiko
- Header multi-baris dengan merge kompleks berisiko salah tafsir jika tanpa verifikasi visual.
- Sebagian sheet memiliki variasi istilah antar dokumen (potensi drift terminologi).
- Risiko over-coupling jika implementasi langsung tanpa matrix mapping canonical.

## Keputusan
- [x] Dua file Excel di atas dikunci sebagai referensi autentik untuk concern ini.
- [x] Tahap saat ini fokus perencanaan + validasi struktur, belum sinkronisasi implementasi.
- [x] Implementasi hanya boleh dimulai setelah peta header visual tervalidasi untuk sheet target.
