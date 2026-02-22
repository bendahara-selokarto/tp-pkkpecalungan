<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Isian Kelompok Simulasi dan Penyuluhan</title>
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

    <div class="title">DATA ISIAN KELOMPOK SIMULASI DAN PENYULUHAN {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;" rowspan="2">NO</th>
                <th style="width: 135px;" rowspan="2">NAMA KEGIATAN</th>
                <th style="width: 145px;" rowspan="2">JENIS SIMULASI/PENYULUHAN</th>
                <th colspan="2">JUMLAH</th>
                <th colspan="2" style="width: 95px;">JUMLAH KADER</th>
            </tr>
            <tr>
                <th style="width: 72px;">KELOMPOK</th>
                <th style="width: 82px;">SOSIALISASI</th>
                <th style="width: 47px;">L</th>
                <th style="width: 48px;">P</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_kegiatan }}</td>
                    <td>{{ $item->jenis_simulasi_penyuluhan }}</td>
                    <td class="center">{{ $item->jumlah_kelompok }}</td>
                    <td class="center">{{ $item->jumlah_sosialisasi }}</td>
                    <td class="center">{{ $item->jumlah_kader_l }}</td>
                    <td class="center">{{ $item->jumlah_kader_p }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Data isian kelompok simulasi dan penyuluhan belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>


