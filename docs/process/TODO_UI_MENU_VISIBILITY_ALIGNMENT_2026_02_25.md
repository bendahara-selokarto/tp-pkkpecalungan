# TODO UI Menu Visibility Alignment 2026-02-25

Tanggal: 2026-02-25  
Status: `planned`

## Konteks

- Navigasi sidebar perlu ditata ulang agar visibilitas menu per role terasa konsisten dan mudah dipahami user.
- Concern ini dibatasi pada UI (`resources/js`) dan tidak mengubah kontrak otorisasi backend.
- Fokus utama: kejelasan grouping, urutan, label, dan konsistensi state tampil/sembunyi menu.

## Target Hasil

- Struktur menu lebih rapi dan konsisten lintas role pada tampilan UI.
- Tidak ada duplikasi item menu pada role gabungan.
- Label menu natural user dan konsisten dengan terminology canonical.
- Perubahan tidak menyentuh policy, middleware, repository, atau test E2E backend.

## Scope

- In scope:
  - Refactor logic render menu di layout/dashboard sidebar.
  - Penataan urutan group dan item.
  - Copywriting label menu pada layer UI.
  - Hardening empty-state/placeholder bila group tidak punya item visible.
- Out of scope:
  - Perubahan policy/scope service/backend visibility payload.
  - Penambahan/ubah feature test E2E.
  - Perubahan kontrak domain matrix backend.

## Langkah Eksekusi

- [ ] Audit kondisi render sidebar saat ini per role utama (`desa`, `kecamatan`, `super-admin`).
- [ ] Petakan item menu yang berpotensi duplikat atau ambigu label.
- [ ] Refactor komposisi grouping + sorting item pada layer UI.
- [ ] Normalisasi copywriting label menu agar natural user dan konsisten.
- [ ] Tambahkan guard UI agar group kosong tidak tampil membingungkan.
- [ ] Sinkronkan dokumentasi terkait jika ada perubahan istilah canonical di UI.

## Validasi (UI Only)

- [ ] Smoke test manual desktop (`lg/xl`): group menu tampil sesuai role.
- [ ] Smoke test manual tablet/mobile: collapse/expand tetap konsisten.
- [ ] Verifikasi tidak ada menu duplikat pada role gabungan.
- [ ] Verifikasi label menu konsisten antar halaman concern yang sama.
- [ ] `npm run build`.

## Risiko

- Risiko drift label dengan dokumen terminology jika copywriting tidak disinkronkan.
- Risiko regressi UX pada sidebar collapse/expand setelah refactor struktur.
- Risiko false sense of security jika dianggap mengubah authority akses (padahal UI-only).

## Mitigasi

- Kunci perubahan hanya di UI layer dan pertahankan backend authority tanpa perubahan.
- Lakukan smoke test manual per breakpoint sebelum penutupan concern.
- Jika ada istilah berubah, sinkronkan dokumen process/domain pada sesi yang sama.

## Keputusan

- [x] Concern ini dikunci sebagai UI-only (tanpa E2E/backend change).
- [x] Otorisasi akses tetap backend-first; UI hanya representasi visibility.
