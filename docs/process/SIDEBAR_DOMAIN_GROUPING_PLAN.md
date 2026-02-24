# Sidebar Domain Grouping Plan

Tujuan:
- Menetapkan organisasi menu sidebar berbasis domain agar navigasi konsisten dengan struktur organisasi TP PKK.
- Menjadi baseline untuk pengaturan `menu -> sub menu -> sub sub menu` pada scope `desa` dan `kecamatan`.
- Menjadi referensi audit teks sidebar agar label mengikuti terminology canonical domain.

Sumber acuan:
- `AGENTS.md`
- `PEDOMAN_DOMAIN_UTAMA_RAKERNAS_X.md`
- `docs/domain/DOMAIN_CONTRACT_MATRIX.md`
- `docs/domain/TERMINOLOGY_NORMALIZATION_MAP.md`

## Prinsip Organisasi Sidebar

1. Level menu:
- `L1` = kategori utama sidebar (`Main`, `Menu Domain`, `Account`).
- `L2` = group organisasi domain (`Sekretaris TPK`, `Pokja I-IV`, `Referensi`, `Monitoring Kecamatan`).
- `L3` = item domain yang mengarah ke modul (route prefix berbasis scope).
2. Semua item domain pada `L3` wajib punya hubungan langsung ke slug domain pada matrix kontrak.
3. Label di `L2/L3` wajib mengacu terminology canonical (bukan istilah ad-hoc).
4. Scope route tetap memakai pola `/{scope}/{slug}` dengan `scope = desa|kecamatan`.

## Struktur Hierarki Sidebar (L1 -> L2 -> L3)

### L1: Main

| L2 | L3 | Scope |
| --- | --- | --- |
| Dashboard | Dashboard | semua role non super-admin |
| Manajemen User | Manajemen User | super-admin |

### L1: Menu Domain

| L2 | L3 (Domain) | Slug/Route Prefix |
| --- | --- | --- |
| Sekretaris TPK | Buku Daftar Anggota Tim Penggerak PKK | `/{scope}/anggota-tim-penggerak` |
| Sekretaris TPK | Buku Daftar Kader Tim Penggerak PKK | `/{scope}/kader-khusus` |
| Sekretaris TPK | Buku Agenda Surat Masuk/Keluar | `/{scope}/agenda-surat` |
| Sekretaris TPK | Buku Keuangan | `/{scope}/buku-keuangan` |
| Sekretaris TPK | Buku Inventaris | `/{scope}/inventaris` |
| Sekretaris TPK | Buku Kegiatan | `/{scope}/activities` |
| Sekretaris TPK | Buku Anggota Pokja | `/{scope}/anggota-pokja` |
| Sekretaris TPK | Prestasi Lomba | `/{scope}/prestasi-lomba` |
| Pokja I | Buku Kegiatan | `/{scope}/activities` |
| Pokja I | Daftar Warga TP PKK | `/{scope}/data-warga` |
| Pokja I | Data Kegiatan Warga | `/{scope}/data-kegiatan-warga` |
| Pokja I | BKL | `/{scope}/bkl` |
| Pokja I | BKR | `/{scope}/bkr` |
| Pokja II | Buku Kegiatan | `/{scope}/activities` |
| Pokja II | Data Pelatihan Kader | `/{scope}/data-pelatihan-kader` |
| Pokja II | Data Isian Taman Bacaan/Perpustakaan | `/{scope}/taman-bacaan` |
| Pokja II | Data Isian Koperasi | `/{scope}/koperasi` |
| Pokja II | Data Isian Kejar Paket/KF/PAUD | `/{scope}/kejar-paket` |
| Pokja III | Buku Kegiatan | `/{scope}/activities` |
| Pokja III | Data Keluarga | `/{scope}/data-keluarga` |
| Pokja III | Data Industri Rumah Tangga | `/{scope}/data-industri-rumah-tangga` |
| Pokja III | Data Pemanfaatan Tanah Pekarangan/HATINYA PKK | `/{scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk` |
| Pokja III | Data Aset (Sarana) Desa/Kelurahan | `/{scope}/warung-pkk` |
| Pokja IV | Buku Kegiatan | `/{scope}/activities` |
| Pokja IV | Data Isian Posyandu oleh TP PKK | `/{scope}/posyandu` |
| Pokja IV | Kelompok Simulasi dan Penyuluhan | `/{scope}/simulasi-penyuluhan` |
| Pokja IV | Catatan Keluarga | `/{scope}/catatan-keluarga` |
| Pokja IV | Program Prioritas | `/{scope}/program-prioritas` |
| Pokja IV | Naskah Pelaporan Pilot Project | `/{scope}/pilot-project-naskah-pelaporan` |
| Pokja IV | Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana | `/{scope}/pilot-project-keluarga-sehat` |
| Referensi | Pedoman Domain Rakernas X | eksternal/lokal |
| Monitoring Kecamatan | Kegiatan Desa | `/kecamatan/desa-activities` |

### L1: Account

| L2 | L3 | Scope |
| --- | --- | --- |
| Akun | Profil | semua role |
| Akun | Keluar | semua role |

## Catatan Audit Teks Sidebar (2026-02-22)

Status audit:
- Fokus audit hanya teks pada `resources/js/Layouts/DashboardLayout.vue`.
- Tujuan audit: memastikan label `L2/L3` relevan dengan domain dan sesuai acuan canonical.

Temuan yang perlu dijaga:
- Label sidebar harus sinkron dengan terminology map saat ada perubahan domain baru.
- `Buku Kegiatan` adalah modul lintas role dan dapat muncul pada lebih dari satu group organisasi; render UI harus mencegah duplikasi item pada role gabungan.
- Penamaan item Pokja IV pilot project harus jelas membedakan:
  - `Naskah Pelaporan Pilot Project` (modul naskah)
  - `Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana` (modul indikator laporan)
- Label administratif non-domain tetap konsisten bahasa:
  - `Manajemen User`
  - `Profil`
  - `Keluar`

Checklist audit sidebar berikutnya:
- [ ] Verifikasi `L1/L2/L3` terhadap `TERMINOLOGY_NORMALIZATION_MAP`.
- [ ] Verifikasi slug route di `DashboardLayout.vue` tetap match dengan matrix domain.
- [ ] Verifikasi penempatan domain baru selalu masuk group organisasi yang benar.
- [ ] Jika ada label baru/non-canonical, catat deviasi di `DOMAIN_DEVIATION_LOG`.

## Catatan Audit Metode Collapse Sidebar (2026-02-22)

Scope audit:
- Membandingkan metode collapse di `resources/js/Layouts/DashboardLayout.vue` dengan template asli `resources/js/admin-one/layouts/LayoutAuthenticated.vue`.
- Fokus hanya pada mekanisme collapse, breakpoint behavior, dan persistence state.

Ringkasan temuan:
- Implementasi saat ini belum 1:1 dengan metode template asli.
- Template asli menyembunyikan aside pada desktop `xl` saat collapsed (`xl:hidden`) dan konten kembali full-width.
- Implementasi saat ini mempertahankan rail sempit (`lg:w-20`) saat collapsed.
- Key persistence berbeda:
  - Template asli: `admin-one-sidebar-collapsed`
  - Implementasi saat ini: `sidebar-collapsed`
- Kontrol collapse saat ini ditampilkan di header desktop dan tombol floating tepi sidebar.

Keputusan audit saat ini:
- `catat-only` (tidak dieksekusi refactor metode collapse pada sesi ini).
- Tidak ada perubahan kontrak domain/menu; concern murni pada UX dan keselarasan pattern template.

Checklist tindak lanjut (opsional, jika nanti disetujui refactor):
- [ ] Samakan state pattern menjadi `isAsideMobileExpanded`, `isAsideLgActive`, `isAsideDesktopCollapsed`.
- [ ] Samakan behavior desktop collapse agar mengikuti pola `AsideMenu` (`lg:hidden xl:flex` + `xl:hidden`).
- [ ] Samakan key localStorage dengan template (`admin-one-sidebar-collapsed`) atau dokumentasikan alasan deviasi.
- [ ] Validasi manual UX di desktop (lg/xl), tablet, dan mobile setelah refactor.

## Implementasi Teknis

1. Definisi struktur menu tetap di `resources/js/Layouts/DashboardLayout.vue`.
2. Path domain tetap di-generate berbasis scope (`desa`/`kecamatan`).
3. External reference wajib `target="_blank"` dan `rel="noopener noreferrer"`.
4. Saat sidebar collapsed, klik group tetap membuka item utama group.

## Status

- [x] Struktur organisasi sidebar `L1/L2/L3` terdokumentasi.
- [x] Mapping domain sidebar ke slug route terdokumentasi.
- [x] Catatan audit teks sidebar dan checklist audit lanjutan ditambahkan.
