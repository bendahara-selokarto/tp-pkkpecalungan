# ADR 0012 Kecamatan Unowned Module Audit Group

Status: `accepted`

Tanggal: 2026-05-06

## Konteks

Owner kehilangan jejak modul yang sudah dibuat, role pemilik modul, dan cara menambah atau mengubah pemilik modul. Pada scope kecamatan ada modul yang route dan menu datanya sudah tersedia, tetapi tidak ada role operasional kecamatan yang memiliki mode `read-write`. Sekretaris kecamatan dapat melihat sebagian modul tersebut lewat group Pokja `read-only`, tetapi posisi modul belum mudah diaudit sebagai daftar tersendiri.

## Keputusan

Tambahkan group visibility backend `belum-ada-pemilik` khusus scope `kecamatan`.

Group ini:

- hanya diberikan ke `kecamatan-sekretaris` dengan mode `read-only`,
- berisi modul kecamatan yang belum memiliki owner `read-write` pada role operasional kecamatan,
- ditampilkan sebagai submenu sidebar `Belum Ada Pemilik`,
- tidak memberi hak tulis baru,
- tetap memakai `RoleMenuVisibilityService` sebagai source of truth mapping modul.

Untuk mengubah owner permanen modul, mapping canonical tetap diubah pada `RoleMenuVisibilityService` melalui concern terpisah. Halaman `Management Ijin Akses` hanya mengelola override modul rollout yang sudah diizinkan konfigurasi, bukan seluruh perubahan owner permanen.

Sinkronisasi 2026-05-06: daftar `belum-ada-pemilik` dikeluarkan dari modul yang sudah dikunci pada struktur aktual Sekretaris/Pokja. Buku yang sudah menjadi milik Pokja I-IV atau Sekretaris tidak boleh tetap muncul sebagai audit tanpa owner.

Untuk scope desa, group yang sama boleh dipakai sebagai guard baca legacy tanpa submenu sidebar. Mode ini tetap `read-only` dan tidak menjadi owner input.

## Dampak

- Sekretaris kecamatan memiliki daftar audit cepat untuk modul kecamatan yang belum punya pemilik input.
- Middleware `module.visibility` tetap menegakkan mode `read-only` untuk group ini.
- Modul yang sama tetap boleh berada pada group domain asalnya, tetapi sidebar kecamatan mengutamakan submenu audit agar daftar tidak tersebar.

## Fallback

Jika submenu ini membingungkan alur operasional, hapus group `belum-ada-pemilik` dari `GROUPS_BY_SCOPE`, `ROLE_GROUP_MODES`, dan sidebar kecamatan. Hak akses domain lain tidak berubah karena group ini hanya menambah visibility `read-only`.
