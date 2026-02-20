<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventaris</title>
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
    <div class="title">Laporan Inventaris {{ strtoupper($level) }}</div>
    <div class="meta">
        Wilayah: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;">NO</th>
                <th style="width: 100px;">NAMA BARANG</th>
                <th style="width: 96px;">ASAL BARANG</th>
                <th style="width: 120px;">TANGGAL PENERIMAAN/PEMBELIAN</th>
                <th style="width: 70px;">JUMLAH</th>
                <th style="width: 90px;">TEMPAT PENYIMPANAN</th>
                <th style="width: 90px;">KONDISI BARANG</th>
                <th>KETERANGAN</th>
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
</body>
</html>
