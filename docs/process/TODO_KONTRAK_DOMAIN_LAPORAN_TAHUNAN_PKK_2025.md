# TODO KLT25A1 Kontrak Domain Laporan Tahunan PKK 2025
Tanggal: 2026-03-02 (normalisasi metadata; perlu verifikasi historis)  
Status: `done`

## Konteks
- User menyediakan dokumen contoh domain: `docs/referensi/LAPORAN TAHUNAN PKK th 2025.docx`.
- Domain matrix saat ini belum memiliki kontrak resmi untuk laporan tahunan berbasis dokumen tersebut.
- Diperlukan lock kontrak data canonical sebagai baseline backend sebelum implementasi modul/menu.

## Target Hasil
- Tersedia dokumen mapping canonical untuk laporan tahunan PKK 2025.
- Tersedia entri resmi pada matrix kontrak domain.
- Terminologi user-facing untuk concern ini terkunci agar tidak drift.
- Deviasi terhadap pedoman utama (karena sumber adalah dokumen lokal) tercatat resmi.
- Kontrak output terkunci: laporan diekspor sebagai `1 file utuh` berformat `.docx` dengan urutan konten sama seperti dokumen contoh.
- Kontrak source data terkunci: isi laporan (terutama kegiatan) wajib berasal dari database aplikasi.
- Kontrak agregasi data terkunci: isi laporan boleh diambil dari lintas tabel sesuai kebutuhan ringkasan kegiatan tahunan.
- Kontrak fallback data terkunci: jika data tidak ditemukan, sistem boleh menyediakan form isian pelengkap laporan tahunan.

## Langkah Eksekusi
- [x] `L1` Validasi keberadaan dan keterbacaan dokumen sumber `.docx`.
- [x] `L2` Ekstraksi text-layer `word/document.xml` dan identifikasi struktur tabel.
- [x] `L3` Pemetaan struktur merge cell (`rowspan/colspan`) per tabel.
- [x] `L4` Susun dokumen mapping canonical `docs/domain/LAPORAN_TAHUNAN_PKK_2025_MAPPING.md`.
- [x] `L5` Sinkronkan `docs/domain/DOMAIN_CONTRACT_MATRIX.md`.
- [x] `L6` Sinkronkan `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`.
- [x] `L7` Catat deviasi sumber pada `docs/domain/DOMAIN_DEVIATION_LOG.md`.
- [x] `L8` Implementasi backend modul `laporan-tahunan-pkk` (route/request/use case/repository/policy + test matrix minimum).
- [x] `L9` Implementasi generator output `.docx` `single-file` dengan urutan dokumen identik contoh (teknologi bebas; `Laravel Office` opsional).
- [x] `L10` Implementasi binding template structure -> data runtime dari database (metadata, kegiatan, narasi evaluasi/penutup).
- [x] `L11` Implementasi agregasi lintas tabel pada repository laporan tahunan dengan guardrail scope (`level`, `area_id`) dan anti data leak.
- [x] `L12` Implementasi form isian pelengkap saat data DB tidak ditemukan, termasuk persistence + policy/scope + validasi input.

## Validasi
- [x] `Test-Path` file sumber mengembalikan `True`.
- [x] Ekstraksi XML `.docx` sukses, tabel terdeteksi: `7`.
- [x] Struktur merge cell terdeteksi:
  - Tabel `1-5`: `4` kolom, `mergeCellMarkers=0`.
  - Tabel `6`: `1` kolom, `mergeCellMarkers=0`.
  - Tabel `7`: `2` kolom, `mergeCellMarkers=0`.
- [x] Sinkronisasi lintas dokumen canonical selesai (matrix + terminology + deviation log + mapping).
- [x] CRUD scoped desa/kecamatan + policy/scope + menu sekretaris aktif.
- [x] Generator `.docx` single-file berjalan melalui route print per laporan.
- [x] Validasi test matrix minimum dan regression:
  - targeted: `17` test pass.
  - full suite: `745` test pass.

## Risiko
- Dokumen sumber adalah contoh lokal, bukan lampiran resmi 4.9-4.24, sehingga tidak memiliki nomor lampiran canonical.
- Header tabel kegiatan tidak menyertakan baris label eksplisit pada beberapa bagian; pemetaan kolom diturunkan dari struktur kolom aktual + konteks narasi.
- Risiko salah implementasi visual jika tabel OOXML dianggap tabel ber-border, padahal fungsinya hanya layout naskah.
- Risiko drift data jika generator mengambil isi dari dokumen contoh, bukan dari query/repository aplikasi.
- Risiko data leak jika agregasi lintas tabel tidak dipagari filter scope wilayah secara konsisten.
- Risiko duplikasi/konflik data jika form pelengkap tidak memiliki aturan prioritas sumber data saat generate dokumen.
- Modul tidak masuk dashboard coverage canonical dan dikecualikan eksplisit sebagai ekstensi lokal.

## Keputusan
- [x] Concern ini dikunci sebagai `Ekstensi Lokal 2025`.
- [x] Sumber referensi utama concern ini tetap dokumen lokal yang user berikan.
- [x] Kontrak data difokuskan pada tiga blok: metadata laporan, daftar kegiatan per bidang, narasi evaluasi/penutup.
- [x] Implementasi modul ditunda ke task terpisah setelah kontrak disetujui user.
- [x] Kontrak output wajib `single-file utuh` sesuai urutan dokumen contoh.
- [x] Format output concern ini dikunci ke `.docx` dan diperlakukan sebagai naskah (layout grid tanpa border).
- [x] Dokumen contoh dipakai hanya sebagai struktur/layout; isi runtime wajib diambil dari database aplikasi.
- [x] Isi runtime boleh berasal dari lintas tabel untuk kebutuhan ringkasan umum kegiatan tahunan.
- [x] Jika data tidak ditemukan, sistem boleh membuat form isian baru untuk melengkapi dokumen.
- [x] `Laravel Office` dicatat sebagai opsi teknis, bukan instruksi wajib.
- [x] Implementasi concern dinyatakan selesai end-to-end (backend, UI, export, test, sinkronisasi dokumen).
