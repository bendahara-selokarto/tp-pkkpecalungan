# Todo List - Implementasi Isolasi Ketat AnggotaPokja

## [DONE] 1. Penguatan Model `AnggotaPokja`
- [x] Menambahkan `ROLE_TO_POKJA_MAP` untuk pemetaan role ke group pokja.
- [x] Implementasi logic `booted()` untuk pengisian kolom `pokja` secara otomatis saat create data (anti-bypass).

## [DONE] 2. Penguatan Service `AnggotaPokjaScopeService`
- [x] Menambahkan logic resolusi group pokja berdasarkan role user.
- [x] Menambahkan validasi `authorizePokjaGroup` untuk mencegah akses IDOR.

## [DONE] 3. Penguatan Repository `AnggotaPokjaRepository`
- [x] Menambahkan filter `applyPokjaFilter` pada query `paginate` dan `get`.
- [x] Update interface `AnggotaPokjaRepositoryInterface` untuk mendukung parameter `$actor`.

## [DONE] 4. Penguatan UseCase
- [x] Update `ListScopedAnggotaPokjaUseCase` untuk meneruskan actor ke repository (mengamankan List UI & Export PDF).
- [x] Update `GetScopedAnggotaPokjaUseCase` untuk memvalidasi hak akses pokja (mengamankan Detail/Edit/Delete).

## [DONE] 5. Verifikasi Keamanan Export
- [x] Memastikan `AnggotaPokjaReportPrintController` menggunakan UseCase yang sudah terproteksi.
- [x] Verifikasi bahwa user Pokja I tidak bisa mengekspor data Pokja II melalui manipulasi URL atau ID.
