# Chart to PDF Examples

Folder ini berisi 5 contoh pendekatan cetak chart ke PDF untuk dibandingkan.

## 1) ApexCharts + jsPDF

File: `apexcharts-jspdf.html`

Kelebihan:
- Cocok dengan stack saat ini (ApexCharts sudah dipakai di dashboard).
- Hasil cepat, tanpa backend tambahan.

Cara pakai:
1. Buka file di browser.
2. Klik `Export PDF`.

## 2) html2canvas + jsPDF

File: `html2canvas-jspdf.html`

Kelebihan:
- Menangkap area dashboard persis seperti tampilan (WYSIWYG).
- Bisa cetak chart + ringkasan + elemen UI sekaligus.

Cara pakai:
1. Buka file di browser.
2. Klik `Capture to PDF`.

## 3) Puppeteer (server-side render)

File: `puppeteer-export.mjs`

Kelebihan:
- Stabil untuk otomatisasi backend/CI.
- Tidak bergantung browser user.

Prerequisite:
- Install: `npm i -D puppeteer`

Cara pakai:
1. Jalankan:
   `node public/chart-pdf-examples/puppeteer-export.mjs`
2. Output default:
   `public/chart-pdf-examples/output/puppeteer-dashboard.pdf`

## 4) Highcharts Offline Export

File: `highcharts-offline-export.html`

Kelebihan:
- Fitur export built-in.
- Tidak perlu implement export manual.

Cara pakai:
1. Buka file di browser.
2. Klik `Export PDF (Offline)`.

## 5) QuickChart API

File: `quickchart-export.ps1`

Kelebihan:
- Mudah dipakai dari backend/script.
- Tidak perlu render chart di browser lokal.

Cara pakai (PowerShell):
1. Jalankan:
   `powershell -ExecutionPolicy Bypass -File public/chart-pdf-examples/quickchart-export.ps1`
2. Output default:
   `public/chart-pdf-examples/output/quickchart-example.pdf`

## Catatan evaluasi

Fokus pembandingan:
- Ketajaman hasil PDF
- Konsistensi layout
- Kecepatan
- Kompleksitas integrasi ke project Laravel + Inertia + Vue
