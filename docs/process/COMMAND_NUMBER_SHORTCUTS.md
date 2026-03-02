# Command Number Shortcuts

Tujuan:
- Menyederhanakan instruksi ke AI dengan cukup kirim nomor perintah.

Cara pakai:
- Kirim angka saja, contoh: `3`.
- Bisa gabung beberapa nomor, contoh: `1,3,7`.

## Daftar Perintah

1. Eksekusi todo di `docs/process/TODO_[NAMA].md` sampai end-to-end.
2. Lanjutkan dari status terakhir, selesaikan semua task yang masih `[ ]`.
3. Commit by concern.
4. Implementasi concern ini end-to-end: kode, test, dokumentasi, validasi.
5. Jangan berhenti di analisis, langsung eksekusi.
6. Patch minimal, jangan ubah file di luar concern.
7. Setelah selesai, jalankan `php -d memory_limit=512M artisan test --compact` lalu laporkan hasilnya.
8. Jalankan validasi report: `php artisan route:list --name=report` dan test terkait.
9. Untuk dokumen autentik: baca -> laporkan/konfirmasi -> sinkronkan -> implementasi.
10. Jika ada deviasi dari pedoman, catat di `docs/domain/DOMAIN_DEVIATION_LOG.md`.
11. Update `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md` + `docs/domain/DOMAIN_CONTRACT_MATRIX.md` jika kontrak berubah.
12. Sebutkan saya harus cek di menu UI bagian mana.
13. Eksekusi todo `[file]` sampai end-to-end, commit by concern, jalankan test, dan sinkronkan dokumen kontrak.
14. Generate TODO concern baru via `powershell -File scripts/generate_todo.ps1 -Code <KODE> -Title "<Judul>"`.
