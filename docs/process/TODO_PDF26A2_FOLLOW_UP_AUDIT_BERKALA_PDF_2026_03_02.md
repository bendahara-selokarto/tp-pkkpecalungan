# TODO PDF26A2 Follow-Up Audit Berkala PDF 2026-03-02

Tanggal: 2026-03-02  
Status: `in-progress`  
Related ADR: `-`

## Konteks
- Concern ini adalah turunan operasional dari `TODO_PDF26A1_AUDIT_KETERSEDIAAN_FORMAT_PDF_2026_02_28.md`.
- `PDF26A1` dikunci sebagai baseline audit awal (snapshot), sedangkan siklus audit berkala dipindahkan ke dokumen ini agar status concern tidak drift.

## Target Hasil
- Audit berkala route PDF berjalan konsisten per siklus tanpa mengubah baseline audit awal.
- Registry temuan PDF yatim (`A/B/C/D`) terus diperbarui pada dokumen induk (`PDF26A1`) dengan jejak eksekusi yang jelas.

## Langkah Eksekusi
- [ ] Jalankan inventory route PDF dan print PDF.
- [ ] Jalankan scan trigger UI (`literal` + `dinamis`).
- [ ] Validasi controller -> view PDF.
- [ ] Klasifikasikan temuan dengan kategori yatim `A/B/C/D`.
- [ ] Update registry tabel pada dokumen induk (`PDF26A1`).
- [ ] Tutup gap sebelum rilis jika status masih `open`.

## Validasi
- [ ] `php artisan route:list --path=report/pdf --json --except-vendor`
- [ ] `php artisan route:list --path=print --json --except-vendor`
- [ ] `php artisan test` pada batch rilis jika concern ini disertakan ke release candidate.

## Risiko
- False-positive audit jika route dinamis tidak dimasukkan (`scopePrefix`/`routes.print`).
- Drift registry jika update temuan tidak disinkronkan ke dokumen induk.

## Keputusan
- [x] Concern operasional berkala dipisah dari concern baseline audit awal.
- [x] Dokumen induk (`PDF26A1`) tetap jadi referensi baseline; concern ini menjadi jalur eksekusi periodik.

## Output Final
- [ ] Ringkasan hasil audit siklus ini.
- [ ] Daftar temuan baru + status (`open/resolved`).
- [ ] Bukti validasi command yang dijalankan.
