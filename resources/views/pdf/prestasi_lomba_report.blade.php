<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Prestasi Lomba</title>
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
    <div class="title">Laporan Prestasi Lomba {{ strtoupper($level) }}</div>
    <div class="meta">
        Wilayah: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 28px;">NO</th>
                <th rowspan="2" style="width: 64px;">TAHUN</th>
                <th rowspan="2" style="width: 110px;">JENIS LOMBA</th>
                <th rowspan="2" style="width: 90px;">LOKASI</th>
                <th colspan="4" style="width: 180px;">PRESTASI/KEBERHASILAN YANG DICAPAI</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr>
                <th style="width: 45px;">KECAMATAN</th>
                <th style="width: 45px;">KABUPATEN</th>
                <th style="width: 45px;">PROVINSI</th>
                <th style="width: 45px;">NASIONAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $item->tahun }}</td>
                    <td>{{ $item->jenis_lomba }}</td>
                    <td>{{ $item->lokasi }}</td>
                    <td class="center">{{ $item->prestasi_kecamatan ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->prestasi_kabupaten ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->prestasi_provinsi ? 'v' : '-' }}</td>
                    <td class="center">{{ $item->prestasi_nasional ? 'v' : '-' }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
