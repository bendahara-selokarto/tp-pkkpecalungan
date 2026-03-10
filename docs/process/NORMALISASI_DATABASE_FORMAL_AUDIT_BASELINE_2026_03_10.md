# Baseline Audit Normalisasi Database Formal 2026-03-10

Tanggal: 2026-03-10  
Status: `draft`

## Scope

- Tabel domain aktif dari `database/migrations`.
- Tabel sistem/framework dicatat terpisah (bukan target audit 1NF/2NF/3NF).
- Artefak legacy `kecamatans`, `desas`, `user_assignments` tetap dilarang sebagai jalur read/write.

## Metode Formal

- 1NF: setiap kolom bernilai atomik; tidak ada repeating group atau daftar CSV/JSON yang seharusnya direlasikan.
- 2NF: pada tabel dengan kunci komposit, semua atribut non-key bergantung penuh pada seluruh kunci.
- 3NF: tidak ada ketergantungan transitif; atribut non-key tidak bergantung pada atribut non-key lain.

## Inventaris Tabel (Baseline)

Perintah baseline:

```bash
rg -o "Schema::create\\('\\w[^']*'" database/migrations | sed -E "s/.*'([^']+)'.*/\\1/" | sort -u
```

Tabel domain aktif:

- activities
- agenda_surats
- anggota_pokjas
- anggota_tim_penggeraks
- areas
- arsip_documents
- bantuans
- bkls
- bkrs
- buku_daftar_hadirs
- buku_keuangans
- buku_notulen_rapats
- buku_tamus
- data_industri_rumah_tanggas
- data_kegiatan_wargas
- data_keluargas
- data_pelatihan_kaders
- data_pemanfaatan_tanah_pekarangan_hatinya_pkks
- data_warga_anggotas
- data_wargas
- inventaris
- kader_khusus
- kejar_pakets
- koperasis
- laporan_tahunan_pkk_entries
- laporan_tahunan_pkk_reports
- module_access_override_audits
- module_access_overrides
- paars
- pilot_project_keluarga_sehat_reports
- pilot_project_keluarga_sehat_values
- pilot_project_naskah_pelaporan_attachments
- pilot_project_naskah_pelaporan_pelaksanaan_items
- pilot_project_naskah_pelaporan_reports
- posyandus
- prestasi_lombas
- program_prioritas
- program_prioritas_funding_sources
- program_prioritas_jadwal_months
- simulasi_penyuluhans
- taman_bacaans
- users
- warung_pkks

Tabel sistem/framework (di luar audit formal):

- cache
- cache_locks
- failed_jobs
- job_batches
- jobs
- password_reset_tokens
- sessions

## Temuan Awal (Risk-Based)

- 1NF high: `program_prioritas` memiliki repeating group `jadwal_bulan_1..12`, `jadwal_i..iv`, dan multi-value `sumber_dana_*`.
- 1NF high: `pilot_project_naskah_pelaporan_reports` memiliki repeating group `pelaksanaan_1..5`.
- 1NF possible: `pilot_project_naskah_pelaporan_reports.surat_tembusan` berpotensi multi-value (butuh konfirmasi kontrak domain).
- 2NF: tidak ditemukan primary key komposit pada tabel domain aktif (2NF umumnya N/A karena `id` surrogate).
- 3NF: kolom `level` bersama `area_id` adalah redundansi yang disetujui oleh kontrak canonical dan wajib konsisten.

## Prioritas Batch (Usulan Awal)

- Batch 1: normalisasi `program_prioritas` (jadwal + sumber dana) ke tabel anak/pivot.
- Batch 2: normalisasi `pilot_project_naskah_pelaporan_reports` (pelaksanaan) ke tabel anak berurutan.
- Batch 3: evaluasi field multi-value lain setelah konfirmasi kontrak domain.

## Status Batch

- Batch 1: implementasi awal (tabel `program_prioritas_jadwal_months` + `program_prioritas_funding_sources`, backfill, dan adapter repository/seeder).
- Batch 2: implementasi awal (tabel `pilot_project_naskah_pelaporan_pelaksanaan_items`, backfill, dan adapter repository/action/seeder).
