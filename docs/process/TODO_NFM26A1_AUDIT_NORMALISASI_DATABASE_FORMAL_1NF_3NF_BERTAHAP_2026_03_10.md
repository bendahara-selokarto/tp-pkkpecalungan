# TODO NFM26A1 Audit Normalisasi Database Formal 1NF-3NF Bertahap

Tanggal: 2026-03-10  
Status: `done`
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

- [x] Inventarisasi tabel + kunci + relasi (scope non-legacy).
- [x] Peta formal 1NF/2NF/3NF per tabel + temuan utama.
- [x] Rencana normalisasi bertahap (batch per domain) + prioritas risiko.
- [x] Batch 1 disiapkan (patch minimal + backfill + test) setelah audit selesai.

## Langkah Eksekusi

- [x] Inventarisasi tabel dari `database/migrations` dan identifikasi PK/FK/candidate keys.
- [x] Catat temuan awal risk-based pada baseline audit.
- [x] Audit 1NF: cek repeating group, multi-value column (CSV/JSON/array), dan nullable yang mengindikasikan entitas terpisah.
- [x] Audit 1NF (partial): repeating group pada migrasi aktif dipetakan; kandidat multi-value menunggu konfirmasi kontrak domain.
- [x] Audit 2NF: cek partial dependency pada tabel dengan kunci komposit atau natural key.
- [x] Audit 3NF: cek transitive dependency dan atribut turunan yang bisa direlasikan.
- [x] Klasifikasi risiko per tabel (high/medium/low) + urutan batch.
- [x] Susun rencana patch bertahap: migration + backfill + adapter kompatibilitas.
- [x] Implementasi batch 1 (program_prioritas) dengan patch minimal pada boundary arsitektur.
- [x] Implementasi batch 2 (pilot_project_naskah_pelaporan_reports) dengan patch minimal pada boundary arsitektur.
- [x] Implementasi batch 3 (agenda_surats lampiran/tembusan + pilot_project_naskah_pelaporan_reports surat_tembusan).
- [x] Sinkronisasi dokumen concern terkait bila trigger hardening aktif.

## Validasi

- [x] L1: audit scoped (`rg` migrasi/kolom) + sanity check schema.
- [x] L2: targeted test per domain batch yang diubah (tercakup oleh full suite).
- [x] L3: `php artisan test --compact` untuk batch migrasi/relasi signifikan.
- [x] L4: `php artisan migrate:fresh --seed` jika batch mengubah struktur data inti.

## Artefak

- Baseline inventaris tabel: `docs/process/NORMALISASI_DATABASE_FORMAL_AUDIT_BASELINE_2026_03_10.md`

## Risiko

- Constraint baru gagal karena data historis belum bersih.
- Report/PDF masih membaca field lama; perlu adapter sementara.
- Backfill besar berisiko lambat tanpa batching.

## Keputusan

- [x] K1: Definisikan kriteria formal 1NF/2NF/3NF untuk konteks repo (termasuk JSON/array).
- [x] K2: Tentukan urutan batch dan tabel prioritas tinggi.
- [x] K3: Putuskan strategi adapter kompatibilitas (request/repository) per batch.

## Keputusan Arsitektur (Jika Ada)

- [ ] Buat/tautkan ADR di `docs/adr/ADR_<NOMOR4>_<RINGKASAN>.md`.
- [ ] Sinkronkan status ADR (`proposed/accepted/superseded/deprecated`) dengan status concern.

## Fallback Plan

- Rollback migration per batch + rollback backfill.
- Aktifkan adapter query sementara untuk field legacy hingga data bersih.

## Output Final

- [x] Ringkasan apa yang diubah dan kenapa.
- [x] Daftar file terdampak.
- [x] Hasil validasi + residual risk.

### Output Final (Ringkas)

- Ringkasan: batch 1-3 normalisasi 1NF multi-value + audit 2NF/3NF selesai.
- File terdampak: baseline audit + TODO concern + migrasi/seed/repository/model/tests batch 1-3.
- Validasi: `php artisan test --compact`, `php artisan migrate:fresh --seed`. Residual risk: monitor kolom free-text untuk drift multi-value.
