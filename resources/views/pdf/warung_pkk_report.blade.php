<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Warung PKK</title>
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

    <div class="title">Laporan Warung PKK {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">NO</th>
                <th style="width: 170px;">NAMA WARUNG PKK</th>
                <th style="width: 150px;">NAMA PENGELOLA</th>
                <th style="width: 170px;">KOMODITI</th>
                <th style="width: 130px;">KATEGORI</th>
                <th>VOLUME</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_warung_pkk }}</td>
                    <td>{{ $item->nama_pengelola }}</td>
                    <td>{{ $item->komoditi }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td>{{ $item->volume }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
