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
            table-layout: fixed;
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
        .footer {
            margin-top: 12px;
            font-size: 10px;
            color: #374151;
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
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 28px;">NO</th>
                <th rowspan="2" style="width: 72px;">PROGRAM</th>
                <th rowspan="2" style="width: 80px;">PRIORITAS PROGRAM</th>
                <th rowspan="2" style="width: 76px;">KEGIATAN</th>
                <th rowspan="2" style="width: 70px;">SASARAN TARGET</th>
                <th colspan="12" style="width: 204px;">JADWAL WAKTU</th>
                <th colspan="4" style="width: 72px;">SUMBER DANA</th>
                <th rowspan="2" style="width: 48px;">KET</th>
            </tr>
            <tr>
                <th style="width: 17px;">1</th>
                <th style="width: 17px;">2</th>
                <th style="width: 17px;">3</th>
                <th style="width: 17px;">4</th>
                <th style="width: 17px;">5</th>
                <th style="width: 17px;">6</th>
                <th style="width: 17px;">7</th>
                <th style="width: 17px;">8</th>
                <th style="width: 17px;">9</th>
                <th style="width: 17px;">10</th>
                <th style="width: 17px;">11</th>
                <th style="width: 17px;">12</th>
                <th style="width: 18px;">Pus</th>
                <th style="width: 18px;">APB</th>
                <th style="width: 18px;">SWL</th>
                <th style="width: 18px;">Ban</th>
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

    <div class="footer">
        Total data: {{ $items->count() }}.
    </div>
</body>
</html>
