# TODO <KODE_UNIK> <Judul Ringkas>

Tanggal: YYYY-MM-DD  
Status: `planned`
Related ADR: `-`

## Aturan Pakai
- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks
- Jelaskan baseline concern dan alasan perubahan.

## Kontrak Concern (Lock)
- Domain:
- Role/scope target:
- Boundary data:
- Acceptance criteria:
- Dampak keputusan arsitektur: `ya/tidak`

## Target Hasil
- [ ] Hasil utama 1.
- [ ] Hasil utama 2.

## Langkah Eksekusi
- [ ] Analisis scoped dependency + side effect.
- [ ] Patch minimal pada boundary arsitektur.
- [ ] Sinkronisasi dokumen concern terkait (jika trigger hardening aktif).

## Validasi
- [ ] L1: syntax/lint/targeted test concern.
- [ ] L2: regression test concern terkait.
- [ ] L3: `php artisan test` jika perubahan signifikan.

## Risiko
- Risiko 1:
- Risiko 2:

## Keputusan
- [ ] K1:
- [ ] K2:

## Keputusan Arsitektur (Jika Ada)
- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan
- Jalur rollback/fallback teknis jika implementasi bermasalah.

## Output Final
- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak.
- [ ] Hasil validasi + residual risk.
