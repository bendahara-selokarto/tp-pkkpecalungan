<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Inventaris</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111827; }
        .lampiran { text-align: right; font-size: 14px; font-weight: 700; margin-bottom: 16px; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 3px; vertical-align: top; word-break: break-word; }
        th { text-align: center; font-size: 9px; font-weight: 700; }
        .number-row th { font-size: 8px; font-weight: 400; }
        .center { text-align: center; }
        .ttd { margin-top: 12px; text-align: right; font-size: 11px; font-weight: 700; }
        .meta-print { margin-top: 8px; font-size: 9px; color: #374151; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="lampiran">LAMPIRAN 4.12</div>
    <div class="title">BUKU INVENTARIS</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }} | Level: {{ $levelLabel }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">NO</th>
                <th style="width: 16%;">NAMA BARANG</th>
                <th style="width: 13%;">ASAL BARANG</th>
                <th style="width: 12%;">TANGGAL PENERIMAAN/PEMBELIAN</th>
                <th style="width: 10%;">JUMLAH</th>
                <th style="width: 15%;">TEMPAT PENYIMPANAN</th>
                <th style="width: 13%;">KONDISI BARANG</th>
                <th>KETERANGAN</th>
            </tr>
            <tr class="number-row">
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->asal_barang ?: '-' }}</td>
                    <td class="center">
                        {{ $item->tanggal_penerimaan ? \Carbon\Carbon::parse($item->tanggal_penerimaan)->format('d/m/Y') : \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                    </td>
                    <td class="center">{{ $item->quantity }} {{ $item->unit }}</td>
                    <td>{{ $item->tempat_penyimpanan ?: '-' }}</td>
                    <td>{{ strtoupper(str_replace('_', ' ', (string) $item->condition)) }}</td>
                    <td>{{ $item->keterangan ?: ($item->description ?: '-') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd">
        Tempat, ........ tanggal, ..... bulan, ........ tahun
    </div>

    <div class="meta-print">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
