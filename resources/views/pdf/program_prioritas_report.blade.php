<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Program Prioritas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }
        .title {
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
        }
        .meta {
            margin-bottom: 8px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #111827;
            padding: 4px;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background: #f3f4f6;
            text-align: center;
            font-size: 10px;
        }
        .center {
            text-align: center;
        }
        
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="title">BUKU PROGRAM KERJA {{ $levelLabel }}</div>

    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Tahun anggaran: {{ $budgetYearLabel ?? '-' }}
    </div>

    <table>
        <colgroup>
            <col style="width: 25px;">
            <col style="width: 120px;">
            <col style="width: 180px;">
            <col style="width: 150px;">
            <col style="width: 120px;">
            <col style="width: 18px;">
            <col style="width: 18px;">
            <col style="width: 18px;">
            <col style="width: 18px;">
            <col style="width: 18px;">
            <col style="width: 18px;">
            <col style="width: 18px;">
            <col style="width: 18px;">
            <col style="width: 18px;">
            <col style="width: 18px;">
            <col style="width: 18px;">
            <col style="width: 18px;">
            <col style="width: 22px;">
            <col style="width: 22px;">
            <col style="width: 22px;">
            <col style="width: 22px;">
            <col style="width: 100px;">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">PROGRAM</th>
                <th rowspan="2">PRIORITAS PROGRAM</th>
                <th rowspan="2">KEGIATAN</th>
                <th rowspan="2">SASARAN TARGET</th>
                <th colspan="12">JADWAL WAKTU</th>
                <th colspan="4">SUMBER DANA</th>
                <th rowspan="2">KET</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
                <th>11</th>
                <th>12</th>
                <th>Pus</th>
                <th>APB</th>
                <th>SWL</th>
                <th>Ban</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->program }}</td>
                    <td>{{ $item->prioritas_program }}</td>
                    <td>{{ $item->kegiatan }}</td>
                    <td>{{ $item->sasaran_target }}</td>
                    <td class="center">{{ $item->jadwal_bulan_1 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_bulan_2 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_bulan_3 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_bulan_4 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_bulan_5 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_bulan_6 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_bulan_7 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_bulan_8 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_bulan_9 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_bulan_10 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_bulan_11 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_bulan_12 ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->sumber_dana_pusat ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->sumber_dana_apbd ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->sumber_dana_swd ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->sumber_dana_bant ? 'v' : '-' }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="22" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials._report_footer')

    <div class="footer-meta">
        Total data: {{ $items->count() }}. | 
        
    </div>
    @include('pdf.partials._report_metadata')
</body>
</html>
