# Auth Scope Route-Policy Map (2026-03-15)

Tujuan: peta cepat `route -> policy -> scope service -> matrix` untuk audit auth/scope.

Sumber matrix:

- `app/Support/RoleScopeMatrix.php`
- `app/Domains/Wilayah/Enums/ScopeLevel.php`

Ringkasan cakupan:

- Total route scoped (desa+kecamatan, non-print): 76
- Route dengan policy terpetakan: 76
- Route tanpa policy match: 0
- Policy tanpa route match: 1

Catatan:

- Map ini berbasis konvensi nama (`slug` route <-> `Policy` class).
- Pastikan controller tetap memanggil policy (manual authorize atau `authorizeResource`).
- Route print/report disaring; audit print tetap ada di jalur PDF map.
- Dokumen ini dihasilkan oleh `scripts/generate_auth_scope_route_map.py`.

Override mapping:

- `desa-activities` -> `ActivityPolicy`
- `desa-arsip` -> `ArsipDocumentPolicy`

## Route -> Policy -> Scope Service -> Matrix

| Scope | Route slug | Controller | Policy | Scope service | Matrix |
| --- | --- | --- | --- | --- | --- |
| `desa` | `activities` | `DesaActivityController` | `ActivityPolicy` | `ActivityScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `activities` | `KecamatanActivityController` | `ActivityPolicy` | `ActivityScopeService` | `RoleScopeMatrix` |
| `desa` | `agenda-surat` | `DesaAgendaSuratController` | `AgendaSuratPolicy` | `AgendaSuratScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `agenda-surat` | `KecamatanAgendaSuratController` | `AgendaSuratPolicy` | `AgendaSuratScopeService` | `RoleScopeMatrix` |
| `desa` | `anggota-pokja` | `DesaAnggotaPokjaController` | `AnggotaPokjaPolicy` | `AnggotaPokjaScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `anggota-pokja` | `KecamatanAnggotaPokjaController` | `AnggotaPokjaPolicy` | `AnggotaPokjaScopeService` | `RoleScopeMatrix` |
| `desa` | `anggota-tim-penggerak` | `DesaAnggotaTimPenggerakController` | `AnggotaTimPenggerakPolicy` | `AnggotaTimPenggerakScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `anggota-tim-penggerak` | `KecamatanAnggotaTimPenggerakController` | `AnggotaTimPenggerakPolicy` | `AnggotaTimPenggerakScopeService` | `RoleScopeMatrix` |
| `desa` | `bantuans` | `DesaBantuanController` | `BantuanPolicy` | `BantuanScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `bantuans` | `KecamatanBantuanController` | `BantuanPolicy` | `BantuanScopeService` | `RoleScopeMatrix` |
| `desa` | `bkb-kegiatan` | `DesaBkbKegiatanController` | `BkbKegiatanPolicy` | `BkbKegiatanScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `bkb-kegiatan` | `KecamatanBkbKegiatanController` | `BkbKegiatanPolicy` | `BkbKegiatanScopeService` | `RoleScopeMatrix` |
| `desa` | `bkl` | `DesaBklController` | `BklPolicy` | `BklScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `bkl` | `KecamatanBklController` | `BklPolicy` | `BklScopeService` | `RoleScopeMatrix` |
| `desa` | `bkr` | `DesaBkrController` | `BkrPolicy` | `BkrScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `bkr` | `KecamatanBkrController` | `BkrPolicy` | `BkrScopeService` | `RoleScopeMatrix` |
| `desa` | `buku-daftar-hadir` | `DesaBukuDaftarHadirController` | `BukuDaftarHadirPolicy` | `BukuDaftarHadirScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `buku-daftar-hadir` | `KecamatanBukuDaftarHadirController` | `BukuDaftarHadirPolicy` | `BukuDaftarHadirScopeService` | `RoleScopeMatrix` |
| `desa` | `buku-keuangan` | `DesaBukuKeuanganController` | `BukuKeuanganPolicy` | `BukuKeuanganScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `buku-keuangan` | `KecamatanBukuKeuanganController` | `BukuKeuanganPolicy` | `BukuKeuanganScopeService` | `RoleScopeMatrix` |
| `desa` | `buku-notulen-rapat` | `DesaBukuNotulenRapatController` | `BukuNotulenRapatPolicy` | `BukuNotulenRapatScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `buku-notulen-rapat` | `KecamatanBukuNotulenRapatController` | `BukuNotulenRapatPolicy` | `BukuNotulenRapatScopeService` | `RoleScopeMatrix` |
| `desa` | `buku-tamu` | `DesaBukuTamuController` | `BukuTamuPolicy` | `BukuTamuScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `buku-tamu` | `KecamatanBukuTamuController` | `BukuTamuPolicy` | `BukuTamuScopeService` | `RoleScopeMatrix` |
| `desa` | `catatan-keluarga` | `DesaCatatanKeluargaController` | `CatatanKeluargaPolicy` | `CatatanKeluargaScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `catatan-keluarga` | `KecamatanCatatanKeluargaController` | `CatatanKeluargaPolicy` | `CatatanKeluargaScopeService` | `RoleScopeMatrix` |
| `desa` | `data-industri-rumah-tangga` | `DesaDataIndustriRumahTanggaController` | `DataIndustriRumahTanggaPolicy` | `DataIndustriRumahTanggaScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `data-industri-rumah-tangga` | `KecamatanDataIndustriRumahTanggaController` | `DataIndustriRumahTanggaPolicy` | `DataIndustriRumahTanggaScopeService` | `RoleScopeMatrix` |
| `desa` | `data-kegiatan-warga` | `DesaDataKegiatanWargaController` | `DataKegiatanWargaPolicy` | `DataKegiatanWargaScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `data-kegiatan-warga` | `KecamatanDataKegiatanWargaController` | `DataKegiatanWargaPolicy` | `DataKegiatanWargaScopeService` | `RoleScopeMatrix` |
| `desa` | `data-keluarga` | `DesaDataKeluargaController` | `DataKeluargaPolicy` | `DataKeluargaScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `data-keluarga` | `KecamatanDataKeluargaController` | `DataKeluargaPolicy` | `DataKeluargaScopeService` | `RoleScopeMatrix` |
| `desa` | `data-pelatihan-kader` | `DesaDataPelatihanKaderController` | `DataPelatihanKaderPolicy` | `DataPelatihanKaderScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `data-pelatihan-kader` | `KecamatanDataPelatihanKaderController` | `DataPelatihanKaderPolicy` | `DataPelatihanKaderScopeService` | `RoleScopeMatrix` |
| `desa` | `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | `DesaDataPemanfaatanTanahPekaranganHatinyaPkkController` | `DataPemanfaatanTanahPekaranganHatinyaPkkPolicy` | `DataPemanfaatanTanahPekaranganHatinyaPkkScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `data-pemanfaatan-tanah-pekarangan-hatinya-pkk` | `KecamatanDataPemanfaatanTanahPekaranganHatinyaPkkController` | `DataPemanfaatanTanahPekaranganHatinyaPkkPolicy` | `DataPemanfaatanTanahPekaranganHatinyaPkkScopeService` | `RoleScopeMatrix` |
| `desa` | `data-warga` | `DesaDataWargaController` | `DataWargaPolicy` | `DataWargaScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `data-warga` | `KecamatanDataWargaController` | `DataWargaPolicy` | `DataWargaScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `desa-activities` | `KecamatanDesaActivityController` | `ActivityPolicy` | `ActivityScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `desa-arsip` | `KecamatanDesaArsipController` | `ArsipDocumentPolicy` | `-` | `RoleScopeMatrix` |
| `desa` | `inventaris` | `DesaInventarisController` | `InventarisPolicy` | `InventarisScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `inventaris` | `KecamatanInventarisController` | `InventarisPolicy` | `InventarisScopeService` | `RoleScopeMatrix` |
| `desa` | `kader-khusus` | `DesaKaderKhususController` | `KaderKhususPolicy` | `KaderKhususScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `kader-khusus` | `KecamatanKaderKhususController` | `KaderKhususPolicy` | `KaderKhususScopeService` | `RoleScopeMatrix` |
| `desa` | `kejar-paket` | `DesaKejarPaketController` | `KejarPaketPolicy` | `KejarPaketScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `kejar-paket` | `KecamatanKejarPaketController` | `KejarPaketPolicy` | `KejarPaketScopeService` | `RoleScopeMatrix` |
| `desa` | `koperasi` | `DesaKoperasiController` | `KoperasiPolicy` | `KoperasiScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `koperasi` | `KecamatanKoperasiController` | `KoperasiPolicy` | `KoperasiScopeService` | `RoleScopeMatrix` |
| `desa` | `laporan-tahunan-pkk` | `DesaLaporanTahunanPkkController` | `LaporanTahunanPkkPolicy` | `LaporanTahunanPkkScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `laporan-tahunan-pkk` | `KecamatanLaporanTahunanPkkController` | `LaporanTahunanPkkPolicy` | `LaporanTahunanPkkScopeService` | `RoleScopeMatrix` |
| `desa` | `literasi-warga` | `DesaLiterasiWargaController` | `LiterasiWargaPolicy` | `LiterasiWargaScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `literasi-warga` | `KecamatanLiterasiWargaController` | `LiterasiWargaPolicy` | `LiterasiWargaScopeService` | `RoleScopeMatrix` |
| `desa` | `paar` | `DesaPaarController` | `PaarPolicy` | `PaarScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `paar` | `KecamatanPaarController` | `PaarPolicy` | `PaarScopeService` | `RoleScopeMatrix` |
| `desa` | `pelatihan-kader-pokja-ii` | `DesaPelatihanKaderPokjaIiController` | `PelatihanKaderPokjaIiPolicy` | `PelatihanKaderPokjaIiScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `pelatihan-kader-pokja-ii` | `KecamatanPelatihanKaderPokjaIiController` | `PelatihanKaderPokjaIiPolicy` | `PelatihanKaderPokjaIiScopeService` | `RoleScopeMatrix` |
| `desa` | `pilot-project-keluarga-sehat` | `DesaPilotProjectKeluargaSehatController` | `PilotProjectKeluargaSehatPolicy` | `PilotProjectKeluargaSehatScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `pilot-project-keluarga-sehat` | `KecamatanPilotProjectKeluargaSehatController` | `PilotProjectKeluargaSehatPolicy` | `PilotProjectKeluargaSehatScopeService` | `RoleScopeMatrix` |
| `desa` | `pilot-project-naskah-pelaporan` | `DesaPilotProjectNaskahPelaporanController` | `PilotProjectNaskahPelaporanPolicy` | `PilotProjectNaskahPelaporanScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `pilot-project-naskah-pelaporan` | `KecamatanPilotProjectNaskahPelaporanController` | `PilotProjectNaskahPelaporanPolicy` | `PilotProjectNaskahPelaporanScopeService` | `RoleScopeMatrix` |
| `desa` | `posyandu` | `DesaPosyanduController` | `PosyanduPolicy` | `PosyanduScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `posyandu` | `KecamatanPosyanduController` | `PosyanduPolicy` | `PosyanduScopeService` | `RoleScopeMatrix` |
| `desa` | `pra-koperasi-up2k` | `DesaPraKoperasiUp2kController` | `PraKoperasiUp2kPolicy` | `PraKoperasiUp2kScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `pra-koperasi-up2k` | `KecamatanPraKoperasiUp2kController` | `PraKoperasiUp2kPolicy` | `PraKoperasiUp2kScopeService` | `RoleScopeMatrix` |
| `desa` | `prestasi-lomba` | `DesaPrestasiLombaController` | `PrestasiLombaPolicy` | `PrestasiLombaScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `prestasi-lomba` | `KecamatanPrestasiLombaController` | `PrestasiLombaPolicy` | `PrestasiLombaScopeService` | `RoleScopeMatrix` |
| `desa` | `program-prioritas` | `DesaProgramPrioritasController` | `ProgramPrioritasPolicy` | `ProgramPrioritasScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `program-prioritas` | `KecamatanProgramPrioritasController` | `ProgramPrioritasPolicy` | `ProgramPrioritasScopeService` | `RoleScopeMatrix` |
| `desa` | `simulasi-penyuluhan` | `DesaSimulasiPenyuluhanController` | `SimulasiPenyuluhanPolicy` | `SimulasiPenyuluhanScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `simulasi-penyuluhan` | `KecamatanSimulasiPenyuluhanController` | `SimulasiPenyuluhanPolicy` | `SimulasiPenyuluhanScopeService` | `RoleScopeMatrix` |
| `desa` | `taman-bacaan` | `DesaTamanBacaanController` | `TamanBacaanPolicy` | `TamanBacaanScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `taman-bacaan` | `KecamatanTamanBacaanController` | `TamanBacaanPolicy` | `TamanBacaanScopeService` | `RoleScopeMatrix` |
| `desa` | `tutor-khusus` | `DesaTutorKhususController` | `TutorKhususPolicy` | `TutorKhususScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `tutor-khusus` | `KecamatanTutorKhususController` | `TutorKhususPolicy` | `TutorKhususScopeService` | `RoleScopeMatrix` |
| `desa` | `warung-pkk` | `DesaWarungPkkController` | `WarungPkkPolicy` | `WarungPkkScopeService` | `RoleScopeMatrix` |
| `kecamatan` | `warung-pkk` | `KecamatanWarungPkkController` | `WarungPkkPolicy` | `WarungPkkScopeService` | `RoleScopeMatrix` |

## Route Tanpa Policy Match

- Tidak ada.

## Policy Tanpa Route Match

| Policy | Scope service |
| --- | --- |
| `UserPolicy` | `-` |
