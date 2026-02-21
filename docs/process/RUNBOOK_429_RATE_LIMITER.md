# Runbook 429 Rate Limiter

Dokumen ini adalah prosedur cepat saat terjadi lonjakan `429 Too Many Requests`.

## 1. Indikator Awal
- Banyak request gagal dengan status `429` pada log aplikasi.
- Pengguna melaporkan aksi login/verifikasi email tertolak berulang.
- Lonjakan event `auth.lockout` atau `http.throttle` di log.

## 2. Cek Cepat (5-10 menit)
1. Periksa log terbaru:
```powershell
Get-Content storage/logs/laravel.log -Tail 200
```
2. Cari signature throttle:
```powershell
rg -n "auth.lockout|http.throttle|Too Many Requests|throttle" storage/logs/laravel.log -S
```
3. Identifikasi endpoint terdampak (login, verify-email, atau endpoint lain).

## 3. Tindakan Cepat
1. Pastikan bukan abuse/bot spike (cek IP dominan di log).
2. Jika trafik valid dan limit terlalu ketat, naikkan limit internal bertahap lewat `.env`:
- `AUTH_LOGIN_MAX_ATTEMPTS`
- `AUTH_LOGIN_DECAY_SECONDS`
- `AUTH_VERIFICATION_MAX_ATTEMPTS`
- `AUTH_VERIFICATION_DECAY_MINUTES`
3. Jalankan:
```powershell
php artisan optimize:clear
```
4. Verifikasi ulang flow auth utama.

## 4. Rollback Plan
Jika setelah peningkatan limit terjadi anomali keamanan/performa:
1. Kembalikan nilai `.env` ke baseline sebelumnya.
2. Jalankan `php artisan optimize:clear`.
3. Pantau log 10-15 menit untuk memastikan stabil.

## 5. Guardrail Penting
- Endpoint auth sensitif jangan dinaikkan agresif tanpa monitoring.
- Untuk layanan eksternal, kuota provider tidak bisa ditambah dari kode aplikasi.
- Prioritaskan mitigasi request volume: debounce frontend, cache response baca, batching query.
