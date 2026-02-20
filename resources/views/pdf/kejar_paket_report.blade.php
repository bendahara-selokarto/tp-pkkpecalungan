<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Isian Kejar Paket/KF/PAUD</title>
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

    <div class="title">DATA ISIAN KEJAR PAKET/KF/PAUD {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;" rowspan="2">NO</th>
                <th style="width: 190px;" rowspan="2">NAMA KEJAR PAKET/KF/PAUD</th>
                <th style="width: 120px;" rowspan="2">JENIS</th>
                <th colspan="2">JUMLAH WARGA BELAJAR/SISWA</th>
                <th colspan="2">JUMLAH PENGAJAR</th>
            </tr>
            <tr>
                <th style="width: 70px;">L</th>
                <th style="width: 70px;">P</th>
                <th style="width: 70px;">L</th>
                <th style="width: 70px;">P</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_kejar_paket }}</td>
                    <td>{{ $item->jenis_kejar_paket }}</td>
                    <td>{{ $item->jumlah_warga_belajar_l }}</td>
                    <td>{{ $item->jumlah_warga_belajar_p }}</td>
                    <td>{{ $item->jumlah_pengajar_l }}</td>
                    <td>{{ $item->jumlah_pengajar_p }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>





