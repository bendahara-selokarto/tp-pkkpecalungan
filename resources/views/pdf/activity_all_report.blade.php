<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kegiatan</title>
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
        .footer { margin-top: 8px; font-size: 9px; color: #374151; }
    </style>
</head>
<body>
    @php
        $scopeLevel = \App\Domains\Wilayah\Enums\ScopeLevel::tryFrom((string) $level);
        $levelLabel = $scopeLevel?->reportLevelLabel() ?? strtoupper((string) $level);
        $areaLabel = $scopeLevel?->reportAreaLabel() ?? 'Wilayah';
    @endphp

    <div class="lampiran">LAMPIRAN 4.13</div>
    <div class="title">BUKU KEGIATAN</div>
    <div class="meta">
        {{ $areaLabel }}: {{ $areaName }} | Level: {{ $levelLabel }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 4%;">NO</th>
                <th rowspan="2" style="width: 10%;">NAMA</th>
                <th rowspan="2" style="width: 14%;">JABATAN</th>
                <th colspan="3">KEGIATAN</th>
                <th rowspan="2" style="width: 22%;">TANDA TANGAN</th>
            </tr>
            <tr>
                <th style="width: 12%;">TANGGAL</th>
                <th style="width: 12%;">TEMPAT</th>
                <th>URAIAN</th>
            </tr>
            <tr class="number-row">
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
                    <td>{{ $item->nama_petugas ?: $item->title ?: '-' }}</td>
                    <td>{{ $item->jabatan_petugas ?: '-' }}</td>
                    <td class="center">{{ $item->activity_date ? \Carbon\Carbon::parse($item->activity_date)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->tempat_kegiatan ?: '-' }}</td>
                    <td>{{ $item->uraian ?: ($item->description ?: '-') }}</td>
                    <td>{{ $item->tanda_tangan ?: ($item->nama_petugas ?: '-') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Data kegiatan belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ $printedBy?->name ?? '-' }} | Dicetak pada: {{ $printedAt->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
