# TODO NFM26A1 Audit Normalisasi Database Formal 1NF-3NF Bertahap

Tanggal: 2026-03-10  
Status: `in-progress`
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- User meminta normalisasi database dilakukan bertahap untuk seluruh domain.
- Audit 2026-02-24 hanya mengunci kontrak canonical (`areas`, `level`, `area_id`, `created_by`) dan belum memetakan 1NF/2NF/3NF secara formal.
- Pendekatan bertahap dipilih agar risiko migrasi, backfill, dan regresi report/PDF tetap terkontrol.

## Kontrak Concern (Lock)

- Domain: seluruh skema domain aktif (non-legacy) + tabel penopang wilayah/user.
- Role/scope target: semua (integritas data; otorisasi backend tidak berubah).
- Boundary data: `database/migrations`, `database/seeders`, repository query domain, kontrak canonical `areas`.
- Acceptance criteria: peta formal 1NF/2NF/3NF per tabel + daftar pelanggaran + rencana batch + tidak ada coupling legacy baru + validasi sesuai ladder.
- Dampak keputusan arsitektur: `tidak` (tahap audit); `ya` bila batch migrasi disetujui.

## Target Hasil

- [ ] Inventarisasi tabel + kunci + relasi (scope non-legacy).
- [ ] Peta formal 1NF/2NF/3NF per tabel + temuan utama.
- [ ] Rencana normalisasi bertahap (batch per domain) + prioritas risiko.
- [ ] Batch 1 disiapkan (patch minimal + backfill + test) setelah audit selesai.

## Langkah Eksekusi

- [x] Inventarisasi tabel dari `database/migrations` dan identifikasi PK/FK/candidate keys.
- [x] Catat temuan awal risk-based pada baseline audit.
- [ ] Audit 1NF: cek repeating group, multi-value column (CSV/JSON/array), dan nullable yang mengindikasikan entitas terpisah.
- [ ] Audit 2NF: cek partial dependency pada tabel dengan kunci komposit atau natural key.
- [ ] Audit 3NF: cek transitive dependency dan atribut turunan yang bisa direlasikan.
- [ ] Klasifikasi risiko per tabel (high/medium/low) + urutan batch.
- [ ] Susun rencana patch bertahap: migration + backfill + adapter kompatibilitas.
- [x] Implementasi batch 1 (program_prioritas) dengan patch minimal pada boundary arsitektur.
- [ ] Sinkronisasi dokumen concern terkait bila trigger hardening aktif.

## Validasi

- [x] L1: audit scoped (`rg` migrasi/kolom) + sanity check schema.
- [ ] L2: targeted test per domain batch yang diubah.
- [x] L3: `php artisan test --compact` untuk batch migrasi/relasi signifikan.
- [ ] L4: `php artisan migrate:fresh --seed` jika batch mengubah struktur data inti.

## Artefak

- Baseline inventaris tabel: `docs/process/NORMALISASI_DATABASE_FORMAL_AUDIT_BASELINE_2026_03_10.md`

## Risiko

- Constraint baru gagal karena data historis belum bersih.
- Report/PDF masih membaca field lama; perlu adapter sementara.
- Backfill besar berisiko lambat tanpa batching.

## Keputusan

- [ ] K1: Definisikan kriteria formal 1NF/2NF/3NF untuk konteks repo (termasuk JSON/array).
- [ ] K2: Tentukan urutan batch dan tabel prioritas tinggi.
- [ ] K3: Putuskan strategi adapter kompatibilitas (request/repository) per batch.

## Keputusan Arsitektur (Jika Ada)

- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Rollback migration per batch + rollback backfill.
- Aktifkan adapter query sementara untuk field legacy hingga data bersih.

## Output Final

- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak.
- [ ] Hasil validasi + residual risk.
