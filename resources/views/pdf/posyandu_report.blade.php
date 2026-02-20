<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Isian Posyandu oleh TP PKK</title>
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

    <div class="title">DATA ISIAN POSYANDU OLEH TP PKK {{ $levelLabel }}</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }}<br>
        Dicetak oleh: {{ $printedBy?->name ?? '-' }}<br>
        Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 28px;" rowspan="2">NO</th>
                <th style="width: 150px;" rowspan="2">NAMA POSYANDU</th>
                <th style="width: 120px;" rowspan="2">PENGELOLA</th>
                <th style="width: 120px;" rowspan="2">SEKRETARIS</th>
                <th style="width: 90px;" rowspan="2">JENIS POSYANDU</th>
                <th style="width: 60px;" rowspan="2">JML KADER</th>
                <th style="width: 120px;" rowspan="2">JENIS KEGIATAN</th>
                <th style="width: 50px;" rowspan="2">FREK.</th>
                <th colspan="2">PENGUNJUNG</th>
                <th colspan="2">PETUGAS</th>
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
                    <td>{{ $item->nama_posyandu }}</td>
                    <td>{{ $item->nama_pengelola }}</td>
                    <td>{{ $item->nama_sekretaris }}</td>
                    <td>{{ $item->jenis_posyandu }}</td>
                    <td class="center">{{ $item->jumlah_kader }}</td>
                    <td>{{ $item->jenis_kegiatan }}</td>
                    <td class="center">{{ $item->frekuensi_layanan }}</td>
                    <td>{{ $item->jumlah_pengunjung_l }}</td>
                    <td>{{ $item->jumlah_pengunjung_p }}</td>
                    <td>{{ $item->jumlah_petugas_l }}</td>
                    <td>{{ $item->jumlah_petugas_p }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="center">Data belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>





