# TODO UVM25R1 UI Menu Visibility Alignment 2026-02-25

Tanggal: 2026-02-25  
Status: `in-progress` (`state:experimental-ui-only`, `state:non-final`, `state:rolling`)

## Force Latest Marker

- Todo Code: `UVM25R1`
- Marker: `VIS-UI-EXP-2026-02-25-R2`
- Jika ada analisa yang memakai versi TODO ini sebelum marker ini ditambahkan, analisa tersebut dianggap usang.
- Wajib gunakan isi terbaru dokumen ini sebagai acuan kerja concern visibility UI eksperimen.

## Update Konfirmasi Tabel 2026-02-25 (R2)

- Konfirmasi ini diperlakukan sebagai baseline penempatan modul pada UI eksperimen (bukan kontrak akses backend final).
- Konfirmasi yang dikunci:
  - `Daftar Anggota Pokja` dan `Buku Prestasi/Lomba`: tetap mengikuti interpretasi R1 (dinyatakan benar).
  - `Data Warga` dan `Data Kegiatan Warga`: untuk track eksperimen UI ditempatkan sebagai concern `sekretaris` (`sekretaris-only` pada layer UI).
  - `Kelompok Simulasi dan Penyuluhan`: untuk track eksperimen UI diposisikan ke `Pokja I Desa`.
  - Baris modul `27-31`: diperlakukan `tidak digunakan` pada eksperimen penempatan saat ini.
- Batas eksekusi:
  - hanya penataan render/grouping/label pada UI,
  - tidak mengubah `RoleMenuVisibilityService`, middleware `module.visibility`, policy, route, repository, atau test E2E.

## Single Source Concern Sidebar

- Dokumen ini adalah satu-satunya acuan aktif untuk concern penataan menu/sidebar eksperimen UI.
- Dokumen terkait berikut diperlakukan sebagai historis (bukan acuan eksekusi aktif concern sidebar):
  - `docs/process/TODO_UI_VISIBILITY_BY_PENANGGUNGJAWAB.md` (implementasi E2E historis).
  - `docs/process/TODO_REFACTOR_DASHBOARD_LINTAS_ROLE_2026_02_24.md` (refactor dashboard historis).
- Jika ada mismatch narasi antar dokumen, keputusan concern sidebar mengikuti dokumen ini.

## Konteks

- Navigasi sidebar perlu ditata ulang agar visibilitas menu per role terasa konsisten dan mudah dipahami user.
- Concern ini dibatasi pada UI (`resources/js`) dan tidak mengubah kontrak otorisasi backend.
- Fokus utama: kejelasan grouping, urutan, label, dan konsistensi state tampil/sembunyi menu.
- Track ini bersifat eksperimental UI, belum menjadi keputusan final, dan bisa sering berubah.

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

- [x] Audit kondisi render sidebar saat ini per role utama (`desa`, `kecamatan`, `super-admin`).
- [x] Petakan item menu yang berpotensi duplikat atau ambigu label.
- [x] Refactor komposisi grouping + sorting item pada layer UI.
- [x] Normalisasi copywriting label menu agar natural user dan konsisten.
- [x] Tambahkan guard UI agar group kosong tidak tampil membingungkan.
- [x] Sinkronkan dokumentasi terkait jika ada perubahan istilah canonical di UI.

## Validasi (UI Only)

- [x] Smoke check desktop (`lg/xl`): group menu tampil sesuai role (audit komposisi render UI).
- [x] Smoke check tablet/mobile: collapse/expand tetap konsisten (audit state/layout sidebar).
- [x] Verifikasi tidak ada menu duplikat pada role gabungan.
- [x] Verifikasi label menu konsisten antar halaman concern yang sama.
- [x] `npm run build`.

## Risiko

- Risiko drift label dengan dokumen terminology jika copywriting tidak disinkronkan.
- Risiko regressi UX pada sidebar collapse/expand setelah refactor struktur.
- Risiko false sense of security jika dianggap mengubah authority akses (padahal UI-only).

## Mitigasi

- Kunci perubahan hanya di UI layer dan pertahankan backend authority tanpa perubahan.
- Lakukan smoke test manual per breakpoint sebelum penutupan concern.
- Jika ada istilah berubah, sinkronkan dokumen process/domain pada sesi yang sama.

## Keputusan

- [x] Concern ini bersifat UI-only eksperimental (tanpa E2E/backend change).
- [x] Keputusan pada TODO ini bersifat sementara dan dapat direvisi cepat selama fase eksperimen.
- [x] Otorisasi akses tetap backend-first; UI hanya representasi visibility.

## Kriteria Exit Eksperimental

- Ubah status concern ini ke `done` jika:
  - struktur sidebar final lintas role sudah stabil pada 2 siklus review berurutan,
  - tidak ada perubahan label/grouping mayor pada concern sidebar selama 2 siklus review,
  - hasil smoke UI desktop + mobile konsisten tanpa isu regressi kritikal.
- Ubah status concern ini ke `historical` jika:
  - concern digantikan SOT baru untuk sidebar UI, atau
  - eksperimen dihentikan dan keputusan final dipindahkan ke concern lain.

## Cadence Review (Mulai 2026-03-02)

- Frekuensi: mingguan, setiap Senin.
- Scope review minimal:
  - audit perubahan grouping/label sidebar pada `resources/js`,
  - verifikasi guard `UI-only` tetap tidak menyentuh policy/middleware/backend visibility,
  - sinkronkan marker terbaru jika ada revisi eksperimen.
- Milestone review aktif:
  - [ ] Review R3: 2026-03-09.
  - [ ] Review R4: 2026-03-16.
