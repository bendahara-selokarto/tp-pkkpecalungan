# TODO RPB26A1 Rencana Perbaikan Koreksi Non-Teknis

Tanggal: 2026-03-10  
Status: `planned`
Related ADR: `-`

## Aturan Pakai

- `KODE_UNIK` wajib 4-8 karakter, huruf kapital + angka (contoh: `A2B9`).
- Format judul wajib: `TODO <KODE_UNIK> <Judul Ringkas>`.
- Simpan file dengan pola: `TODO_<KODE_UNIK>_<RINGKASAN>_<YYYY_MM_DD>.md`.
- Gunakan checklist `- [ ]` dan ubah ke `- [x]` saat item selesai.

## Konteks

- Ada beberapa catatan perbaikan yang bersifat non-teknis agar proyek makin mudah dipahami dan konsisten di lapangan.
- Fokus utama: merapikan warisan lama, menyatukan istilah/aturan, memperkuat konsistensi data antar bagian, dan menyiapkan komunikasi perubahan.

## Kontrak Concern (Lock)

- Domain: Perbaikan tata kelola data dan komunikasi non-teknis.
- Role/scope target: Tim pengelola, pemangku kepentingan, dan pengguna akhir (non-teknis).
- Boundary data: Dokumentasi, istilah, aturan kerja, serta rencana transisi dan komunikasi perubahan.
- Acceptance criteria: Istilah konsisten, rencana transisi jelas, aturan konsistensi dipahami, dan jadwal perbaikan disepakati.
- Dampak keputusan arsitektur: `tidak`

## Target Hasil

- [ ] Rencana transisi dari struktur lama yang jelas dan tidak meninggalkan ketergantungan baru.
- [ ] Istilah dan aturan disatukan sehingga semua dokumen “berbicara” dengan bahasa yang sama.
- [ ] Aturan konsistensi data antar bagian diperjelas agar mengurangi kesalahan operasional.
- [ ] Ringkasan perubahan besar yang mudah dipahami oleh tim non-teknis selalu tersedia.
- [ ] Jadwal perbaikan bertahap disepakati agar operasional harian tidak terganggu.

## Langkah Eksekusi

- [ ] Audit istilah dan aturan yang masih berbeda di dokumen terkait.
- [ ] Petakan sisa ketergantungan warisan lama dan opsi transisinya.
- [ ] Susun aturan konsistensi data yang mudah dipahami dan disetujui bersama.
- [ ] Siapkan ringkasan komunikasi perubahan (bahasa non-teknis).
- [ ] Bentuk jadwal perbaikan bertahap dan konfirmasi dengan pemangku kepentingan.

## Validasi

- [ ] L1: Review konsistensi istilah dan aturan di dokumen utama.
- [ ] L2: Uji pemahaman dengan perwakilan pengguna (apakah jelas dan operasional).
- [ ] L3: Jika berdampak ke sistem, lakukan validasi sesuai prosedur internal.

## Risiko

- Risiko 1: Perubahan istilah membuat kebingungan sementara jika komunikasi tidak jelas.
- Risiko 2: Jadwal perbaikan bertahap berbenturan dengan kebutuhan operasional mendesak.

## Keputusan

- [ ] K1: Prioritas area yang ditransisikan lebih dulu.
- [ ] K2: Format ringkasan perubahan yang paling mudah dipahami tim non-teknis.

## Keputusan Arsitektur (Jika Ada)

- [ ] Tidak ada keputusan arsitektur baru (konfirmasi).

## Fallback Plan

- Jika perubahan mengganggu operasional, hentikan tahap berikutnya, kembali ke aturan sebelumnya, dan ulangi sosialisasi.

## Output Final

- [ ] Ringkasan apa yang diubah dan kenapa.
- [ ] Daftar file terdampak.
- [ ] Hasil validasi + residual risk.
