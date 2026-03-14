<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kegiatan PKK Pokja I</title>
    <style>
        @page { margin: 14px 16px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7px; color: #111827; margin: 0; }
        .header { min-height: 98px; margin-bottom: 8px; }
        .lampiran { text-align: right; font-weight: 700; font-size: 10px; margin-bottom: 14px; }
        .title { text-align: center; font-size: 12px; font-weight: 700; margin-bottom: 8px; }
        .meta { width: 170px; border-collapse: collapse; margin: 0 auto; }
        .meta td { padding: 0 2px; }
        .meta .label { width: 52px; font-weight: 700; }
        .meta .sep { width: 8px; text-align: center; font-weight: 700; }
        .pokja { font-size: 10px; font-weight: 700; margin: 6px 0 6px; }
        .main-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .main-table th, .main-table td {
            border: 1px solid #111827;
            padding: 2px 1px;
            text-align: center;
            vertical-align: middle;
            word-break: break-word;
        }
        .main-table th { font-weight: 700; line-height: 1.2; }
        .left { text-align: left; }
        .number-row th { font-size: 6px; }
        .total-row td { font-weight: 700; }
    </style>
</head>
<body>
    @php($totals = is_array($totals ?? null) ? $totals : [])
    <div class="header">
        <div class="lampiran">LAMPIRAN 4.21</div>
        <div class="title">DATA KEGIATAN PKK</div>

        <table class="meta">
            <tr>
                <td class="label">TP PKK</td>
                <td class="sep">:</td>
                <td>{{ $areaName ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">TAHUN</td>
                <td class="sep">:</td>
                <td>{{ $tahun ?? now()->format('Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="pokja">POKJA I</div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="3">NO</th>
                <th rowspan="3">NAMA WILAYAH<br>(DUSUN/DESA/KEL./KEC./KAB./KOTA/PROV)</th>
                <th rowspan="3">JML KADER</th>
                <th colspan="24">PENGHAYATAN DAN PENGAMALAN PANCASILA DAN GOTONG ROYONG</th>
            </tr>
            <tr>
                <th colspan="4">KISAH</th>
                <th colspan="4">KRISAN</th>
                <th colspan="4">KILAS</th>
                <th colspan="4">KTIAT</th>
                <th colspan="4">KISAK</th>
                <th colspan="4">PKBN</th>
            </tr>
            <tr>
                <th>KEGIATAN</th>
                <th>VOL. KEG</th>
                <th>METODE</th>
                <th>JML. SASARAN</th>
                <th>KEGIATAN</th>
                <th>VOL. KEG</th>
                <th>METODE</th>
                <th>JML. SASARAN</th>
                <th>KEGIATAN</th>
                <th>VOL. KEG</th>
                <th>METODE</th>
                <th>JML. SASARAN</th>
                <th>KEGIATAN</th>
                <th>VOL. KEG</th>
                <th>METODE</th>
                <th>JML. SASARAN</th>
                <th>KEGIATAN</th>
                <th>VOL. KEG</th>
                <th>METODE</th>
                <th>JML. SASARAN</th>
                <th>KEGIATAN</th>
                <th>VOL. KEG</th>
                <th>METODE</th>
                <th>JML. SASARAN</th>
            </tr>
            <tr class="number-row">
                @for ($column = 1; $column <= 27; $column++)
                    <th>{{ $column }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item['nomor_urut'] ?? '-' }}</td>
                    <td class="left">{{ $item['nama_wilayah'] ?? '-' }}</td>
                    <td>{{ $item['jumlah_kader'] ?? 0 }}</td>
                    <td>{{ $item['kisah_kegiatan'] ?? 0 }}</td>
                    <td>{{ $item['kisah_volume'] ?? 0 }}</td>
                    <td>{{ $item['kisah_metode'] ?? 0 }}</td>
                    <td>{{ $item['kisah_sasaran'] ?? 0 }}</td>
                    <td>{{ $item['krisan_kegiatan'] ?? 0 }}</td>
                    <td>{{ $item['krisan_volume'] ?? 0 }}</td>
                    <td>{{ $item['krisan_metode'] ?? 0 }}</td>
                    <td>{{ $item['krisan_sasaran'] ?? 0 }}</td>
                    <td>{{ $item['kilas_kegiatan'] ?? 0 }}</td>
                    <td>{{ $item['kilas_volume'] ?? 0 }}</td>
                    <td>{{ $item['kilas_metode'] ?? 0 }}</td>
                    <td>{{ $item['kilas_sasaran'] ?? 0 }}</td>
                    <td>{{ $item['ktiat_kegiatan'] ?? 0 }}</td>
                    <td>{{ $item['ktiat_volume'] ?? 0 }}</td>
                    <td>{{ $item['ktiat_metode'] ?? 0 }}</td>
                    <td>{{ $item['ktiat_sasaran'] ?? 0 }}</td>
                    <td>{{ $item['kisak_kegiatan'] ?? 0 }}</td>
                    <td>{{ $item['kisak_volume'] ?? 0 }}</td>
                    <td>{{ $item['kisak_metode'] ?? 0 }}</td>
                    <td>{{ $item['kisak_sasaran'] ?? 0 }}</td>
                    <td>{{ $item['pkbn_kegiatan'] ?? 0 }}</td>
                    <td>{{ $item['pkbn_volume'] ?? 0 }}</td>
                    <td>{{ $item['pkbn_metode'] ?? 0 }}</td>
                    <td>{{ $item['pkbn_sasaran'] ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td>1</td>
                    <td class="left">-</td>
                    @for ($column = 3; $column <= 27; $column++)
                        <td>0</td>
                    @endfor
                </tr>
            @endforelse
            <tr class="total-row">
                <td></td>
                <td class="left">JUMLAH</td>
                <td>{{ $totals['jumlah_kader'] ?? 0 }}</td>
                <td>{{ $totals['kisah_kegiatan'] ?? 0 }}</td>
                <td>{{ $totals['kisah_volume'] ?? 0 }}</td>
                <td>{{ $totals['kisah_metode'] ?? 0 }}</td>
                <td>{{ $totals['kisah_sasaran'] ?? 0 }}</td>
                <td>{{ $totals['krisan_kegiatan'] ?? 0 }}</td>
                <td>{{ $totals['krisan_volume'] ?? 0 }}</td>
                <td>{{ $totals['krisan_metode'] ?? 0 }}</td>
                <td>{{ $totals['krisan_sasaran'] ?? 0 }}</td>
                <td>{{ $totals['kilas_kegiatan'] ?? 0 }}</td>
                <td>{{ $totals['kilas_volume'] ?? 0 }}</td>
                <td>{{ $totals['kilas_metode'] ?? 0 }}</td>
                <td>{{ $totals['kilas_sasaran'] ?? 0 }}</td>
                <td>{{ $totals['ktiat_kegiatan'] ?? 0 }}</td>
                <td>{{ $totals['ktiat_volume'] ?? 0 }}</td>
                <td>{{ $totals['ktiat_metode'] ?? 0 }}</td>
                <td>{{ $totals['ktiat_sasaran'] ?? 0 }}</td>
                <td>{{ $totals['kisak_kegiatan'] ?? 0 }}</td>
                <td>{{ $totals['kisak_volume'] ?? 0 }}</td>
                <td>{{ $totals['kisak_metode'] ?? 0 }}</td>
                <td>{{ $totals['kisak_sasaran'] ?? 0 }}</td>
                <td>{{ $totals['pkbn_kegiatan'] ?? 0 }}</td>
                <td>{{ $totals['pkbn_volume'] ?? 0 }}</td>
                <td>{{ $totals['pkbn_metode'] ?? 0 }}</td>
                <td>{{ $totals['pkbn_sasaran'] ?? 0 }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
