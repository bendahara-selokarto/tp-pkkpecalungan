# AI EXECUTION CONTRACT (RATE-LIMIT OPTIMIZED)

Dokumen ini adalah source of truth AI untuk repository ini.
Dokumen manusia ada di `README.md`.
Dokumen pedoman domain utama ada di `PEDOMAN_DOMAIN_UTAMA_101_150.md` (sumber: https://pubhtml5.com/zsnqq/vjcf/basic/101-150).

## 0. Priority

Jika konflik dokumen:
1. `AGENTS.md` (aturan teknis, arsitektur, eksekusi agent)
2. `PEDOMAN_DOMAIN_UTAMA_101_150.md` (terminologi/kontrak domain lampiran 4.9-4.15)
3. `README.md`

Aturan koherensi domain:
- Jika ada perbedaan istilah, label, atau kontrak domain antara dokumen internal dan pedoman utama, utamakan `PEDOMAN_DOMAIN_UTAMA_101_150.md`.
- Aspek teknis implementasi (arsitektur, policy/scope, quality gate, test matrix) tetap mengikuti `AGENTS.md`.

## 1. Fast Context

Stack:
- Laravel 12
- Inertia + Vue 3
- Tailwind
- Vite

Architecture:
- `Controller -> UseCase/Action -> Repository Interface -> Repository -> Model`

Authorization:
- `Policy -> Scope Service`

Domain canonical:
- `areas` adalah single source of truth wilayah.

Legacy tables (compatibility only):
- `kecamatans`
- `desas`
- `user_assignments`

## 2. Hard Invariants

- `areas` tetap single source of truth wilayah.
- Data domain wilayah wajib punya `level`, `area_id`, `created_by`.
- `level` data harus konsisten dengan `areas.level`.
- `role`, `scope`, `area_id` user harus konsisten.
- `area_id` user harus cocok levelnya dengan scope.
- Default orientasi output PDF adalah `landscape`; `portrait` hanya jika diminta eksplisit.
- Untuk dokumen autentik bertabel, hasil pembacaan wajib mencapai peta header tabel sampai tingkat penggabungan sel (`rowspan`/`colspan`) sebelum sinkronisasi kontrak/implementasi.

## 3. Execution Flow (Mandatory)

1. Analyze: baca file relevan, petakan dependency, identifikasi side effect.
2. Clarify: jika ambigu, tanya singkat dan spesifik.
3. Patch minimal: perubahan sekecil mungkin, hindari rewrite luas.
4. Validate: jalankan test/cek dampak, pastikan tidak ada behavior drift.

Flow pembacaan dokumen (wajib, terutama header tabel):
1. Baca:
   - Lakukan ekstraksi text-layer terlebih dahulu untuk token identitas dokumen.
   - Jika header tabel tidak terbaca utuh, wajib render visual halaman (screenshot) lalu verifikasi manual struktur tabel (jumlah kolom, merge row/col, label header).
2. Laporkan/Konfirmasi:
   - Laporkan hasil baca sampai level peta header + penggabungan sel (`rowspan`/`colspan`).
   - Jika level ini belum tercapai, status wajib `belum siap sinkronisasi` (tidak boleh lanjut implementasi).
3. Sinkronkan: sinkronkan kontrak domain (terminology/matrix/mapping) dan implementasi terkait hanya setelah konfirmasi peta header lengkap.

## 4. Rate-Limiter Efficiency Rules

- Scoped analysis only: baca file yang relevan, jangan scan seluruh project tanpa alasan.
- Diff-first: prioritaskan patch kecil dibanding regenerasi file panjang.
- Response compression: ringkas, padat, tanpa pengulangan konteks.
- State-aware: jangan ulang informasi yang sudah dikonfirmasi pada sesi yang sama.
- Jangan ubah file non-target.

## 5. Forbidden Patterns

- Fat controller.
- Business logic di controller/helper/view.
- Query domain baru di luar repository boundary.
- Service locator `app()` di use case/action/service.
- UI dianggap authority akses (authority tetap backend).

## 6. Quality Gate Before Finish

- Scope authorization tetap aman.
- Jika ada upgrade/migrasi legacy, wajib ada mapping dampak + fallback plan.
- Tidak ada coupling baru yang tidak perlu.
- Tidak ada drift `role` vs `scope` vs `areas.level`.
- Test relevan lulus (`php artisan test` untuk perubahan signifikan).
- Tidak ada perubahan perilaku yang tidak diminta.
- Untuk dokumen autentik bertabel: peta header sampai level merge (`rowspan`/`colspan`) sudah tervalidasi sebelum patch implementasi.

## 7. New Menu/Domain Protocol (Mandatory)

Urutan eksekusi untuk modul/menu baru:
1. Tetapkan kontrak: nama domain, scope target, role aktif, boundary data.
2. Route + middleware: gunakan `scope.role:{desa|kecamatan}`.
3. Request: validasi + normalisasi input (tanggal UI ke format canonical).
4. UseCase/Action: business flow hanya di layer ini.
5. Repository Interface + Repository: semua query domain lewat boundary repository.
6. Policy + Scope Service: source of truth akses backend.
7. Inertia page mapping: data disiapkan backend, frontend hanya consume.
8. Tests: penuhi matrix minimum di bagian 8.
9. Dashboard trigger (wajib): setiap menu/domain baru harus memicu audit dashboard dan penyesuaian representasi dashboard.
   - Minimal cek: apakah menu baru masuk KPI coverage, chart coverage, dan ringkasan progress input.
   - Jika tidak relevan ditampilkan, wajib tulis justifikasi eksplisit pada dokumen perubahan.

Aturan konsistensi generasi kode:
- Gunakan enum (`ScopeLevel`) untuk scope/level di PHP, hindari literal berulang.
- Untuk flow yang bergantung wilayah, `area_id` harus jadi acuan canonical level.
- Jangan jadikan frontend sebagai authority akses.
- Untuk flow user management, cegah mutasi `super-admin` pada path administratif.

## 8. Minimum Test Matrix (Mandatory)

Untuk modul/menu baru, minimal harus ada:
1. Feature test jalur sukses untuk role/scope valid.
2. Feature test tolak role tidak valid.
3. Feature test tolak mismatch role-area level (stale metadata scenario).
4. Unit test policy/scope service untuk akses inti (`view`, `update`/`delete`).
5. Jika ada scoped query kompleks, tambah test use case/repository anti data leak.
6. Jalankan `php artisan test` sebelum final report.

## 9. Output Contract

- Laporan harus menyebut: apa yang diubah, kenapa, file terdampak, dan hasil validasi.
- Jika gagal, laporkan root cause + opsi solusi + dampak tiap opsi.

## 10. AI-Friendly Pattern Evolution

- Pattern eksekusi reusable lintas domain disimpan di `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`.
- Jika ditemukan jalur baru yang lebih efisien/akurat/valid, update playbook tersebut pada sesi yang sama.
- Jika pattern lama sudah kurang efektif, ubah status pattern (mis. `deprecated`) dan tulis alternatifnya.
- Jangan simpan pattern penting hanya di chat; wajib masuk dokumen agar bisa dipakai project berikutnya.

## 11. Temporary Pre-Release Upgrade Policy (Aktif)

Status saat ini:
- Aplikasi belum release; reset data development diperbolehkan.
- AI boleh melakukan upgrade/refactor dependency legacy secara terkontrol.

Aturan eksekusi:
- `php artisan migrate:fresh` diperbolehkan untuk pekerjaan refactor struktur data.
- Saat memakai `migrate:fresh`, laporkan eksplisit bahwa seluruh data lokal ter-reset.
- Upgrade legacy wajib mengarah ke pengurangan coupling ke tabel legacy, bukan menambah debt baru.
- Setiap perubahan legacy wajib menyertakan:
  1) tujuan migrasi,
  2) area terdampak (route/request/use case/repository/test),
  3) rollback/fallback plan minimal level teknis.

Guardrail tetap:
- Otorisasi backend tidak boleh melemah.
- `areas` tetap canonical wilayah.
- Konsistensi `role` vs `scope` vs `areas.level` tidak boleh drift.

## 12. Markdown Documentation Rules (Upgrade)

Aturan markdown operasional:
- Semua rencana aksi lintas-file wajib dibuat dalam dokumen TODO terpisah di `docs/process/`.
- Gunakan format checklist `- [ ]` untuk task dan `- [x]` untuk task selesai.
- Setiap TODO wajib memuat: konteks, target hasil, langkah eksekusi, validasi, risiko, keputusan.
- Setiap update dokumen harus ringkas, diff-first, dan hindari pengulangan konteks yang sama.
- Perubahan dengan sinyal canonical wajib mengupdate minimal satu markdown arsitektur (`AGENTS.md` / playbook / terminology map) dan diverifikasi oleh CI gate.
