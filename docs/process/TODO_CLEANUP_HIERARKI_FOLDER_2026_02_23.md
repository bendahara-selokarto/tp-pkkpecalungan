# TODO Cleanup Hirarki Folder (2026-02-23)

## Konteks
- Root project masih memuat artefak utilitas dan report yang tidak berada pada hirarki folder terstruktur.
- Tujuan cleanup: membuat struktur lebih jelas tanpa mengubah perilaku aplikasi.

## Target Hasil
- Utilitas analisis report test berada di folder tooling terdedikasi.
- Artefak report test memiliki folder terdedikasi (`reports/`) dan tidak mengotori root.
- Root project hanya berisi entrypoint/konfigurasi utama yang relevan.

## Langkah Eksekusi
- [x] `C1` Pindahkan utilitas `extract-failure.php` ke `tools/testing/parse-junit-report.php`.
- [x] `C2` Hapus artefak root yang tidak diperlukan lagi (`extract-failure.php`, `report.xml`).
- [x] `C3` Rapikan `.gitignore` agar mengikuti hirarki baru (`reports/`).
- [x] `C4` Validasi sintaks script tooling dan perubahan git tree.

## Validasi
- [x] `php -l tools/testing/parse-junit-report.php` lulus.
- [x] `git status --short` menunjukkan perubahan hanya pada concern cleanup hirarki.

## Risiko
- [x] Risiko rendah: tool manual lama di root tidak ditemukan jika masih dipanggil via path lama.
- [x] Mitigasi: nama fungsi utilitas dipertahankan di lokasi baru dan path baru eksplisit.

## Keputusan
- [x] Struktur utilitas non-runtime dipusatkan di `tools/`.
- [x] Struktur output report dipusatkan di `reports/`.

