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
    <div class="title">Laporan Program Prioritas {{ ucfirst($level) }}</div>

    <div class="meta">
        Wilayah: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 28px;">NO</th>
                <th rowspan="2" style="width: 80px;">PROGRAM</th>
                <th rowspan="2" style="width: 92px;">PRIORITAS PROGRAM</th>
                <th rowspan="2" style="width: 88px;">KEGIATAN</th>
                <th rowspan="2" style="width: 88px;">SASARAN TARGET</th>
                <th colspan="4" style="width: 120px;">JADWAL WAKTU</th>
                <th colspan="4" style="width: 120px;">SUMBER DANA</th>
                <th rowspan="2" style="width: 64px;">KET</th>
            </tr>
            <tr>
                <th style="width: 30px;">I</th>
                <th style="width: 30px;">II</th>
                <th style="width: 30px;">III</th>
                <th style="width: 30px;">IV</th>
                <th style="width: 30px;">Pusat</th>
                <th style="width: 30px;">APBD</th>
                <th style="width: 30px;">SWD</th>
                <th style="width: 30px;">Bant</th>
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
                    <td class="center">{{ $item->jadwal_i ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_ii ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_iii ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->jadwal_iv ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->sumber_dana_pusat ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->sumber_dana_apbd ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->sumber_dana_swd ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->sumber_dana_bant ? 'v' : '-' }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total data: {{ $items->count() }}.
    </div>
</body>
</html>
