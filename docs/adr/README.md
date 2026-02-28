# ADR Directory Guide

Folder ini menyimpan `Architecture Decision Record` (ADR), yaitu catatan keputusan arsitektur yang berdampak jangka panjang.

## Kapan Wajib Buat ADR
- Perubahan boundary arsitektur utama (`Controller -> UseCase/Action -> Repository -> Model`).
- Perubahan enforcement akses backend (`Policy`, `Scope Service`, middleware akses).
- Perubahan kontrak canonical yang berisiko menimbulkan drift lintas modul.
- Ada beberapa opsi teknis valid dan keputusan perlu jejak trade-off yang bisa diaudit.

## Konvensi Penamaan
- Nama file: `ADR_<NOMOR4>_<RINGKASAN>.md`.
- Format judul: `# ADR <NOMOR4> <Judul Ringkas>`.
- Contoh: `ADR_0001_REPOSITORY_BOUNDARY_FOR_DASHBOARD.md`.

## Status ADR
- `proposed`: masih dibahas.
- `accepted`: sudah dipilih dan jadi acuan.
- `superseded`: sudah digantikan ADR baru.
- `deprecated`: tidak lagi direkomendasikan.

## Alur Ringkas
1. Buat/aktifkan TODO concern di `docs/process/` sebagai rencana eksekusi.
2. Isi ADR dari template `docs/adr/ADR_TEMPLATE.md`.
3. Tautkan ADR ke TODO concern, file implementasi, dan test validasi.
4. Jika keputusan berubah, buat ADR baru lalu tandai ADR lama sebagai `superseded`.
