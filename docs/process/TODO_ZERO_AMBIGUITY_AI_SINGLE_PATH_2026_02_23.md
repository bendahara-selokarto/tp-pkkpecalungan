# TODO Zero Ambiguity AI Single Path (2026-02-23)

## Konteks
- User meminta arsitektur project yang `zero ambiguity` agar AI sesi berikutnya berjalan pada jalur tunggal.
- Dokumen canonical saat ini sudah kuat (`AGENTS.md` + playbook), tetapi belum ada satu dokumen routing tunggal yang mengikat keputusan eksekusi end-to-end.
- Concern ini memicu `doc-hardening pass` karena menyentuh kontrak canonical lintas dokumen.

## Target Hasil
- Tersedia dokumen arsitektur tunggal AI yang memetakan:
  - jalur keputusan deterministik,
  - routing task -> file target -> validasi wajib,
  - aturan anti-ambiguity saat konflik instruksi.
- `AGENTS.md` menunjuk eksplisit ke dokumen tunggal tersebut.
- Playbook memiliki pattern reusable agar jalur tunggal menjadi kebiasaan operasional lintas sesi.

## Langkah Eksekusi
- [x] `Z1` Buat dokumen arsitektur jalur tunggal AI di `docs/process/AI_SINGLE_PATH_ARCHITECTURE.md`.
- [x] `Z2` Sinkronkan `AGENTS.md` agar dokumen tersebut menjadi referensi operasional wajib.
- [x] `Z3` Tambah pattern registry di playbook untuk menjaga keberlanjutan jalur tunggal lintas sesi.
- [x] `Z4` Jalankan doc-hardening pass (cek koherensi istilah, status, dan referensi dokumen).
- [x] `Z5` Catat hasil hardening ke `docs/process/OPERATIONAL_VALIDATION_LOG.md`.

## Validasi
- [x] Referensi lintas dokumen valid dan konsisten (`AGENTS.md` -> single-path doc -> playbook).
- [x] Tidak ada perubahan kode runtime (hanya hardening dokumentasi arsitektur).
- [x] Struktur dokumen memuat konteks, keputusan, risiko, dan jalur validasi yang dapat dieksekusi ulang.

## Risiko
- [x] Risiko over-constraint: AI bisa terlalu kaku jika semua task dipaksa template yang sama.
- [x] Risiko drift: dokumen tunggal usang jika tidak ikut diperbarui saat pola baru muncul.

## Keputusan Dikunci
- [x] Dokumen jalur tunggal AI menjadi acuan operasional utama untuk routing kerja AI, dengan prioritas tetap tunduk pada `AGENTS.md`.
- [x] Perubahan besar berikutnya yang memengaruhi eksekusi AI wajib memperbarui dokumen ini pada sesi yang sama.
- [x] Pattern playbook ditambah agar mekanisme ini reusable lintas concern.
