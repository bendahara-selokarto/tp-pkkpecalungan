# Referensi Migration Manifest

Tanggal: 2026-03-11  
Status: `in-progress` (`state:pilot-rename-1`)

## Aturan

- `doc-key` lower-case, tanpa spasi, gunakan `-`.
- Mapping dapat berupa file spesifik atau glob (`**`) untuk folder.
- Status legend: `pilot-done` (sudah dipindah + referensi tersinkron pada scope pilot), `planned` (siap dipindah setelah batch berikutnya ditetapkan).

## Mapping

| Kategori | Old Path | New Path | Status | Catatan |
| --- | --- | --- | --- | --- |
| canonical | `docs/referensi/Rakernas X.pdf` | `docs/referensi/canonical/rakernas-x.pdf` | planned | sumber pedoman utama | 
| canonical | `docs/referensi/176.pdf` | `docs/referensi/canonical/176.pdf` | planned | numeric-only, doc-key tetap angka | 
| canonical | `docs/referensi/207.pdf` | `docs/referensi/canonical/207.pdf` | planned | numeric-only, doc-key tetap angka | 
| canonical | `docs/referensi/213.pdf` | `docs/referensi/canonical/213.pdf` | planned | numeric-only, doc-key tetap angka | 
| canonical | `docs/referensi/215.pdf` | `docs/referensi/canonical/215.pdf` | planned | numeric-only, doc-key tetap angka | 
| canonical | `docs/referensi/224-225.pdf` | `docs/referensi/canonical/224-225.pdf` | planned | numeric-only, doc-key tetap angka | 
| canonical | `docs/referensi/226.pdf` | `docs/referensi/canonical/226.pdf` | planned | numeric-only, doc-key tetap angka | 
| canonical | `docs/referensi/227.pdf` | `docs/referensi/canonical/227.pdf` | planned | numeric-only, doc-key tetap angka | 
| canonical | `docs/referensi/229-230.pdf` | `docs/referensi/canonical/229-230.pdf` | planned | numeric-only, doc-key tetap angka | 
| canonical | `docs/referensi/232.pdf` | `docs/referensi/canonical/232.pdf` | planned | numeric-only, doc-key tetap angka | 
| supporting | `docs/referensi/Cara Pengisian Lampiran 4.22.pdf` | `docs/referensi/supporting/lampiran-4-22-cara-pengisian.pdf` | pilot-done | referensi cara pengisian 4.22 | 
| supporting | `docs/referensi/cara pengisian 4.19a.pdf` | `docs/referensi/supporting/lampiran-4-19a-cara-pengisian.pdf` | planned | cara pengisian 4.19a | 
| supporting | `docs/referensi/Lampiran 4.22.xlsx` | `docs/referensi/supporting/lampiran-4-22.xlsx` | planned | workbook lampiran 4.22 | 
| supporting | `docs/referensi/Pemetaan Modul.xlsx` | `docs/referensi/supporting/pemetaan-modul.xlsx` | planned | workbook mapping modul | 
| supporting | `docs/referensi/ROUTE_LIST_2026_03_03.xlsx` | `docs/referensi/supporting/route-list-2026-03-03.xlsx` | planned | route list audit | 
| supporting | `docs/referensi/LAPORAN TAHUNAN PKK th 2025.docx` | `docs/referensi/supporting/laporan-tahunan-pkk-2025.docx` | planned | template laporan tahunan | 
| supporting | `docs/referensi/excel/**` | `docs/referensi/supporting/excel/**` | planned | folder workbook lama | 
| evidence | `docs/referensi/_screenshots/**` | `docs/referensi/evidence/screenshots/**` | planned | bukti visual | 
| evidence | `docs/referensi/Screenshot 2026-03-11 003306.png` | `docs/referensi/evidence/screenshots/2026-03-11-003306.png` | planned | screenshot lokal root | 
| evidence | `docs/referensi/Screenshot 2026-03-11 010802.png` | `docs/referensi/evidence/screenshots/2026-03-11-010802.png` | planned | screenshot lokal root | 
| local | `docs/referensi/_local/**` | `docs/referensi/_local/**` | planned | zona non-tracked tetap | 
