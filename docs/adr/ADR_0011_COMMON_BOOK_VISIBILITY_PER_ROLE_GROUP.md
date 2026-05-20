# ADR 0011 Common Book Visibility Per Role Group

Status: `accepted`

Tanggal: 2026-05-02

## Konteks

Owner mengunci struktur administrasi organisasi menjadi dua kategori umum: `buku wajib` dan `buku pembantu`.

Revisi owner 2026-05-06: `bendahara-tpk` tidak menjadi pemilik modul pada struktur administrasi aktual ini. Catatan saat itu menempatkan `inventaris` sebagai buku bantu seragam; bagian ini disupersede oleh revisi 2026-05-20. Tiap Pokja tetap memiliki buku pembantu unik dengan format berbeda.

Revisi owner 2026-05-20: khusus Sekretaris memiliki kategori ketiga `penunjang buku wajib`. `Buku Inventaris` dipindahkan dari buku pembantu seragam menjadi buku wajib Sekretaris. Buku pembantu bersama untuk semua role aktif hanya `Buku Prestasi`, `Buku Bantuan`, dan `Buku Kader Khusus`.

## Keputusan

`RoleMenuVisibilityService` menjadi source of truth visibilitas.

`sekretaris-tpk` memiliki buku wajib `agenda-surat`, daftar anggota TP PKK, `inventaris`, `activities`, dan `buku-notulen-rapat`; buku pembantu bersama `prestasi-lomba`, `bantuans`, dan `kader-khusus`; serta penunjang `catatan-keluarga` untuk data umum dan `program-prioritas`.

Pokja I-IV masing-masing memiliki buku pembantu bersama serta buku pembantu spesifik Pokja. Link menu/sidebar/cetak untuk modul seragam wajib membawa konteks `book_group` agar akun multi-role tetap melihat satu buku jabatan yang sedang dibuka, bukan gabungan seluruh group role user. Slug data kegiatan aktual: Pokja I memakai `data-kegiatan-pkk-pokja-i`; Pokja II-IV masih memakai route cetak di bawah slug `catatan-keluarga` sampai modul input spesifik tersedia.

Buku bantu unik yang sudah memiliki slug modul saat ini:

- Pokja I: `simulasi-penyuluhan`, `bkr`, `anggota-pokja`, `paar`.
- Pokja II: `pra-koperasi-up2k`.
- Pokja III: `data-keluarga`, `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, `data-industri-rumah-tangga`.
- Pokja IV: `posyandu` dan `catatan-keluarga` untuk data umum/data kegiatan.

Item aktual yang belum punya slug modul khusus tidak dipaksakan ke modul lain. Gap ini mencakup antara lain grafik, IVA test, ASI eksklusif, konsultasi, kas Pokja III, Buku Rumah Sehat dan Anak Sehat, Buku Bantu Pangan, sub-buku simulasi terpisah, dan data pengunjung.

Untuk modul yang dipakai lintas role-group dengan format sama, isolasi data wajib memakai `level + area_id + tahun_anggaran + group`.

## Dampak

- Sidebar dan menu cetak mengikuti struktur aktual Sekretaris dan Pokja I-IV.
- `inventaris` tidak lagi menjadi buku pembantu bersama; perubahan runtime perlu memindahkan visibilitasnya ke Buku Wajib Sekretaris atau buku spesifik Pokja III jika format/slugnya dikunci.
- Guard anti-duplikasi sidebar hanya mengizinkan slug yang memang dipakai lintas group.
- Modul tanpa slug spesifik tetap menjadi backlog/gap, bukan diberi owner salah.
- Policy dan scope backend tetap menjadi authority akses; UI bukan sumber izin.

## Fallback

Jika ditemukan privilege escalation atau kebocoran data, rollback dilakukan dengan menghapus slug terdampak dari group target di `RoleMenuVisibilityService`, mengembalikan whitelist anti-duplikasi frontend ke baseline terakhir yang lulus, dan rollback migration `group` pada tabel terdampak bila perlu.
