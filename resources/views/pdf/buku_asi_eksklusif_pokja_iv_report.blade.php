<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku ASI Eksklusif</title>
    <style>
        @page { margin: 14px 16px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 6px; color: #111827; margin: 0; }
        .lampiran { text-align: right; font-weight: 700; font-size: 8px; margin-bottom: 4px; }
        .title { text-align: center; font-size: 10px; font-weight: 700; margin-bottom: 8px; }
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table th, .main-table td {
            border: 1px solid #111827;
            padding: 2px 1px;
            vertical-align: middle;
            text-align: center;
        }
        .main-table th { font-weight: 700; background-color: #f3f4f6; }
    </style>
</head>
<body>
    <div class="lampiran">BUKU BANTU POKJA IV</div>
    <div class="title">BUKU ASI EKSLUSIF</div>

    <table class="main-table">
        <colgroup>
            <col style="width: 25px;">
            <col style="width: 80px;">
            @for ($i = 0; $i < 22; $i++)
                <col style="width: 20px;">
            @endfor
            <col style="width: 35px;">
            <col style="width: 30px;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="3">NO</th>
                <th rowspan="3">NAMA DESA</th>
                <th colspan="3">JUMLAH DARI UMUR (0-5 BLN)</th>
                <th colspan="3">JUMLAH DARI BAYI 6 BULAN</th>
                <th colspan="2">E0</th>
                <th colspan="2">E1</th>
                <th colspan="2">E2</th>
                <th colspan="2">E3</th>
                <th colspan="2">E4</th>
                <th colspan="2">E5</th>
                <th colspan="2">E6</th>
                <th colspan="2">JUMLAH E</th>
                <th rowspan="3">JUMLAH TOTAL</th>
                <th rowspan="3">%</th>
            </tr>
            <tr>
                <th rowspan="2">L</th>
                <th rowspan="2">P</th>
                <th rowspan="2">JML</th>
                <th rowspan="2">L</th>
                <th rowspan="2">P</th>
                <th rowspan="2">JML</th>
                <th colspan="2">JUMLAH</th>
                <th colspan="2">JUMLAH</th>
                <th colspan="2">JUMLAH</th>
                <th colspan="2">JUMLAH</th>
                <th colspan="2">JUMLAH</th>
                <th colspan="2">JUMLAH</th>
                <th colspan="2">JUMLAH</th>
                <th rowspan="2">L</th>
                <th rowspan="2">P</th>
            </tr>
            <tr>
                @for ($i = 0; $i < 8; $i++)
                    <th>L</th><th>P</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="26">Data belum tersedia. Laporan ini merupakan format ringkasan (Report-Only).</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
