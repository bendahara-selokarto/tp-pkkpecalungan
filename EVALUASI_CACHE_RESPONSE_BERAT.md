# EVALUASI_CACHE_RESPONSE_BERAT.md

Tujuan: mengevaluasi titik response berat agar konsumsi request/API dan beban backend lebih efisien.

## 1. Kandidat Endpoint Berat
- Endpoint list domain scoped (desa/kecamatan) dengan tabel data panjang.
- Endpoint cetak report PDF (`/report/pdf`) yang melakukan query agregat/list sebelum render PDF.

## 2. Temuan Saat Ini
- Domain report baru (`bkl`, `bkr`, `simulasi-penyuluhan`) sudah stabil secara fungsional.
- Belum ada lapisan cache khusus untuk response list/report lintas domain.
- Dengan jumlah data besar, endpoint report berpotensi jadi hotspot.

## 3. Rekomendasi Implementasi Bertahap
1. Tahap 1 (low risk): cache read-only untuk data report PDF dengan TTL pendek (30-120 detik) per `level + area_id + domain`.
2. Tahap 2: invalidasi selektif saat create/update/delete pada domain terkait.
3. Tahap 3: tambah cache untuk list index yang sering diakses berulang pada halaman dashboard/modul.

## 4. Kunci Cache yang Disarankan
- Format: `report:{domain}:{level}:{area_id}`
- Contoh:
  - `report:bkl:desa:123`
  - `report:bkr:kecamatan:45`

## 5. Risiko dan Mitigasi
- Risiko stale data: gunakan TTL pendek + invalidasi on write.
- Risiko cache stampede: gunakan lock ringan atau jitter TTL bila diperlukan.
- Risiko kompleksitas debugging: log cache hit/miss pada fase awal rollout.

## 6. Keputusan Saat Ini
- Evaluasi selesai.
- Implementasi cache ditunda ke fase terpisah agar perubahan terkontrol dan mudah rollback.
