<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku HATINYA PKK</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .lampiran { text-align: right; font-size: 14px; font-weight: 700; margin-bottom: 16px; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-break: break-word; }
        th { text-align: center; font-size: 10px; font-weight: 700; }
        .number-row th { font-size: 9px; font-weight: 400; }
        .center { text-align: center; }
        .note { margin-top: 8px; font-style: italic; }
        .meta-print { margin-top: 8px; font-size: 9px; color: #374151; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="lampiran">LAMPIRAN 4.14.2b</div>
    <div class="title">BUKU HATINYA PKK</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }} | Level: {{ $levelLabel }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">NO</th>
                <th style="width: 220px;">KATEGORI</th>
                <th style="width: 220px;">KOMODITI</th>
                <th>JUMLAH</th>
            </tr>
            <tr class="number-row">
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
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
                    <td colspan="4" class="center">Buku HATINYA PKK belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="note">
        Kategori : (peternakan, perikanan, warung hidup, toga, tanaman keras, lainnya)
    </div>

    <div class="meta-print">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>



