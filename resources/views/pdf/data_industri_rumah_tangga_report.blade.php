<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Industri Rumah Tangga</title>
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

    <div class="title">BUKU INDUSTRI RUMAH TANGGA {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">NO</th>
                <th style="width: 220px;">KATEGORI JENIS INDUSTRI RUMAH TANGGA</th>
                <th style="width: 220px;">KOMODITI YANG DIUSAHAKAN</th>
                <th>JUMLAH KOMODITI YANG DIKELOLA</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->kategori_jenis_industri }}</td>
                    <td>{{ $item->komoditi }}</td>
                    <td class="center">{{ $item->jumlah_komoditi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center">Buku Industri Rumah Tangga belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>






