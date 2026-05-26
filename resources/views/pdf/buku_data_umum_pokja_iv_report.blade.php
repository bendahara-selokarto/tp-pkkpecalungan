<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Data Umum Pokja IV</title>
    <style>
        @page { margin: 14px 16px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 6px; color: #111827; margin: 0; }
        .lampiran { text-align: right; font-weight: 700; font-size: 8px; margin-bottom: 4px; }
        .title { text-align: center; font-size: 10px; font-weight: 700; margin-bottom: 8px; }
        .main-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
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
    <div class="title">BUKU DATA UMUM POKJA IV</div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" width="20">NO</th>
                <th rowspan="2" width="60">DESA</th>
                <th colspan="4">KA</th>
                <th colspan="4">POSYANDU</th>
                <th colspan="5">GERAKAN PHBS</th>
                <th colspan="5">GERMAS</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr>
                <th>BUKU RESTI</th>
                <th>BUMIL KEK</th>
                <th>KI</th>
                <th>KA</th>
                <th>PERTAMA</th>
                <th>MADYA</th>
                <th>PURNAMA</th>
                <th>MANDIRI</th>
                <th>JLRT SEHAT PERTAMA</th>
                <th>JLRT SEHAT DIDATA PHB</th>
                <th>JLRT SEHAT MADYA</th>
                <th>JLRT SEHAT UTAMA</th>
                <th>JLRT SEHAT DARI PURNA</th>
                <th>AKTIFITAS FISIK</th>
                <th>CEK KESEHATAN</th>
                <th>KONSUMSI BUAH & SAYUR</th>
                <th>ORANG YANG TIDAK MEROKOK</th>
                <th>ASI EKSLUSIF</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 21; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="21">Data belum tersedia. Laporan ini merupakan format ringkasan (Report-Only).</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
