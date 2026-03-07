# Referensi Domain (Lokal)

Folder ini menyimpan bahan referensi domain (PDF/Excel/screenshot) yang dipakai untuk sinkronisasi kontrak.

Aturan repo:
- File referensi besar **tidak** di-versioning secara default.
- Yang di-track hanya dokumen kontrol ini dan marker `.gitkeep`.
- Simpan artefak lokal pada `docs/referensi/_local/`.

Dokumen canonical aktif yang wajib tersedia di environment kerja:
- `docs/referensi/Rakernas X.pdf`

Jika file canonical belum tersedia:
1. Ambil dari sumber internal tim.
2. Simpan ke path persis `docs/referensi/Rakernas X.pdf`.
3. Jangan commit file referensi biner ke git kecuali diputuskan eksplisit di concern terpisah.
