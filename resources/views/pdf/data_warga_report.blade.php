<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Warga</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 10px; }
        .center { text-align: center; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="title">DATA WARGA {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">NO</th>
                <th style="width: 110px;">DASAWISMA</th>
                <th style="width: 140px;">NAMA KEPALA KELUARGA</th>
                <th style="width: 170px;">ALAMAT</th>
                <th style="width: 80px;">WARGA L</th>
                <th style="width: 80px;">WARGA P</th>
                <th style="width: 80px;">TOTAL</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->dasawisma }}</td>
                    <td>{{ $item->nama_kepala_keluarga }}</td>
                    <td>{{ $item->alamat }}</td>
                    <td class="center">{{ $item->jumlah_warga_laki_laki }}</td>
                    <td class="center">{{ $item->jumlah_warga_perempuan }}</td>
                    <td class="center">{{ $item->total_warga }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Data warga belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
