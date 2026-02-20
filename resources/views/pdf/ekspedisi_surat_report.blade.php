<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Ekspedisi Surat</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { margin-bottom: 8px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111827; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background: #f3f4f6; text-align: center; font-size: 9px; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <div class="title">BUKU EKSPEDISI SURAT {{ strtoupper($level) }}</div>
    <div class="meta">
        Wilayah: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 26px;">NO</th>
                <th style="width: 72px;">TGL SURAT</th>
                <th style="width: 98px;">NO SURAT</th>
                <th style="width: 140px;">KEPADA</th>
                <th style="width: 185px;">PERIHAL</th>
                <th style="width: 88px;">LAMPIRAN</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}</td>
                    <td>{{ $item->nomor_surat }}</td>
                    <td>{{ $item->kepada ?: '-' }}</td>
                    <td>{{ $item->perihal }}</td>
                    <td>{{ $item->lampiran ?: '-' }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Data surat keluar belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
