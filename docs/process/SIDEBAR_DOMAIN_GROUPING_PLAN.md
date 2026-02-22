# Sidebar Domain Grouping Plan

Tujuan:
- Menetapkan organisasi menu sidebar berbasis domain agar navigasi konsisten dengan struktur organisasi TP PKK.
- Menjadi baseline untuk pengaturan `menu -> sub menu -> sub sub menu` pada scope `desa` dan `kecamatan`.
- Menjadi referensi audit teks sidebar agar label mengikuti terminology canonical domain.

Sumber acuan:
- `AGENTS.md`
- `PEDOMAN_DOMAIN_UTAMA_101_150.md`
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
| Sekretaris TPK | Buku Keuangan | `/{scope}/bantuans` |
| Sekretaris TPK | Buku Inventaris | `/{scope}/inventaris` |
| Sekretaris TPK | Buku Kegiatan | `/{scope}/activities` |
| Sekretaris TPK | Anggota Pokja | `/{scope}/anggota-pokja` |
| Sekretaris TPK | Prestasi Lomba | `/{scope}/prestasi-lomba` |
| Pokja I | Daftar Warga TP PKK | `/{scope}/data-warga` |
| Pokja I | Data Kegiatan Warga | `/{scope}/data-kegiatan-warga` |
| Pokja I | BKL | `/{scope}/bkl` |
| Pokja I | BKR | `/{scope}/bkr` |
| Pokja II | Data Pelatihan Kader | `/{scope}/data-pelatihan-kader` |
| Pokja II | Data Isian Taman Bacaan/Perpustakaan | `/{scope}/taman-bacaan` |
| Pokja II | Data Isian Koperasi | `/{scope}/koperasi` |
| Pokja II | Data Isian Kejar Paket/KF/PAUD | `/{scope}/kejar-paket` |
| Pokja III | Data Keluarga | `/{scope}/data-keluarga` |
| Pokja III | Data Industri Rumah Tangga | `/{scope}/data-industri-rumah-tangga` |
| Pokja III | Data Pemanfaatan Tanah Pekarangan/HATINYA PKK | `/{scope}/data-pemanfaatan-tanah-pekarangan-hatinya-pkk` |
| Pokja III | Data Aset (Sarana) Desa/Kelurahan | `/{scope}/warung-pkk` |
| Pokja IV | Data Isian Posyandu oleh TP PKK | `/{scope}/posyandu` |
| Pokja IV | Data Isian Kelompok Simulasi dan Penyuluhan | `/{scope}/simulasi-penyuluhan` |
| Pokja IV | Catatan Keluarga | `/{scope}/catatan-keluarga` |
| Pokja IV | Program Prioritas | `/{scope}/program-prioritas` |
| Pokja IV | Naskah Pelaporan Pilot Project | `/{scope}/pilot-project-naskah-pelaporan` |
| Pokja IV | Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana | `/{scope}/pilot-project-keluarga-sehat` |
| Referensi | Pedoman Domain Utama 101-150 | eksternal |
| Referensi | Pedoman Lanjutan 201-241 | eksternal |
| Monitoring Kecamatan | Kegiatan Desa | `/kecamatan/desa-activities` |

### L1: Account

| L2 | L3 | Scope |
| --- | --- | --- |
| Akun | Profil | semua role |
| Akun | Keluar (Log Out) | semua role |

## Catatan Audit Teks Sidebar (2026-02-22)

Status audit:
- Fokus audit hanya teks pada `resources/js/Layouts/DashboardLayout.vue`.
- Tujuan audit: memastikan label `L2/L3` relevan dengan domain dan sesuai acuan canonical.

Temuan yang perlu dijaga:
- Label sidebar harus sinkron dengan terminology map saat ada perubahan domain baru.
- Penamaan item Pokja IV pilot project harus jelas membedakan:
  - `Naskah Pelaporan Pilot Project` (modul naskah)
  - `Laporan Pelaksanaan Pilot Project Gerakan Keluarga Sehat Tanggap dan Tangguh Bencana` (modul indikator laporan)
- Label administratif non-domain tetap konsisten bahasa:
  - `Manajemen User`
  - `Profil`
  - `Keluar (Log Out)`

Checklist audit sidebar berikutnya:
- [ ] Verifikasi `L1/L2/L3` terhadap `TERMINOLOGY_NORMALIZATION_MAP`.
- [ ] Verifikasi slug route di `DashboardLayout.vue` tetap match dengan matrix domain.
- [ ] Verifikasi penempatan domain baru selalu masuk group organisasi yang benar.
- [ ] Jika ada label baru/non-canonical, catat deviasi di `DOMAIN_DEVIATION_LOG`.

## Implementasi Teknis

1. Definisi struktur menu tetap di `resources/js/Layouts/DashboardLayout.vue`.
2. Path domain tetap di-generate berbasis scope (`desa`/`kecamatan`).
3. External reference wajib `target="_blank"` dan `rel="noopener noreferrer"`.
4. Saat sidebar collapsed, klik group tetap membuka item utama group.

## Status

- [x] Struktur organisasi sidebar `L1/L2/L3` terdokumentasi.
- [x] Mapping domain sidebar ke slug route terdokumentasi.
- [x] Catatan audit teks sidebar dan checklist audit lanjutan ditambahkan.
