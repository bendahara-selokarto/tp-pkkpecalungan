# TODO Implementasi Autentik Buku Kegiatan (2026-02-24)

## Konteks
- Sumber autentik: `docs/referensi/excel/Buku Wajib Pokja I.xlsx`, sheet `Buku Kegiatan`.
- Hasil baca text-layer XML sudah tersedia (struktur merge awal sudah teridentifikasi).
- Status saat ini: `belum siap sinkronisasi` karena verifikasi visual header tabel belum dikunci.

## Target Hasil
- Kontrak header final sheet `Buku Kegiatan` tervalidasi sampai merge cell.
- Mapping kolom autentik ke field aplikasi terdokumentasi.
- Rencana patch implementasi minimal lintas layer (`request -> use case -> repository -> inertia`) siap eksekusi.

## Langkah Eksekusi
- [ ] Ambil bukti visual header tabel `Buku Kegiatan` (header utuh + garis sel + teks terbaca).
- [ ] Finalisasi peta header:
  - `NO`
  - `NAMA`
  - `JABATAN`
  - `KEGIATAN` (`TANGGAL`, `TEMPAT`, `URAIAN`)
  - `TANDA TANGAN`
- [ ] Tetapkan matrix mapping `kolom autentik -> field storage/report`.
- [ ] Audit dampak implementasi:
  - route + middleware scope/role
  - request normalisasi/validasi
  - use case/repository query boundary
  - inertia payload/dashboard trigger (jika relevan)
- [ ] Siapkan daftar test minimum (feature + policy/scope + anti data leak bila query scoped kompleks).
- [ ] Sinkronkan dokumen concern jika ada perubahan kontrak canonical.

## Validasi
- [ ] Bukti visual header valid tersimpan.
- [ ] Tidak ada ambiguitas merge cell pada header.
- [ ] Checklist dampak layer arsitektur lengkap.
- [ ] Rencana test executable sebelum patch implementasi.

## Risiko
- Salah interpretasi header `KEGIATAN` multi-subkolom jika hanya mengandalkan text-layer.
- Drift istilah kolom antara referensi autentik dan label UI existing.

## Keputusan
- [x] Sheet `Buku Kegiatan` dipilih sebagai prioritas fase 1A.
- [x] Implementasi ditahan sampai verifikasi visual header selesai.
