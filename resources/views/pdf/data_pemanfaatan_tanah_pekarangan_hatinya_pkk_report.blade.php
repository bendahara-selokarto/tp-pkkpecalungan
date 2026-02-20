<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pemanfaatan Tanah Pekarangan/HATINYA PKK</title>
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

    <div class="title">DATA PEMANFAATAN TANAH PEKARANGAN/HATINYA PKK {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">NO</th>
                <th style="width: 220px;">KATEGORI JENIS PEMANFAATAN LAHAN</th>
                <th style="width: 220px;">KOMODITI DIBUDIDAYAKAN</th>
                <th>JUMLAH KOMODITI DIBUDIDAYAKAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->kategori_pemanfaatan_lahan }}</td>
                    <td>{{ $item->komoditi }}</td>
                    <td class="center">{{ $item->jumlah_komoditi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center">Data Pemanfaatan Tanah Pekarangan/HATINYA PKK belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>


