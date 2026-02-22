# TODO Flow Baca-Lapor-Sinkron Header Tabel Dokumen

## Konteks
- Beberapa lampiran pedoman memiliki header tabel kompleks (multi-row, merge row/col).
- Parser text-layer otomatis tidak selalu bisa merekonstruksi struktur header secara penuh.
- Diperlukan flow operasional baku agar pembacaan dokumen tetap akurat dan sinkron dengan kontrak domain.

## Target Hasil
- Flow pembacaan dokumen resmi: `Baca -> Laporkan/Konfirmasi -> Sinkronkan`.
- Flow ini tercatat di dokumen arsitektur dan playbook agar reusable lintas domain.
- Ada guardrail eksplisit untuk kasus header tabel kompleks.

## Keputusan
- [x] Flow baku untuk pembacaan dokumen ditetapkan sebagai `Baca -> Laporkan/Konfirmasi -> Sinkronkan`.
- [x] Flow diprioritaskan untuk kasus header tabel kompleks.
- [x] Dokumen autentik tetap source of truth saat hasil parser bertentangan.

## Langkah Eksekusi
- [x] Tambahkan flow pembacaan dokumen pada `AGENTS.md`.
- [x] Selaraskan pattern playbook (`P-009`) dengan urutan `Baca -> Laporkan/Konfirmasi -> Sinkronkan`.
- [x] Simpan TODO ini sebagai jejak operasional perubahan lintas-file.

## Validasi
- [x] `AGENTS.md` memuat flow pembacaan dokumen eksplisit.
- [x] `AI_FRIENDLY_EXECUTION_PLAYBOOK.md` memuat urutan langkah yang sama pada pattern PDF.
- [x] Tidak ada perubahan perilaku aplikasi; perubahan terbatas pada dokumentasi proses.

## Risiko
- Risiko bypass flow saat pekerjaan cepat jika tidak dijadikan checklist rutin.
- Risiko drift kontrak jika langkah konfirmasi dilewati.

## Fallback Plan
- [x] Gunakan dokumen autentik sebagai acuan akhir jika hasil baca parser tidak konsisten.
- [x] Wajib dokumentasikan gap parsing pada laporan sebelum sinkronisasi implementasi.
