<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bantuan</title>
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
    <div class="title">BUKU BANTUAN {{ strtoupper($level) }}</div>
    <div class="meta">
        Wilayah: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 28px;">NO</th>
                <th rowspan="2" style="width: 72px;">TANGGAL</th>
                <th rowspan="2" style="width: 92px;">ASAL BANTUAN</th>
                <th colspan="2" style="width: 110px;">JENIS BANTUAN</th>
                <th rowspan="2" style="width: 80px;">JUMLAH</th>
                <th rowspan="2" style="width: 130px;">LOKASI PENERIMA (SASARAN)</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr>
                <th style="width: 55px;">UANG</th>
                <th style="width: 55px;">BARANG</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                @php
                    $jenisBantuan = strtolower(trim((string) $item->category));
                    $isUang = in_array($jenisBantuan, ['uang', 'keuangan', 'dana'], true);
                    $asalBantuan = match ((string) $item->source) {
                        'pusat' => 'PUSAT',
                        'provinsi' => 'PROVINSI',
                        'kabupaten' => 'KABUPATEN',
                        'pihak_ketiga' => 'PIHAK KETIGA',
                        default => strtoupper(str_replace('_', ' ', (string) $item->source)),
                    };
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->received_date)->format('d/m/Y') }}</td>
                    <td>{{ $asalBantuan }}</td>
                    <td class="center">{{ $isUang ? 'v' : '-' }}</td>
                    <td class="center">{{ $isUang ? '-' : 'v' }}</td>
                    <td class="center">{{ number_format((float) $item->amount, 0, ',', '.') }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->description ?: '-' }}</td>
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
