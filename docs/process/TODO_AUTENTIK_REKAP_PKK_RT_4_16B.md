# TODO Autentik Rekap PKK RT 4.16b

## Konteks
- Dokumen autentik baru ditetapkan: `d:\pedoman\181.pdf`.
- Judul dokumen: `REKAPITULASI CATATAN DATA DAN KEGIATAN WARGA KELOMPOK PKK RT`.
- Dokumen menampilkan rekap tabel hingga kolom `30` dengan header bertingkat.

## Target Hasil
- Struktur autentik 4.16b terdokumentasi sebagai referensi kontrak domain.
- Terminologi dan matrix domain menyebut status implementasi 4.16b secara eksplisit.
- Gap implementasi saat ini tercatat agar tidak terjadi salah asumsi coverage.

## Keputusan
- [x] `d:\pedoman\181.pdf` dijadikan sumber autentik Lampiran 4.16b.
- [x] Parser Node.js diposisikan untuk identitas dokumen, bukan source of truth header tabel kompleks.
- [x] Struktur header 30 kolom dikunci pada dokumen mapping domain.

## Langkah Eksekusi
- [x] Baca dokumen autentik sesuai flow `Baca -> Laporkan/Konfirmasi -> Sinkronkan`.
- [x] Buat mapping domain: `docs/domain/REKAP_PKK_RT_4_16B_MAPPING.md`.
- [x] Sinkronkan terminology map dan domain contract matrix.
- [ ] Putuskan apakah akan dibuat modul/report baru Lampiran 4.16b atau dipertahankan sebagai referensi rekap lintas-modul.

## Validasi
- [x] Dokumen mapping 4.16b tersedia.
- [x] Dokumen domain utama menunjuk referensi 4.16b.
- [ ] Tersedia test baseline jika modul/report 4.16b diimplementasikan.

## Risiko
- Risiko salah tafsir kolom jika implementasi baru dibuat tanpa mengacu mapping merge header.
- Risiko drift data jika rekap dianggap sudah terimplementasi padahal belum ada layout 30 kolom di aplikasi.

## Fallback Plan
- [x] Jika parser gagal baca header detail, gunakan verifikasi visual dokumen autentik sebagai sumber final.
- [x] Pertahankan status `reference-only` sampai ada keputusan implementasi teknis.
