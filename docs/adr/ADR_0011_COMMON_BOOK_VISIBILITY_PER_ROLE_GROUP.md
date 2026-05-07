# ADR 0011 Common Book Visibility Per Role Group

Status: `accepted`

Tanggal: 2026-05-02

## Konteks

Owner mengunci struktur administrasi organisasi menjadi tiga kelompok: `buku wajib`, `buku bantu`, dan `buku penunjang buku wajib`.

Revisi owner 2026-05-06: `bendahara-tpk` tidak menjadi pemilik modul pada struktur administrasi aktual ini. Buku bantu seragam adalah `prestasi-lomba`, `bantuans`, `inventaris`, dan `kader-khusus`; dimiliki Sekretaris dan Pokja I-IV. Tiap Pokja juga memiliki buku bantu unik dengan format berbeda.

## Keputusan

`RoleMenuVisibilityService` menjadi source of truth visibilitas.

`sekretaris-tpk` memiliki buku wajib `agenda-surat`, `anggota-tim-penggerak`, `anggota-tim-penggerak-kader`, `activities`, dan `buku-notulen-rapat`; buku bantu seragam `prestasi-lomba`, `bantuans`, `inventaris`, `kader-khusus`; serta penunjang `catatan-keluarga` untuk data umum dan `program-prioritas`.

Pokja I-IV masing-masing memiliki `program-prioritas`, buku data kegiatan, `activities`, dan buku bantu seragam. Link menu/sidebar/cetak untuk modul seragam wajib membawa konteks `book_group` agar akun multi-role tetap melihat satu buku jabatan yang sedang dibuka, bukan gabungan seluruh group role user. Slug data kegiatan aktual: Pokja I memakai `data-kegiatan-pkk-pokja-i`; Pokja II-IV masih memakai route cetak di bawah slug `catatan-keluarga` sampai modul input spesifik tersedia.

Buku bantu unik yang sudah memiliki slug modul saat ini:

- Pokja I: `simulasi-penyuluhan`, `bkr`, `anggota-pokja`, `paar`.
- Pokja II: `pra-koperasi-up2k`.
- Pokja III: `data-keluarga`, `data-pemanfaatan-tanah-pekarangan-hatinya-pkk`, `data-industri-rumah-tangga`.
- Pokja IV: `posyandu` dan `catatan-keluarga` untuk data umum/data kegiatan.

Item aktual yang belum punya slug modul khusus tidak dipaksakan ke modul lain. Gap ini mencakup antara lain grafik, IVA test, ASI eksklusif, konsultasi, kas Pokja III, dan sub-buku simulasi terpisah.

Untuk modul yang dipakai lintas role-group dengan format sama, isolasi data wajib memakai `level + area_id + tahun_anggaran + group`.

## Dampak

- Sidebar dan menu cetak mengikuti struktur aktual Sekretaris dan Pokja I-IV.
- Guard anti-duplikasi sidebar hanya mengizinkan slug yang memang dipakai lintas group.
- Modul tanpa slug spesifik tetap menjadi backlog/gap, bukan diberi owner salah.
- Policy dan scope backend tetap menjadi authority akses; UI bukan sumber izin.

## Fallback

Jika ditemukan privilege escalation atau kebocoran data, rollback dilakukan dengan menghapus slug terdampak dari group target di `RoleMenuVisibilityService`, mengembalikan whitelist anti-duplikasi frontend ke baseline terakhir yang lulus, dan rollback migration `group` pada tabel terdampak bila perlu.
