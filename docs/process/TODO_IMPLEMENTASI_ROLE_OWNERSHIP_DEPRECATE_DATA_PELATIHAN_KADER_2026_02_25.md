# TODO ROD26A1 Implementasi Deprecate Ownership Data Pelatihan Kader

Tanggal: 2026-02-25  
Status: `done`

## Konteks

- Koreksi domain menandai `data-pelatihan-kader` sebagai `tidak usah`.
- Modul masih aktif di route dan matrix visibilitas saat ini.

## Target Hasil

- Tersedia keputusan eksplisit apakah modul dihapus dari ownership aktif, diset read-only, atau dipertahankan sementara.
- Jika dinonaktifkan, jalur akses dan dashboard coverage tidak menghasilkan drift kontrak.

## Langkah Eksekusi

- [x] Putuskan status modul `data-pelatihan-kader`: `deprecate` atau `retain`. (Keputusan: `retain` sementara).
- [x] Jika `deprecate`, turunkan/putus akses modul di `RoleMenuVisibilityService`. (N/A pada batch ini karena keputusan `retain`).
- [x] Jika `deprecate`, audit route `desa/kecamatan` dan tentukan strategy:
  - [x] hard remove route (N/A karena `retain`)
  - [x] keep route read-only sementara + banner deprecation (N/A karena `retain`)
- [x] Sinkronkan dokumen kontrak domain dan checklist audit role.
- [x] Tambah test sesuai keputusan (`forbidden` jika nonaktif, atau `read-only guard` jika transisi). (Dipenuhi lewat regression test `DataPelatihanKader*` + visibility payload).

## Validasi

- [x] `php artisan route:list --name=data-pelatihan-kader`
- [x] `php artisan test --filter=DataPelatihanKader|module.visibility` (dijalankan via filter `DataPelatihanKader`).
- [x] `php artisan test` penuh

Catatan validasi:
- `php artisan test` penuh lulus pada eksekusi 2026-02-25 (suite hijau end-to-end).

## Risiko

- Risiko perubahan tiba-tiba pada user flow operasional jika modul langsung dihapus.
- Risiko technical debt jika modul dibiarkan aktif tanpa status yang jelas.

## Keputusan

- [x] Concern ini dikunci dari temuan audit ownership 2026-02-25.
- [x] Keputusan final sesi ini: modul dipertahankan (`retain`) sampai ada keputusan domain terpisah untuk deprecate.
