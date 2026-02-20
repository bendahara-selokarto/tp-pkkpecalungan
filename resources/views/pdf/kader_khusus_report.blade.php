<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kader Khusus</title>
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
    <div class="title">Laporan Kader Khusus {{ strtoupper($level) }}</div>
    <div class="meta">
        Wilayah: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 24px;">NO</th>
                <th rowspan="2" style="width: 90px;">NAMA</th>
                <th colspan="2" style="width: 60px;">JENIS KELAMIN</th>
                <th rowspan="2" style="width: 110px;">TEMPAT TANGGAL LAHIR</th>
                <th colspan="2" style="width: 90px;">STATUS</th>
                <th rowspan="2" style="width: 85px;">ALAMAT</th>
                <th rowspan="2" style="width: 70px;">PENDIDIKAN</th>
                <th rowspan="2" style="width: 95px;">JENIS KADER KHUSUS</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr>
                <th style="width: 30px;">L</th>
                <th style="width: 30px;">P</th>
                <th style="width: 45px;">NIKAH</th>
                <th style="width: 45px;">BLM NIKAH</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                @php
                    $isL = $item->jenis_kelamin === 'L';
                    $isKawin = $item->status_perkawinan === 'kawin';
                    $umur = \Carbon\Carbon::parse($item->tanggal_lahir)->age;
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="center">{{ $isL ? 'v' : '-' }}</td>
                    <td class="center">{{ $isL ? '-' : 'v' }}</td>
                    <td>{{ $item->tempat_lahir }}, {{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') }} ({{ $umur }})</td>
                    <td class="center">{{ $isKawin ? 'v' : '-' }}</td>
                    <td class="center">{{ $isKawin ? '-' : 'v' }}</td>
                    <td>{{ $item->alamat }}</td>
                    <td>{{ $item->pendidikan }}</td>
                    <td>{{ $item->jenis_kader_khusus }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
