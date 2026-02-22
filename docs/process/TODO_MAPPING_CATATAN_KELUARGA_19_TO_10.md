# TODO Mapping Catatan Keluarga 19 -> 10

## Konteks
- Lampiran autentik Catatan Keluarga (`d:\pedoman\177.pdf`) memakai tabel fisik 19 kolom.
- Implementasi report aplikasi saat ini menggunakan representasi operasional 10 kolom.
- Diperlukan kontrak transformasi yang terdokumentasi agar tidak terjadi drift istilah/struktur.

## Target Hasil
- Tersedia dokumen mapping resmi antara layout autentik 19 kolom dan report operasional 10 kolom.
- Terminologi domain 4.15 menyatakan status transformasi secara eksplisit.
- Playbook memuat pattern validasi PDF yang sesuai untuk dokumen dengan merge-header kompleks.

## Keputusan
- [x] Struktur autentik 19 kolom menjadi referensi domain utama.
- [x] Report 10 kolom diperlakukan sebagai proyeksi operasional (bukan pengganti struktur autentik).
- [x] Pembacaan Node.js diposisikan sebagai alat bantu deteksi token identitas, bukan sumber kebenaran struktur tabel merge.

## Langkah Eksekusi
- [x] Buat dokumen mapping `docs/domain/CATATAN_KELUARGA_19_TO_10_MAPPING.md`.
- [x] Update istilah domain 4.15 pada `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`.
- [x] Update kontrak domain 4.15 pada `docs/domain/DOMAIN_CONTRACT_MATRIX.md`.
- [x] Tambah pattern validasi PDF merge-header pada `docs/process/AI_FRIENDLY_EXECUTION_PLAYBOOK.md`.
- [ ] (Lanjutan) Finalkan transkripsi label detail kolom fisik 1-19 secara penuh dari lampiran autentik.
- [ ] (Lanjutan) Putuskan apakah report aplikasi tetap 10 kolom atau ditingkatkan ke 19 kolom.

## Validasi
- [x] Dokumen mapping baru tersedia dan terhubung dari dokumen domain utama.
- [x] Terminology map menyebut status transformasi 4.15 secara eksplisit.
- [x] Playbook memiliki guardrail untuk parsing PDF tabel merge.
- [ ] (Lanjutan) Coverage test header report mencakup keputusan final 10 kolom vs 19 kolom.

## Risiko
- Risiko salah tafsir jika struktur autentik 19 kolom tidak dibedakan dari representasi report 10 kolom.
- Risiko gap data jika ada kolom autentik yang belum dipetakan ke model/domain aplikasi.
- Risiko false confidence jika OCR/parser teks dipakai sebagai satu-satunya sumber kebenaran.

## Fallback Plan
- [x] Gunakan dokumen autentik sebagai sumber final saat terjadi konflik hasil parsing.
- [x] Pertahankan baseline test header report 10 kolom hingga keputusan perubahan struktur disetujui.
- [ ] Jika diputuskan migrasi ke 19 kolom, lakukan bertahap: kontrak domain -> repository -> PDF view -> test fixture.
