<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Agenda Surat</title>
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
    <div class="title">BUKU AGENDA SURAT {{ strtoupper($level) }}</div>
    <div class="meta">
        Wilayah: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 26px;">NO</th>
                <th style="width: 44px;">JENIS</th>
                <th style="width: 66px;">TGL TERIMA</th>
                <th style="width: 66px;">TGL SURAT</th>
                <th style="width: 78px;">NO SURAT</th>
                <th style="width: 70px;">ASAL</th>
                <th style="width: 70px;">DARI</th>
                <th style="width: 70px;">KEPADA</th>
                <th style="width: 90px;">PERIHAL</th>
                <th style="width: 70px;">LAMPIRAN</th>
                <th style="width: 78px;">DITERUSKAN</th>
                <th style="width: 78px;">TEMBUSAN</th>
                <th>KET</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ strtoupper($item->jenis_surat) }}</td>
                    <td class="center">{{ $item->tanggal_terima ? \Carbon\Carbon::parse($item->tanggal_terima)->format('d/m/Y') : '-' }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}</td>
                    <td>{{ $item->nomor_surat }}</td>
                    <td>{{ $item->asal_surat ?: '-' }}</td>
                    <td>{{ $item->dari ?: '-' }}</td>
                    <td>{{ $item->kepada ?: '-' }}</td>
                    <td>{{ $item->perihal }}</td>
                    <td>{{ $item->lampiran ?: '-' }}</td>
                    <td>{{ $item->diteruskan_kepada ?: '-' }}</td>
                    <td>{{ $item->tembusan ?: '-' }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
