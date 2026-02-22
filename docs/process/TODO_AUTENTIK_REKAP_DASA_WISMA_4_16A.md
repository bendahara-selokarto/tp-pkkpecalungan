# TODO Autentik Rekap Dasa Wisma 4.16a

## Konteks
- Dokumen autentik baru ditetapkan: `d:\pedoman\179.pdf`.
- Judul dokumen: `REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK DASA WISMA`.
- Dokumen menampilkan rekap tabel hingga kolom `29` dengan header bertingkat.

## Target Hasil
- Struktur autentik 4.16a terdokumentasi sebagai referensi kontrak domain.
- Terminologi dan matrix domain menyebut status implementasi 4.16a secara eksplisit.
- Gap implementasi saat ini tercatat agar tidak terjadi salah asumsi coverage.

## Keputusan
- [x] `d:\pedoman\179.pdf` dijadikan sumber autentik Lampiran 4.16a.
- [x] Parser Node.js diposisikan untuk identitas dokumen, bukan source of truth header tabel kompleks.
- [x] Struktur header 29 kolom dikunci pada dokumen mapping domain dengan penanda area yang perlu transkripsi final.

## Langkah Eksekusi
- [x] Baca dokumen autentik sesuai flow `Baca -> Laporkan/Konfirmasi -> Sinkronkan`.
- [x] Buat mapping domain: `docs/domain/REKAP_DASA_WISMA_4_16A_MAPPING.md`.
- [x] Sinkronkan terminology map dan domain contract matrix.
- [ ] Finalisasi transkripsi label sub-header bertanda `(?)` langsung dari dokumen autentik.
- [ ] Putuskan apakah akan dibuat modul/report baru Lampiran 4.16a atau dipertahankan sebagai referensi rekap lintas-modul.

## Validasi
- [x] Dokumen mapping 4.16a tersedia.
- [x] Dokumen domain utama menunjuk referensi 4.16a.
- [ ] Tersedia test baseline jika modul/report 4.16a diimplementasikan.

## Risiko
- Risiko salah tafsir kolom jika transkripsi sub-header kecil tidak difinalkan.
- Risiko drift data jika rekap dianggap sudah terimplementasi padahal belum ada layout 29 kolom di aplikasi.

## Fallback Plan
- [x] Jika parser gagal baca header detail, gunakan verifikasi visual dokumen autentik sebagai sumber final.
- [x] Pertahankan status `reference-only` sampai ada keputusan implementasi teknis.
