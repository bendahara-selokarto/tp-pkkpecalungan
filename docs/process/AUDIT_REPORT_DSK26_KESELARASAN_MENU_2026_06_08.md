# Audit Report DSK26 Keselarasan Menu & Permission (Desa vs Kecamatan)

Tanggal: 2026-06-08  
Status: `done`  
Analisis ID: `DSK26-AUDIT-001`

## 1. Ringkasan Keselarasan Peran (Mirroring)

Berdasarkan audit terhadap `RoleMenuVisibilityService.php` dan `RoleScopeMatrix.php`, struktur menu dan permission antara level kecamatan dan desa secara umum sudah mengikuti pola mirror yang konsisten dengan beberapa pengecualian yang bersifat fungsional (scope-based).

- **Sekretaris (95% Mirror):** Sangat selaras. Satu-satunya delta besar adalah grup `monitoring` yang eksklusif untuk Kecamatan.
- **Bendahara (100% Mirror):** Paling stabil dan simetris antara level Desa dan Kecamatan.
- **Pokja I-IV (90% Mirror):** Selaras pada modul wajib. Delta berupa overlay `sekretaris-bantu` dan `pkk-data-dasar` pada level Desa adalah intentional untuk mendukung input data dasar di tingkat warga.

## 2. Matriks Drift & Gap (Backlog Action)

Daftar ketidaksesuaian yang ditemukan dan perlu ditindaklanjuti:

| Modul / Permission | Jabatan | Klasifikasi | Temuan & Risiko |
| --- | --- | --- | --- |
| `bkb-kegiatan`, `literasi-warga`, `tutor-khusus` | `pokja-ii` (Kec & Desa) | `drift` | Modul feeder Lampiran 4.22 ada di Permission Matrix tapi **MISSING** dari VisibilityService. Menu tidak muncul di sidebar. |
| `anggota-pokja` | `pokja-iii` (Kec & Desa) | `drift` | Menu visible Read-Write (RW) di Sidebar, tapi permission backend hanya `view`. Risiko **403 Forbidden** saat mencoba simpan/edit. |
| `buku-kliping` | `pokja-iii` (Kecamatan) | `drift` | Menu visible RW di Sidebar, tapi permission backend hanya `view`. Risiko **403 Forbidden**. |
| `agenda-surat-tugas` | `pokja-iii` (Desa) | `drift` | Permission backend (`.*`) terlalu longgar dibanding Pokja lain yang biasanya hanya `view` atau CRUD terbatas. |
| `data-kegiatan-pkk-pokja-i..iv` | `pokja-i..iv` (Desa) | `intentional` | Muncul di `pokja-x-wajib` untuk Desa sebagai feeder data. |

## 3. Identifikasi Risiko 403 (Prioritas Tinggi)

Berikut adalah titik-titik kritis di mana user bisa melihat menu/tombol tetapi akan ditolak oleh backend (Policy):

1. **Pokja III (Kecamatan & Desa) - Anggota Pokja:**
   - Visibility: `MODE_READ_WRITE` via grup `pokja-iii`.
   - Backend: Hanya `anggota_pokja.view`.
   - Action: Tambahkan `create`, `update`, `delete` di `RoleScopeMatrix`.

2. **Pokja III (Kecamatan) - Buku Kliping:**
   - Visibility: `MODE_READ_WRITE` via grup `pokja-iii`.
   - Backend: Hanya `buku_kliping.view`.
   - Action: Tambahkan `create`, `update`, `delete` di `RoleScopeMatrix`.

## 4. Daftar Pengecualian Intentional (Tetap Dipertahankan)

Daftar ini adalah delta yang memang sengaja berbeda dan **TIDAK BOLEH** disamakan:

- **`monitoring` Group:** Eksklusif untuk `kecamatan-sekretaris`. Desa tidak memiliki akses monitoring lintas wilayah.
- **`sekretaris-bantu` Overlay:** Muncul di semua Pokja Desa sebagai mode `READ_ONLY` untuk membantu verifikasi data penunjang. Di Kecamatan, Pokja tidak melihat menu ini.
- **`pkk-data-dasar` Overlay:** Muncul di semua peran Desa (Sekretaris, Bendahara, Pokja) untuk akses data warga/keluarga.

## 5. Rencana Validasi (Test Matrix)

Test yang perlu diaktifkan kembali atau dibuat baru untuk mengunci hasil audit ini:

- `Tests\Unit\Services\RoleMenuVisibilityServiceTest`: Pastikan `bkb-kegiatan` dkk muncul untuk Pokja II.
- `Tests\Feature\AccessControl\RolePermissionMirrorTest` (Proposed): Test baru untuk memastikan mirroring antara Kec/Desa tetap terjaga secara otomatis di masa depan.
- `Tests\Feature\AccessControl\Policy403PreventionTest`: Verifikasi manual/otomatis pada endpoint Pokja III untuk mencegah regresi 403.
