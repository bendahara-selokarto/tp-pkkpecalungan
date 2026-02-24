# TODO UI Runtime Guardrail (2026-02-24)

## Konteks
- Beberapa interaksi utama UI (sidebar, mode siang/malam, dropdown) bergantung pada JavaScript runtime.
- Error JavaScript global (runtime error / unhandled promise rejection) dapat menyebabkan behavior UI tidak terduga.
- Diperlukan concern khusus untuk memastikan fallback UI tetap aman saat terjadi error JavaScript.

## Target Hasil
- Ada guardrail runtime global untuk menangkap error JavaScript dan promise rejection.
- UI menampilkan fallback non-blocking agar user tahu ada gangguan dan punya aksi pemulihan cepat.
- Interaksi kritikal tidak gagal total saat storage browser (`localStorage`) tidak tersedia.

## Langkah Eksekusi
- [x] Tambahkan global runtime guard di bootstrap frontend (`window.error`, `window.unhandledrejection`, `app.config.errorHandler`).
- [x] Tambahkan event internal `ui-runtime-error` untuk menghubungkan error runtime ke layer UI.
- [x] Tambahkan fallback banner di layout utama saat runtime error terdeteksi.
- [x] Tambahkan aksi pemulihan cepat (`Muat Ulang`) pada fallback banner.
- [x] Tambahkan fallback aman akses `localStorage` pada preferensi sidebar collapse.
- [x] Tambahkan observability lanjutan: kirim error JS ke backend logging endpoint terproteksi.

## Validasi
- [x] Build frontend berhasil (`npm run build`).
- [x] Tidak ada regresi test backend yang terkait visibilitas menu (`php artisan test` targeted).
- [x] Layout tetap berfungsi normal pada flow utama (dashboard/profile/menu role-based).

## Risiko
- Jika terjadi error berulang dari third-party script, banner fallback bisa sering muncul.
- Guardrail hanya memitigasi dampak UI; root cause error tetap perlu diperbaiki pada modul sumber.

## Keputusan
- [x] Guardrail runtime UI ditetapkan sebagai concern aktif.
- [x] Event `ui-runtime-error` dipakai sebagai kontrak internal lintas bootstrap -> layout.
- [x] Fallback banner dipilih sebagai mitigasi default karena minim risiko behavior drift.
